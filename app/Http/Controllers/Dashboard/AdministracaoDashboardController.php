<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Service;
use App\Models\Visitor;
use Illuminate\View\View;

class AdministracaoDashboardController
{
    public function __invoke(): View
    {
        // --- CULTOS STATS ---
        $totalServices = Service::count();
        $latestService = Service::with(['offerings', 'tithes', 'individualOfferings', 'zoneParticipations'])
            ->orderBy('date', 'desc')
            ->first();

        // Average participation across all services
        $services = Service::with(['offerings', 'tithes', 'individualOfferings', 'zoneParticipations'])
            ->orderBy('date', 'desc')
            ->get();

        $avgMembers = $services->count() > 0 ? round($services->avg('total_members')) : 0;
        $avgVisitors = $services->count() > 0 ? round($services->avg('total_visitors')) : 0;
        $totalSalvations = Service::selectRaw('COALESCE(SUM(adults_salvations), 0) + COALESCE(SUM(children_salvations), 0) as total')
            ->value('total') ?? 0;

        // Last 6 months chart data
        $monthlyStats = Service::selectRaw("
                DATE_FORMAT(date, '%Y-%m') as month,
                SUM(COALESCE(adults_members, 0) + COALESCE(children_members, 0)) as members,
                SUM(COALESCE(adults_visitors, 0) + COALESCE(children_visitors, 0)) as visitors,
                SUM(COALESCE(adults_salvations, 0) + COALESCE(children_salvations, 0)) as salvations,
                COUNT(*) as total_services
            ")
            ->where('date', '>=', now()->subMonths(6))
            ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        $chartLabels = $monthlyStats->pluck('month')->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'))->toArray();
        $chartMembers = $monthlyStats->pluck('members')->toArray();
        $chartVisitors = $monthlyStats->pluck('visitors')->toArray();
        $chartSalvations = $monthlyStats->pluck('salvations')->toArray();

        // Recent services
        $recentServices = Service::with('preacher')
            ->orderBy('date', 'desc')
            ->limit(8)
            ->get();

        // --- VISITAS STATS ---
        $totalVisitors = Visitor::count();
        $pendingVisitors = Visitor::pending()->count();
        $contactedVisitors = Visitor::contacted()->count();
        $integratedVisitors = Visitor::integrated()->count();

        $integrationRate = $totalVisitors > 0 ? round(($integratedVisitors / $totalVisitors) * 100, 1) : 0;

        // Recent pending visitors for quick action
        $pendingVisitorsList = Visitor::with(['zone', 'cell', 'service'])
            ->pending()
            ->orderBy('visit_date', 'desc')
            ->limit(10)
            ->get();

        // Monthly visitor trend (last 6 months)
        $visitorMonthly = Visitor::selectRaw("
                DATE_FORMAT(visit_date, '%Y-%m') as month,
                COUNT(*) as total,
                SUM(CASE WHEN contact_status = 'integrado' THEN 1 ELSE 0 END) as integrated
            ")
            ->where('visit_date', '>=', now()->subMonths(6))
            ->groupByRaw("DATE_FORMAT(visit_date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        $visitorChartLabels = $visitorMonthly->pluck('month')->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'))->toArray();
        $visitorChartTotal = $visitorMonthly->pluck('total')->toArray();
        $visitorChartIntegrated = $visitorMonthly->pluck('integrated')->toArray();

        return view('dashboard.administracao', compact(
            'totalServices',
            'latestService',
            'avgMembers',
            'avgVisitors',
            'totalSalvations',
            'chartLabels',
            'chartMembers',
            'chartVisitors',
            'chartSalvations',
            'recentServices',
            'totalVisitors',
            'pendingVisitors',
            'contactedVisitors',
            'integratedVisitors',
            'integrationRate',
            'pendingVisitorsList',
            'visitorChartLabels',
            'visitorChartTotal',
            'visitorChartIntegrated'
        ));
    }
}
