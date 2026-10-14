<?php

namespace App\Http\Controllers;

use App\Models\OfficialDocument;
use App\Services\OfficialDocumentSignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class VerificationController extends Controller
{
    public function index()
    {
        return view('public.verification.index');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'official_number' => ['required', 'string', 'max:100'],
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

    public function publicShow(OfficialDocument $officialDocument)
    {
        return $this->verificationView($officialDocument);
    }

    public function show(
        Request $request,
        OfficialDocument $officialDocument
    ) {
        abort_unless(
            $request->hasValidSignature(),
            403,
            'Lien de vérification invalide.'
        );

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
            'application.procedure.ministry',
        ]);

        $fileExists = false;
        $hashValid = false;

        $path = $officialDocument->file_path;

        if (
            is_string($path)
            && $path !== ''
            && $path !== 'pending'
            && Storage::disk('local')->exists($path)
        ) {
            $fileExists = true;

            $stream = Storage::disk('local')->readStream($path);

            if ($stream !== false) {
                try {
                    $context = hash_init('sha256');
                    hash_update_stream($context, $stream);
                    $currentHash = hash_final($context);

                    $hashValid =
                        filled($officialDocument->file_hash_sha256)
                        && hash_equals(
                            (string) $officialDocument->file_hash_sha256,
                            $currentHash
                        );
                } finally {
                    fclose($stream);
                }
            }
        }

        $signatureVersion = $officialDocument->signature_version;

        $signatureValid = null;

        if (
            $signatureVersion ===
            OfficialDocumentSignatureService::VERSION
        ) {
            try {
                $signatureValid = app(
                    OfficialDocumentSignatureService::class
                )->verify($officialDocument);
            } catch (Throwable $exception) {
                report($exception);
                $signatureValid = false;
            }
        } elseif ($signatureVersion !== null) {
            $signatureValid = false;
        }

        $documentAuthentic =
            $officialDocument->isActive()
            && $fileExists
            && $hashValid
            && $signatureValid === true;

        return view('public.verification.show', compact(
            'officialDocument',
            'fileExists',
            'hashValid',
            'signatureValid',
            'signatureVersion',
            'documentAuthentic'
        ));
    }
}
