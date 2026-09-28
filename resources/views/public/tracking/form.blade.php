@extends('layouts.app')

@section('title', 'Suivre une demande')

@section('content')

<section class="public-enterprise-page">

    <div class="public-enterprise-heading">

        <span class="public-kicker">
            SUIVI ADMINISTRATIF
        </span>

        <h1>Suivre une demande</h1>

        <p>
            Consultez l’état d’avancement de votre dossier à partir
            de sa référence et de votre adresse électronique.
        </p>

    </div>

    <div class="tracking-search-card">

        <div class="public-card-header">

            <div class="public-card-icon">
                🔎
            </div>

            <div>
                <h2>Rechercher un dossier</h2>

                <p>
                    Saisissez les informations utilisées lors du dépôt
                    de votre demande.
                </p>
            </div>

        </div>

        @if ($errors->any())
            <div class="public-alert public-alert-danger">

                <strong>
                    Veuillez corriger les erreurs suivantes :
                </strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        <form
            method="POST"
            action="{{ route('public.tracking.search') }}"
            class="tracking-search-form"
        >
            @csrf

            <div class="tracking-form-grid">

                <div class="public-form-group">

                    <label for="reference">
                        Référence de la demande
                    </label>

                    <div class="public-input-wrapper">

                        <span class="public-input-icon">
                            📁
                        </span>

                        <input
                            id="reference"
                            name="reference"
                            type="text"
                            value="{{ old('reference') }}"
                            placeholder="Ex. PNAE-2026-ABC123"
                            autocomplete="off"
                            required
                        >

                    </div>

                    @error('reference')
                        <small class="public-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

                <div class="public-form-group">

                    <label for="email">
                        Adresse électronique
                    </label>

                    <div class="public-input-wrapper">

                        <span class="public-input-icon">
                            ✉️
                        </span>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="citoyen@exemple.com"
                            autocomplete="email"
                            required
                        >

                    </div>

                    @error('email')
                        <small class="public-form-error">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

            <div class="public-form-actions">

                <button
                    type="submit"
                    class="public-btn public-btn-primary"
                >
                    🔍 Rechercher la demande
                </button>

                <a
                    href="{{ route('home') }}"
                    class="public-btn public-btn-secondary"
                >
                    Retour à l’accueil
                </a>

            </div>

        </form>

    </div>

    @isset($searched)

        <div class="tracking-result-card">

            @if ($application)

                @php
                    $status = $application->status;

                    $statusLabel = match ($status) {
                        'soumise' => 'Soumise',
                        'en_attente' => 'En attente',
                        'en_traitement' => 'En traitement',
                        'validee' => 'Validée',
                        'rejetee' => 'Rejetée',
                        'terminee' => 'Terminée',
                        default => ucfirst(
                            str_replace('_', ' ', $status)
                        ),
                    };

                    $statusClass = match ($status) {
                        'validee',
                        'terminee' => 'tracking-status-success',

                        'rejetee' => 'tracking-status-danger',

                        'en_traitement' => 'tracking-status-warning',

                        default => 'tracking-status-neutral',
                    };

                    $progressStep = match ($status) {
                        'soumise',
                        'en_attente' => 1,

                        'en_traitement' => 2,

                        'validee',
                        'rejetee' => 3,

                        'terminee' => 4,

                        default => 1,
                    };
                @endphp

                <div class="public-card-header">

                    <div class="public-card-icon success">
                        ✅
                    </div>

                    <div>
                        <h2>Résultat du suivi</h2>

                        <p>
                            Les informations actuelles de votre dossier.
                        </p>
                    </div>

                </div>

                <div class="tracking-reference-banner">

                    <div>
                        <span>Référence du dossier</span>

                        <strong>
                            {{ $application->reference }}
                        </strong>
                    </div>

                    <span class="tracking-status-badge {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>

                </div>

                <div class="tracking-details-grid">

                    <div class="tracking-detail-item">

                        <span>Démarche</span>

                        <strong>
                            {{ $application->procedure->title ?? '—' }}
                        </strong>

                    </div>

                    <div class="tracking-detail-item">

                        <span>Ministère compétent</span>

                        <strong>
                            {{ $application->procedure->ministry->name ?? '—' }}
                        </strong>

                    </div>

                    <div class="tracking-detail-item">

                        <span>Date de dépôt</span>

                        <strong>
                            {{ $application->created_at?->format('d/m/Y H:i') }}
                        </strong>

                    </div>

                    <div class="tracking-detail-item">

                        <span>Dernière mise à jour</span>

                        <strong>
                            {{ $application->updated_at?->format('d/m/Y H:i') }}
                        </strong>

                    </div>

                </div>

                <div class="tracking-progress-section">

                    <h3>Progression administrative</h3>

                    <div class="tracking-progress">

                        <div class="tracking-progress-item active">

                            <div class="tracking-progress-dot">
                                1
                            </div>

                            <span>Demande déposée</span>

                        </div>

                        <div class="tracking-progress-line {{
                            $progressStep >= 2 ? 'active' : ''
                        }}"></div>

                        <div class="tracking-progress-item {{
                            $progressStep >= 2 ? 'active' : ''
                        }}">

                            <div class="tracking-progress-dot">
                                2
                            </div>

                            <span>En traitement</span>

                        </div>

                        <div class="tracking-progress-line {{
                            $progressStep >= 3 ? 'active' : ''
                        }}"></div>

                        <div class="tracking-progress-item {{
                            $progressStep >= 3 ? 'active' : ''
                        }}">

                            <div class="tracking-progress-dot">
                                3
                            </div>

                            <span>Décision</span>

                        </div>

                        <div class="tracking-progress-line {{
                            $progressStep >= 4 ? 'active' : ''
                        }}"></div>

                        <div class="tracking-progress-item {{
                            $progressStep >= 4 ? 'active' : ''
                        }}">

                            <div class="tracking-progress-dot">
                                4
                            </div>

                            <span>Dossier terminé</span>

                        </div>

                    </div>

                </div>

                @if ($status === 'rejetee')
                    <div class="public-alert public-alert-danger">
                        Cette demande a été rejetée. Connectez-vous à votre
                        espace citoyen pour consulter les observations.
                    </div>
                @elseif ($status === 'terminee')
                    <div class="public-alert public-alert-success">
                        Le traitement administratif de votre dossier est terminé.
                    </div>
                @elseif ($status === 'validee')
                    <div class="public-alert public-alert-success">
                        Votre demande a été validée et poursuit son circuit administratif.
                    </div>
                @endif

            @else

                <div class="tracking-empty-state">

                    <div class="tracking-empty-icon">
                        📂
                    </div>

                    <h2>Aucune demande trouvée</h2>

                    <p>
                        Aucune demande ne correspond à cette référence
                        et à cette adresse électronique.
                    </p>

                    <a
                        href="{{ route('public.tracking.form') }}"
                        class="public-btn public-btn-primary"
                    >
                        Effectuer une nouvelle recherche
                    </a>

                </div>

            @endif

        </div>

    @endisset

    <div class="public-security-notice">

        <span>🔒</span>

        <div>
            <strong>Consultation sécurisée</strong>

            <p>
                La référence et l’adresse électronique doivent correspondre
                exactement aux informations enregistrées lors du dépôt.
            </p>
        </div>

    </div>

</section>

@endsection