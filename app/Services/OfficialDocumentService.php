<?php

namespace App\Services;

use App\Models\Application;
use App\Models\OfficialDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class OfficialDocumentService
{
    public function generate(
        Application $application
    ): OfficialDocument {
        $application->load([
            'user',
            'procedure.ministry',
            'documents',
            'latestPayment',
            'officialDocument',
        ]);

        $this->validateApplication($application);

        if ($application->officialDocument) {
            return $application->officialDocument;
        }

        $storedPath = null;

        try {
            return DB::transaction(function () use (
                $application,
                &$storedPath
            ) {
                $verificationToken = (string) Str::uuid();
                $officialNumber = $this->generateOfficialNumber();
                $signatureCode = $this->generateSignatureCode(
                    $application,
                    $officialNumber,
                    $verificationToken
                );
$documentFingerprint = hash(
    'sha256',
    implode('|', [
        $application->id,
        $application->reference,
        $application->user_id,
        $application->procedure_id,
        $officialNumber,
        $verificationToken,
        now()->toIso8601String(),
    ])
);
                $officialDocument = OfficialDocument::create([
                    'application_id' => $application->id,
                    'generated_by' => auth()->id(),
                    'official_number' => $officialNumber,
                    'verification_token' => $verificationToken,
                    'title' => $application->procedure->title
                        ?? 'Document administratif',
                    'file_path' => 'pending',
                    'mime_type' => 'application/pdf',
                    'hash_sha256' => $documentFingerprint,
                    'signature_code' => $signatureCode,
                    'status' => 'actif',
                    'issued_at' => now(),
                ]);

                $verificationUrl = URL::signedRoute(
                    'verification.documents.show',
                    [
                        'officialDocument' => $officialDocument,
                        'token' => $verificationToken,
                    ]
                );

                $qrCodeDataUri = $this->generateQrCode(
                    $verificationUrl
                );
$logoPath = public_path('images/logo-rca.png');

$logoDataUri = null;

if (file_exists($logoPath)) {
    $logoMimeType = mime_content_type($logoPath) ?: 'image/png';

    $logoDataUri = sprintf(
        'data:%s;base64,%s',
        $logoMimeType,
        base64_encode(file_get_contents($logoPath))
    );
}
               $pdf = Pdf::loadView(
    'pdf.official-document',
    [
        'application' => $application,
        'officialDocument' => $officialDocument,
        'verificationUrl' => $verificationUrl,
        'qrCodeDataUri' => $qrCodeDataUri,
        'logoDataUri' => $logoDataUri,

        'signatoryName' => config(
            'pnae.document.signatory_name'
        ),

        'signatoryTitle' => config(
            'pnae.document.signatory_title'
        ),

        'signatoryInstitution' => config(
            'pnae.document.institution'
        ),

        'documentLocation' => config(
            'pnae.document.location'
        ),
    ]
)
    ->setPaper('a4', 'portrait')
    ->setOption('defaultFont', 'DejaVu Sans');

               $pdfBinary = $pdf->output();

                $directory = sprintf(
                    'official-documents/%s',
                    now()->format('Y')
                );

                $filename = sprintf(
                    '%s.pdf',
                    $officialNumber
                );

                $storedPath = $directory . '/' . $filename;

                Storage::disk('local')->put(
                    $storedPath,
                    $pdfBinary
                );

                $fileHash = hash('sha256', $pdfBinary);
                $officialDocument->update([
                         'file_path' => $storedPath,
                         'file_hash_sha256' => $fileHash,
                     ]);

                AuditService::log(
                    'Génération de document officiel',
                    'Document officiel',
                    $officialDocument->id,
                    [
                        'official_number' => $officialNumber,
                        'application_reference' =>
                            $application->reference,
                        'file_hash_sha256' => $fileHash,
                    ]
                );

                return $officialDocument->fresh([
                    'application.user',
                    'application.procedure.ministry',
                    'generator',
                ]);
            });
        } catch (Throwable $exception) {
            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }

            throw $exception;
        }
    }

    private function validateApplication(
        Application $application
    ): void {
        if (! in_array(
            $application->status,
            ['validee', 'terminee'],
            true
        )) {
            throw ValidationException::withMessages([
                'document' =>
                    'Le dossier doit être validé avant de générer le document officiel.',
            ]);
        }

        

$invalidDocuments = $application->documents
    ->filter(
        fn ($document) =>
            $document->status !== 'valide'
    );

if ($invalidDocuments->isNotEmpty()) {
    throw ValidationException::withMessages([
        'document' =>
            'Toutes les pièces jointes présentes doivent être validées.',
    ]);
}

        $fee = (float) ($application->procedure->fee ?? 0);

        if (
            $fee > 0
            && $application->payment_status !== 'paye'
        ) {
            throw ValidationException::withMessages([
                'payment' =>
                    'Le paiement doit être confirmé avant la génération du document.',
            ]);
        }
    }

    private function generateQrCode(
        string $verificationUrl
    ): string {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $verificationUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 260,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        return $builder->build()->getDataUri();
    }

    private function generateOfficialNumber(): string
    {
        do {
            $number = sprintf(
                'DOC-%s-%s',
                now()->format('Y'),
                strtoupper(Str::random(10))
            );
        } while (
            OfficialDocument::where(
                'official_number',
                $number
            )->exists()
        );

        return $number;
    }

    private function generateSignatureCode(
        Application $application,
        string $officialNumber,
        string $verificationToken
    ): string {
        return hash_hmac(
            'sha256',
            implode('|', [
                $application->id,
                $application->reference,
                $officialNumber,
                $verificationToken,
                now()->toIso8601String(),
            ]),
            config('app.key')
        );
    }
}