@extends('layouts.responsable')

@section('title', 'Formations de l’agent')

@section('content')

<style>
    .training-page {
        max-width: 1180px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .training-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
    }

    .training-kicker {
        display: block;
        margin-bottom: 6px;
        color: #d6002f;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .training-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 29px;
        line-height: 1.15;
    }

    .training-header p {
        margin: 7px 0 0;
        color: #65738a;
        font-size: 13px;
    }

    .training-header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .training-btn {
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
        white-space: nowrap;
    }

    .training-btn-yellow {
        background: #ffc900;
        color: #082d61;
        border-color: #dfb400;
    }

    .training-btn-outline {
        background: #fff;
        color: #0b4ca1;
        border-color: #9eb9dc;
    }

    .training-summary {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .training-summary-card {
        min-height: 72px;
        padding: 13px 14px;
        background: #fff;
        border: 1px solid #d8e3ef;
        border-radius: 9px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .training-summary-card span {
        display: block;
        margin-bottom: 5px;
        color: #718096;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .training-summary-card strong {
        color: #073b82;
        font-size: 21px;
        font-weight: 900;
    }

    .training-card {
        background: #fff;
        border: 1px solid #d8e2ee;
        border-radius: 12px;
        overflow: hidden;
    }

    .training-card-header {
        padding: 15px 18px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .training-card-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 16px;
    }

    .training-table-wrapper {
        overflow-x: auto;
    }

    .training-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
    }

    .training-table th {
        background: #0b4ca1;
        color: #fff;
        padding: 10px 11px;
        font-size: 10px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
    }

    .training-table td {
        padding: 11px;
        border-bottom: 1px solid #dfe7f1;
        color: #17365f;
        font-size: 11px;
        vertical-align: top;
    }

    .training-table tbody tr:hover {
        background: #f7faff;
    }

    .training-title {
        color: #073b82;
        font-weight: 800;
    }

    .training-subtext {
        margin-top: 4px;
        color: #718096;
        font-size: 9px;
        line-height: 1.35;
    }

    .training-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .training-status-planned {
        background: #fff3cd;
        color: #946200;
    }

    .training-status-progress {
        background: #e7f1ff;
        color: #0756a8;
    }

    .training-status-completed {
        background: #def7e6;
        color: #08783b;
    }

    .training-status-cancelled {
        background: #fde7ec;
        color: #b4233b;
    }

    .training-certificate {
        color: #0b4ca1;
        font-weight: 700;
    }

    .training-small-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 5px 9px;
        border-radius: 5px;
        border: 1px solid #9fbde3;
        background: #e8f1ff;
        color: #0756a8;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .training-skill-badge {
        display: inline-flex;
        margin: 2px 3px 2px 0;
        padding: 3px 6px;
        border-radius: 999px;
        background: #edf4ff;
        color: #0b4ca1;
        font-size: 9px;
        font-weight: 700;
    }

    .training-empty {
        padding: 30px !important;
        text-align: center;
        color: #718096;
    }

    .training-alert {
        margin-bottom: 16px;
        padding: 11px 14px;
        border-radius: 8px;
        font-size: 12px;
    }

    .training-alert-success {
        background: #e4f7eb;
        border: 1px solid #9ed5b0;
        color: #08783b;
    }

    @media (max-width: 900px) {
        .training-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .training-header {
            flex-direction: column;
        }

        .training-header-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 520px) {
        .training-summary {
            grid-template-columns: 1fr;
        }
    }
</style>


<section class="training-page">

    @if(session('success'))
        <div class="training-alert training-alert-success">
            {{ session('success') }}
        </div>
    @endif


    <header class="training-header">

        <div>
            <span class="training-kicker">
                Dossier ressources humaines
            </span>

            <h1>
                Formation et compétences
            </h1>

            <p>
                {{ $agent->name }}

                @if($agent->agentProfile?->matricule)
                    — {{ $agent->agentProfile->matricule }}
                @endif
            </p>
        </div>

        <div class="training-header-actions">

            <a
                href="{{ route('responsable.agents.trainings.create', $agent) }}"
                class="training-btn training-btn-yellow"
            >
                + Ajouter une formation
            </a>

            <a
                href="{{ route('responsable.agents.show', $agent) }}"
                class="training-btn training-btn-outline"
            >
                Retour au dossier RH
            </a>

        </div>

    </header>


    @php
        $totalTrainings = $trainings->count();

        $plannedTrainings = $trainings
            ->where('status', 'planned')
            ->count();

        $inProgressTrainings = $trainings
            ->where('status', 'in_progress')
            ->count();

        $completedTrainings = $trainings
            ->where('status', 'completed')
            ->count();

        $totalSkills = $trainings
            ->sum(fn ($training) => $training->skills->count());
    @endphp


    <div class="training-summary">

        <div class="training-summary-card">
            <span>Formations</span>
            <strong>{{ $totalTrainings }}</strong>
        </div>

        <div class="training-summary-card">
            <span>Planifiées</span>
            <strong>{{ $plannedTrainings }}</strong>
        </div>

        <div class="training-summary-card">
            <span>En cours</span>
            <strong>{{ $inProgressTrainings }}</strong>
        </div>

        <div class="training-summary-card">
            <span>Terminées</span>
            <strong>{{ $completedTrainings }}</strong>
        </div>

        <div class="training-summary-card">
            <span>Compétences liées</span>
            <strong>{{ $totalSkills }}</strong>
        </div>

    </div>


    <article class="training-card">

        <div class="training-card-header">
            <h2>Parcours de formation</h2>
        </div>

        <div class="training-table-wrapper">

            <table class="training-table">

                <thead>
                    <tr>
                        <th>Période</th>
                        <th>Formation</th>
                        <th>Type</th>
                        <th>Organisme</th>
                        <th>Statut</th>
                        <th>Résultat</th>
                        <th>Certification</th>
                        <th>Compétences</th>
                        <th>Créé par</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($trainings as $training)

                        @php
                            $statusClass = match($training->status) {
                                'completed' => 'training-status-completed',
                                'in_progress' => 'training-status-progress',
                                'cancelled' => 'training-status-cancelled',
                                default => 'training-status-planned',
                            };
                        @endphp

                        <tr>

                            <td>
                                @if($training->start_date)
                                    {{ $training->start_date->format('d/m/Y') }}
                                @else
                                    —
                                @endif

                                <div class="training-subtext">
                                    @if($training->end_date)
                                        → {{ $training->end_date->format('d/m/Y') }}
                                    @endif
                                </div>
                            </td>


                            <td>
                                <div class="training-title">
                                    {{ $training->title }}
                                </div>

                                @if($training->description)
                                    <div class="training-subtext">
                                        {{ $training->description }}
                                    </div>
                                @endif
                            </td>


                            <td>
                                {{ $training->type_label }}
                            </td>


                            <td>
                                {{ $training->organization ?: '—' }}
                            </td>


                            <td>
                                <span class="training-badge {{ $statusClass }}">
                                    {{ $training->status_label }}
                                </span>
                            </td>


                            <td>
                                {{ $training->result ?: '—' }}
                            </td>


                            <td>

                                @if($training->certificate_reference)

                                    <div class="training-certificate">
                                        {{ $training->certificate_reference }}
                                    </div>

                                @else
                                    —
                                @endif

                                @if($training->certificate_file_path)

                                    <div style="margin-top:6px;">
                                        <a
                                            href="{{ route(
                                                'responsable.agents.trainings.certificate',
                                                [$agent, $training]
                                            ) }}"
                                            class="training-small-btn"
                                        >
                                            Télécharger
                                        </a>
                                    </div>

                                @endif

                            </td>


                            <td>

                                @forelse($training->skills as $skill)

                                    <span class="training-skill-badge">
                                        {{ $skill->name }}
                                        ·
                                        {{ $skill->level_label }}
                                    </span>

                                @empty
                                    —
                                @endforelse

                            </td>


                            <td>
                                {{ $training->creator?->name ?? 'Système' }}
                            </td>


                            <td>
                                <span style="color:#718096;">
                                    —
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="10"
                                class="training-empty"
                            >
                                Aucune formation enregistrée dans le dossier RH de cet agent.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </article>

</section>

@endsection