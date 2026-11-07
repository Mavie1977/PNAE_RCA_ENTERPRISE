<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationDecisionProposal;
use App\Notifications\ApplicationStatusChangedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkflowService
{
    private array $transitions = [
        'soumise' => ['en_traitement', 'rejetee'],
        'en_traitement' => ['validee', 'rejetee'],
        'validee' => ['terminee', 'en_traitement'],
        'rejetee' => ['en_traitement'],
        'terminee' => ['en_traitement'],
    ];

    public function changeStatus(Application $application, string $newStatus, ?string $comment = null, ?int $approvedProposalId = null): Application
    {
        $user = auth()->user();
        abort_unless($user && $user->role === 'responsable' && $user->active && $user->ministry_id !== null, 403);

        return DB::transaction(function () use ($application, $newStatus, $comment, $user, $approvedProposalId) {
            $locked = Application::query()->whereKey($application->id)->lockForUpdate()->firstOrFail();
            $locked->loadMissing('procedure');
            abort_unless($locked->procedure && (int) $locked->procedure->ministry_id === (int) $user->ministry_id, 403);
            $oldStatus = $locked->status;
            if (in_array($newStatus, ['validee', 'rejetee'], true)) {
                $proposal = $approvedProposalId === null ? null : ApplicationDecisionProposal::query()
                    ->whereKey($approvedProposalId)
                    ->where('application_id', $locked->id)
                    ->where('agent_id', $locked->assigned_to)
                    ->where('proposed_status', $newStatus)
                    ->where('status', 'approved')
                    ->where('reviewed_by', $user->id)
                    ->first();
                if (! $proposal) {
                    throw ValidationException::withMessages(['status' => 'Décision finale sans proposition approuvée interdite.']);
                }
            }
            if (! $this->canTransition($oldStatus, $newStatus)) {
                throw ValidationException::withMessages(['status' => 'Transition de statut interdite.']);
            }
            $locked->update(['status' => $newStatus, 'decision_note' => $comment]);
            DB::table('workflow_logs')->insert([
                'application_id' => $locked->id,
                'user_id' => $user->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'comment' => $comment,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            AuditService::log('Changement de statut', 'Demande', $locked->id, [
                'reference' => $locked->reference,
                'ancien_statut' => $oldStatus,
                'nouveau_statut' => $newStatus,
                'commentaire' => $comment,
            ]);
            $applicationId = $locked->id;
            DB::afterCommit(function () use ($applicationId, $oldStatus, $newStatus, $comment) {
                $notified = Application::query()->with('user')->find($applicationId);
                if ($notified?->user) {
                    $notified->user->notify(new ApplicationStatusChangedNotification(
                        application: $notified,
                        oldStatus: $oldStatus,
                        newStatus: $newStatus,
                        comment: $comment,
                    ));
                }
            });
            return $locked->fresh(['user', 'procedure.ministry', 'documents', 'workflowLogs.user']);
        }, 3);
    }

    public function canTransition(string $oldStatus, string $newStatus): bool
    {
        return in_array($newStatus, $this->transitions[$oldStatus] ?? [], true);
    }

    public function availableTransitions(Application $application): array
    {
        return collect($this->transitions[$application->status] ?? [])
            ->mapWithKeys(fn (string $status) => [$status => $this->label($status)])->all();
    }

    public function label(string $status): string
    {
        return match ($status) {
            'soumise' => 'Soumise',
            'en_traitement' => 'En traitement',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
            'terminee' => 'Terminée',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
}
