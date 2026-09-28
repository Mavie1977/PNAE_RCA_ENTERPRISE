<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $baseQuery = Application::query();

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

        $applications = Application::with([
            'user',
            'procedure.ministry',
            'documents',
        ])
            ->latest()
            ->take(10)
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