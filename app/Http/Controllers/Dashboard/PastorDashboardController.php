<?php
namespace App\Http\Controllers\Dashboard;

use App\Models\Supervision;
use Illuminate\View\View;

class PastorDashboardController {
    public function __invoke(): View {
        $pastor = auth()->user();
        $zone = $pastor->cell ? $pastor->cell->supervision->zone : null;

        if (!$zone) {
            return view('dashboard.pastor', [
                'supervisions' => collect(),
                'total' => 0,
                'zoneName' => 'Sem Zona Atribuída',
            ]);
        }

        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        $supervisions = Supervision::where('zone_id', $zone->id)
            ->with('cells')
            ->get()
            ->map(function ($supervision) {
                return [
                    'name' => $supervision->name,
                    'total' => $supervision->getTotalContributedThisMonth(),
                    'cells' => $supervision->cells->count(),
                ];
            });

        $total = $zone->getTotalContributedThisMonth();

        return view('dashboard.pastor', [
            'zone' => $zone,
            'supervisions' => $supervisions,
            'total' => $total,
            'zoneName' => $zone->name,
        ]);
    }
}