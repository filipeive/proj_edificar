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
        $zones = Zone::withCount(['supervisions', 'cells'])->get();

        $membersPerZone = User::where('users.role', 'membro')
            ->where('users.is_active', true)
            ->whereNotNull('users.cell_id')
            ->join('cells', 'users.cell_id', '=', 'cells.id')
            ->join('supervisions', 'cells.supervision_id', '=', 'supervisions.id')
            ->selectRaw('supervisions.zone_id, count(users.id) as total')
            ->groupBy('supervisions.zone_id')
            ->pluck('total', 'supervisions.zone_id');

        $zoneStats = $zones->map(function ($zone) use ($membersPerZone) {
            return [
                'name' => $zone->name,
                'total' => (int) ($membersPerZone->get($zone->id) ?? 0),
            ];
        });

        $zoneStructures = $zones->map(function ($zone) {
            return [
                'name' => $zone->name,
                'supervisions' => (int) $zone->supervisions_count,
                'cells' => (int) $zone->cells_count,
            ];
        });

        $mapZones = Zone::with('supervisions.cells')->get()->map(function (Zone $zone) {
            return [
                'name' => $zone->name,
                'latitude' => $zone->latitude !== null ? (float) $zone->latitude : null,
                'longitude' => $zone->longitude !== null ? (float) $zone->longitude : null,
                'boundary' => $zone->boundary,
                'cells' => $zone->supervisions->flatMap->cells
                    ->filter(fn(Cell $cell) => $cell->latitude !== null && $cell->longitude !== null)
                    ->map(fn(Cell $cell) => [
                        'name' => $cell->name,
                        'latitude' => (float) $cell->latitude,
                        'longitude' => (float) $cell->longitude,
                    ])->values(),
            ];
        })->values();


        // Top Cells (Ecclesiastical Only) - Optimized with eager relationships and aggregations
        $cellContributions = Contribution::whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->whereNull('package_id')
            ->whereNotNull('cell_id')
            ->selectRaw('cell_id, sum(amount) as total')
            ->groupBy('cell_id')
            ->pluck('total', 'cell_id');

        $cellContributedMembers = User::whereNotNull('cell_id')
            ->whereHas('contributions', function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('contribution_date', [$monthStart, $monthEnd])
                    ->where('status', 'verificada')
                    ->whereNull('package_id');
            })
            ->selectRaw('cell_id, count(id) as total')
            ->groupBy('cell_id')
            ->pluck('total', 'cell_id');

        $topCells = Cell::with('supervision')
            ->withCount(['members' => fn($q) => $q->where('is_active', true)])
            ->get()
            ->map(function ($cell) use ($cellContributions, $cellContributedMembers) {
                $totalCell = (float) ($cellContributions->get($cell->id) ?? 0);
                $membersContributedCount = (int) ($cellContributedMembers->get($cell->id) ?? 0);

                return [
                    'name' => $cell->name,
                    'total' => $totalCell,
                    'members' => (int) $cell->members_count,
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
            'mapZones' => $mapZones,
            'topCells' => $topCells,
            'upcomingEvents' => $upcomingEvents,
            'recentServices' => $recentServices,
            'recentActivity' => $recentActivity,
            'growthLabels' => $growthLabels,
            'growthData' => $growthData,

        ]);
    }
}
