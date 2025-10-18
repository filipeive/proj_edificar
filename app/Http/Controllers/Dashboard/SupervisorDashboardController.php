<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\View\View;

class SupervisorDashboardController {
    public function __invoke(): View {
        $supervisor = auth()->user();

        if ($supervisor->role === 'pastor_zona') {
            return redirect()->route('dashboard.pastor');
        }

        $supervision = $supervisor->cell ? $supervisor->cell->supervision : null;

        if (!$supervision) {
            return view('dashboard.supervisor', [
                'cells' => collect(),
                'total' => 0,
                'supervisionName' => 'Sem Supervisão Atribuída',
            ]);
        }

        $cells = $supervision->cells
            ->map(function ($cell) {
                return [
                    'name' => $cell->name,
                    'leader' => $cell->leader ? $cell->leader->name : 'N/A',
                    'members' => $cell->getMembersCount(),
                    'contributed' => $cell->getMembersContributedThisMonth(),
                    'total' => $cell->getTotalContributedThisMonth(),
                ];
            });

        $total = $supervision->getTotalContributedThisMonth();

        return view('dashboard.supervisor', [
            'cells' => $cells,
            'total' => $total,
            'supervisionName' => $supervision->name,
        ]);
    }
}