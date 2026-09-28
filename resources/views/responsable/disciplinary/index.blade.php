@extends('layouts.responsable')

@section('title', 'Sanctions disciplinaires')

@section('content')

<style>
    .disciplinary-page {
        max-width: 1180px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .disciplinary-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .disciplinary-kicker {
        display: block;
        margin-bottom: 6px;
        color: #d6002f;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .disciplinary-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 29px;
        line-height: 1.15;
    }

    .disciplinary-header p {
        margin: 7px 0 0;
        color: #65738a;
        font-size: 13px;
    }

    .disciplinary-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .disciplinary-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 9px 14px;
        border-radius: 7px;
        border: 1px solid transparent;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .disciplinary-btn-blue {
        background: #0b4ca1;
        color: #fff;
    }

    .disciplinary-btn-yellow {
        background: #ffc900;
        color: #082d61;
        border-color: #e3b700;
    }

    .disciplinary-btn-outline {
        background: #fff;
        color: #0b4ca1;
        border-color: #9eb9dc;
    }

    .disciplinary-card {
        background: #fff;
        border: 1px solid #d8e2ee;
        border-radius: 12px;
        overflow: hidden;
    }

    .disciplinary-card-header {
        padding: 15px 18px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .disciplinary-card-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 16px;
    }

    .disciplinary-table-wrapper {
        overflow-x: auto;
    }

    .disciplinary-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    .disciplinary-table th {
        background: #0b4ca1;
        color: #fff;
        padding: 10px 11px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        text-align: left;
    }

    .disciplinary-table td {
        padding: 11px;
        border-bottom: 1px solid #dfe7f1;
        color: #17365f;
        font-size: 11px;
        vertical-align: top;
    }

    .disciplinary-table tbody tr:hover {
        background: #f7faff;
    }

    .disciplinary-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .disciplinary-badge-pending {
        background: #fff3cd;
        color: #946200;
    }

    .disciplinary-badge-approved {
        background: #def7e6;
        color: #08783b;
    }

    .disciplinary-badge-rejected {
        background: #fde7ec;
        color: #b4233b;
    }

    .disciplinary-type {
        font-weight: 800;
        color: #073b82;
    }

    .disciplinary-reference {
        font-weight: 700;
        color: #0b4ca1;
    }

    .disciplinary-inline-actions {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .disciplinary-action-form {
        display: inline;
    }

    .disciplinary-action-button {
        min-height: 28px;
        padding: 5px 9px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 800;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .disciplinary-action-approve {
        background: #e3f7e9;
        color: #08783b;
        border-color: #9bd3ad;
    }

    .disciplinary-action-reject {
        background: #fff;
        color: #b4233b;
        border-color: #df9dab;
    }

    .disciplinary-empty {
        text-align: center;
        color: #718096;
        padding: 28px !important;
    }

    .disciplinary-alert {
        margin-bottom: 16px;
        padding: 11px 14px;
        border-radius: 8px;
        font-size: 12px;
    }

    .disciplinary-alert-success {
        background: #e4f7eb;
        border: 1px solid #9ed5b0;
        color: #08783b;
    }

    .disciplinary-alert-error {
        background: #fff0f2;
        border: 1px solid #e8a8b5;
        color: #9f1834;
    }

    @media (max-width: 760px) {
        .disciplinary-header {
            flex-direction: column;
        }

        .disciplinary-actions {
            justify-content: flex-start;
        }
    }
</style>


<section class="disciplinary-page">

    @if (session('success'))
        <div class="disciplinary-alert disciplinary-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="disciplinary-alert disciplinary-alert-error">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <header class="disciplinary-header">

        <div>
            <span class="disciplinary-kicker">
                Dossier ressources humaines
            </span>

            <h1>
                Sanctions disciplinaires
            </h1>

            <p>
                {{ $agent->name }}
                @if($agent->agentProfile?->matricule)
                    — {{ $agent->agentProfile->matricule }}
                @endif
            </p>
        </div>

        <div class="disciplinary-actions">

            <a
                href="{{ route('responsable.agents.disciplinary.create', $agent) }}"
                class="disciplinary-btn disciplinary-btn-yellow"
            >
                + Nouvelle procédure
            </a>

            <a
                href="{{ route('responsable.agents.show', $agent) }}"
                class="disciplinary-btn disciplinary-btn-outline"
            >
                Retour au dossier RH
            </a>

        </div>

    </header>


    <article class="disciplinary-card">

        <div class="disciplinary-card-header">
            <h2>Historique disciplinaire</h2>
        </div>

        <div class="disciplinary-table-wrapper">

            <table class="disciplinary-table">

                <thead>
                    <tr>
                        <th>Date d'effet</th>
                        <th>Sanction</th>
                        <th>Statut</th>
                        <th>Période</th>
                        <th>Référence</th>
                        <th>Motif</th>
                        <th>Auteur</th>
                        <th>Décision</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($actions as $action)

                        @php
                            $statusClass = match($action->status) {
                                'approved' => 'disciplinary-badge-approved',
                                'rejected' => 'disciplinary-badge-rejected',
                                default => 'disciplinary-badge-pending',
                            };
                        @endphp

                        <tr>

                            <td>
                                {{ $action->effective_at?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td>
                                <span class="disciplinary-type">
                                    {{ $action->type_label }}
                                </span>
                            </td>

                            <td>
                                <span class="disciplinary-badge {{ $statusClass }}">
                                    {{ $action->status_label }}
                                </span>
                            </td>

                            <td>
                                @if($action->end_at)
                                    {{ $action->effective_at?->format('d/m/Y') }}
                                    →
                                    {{ $action->end_at?->format('d/m/Y') }}
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                <span class="disciplinary-reference">
                                    {{ $action->reference ?: '—' }}
                                </span>
                            </td>

                            <td>
                                {{ $action->reason ?: '—' }}
                            </td>

                            <td>
                                {{ $action->creator?->name ?? 'Système' }}
                            </td>

                            <td>
                                @if($action->decided_at)
                                    <strong>
                                        {{ $action->decisionAuthor?->name ?? '—' }}
                                    </strong>

                                    <div style="margin-top:3px;color:#718096;font-size:9px;">
                                        {{ $action->decided_at->format('d/m/Y H:i') }}
                                    </div>

                                    @if($action->decision_reason)
                                        <div style="margin-top:4px;color:#4a5568;">
                                            {{ $action->decision_reason }}
                                        </div>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>

                            <td>

                                @if($action->status === 'pending')

                                    <div class="disciplinary-inline-actions">

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'responsable.agents.disciplinary.approve',
                                                [$agent, $action]
                                            ) }}"
                                            class="disciplinary-action-form"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="disciplinary-action-button disciplinary-action-approve"
                                                onclick="return confirm('Confirmer la validation de cette sanction disciplinaire ?')"
                                            >
                                                Valider
                                            </button>
                                        </form>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'responsable.agents.disciplinary.reject',
                                                [$agent, $action]
                                            ) }}"
                                            class="disciplinary-action-form"
                                            onsubmit="
                                                const motif = prompt('Motif du refus :');
                                                if (!motif) return false;
                                                this.querySelector('[name=decision_reason]').value = motif;
                                            "
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="decision_reason"
                                                value=""
                                            >

                                            <button
                                                type="submit"
                                                class="disciplinary-action-button disciplinary-action-reject"
                                            >
                                                Refuser
                                            </button>
                                        </form>

                                    </div>

                                @else
                                    —
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="9"
                                class="disciplinary-empty"
                            >
                                Aucune procédure disciplinaire enregistrée pour cet agent.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </article>

</section>

@endsection