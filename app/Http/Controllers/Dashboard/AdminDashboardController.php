<?php
namespace App\Http\Controllers\Dashboard;

use App\Models\Cell;
use App\Models\Contribution;
use App\Models\User;
use App\Models\Zone;
use Illuminate\View\View;

class AdminDashboardController
{
    public function __invoke(): View
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        $totalContributed = Contribution::whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->sum('amount');

        $totalMembers = User::where('role', 'membro')->where('is_active', true)->count();
        $membersContributed = User::where('role', 'membro')
            ->where('is_active', true)
            ->whereHas('contributions', function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('contribution_date', [$monthStart, $monthEnd])
                    ->where('status', 'verificada');
            })
            ->count();

        $pendingContributions = Contribution::where('status', 'pendente')->count();

        $zones = Zone::with('supervisions')->get();
        $zoneStats = [];
        foreach ($zones as $zone) {
            $zoneStats[] = [
                'name' => $zone->name,
                'total' => $zone->getTotalContributedThisMonth(),
            ];
        }
        $zoneStats = collect($zoneStats);


        $topCells = Cell::with('supervision')
            ->get()
            ->map(function ($cell) {
                return [
                    'name' => $cell->name,
                    'total' => $cell->getTotalContributedThisMonth(),
                    'members' => $cell->getMembersCount(),
                    'contributed' => $cell->getMembersContributedThisMonth(),
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values();

        $percentageContributed = $totalMembers > 0
            ? round(($membersContributed / $totalMembers) * 100, 2)
            : 0;

        $upcomingEvents = \App\Models\Event::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        $recentServices = \App\Models\Service::orderBy('date', 'desc')
            ->limit(5)
            ->get();

        $recentActivity = collect();

        // Latest 5 verified contributions
        $latestContributions = Contribution::with(['user', 'zone'])
            ->where('status', 'verificada')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($c) {
                return [
                    'type' => 'contribution',
                    'title' => 'Nova Contribuição',
                    'description' => $c->user->name . ' contribuiu com ' . number_format((float) $c->amount, 0) . ' MT',
                    'time' => $c->created_at,
                    'icon' => 'bi-cash-coin',
                    'color' => 'text-green-600',
                    'bg' => 'bg-green-50'
                ];
            });

        // Latest 5 new members
        $latestMembers = User::where('role', 'membro')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($m) {
                return [
                    'type' => 'member',
                    'title' => 'Novo Membro',
                    'description' => $m->name . ' juntou-se à igreja',
                    'time' => $m->created_at,
                    'icon' => 'bi-person-plus',
                    'color' => 'text-blue-600',
                    'bg' => 'bg-blue-50'
                ];
            });

        $recentActivity = $latestContributions->concat($latestMembers)->sortByDesc('time')->take(6);

        // Growth data (last 6 months)
        $growthLabels = [];
        $growthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $growthLabels[] = $date->translatedFormat('M');
            $growthData[] = User::where('role', 'membro')
                ->where('created_at', '<=', $date->endOfMonth())
                ->count();
        }

        return view('dashboard.admin', [
            'totalContributed' => $totalContributed,
            'totalMembers' => $totalMembers,
            'membersContributed' => $membersContributed,
            'percentageContributed' => $percentageContributed,
            'pendingContributions' => $pendingContributions,
            'zoneStats' => $zoneStats,
            'topCells' => $topCells,
            'upcomingEvents' => $upcomingEvents,
            'recentServices' => $recentServices,
            'recentActivity' => $recentActivity,
            'growthLabels' => $growthLabels,
            'growthData' => $growthData,
        ]);
    }
}
