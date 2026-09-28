<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\UpdateApplicationStatusRequest;
use App\Models\Application;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with([
            'user',
            'procedure.ministry',
            'documents',
            'assignedAgent',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where(
                        'reference',
                        'ilike',
                        '%' . $search . '%'
                    )
                    ->orWhereHas(
                        'user',
                        function ($userQuery) use ($search) {
                            $userQuery
                                ->where(
                                    'name',
                                    'ilike',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'email',
                                    'ilike',
                                    '%' . $search . '%'
                                );
                        }
                    )
                    ->orWhereHas(
                        'procedure',
                        function ($procedureQuery) use ($search) {
                            $procedureQuery->where(
                                'title',
                                'ilike',
                                '%' . $search . '%'
                            );
                        }
                    );
            });
        }

        $applications = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'agent.applications.index',
            compact('applications')
        );
    }

    public function show(
        Application $application,
        WorkflowService $workflowService
    ) {
        $application->load([
                   'user',
                   'procedure.ministry',
                   'documents',
                   'workflowLogs.user',
                   'assignedAgent',
                   'latestPayment',
                   'officialDocument',
             ]);

        $availableTransitions =
            $workflowService->availableTransitions($application);

        return view(
            'agent.applications.show',
            compact('application', 'availableTransitions')
        );
    }

    public function updateStatus(
        UpdateApplicationStatusRequest $request,
        Application $application,
        WorkflowService $workflowService
    ) {
        $validated = $request->validated();

        $workflowService->changeStatus(
            $application,
            $validated['status'],
            $validated['comment'] ?? null
        );

        return redirect()
            ->route('agent.applications.show', $application)
            ->with(
                'success',
                'Le statut du dossier a été mis à jour avec succès.'
            );
    }
}