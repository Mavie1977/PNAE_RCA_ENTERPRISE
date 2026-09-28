<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Notifications\ApplicationStatusChangedNotification;

class WorkflowService
{
    /**
     * Transitions autorisées entre les statuts.
     */
    private array $transitions = [
        'soumise' => [
            'en_traitement',
            'rejetee',
        ],

        'en_traitement' => [
            'validee',
            'rejetee',
        ],

        'validee' => [
            'terminee',
            'en_traitement',
        ],

        'rejetee' => [
            'en_traitement',
        ],

        /*
         * Réouverture autorisée pour les dossiers terminés.
         */
        'terminee' => [
            'en_traitement',
        ],
    ];

    public function changeStatus(
        Application $application,
        string $newStatus,
        ?string $comment = null
    ): Application {
        $oldStatus = $application->status;

        if ($oldStatus === $newStatus) {
            throw ValidationException::withMessages([
                'status' => 'Le dossier possède déjà ce statut.',
            ]);
        }

        if (! $this->canTransition($oldStatus, $newStatus)) {
            throw ValidationException::withMessages([
                'status' => sprintf(
                    'Le passage du statut « %s » vers « %s » n’est pas autorisé.',
                    $this->label($oldStatus),
                    $this->label($newStatus)
                ),
            ]);
        }

        return DB::transaction(function () use (
            $application,
            $oldStatus,
            $newStatus,
            $comment
        ) {
            $oldStatus = $application->status;

$application->update([
    'status' => $newStatus,
    'agent_comment' => $comment,
]);

$application->loadMissing('user');

if ($application->user) {
    $application->user->notify(
        new ApplicationStatusChangedNotification(
            application: $application,
            oldStatus: $oldStatus,
            newStatus: $newStatus,
            comment: $comment,
        )
    );
}

return $application->refresh();

            DB::table('workflow_logs')->insert([
                'application_id' => $application->id,
                'user_id' => auth()->id(),
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'comment' => $comment,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            AuditService::log(
                'Changement de statut',
                'Demande',
                $application->id,
                [
                    'reference' => $application->reference,
                    'ancien_statut' => $oldStatus,
                    'nouveau_statut' => $newStatus,
                    'commentaire' => $comment,
                ]
            );

            return $application->fresh([
                'user',
                'procedure.ministry',
                'documents',
                'workflowLogs.user',
            ]);
        });
    }

    public function canTransition(
        string $oldStatus,
        string $newStatus
    ): bool {
        return in_array(
            $newStatus,
            $this->transitions[$oldStatus] ?? [],
            true
        );
    }

    public function availableTransitions(
        Application $application
    ): array {
        return collect(
            $this->transitions[$application->status] ?? []
        )
            ->mapWithKeys(function (string $status) {
                return [
                    $status => $this->label($status),
                ];
            })
            ->all();
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