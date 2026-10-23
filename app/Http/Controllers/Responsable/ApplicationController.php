<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDecisionProposal;
use App\Models\User;
use App\Services\AuditService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    private function authorizeResponsable(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'responsable' && $user->active && $user->ministry_id !== null, 403);
    }

    private function authorizeMinistry(Request $request, Application $application): void
    {
        $this->authorizeResponsable($request);
        $application->loadMissing('procedure');
        abort_unless($application->procedure
            && (int) $application->procedure->ministry_id === (int) $request->user()->ministry_id, 403);
    }

    public function index(Request $request)
    {
        $this->authorizeResponsable($request);
        $query = Application::query()->with(['user', 'procedure.ministry', 'assignedAgent'])
            ->whereHas('procedure', fn ($q) => $q->where('ministry_id', $request->user()->ministry_id));
        if ($request->filled('status')) {
            $validated = $request->validate([
                'status' => ['required', Rule::in(['soumise', 'en_traitement', 'validee', 'rejetee', 'terminee'])],
            ]);
            $query->where('status', $validated['status']);
        }
        $applications = $query->latest()->paginate(15)->withQueryString();
        return view('responsable.applications.index', compact('applications'));
    }

    public function show(Request $request, Application $application)
    {
        $this->authorizeMinistry($request, $application);
        $application->load(['user', 'procedure.ministry', 'assignedAgent', 'workflowLogs.user']);
        $agents = User::query()->where('role', 'agent')->where('active', true)
            ->where('ministry_id', $request->user()->ministry_id)->orderBy('name')->get();
        $pendingProposal = ApplicationDecisionProposal::query()->with('agent')
            ->where('application_id', $application->id)->where('status', 'pending')
            ->latest('id')->first();
        return view('responsable.applications.show', compact('application', 'agents', 'pendingProposal'));
    }

    public function assign(Request $request, Application $application)
    {
        $this->authorizeMinistry($request, $application);
        $validated = $request->validate(['agent_id' => ['required', 'integer', 'exists:users,id']]);
        DB::transaction(function () use ($request, $application, $validated) {
            $locked = Application::query()->whereKey($application->id)->lockForUpdate()->firstOrFail();
            $this->authorizeMinistry($request, $locked);
            if (! in_array($locked->status, ['soumise', 'en_traitement'], true)) {
                throw ValidationException::withMessages(['agent_id' => 'Ce dossier ne peut plus être affecté.']);
            }
            $agent = User::query()->whereKey($validated['agent_id'])->where('role', 'agent')
                ->where('active', true)->where('ministry_id', $request->user()->ministry_id)->first();
            if (! $agent) {
                throw ValidationException::withMessages(['agent_id' => 'Agent non habilité dans ce ministère.']);
            }
            if ($locked->assigned_to !== null && (int) $locked->assigned_to === (int) $agent->id) {
                throw ValidationException::withMessages(['agent_id' => 'Ce dossier est déjà affecté à cet agent.']);
            }
            $previous = $locked->assigned_to;
            ApplicationDecisionProposal::query()->where('application_id', $locked->id)
                ->where('status', 'pending')->update([
                    'status' => 'cancelled',
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                    'review_comment' => 'Annulée à la suite de la réaffectation du dossier.',
                ]);
            $locked->assigned_to = $agent->id;
            $locked->save();
            AuditService::log('affectation_demande', 'Demande', $locked->id, [
                'reference' => $locked->reference,
                'ministry_id' => $request->user()->ministry_id,
                'ancien_agent_id' => $previous,
                'nouvel_agent_id' => $agent->id,
            ]);
        }, 3);
        return redirect()->route('responsable.applications.show', $application)
            ->with('success', 'Affectation enregistrée. Les propositions antérieures en attente ont été annulées.');
    }

    public function decide(Request $request, Application $application, WorkflowService $workflowService)
    {
        $this->authorizeMinistry($request, $application);
        $validated = $request->validate([
            'proposal_id' => ['required', 'integer', 'exists:application_decision_proposals,id'],
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'review_comment' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $application, $validated, $workflowService) {
            $locked = Application::query()->whereKey($application->id)->lockForUpdate()->firstOrFail();
            $this->authorizeMinistry($request, $locked);
            if ($locked->status !== 'en_traitement' || $locked->assigned_to === null) {
                throw ValidationException::withMessages(['decision' => 'Dossier non éligible à une décision.']);
            }
            $proposal = ApplicationDecisionProposal::query()
                ->whereKey($validated['proposal_id'])->where('application_id', $locked->id)
                ->where('status', 'pending')->lockForUpdate()->first();
            if (! $proposal || (int) $proposal->agent_id !== (int) $locked->assigned_to) {
                throw ValidationException::withMessages(['decision' => 'Proposition absente, périmée ou non autorisée.']);
            }
            $agent = User::query()->whereKey($proposal->agent_id)->where('role', 'agent')
                ->where('active', true)->where('ministry_id', $request->user()->ministry_id)->first();
            if (! $agent) {
                throw ValidationException::withMessages(['decision' => 'L’agent instructeur n’est plus habilité.']);
            }
            // Un refus de proposition renvoie le dossier à l'agent sans décision finale.
            if ($validated['decision'] === 'reject') {
                $proposal->update([
                    'status' => 'rejected', 'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(), 'review_comment' => $validated['review_comment'],
                ]);
                AuditService::log('proposition_refusee', 'Demande', $locked->id, [
                    'proposal_id' => $proposal->id, 'agent_id' => $agent->id,
                    'commentaire' => $validated['review_comment'],
                ]);
                return;
            }
            // Approbation = adoption de la proposition (validation OU rejet du dossier).
            if (! in_array($proposal->proposed_status, ['validee', 'rejetee'], true)) {
                throw ValidationException::withMessages(['decision' => 'Statut proposé invalide.']);
            }
            $proposal->update([
                'status' => 'approved', 'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(), 'review_comment' => $validated['review_comment'],
            ]);
            $workflowService->changeStatus($locked, $proposal->proposed_status, $validated['review_comment'], $proposal->id);
            AuditService::log('decision_ministerielle', 'Demande', $locked->id, [
                'proposal_id' => $proposal->id, 'agent_id' => $agent->id,
                'decision' => $proposal->proposed_status,
                'commentaire' => $validated['review_comment'],
            ]);
        }, 3);
        return redirect()->route('responsable.applications.show', $application)
            ->with('success', 'Examen de la proposition enregistré.');
    }
}
