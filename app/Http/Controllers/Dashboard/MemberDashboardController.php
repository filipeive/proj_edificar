<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\View\View;

class MemberDashboardController
{
    public function __invoke(): View
    {
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

        $upcomingEvents = \App\Models\Event::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        $recentServices = \App\Models\Service::orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // 1. Minha Célula
        $myCell = $member->cell()->with('leader')->first();

        // 2. Estatísticas de Participação (últimos 3 meses e total)
        $attendanceStats = [
            'total_present' => 0,
            'last_3_months_present' => 0,
            'last_attendance' => null
        ];

        if ($myCell) {
            $attendanceStats['total_present'] = \App\Models\Attendance::where('user_id', $member->id)
                ->where('cell_id', $myCell->id)
                ->where('status', true)
                ->count();

            $attendanceStats['last_3_months_present'] = \App\Models\Attendance::where('user_id', $member->id)
                ->where('cell_id', $myCell->id)
                ->where('status', true)
                ->where('date', '>=', now()->subMonths(3))
                ->count();

            $attendanceStats['last_attendance'] = \App\Models\Attendance::where('user_id', $member->id)
                ->where('cell_id', $myCell->id)
                ->where('status', true)
                ->max('date');
        }

        return view('dashboard.membro', [
            'commitment' => $commitment,
            'contributions' => $contributions,
            'totalThisMonth' => $totalThisMonth,
            'upcomingEvents' => $upcomingEvents,
            'recentServices' => $recentServices,
            'myCell' => $myCell,
            'attendanceStats' => $attendanceStats,
        ]);
    }
}
