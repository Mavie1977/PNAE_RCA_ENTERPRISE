@extends('layouts.responsable')

@section('title', 'Ajouter une formation')

@section('content')

<style>
    .training-create-page {
        max-width: 1050px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .training-create-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .training-create-kicker {
        display: block;
        margin-bottom: 6px;
        color: #d6002f;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .training-create-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 29px;
        line-height: 1.15;
    }

    .training-create-header p {
        margin: 7px 0 0;
        color: #65738a;
        font-size: 13px;
    }

    .training-create-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 9px 16px;
        border-radius: 7px;
        border: 1px solid transparent;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .training-create-blue {
        background: #0b4ca1;
        color: #fff;
    }

    .training-create-yellow {
        background: #ffc900;
        color: #082d61;
        border-color: #dfb400;
    }

    .training-create-card {
        background: #fff;
        border: 1px solid #d8e2ee;
        border-radius: 12px;
        overflow: hidden;
    }

    .training-create-card-header {
        padding: 15px 18px;
        border-bottom: 1px solid #e3e9f1;
    }

    .training-create-card-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 16px;
    }

    .training-create-body {
        padding: 20px;
    }

    .training-agent-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .training-agent-card {
        padding: 12px;
        background: #f8fbff;
        border: 1px solid #d8e3ef;
        border-radius: 8px;
    }

    .training-agent-card span {
        display: block;
        margin-bottom: 5px;
        color: #718096;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .training-agent-card strong {
        color: #073b82;
        font-size: 12px;
    }

    .training-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .training-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .training-field.full {
        grid-column: 1 / -1;
    }

    .training-field label {
        color: #17365f;
        font-size: 11px;
        font-weight: 800;
    }

    .training-field input,
    .training-field select,
    .training-field textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #cbd8e8;
        border-radius: 7px;
        background: #fff;
        color: #17365f;
        font-size: 12px;
    }

    .training-field textarea {
        min-height: 100px;
        resize: vertical;
    }

    .training-help {
        color: #718096;
        font-size: 9px;
        line-height: 1.4;
    }

    .training-form-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
    }

    .training-error {
        margin-bottom: 16px;
        padding: 11px 14px;
        border-radius: 8px;
        background: #fff0f2;
        border: 1px solid #e6a8b5;
        color: #9f1834;
        font-size: 12px;
    }

    .training-information {
        margin-bottom: 18px;
        padding: 12px 14px;
        background: #eef5ff;
        border-left: 3px solid #0b4ca1;
        border-radius: 6px;
        color: #284b75;
        font-size: 11px;
        line-height: 1.45;
    }

    @media (max-width: 760px) {
        .training-create-header {
            flex-direction: column;
        }

        .training-agent-grid,
        .training-form-grid {
            grid-template-columns: 1fr;
        }

        .training-field.full {
            grid-column: auto;
        }
    }
</style>


<section class="training-create-page">

    <header class="training-create-header">

        <div>
            <span class="training-create-kicker">
                Formation professionnelle
            </span>

            <h1>
                Ajouter une formation
            </h1>

            <p>
                {{ $agent->name }}
            </p>
        </div>

        <a
            href="{{ route('responsable.agents.trainings.index', $agent) }}"
            class="training-create-btn training-create-yellow"
        >
            Retour aux formations
        </a>

    </header>


    @if($errors->any())

        <div class="training-error">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif


    <article class="training-create-card">

        <div class="training-create-card-header">
            <h2>Informations sur la formation</h2>
        </div>

        <div class="training-create-body">

            <div class="training-agent-grid">

                <div class="training-agent-card">
                    <span>Agent</span>
                    <strong>{{ $agent->name }}</strong>
                </div>

                <div class="training-agent-card">
                    <span>Matricule</span>
                    <strong>
                        {{ $agent->agentProfile?->matricule ?? 'Non renseigné' }}
                    </strong>
                </div>

                <div class="training-agent-card">
                    <span>Ministère</span>
                    <strong>
                        {{ $agent->ministry?->name ?? 'Non affecté' }}
                    </strong>
                </div>

                <div class="training-agent-card">
                    <span>Situation</span>
                    <strong>
                        {{ $agent->active ? 'Agent actif' : 'Compte inactif' }}
                    </strong>
                </div>

            </div>


            <div class="training-information">
                Une formation enregistrée est automatiquement ajoutée à
                l’historique administratif de l’agent. Un certificat ou une
                attestation peut également être conservé dans le stockage privé.
            </div>


            <form
                method="POST"
                enctype="multipart/form-data"
                action="{{ route(
                    'responsable.agents.trainings.store',
                    $agent
                ) }}"
            >

                @csrf


                <div class="training-form-grid">

                    <div class="training-field full">

                        <label for="title">
                            Intitulé de la formation *
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Ex. Formation avancée en gestion des finances publiques"
                            required
                        >

                    </div>


                    <div class="training-field">

                        <label for="training_type">
                            Type de formation *
                        </label>

                        <select
                            id="training_type"
                            name="training_type"
                            required
                        >

                            <option value="">
                                Choisir un type
                            </option>

                            <option
                                value="training"
                                @selected(old('training_type') === 'training')
                            >
                                Formation
                            </option>

                            <option
                                value="continuing_education"
                                @selected(old('training_type') === 'continuing_education')
                            >
                                Formation continue
                            </option>

                            <option
                                value="seminar"
                                @selected(old('training_type') === 'seminar')
                            >
                                Séminaire
                            </option>

                            <option
                                value="workshop"
                                @selected(old('training_type') === 'workshop')
                            >
                                Atelier
                            </option>

                            <option
                                value="certification"
                                @selected(old('training_type') === 'certification')
                            >
                                Certification
                            </option>

                            <option
                                value="other"
                                @selected(old('training_type') === 'other')
                            >
                                Autre
                            </option>

                        </select>

                    </div>


                    <div class="training-field">

                        <label for="organization">
                            Organisme de formation
                        </label>

                        <input
                            id="organization"
                            type="text"
                            name="organization"
                            value="{{ old('organization') }}"
                            placeholder="Ex. ENA, Université, Institut..."
                        >

                    </div>


                    <div class="training-field">

                        <label for="start_date">
                            Date de début
                        </label>

                        <input
                            id="start_date"
                            type="date"
                            name="start_date"
                            value="{{ old('start_date') }}"
                        >

                    </div>


                    <div class="training-field">

                        <label for="end_date">
                            Date de fin
                        </label>

                        <input
                            id="end_date"
                            type="date"
                            name="end_date"
                            value="{{ old('end_date') }}"
                        >

                    </div>


                    <div class="training-field">

                        <label for="status">
                            Statut *
                        </label>

                        <select
                            id="status"
                            name="status"
                            required
                        >

                            <option
                                value="planned"
                                @selected(old('status', 'planned') === 'planned')
                            >
                                Planifiée
                            </option>

                            <option
                                value="in_progress"
                                @selected(old('status') === 'in_progress')
                            >
                                En cours
                            </option>

                            <option
                                value="completed"
                                @selected(old('status') === 'completed')
                            >
                                Terminée
                            </option>

                            <option
                                value="cancelled"
                                @selected(old('status') === 'cancelled')
                            >
                                Annulée
                            </option>

                        </select>

                    </div>


                    <div class="training-field">

                        <label for="certificate_reference">
                            Référence du certificat
                        </label>

                        <input
                            id="certificate_reference"
                            type="text"
                            name="certificate_reference"
                            value="{{ old('certificate_reference') }}"
                            placeholder="Ex. CERT-FORMATION-2026-001"
                        >

                    </div>


                    <div class="training-field full">

                        <label for="certificate">
                            Certificat / attestation
                        </label>

                        <input
                            id="certificate"
                            type="file"
                            name="certificate"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >

                        <span class="training-help">
                            PDF, JPG, JPEG ou PNG — maximum 10 Mo.
                        </span>

                    </div>


                    <div class="training-field full">

                        <label for="description">
                            Description / contenu
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            placeholder="Objectifs, modules suivis, contenu pédagogique..."
                        >{{ old('description') }}</textarea>

                    </div>


                    <div class="training-field full">

                        <label for="result">
                            Résultat / appréciation
                        </label>

                        <textarea
                            id="result"
                            name="result"
                            placeholder="Formation réussie, certificat obtenu, appréciation, résultat..."
                        >{{ old('result') }}</textarea>

                    </div>

                </div>


                <div class="training-form-actions">

                    <button
                        type="submit"
                        class="training-create-btn training-create-blue"
                    >
                        Enregistrer la formation
                    </button>

                    <a
                        href="{{ route(
                            'responsable.agents.trainings.index',
                            $agent
                        ) }}"
                        class="training-create-btn training-create-yellow"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </div>

    </article>

</section>

@endsection