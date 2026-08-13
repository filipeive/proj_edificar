<?php
namespace App\Http\Controllers\Dashboard;

use App\Models\Cell;
use App\Models\CellMeeting;
use App\Models\Contribution;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LiderDashboardController
{
    public function __invoke(): View
    {
        $lider = auth()->user();
        $cellIds = $lider->getManagedCellIds();

        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        $cells = Cell::query()
            ->with(['supervision.zone'])
            ->withCount(['members as active_members_count' => function ($query) {
                $query->where('is_active', true);
            }])
            ->whereIn('id', $cellIds)
            ->orderBy('name')
            ->get();

        $cellIds = $cells->pluck('id');

        if ($cells->isEmpty()) {
            return view('dashboard.lider', [
                'cells' => $cells,
                'cellCards' => collect(),
                'members' => collect(),
                'recentMeetings' => collect(),
                'upcomingEvents' => collect(),
                'total' => 0,
                'totalMembers' => 0,
                'contributedCount' => 0,
                'percentage' => 0,
                'cellName' => 'Nenhuma Célula Atribuída',
                'primaryCell' => null,
                'monthStart' => $monthStart,
                'monthEnd' => $monthEnd,
            ]);
        }

        $cellTotals = Contribution::query()
            ->select('cell_id', DB::raw('SUM(amount) as total'))
            ->whereIn('cell_id', $cellIds)
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->groupBy('cell_id')
            ->pluck('total', 'cell_id');

        $members = User::query()
            ->with('cell')
            ->whereIn('cell_id', $cellIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $memberTotals = Contribution::query()
            ->select('user_id', DB::raw('SUM(amount) as total'))
            ->whereIn('cell_id', $cellIds)
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $members = $members->map(function ($member) use ($memberTotals) {
            $total = (float) ($memberTotals[$member->id] ?? 0);

            return [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'cell_id' => $member->cell_id,
                'cell_name' => $member->cell?->name,
                'total' => $total,
                'status' => $total > 0 ? 'Contribuiu' : 'Faltoso',
            ];
        });

        $cellCards = $cells->map(function ($cell) use ($cellTotals) {
            return [
                'id' => $cell->id,
                'name' => $cell->name,
                'type' => $cell->type_label,
                'zone' => $cell->supervision?->zone?->name,
                'supervision' => $cell->supervision?->name,
                'active_members_count' => $cell->active_members_count,
                'total' => (float) ($cellTotals[$cell->id] ?? 0),
            ];
        });

        $zoneIds = $cells->pluck('supervision.zone_id')->filter()->unique()->values();

        $upcomingEvents = Event::query()
            ->where('date', '>=', now())
            ->where(function ($query) use ($zoneIds, $cellIds) {
                $query->whereIn('zone_id', $zoneIds)
                    ->orWhereIn('cell_id', $cellIds);
            })
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        $recentMeetings = CellMeeting::query()
            ->with('cell')
            ->whereIn('cell_id', $cellIds)
            ->orderBy('meeting_date', 'desc')
            ->limit(6)
            ->get();

        $totalMembers = $members->count();
        $contributedCount = $members->where('status', 'Contribuiu')->count();

        return view('dashboard.lider', [
            'cells' => $cells,
            'cellCards' => $cellCards,
            'members' => $members,
            'recentMeetings' => $recentMeetings,
            'upcomingEvents' => $upcomingEvents,
            'total' => (float) $cellTotals->sum(),
            'totalMembers' => $totalMembers,
            'contributedCount' => $contributedCount,
            'percentage' => $totalMembers > 0 ? round(($contributedCount / $totalMembers) * 100, 1) : 0,
            'cellName' => $cells->count() === 1 ? $cells->first()->name : $cells->count() . ' células geridas',
            'primaryCell' => $cells->first(),
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
        ]);
    }
}
