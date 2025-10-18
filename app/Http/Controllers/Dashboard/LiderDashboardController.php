<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\View\View;

class LiderDashboardController {
    public function __invoke(): View {
        $lider = auth()->user();
        $cell = $lider->cell;

        if (!$cell || $cell->leader_id !== $lider->id) {
            return view('dashboard.lider', [
                'members' => collect(),
                'total' => 0,
                'cellName' => 'Nenhuma Célula Atribuída',
            ]);
        }

        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        $members = $cell->members()
            ->where('is_active', true)
            ->get()
            ->map(function ($member) use ($monthStart, $monthEnd) {
                $contributions = $member->contributions()
                    ->whereBetween('contribution_date', [$monthStart, $monthEnd])
                    ->where('status', 'verificada')
                    ->sum('amount');

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'total' => $contributions,
                    'status' => $contributions > 0 ? 'Contribuiu' : 'Faltoso',
                ];
            });

        $total = $cell->getTotalContributedThisMonth();

        return view('dashboard.lider', [
            'members' => $members,
            'total' => $total,
            'cellName' => $cell->name,
        ]);
    }
}