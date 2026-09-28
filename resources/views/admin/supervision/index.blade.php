@extends('layouts.admin')

@section('title', 'Supervision nationale')

@section('content')

<section class="national-supervision">

    <div class="supervision-heading">

        <div>
            <span class="supervision-kicker">
                CENTRE DE PILOTAGE NATIONAL
            </span>

            <h1>Supervision nationale</h1>

            <p>
                Situation consolidée de la Plateforme Nationale
                d’Administration Électronique.
            </p>
        </div>
		<div class="supervision-toolbar">
    <a
        href="{{ route('admin.supervision.report.pdf') }}"
        class="supervision-export-button"
    >
        📄 Exporter PDF
    </a>

    <a
        href="{{ route('admin.supervision.report.excel') }}"
        class="supervision-export-button supervision-export-excel"
    >
        📊 Exporter Excel
    </a>

    <button
        type="button"
        id="refreshDashboard"
        class="supervision-refresh-button"
    >
        🔄 Actualiser
    </button>

    <span class="supervision-live-indicator">
        <span></span>
        Actualisation toutes les 30 secondes
    </span>
</div>

        <div class="supervision-date">
            <span>Dernière actualisation</span>
            <strong>{{ now()->format('d/m/Y H:i') }}</strong>
        </div>

    </div>

    {{-- =====================================================
         INDICATEURS PRINCIPAUX
         ===================================================== --}}
    <div class="national-kpi-grid">

        <article class="national-kpi-card">
            <div class="national-kpi-icon">👥</div>
            <div>
                <span>Citoyens</span>
                <strong>
                    {{ number_format($dashboard['summary']['citizens'], 0, ',', ' ') }}
                </strong>
            </div>
        </article>

        <article class="national-kpi-card">
            <div class="national-kpi-icon">🧑‍💼</div>
            <div>
                <span>Agents publics</span>
                <strong>
                    {{ number_format($dashboard['summary']['agents'], 0, ',', ' ') }}
                </strong>
            </div>
        </article>

        <article class="national-kpi-card">
            <div class="national-kpi-icon">🏛️</div>
            <div>
                <span>Ministères</span>
                <strong>
                    {{ number_format($dashboard['summary']['ministries'], 0, ',', ' ') }}
                </strong>
            </div>
        </article>

        <article class="national-kpi-card">
            <div class="national-kpi-icon">📋</div>
            <div>
                <span>Démarches</span>
                <strong>
                    {{ number_format($dashboard['summary']['procedures'], 0, ',', ' ') }}
                </strong>
            </div>
        </article>

        <article class="national-kpi-card">
            <div class="national-kpi-icon">📁</div>
            <div>
                <span>Total dossiers</span>
                <strong>
                    {{ number_format($dashboard['summary']['applications'], 0, ',', ' ') }}
                </strong>
            </div>
        </article>

        <article class="national-kpi-card">
            <div class="national-kpi-icon">📆</div>
            <div>
                <span>Dossiers ce mois</span>
                <strong>
                    {{ number_format($dashboard['summary']['applications_month'], 0, ',', ' ') }}
                </strong>
            </div>
        </article>

        <article class="national-kpi-card">
            <div class="national-kpi-icon">💳</div>
            <div>
                <span>Paiements confirmés</span>
                <strong>
                    {{ number_format($dashboard['paymentSummary']['paye'], 0, ',', ' ') }}
                </strong>
            </div>
        </article>

        <article class="national-kpi-card national-kpi-revenue">
            <div class="national-kpi-icon">💰</div>
            <div>
                <span>Recettes nationales</span>
                <strong>
                    {{
                        number_format(
                            $dashboard['summary']['revenue_total'],
                            0,
                            ',',
                            ' '
                        )
                    }}
                    FCFA
                </strong>
            </div>
        </article>

    </div>

    {{-- =====================================================
         ACTIVITÉ DE LA PÉRIODE
         ===================================================== --}}
    <div class="period-summary-grid">

        <article>
            <span>Aujourd’hui</span>
            <strong>
                {{ $dashboard['summary']['applications_today'] }}
            </strong>
            <small>nouvelle(s) demande(s)</small>
        </article>

        <article>
            <span>Cette semaine</span>
            <strong>
                {{ $dashboard['summary']['applications_week'] }}
            </strong>
            <small>nouvelle(s) demande(s)</small>
        </article>

        <article>
            <span>Ce mois</span>
            <strong>
                {{ $dashboard['summary']['applications_month'] }}
            </strong>
            <small>nouvelle(s) demande(s)</small>
        </article>

        <article>
            <span>Recettes du jour</span>
            <strong>
                {{
                    number_format(
                        $dashboard['summary']['revenue_today'],
                        0,
                        ',',
                        ' '
                    )
                }}
            </strong>
            <small>FCFA encaissés</small>
        </article>

        <article>
            <span>Recettes du mois</span>
            <strong>
                {{
                    number_format(
                        $dashboard['summary']['revenue_month'],
                        0,
                        ',',
                        ' '
                    )
                }}
            </strong>
            <small>FCFA encaissés</small>
        </article>

        <article>
            <span>Documents officiels</span>
            <strong>
                {{ $dashboard['summary']['official_documents'] }}
            </strong>
            <small>documents générés</small>
        </article>

    </div>

    {{-- =====================================================
         GRAPHIQUES
         ===================================================== --}}
    <div class="supervision-grid supervision-grid-two">

        <section class="supervision-panel">
            <div class="supervision-panel-heading">
                <div>
                    <h2>Évolution des demandes</h2>
                    <p>Douze derniers mois.</p>
                </div>
                <span>📈</span>
            </div>

            <div class="chart-container">
                <canvas id="applicationsChart"></canvas>
            </div>
        </section>

        <section class="supervision-panel">
            <div class="supervision-panel-heading">
                <div>
                    <h2>Recettes mensuelles</h2>
                    <p>Paiements confirmés en FCFA.</p>
                </div>
                <span>💰</span>
            </div>

            <div class="chart-container">
                <canvas id="paymentsChart"></canvas>
            </div>
        </section>

    </div>

    <div class="supervision-grid supervision-grid-two">

        <section class="supervision-panel">
            <div class="supervision-panel-heading">
                <div>
                    <h2>Répartition des dossiers</h2>
                    <p>Situation actuelle par statut.</p>
                </div>
                <span>📊</span>
            </div>

            <div class="chart-container chart-container-small">
                <canvas id="statusesChart"></canvas>
            </div>
        </section>

        <section class="supervision-panel">
            <div class="supervision-panel-heading">
                <div>
                    <h2>Situation des paiements</h2>
                    <p>Transactions par statut.</p>
                </div>
                <span>💳</span>
            </div>

            <div class="chart-container chart-container-small">
                <canvas id="paymentStatusesChart"></canvas>
            </div>
        </section>

    </div>
<div class="supervision-grid supervision-grid-two">

    <section class="supervision-panel">
        <div class="supervision-panel-heading">
            <div>
                <h2>Indice national de performance</h2>
                <p>
                    Validation, paiements, documents et taux de rejet.
                </p>
            </div>
            <span>🎯</span>
        </div>

        <div class="national-score-layout">
            <div
                class="national-score-circle"
                style="--score: {{ $dashboard['nationalScore']['score'] }}"
            >
                <strong>
                    {{ $dashboard['nationalScore']['score'] }} %
                </strong>

                <span>{{ $dashboard['nationalScore']['label'] }}</span>
            </div>

            <div class="national-score-details">
                <div>
                    <span>Validation</span>
                    <strong>
                        {{ $dashboard['nationalScore']['validation_rate'] }} %
                    </strong>
                </div>

                <div>
                    <span>Paiement</span>
                    <strong>
                        {{ $dashboard['nationalScore']['payment_rate'] }} %
                    </strong>
                </div>

                <div>
                    <span>Documents produits</span>
                    <strong>
                        {{ $dashboard['nationalScore']['document_rate'] }} %
                    </strong>
                </div>

                <div>
                    <span>Rejet</span>
                    <strong>
                        {{ $dashboard['nationalScore']['rejection_rate'] }} %
                    </strong>
                </div>
            </div>
        </div>
    </section>

    <section class="supervision-panel">
        <div class="supervision-panel-heading">
            <div>
                <h2>Objectifs mensuels</h2>
                <p>Suivi des cibles nationales du mois.</p>
            </div>
            <span>🏁</span>
        </div>

        <div class="objective-item">
            <div class="objective-header">
                <span>Dossiers déposés</span>

                <strong>
                    {{ $dashboard['monthlyObjective']['applications']['actual'] }}
                    /
                    {{ $dashboard['monthlyObjective']['applications']['target'] }}
                </strong>
            </div>

            <div class="objective-track">
                <div
                    class="objective-value"
                    style="width: {{
                        $dashboard['monthlyObjective']['applications']['percentage']
                    }}%"
                ></div>
            </div>
        </div>

        <div class="objective-item">
            <div class="objective-header">
                <span>Recettes nationales</span>

                <strong>
                    {{
                        number_format(
                            $dashboard['monthlyObjective']['revenue']['actual'],
                            0,
                            ',',
                            ' '
                        )
                    }}
                    /
                    {{
                        number_format(
                            $dashboard['monthlyObjective']['revenue']['target'],
                            0,
                            ',',
                            ' '
                        )
                    }}
                    FCFA
                </strong>
            </div>

            <div class="objective-track">
                <div
                    class="objective-value objective-value-revenue"
                    style="width: {{
                        $dashboard['monthlyObjective']['revenue']['percentage']
                    }}%"
                ></div>
            </div>
        </div>

        <div class="average-processing-card">
            <span>Temps moyen de traitement</span>

            <strong>
                {{ $dashboard['averageProcessing']['global_days'] }}
                jour(s)
            </strong>
        </div>
    </section>

</div>
    {{-- =====================================================
         ALERTES
         ===================================================== --}}
    <section class="supervision-panel">

        <div class="supervision-panel-heading">
            <div>
                <h2>Centre d’alertes</h2>
                <p>Éléments nécessitant une attention administrative.</p>
            </div>
            <span>🚨</span>
        </div>

        <div class="national-alert-grid">

            @foreach($dashboard['alerts'] as $alert)

                <article class="national-alert national-alert-{{ $alert['level'] }}">

                    <div class="national-alert-value">
                        {{ $alert['count'] }}
                    </div>

                    <div>
                        <strong>{{ $alert['title'] }}</strong>
                        <p>{{ $alert['description'] }}</p>
                    </div>

                </article>

            @endforeach

        </div>

    </section>

    {{-- =====================================================
         PERFORMANCE DES MINISTÈRES
         ===================================================== --}}
    <section class="supervision-panel">

        <div class="supervision-panel-heading">
            <div>
                <h2>Performance des ministères</h2>
                <p>Comparaison nationale du traitement des dossiers.</p>
            </div>
            <span>🏛️</span>
        </div>

        <div class="table-responsive">

            <table class="rca-table national-performance-table">

                <thead>
                    <tr>
                        <th>Ministère</th>
                        <th>Démarches</th>
                        <th>Dossiers</th>
                        <th>Validés</th>
                        <th>En traitement</th>
                        <th>Rejetés</th>
                        <th>Taux de validation</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($dashboard['ministryPerformance'] as $ministry)

                        <tr>
                            <td>
                                <strong>{{ $ministry->name }}</strong>
                            </td>

                            <td>{{ $ministry->procedures_count }}</td>

                            <td>{{ $ministry->total_applications }}</td>

                            <td>
                                <span class="metric-success">
                                    {{ $ministry->validated_applications }}
                                </span>
                            </td>

                            <td>
                                <span class="metric-warning">
                                    {{ $ministry->processing_applications }}
                                </span>
                            </td>

                            <td>
                                <span class="metric-danger">
                                    {{ $ministry->rejected_applications }}
                                </span>
                            </td>

                            <td>
                                <div class="performance-rate">
                                    <div class="performance-rate-header">
                                        <span>
                                            {{ $ministry->validation_rate }} %
                                        </span>
                                    </div>

                                    <div class="performance-rate-track">
                                        <div
                                            class="performance-rate-value"
                                            style="width: {{
                                                min(
                                                    100,
                                                    $ministry->validation_rate
                                                )
                                            }}%"
                                        ></div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center">
                                Aucun ministère enregistré.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
			<div class="supervision-grid supervision-grid-two">

    <section class="supervision-panel">
        <div class="supervision-panel-heading">
            <div>
                <h2>Top des démarches</h2>
                <p>Services les plus sollicités.</p>
            </div>
            <span>🏆</span>
        </div>

        <div class="procedure-ranking">
            @forelse($dashboard['topProcedures'] as $procedure)
                <article>
                    <span class="procedure-rank">
                        {{ $loop->iteration }}
                    </span>

                    <div>
                        <strong>{{ $procedure->title }}</strong>

                        <small>
                            {{ $procedure->ministry->name ?? '-' }}
                        </small>
                    </div>

                    <span class="procedure-total">
                        {{ $procedure->applications_count }}
                    </span>
                </article>
            @empty
                <p>Aucune donnée disponible.</p>
            @endforelse
        </div>
    </section>

    <section class="supervision-panel">
        <div class="supervision-panel-heading">
            <div>
                <h2>Activité des 35 derniers jours</h2>
                <p>Nombre quotidien de nouvelles demandes.</p>
            </div>
            <span>🗓️</span>
        </div>

        <div class="activity-heatmap">
            @foreach($dashboard['activityHeatmap'] as $day)
                <span
                    class="heatmap-cell heatmap-level-{{ $day['level'] }}"
                    title="{{ $day['label'] }} : {{ $day['total'] }} demande(s)"
                ></span>
            @endforeach
        </div>

        <div class="heatmap-legend">
            <span>Moins</span>
            <i class="heatmap-level-0"></i>
            <i class="heatmap-level-1"></i>
            <i class="heatmap-level-2"></i>
            <i class="heatmap-level-3"></i>
            <i class="heatmap-level-4"></i>
            <span>Plus</span>
        </div>
    </section>

</div>

        </div>

    </section>

    {{-- =====================================================
         DERNIÈRES OPÉRATIONS
         ===================================================== --}}
    <div class="supervision-grid supervision-grid-two">

        <section class="supervision-panel">

            <div class="supervision-panel-heading">
                <div>
                    <h2>Derniers paiements</h2>
                    <p>Transactions récemment enregistrées.</p>
                </div>
                <span>💳</span>
            </div>

            <div class="compact-list">

                @forelse($dashboard['recentPayments'] as $payment)

                    <article class="compact-list-item">

                        <div>
                            <strong>{{ $payment->reference }}</strong>

                            <span>
                                {{ $payment->user->name ?? 'Utilisateur' }}
                                —
                                {{
                                    $payment->application
                                        ->procedure
                                        ->title
                                    ?? '-'
                                }}
                            </span>
                        </div>

                        <div class="compact-list-meta">
                            <strong>
                                {{
                                    number_format(
                                        (float) $payment->amount,
                                        0,
                                        ',',
                                        ' '
                                    )
                                }}
                                FCFA
                            </strong>

                            <x-status-badge
                                :status="$payment->status"
                                :label="$payment->status_label"
                            />
                        </div>

                    </article>

                @empty

                    <p class="empty-compact-list">
                        Aucun paiement enregistré.
                    </p>

                @endforelse

            </div>

        </section>

        <section class="supervision-panel">

            <div class="supervision-panel-heading">
                <div>
                    <h2>Documents officiels récents</h2>
                    <p>Derniers documents générés.</p>
                </div>
                <span>📜</span>
            </div>

            <div class="compact-list">

                @forelse($dashboard['recentDocuments'] as $document)

                    <article class="compact-list-item">

                        <div>
                            <strong>
                                {{ $document->official_number }}
                            </strong>

                            <span>
                                {{
                                    $document->application->user->name
                                    ?? 'Citoyen'
                                }}
                                —
                                {{
                                    $document->application
                                        ->procedure
                                        ->title
                                    ?? '-'
                                }}
                            </span>
                        </div>

                        <div class="compact-list-meta">
                            <span>
                                {{
                                    $document->issued_at
                                        ?->format('d/m/Y H:i')
                                }}
                            </span>

                            <x-status-badge
                                :status="$document->status"
                                :label="$document->status_label"
                            />
                        </div>

                    </article>

                @empty

                    <p class="empty-compact-list">
                        Aucun document officiel généré.
                    </p>

                @endforelse

            </div>

        </section>

    </div>


<div class="supervision-grid supervision-grid-two">

    <section class="supervision-panel">
        <div class="supervision-panel-heading">
            <div>
                <h2>Activité récente</h2>
                <p>Dernières opérations importantes.</p>
            </div>
            <span>🕒</span>
        </div>

        <div class="activity-timeline">
            @forelse($dashboard['recentActivity'] as $activity)
                <article>
                    <time>
                        {{
                            \Carbon\Carbon::parse($activity->created_at)
                                ->format('H:i')
                        }}
                    </time>

                    <span class="activity-dot"></span>

                    <div>
                        <strong>{{ $activity->action }}</strong>

                        <small>
                            {{ $activity->user_name ?? 'Système' }}
                            @if($activity->entity)
                                — {{ $activity->entity }}
                            @endif
                        </small>
                    </div>
                </article>
            @empty
                <p>Aucune activité récente.</p>
            @endforelse
        </div>
    </section>

    <section class="supervision-panel">
        <div class="supervision-panel-heading">
            <div>
                <h2>Audit de sécurité</h2>
                <p>Événements sensibles des trente derniers jours.</p>
            </div>
            <span>🛡️</span>
        </div>

        <div class="security-audit-grid">
            <article>
                <span>Connexions échouées</span>
                <strong>
                    {{ $dashboard['securityAudit']['failed_logins'] }}
                </strong>
            </article>

            <article>
                <span>Paiements annulés</span>
                <strong>
                    {{ $dashboard['securityAudit']['cancelled_payments'] }}
                </strong>
            </article>

            <article>
                <span>Documents révoqués</span>
                <strong>
                    {{ $dashboard['securityAudit']['revoked_documents'] }}
                </strong>
            </article>

            <article>
                <span>Erreurs critiques</span>
                <strong>
                    {{ $dashboard['securityAudit']['critical_errors'] }}
                </strong>
            </article>
        </div>
    </section>

</div>
    {{-- =====================================================
         SANTÉ DE LA PLATEFORME
         ===================================================== --}}
    <section class="supervision-panel">

        <div class="supervision-panel-heading">
            <div>
                <h2>Santé de la plateforme</h2>
                <p>État des principaux composants techniques.</p>
            </div>
            <span>🩺</span>
        </div>

        <div class="system-health-grid">

            @foreach($dashboard['systemHealth'] as $name => $health)

                <article class="system-health-card">

                    <span class="system-health-indicator {{
                        $health['status']
                            ? 'system-health-ok'
                            : 'system-health-error'
                    }}"></span>

                    <div>
                        <strong>
                            {{ ucfirst($name) }}
                        </strong>

                        <span>{{ $health['label'] }}</span>
                    </div>

                </article>

            @endforeach

        </div>

    </section>

</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n’est pas chargé.');
        return;
    }

    new Chart(
        document.getElementById('applicationsChart'),
        {
            type: 'line',
            data: {
                labels: @json($dashboard['monthlyApplications']['labels']),
                datasets: [{
                    label: 'Demandes',
                    data: @json($dashboard['monthlyApplications']['values']),
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        }
    );

    new Chart(
        document.getElementById('paymentsChart'),
        {
            type: 'bar',
            data: {
                labels: @json($dashboard['monthlyPayments']['labels']),
                datasets: [{
                    label: 'Recettes FCFA',
                    data: @json($dashboard['monthlyPayments']['values']),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        }
    );

    new Chart(
        document.getElementById('statusesChart'),
        {
            type: 'doughnut',
            data: {
                labels: [
                    'Soumises',
                    'En traitement',
                    'Validées',
                    'Rejetées',
                    'Terminées'
                ],
                datasets: [{
                    data: [
                        {{ $dashboard['applicationStatuses']['soumise'] }},
                        {{ $dashboard['applicationStatuses']['en_traitement'] }},
                        {{ $dashboard['applicationStatuses']['validee'] }},
                        {{ $dashboard['applicationStatuses']['rejetee'] }},
                        {{ $dashboard['applicationStatuses']['terminee'] }}
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    );

    new Chart(
        document.getElementById('paymentStatusesChart'),
        {
            type: 'doughnut',
            data: {
                labels: [
                    'En attente',
                    'Payés',
                    'Échoués',
                    'Remboursés'
                ],
                datasets: [{
                    data: [
                        {{ $dashboard['paymentSummary']['en_attente'] }},
                        {{ $dashboard['paymentSummary']['paye'] }},
                        {{ $dashboard['paymentSummary']['echoue'] }},
                        {{ $dashboard['paymentSummary']['rembourse'] }}
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    );
});

const refreshUrl = @json(route('admin.supervision.data'));

async function refreshNationalDashboard() {
    const button = document.getElementById('refreshDashboard');

    try {
        if (button) {
            button.disabled = true;
            button.textContent = '⏳ Actualisation...';
        }

        const response = await fetch(refreshUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Actualisation impossible');
        }

        /*
         * Rechargement propre afin de synchroniser aussi les tableaux
         * et les graphiques Chart.js.
         */
        window.location.reload();
    } catch (error) {
        console.error(error);

        if (button) {
            button.disabled = false;
            button.textContent = '⚠️ Réessayer';
        }
    }
}

document
    .getElementById('refreshDashboard')
    ?.addEventListener('click', refreshNationalDashboard);

window.setTimeout(
    refreshNationalDashboard,
    30000
);
</script>
@endpush