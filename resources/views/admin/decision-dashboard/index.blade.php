@extends('layouts.admin')

@section('title', 'Pilotage décisionnel national')

@section('content')

<section class="decision-dashboard">

    <div class="decision-heading">

        <div>
            <span class="decision-kicker">
                CENTRE NATIONAL DE PILOTAGE
            </span>

            <h1>Tableau de bord décisionnel</h1>

            <p>
                Suivi consolidé des services publics numériques,
                des dossiers, paiements et performances ministérielles.
            </p>
        </div>

        <div class="decision-updated-at">
            <small>Dernière actualisation</small>
            <strong>{{ now()->format('d/m/Y H:i') }}</strong>
        </div>

    </div>

    <div class="decision-kpi-grid">

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">👥</span>
            <div>
                <small>Citoyens</small>
                <strong>{{ number_format($kpis['citizens'], 0, ',', ' ') }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">🧑‍💼</span>
            <div>
                <small>Agents et responsables</small>
                <strong>{{ number_format($kpis['agents'], 0, ',', ' ') }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">🏛️</span>
            <div>
                <small>Ministères actifs</small>
                <strong>{{ number_format($kpis['ministries'], 0, ',', ' ') }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">📋</span>
            <div>
                <small>Démarches actives</small>
                <strong>{{ number_format($kpis['procedures'], 0, ',', ' ') }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">📁</span>
            <div>
                <small>Total dossiers</small>
                <strong>{{ number_format($kpis['applications'], 0, ',', ' ') }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">✅</span>
            <div>
                <small>Dossiers validés</small>
                <strong>
                    {{ number_format(
                        $kpis['validated'] + $kpis['completed'],
                        0,
                        ',',
                        ' '
                    ) }}
                </strong>
            </div>
        </article>

        <article class="decision-kpi-card">
            <span class="decision-kpi-icon">💳</span>
            <div>
                <small>Paiements confirmés</small>
                <strong>{{ number_format($kpis['paid_payments'], 0, ',', ' ') }}</strong>
            </div>
        </article>

        <article class="decision-kpi-card decision-kpi-primary">
            <span class="decision-kpi-icon">💰</span>
            <div>
                <small>Recettes nationales</small>
                <strong>
                    {{ number_format(
                        $kpis['total_revenue'],
                        0,
                        ',',
                        ' '
                    ) }}
                    FCFA
                </strong>
            </div>
        </article>

    </div>

    <div class="decision-secondary-kpis">

        <div>
            <small>Dossiers du mois</small>
            <strong>{{ $kpis['applications_month'] }}</strong>
        </div>

        <div>
            <small>Soumis ou en attente</small>
            <strong>{{ $kpis['submitted'] }}</strong>
        </div>

        <div>
            <small>En traitement</small>
            <strong>{{ $kpis['processing'] }}</strong>
        </div>

        <div>
            <small>Rejetés</small>
            <strong>{{ $kpis['rejected'] }}</strong>
        </div>

        <div>
            <small>Documents officiels</small>
            <strong>{{ $kpis['official_documents'] }}</strong>
        </div>

        <div>
            <small>Temps moyen</small>
            <strong>
                {{ number_format(
                    $kpis['average_processing_days'],
                    1,
                    ',',
                    ' '
                ) }}
                jour(s)
            </strong>
        </div>

    </div>

    <div class="decision-chart-grid">

        <article class="decision-panel">
            <div class="decision-panel-header">
                <div>
                    <h2>Évolution des demandes</h2>
                    <p>Nombre mensuel de nouveaux dossiers.</p>
                </div>
                <span>📈</span>
            </div>

            <div class="decision-chart-container">
                <canvas id="decisionApplicationsChart"></canvas>
            </div>
        </article>

        <article class="decision-panel">
            <div class="decision-panel-header">
                <div>
                    <h2>Recettes mensuelles</h2>
                    <p>Paiements confirmés en FCFA.</p>
                </div>
                <span>💰</span>
            </div>

            <div class="decision-chart-container">
                <canvas id="decisionRevenueChart"></canvas>
            </div>
        </article>

        <article class="decision-panel">
            <div class="decision-panel-header">
                <div>
                    <h2>Répartition des dossiers</h2>
                    <p>Situation actuelle par statut.</p>
                </div>
                <span>📊</span>
            </div>

            <div class="decision-chart-container decision-chart-small">
                <canvas id="decisionStatusChart"></canvas>
            </div>
        </article>

        <article class="decision-panel">
            <div class="decision-panel-header">
                <div>
                    <h2>Situation des paiements</h2>
                    <p>Transactions classées par statut.</p>
                </div>
                <span>💳</span>
            </div>

            <div class="decision-chart-container decision-chart-small">
                <canvas id="decisionPaymentChart"></canvas>
            </div>
        </article>

    </div>

    <div class="decision-performance-grid">

        <article class="decision-panel">

            <div class="decision-panel-header">
                <div>
                    <h2>Indice national de performance</h2>
                    <p>
                        Validation, paiement, documents et délais.
                    </p>
                </div>
                <span>🎯</span>
            </div>

            <div class="decision-performance-content">

                <div
                    class="decision-score-circle"
                    style="--decision-score: {{ $performance['score'] }};"
                >
                    <strong>
                        {{ number_format(
                            $performance['score'],
                            1,
                            ',',
                            ' '
                        ) }} %
                    </strong>

                    <small>{{ $performance['level'] }}</small>
                </div>

                <div class="decision-performance-details">

                    <div>
                        <span>Validation</span>
                        <strong>{{ $performance['validation_rate'] }} %</strong>
                    </div>

                    <div>
                        <span>Paiement</span>
                        <strong>{{ $performance['payment_rate'] }} %</strong>
                    </div>

                    <div>
                        <span>Documents produits</span>
                        <strong>{{ $performance['document_rate'] }} %</strong>
                    </div>

                    <div>
                        <span>Rejet</span>
                        <strong>{{ $performance['rejection_rate'] }} %</strong>
                    </div>

                </div>

            </div>

        </article>

        <article class="decision-panel">

            <div class="decision-panel-header">
                <div>
                    <h2>Objectifs mensuels</h2>
                    <p>Suivi des cibles nationales du mois.</p>
                </div>
                <span>🏁</span>
            </div>

            <div class="decision-objectives">

                @foreach ($objectives as $key => $objective)

                    @php
                        $objectiveLabel = match ($key) {
                            'applications' => 'Dossiers déposés',
                            'revenue' => 'Recettes nationales',
                            'documents' => 'Documents officiels',
                            'processing_days' =>
                                'Performance du délai moyen',
                            default => ucfirst($key),
                        };
                    @endphp

                    <div class="decision-objective">

                        <div>
                            <span>{{ $objectiveLabel }}</span>

                            <strong>
                                {{ number_format(
                                    $objective['current'],
                                    $key === 'processing_days' ? 1 : 0,
                                    ',',
                                    ' '
                                ) }}
                                /
                                {{ number_format(
                                    $objective['target'],
                                    $key === 'processing_days' ? 1 : 0,
                                    ',',
                                    ' '
                                ) }}
                                {{ $objective['suffix'] }}
                            </strong>
                        </div>

                        <div class="decision-progress">
                            <span
                                style="width:
                                    {{ $objective['percentage'] }}%"
                            ></span>
                        </div>

                    </div>

                @endforeach

            </div>

        </article>

    </div>

    <article class="decision-panel decision-alert-panel">

        <div class="decision-panel-header">
            <div>
                <h2>Centre d’alertes</h2>
                <p>
                    Éléments nécessitant une attention administrative.
                </p>
            </div>

            <span>🚨</span>
        </div>

        <div class="decision-alert-grid">

            @foreach ($alerts as $alert)

                <div class="decision-alert decision-alert-{{ $alert['level'] }}">

                    <strong>{{ $alert['value'] }}</strong>

                    <div>
                        <span>{{ $alert['label'] }}</span>
                        <small>{{ $alert['description'] }}</small>
                    </div>

                </div>

            @endforeach

        </div>

    </article>

    <article class="decision-panel">

        <div class="decision-panel-header">
            <div>
                <h2>Performance des ministères</h2>
                <p>
                    Comparaison nationale du traitement des dossiers.
                </p>
            </div>

            <span>🏛️</span>
        </div>

        <div class="decision-table-wrapper">

            <table class="decision-table">
                <thead>
                    <tr>
                        <th>Ministère</th>
                        <th>Démarches</th>
                        <th>Dossiers</th>
                        <th>Validés</th>
                        <th>En traitement</th>
                        <th>Rejetés</th>
                        <th>Recettes</th>
                        <th>Taux de validation</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($ministryPerformance as $ministry)
                        <tr>
                            <td>
                                <strong>{{ $ministry['name'] }}</strong>
                            </td>

                            <td>{{ $ministry['procedures'] }}</td>
                            <td>{{ $ministry['applications'] }}</td>
                            <td>{{ $ministry['validated'] + $ministry['completed'] }}</td>
                            <td>{{ $ministry['processing'] }}</td>
                            <td>{{ $ministry['rejected'] }}</td>

                            <td>
                                {{ number_format(
                                    $ministry['revenue'],
                                    0,
                                    ',',
                                    ' '
                                ) }}
                                FCFA
                            </td>

                            <td>
                                <div class="decision-rate">
                                    <strong>
                                        {{ $ministry['validation_rate'] }} %
                                    </strong>

                                    <div>
                                        <span
                                            style="width:
                                                {{ $ministry['validation_rate'] }}%"
                                        ></span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                Aucun ministère disponible.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </article>

    <div class="decision-bottom-grid">

        <article class="decision-panel">

            <div class="decision-panel-header">
                <div>
                    <h2>Top des démarches</h2>
                    <p>Services publics les plus sollicités.</p>
                </div>

                <span>🏆</span>
            </div>

            <div class="decision-top-list">

                @forelse ($topProcedures as $index => $procedure)
                    <div>
                        <span class="decision-rank">
                            {{ $index + 1 }}
                        </span>

                        <div>
                            <strong>{{ $procedure->title }}</strong>
                            <small>
                                {{ $procedure->ministry_name ?? '—' }}
                            </small>
                        </div>

                        <b>{{ $procedure->applications_count }}</b>
                    </div>
                @empty
                    <p>Aucune démarche disponible.</p>
                @endforelse

            </div>

        </article>

        <article class="decision-panel">

            <div class="decision-panel-header">
                <div>
                    <h2>Activité des 35 derniers jours</h2>
                    <p>Nombre quotidien de nouvelles demandes.</p>
                </div>

                <span>🗓️</span>
            </div>

            <div class="decision-chart-container">
                <canvas id="decisionDailyChart"></canvas>
            </div>

        </article>

    </div>

</section>

@php
    $decisionDashboardData = [
        'monthly' => $monthlyEvolution,
        'applicationStatuses' => $applicationStatuses,
        'paymentStatuses' => $paymentStatuses,
        'dailyActivity' => $dailyActivity,
    ];
@endphp

<script>
    window.PNAEDecisionDashboard =
        {{ Illuminate\Support\Js::from($decisionDashboardData) }};
</script>

@endsection