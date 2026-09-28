<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NationalDashboardService;


class NationalDashboardController extends Controller
{
    public function index(
        NationalDashboardService $dashboardService
    ) {
        $dashboard = $dashboardService->getDashboardData();

        return view(
            'admin.supervision.index',
            compact('dashboard')
        );
    }
	
	public function data(
    NationalDashboardService $dashboardService
) {
    return response()->json(
        $dashboardService->getDashboardData()
    );
}
}