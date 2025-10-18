<?php
namespace App\Http\Controllers\Dashboard;

use App\Models\Cell;
use App\Models\Contribution;
use App\Models\User;
use App\Models\Zone;
use Illuminate\View\View;

class AdminDashboardController {
    public function __invoke(): View {
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

        return view('dashboard.admin', [
            'totalContributed' => $totalContributed,
            'totalMembers' => $totalMembers,
            'membersContributed' => $membersContributed,
            'percentageContributed' => $percentageContributed,
            'pendingContributions' => $pendingContributions,
            'zoneStats' => $zoneStats,
            'topCells' => $topCells,
        ]);
    }
}
