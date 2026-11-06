<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDecisionProposal;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    private function authorizeAgent(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'agent' && $user->active && $user->ministry_id !== null, 403);
    }

    private function authorizeApplication(Request $request, Application $application): void
    {
        $this->authorizeAgent($request);
        $application->loadMissing('procedure');
        abort_unless(
            $application->procedure
            && $application->assigned_to !== null
            && (int) $application->assigned_to === (int) $request->user()->id
            && (int) $application->procedure->ministry_id === (int) $request->user()->ministry_id,
            403
        );
    }

    public function index(Request $request)
    {
        $this->authorizeAgent($request);
        $user = $request->user();
        $query = Application::query()
            ->with(['user', 'procedure.ministry', 'documents', 'assignedAgent'])
            ->where('assigned_to', $user->id)
            ->whereHas('procedure', fn ($q) => $q->where('ministry_id', $user->ministry_id));

        if ($request->filled('status')) {
            $validated = $request->validate([
                'status' => ['required', Rule::in(['soumise', 'en_traitement', 'validee', 'rejetee', 'terminee'])],
            ]);
            $query->where('status', $validated['status']);
        }
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%"))
                    ->orWhereHas('procedure', fn ($p) => $p->where('title', 'ilike', "%{$search}%"));
            });
        }
        $applications = $query->latest()->paginate(15)->withQueryString();
        return view('agent.applications.index', compact('applications'));
    }

    public function show(Request $request, Application $application)
    {
        $this->authorizeApplication($request, $application);
        $application->load(['user', 'procedure.ministry', 'documents', 'workflowLogs.user',
            'assignedAgent', 'latestPayment', 'officialDocument']);
        $pendingProposal = ApplicationDecisionProposal::query()
            ->where('application_id', $application->id)
            ->where('agent_id', $request->user()->id)
            ->where('status', 'pending')
            ->latest('id')->first();
        $availableTransitions = [];
        return view('agent.applications.show', compact('application', 'availableTransitions', 'pendingProposal'));
    }

    public function updateStatus(Request $request, Application $application)
{
    $this->authorizeApplication($request, $application);

    $validated = $request->validate([
        'status' => ['required', Rule::in(['en_traitement'])],
    ]);

    DB::transaction(function () use ($request, $application, $validated) {
        $locked = Application::query()
            ->whereKey($application->id)
            ->lockForUpdate()
            ->firstOrFail();

        $this->authorizeApplication($request, $locked);

        if ($locked->status !== 'soumise') {
            throw ValidationException::withMessages([
                'status' => 'Seul un dossier soumis peut être mis en traitement.',
            ]);
        }

        $oldStatus = $locked->status;

        $locked->update([
            'status' => $validated['status'],
        ]);

        AuditService::log('changement_statut', 'Demande', $locked->id, [
            'reference' => $locked->reference,
            'ancien_statut' => $oldStatus,
            'nouveau_statut' => $validated['status'],
            'agent_id' => $request->user()->id,
        ]);
    }, 3);

    return back()->with(
        'success',
        'Le dossier a été placé en traitement avec succès.'
    );
}
    public function proposeDecision(Request $request, Application $application)
    {
        $this->authorizeApplication($request, $application);
        $validated = $request->validate([
            'proposed_status' => ['required', Rule::in(['validee', 'rejetee'])],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $application, $validated) {
            $locked = Application::query()->whereKey($application->id)->lockForUpdate()->firstOrFail();
            $this->authorizeApplication($request, $locked);
            if ($locked->status !== 'en_traitement') {
                throw ValidationException::withMessages(['proposed_status' => 'Le dossier doit être en traitement.']);
            }
            if (ApplicationDecisionProposal::query()->where('application_id', $locked->id)
                ->where('status', 'pending')->exists()) {
                throw ValidationException::withMessages(['proposed_status' => 'Une proposition est déjà en attente de décision.']);
            }
            $proposal = ApplicationDecisionProposal::create([
                'application_id' => $locked->id,
                'agent_id' => $request->user()->id,
                'proposed_status' => $validated['proposed_status'],
                'comment' => $validated['comment'],
                'status' => 'pending',
            ]);
            AuditService::log('proposition_decision', 'Demande', $locked->id, [
                'proposal_id' => $proposal->id,
                'reference' => $locked->reference,
                'agent_id' => $request->user()->id,
                'proposed_status' => $validated['proposed_status'],
            ]);
        }, 3);

        return back()->with('success', 'Proposition transmise au responsable ministériel.');
    }
}
