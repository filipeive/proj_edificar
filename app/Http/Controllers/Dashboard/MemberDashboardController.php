<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\View\View;

class MemberDashboardController {
    public function __invoke(): View {
        $member = auth()->user();
        $commitment = $member->getActiveCommitment();

        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        $contributions = $member->contributions()
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->orderBy('contribution_date', 'desc')
            ->take(5)
            ->get();

        $totalThisMonth = $member->getTotalContributedThisMonth();

        return view('dashboard.membro', [
            'commitment' => $commitment,
            'contributions' => $contributions,
            'totalThisMonth' => $totalThisMonth,
        ]);
    }
}
