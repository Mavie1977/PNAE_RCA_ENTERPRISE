@extends('layouts.citizen')

@section('title', 'Détail du paiement')

@section('content')
<section class="page-section">

    <x-page-header
        title="Détail du paiement"
        :subtitle="$payment->reference"
        kicker="Paiement électronique"
    />

    <x-panel
        title="Transaction"
        subtitle="État actuel de votre paiement."
        icon="💳"
    >
        <div class="payment-status-header">

            <div>
                <span class="payment-reference">
                    {{ $payment->reference }}
                </span>

                <strong class="payment-main-amount">
                    {{ $payment->formatted_amount }}
                </strong>
            </div>

            <x-status-badge
                :status="$payment->status"
                :label="$payment->status_label"
            />

        </div>

        <dl class="payment-summary">
            <div>
                <dt>Demande</dt>
                <dd>{{ $payment->application->reference }}</dd>
            </div>

            <div>
                <dt>Démarche</dt>
                <dd>
                    {{ $payment->application->procedure->title ?? '-' }}
                </dd>
            </div>

            <div>
                <dt>Moyen de paiement</dt>
                <dd>{{ $payment->method_label }}</dd>
            </div>

            <div>
                <dt>Payeur</dt>
                <dd>{{ $payment->payer_name }}</dd>
            </div>

            <div>
                <dt>Téléphone</dt>
                <dd>{{ $payment->payer_phone ?? '-' }}</dd>
            </div>

            <div>
                <dt>Référence opérateur</dt>
                <dd>{{ $payment->provider_reference ?? 'Non attribuée' }}</dd>
            </div>

            <div>
                <dt>Date d’initiation</dt>
                <dd>{{ $payment->created_at->format('d/m/Y H:i') }}</dd>
            </div>

            <div>
                <dt>Date de paiement</dt>
                <dd>
                    {{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}
                </dd>
            </div>
        </dl>

        @if(in_array($payment->status, ['initie', 'en_attente'], true))
            <x-alert type="warning" title="Mode démonstration">
                Aucun débit réel ne sera effectué. Le bouton ci-dessous simule
                la confirmation reçue d’un opérateur de paiement.
            </x-alert>

            <div class="form-actions">
                <form
                    method="POST"
                    action="{{ route('citizen.payments.confirm', $payment) }}"
                >
                    @csrf

                    <x-action-button
                        type="submit"
                        variant="success"
                        icon="✅"
                    >
                        Confirmer le paiement test
                    </x-action-button>
                </form>

                <form
                    method="POST"
                    action="{{ route('citizen.payments.cancel', $payment) }}"
                >
                    @csrf

                    <x-action-button
                        type="submit"
                        variant="danger"
                        icon="✖️"
                    >
                        Annuler
                    </x-action-button>
                </form>
            </div>
        @endif

        @if($payment->isPaid())
            <x-alert type="success" title="Paiement confirmé">
                Votre paiement a été enregistré. La demande peut poursuivre
                son circuit administratif.
            </x-alert>
        @endif

    </x-panel>

</section>
@endsection