<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\GitHubService;
use App\Services\QualityControlService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $dashboard = app(DashboardService::class);

        $stats = $dashboard->getStats($userId);
        $githubStats = app(GitHubService::class)->getStats();
        $qualityChecklists = collect(app(QualityControlService::class)->getAllProgress($userId));

        $recentActivities = $dashboard->getRecentActivities($userId);
        $overdueInvoices = $dashboard->getOverdueInvoices($userId);

        return view('dashboard.index', compact(
            'stats',
            'githubStats',
            'qualityChecklists',
            'recentActivities',
            'overdueInvoices',
        ));
    }
}
