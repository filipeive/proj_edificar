<?php
namespace App\Http\Controllers\Dashboard;

use App\Models\Supervision;
use App\Models\Zone;
use App\Models\CellMeeting;
use App\Models\Contribution;
use App\Models\Event;
use App\Models\Service;
use Illuminate\View\View;
use Carbon\Carbon;

class PastorDashboardController
{
    public function __invoke(): View
    {
        $pastor = auth()->user();

        // Find zone directly managed by this pastor
        $zone = Zone::where('pastor_id', $pastor->id)->first();

        // Fallback to cell association if direct management not found (legacy or backup)
        if (!$zone) {
            $zone = $pastor->cell ? $pastor->cell->supervision->zone : null;
        }

        if (!$zone) {
            return view('dashboard.pastor', [
                'supervisions' => collect(),
                'total' => 0,
                'totalMembers' => 0,
                'zoneName' => 'Sem Zona Atribuída',
                'upcomingEvents' => collect(),
                'recentServices' => collect(),
                'recentCellMeetings' => collect(),
                'chartData' => ['labels' => [], 'data' => []],
            ]);
        }

        $supervisions = Supervision::where('zone_id', $zone->id)
            ->withCount('cells')
            ->get()
            ->map(function ($supervision) {
                return [
                    'name' => $supervision->name,
                    'total' => $supervision->getTotalContributedThisMonth(),
                    'cells' => $supervision->cells_count,
                ];
            });

        $total = $zone->getTotalContributedThisMonth();
        $totalMembers = $zone->getTotalMembers();

        // Historical Data (Last 6 Months)
        $chartData = [
            'labels' => [],
            'data' => []
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth()->addDays(19);
            $monthEnd = $date->copy()->addMonth()->startOfMonth()->addDays(4);

            $amount = $zone->contributions()
                ->whereBetween('contribution_date', [$monthStart, $monthEnd])
                ->where('status', 'verificada')
                ->sum('amount');

            $chartData['labels'][] = $date->translatedFormat('M/Y');
            $chartData['data'][] = (float) $amount;
        }

        $upcomingEvents = Event::where('zone_id', $zone->id)
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        $recentServices = Service::orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Recent Cell Meetings in this zone
        $recentCellMeetings = CellMeeting::whereHas('cell.supervision', function ($q) use ($zone) {
            $q->where('zone_id', $zone->id);
        })
            ->with(['cell', 'leader'])
            ->orderBy('meeting_date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.pastor', [
            'zone' => $zone,
            'supervisions' => $supervisions,
            'total' => $total,
            'totalMembers' => $totalMembers,
            'zoneName' => $zone->name,
            'upcomingEvents' => $upcomingEvents,
            'recentServices' => $recentServices,
            'recentCellMeetings' => $recentCellMeetings,
            'chartData' => $chartData,
        ]);
    }
}