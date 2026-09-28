@extends('layouts.responsable')

@section('title', 'Supervision ministérielle')

@section('content')

<section class="decision-dashboard">

    <div class="decision-heading">

        <div>
            <span class="decision-kicker">
                SUPERVISION MINISTÉRIELLE
            </span>

            <h1>{{ $ministry->name }}</h1>

            <p>
                Pilotage des agents, des démarches, des dossiers
                et des performances de votre ministère.
            </p>
        </div>

        <div class="decision-updated-at">
            <small>Dernière actualisation</small>
            <strong>{{ now()->format('d/m/Y H:i') }}</strong>
        </div>

    </div>

    <div class="decision-kpi-grid">

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">📁</span>

            <div>
                <small>Total des dossiers</small>
                <strong>{{ $statistics['total'] }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">📥</span>

            <div>
                <small>Demandes soumises</small>
                <strong>{{ $statistics['submitted'] }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">⌛</span>

            <div>
                <small>En traitement</small>
                <strong>{{ $statistics['processing'] }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">✅</span>

            <div>
                <small>Dossiers validés</small>
                <strong>
                    {{ $statistics['validated'] + $statistics['completed'] }}
                </strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">❌</span>

            <div>
                <small>Dossiers rejetés</small>
                <strong>{{ $statistics['rejected'] }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">🧑‍💼</span>

            <div>
                <small>Agents actifs</small>
                <strong>{{ $statistics['agents'] }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">📋</span>

            <div>
                <small>Démarches du ministère</small>
                <strong>{{ $statistics['procedures'] }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card decision-kpi-primary">
            <span class="decision-kpi-icon">💰</span>

            <div>
                <small>Recettes encaissées</small>

                <strong>
                    {{ number_format(
                        $statistics['revenue'],
                        0,
                        ',',
                        ' '
                    ) }}
                    FCFA
                </strong>
            </div>
        </article>

    </div>

    <div
    id="performance"
    class="decision-secondary-kpis"
>

        <div>
            <small>Taux de validation</small>
            <strong>{{ $statistics['validation_rate'] }} %</strong>
        </div>

        <div>
            <small>Documents officiels</small>
            <strong>{{ $statistics['official_documents'] }}</strong>
        </div>

        <div>
            <small>Dossiers en retard</small>
            <strong>{{ $lateApplications }}</strong>
        </div>

        <div>
            <small>Dossiers terminés</small>
            <strong>{{ $statistics['completed'] }}</strong>
        </div>

        <div>
            <small>Agents actifs</small>
            <strong>{{ $statistics['agents'] }}</strong>
        </div>

        <div>
            <small>Ministère</small>
            <strong>{{ $ministry->code ?? '—' }}</strong>
        </div>

    </div>

    @if ($lateApplications > 0)
        <div class="alert alert-warning">
            <strong>Attention :</strong>
            {{ $lateApplications }} dossier(s) sont en traitement
            depuis plus de sept jours.
        </div>
    @endif

    <article
    id="dossiers"
    class="decision-panel"
>

        <div class="decision-panel-header">
            <div>
                <h2>Dossiers récents du ministère</h2>

                <p>
                    Dernières demandes relevant des démarches
                    administrées par votre ministère.
                </p>
            </div>

            <span>📁</span>
        </div>

        <div class="decision-table-wrapper">

            <table class="decision-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Citoyen</th>
                        <th>Démarche</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($recentApplications as $application)
                        <tr>
                            <td>
                                <strong>
                                    {{ $application->reference }}
                                </strong>
                            </td>

                            <td>{{ $application->citizen_name }}</td>

                            <td>{{ $application->procedure_title }}</td>

                            <td>
                                {{ ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $application->priority ?? 'normale'
                                    )
                                ) }}
                            </td>

                            <td>
                                {{ ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $application->status
                                    )
                                ) }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse(
                                    $application->created_at
                                )->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                Aucun dossier n’est actuellement rattaché
                                aux démarches de ce ministère.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </article>

    <article
    id="agents"
    class="decision-panel"
    style="margin-top: 18px;"
>

        <div class="decision-panel-header">
            <div>
                <h2>Performance des agents</h2>

                <p>
                    Activité des agents affectés à votre ministère.
                </p>
            </div>

            <span>👥</span>
        </div>

        <div class="decision-table-wrapper">

            <table class="decision-table">
                <thead>
                    <tr>
                        <th>Agent</th>
                        <th>Adresse électronique</th>
                        <th>Dossiers affectés</th>
                        <th>En traitement</th>
                        <th>Traités</th>
                        <th>État</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($agents as $agent)
                        <tr>
                            <td>
                                <strong>{{ $agent->name }}</strong>
                            </td>

                            <td>{{ $agent->email }}</td>

                            <td>{{ $agent->assigned_count }}</td>
                            <td>{{ $agent->processing_count }}</td>
                            <td>{{ $agent->completed_count }}</td>

                            <td>
                                {{ $agent->active
                                    ? 'Actif'
                                    : 'Désactivé' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                Aucun agent n’est affecté à ce ministère.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </article>

</section>

@endsection