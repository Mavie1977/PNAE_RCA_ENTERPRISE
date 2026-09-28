@extends('layouts.responsable')

@section('title', 'Nouvelle procédure disciplinaire')

@section('content')

<style>
    .disciplinary-create-page {
        max-width: 1050px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .disciplinary-create-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .disciplinary-create-kicker {
        display: block;
        margin-bottom: 6px;
        color: #d6002f;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .disciplinary-create-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 29px;
        line-height: 1.15;
    }

    .disciplinary-create-header p {
        margin: 7px 0 0;
        color: #65738a;
        font-size: 13px;
    }

    .disciplinary-create-card {
        background: #fff;
        border: 1px solid #d8e2ee;
        border-radius: 12px;
        overflow: hidden;
    }

    .disciplinary-create-card-header {
        padding: 15px 18px;
        border-bottom: 1px solid #e3e9f1;
    }

    .disciplinary-create-card-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 16px;
    }

    .disciplinary-create-body {
        padding: 20px;
    }

    .disciplinary-current {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .disciplinary-current-card {
        background: #f8fbff;
        border: 1px solid #d8e3ef;
        border-radius: 8px;
        padding: 12px;
    }

    .disciplinary-current-card span {
        display: block;
        color: #718096;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .disciplinary-current-card strong {
        color: #073b82;
        font-size: 12px;
    }

    .disciplinary-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .disciplinary-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .disciplinary-field.full {
        grid-column: 1 / -1;
    }

    .disciplinary-field label {
        color: #17365f;
        font-size: 11px;
        font-weight: 800;
    }

    .disciplinary-field input,
    .disciplinary-field select,
    .disciplinary-field textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #cbd8e8;
        border-radius: 7px;
        background: #fff;
        color: #17365f;
        font-size: 12px;
    }

    .disciplinary-field textarea {
        min-height: 110px;
        resize: vertical;
    }

    .disciplinary-form-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
    }

    .disciplinary-create-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 9px 16px;
        border-radius: 7px;
        border: 1px solid transparent;
        text-decoration: none;
        font-weight: 800;
        font-size: 12px;
        cursor: pointer;
    }

    .disciplinary-create-btn-blue {
        background: #0b4ca1;
        color: #fff;
    }

    .disciplinary-create-btn-yellow {
        background: #ffc900;
        color: #082d61;
        border-color: #dfb400;
    }

    .disciplinary-create-alert {
        margin-bottom: 16px;
        padding: 11px 14px;
        border-radius: 8px;
        background: #fff0f2;
        border: 1px solid #e6a8b5;
        color: #9f1834;
        font-size: 12px;
    }

    @media (max-width: 760px) {
        .disciplinary-create-header {
            flex-direction: column;
        }

        .disciplinary-current,
        .disciplinary-form-grid {
            grid-template-columns: 1fr;
        }

        .disciplinary-field.full {
            grid-column: auto;
        }
    }
</style>


<section class="disciplinary-create-page">

    <header class="disciplinary-create-header">

        <div>
            <span class="disciplinary-create-kicker">
                Gestion disciplinaire
            </span>

            <h1>
                Nouvelle procédure disciplinaire
            </h1>

            <p>
                {{ $agent->name }}
            </p>
        </div>

        <a
            href="{{ route('responsable.agents.disciplinary.index', $agent) }}"
            class="disciplinary-create-btn disciplinary-create-btn-yellow"
        >
            Retour aux sanctions
        </a>

    </header>


    @if ($errors->any())

        <div class="disciplinary-create-alert">

            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <article class="disciplinary-create-card">

        <div class="disciplinary-create-card-header">
            <h2>Informations de la procédure</h2>
        </div>

        <div class="disciplinary-create-body">

            <div class="disciplinary-current">

                <div class="disciplinary-current-card">
                    <span>Agent</span>

                    <strong>
                        {{ $agent->name }}
                    </strong>
                </div>

                <div class="disciplinary-current-card">
                    <span>Matricule</span>

                    <strong>
                        {{ $agent->agentProfile?->matricule ?? 'Non renseigné' }}
                    </strong>
                </div>

                <div class="disciplinary-current-card">
                    <span>Ministère</span>

                    <strong>
                        {{ $agent->ministry?->name ?? 'Non affecté' }}
                    </strong>
                </div>

                <div class="disciplinary-current-card">
                    <span>État du compte</span>

                    <strong>
                        {{ $agent->active ? 'Actif' : 'Inactif' }}
                    </strong>
                </div>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'responsable.agents.disciplinary.store',
                    $agent
                ) }}"
            >

                @csrf

                <div class="disciplinary-form-grid">

                    <div class="disciplinary-field">

                        <label for="sanction_type">
                            Type de sanction *
                        </label>

                        <select
                            id="sanction_type"
                            name="sanction_type"
                            required
                        >
                            <option value="">
                                Choisir une sanction
                            </option>

                            <option
                                value="warning"
                                @selected(old('sanction_type') === 'warning')
                            >
                                Avertissement
                            </option>

                            <option
                                value="reprimand"
                                @selected(old('sanction_type') === 'reprimand')
                            >
                                Blâme
                            </option>

                            <option
                                value="suspension"
                                @selected(old('sanction_type') === 'suspension')
                            >
                                Suspension
                            </option>

                            <option
                                value="temporary_exclusion"
                                @selected(old('sanction_type') === 'temporary_exclusion')
                            >
                                Exclusion temporaire
                            </option>

                            <option
                                value="dismissal"
                                @selected(old('sanction_type') === 'dismissal')
                            >
                                Révocation / radiation
                            </option>

                            <option
                                value="other"
                                @selected(old('sanction_type') === 'other')
                            >
                                Autre
                            </option>

                        </select>

                    </div>


                    <div class="disciplinary-field">

                        <label for="reference">
                            Référence de la décision
                        </label>

                        <input
                            id="reference"
                            type="text"
                            name="reference"
                            value="{{ old('reference') }}"
                            placeholder="Ex. DECISION-DISC-2026-001"
                        >

                    </div>


                    <div class="disciplinary-field">

                        <label for="effective_at">
                            Date d'effet *
                        </label>

                        <input
                            id="effective_at"
                            type="date"
                            name="effective_at"
                            value="{{ old(
                                'effective_at',
                                now()->format('Y-m-d')
                            ) }}"
                            required
                        >

                    </div>


                    <div class="disciplinary-field">

                        <label for="end_at">
                            Date de fin
                        </label>

                        <input
                            id="end_at"
                            type="date"
                            name="end_at"
                            value="{{ old('end_at') }}"
                        >

                    </div>


                    <div class="disciplinary-field full">

                        <label for="reason">
                            Motif détaillé *
                        </label>

                        <textarea
                            id="reason"
                            name="reason"
                            required
                            placeholder="Décrivez les faits, le motif administratif et les éléments justifiant l'ouverture de la procédure..."
                        >{{ old('reason') }}</textarea>

                    </div>

                </div>


                <div class="disciplinary-form-actions">

                    <button
                        type="submit"
                        class="disciplinary-create-btn disciplinary-create-btn-blue"
                    >
                        Enregistrer la procédure
                    </button>

                    <a
                        href="{{ route(
                            'responsable.agents.disciplinary.index',
                            $agent
                        ) }}"
                        class="disciplinary-create-btn disciplinary-create-btn-yellow"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </div>

    </article>

</section>

@endsection