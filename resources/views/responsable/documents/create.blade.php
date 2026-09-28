@extends('layouts.responsable')

@section('title', 'Ajouter un document RH')

@section('content')

<style>
    .rh-doc-create-page {
        max-width: 1050px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .rh-doc-create-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .rh-doc-create-kicker {
        display: block;
        margin-bottom: 6px;
        color: #d6002f;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .rh-doc-create-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 29px;
    }

    .rh-doc-create-header p {
        margin: 7px 0 0;
        color: #65738a;
        font-size: 13px;
    }

    .rh-doc-create-card {
        background: #fff;
        border: 1px solid #d8e2ee;
        border-radius: 12px;
        overflow: hidden;
    }

    .rh-doc-create-card-header {
        padding: 15px 18px;
        border-bottom: 1px solid #e3e9f1;
    }

    .rh-doc-create-card-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 16px;
    }

    .rh-doc-create-body {
        padding: 20px;
    }

    .rh-doc-agent-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .rh-doc-agent-card {
        background: #f8fbff;
        border: 1px solid #d8e3ef;
        border-radius: 8px;
        padding: 12px;
    }

    .rh-doc-agent-card span {
        display: block;
        color: #718096;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .rh-doc-agent-card strong {
        color: #073b82;
        font-size: 12px;
    }

    .rh-doc-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .rh-doc-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .rh-doc-field.full {
        grid-column: 1 / -1;
    }

    .rh-doc-field label {
        color: #17365f;
        font-size: 11px;
        font-weight: 800;
    }

    .rh-doc-field input,
    .rh-doc-field select,
    .rh-doc-field textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #cbd8e8;
        border-radius: 7px;
        background: #fff;
        color: #17365f;
        font-size: 12px;
    }

    .rh-doc-field textarea {
        min-height: 95px;
        resize: vertical;
    }

    .rh-doc-file-help {
        color: #718096;
        font-size: 9px;
    }

    .rh-doc-form-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
    }

    .rh-doc-create-btn {
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

    .rh-doc-create-blue {
        background: #0b4ca1;
        color: #fff;
    }

    .rh-doc-create-yellow {
        background: #ffc900;
        color: #082d61;
        border-color: #dfb400;
    }

    .rh-doc-error {
        margin-bottom: 16px;
        padding: 11px 14px;
        border-radius: 8px;
        background: #fff0f2;
        border: 1px solid #e6a8b5;
        color: #9f1834;
        font-size: 12px;
    }

    @media (max-width: 760px) {
        .rh-doc-create-header {
            flex-direction: column;
        }

        .rh-doc-agent-grid,
        .rh-doc-form-grid {
            grid-template-columns: 1fr;
        }

        .rh-doc-field.full {
            grid-column: auto;
        }
    }
</style>


<section class="rh-doc-create-page">

    <header class="rh-doc-create-header">

        <div>
            <span class="rh-doc-create-kicker">
                Documents administratifs RH
            </span>

            <h1>
                Ajouter un document
            </h1>

            <p>
                {{ $agent->name }}
            </p>
        </div>

        <a
            href="{{ route('responsable.agents.documents.index', $agent) }}"
            class="rh-doc-create-btn rh-doc-create-yellow"
        >
            Retour aux documents
        </a>

    </header>


    @if($errors->any())

        <div class="rh-doc-error">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif


    <article class="rh-doc-create-card">

        <div class="rh-doc-create-card-header">
            <h2>Informations du document</h2>
        </div>

        <div class="rh-doc-create-body">

            <div class="rh-doc-agent-grid">

                <div class="rh-doc-agent-card">
                    <span>Agent</span>
                    <strong>{{ $agent->name }}</strong>
                </div>

                <div class="rh-doc-agent-card">
                    <span>Matricule</span>
                    <strong>
                        {{ $agent->agentProfile?->matricule ?? 'Non renseigné' }}
                    </strong>
                </div>

                <div class="rh-doc-agent-card">
                    <span>Ministère</span>
                    <strong>
                        {{ $agent->ministry?->name ?? 'Non affecté' }}
                    </strong>
                </div>

                <div class="rh-doc-agent-card">
                    <span>Compte</span>
                    <strong>
                        {{ $agent->active ? 'Actif' : 'Inactif' }}
                    </strong>
                </div>

            </div>


            <form
                method="POST"
                enctype="multipart/form-data"
                action="{{ route(
                    'responsable.agents.documents.store',
                    $agent
                ) }}"
            >

                @csrf

                <div class="rh-doc-form-grid">

                    <div class="rh-doc-field">

                        <label for="document_type">
                            Type de document *
                        </label>

                        <select
                            id="document_type"
                            name="document_type"
                            required
                        >

                            <option value="">
                                Choisir un type
                            </option>

                            <option value="recruitment_order"
                                @selected(old('document_type') === 'recruitment_order')
                            >
                                Arrêté de recrutement
                            </option>

                            <option value="appointment_decision"
                                @selected(old('document_type') === 'appointment_decision')
                            >
                                Décision de nomination
                            </option>

                            <option value="assignment_decision"
                                @selected(old('document_type') === 'assignment_decision')
                            >
                                Décision d'affectation
                            </option>

                            <option value="transfer_decision"
                                @selected(old('document_type') === 'transfer_decision')
                            >
                                Décision de mutation
                            </option>

                            <option value="advancement_order"
                                @selected(old('document_type') === 'advancement_order')
                            >
                                Arrêté d'avancement
                            </option>

                            <option value="leave_decision"
                                @selected(old('document_type') === 'leave_decision')
                            >
                                Décision de congé
                            </option>

                            <option value="disciplinary_decision"
                                @selected(old('document_type') === 'disciplinary_decision')
                            >
                                Décision disciplinaire
                            </option>

                            <option value="certificate"
                                @selected(old('document_type') === 'certificate')
                            >
                                Attestation / certificat
                            </option>

                            <option value="diploma"
                                @selected(old('document_type') === 'diploma')
                            >
                                Diplôme
                            </option>

                            <option value="identity_document"
                                @selected(old('document_type') === 'identity_document')
                            >
                                Pièce d'identité
                            </option>

                            <option value="other"
                                @selected(old('document_type') === 'other')
                            >
                                Autre document
                            </option>

                        </select>

                    </div>


                    <div class="rh-doc-field">

                        <label for="title">
                            Titre du document *
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Ex. Arrêté d'avancement au grade A2"
                            required
                        >

                    </div>


                    <div class="rh-doc-field">

                        <label for="reference">
                            Référence administrative
                        </label>

                        <input
                            id="reference"
                            type="text"
                            name="reference"
                            value="{{ old('reference') }}"
                            placeholder="Ex. ARRETE-AVANCEMENT-2026-001"
                        >

                    </div>


                    <div class="rh-doc-field">

                        <label for="document_date">
                            Date du document
                        </label>

                        <input
                            id="document_date"
                            type="date"
                            name="document_date"
                            value="{{ old('document_date') }}"
                        >

                    </div>


                    <div class="rh-doc-field full">

                        <label for="file">
                            Fichier *
                        </label>

                        <input
                            id="file"
                            type="file"
                            name="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                        >

                        <span class="rh-doc-file-help">
                            Formats autorisés : PDF, JPG, JPEG, PNG — maximum 10 Mo.
                        </span>

                    </div>


                    <div class="rh-doc-field full">

                        <label for="notes">
                            Observations
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            placeholder="Informations complémentaires concernant ce document..."
                        >{{ old('notes') }}</textarea>

                    </div>

                </div>


                <div class="rh-doc-form-actions">

                    <button
                        type="submit"
                        class="rh-doc-create-btn rh-doc-create-blue"
                    >
                        Enregistrer le document
                    </button>

                    <a
                        href="{{ route(
                            'responsable.agents.documents.index',
                            $agent
                        ) }}"
                        class="rh-doc-create-btn rh-doc-create-yellow"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </div>

    </article>

</section>

@endsection