<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user();

        abort_unless(
            $agent
            && $agent->role === 'agent'
            && $agent->active
            && $agent->ministry_id !== null,
            403,
            'Accès non autorisé.'
        );

        // Un agent ne voit que ses propres dossiers,
        // appartenant à son ministère.
        $baseQuery = Application::query()
            ->where('assigned_to', $agent->id)
            ->whereHas('procedure', function ($query) use ($agent) {
                $query->where('ministry_id', $agent->ministry_id);
            });

        $total = (clone $baseQuery)->count();

        $soumise = (clone $baseQuery)
            ->where('status', 'soumise')
            ->count();

        $traitement = (clone $baseQuery)
           ->where('status', 'en_traitement')
           ->count();

        $validee = (clone $baseQuery)
            ->where('status', 'validee')
            ->count();

        $rejetee = (clone $baseQuery)
            ->where('status', 'rejetee')
            ->count();

        $terminee = (clone $baseQuery)
            ->where('status', 'terminee')
            ->count();

        $applications = (clone $baseQuery)
            ->with([
                'user',
                'procedure.ministry',
                'documents',
            ])
            ->latest()
            ->limit(10)
            ->get();

        return view('agent.dashboard', compact(
            'total',
            'soumise',
            'traitement',
            'validee',
            'rejetee',
            'terminee',
            'applications'
        ));
    }
}