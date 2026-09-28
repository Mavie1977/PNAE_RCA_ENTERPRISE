<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\OfficialDocument;
use App\Services\AuditService;
use App\Services\OfficialDocumentService;
use Illuminate\Support\Facades\Storage;

class OfficialDocumentController extends Controller
{
    public function store(
        Application $application,
        OfficialDocumentService $officialDocumentService
    ) {
        $officialDocumentService->generate($application);

        return redirect()
            ->route('agent.applications.show', $application)
            ->with(
                'success',
                'Le document officiel a été généré avec succès.'
            );
    }

    public function download(
        OfficialDocument $officialDocument
    ) {
        /*
         * L’agent est déjà protégé par le middleware :
         * auth + role:agent,responsable.
         *
         * Il ne faut pas vérifier que l’agent est propriétaire
         * de la demande, car le propriétaire est le citoyen.
         */

        abort_unless(
            $officialDocument->isActive(),
            403,
            'Ce document officiel est révoqué ou inactif.'
        );

        abort_unless(
            Storage::disk('local')->exists(
                $officialDocument->file_path
            ),
            404,
            'Le fichier officiel est introuvable.'
        );

        AuditService::log(
            'Téléchargement de document officiel',
            'Document officiel',
            $officialDocument->id,
            [
                'official_number' =>
                    $officialDocument->official_number,

                'application_id' =>
                    $officialDocument->application_id,

                'profil' => 'agent',
            ]
        );

        return Storage::disk('local')->download(
            $officialDocument->file_path,
            $officialDocument->official_number . '.pdf',
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }
}