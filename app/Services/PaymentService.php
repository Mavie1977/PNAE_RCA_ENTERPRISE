<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{

private function resolveProvider(string $method): string
{
    return match ($method) {
        'orange_money' => 'orange_money',
        'mobile_money' => 'mobile_money',
        'carte' => 'sandbox_card',
        'virement' => 'sandbox_bank',
        default => 'sandbox',
    };
}
    public function initiate(
        Application $application,
        User $citizen,
        array $data
    ): Payment {
        $this->ensureCitizenOwnsApplication($application, $citizen);

        if ($application->payment_status === 'paye') {
            throw ValidationException::withMessages([
                'payment' => 'Cette demande a déjà été payée.',
            ]);
        }

        $amount = (float) ($application->procedure->fee ?? 0);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'payment' => 'Cette démarche ne nécessite aucun paiement.',
            ]);
        }

        $existingPendingPayment = $application->payments()
                       ->where('status', 'en_attente')
                       ->latest()
                       ->first();

        if ($existingPendingPayment) {
            return $existingPendingPayment;
        }

        return DB::transaction(function () use (
            $application,
            $citizen,
            $data,
            $amount
        ) {
            $payment = Payment::create([
                       'application_id' => $application->id,
                       'user_id' => $citizen->id,
                       'reference' => $this->generateReference(),
                       'provider' => $this->resolveProvider($data['method']),
                       'amount' => $amount,
                       'currency' => 'XAF',
                       'method' => $data['method'],
    // Valeur autorisée par la contrainte PostgreSQL
                       'status' => 'en_attente',
                       'provider_reference' => null,
                       'payer_name' => $data['payer_name'],
                       'payer_phone' => $data['payer_phone'] ?? null,
                       'metadata' => [
                       'environment' => 'sandbox',
                       'procedure' => $application->procedure->title ?? null,
                  ],

                      'failure_reason' => null,
                      'paid_at' => null,
               ]);

            $application->update([
                'payment_status' => 'en_attente',
            ]);

            AuditService::log(
                'Initiation de paiement',
                'Paiement',
                $payment->id,
                [
                    'payment_reference' => $payment->reference,
                    'application_reference' => $application->reference,
                    'amount' => $amount,
                    'currency' => 'XAF',
                    'method' => $data['method'],
                ]
            );

            return $payment->fresh([
                'application.procedure',
                'user',
            ]);
        });
    }

    public function confirmSandboxPayment(
    Payment $payment,
    User $citizen
): Payment {
    $this->ensureCitizenOwnsPayment($payment, $citizen);

    if ($payment->status === 'paye') {
        return $payment;
    }

    if ($payment->status !== 'en_attente') {
        throw ValidationException::withMessages([
            'payment' => 'Ce paiement ne peut plus être confirmé.',
        ]);
    }

    return DB::transaction(function () use ($payment) {
        $providerReference = sprintf(
            'SANDBOX-%s',
            strtoupper(Str::random(12))
        );

        $payment->update([
            'status' => 'paye',
            'provider_reference' => $providerReference,
            'paid_at' => now(),
            'failure_reason' => null,
        ]);

        $payment->application->update([
            'payment_status' => 'paye',
        ]);

        AuditService::log(
            'Confirmation de paiement',
            'Paiement',
            $payment->id,
            [
                'payment_reference' => $payment->reference,
                'provider_reference' => $providerReference,
                'application_reference' =>
                    $payment->application->reference,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
            ]
        );

        return $payment->fresh([
            'application.procedure.ministry',
            'user',
        ]);
    });
}
    public function cancel(
        Payment $payment,
        User $citizen
    ): Payment {
        $this->ensureCitizenOwnsPayment($payment, $citizen);

        if ($payment->status === 'paye') {
            throw ValidationException::withMessages([
                'payment' =>
                    'Un paiement confirmé ne peut pas être annulé depuis cet écran.',
            ]);
        }

        return DB::transaction(function () use ($payment) {
            $payment->update([
                    'status' => 'echoue',
                    'failure_reason' => 'Paiement annulé par le citoyen.',
                    'cancelled_at' => now(),
           ]);

            $payment->application->update([
                'payment_status' => 'en_attente',
            ]);

            AuditService::log(
                'Annulation de paiement',
                'Paiement',
                $payment->id,
                [
                    'payment_reference' => $payment->reference,
                    'application_reference' =>
                        $payment->application->reference,
                ]
            );

            return $payment->fresh();
        });
    }

    private function generateReference(): string
    {
        do {
            $reference = sprintf(
                'PAY-%s-%s',
                now()->format('Ymd'),
                strtoupper(Str::random(10))
            );
        } while (
            Payment::where('reference', $reference)->exists()
        );

        return $reference;
    }

    private function ensureCitizenOwnsApplication(
        Application $application,
        User $citizen
    ): void {
        abort_unless(
            (int) $application->user_id === (int) $citizen->id,
            403,
            'Vous ne pouvez pas payer cette demande.'
        );
    }

    private function ensureCitizenOwnsPayment(
        Payment $payment,
        User $citizen
    ): void {
        abort_unless(
            (int) $payment->user_id === (int) $citizen->id,
            403,
            'Vous ne pouvez pas consulter ce paiement.'
        );
    }
}