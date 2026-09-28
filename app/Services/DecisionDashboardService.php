<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DecisionDashboardService
{
    /**
     * Statuts utilisés par PNAE-RCA.
     */
    private const SUBMITTED_STATUSES = [
        'soumise',
        'soumis',
        'en_attente',
        'pending',
        'submitted',
    ];

    private const PROCESSING_STATUSES = [
        'en_traitement',
        'processing',
    ];

    private const VALIDATED_STATUSES = [
        'validee',
        'valide',
        'approved',
        'validated',
    ];

    private const REJECTED_STATUSES = [
        'rejetee',
        'rejete',
        'rejected',
    ];

    private const COMPLETED_STATUSES = [
        'terminee',
        'termine',
        'completed',
    ];

    private const PAID_PAYMENT_STATUSES = [
        'paye',
        'payee',
        'paid',
        'confirme',
        'confirmed',
        'success',
        'succeeded',
    ];

    private const PENDING_PAYMENT_STATUSES = [
        'en_attente',
        'pending',
        'initiated',
        'initie',
    ];

    private const FAILED_PAYMENT_STATUSES = [
        'echoue',
        'failed',
        'cancelled',
        'annule',
    ];

    public function build(): array
    {
        $period = $this->currentMonthPeriod();

        $kpis = $this->buildKpis($period);
        $monthlyEvolution = $this->buildMonthlyEvolution();
        $applicationStatuses = $this->buildApplicationStatuses();
        $paymentStatuses = $this->buildPaymentStatuses();
        $ministryPerformance = $this->buildMinistryPerformance();
        $topProcedures = $this->buildTopProcedures();
        $dailyActivity = $this->buildDailyActivity();
        $objectives = $this->buildObjectives($period, $kpis);
        $alerts = $this->buildAlerts();
        $performance = $this->buildPerformanceIndex($kpis);

        return compact(
            'kpis',
            'monthlyEvolution',
            'applicationStatuses',
            'paymentStatuses',
            'ministryPerformance',
            'topProcedures',
            'dailyActivity',
            'objectives',
            'alerts',
            'performance'
        );
    }

    private function currentMonthPeriod(): array
    {
        $now = CarbonImmutable::now();

        return [
            'start' => $now->startOfMonth(),
            'end' => $now->endOfMonth(),
        ];
    }

    private function buildKpis(array $period): array
    {
        $citizens = DB::table('users')
            ->where('role', 'citoyen')
            ->count();

        $agents = DB::table('users')
            ->whereIn('role', ['agent', 'responsable'])
            ->count();

        $activeUsers = DB::table('users')
            ->where('active', true)
            ->count();

        $ministries = DB::table('ministries')
            ->where('active', true)
            ->count();

        $procedures = DB::table('procedures')
            ->where('active', true)
            ->count();

        $applications = DB::table('applications')->count();

        $applicationsThisMonth = DB::table('applications')
            ->whereBetween('created_at', [
                $period['start'],
                $period['end'],
            ])
            ->count();

        $submitted = $this->countApplicationsByStatuses(
            self::SUBMITTED_STATUSES
        );

        $processing = $this->countApplicationsByStatuses(
            self::PROCESSING_STATUSES
        );

        $validated = $this->countApplicationsByStatuses(
            self::VALIDATED_STATUSES
        );

        $rejected = $this->countApplicationsByStatuses(
            self::REJECTED_STATUSES
        );

        $completed = $this->countApplicationsByStatuses(
            self::COMPLETED_STATUSES
        );

        $paidPayments = DB::table('payments')
            ->whereIn('status', self::PAID_PAYMENT_STATUSES)
            ->count();

        $totalRevenue = (float) DB::table('payments')
            ->whereIn('status', self::PAID_PAYMENT_STATUSES)
            ->sum('amount');

        $monthlyRevenue = (float) DB::table('payments')
            ->whereIn('status', self::PAID_PAYMENT_STATUSES)
            ->whereBetween(
                DB::raw('COALESCE(paid_at, created_at)'),
                [
                    $period['start'],
                    $period['end'],
                ]
            )
            ->sum('amount');

        $officialDocuments = Schema::hasTable('official_documents')
            ? DB::table('official_documents')->count()
            : 0;

        $documentsThisMonth = Schema::hasTable('official_documents')
            ? DB::table('official_documents')
                ->whereBetween(
                    DB::raw('COALESCE(issued_at, created_at)'),
                    [
                        $period['start'],
                        $period['end'],
                    ]
                )
                ->count()
            : 0;

        $averageProcessingDays = $this->averageProcessingDays();

        return [
            'citizens' => $citizens,
            'agents' => $agents,
            'active_users' => $activeUsers,
            'ministries' => $ministries,
            'procedures' => $procedures,
            'applications' => $applications,
            'applications_month' => $applicationsThisMonth,
            'submitted' => $submitted,
            'processing' => $processing,
            'validated' => $validated,
            'rejected' => $rejected,
            'completed' => $completed,
            'paid_payments' => $paidPayments,
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'official_documents' => $officialDocuments,
            'documents_month' => $documentsThisMonth,
            'average_processing_days' => $averageProcessingDays,
        ];
    }

    private function countApplicationsByStatuses(array $statuses): int
    {
        return DB::table('applications')
            ->whereIn('status', $statuses)
            ->count();
    }

    private function buildMonthlyEvolution(): array
    {
        $labels = [];
        $applications = [];
        $revenues = [];

        $startMonth = CarbonImmutable::now()
            ->startOfMonth()
            ->subMonths(11);

        for ($index = 0; $index < 12; $index++) {
            $month = $startMonth->addMonths($index);

            $start = $month->startOfMonth();
            $end = $month->endOfMonth();

            $labels[] = ucfirst(
                $month->locale('fr_FR')->translatedFormat('M Y')
            );

            $applications[] = DB::table('applications')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $revenues[] = (float) DB::table('payments')
                ->whereIn(
                    'status',
                    self::PAID_PAYMENT_STATUSES
                )
                ->whereBetween(
                    DB::raw('COALESCE(paid_at, created_at)'),
                    [$start, $end]
                )
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'applications' => $applications,
            'revenues' => $revenues,
        ];
    }

    private function buildApplicationStatuses(): array
    {
        return [
            'labels' => [
                'Soumises',
                'En traitement',
                'Validées',
                'Rejetées',
                'Terminées',
            ],

            'values' => [
                $this->countApplicationsByStatuses(
                    self::SUBMITTED_STATUSES
                ),

                $this->countApplicationsByStatuses(
                    self::PROCESSING_STATUSES
                ),

                $this->countApplicationsByStatuses(
                    self::VALIDATED_STATUSES
                ),

                $this->countApplicationsByStatuses(
                    self::REJECTED_STATUSES
                ),

                $this->countApplicationsByStatuses(
                    self::COMPLETED_STATUSES
                ),
            ],
        ];
    }

    private function buildPaymentStatuses(): array
    {
        return [
            'labels' => [
                'En attente',
                'Payés',
                'Échoués ou annulés',
            ],

            'values' => [
                DB::table('payments')
                    ->whereIn(
                        'status',
                        self::PENDING_PAYMENT_STATUSES
                    )
                    ->count(),

                DB::table('payments')
                    ->whereIn(
                        'status',
                        self::PAID_PAYMENT_STATUSES
                    )
                    ->count(),

                DB::table('payments')
                    ->whereIn(
                        'status',
                        self::FAILED_PAYMENT_STATUSES
                    )
                    ->count(),
            ],
        ];
    }

    private function buildMinistryPerformance(): Collection
    {
        $ministries = DB::table('ministries')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return $ministries->map(function (object $ministry): array {
            $procedureIds = DB::table('procedures')
                ->where('ministry_id', $ministry->id)
                ->pluck('id');

            $applicationsQuery = DB::table('applications')
                ->whereIn('procedure_id', $procedureIds);

            $applicationIds = (clone $applicationsQuery)
                ->pluck('id');

            $totalApplications = (clone $applicationsQuery)
                ->count();

            $validated = (clone $applicationsQuery)
                ->whereIn(
                    'status',
                    self::VALIDATED_STATUSES
                )
                ->count();

            $processing = (clone $applicationsQuery)
                ->whereIn(
                    'status',
                    self::PROCESSING_STATUSES
                )
                ->count();

            $rejected = (clone $applicationsQuery)
                ->whereIn(
                    'status',
                    self::REJECTED_STATUSES
                )
                ->count();

            $completed = (clone $applicationsQuery)
                ->whereIn(
                    'status',
                    self::COMPLETED_STATUSES
                )
                ->count();

            $revenue = (float) DB::table('payments')
                ->whereIn('application_id', $applicationIds)
                ->whereIn(
                    'status',
                    self::PAID_PAYMENT_STATUSES
                )
                ->sum('amount');

            $validationRate = $totalApplications > 0
                ? round(
                    (($validated + $completed) / $totalApplications)
                    * 100,
                    1
                )
                : 0.0;

            return [
                'id' => $ministry->id,
                'name' => $ministry->name,
                'procedures' => $procedureIds->count(),
                'applications' => $totalApplications,
                'validated' => $validated,
                'processing' => $processing,
                'rejected' => $rejected,
                'completed' => $completed,
                'revenue' => $revenue,
                'validation_rate' => $validationRate,
            ];
        })->sortByDesc('applications')->values();
    }

    private function buildTopProcedures(): Collection
    {
        return DB::table('procedures')
            ->leftJoin(
                'ministries',
                'ministries.id',
                '=',
                'procedures.ministry_id'
            )
            ->leftJoin(
                'applications',
                'applications.procedure_id',
                '=',
                'procedures.id'
            )
            ->select([
                'procedures.id',
                'procedures.title',
                'ministries.name as ministry_name',
            ])
            ->selectRaw(
                'COUNT(applications.id) AS applications_count'
            )
            ->where('procedures.active', true)
            ->groupBy(
                'procedures.id',
                'procedures.title',
                'ministries.name'
            )
            ->orderByDesc('applications_count')
            ->limit(10)
            ->get();
    }

    private function buildDailyActivity(): array
    {
        $labels = [];
        $values = [];

        $start = CarbonImmutable::today()->subDays(34);

        for ($index = 0; $index < 35; $index++) {
            $date = $start->addDays($index);

            $labels[] = $date->format('d/m');

            $values[] = DB::table('applications')
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function buildObjectives(
        array $period,
        array $kpis
    ): array {
        $targets = config(
            'decision_dashboard.monthly_targets'
        );

        return [
            'applications' => $this->objectiveItem(
                current: $kpis['applications_month'],
                target: (float) $targets['applications'],
                suffix: 'dossier(s)'
            ),

            'revenue' => $this->objectiveItem(
                current: $kpis['monthly_revenue'],
                target: (float) $targets['revenue'],
                suffix: 'FCFA'
            ),

            'documents' => $this->objectiveItem(
                current: $kpis['documents_month'],
                target: (float) $targets['official_documents'],
                suffix: 'document(s)'
            ),

            'processing_days' => [
                'current' => $kpis['average_processing_days'],
                'target' => (float) $targets['processing_days'],
                'percentage' => $kpis['average_processing_days'] > 0
                    ? min(
                        100,
                        round(
                            (
                                (float) $targets['processing_days']
                                / $kpis['average_processing_days']
                            ) * 100,
                            1
                        )
                    )
                    : 100,
                'suffix' => 'jour(s)',
            ],
        ];
    }

    private function objectiveItem(
        float $current,
        float $target,
        string $suffix
    ): array {
        return [
            'current' => $current,
            'target' => $target,
            'percentage' => $target > 0
                ? min(
                    100,
                    round(($current / $target) * 100, 1)
                )
                : 0,
            'suffix' => $suffix,
        ];
    }

    private function buildAlerts(): array
    {
        $submittedDays = (int) config(
            'decision_dashboard.alerts.submitted_days',
            3
        );

        $processingDays = (int) config(
            'decision_dashboard.alerts.processing_days',
            7
        );

        $submittedWaiting = DB::table('applications')
            ->whereIn(
                'status',
                self::SUBMITTED_STATUSES
            )
            ->where(
                'created_at',
                '<',
                CarbonImmutable::now()->subDays($submittedDays)
            )
            ->count();

        $processingLate = DB::table('applications')
            ->whereIn(
                'status',
                self::PROCESSING_STATUSES
            )
            ->where(
                DB::raw('COALESCE(submitted_at, created_at)'),
                '<',
                CarbonImmutable::now()->subDays($processingDays)
            )
            ->count();

        $pendingPayments = DB::table('payments')
            ->whereIn(
                'status',
                self::PENDING_PAYMENT_STATUSES
            )
            ->count();

        $failedPayments = DB::table('payments')
            ->whereIn(
                'status',
                self::FAILED_PAYMENT_STATUSES
            )
            ->count();

        $ministriesWithoutAgents = DB::table('ministries')
            ->where('active', true)
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('users')
                    ->whereColumn(
                        'users.ministry_id',
                        'ministries.id'
                    )
                    ->whereIn(
                        'users.role',
                        ['agent', 'responsable']
                    )
                    ->where('users.active', true);
            })
            ->count();

        $documentsToGenerate = DB::table('applications')
            ->join(
                'procedures',
                'procedures.id',
                '=',
                'applications.procedure_id'
            )
            ->where(
                'procedures.official_document_required',
                true
            )
            ->whereIn(
                'applications.status',
                array_merge(
                    self::VALIDATED_STATUSES,
                    self::COMPLETED_STATUSES
                )
            )
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('official_documents')
                    ->whereColumn(
                        'official_documents.application_id',
                        'applications.id'
                    );
            })
            ->count();

        $failedJobs = Schema::hasTable('failed_jobs')
            ? DB::table('failed_jobs')->count()
            : 0;

        return [
            [
                'label' => 'Demandes soumises en attente',
                'description' =>
                    "Dossiers déposés depuis plus de {$submittedDays} jours.",
                'value' => $submittedWaiting,
                'level' => $submittedWaiting > 0
                    ? 'warning'
                    : 'success',
            ],

            [
                'label' => 'Dossiers en retard',
                'description' =>
                    "Dossiers en traitement depuis plus de {$processingDays} jours.",
                'value' => $processingLate,
                'level' => $processingLate > 0
                    ? 'danger'
                    : 'success',
            ],

            [
                'label' => 'Paiements en attente',
                'description' =>
                    'Paiements initiés mais non encore confirmés.',
                'value' => $pendingPayments,
                'level' => $pendingPayments > 0
                    ? 'warning'
                    : 'success',
            ],

            [
                'label' => 'Paiements échoués',
                'description' =>
                    'Transactions annulées ou ayant échoué.',
                'value' => $failedPayments,
                'level' => $failedPayments > 0
                    ? 'danger'
                    : 'success',
            ],

            [
                'label' => 'Ministères sans agent actif',
                'description' =>
                    'Organisations actives sans agent ni responsable.',
                'value' => $ministriesWithoutAgents,
                'level' => $ministriesWithoutAgents > 0
                    ? 'danger'
                    : 'success',
            ],

            [
                'label' => 'Documents officiels à générer',
                'description' =>
                    'Dossiers validés nécessitant un document officiel.',
                'value' => $documentsToGenerate,
                'level' => $documentsToGenerate > 0
                    ? 'warning'
                    : 'success',
            ],

            [
                'label' => 'Tâches techniques échouées',
                'description' =>
                    'Tâches enregistrées dans la file des échecs.',
                'value' => $failedJobs,
                'level' => $failedJobs > 0
                    ? 'danger'
                    : 'success',
            ],
        ];
    }

    private function buildPerformanceIndex(array $kpis): array
    {
        $applications = max(
            1,
            (int) $kpis['applications']
        );

        $validationRate = round(
            (
                (
                    $kpis['validated']
                    + $kpis['completed']
                )
                / $applications
            ) * 100,
            1
        );

        $rejectionRate = round(
            ($kpis['rejected'] / $applications) * 100,
            1
        );

        $paymentRate = round(
            (
                $kpis['paid_payments']
                / $applications
            ) * 100,
            1
        );

        $eligibleDocuments = max(
            1,
            $kpis['validated'] + $kpis['completed']
        );

        $documentRate = round(
            (
                $kpis['official_documents']
                / $eligibleDocuments
            ) * 100,
            1
        );

        $processingTarget = (float) config(
            'decision_dashboard.monthly_targets.processing_days',
            5
        );

        $processingScore = $kpis['average_processing_days'] > 0
            ? min(
                100,
                round(
                    (
                        $processingTarget
                        / $kpis['average_processing_days']
                    ) * 100,
                    1
                )
            )
            : 100;

        $score = round(
            ($validationRate * 0.35)
            + (min(100, $paymentRate) * 0.20)
            + (min(100, $documentRate) * 0.20)
            + ($processingScore * 0.15)
            + ((100 - min(100, $rejectionRate)) * 0.10),
            1
        );

        $level = match (true) {
            $score >= 85 => 'Excellent',
            $score >= 70 => 'Satisfaisant',
            $score >= 50 => 'À améliorer',
            default => 'Critique',
        };

        return [
            'score' => min(100, $score),
            'level' => $level,
            'validation_rate' => $validationRate,
            'payment_rate' => min(100, $paymentRate),
            'document_rate' => min(100, $documentRate),
            'rejection_rate' => $rejectionRate,
            'processing_score' => $processingScore,
        ];
    }

    private function averageProcessingDays(): float
    {
        $records = DB::table('applications')
            ->whereNotNull('completed_at')
            ->select([
                'submitted_at',
                'created_at',
                'completed_at',
            ])
            ->get();

        if ($records->isEmpty()) {
            return 0.0;
        }

        $total = 0.0;
        $count = 0;

        foreach ($records as $record) {
            $startValue = $record->submitted_at
                ?? $record->created_at;

            if (
                $startValue === null
                || $record->completed_at === null
            ) {
                continue;
            }

            $start = CarbonImmutable::parse($startValue);
            $end = CarbonImmutable::parse($record->completed_at);

            $total += $start->diffInMinutes($end) / 1440;
            $count++;
        }

        return $count > 0
            ? round($total / $count, 1)
            : 0.0;
    }
}