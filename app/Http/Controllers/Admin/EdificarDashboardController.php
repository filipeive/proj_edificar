<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cell;
use App\Models\CommitmentPackage;
use App\Models\Contribution;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdificarDashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $month = $now->month;
        $year = $now->year;

        // 1. Estatísticas Gerais de Arrecadação
        $contributionsQuery = Contribution::verified()->whereNotNull('package_id');

        $totalArrecadado = (clone $contributionsQuery)->sum('amount');

        $arrecadadoMes = (clone $contributionsQuery)
            ->whereMonth('contribution_date', $month)
            ->whereYear('contribution_date', $year)
            ->sum('amount');

        // 2. Evolução Mensal (Últimos 6 meses)
        $evolucaoMensal = $contributionsQuery
            ->select(
                DB::raw('DATE_FORMAT(contribution_date, "%Y-%m") as mes'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        // 3. Performance por Pacote
        $pacotes = CommitmentPackage::active()
            ->withCount([
                'userCommitments as membros_ativos' => function ($q) use ($now) {
                    $q->whereNull('end_date')->orWhere('end_date', '>', $now);
                }
            ])
            ->get()
            ->map(function ($package) use ($month, $year) {
                $arrecadado = $package->getTotalContributionsThisMonth();
                $esperado = $package->userCommitments()
                    ->where(function ($q) use (&$now) {
                        $q->whereNull('end_date')->orWhere('end_date', '>', now());
                    })
                    ->sum('committed_amount');

                return [
                    'name' => $package->name,
                    'membros' => $package->membros_ativos,
                    'arrecadado' => (float) $arrecadado,
                    'esperado' => (float) $esperado,
                    'percentual' => $esperado > 0 ? round(($arrecadado / $esperado) * 100, 1) : 0
                ];
            });

        // 4. Top Células (EDIFICAR ONLY) - Optimized to single query
        $topCells = Contribution::verified()
            ->whereNotNull('package_id')
            ->whereMonth('contribution_date', $month)
            ->whereYear('contribution_date', $year)
            ->select('cell_id', DB::raw('SUM(amount) as total'))
            ->groupBy('cell_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($contribution) {
                $cell = Cell::find($contribution->cell_id);
                return [
                    'name' => $cell ? $cell->name : 'N/A',
                    'total' => (float) $contribution->total,
                ];
            });

        // 5. Zone Stats (EDIFICAR ONLY) - Optimized to single query
        $zoneStats = Contribution::verified()
            ->whereNotNull('package_id')
            ->whereMonth('contribution_date', $month)
            ->whereYear('contribution_date', $year)
            ->select('zone_id', DB::raw('SUM(amount) as total'))
            ->groupBy('zone_id')
            ->get()
            ->map(function ($contribution) {
                $zone = Zone::find($contribution->zone_id);
                return [
                    'name' => $zone ? $zone->name : 'N/A',
                    'total' => (float) $contribution->total,
                ];
            });

        $pendingContributions = Contribution::where('status', 'pendente')
            ->whereNotNull('package_id')
            ->count();

        return view('admin.edificar.dashboard', compact(
            'totalArrecadado',
            'arrecadadoMes',
            'evolucaoMensal',
            'pacotes',
            'topCells',
            'zoneStats',
            'pendingContributions'
        ));
    }
}
