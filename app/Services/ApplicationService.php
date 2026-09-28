<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ApplicationService
{
    /**
     * Créer une demande citoyenne.
     *
     * @param array<string, mixed> $data
     * @param array<int, UploadedFile> $documents
     */
    public function createForCitizen(
        User $citizen,
        array $data,
        array $documents = []
    ): Application {
        $storedPaths = [];

        try {
            $application = DB::transaction(function () use (
                $citizen,
                $data,
                $documents,
                &$storedPaths
            ) {
                $application = Application::create([
                    'user_id' => $citizen->id,
                    'procedure_id' => $data['procedure_id'],
                    'reference' => $this->generateReference(),
                    'status' => 'soumise',
                    'payment_status' => 'en_attente',
                    'priority' => $data['priority'] ?? 'normale',
                    'message' => $data['message'] ?? null,
                    'assigned_to' => null,
                ]);

                foreach ($documents as $file) {
                    $path = $file->store(
                        'documents/applications',
                        'public'
                    );

                    $storedPaths[] = $path;

                    ApplicationDocument::create([
                           'application_id' => $application->id,
                           'label' => $file->getClientOriginalName(),
                           'original_name' => $file->getClientOriginalName(),
                           'file_path' => $path,
                           'status' => 'attendu',
                           'note' => null,
                           'mime_type' => $file->getMimeType(),
                           'size' => $file->getSize(),
                    ]);
                }

                AuditService::log(
                    'Création',
                    'Demande',
                    $application->id,
                    [
                        'reference' => $application->reference,
                        'procedure_id' => $application->procedure_id,
                        'documents' => count($documents),
                    ]
                );

                return $application;
            });

            return $application->fresh([
                'user',
                'procedure.ministry',
                'documents',
            ]);
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }
    }

    private function generateReference(): string
    {
        do {
            $reference = sprintf(
                'PNAE-%s-%s',
                now()->format('Y'),
                strtoupper(Str::random(8))
            );
        } while (
            Application::where('reference', $reference)->exists()
        );

        return $reference;
    }
}