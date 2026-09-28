<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DecisionDashboardService;
use Illuminate\View\View;

class DecisionDashboardController extends Controller
{
    public function __construct(
        private readonly DecisionDashboardService $dashboardService
    ) {
    }

    public function index(): View
    {
        return view(
            'admin.decision-dashboard.index',
            $this->dashboardService->build()
        );
    }
}