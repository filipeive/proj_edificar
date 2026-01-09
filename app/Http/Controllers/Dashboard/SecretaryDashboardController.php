<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Cell;
use App\Models\Event;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class SecretaryDashboardController
{
    public function __invoke(): View
    {
        // Estatísticas Gerais
        $totalMembers = User::where('role', 'membro')->where('is_active', true)->count();
        $totalCells = Cell::count();

        // Eventos e Cultos
        $upcomingEvents = Event::with('eventType')
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        $recentServices = Service::orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Atividade Recente (Novos membros)
        $recentMembers = User::where('role', 'membro')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.secretaria', compact(
            'totalMembers',
            'totalCells',
            'upcomingEvents',
            'recentServices',
            'recentMembers'
        ));
    }
}
