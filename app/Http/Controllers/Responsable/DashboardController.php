<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const SUBMITTED_STATUSES = [
        'soumise',
        'soumis',
        'en_attente',
        'submitted',
        'pending',
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

    private const PAID_STATUSES = [
        'paye',
        'payee',
        'paid',
        'confirme',
        'confirmed',
        'success',
        'succeeded',
    ];

    public function index(): View
    {
        $responsable = auth()->user();

        abort_if(
            $responsable->ministry_id === null,
            403,
            'Aucun ministère n’est affecté à ce responsable.'
        );

        $ministry = DB::table('ministries')
            ->where('id', $responsable->ministry_id)
            ->first();

        abort_if(
            $ministry === null,
            404,
            'Le ministère associé à ce responsable est introuvable.'
        );

        $procedureIds = DB::table('procedures')
            ->where('ministry_id', $responsable->ministry_id)
            ->pluck('id');

        $applications = DB::table('applications')
            ->whereIn('procedure_id', $procedureIds);

        $applicationIds = (clone $applications)->pluck('id');

        $totalApplications = (clone $applications)->count();

        $statistics = [
            'total' => $totalApplications,

            'submitted' => (clone $applications)
                ->whereIn('status', self::SUBMITTED_STATUSES)
                ->count(),

            'processing' => (clone $applications)
                ->whereIn('status', self::PROCESSING_STATUSES)
                ->count(),

            'validated' => (clone $applications)
                ->whereIn('status', self::VALIDATED_STATUSES)
                ->count(),

            'rejected' => (clone $applications)
                ->whereIn('status', self::REJECTED_STATUSES)
                ->count(),

            'completed' => (clone $applications)
                ->whereIn('status', self::COMPLETED_STATUSES)
                ->count(),

            'agents' => DB::table('users')
                ->where('ministry_id', $responsable->ministry_id)
                ->where('role', 'agent')
                ->where('active', true)
                ->count(),

            'procedures' => $procedureIds->count(),

            'revenue' => (float) DB::table('payments')
                ->whereIn('application_id', $applicationIds)
                ->whereIn('status', self::PAID_STATUSES)
                ->sum('amount'),

            'official_documents' => DB::table('official_documents')
                ->whereIn('application_id', $applicationIds)
                ->count(),
        ];

        $statistics['validation_rate'] = $totalApplications > 0
            ? round(
                (
                    (
                        $statistics['validated']
                        + $statistics['completed']
                    ) / $totalApplications
                ) * 100,
                1
            )
            : 0.0;

        $lateApplications = DB::table('applications')
            ->whereIn('procedure_id', $procedureIds)
            ->whereIn('status', self::PROCESSING_STATUSES)
            ->where(
                DB::raw('COALESCE(submitted_at, created_at)'),
                '<',
                CarbonImmutable::now()->subDays(7)
            )
            ->count();

        $recentApplications = DB::table('applications')
            ->join(
                'users',
                'users.id',
                '=',
                'applications.user_id'
            )
            ->join(
                'procedures',
                'procedures.id',
                '=',
                'applications.procedure_id'
            )
            ->whereIn('applications.procedure_id', $procedureIds)
            ->select([
                'applications.id',
                'applications.reference',
                'applications.status',
                'applications.priority',
                'applications.created_at',
                'users.name as citizen_name',
                'procedures.title as procedure_title',
            ])
            ->orderByDesc('applications.created_at')
            ->limit(10)
            ->get();

        $agents = DB::table('users')
            ->where('ministry_id', $responsable->ministry_id)
            ->where('role', 'agent')
            ->select([
                'id',
                'name',
                'email',
                'active',
                'created_at',
            ])
            ->orderBy('name')
            ->get()
            ->map(function (object $agent): object {
                $agent->assigned_count = DB::table('applications')
                    ->where('assigned_to', $agent->id)
                    ->count();

                $agent->processing_count = DB::table('applications')
                    ->where('assigned_to', $agent->id)
                    ->whereIn(
                        'status',
                        self::PROCESSING_STATUSES
                    )
                    ->count();

                $agent->completed_count = DB::table('applications')
                    ->where('assigned_to', $agent->id)
                    ->whereIn(
                        'status',
                        array_merge(
                            self::VALIDATED_STATUSES,
                            self::COMPLETED_STATUSES
                        )
                    )
                    ->count();

                return $agent;
            });

        return view(
            'responsable.dashboard',
            compact(
                'ministry',
                'statistics',
                'lateApplications',
                'recentApplications',
                'agents'
            )
        );
    }
}