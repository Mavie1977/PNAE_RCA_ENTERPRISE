<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Ministry;
use App\Models\Procedure;
use App\Models\User;
use Illuminate\Support\Collection;

class StatisticsService
{
    public function adminDashboard(): array
    {
        $totalApplications = Application::count();

        $statusCounts = [
            'soumise' => Application::where('status', 'soumise')->count(),
            'en_traitement' => Application::where('status', 'en_traitement')->count(),
            'validee' => Application::where('status', 'validee')->count(),
            'rejetee' => Application::where('status', 'rejetee')->count(),
            'terminee' => Application::where('status', 'terminee')->count(),
        ];

        $completedApplications =
            $statusCounts['validee'] + $statusCounts['terminee'];

        $validationRate = $totalApplications > 0
            ? round(($completedApplications / $totalApplications) * 100, 1)
            : 0;

        $processingRate = $totalApplications > 0
            ? round(($statusCounts['en_traitement'] / $totalApplications) * 100, 1)
            : 0;

        $rejectionRate = $totalApplications > 0
            ? round(($statusCounts['rejetee'] / $totalApplications) * 100, 1)
            : 0;

        return [
            'stats' => [
                'citizens' => User::where('role', 'citoyen')->count(),
                'agents' => User::whereIn('role', ['agent', 'responsable'])->count(),
                'ministries' => Ministry::count(),
                'procedures' => Procedure::count(),
                'applications' => $totalApplications,
                'submitted' => $statusCounts['soumise'],
                'processing' => $statusCounts['en_traitement'],
                'validated' => $statusCounts['validee'],
                'rejected' => $statusCounts['rejetee'],
                'completed' => $statusCounts['terminee'],
            ],

            'rates' => [
                'validation' => $validationRate,
                'processing' => $processingRate,
                'rejection' => $rejectionRate,
            ],

            'statusCounts' => $statusCounts,

            'applicationsByMinistry' => $this->applicationsByMinistry(),

            'applicationsByProcedure' => $this->applicationsByProcedure(),

            'recentApplications' => Application::with([
                'user',
                'procedure.ministry',
                'documents',
            ])
                ->latest()
                ->take(8)
                ->get(),

            'recentUsers' => User::latest()
                ->take(6)
                ->get(),
        ];
    }

    private function applicationsByMinistry(): Collection
    {
        return Ministry::query()
            ->withCount([
                'procedures as applications_count' => function ($query) {
                    $query->join(
                        'applications',
                        'applications.procedure_id',
                        '=',
                        'procedures.id'
                    );
                },
            ])
            ->orderByDesc('applications_count')
            ->take(6)
            ->get();
    }

    private function applicationsByProcedure(): Collection
    {
        return Procedure::query()
            ->with('ministry')
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->take(6)
            ->get();
    }
}