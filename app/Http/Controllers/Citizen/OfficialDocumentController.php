<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\OfficialDocument;
use Illuminate\Support\Facades\Storage;

class OfficialDocumentController extends Controller
{
    public function download(
        OfficialDocument $officialDocument
    ) {
        abort_unless(
            (int) $officialDocument->application->user_id
                === (int) auth()->id(),
            403,
            'Vous ne pouvez pas télécharger ce document.'
        );

        abort_unless(
            $officialDocument->isActive(),
            403,
            'Ce document officiel n’est plus valide.'
        );

        abort_unless(
            Storage::disk('local')->exists(
                $officialDocument->file_path
            ),
            404,
            'Le fichier officiel est introuvable.'
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