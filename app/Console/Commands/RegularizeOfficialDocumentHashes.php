<?php

namespace App\Console\Commands;

use App\Models\OfficialDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class RegularizeOfficialDocumentHashes extends Command
{
    protected $signature = 'pnae:regularize-document-hashes
                            {--apply : Enregistrer les empreintes après vérification}';

    protected $description = 'Contrôle et régularisation des empreintes SHA-256 des PDF historiques';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $disk = Storage::disk('local');
        $errors = 0;
        $updated = 0;

        if ($apply && ! $this->confirm(
            'Confirmer la régularisation technique des empreintes historiques ?'
        )) {
            $this->warn('Opération annulée.');
            return self::SUCCESS;
        }

        foreach (OfficialDocument::query()->orderBy('id')->get() as $document) {
            if (filled($document->file_hash_sha256)) {
                $this->line("Document {$document->id} : empreinte déjà enregistrée.");
                continue;
            }

            $path = $document->file_path;

            if (! is_string($path)
                || $path === ''
                || $path === 'pending'
                || ! $disk->exists($path)) {
                $this->error("Document {$document->id} : fichier absent.");
                $errors++;
                continue;
            }

            $stream = $disk->readStream($path);

            if ($stream === false) {
                $this->error("Document {$document->id} : lecture impossible.");
                $errors++;
                continue;
            }

            try {
                $context = hash_init('sha256');
                hash_update_stream($context, $stream);
                $hash = hash_final($context);
            } finally {
                fclose($stream);
            }

            $this->line("{$document->official_number} : {$hash}");

            if (! $apply) {
                continue;
            }

            try {
                DB::transaction(function () use ($document, $hash) {
                    $current = OfficialDocument::query()
                        ->lockForUpdate()
                        ->findOrFail($document->id);

                    if (filled($current->file_hash_sha256)) {
                        throw new RuntimeException(
                            'Une empreinte a déjà été enregistrée.'
                        );
                    }

                    $current->file_hash_sha256 = $hash;
                    $current->save();

                    DB::table('audit_logs')->insert([
                        'user_id' => null,
                        'action' => 'regularisation_empreinte_document',
                        'entity' => 'Document officiel',
                        'entity_id' => $current->id,
                        'ip_address' => null,
                        'user_agent' => 'artisan:pnae:regularize-document-hashes',
                        'payload' => json_encode([
                            'official_number' => $current->official_number,
                            'file_hash_sha256' => $hash,
                            'type' => 'empreinte_reference_historique',
                            'authenticite_initiale_verifiee' => false,
                        ], JSON_UNESCAPED_UNICODE),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });

                $updated++;
            } catch (Throwable $e) {
                $errors++;
                $this->error(
                    "Document {$document->id} : {$e->getMessage()}"
                );
            }
        }

        $this->info(
            $apply
                ? "{$updated} document(s) régularisé(s)."
                : 'Simulation terminée : aucune donnée modifiée.'
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
