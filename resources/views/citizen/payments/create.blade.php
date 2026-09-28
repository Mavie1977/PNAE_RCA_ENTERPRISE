@extends('layouts.citizen')

@section('title', 'Paiement de la démarche')

@section('content')
<section class="page-section">

    <x-page-header
        title="Paiement de la démarche"
        subtitle="Choisissez votre moyen de paiement et vérifiez les informations."
        kicker="Paiement électronique"
    />

    @if($errors->any())
        <x-alert type="error" title="Paiement impossible">
            <ul class="component-error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <div class="payment-layout">

        <x-panel
            title="Récapitulatif"
            subtitle="Informations relatives à la demande."
            icon="🧾"
        >
            <dl class="payment-summary">
                <div>
                    <dt>Référence de la demande</dt>
                    <dd>{{ $application->reference }}</dd>
                </div>

                <div>
                    <dt>Démarche</dt>
                    <dd>{{ $application->procedure->title ?? '-' }}</dd>
                </div>

                <div>
                    <dt>Ministère</dt>
                    <dd>
                        {{ $application->procedure->ministry->name ?? '-' }}
                    </dd>
                </div>

                <div class="payment-total-row">
                    <dt>Montant à payer</dt>
                    <dd>
                        {{ number_format(
                            (float) ($application->procedure->fee ?? 0),
                            0,
                            ',',
                            ' '
                        ) }}
                        FCFA
                    </dd>
                </div>
            </dl>
        </x-panel>

        <x-panel
            title="Moyen de paiement"
            subtitle="Environnement de démonstration PNAE-RCA."
            icon="💳"
        >
            <form
                method="POST"
                action="{{ route('citizen.payments.store', $application) }}"
            >
                @csrf

                <div class="payment-method-grid">

                    <label class="payment-method-card">
                        <input
                            type="radio"
                            name="method"
                            value="orange_money"
                            @checked(old('method') === 'orange_money')
                            required
                        >

                        <span class="payment-method-icon">🟠</span>
                        <strong>Orange Money</strong>
                        <small>Paiement par téléphone</small>
                    </label>

                    <label class="payment-method-card">
                        <input
                            type="radio"
                            name="method"
                            value="mobile_money"
                            @checked(old('method') === 'mobile_money')
                        >

                        <span class="payment-method-icon">📱</span>
                        <strong>Mobile Money</strong>
                        <small>Portefeuille mobile</small>
                    </label>

                    <label class="payment-method-card">
                        <input
                            type="radio"
                            name="method"
                            value="carte"
                            @checked(old('method') === 'carte')
                        >

                        <span class="payment-method-icon">💳</span>
                        <strong>Carte bancaire</strong>
                        <small>Visa ou Mastercard</small>
                    </label>

                    <label class="payment-method-card">
                        <input
                            type="radio"
                            name="method"
                            value="virement"
                            @checked(old('method') === 'virement')
                        >

                        <span class="payment-method-icon">🏦</span>
                        <strong>Virement</strong>
                        <small>Paiement bancaire</small>
                    </label>

                </div>

                <div class="form-grid payment-form-grid">

                    <div class="form-group">
                        <label for="payer_name">Nom du payeur</label>

                        <input
                            id="payer_name"
                            name="payer_name"
                            type="text"
                            value="{{ old('payer_name', auth()->user()->name) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="payer_phone">
                            Numéro de téléphone
                        </label>

                        <input
                            id="payer_phone"
                            name="payer_phone"
                            type="text"
                            value="{{ old('payer_phone', auth()->user()->phone) }}"
                            placeholder="+236..."
                        >
                    </div>

                </div>

                <label class="payment-terms">
                    <input
                        type="checkbox"
                        name="accept_terms"
                        value="1"
                        required
                    >

                    <span>
                        Je confirme les informations et accepte les conditions
                        de paiement.
                    </span>
                </label>

                <div class="form-actions">
                    <x-action-button
                        type="submit"
                        variant="success"
                        icon="🔒"
                    >
                        Continuer vers le paiement
                    </x-action-button>

                    <x-action-button
                        :href="route('citizen.applications')"
                        variant="secondary"
                    >
                        Retour
                    </x-action-button>
                </div>

            </form>
        </x-panel>

    </div>

</section>
@endsection