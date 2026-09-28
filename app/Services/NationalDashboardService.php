<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Ministry;
use App\Models\OfficialDocument;
use App\Models\Payment;
use App\Models\Procedure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;

class NationalDashboardService
{
    /**
     * Ensemble des données nécessaires au tableau de bord national.
     */
    public function getDashboardData(): array
{
    return Cache::remember(
        'pnae:national-dashboard',
        now()->addSeconds(30),
        function (): array {
            return [
                'summary' => $this->getSummary(),
                'applicationStatuses' => $this->getApplicationStatuses(),
                'paymentSummary' => $this->getPaymentSummary(),
                'monthlyApplications' => $this->getMonthlyApplications(),
                'monthlyPayments' => $this->getMonthlyPayments(),
                'ministryPerformance' => $this->getMinistryPerformance(),
                'alerts' => $this->getOperationalAlerts(),
                'recentPayments' => $this->getRecentPayments(),
                'recentDocuments' => $this->getRecentOfficialDocuments(),
                'systemHealth' => $this->getSystemHealth(),

                // Nouvelles données Phase 10.1
                'nationalScore' => $this->getNationalScore(),
                'averageProcessing' => $this->getAverageProcessing(),
                'topProcedures' => $this->getTopProcedures(),
                'monthlyObjective' => $this->getMonthlyObjective(),
                'activityHeatmap' => $this->getActivityHeatmap(),
                'recentActivity' => $this->getRecentActivity(),
                'securityAudit' => $this->getSecurityAudit(),
            ];
        }
    );
}

    /**
     * Indicateurs principaux.
     */
    private function getSummary(): array
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        return [
            'citizens' => User::where('role', 'citoyen')->count(),

            'agents' => User::whereIn(
                'role',
                ['agent', 'responsable']
            )->count(),

            'administrators' => User::where(
                'role',
                'admin'
            )->count(),

            'ministries' => Ministry::count(),

            'procedures' => Procedure::count(),

            'applications' => Application::count(),

            'applications_today' => Application::where(
                'created_at',
                '>=',
                $today
            )->count(),

            'applications_week' => Application::where(
                'created_at',
                '>=',
                $weekStart
            )->count(),

            'applications_month' => Application::where(
                'created_at',
                '>=',
                $monthStart
            )->count(),

            'official_documents' => Schema::hasTable(
                'official_documents'
            )
                ? OfficialDocument::count()
                : 0,

            'payments' => Schema::hasTable('payments')
                ? Payment::count()
                : 0,

            'revenue_total' => Schema::hasTable('payments')
                ? (float) Payment::where('status', 'paye')
                    ->sum('amount')
                : 0,

            'revenue_today' => Schema::hasTable('payments')
                ? (float) Payment::where('status', 'paye')
                    ->where('paid_at', '>=', $today)
                    ->sum('amount')
                : 0,

            'revenue_month' => Schema::hasTable('payments')
                ? (float) Payment::where('status', 'paye')
                    ->where('paid_at', '>=', $monthStart)
                    ->sum('amount')
                : 0,
        ];
    }

    /**
     * Nombre de demandes pour chaque statut.
     */
    private function getApplicationStatuses(): array
    {
        $statuses = [
            'soumise',
            'en_traitement',
            'validee',
            'rejetee',
            'terminee',
        ];

        $counts = Application::query()
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect($statuses)
            ->mapWithKeys(function (string $status) use ($counts) {
                return [
                    $status => (int) ($counts[$status] ?? 0),
                ];
            })
            ->all();
    }

    /**
     * Statistiques financières.
     */
    private function getPaymentSummary(): array
    {
        if (! Schema::hasTable('payments')) {
            return [
                'en_attente' => 0,
                'paye' => 0,
                'echoue' => 0,
                'rembourse' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
            ];
        }

        $counts = Payment::query()
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'en_attente' => (int) ($counts['en_attente'] ?? 0),
            'paye' => (int) ($counts['paye'] ?? 0),
            'echoue' => (int) ($counts['echoue'] ?? 0),
            'rembourse' => (int) ($counts['rembourse'] ?? 0),

            'total_amount' => (float) Payment::sum('amount'),

            'paid_amount' => (float) Payment::where(
                'status',
                'paye'
            )->sum('amount'),
        ];
    }

    /**
     * Évolution des demandes sur les douze derniers mois.
     */
    private function getMonthlyApplications(): array
    {
        $start = now()
            ->subMonths(11)
            ->startOfMonth();

        $rows = Application::query()
            ->selectRaw(
                "TO_CHAR(created_at, 'YYYY-MM') AS period"
            )
            ->selectRaw('COUNT(*) AS total')
            ->where('created_at', '>=', $start)
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $values = [];

        for ($index = 0; $index < 12; $index++) {
            $date = $start->copy()->addMonths($index);
            $period = $date->format('Y-m');

            $labels[] = ucfirst(
                $date->locale('fr')->translatedFormat('M Y')
            );

            $values[] = (int) ($rows[$period] ?? 0);
        }

        return compact('labels', 'values');
    }

    /**
     * Recettes confirmées sur les douze derniers mois.
     */
    private function getMonthlyPayments(): array
    {
        $start = now()
            ->subMonths(11)
            ->startOfMonth();

        if (! Schema::hasTable('payments')) {
            return [
                'labels' => [],
                'values' => [],
            ];
        }

        $rows = Payment::query()
            ->selectRaw(
                "TO_CHAR(paid_at, 'YYYY-MM') AS period"
            )
            ->selectRaw('COALESCE(SUM(amount), 0) AS total')
            ->where('status', 'paye')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $start)
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $labels = [];
        $values = [];

        for ($index = 0; $index < 12; $index++) {
            $date = $start->copy()->addMonths($index);
            $period = $date->format('Y-m');

            $labels[] = ucfirst(
                $date->locale('fr')->translatedFormat('M Y')
            );

            $values[] = (float) ($rows[$period] ?? 0);
        }

        return compact('labels', 'values');
    }

    /**
     * Performance de chaque ministère.
     */
    private function getMinistryPerformance(): Collection
    {
        return Ministry::query()
            ->withCount('procedures')
            ->withCount([
                'procedures as total_applications' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    );
                },

                'procedures as validated_applications' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    )->whereIn(
                        'applications.status',
                        ['validee', 'terminee']
                    );
                },

                'procedures as processing_applications' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    )->where(
                        'applications.status',
                        'en_traitement'
                    );
                },

                'procedures as rejected_applications' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    )->where(
                        'applications.status',
                        'rejetee'
                    );
                },
            ])
            ->orderByDesc('total_applications')
            ->get()
            ->map(function (Ministry $ministry) {
                $total = (int) $ministry->total_applications;
                $validated = (int) $ministry->validated_applications;

                $ministry->validation_rate = $total > 0
                    ? round(($validated / $total) * 100, 1)
                    : 0;

                return $ministry;
            });
    }

    /**
     * Alertes nécessitant une intervention.
     */
    private function getOperationalAlerts(): array
    {
        $threeDaysAgo = now()->subDays(3);
        $sevenDaysAgo = now()->subDays(7);

        $unpaid = Application::query()
            ->where('payment_status', '!=', 'paye')
            ->whereHas('procedure', function ($query) {
                $query->where('fee', '>', 0);
            })
            ->count();

        $longProcessing = Application::query()
            ->where('status', 'en_traitement')
            ->where('updated_at', '<=', $sevenDaysAgo)
            ->count();

        $submittedTooLong = Application::query()
            ->where('status', 'soumise')
            ->where('created_at', '<=', $threeDaysAgo)
            ->count();

        $rejectedDocuments = Schema::hasTable(
            'application_documents'
        )
            ? DB::table('application_documents')
                ->where('status', 'rejete')
                ->count()
            : 0;

        $missingOfficialDocuments = Schema::hasTable(
            'official_documents'
        )
            ? Application::query()
                ->whereIn(
                    'status',
                    ['validee', 'terminee']
                )
                ->whereDoesntHave('officialDocument')
                ->count()
            : 0;

        return [
            [
                'level' => $submittedTooLong > 0
                    ? 'warning'
                    : 'success',

                'title' => 'Demandes soumises en attente',
                'count' => $submittedTooLong,
                'description' =>
                    'Demandes déposées depuis plus de trois jours.',
            ],

            [
                'level' => $longProcessing > 0
                    ? 'danger'
                    : 'success',

                'title' => 'Dossiers en retard',
                'count' => $longProcessing,
                'description' =>
                    'Dossiers en traitement depuis au moins sept jours.',
            ],

            [
                'level' => $unpaid > 0
                    ? 'warning'
                    : 'success',

                'title' => 'Paiements en attente',
                'count' => $unpaid,
                'description' =>
                    'Démarches payantes non encore réglées.',
            ],

            [
                'level' => $rejectedDocuments > 0
                    ? 'danger'
                    : 'success',

                'title' => 'Pièces rejetées',
                'count' => $rejectedDocuments,
                'description' =>
                    'Documents nécessitant une correction citoyenne.',
            ],

            [
                'level' => $missingOfficialDocuments > 0
                    ? 'warning'
                    : 'success',

                'title' => 'Documents officiels à générer',
                'count' => $missingOfficialDocuments,
                'description' =>
                    'Dossiers validés sans document officiel.',
            ],
        ];
    }

    /**
     * Derniers paiements.
     */
    private function getRecentPayments(): Collection
    {
        if (! Schema::hasTable('payments')) {
            return collect();
        }

        return Payment::with([
            'user',
            'application.procedure',
        ])
            ->latest()
            ->limit(8)
            ->get();
    }

    /**
     * Derniers documents officiels.
     */
    private function getRecentOfficialDocuments(): Collection
    {
        if (! Schema::hasTable('official_documents')) {
            return collect();
        }

        return OfficialDocument::with([
            'application.user',
            'application.procedure',
            'generator',
        ])
            ->latest('issued_at')
            ->limit(8)
            ->get();
    }

    /**
     * Santé technique simple de la plateforme.
     */
    private function getSystemHealth(): array
    {
        $databaseConnected = true;

        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $databaseConnected = false;
        }

        $storagePath = storage_path();
        $freeSpace = @disk_free_space($storagePath);
        $totalSpace = @disk_total_space($storagePath);

        $storageUsage = (
            is_numeric($freeSpace)
            && is_numeric($totalSpace)
            && $totalSpace > 0
        )
            ? round(
                (($totalSpace - $freeSpace) / $totalSpace) * 100,
                1
            )
            : null;

        return [
            'database' => [
                'status' => $databaseConnected,
                'label' => $databaseConnected
                    ? 'Opérationnelle'
                    : 'Indisponible',
            ],

            'storage' => [
                'status' => $storageUsage === null
                    || $storageUsage < 90,

                'label' => $storageUsage === null
                    ? 'Non mesuré'
                    : $storageUsage . ' % utilisé',

                'usage' => $storageUsage,
            ],

            'php' => [
                'status' => true,
                'label' => PHP_VERSION,
            ],

            'laravel' => [
                'status' => true,
                'label' => app()->version(),
            ],

            'environment' => [
                'status' => app()->environment('production'),
                'label' => app()->environment(),
            ],

            'debug' => [
                'status' => ! config('app.debug'),
                'label' => config('app.debug')
                    ? 'Activé'
                    : 'Désactivé',
            ],
        ];
    }
	private function getNationalScore(): array
{
    $total = Application::count();

    $validated = Application::whereIn(
        'status',
        ['validee', 'terminee']
    )->count();

    $rejected = Application::where(
        'status',
        'rejetee'
    )->count();

    $paidRequired = Application::query()
        ->whereHas('procedure', function ($query) {
            $query->where('fee', '>', 0);
        })
        ->count();

    $paid = Application::query()
        ->whereHas('procedure', function ($query) {
            $query->where('fee', '>', 0);
        })
        ->where('payment_status', 'paye')
        ->count();

    $documentsExpected = Application::whereIn(
        'status',
        ['validee', 'terminee']
    )->count();

    $documentsGenerated = Schema::hasTable('official_documents')
        ? OfficialDocument::count()
        : 0;

    $validationRate = $total > 0
        ? ($validated / $total) * 100
        : 100;

    $rejectionRate = $total > 0
        ? ($rejected / $total) * 100
        : 0;

    $paymentRate = $paidRequired > 0
        ? ($paid / $paidRequired) * 100
        : 100;

    $documentRate = $documentsExpected > 0
        ? min(
            100,
            ($documentsGenerated / $documentsExpected) * 100
        )
        : 100;

    $score = round(
        ($validationRate * 0.40)
        + ($paymentRate * 0.25)
        + ($documentRate * 0.25)
        + ((100 - $rejectionRate) * 0.10),
        1
    );

    return [
        'score' => min(100, max(0, $score)),
        'validation_rate' => round($validationRate, 1),
        'payment_rate' => round($paymentRate, 1),
        'document_rate' => round($documentRate, 1),
        'rejection_rate' => round($rejectionRate, 1),
        'label' => match (true) {
            $score >= 90 => 'Excellente',
            $score >= 75 => 'Bonne',
            $score >= 60 => 'À renforcer',
            default => 'Critique',
        },
    ];
}
private function getAverageProcessing(): array
{
    $globalHours = Application::query()
        ->whereIn('status', ['validee', 'terminee'])
        ->selectRaw(
            'AVG(EXTRACT(EPOCH FROM (updated_at - created_at)) / 3600) AS average_hours'
        )
        ->value('average_hours');

    $byMinistry = DB::table('applications')
        ->join(
            'procedures',
            'procedures.id',
            '=',
            'applications.procedure_id'
        )
        ->join(
            'ministries',
            'ministries.id',
            '=',
            'procedures.ministry_id'
        )
        ->whereIn(
            'applications.status',
            ['validee', 'terminee']
        )
        ->select(
            'ministries.id',
            'ministries.name'
        )
        ->selectRaw(
            'AVG(EXTRACT(EPOCH FROM (applications.updated_at - applications.created_at)) / 3600) AS average_hours'
        )
        ->groupBy(
            'ministries.id',
            'ministries.name'
        )
        ->orderBy('average_hours')
        ->get()
        ->map(function ($row) {
            $hours = round((float) $row->average_hours, 1);

            return [
                'id' => $row->id,
                'name' => $row->name,
                'hours' => $hours,
                'days' => round($hours / 24, 1),
            ];
        });

    $hours = round((float) ($globalHours ?? 0), 1);

    return [
        'global_hours' => $hours,
        'global_days' => round($hours / 24, 1),
        'by_ministry' => $byMinistry,
    ];
}

private function getTopProcedures(): Collection
{
    return Procedure::query()
        ->with('ministry')
        ->withCount('applications')
        ->orderByDesc('applications_count')
        ->limit(10)
        ->get();
}

private function getMonthlyObjective(): array
{
    $applicationTarget = max(
        1,
        (int) env('PNAE_MONTHLY_APPLICATION_TARGET', 100)
    );

    $revenueTarget = max(
        1,
        (int) env('PNAE_MONTHLY_REVENUE_TARGET', 500000)
    );

    $applications = Application::where(
        'created_at',
        '>=',
        now()->startOfMonth()
    )->count();

    $revenue = Schema::hasTable('payments')
        ? (float) Payment::where('status', 'paye')
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('amount')
        : 0;

    return [
        'applications' => [
            'target' => $applicationTarget,
            'actual' => $applications,
            'percentage' => min(
                100,
                round(($applications / $applicationTarget) * 100, 1)
            ),
        ],

        'revenue' => [
            'target' => $revenueTarget,
            'actual' => $revenue,
            'percentage' => min(
                100,
                round(($revenue / $revenueTarget) * 100, 1)
            ),
        ],
    ];
}

private function getActivityHeatmap(): array
{
    $start = now()
        ->subDays(34)
        ->startOfDay();

    $rows = Application::query()
        ->selectRaw("TO_CHAR(created_at, 'YYYY-MM-DD') AS activity_date")
        ->selectRaw('COUNT(*) AS total')
        ->where('created_at', '>=', $start)
        ->groupBy('activity_date')
        ->pluck('total', 'activity_date');

    $days = [];

    for ($index = 0; $index < 35; $index++) {
        $date = $start->copy()->addDays($index);
        $key = $date->format('Y-m-d');
        $total = (int) ($rows[$key] ?? 0);

        $days[] = [
            'date' => $key,
            'label' => $date
                ->locale('fr')
                ->translatedFormat('D d M'),
            'total' => $total,
            'level' => match (true) {
                $total === 0 => 0,
                $total <= 2 => 1,
                $total <= 5 => 2,
                $total <= 10 => 3,
                default => 4,
            },
        ];
    }

    return $days;
}

private function getRecentActivity(): Collection
{
    if (! Schema::hasTable('audit_logs')) {
        return collect();
    }

    return DB::table('audit_logs')
        ->leftJoin(
            'users',
            'users.id',
            '=',
            'audit_logs.user_id'
        )
        ->select([
            'audit_logs.id',
            'audit_logs.action',
            'audit_logs.entity',
            'audit_logs.entity_id',
            'audit_logs.ip_address',
            'audit_logs.created_at',
            'users.name as user_name',
        ])
        ->latest('audit_logs.created_at')
        ->limit(12)
        ->get();
}

private function getSecurityAudit(): array
{
    if (! Schema::hasTable('audit_logs')) {
        return [
            'failed_logins' => 0,
            'cancelled_payments' => 0,
            'revoked_documents' => 0,
            'critical_errors' => 0,
        ];
    }

    $since = now()->subDays(30);

    return [
        'failed_logins' => DB::table('audit_logs')
            ->where('created_at', '>=', $since)
            ->where(function ($query) {
                $query
                    ->whereRaw('LOWER(action) LIKE ?', ['%connexion%échou%'])
                    ->orWhereRaw('LOWER(action) LIKE ?', ['%login%fail%']);
            })
            ->count(),

        'cancelled_payments' => Schema::hasTable('payments')
            ? Payment::where('status', 'echoue')
                ->whereNotNull('cancelled_at')
                ->where('cancelled_at', '>=', $since)
                ->count()
            : 0,

        'revoked_documents' => Schema::hasTable('official_documents')
            ? OfficialDocument::where('status', 'revoque')
                ->where('updated_at', '>=', $since)
                ->count()
            : 0,

        'critical_errors' => DB::table('audit_logs')
            ->where('created_at', '>=', $since)
            ->where(function ($query) {
                $query
                    ->whereRaw('LOWER(action) LIKE ?', ['%erreur critique%'])
                    ->orWhereRaw('LOWER(action) LIKE ?', ['%exception%']);
            })
            ->count(),
    ];
}


}