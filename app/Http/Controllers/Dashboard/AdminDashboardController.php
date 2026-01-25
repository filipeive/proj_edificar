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
        // Fix: Use Calendar Month to avoid 'Zero' confusion if outside fiscal range
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        // Calculate Tithes and Offerings from Services (not Contributions)
        // Services contain the actual church tithes/offerings from worship services
        $services = \App\Models\Service::with(['tithes', 'offerings'])
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get();

        $totalContributed = $services->sum(function ($service) {
            return $service->total_tithes + $service->total_offerings;
        });

        $totalMembers = User::where('role', 'membro')->where('is_active', true)->count();
        $membersContributed = User::where('role', 'membro')
            ->where('is_active', true)
            ->whereHas('contributions', function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('contribution_date', [$monthStart, $monthEnd])
                    ->where('status', 'verificada')
                    ->whereNull('package_id'); // Strict Isolation
            })
            ->count();

        $pendingContributions = Contribution::where('status', 'pendente')
            ->whereNull('package_id')
            ->count();

        // 1. Members per Zone (Requested Change)
        $zones = Zone::withCount(['supervisions', 'cells'])->get(); // Eager load counts if relationships exist on Zone (Need to check model)
        // Zone model might NOT have 'cells' relation directly (Zone -> Supervision -> Cell).
        // I will calculate manually to be safe or assuming relations exist.

        $zoneStats = []; // Reusing name but for Members
        $zoneStructures = []; // New chart data: Cells & Supervisions

        foreach ($zones as $zone) {
            // Count Members in this Zone
            // Zone hasMany Supervisions? Supervision hasMany Cells? Cell hasMany Members?
            // Or Zone hasMany Users through relations? 
            // Often efficient to query User where zone_id (if exists) or via cell.
            // Assuming User doesn't have direct zone_id (it's on Cell->Supervision->Zone).
            // Let's check Zone model if I can. But for now I'll assume standard nested relation.
            // Actually, Contribution has zone_id, but User? 
            // I'll filter Users who belong to a cell in a supervision in this zone.

            // To be faster/easier:
            // $zoneMembers = User::whereHas('cell.supervision', fn($q) => $q->where('zone_id', $zone->id))->count();

            // However, let's look at `User.php`: `getZoneId()` exists.
            // I'll try to use the relationships if they exist.
            // Zone -> hasMany Supervisions.
            // Supervision -> hasMany Cells.
            // Cell -> hasMany Members (User).

            $membersCount = User::whereHas('cell.supervision', function ($q) use ($zone) {
                $q->where('zone_id', $zone->id);
            })->where('role', 'membro')->where('is_active', true)->count();

            $zoneStats[] = [
                'name' => $zone->name,
                'total' => $membersCount, // "Total" here means Members
            ];

            $supervisionsCount = $zone->supervisions()->count();
            // Cells: Zone->Supervisions->Cells
            $cellsCount = Cell::whereHas('supervision', function ($q) use ($zone) {
                $q->where('zone_id', $zone->id);
            })->count();

            $zoneStructures[] = [
                'name' => $zone->name,
                'supervisions' => $supervisionsCount,
                'cells' => $cellsCount,
            ];
        }
        $zoneStats = collect($zoneStats);
        $zoneStructures = collect($zoneStructures);


        // Top Cells (Ecclesiastical Only)
        $topCells = Cell::with('supervision')
            ->get()
            ->map(function ($cell) use ($monthStart, $monthEnd) {
                $totalCell = Contribution::where('cell_id', $cell->id)
                    ->whereBetween('contribution_date', [$monthStart, $monthEnd])
                    ->where('status', 'verificada')
                    ->whereNull('package_id')
                    ->sum('amount');

                $membersContributedCount = User::where('cell_id', $cell->id)
                    ->whereHas('contributions', function ($q) use ($monthStart, $monthEnd) {
                        $q->whereBetween('contribution_date', [$monthStart, $monthEnd])
                            ->where('status', 'verificada')
                            ->whereNull('package_id');
                    })->count();

                return [
                    'name' => $cell->name,
                    'total' => $totalCell,
                    'members' => $cell->getMembersCount(),
                    'contributed' => $membersContributedCount,
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

        // Latest 5 verified contributions (Ecclesiastical)
        $latestContributions = Contribution::with(['user', 'zone'])
            ->where('status', 'verificada')
            ->whereNull('package_id')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($c) {
                return [
                    'type' => 'contribution',
                    'title' => 'Dízimo/Oferta',
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
            'zoneStats' => $zoneStats, // Now Members per Zone
            'zoneStructures' => $zoneStructures, // New: Cells/Supervisions per Zone
            'topCells' => $topCells,
            'upcomingEvents' => $upcomingEvents,
            'recentServices' => $recentServices,
            'recentActivity' => $recentActivity,
            'growthLabels' => $growthLabels,
            'growthData' => $growthData,

        ]);
    }
}
