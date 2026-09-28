<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;

class DashboardController extends Controller
{
    public function index(StatisticsService $statisticsService)
    {
        $dashboard = $statisticsService->adminDashboard();

        return view('admin.dashboard', $dashboard);
    }
}