@extends('layouts.responsable')

@section('title', 'Recruter un agent')

@section('content')

<section class="decision-dashboard">

    <div class="decision-heading">
        <div>
            <span class="decision-kicker">
                RECRUTEMENT NATIONAL
            </span>

            <h1>Créer un compte agent</h1>

            <p>
                Enregistrez un agent recruté puis affectez-le
                au ministère compétent.
            </p>
        </div>

        <a
            href="{{ route('responsable.agents.index') }}"
            class="btn-rca-secondary"
        >
            Retour à la liste
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Le formulaire contient des erreurs.</strong>

            <ul style="margin-top: 8px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <article class="decision-panel">

        <div class="decision-panel-header">
            <div>
                <h2>Informations de l’agent</h2>

                <p>
                    Tous les champs marqués comme obligatoires
                    doivent être renseignés.
                </p>
            </div>

            <span>🧑‍💼</span>
        </div>

        <form
            method="POST"
            action="{{ route(
                'responsable.recruitment.agents.store'
            ) }}"
            class="enterprise-form"
        >
            @csrf


<div class="form-grid">

    <div class="form-group">
        <label for="recruitment_reference">
            Référence de l’acte de recrutement
        </label>

        <input
            id="recruitment_reference"
            type="text"
            name="recruitment_reference"
            value="{{ old('recruitment_reference') }}"
            placeholder="Ex. ARRÊTÉ-2026-00125"
        >
    </div>

    <div class="form-group form-group-full">
        <label for="recruitment_reason">
            Motif ou décision de recrutement
        </label>

        <textarea
            id="recruitment_reason"
            name="recruitment_reason"
            rows="4"
            required
            placeholder="Indiquez la décision administrative ayant conduit au recrutement..."
        >{{ old('recruitment_reason') }}</textarea>
    </div>

</div>
            <div class="form-grid">

                <div class="form-group">
                    <label for="name">
                        Nom complet
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="email">
                        Adresse électronique
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone">
                        Téléphone
                    </label>

                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                    >
                </div>

                <div class="form-group">
                    <label for="ministry_id">
                        Ministère d’affectation
                    </label>

                    <select
                        id="ministry_id"
                        name="ministry_id"
                        required
                    >
                        <option value="">
                            Choisir un ministère
                        </option>

                        @foreach ($ministries as $ministry)
                            <option
                                value="{{ $ministry->id }}"
                                @selected(
                                    old('ministry_id')
                                    == $ministry->id
                                )
                            >
                                {{ $ministry->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">
                        Mot de passe provisoire
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        Confirmation du mot de passe
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                    >
                </div>

            </div>

            <div class="form-notice">
                <strong>Rôle attribué automatiquement :</strong>
                Agent public.
            </div>

            <div class="form-actions">
                <button
                    type="submit"
                    class="btn-rca-primary"
                >
                    Enregistrer et affecter l’agent
                </button>

                <a
                    href="{{ route(
                        'responsable.agents.index'
                    ) }}"
                    class="btn-rca-secondary"
                >
                    Annuler
                </a>
            </div>

        </form>

    </article>

</section>

@endsection