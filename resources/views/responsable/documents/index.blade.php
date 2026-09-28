@extends('layouts.responsable')

@section('title', 'Documents RH')

@section('content')

<style>
    .rh-doc-page {
        max-width: 1180px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .rh-doc-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .rh-doc-kicker {
        display: block;
        margin-bottom: 6px;
        color: #d6002f;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .rh-doc-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 29px;
        line-height: 1.15;
    }

    .rh-doc-header p {
        margin: 7px 0 0;
        color: #65738a;
        font-size: 13px;
    }

    .rh-doc-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .rh-doc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 9px 14px;
        border-radius: 7px;
        border: 1px solid transparent;
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
    }

    .rh-doc-btn-yellow {
        background: #ffc900;
        color: #082d61;
        border-color: #e0b400;
    }

    .rh-doc-btn-outline {
        background: #fff;
        color: #0b4ca1;
        border-color: #9eb9dc;
    }

    .rh-doc-card {
        background: #fff;
        border: 1px solid #d8e2ee;
        border-radius: 12px;
        overflow: hidden;
    }

    .rh-doc-card-header {
        padding: 15px 18px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rh-doc-card-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 16px;
    }

    .rh-doc-table-wrapper {
        overflow-x: auto;
    }

    .rh-doc-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    .rh-doc-table th {
        background: #0b4ca1;
        color: #fff;
        padding: 10px 11px;
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
    }

    .rh-doc-table td {
        padding: 11px;
        border-bottom: 1px solid #dfe7f1;
        font-size: 11px;
        color: #17365f;
        vertical-align: middle;
    }

    .rh-doc-table tbody tr:hover {
        background: #f7faff;
    }

    .rh-doc-title {
        font-weight: 800;
        color: #073b82;
    }

    .rh-doc-ref {
        font-weight: 700;
        color: #0b4ca1;
    }

    .rh-doc-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .rh-doc-active {
        background: #def7e6;
        color: #08783b;
    }

    .rh-doc-archived {
        background: #edf0f4;
        color: #64748b;
    }

    .rh-doc-inline-actions {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .rh-doc-small-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 5px 9px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .rh-doc-download {
        background: #e8f1ff;
        color: #0756a8;
        border-color: #9fbde3;
    }

    .rh-doc-archive {
        background: #fff;
        color: #a56600;
        border-color: #e6bd6b;
    }

    .rh-doc-empty {
        text-align: center;
        color: #718096;
        padding: 28px !important;
    }

    .rh-doc-alert {
        margin-bottom: 16px;
        padding: 11px 14px;
        border-radius: 8px;
        background: #e4f7eb;
        border: 1px solid #9ed5b0;
        color: #08783b;
        font-size: 12px;
    }

    @media (max-width: 760px) {
        .rh-doc-header {
            flex-direction: column;
        }

        .rh-doc-actions {
            justify-content: flex-start;
        }
    }
</style>


<section class="rh-doc-page">

    @if(session('success'))
        <div class="rh-doc-alert">
            {{ session('success') }}
        </div>
    @endif

    <header class="rh-doc-header">

        <div>
            <span class="rh-doc-kicker">
                Dossier ressources humaines
            </span>

            <h1>
                Documents administratifs RH
            </h1>

            <p>
                {{ $agent->name }}

                @if($agent->agentProfile?->matricule)
                    — {{ $agent->agentProfile->matricule }}
                @endif
            </p>
        </div>

        <div class="rh-doc-actions">

            <a
                href="{{ route('responsable.agents.documents.create', $agent) }}"
                class="rh-doc-btn rh-doc-btn-yellow"
            >
                + Ajouter un document
            </a>

            <a
                href="{{ route('responsable.agents.show', $agent) }}"
                class="rh-doc-btn rh-doc-btn-outline"
            >
                Retour au dossier RH
            </a>

        </div>

    </header>


    <article class="rh-doc-card">

        <div class="rh-doc-card-header">
            <h2>Pièces du dossier administratif</h2>
        </div>

        <div class="rh-doc-table-wrapper">

            <table class="rh-doc-table">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Document</th>
                        <th>Référence</th>
                        <th>Fichier</th>
                        <th>Déposé par</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($documents as $document)

                        <tr>

                            <td>
                                {{ $document->document_date?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td>
                                {{ $document->type_label }}
                            </td>

                            <td>
                                <div class="rh-doc-title">
                                    {{ $document->title }}
                                </div>

                                @if($document->notes)
                                    <div style="margin-top:4px;color:#718096;font-size:9px;">
                                        {{ $document->notes }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="rh-doc-ref">
                                    {{ $document->reference ?: '—' }}
                                </span>
                            </td>

                            <td>
                                {{ $document->original_name }}

                                @if($document->file_size)
                                    <div style="margin-top:3px;color:#718096;font-size:9px;">
                                        {{ number_format($document->file_size / 1024, 0, ',', ' ') }} Ko
                                    </div>
                                @endif
                            </td>

                            <td>
                                {{ $document->uploader?->name ?? 'Système' }}
                            </td>

                            <td>
                                @if($document->active)
                                    <span class="rh-doc-badge rh-doc-active">
                                        Actif
                                    </span>
                                @else
                                    <span class="rh-doc-badge rh-doc-archived">
                                        Archivé
                                    </span>
                                @endif
                            </td>

                            <td>

                                <div class="rh-doc-inline-actions">

                                    <a
                                        href="{{ route(
                                            'responsable.agents.documents.download',
                                            [$agent, $document]
                                        ) }}"
                                        class="rh-doc-small-btn rh-doc-download"
                                    >
                                        Télécharger
                                    </a>

                                    @if($document->active)

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'responsable.agents.documents.archive',
                                                [$agent, $document]
                                            ) }}"
                                            style="display:inline;"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rh-doc-small-btn rh-doc-archive"
                                                onclick="return confirm('Archiver ce document RH ?')"
                                            >
                                                Archiver
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="8"
                                class="rh-doc-empty"
                            >
                                Aucun document administratif RH enregistré pour cet agent.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </article>

</section>

@endsection