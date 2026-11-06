<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\OfficialDocument;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfficialDocumentController extends Controller
{
    public function download(
        Request $request,
        OfficialDocument $officialDocument
    ) {
        $user = $request->user();

        abort_unless(
            $user
                && $user->role === 'citoyen'
                && $user->active
                && (int) $officialDocument->application->user_id
                    === (int) $user->id,
            403,
            'Accès non autorisé à ce document.'
        );

        abort_unless(
            $officialDocument->isActive(),
            403,
            'Ce document officiel n’est plus valide.'
        );

        $disk = Storage::disk('local');
        $path = $officialDocument->file_path;

        abort_unless(
            is_string($path)
                && $path !== ''
                && $path !== 'pending'
                && $disk->exists($path),
            404,
            'Le fichier officiel est introuvable.'
        );

        $stream = $disk->readStream($path);

        abort_if(
            $stream === false,
            500,
            'Impossible de lire le document officiel.'
        );

        try {
            $context = hash_init('sha256');
            hash_update_stream($context, $stream);
            $currentHash = hash_final($context);
        } finally {
            fclose($stream);
        }

        abort_unless(
            filled($officialDocument->file_hash_sha256)
                && hash_equals(
                    (string) $officialDocument->file_hash_sha256,
                    $currentHash
                ),
            409,
            'L’intégrité du document officiel ne peut pas être confirmée.'
        );

        AuditService::log(
            'telechargement_document_officiel_citoyen',
            'Document officiel',
            $officialDocument->id,
            [
                'application_id' => $officialDocument->application_id,
                'official_number' => $officialDocument->official_number,
                'user_id' => $user->id,
                'integrite_sha256' => 'valide',
            ]
        );

        return $disk->download(
            $path,
            $officialDocument->official_number . '.pdf',
            [
                'Content-Type' => 'application/pdf',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }
}
