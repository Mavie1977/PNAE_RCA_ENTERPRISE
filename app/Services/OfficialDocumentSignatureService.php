<?php

namespace App\Services;

use App\Models\OfficialDocument;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class OfficialDocumentSignatureService
{
    public const VERSION = 'PNAE-RCA-HMAC-V2';

    private function secret(): string
    {
        $key = config('pnae.document.hmac_key');

        if (! is_string($key) || strlen($key) < 32) {
            throw new RuntimeException(
                'La clé HMAC dédiée aux documents est absente ou trop courte.'
            );
        }

        return $key;
    }

    public function payload(
        int $applicationId,
        string $reference,
        string $officialNumber,
        string $verificationToken,
        CarbonInterface $issuedAt
    ): string {
        return implode('|', [
            self::VERSION,
            $applicationId,
            $reference,
            $officialNumber,
            $verificationToken,
            $issuedAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function sign(
        int $applicationId,
        string $reference,
        string $officialNumber,
        string $verificationToken,
        CarbonInterface $issuedAt
    ): string {
        return hash_hmac(
            'sha256',
            $this->payload(
                $applicationId,
                $reference,
                $officialNumber,
                $verificationToken,
                $issuedAt
            ),
            $this->secret()
        );
    }

    public function verify(OfficialDocument $document): bool
    {
        if (
            ! $document->issued_at
            || ! $document->application
            || ! is_string($document->signature_code)
        ) {
            return false;
        }

        $expected = $this->sign(
            (int) $document->application_id,
            (string) $document->application->reference,
            (string) $document->official_number,
            (string) $document->verification_token,
            $document->issued_at
        );

        return hash_equals($expected, $document->signature_code);
    }
}
