<?php

namespace App\Services;

use App\Models\ApplicationDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DocumentService
{
    /**
     * Statuts documentaires autorisés par PostgreSQL.
     */
    private array $allowedStatuses = [
        'attendu',
        'recu',
        'valide',
        'rejete',
    ];

    public function updateStatus(
        ApplicationDocument $document,
        string $status,
        ?string $note = null
    ): ApplicationDocument {
        if (! in_array($status, $this->allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'status' => 'Le statut documentaire sélectionné est invalide.',
            ]);
        }

        if ($status === 'rejete' && blank($note)) {
            throw ValidationException::withMessages([
                'note' => 'Le motif du rejet est obligatoire.',
            ]);
        }

        return DB::transaction(function () use (
            $document,
            $status,
            $note
        ) {
            $oldStatus = $document->status;

            $document->update([
                'status' => $status,
                'note' => $note,
            ]);

            AuditService::log(
                'Contrôle documentaire',
                'Document',
                $document->id,
                [
                    'application_id' => $document->application_id,
                    'nom_fichier' => $document->original_name
                        ?? $document->label,
                    'ancien_statut' => $oldStatus,
                    'nouveau_statut' => $status,
                    'note' => $note,
                ]
            );

            return $document->fresh('application');
        });
    }

    public function label(string $status): string
    {
        return match ($status) {
            'attendu' => 'Attendu',
            'recu' => 'Reçu',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            default => ucfirst($status),
        };
    }

    public function allowedStatuses(): array
    {
        return [
            'attendu' => 'Attendu',
            'recu' => 'Reçu',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
        ];
    }
}