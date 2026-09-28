<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Citizen\StoreApplicationRequest;
use App\Models\Application;
use App\Models\Procedure;
use App\Services\ApplicationService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
{
    $applications = Application::with([
        'procedure.ministry',
        'documents',
        'latestPayment',
        'officialDocument',
    ])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    return view(
        'citizen.applications.index',
        compact('applications')
    );
}

    public function create(Request $request)
    {
        $procedures = Procedure::with('ministry')
            ->where('active', true)
            ->orderBy('title')
            ->get();

        $selectedProcedure = null;

        if ($request->filled('procedure')) {
            $selectedProcedure = $procedures->firstWhere(
                'id',
                (int) $request->input('procedure')
            );
        }

        return view(
            'citizen.applications.create',
            compact(
                'procedures',
                'selectedProcedure'
            )
        );
    }

    public function store(
        StoreApplicationRequest $request,
        ApplicationService $applicationService
    ) {
        $validated = $request->validated();

        $application = $applicationService->createForCitizen(
            $request->user(),
            $validated,
            $request->file('documents', [])
        );

        return redirect()
            ->route('citizen.applications')
            ->with(
                'success',
                sprintf(
                    'Votre demande %s a été déposée avec succès.',
                    $application->reference
                )
            );
    }
}