<?php

namespace App\Http\Controllers;

use App\Models\OfficialDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index()
    {
        return view('public.verification.index');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'official_number' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $officialDocument = OfficialDocument::where(
            'official_number',
            trim($validated['official_number'])
        )->first();

        if (! $officialDocument) {
            return back()
                ->withInput()
                ->withErrors([
                    'official_number' =>
                        'Aucun document ne correspond à ce numéro.',
                ]);
        }

        return redirect()->route(
            'verification.documents.public',
            $officialDocument
        );
    }

    public function publicShow(
        OfficialDocument $officialDocument
    ) {
        return $this->verificationView($officialDocument);
    }

    public function show(
        Request $request,
        OfficialDocument $officialDocument
    ) {
        abort_unless(
            hash_equals(
                (string) $officialDocument->verification_token,
                (string) $request->query('token')
            ),
            403,
            'Jeton de vérification invalide.'
        );

        return $this->verificationView($officialDocument);
    }

    private function verificationView(
        OfficialDocument $officialDocument
    ) {
        $officialDocument->load([
            'application.user',
            'application.procedure.ministry',
            'generator',
        ]);

        $fileExists = Storage::disk('local')->exists(
            $officialDocument->file_path
        );

        $currentHash = $fileExists
            ? hash(
                'sha256',
                Storage::disk('local')->get(
                    $officialDocument->file_path
                )
            )
            : null;

       $hashValid = $fileExists
    && filled($officialDocument->file_hash_sha256)
    && hash_equals(
        (string) $officialDocument->file_hash_sha256,
        (string) $currentHash
    );

        return view(
            'public.verification.show',
            compact(
                'officialDocument',
                'fileExists',
                'hashValid'
            )
        );
    }
}