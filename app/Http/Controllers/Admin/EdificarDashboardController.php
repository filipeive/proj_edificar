<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitmentPackage;
use App\Models\Contribution;
use App\Models\UserCommitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdificarDashboardController extends Controller
{
    public function index()
    {
        // 1. Estatísticas Gerais de Arrecadação
        $totalArrecadado = Contribution::verified()
            ->whereNotNull('package_id')
            ->sum('amount');

        $arrecadadoMes = Contribution::verified()
            ->whereNotNull('package_id')
            ->whereMonth('contribution_date', now()->month)
            ->whereYear('contribution_date', now()->year)
            ->sum('amount');

        // 2. Evolução Mensal (Últimos 6 meses)
        $evolucaoMensal = Contribution::verified()
            ->whereNotNull('package_id')
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
                'userCommitments as membros_ativos' => function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>', now());
                }
            ])
            ->get()
            ->map(function ($package) {
                $arrecadado = $package->getTotalContributionsThisMonth();
                $esperado = $package->userCommitments()
                    ->where(function ($q) {
                        $q->whereNull('end_date')->orWhere('end_date', '>', now());
                    })
                    ->sum('committed_amount');

                return [
                    'name' => $package->name,
                    'membros' => $package->membros_ativos,
                    'arrecadado' => $arrecadado,
                    'esperado' => $esperado,
                    'percentual' => $esperado > 0 ? round(($arrecadado / $esperado) * 100, 1) : 0
                ];
            });

        return view('admin.edificar.dashboard', compact(
            'totalArrecadado',
            'arrecadadoMes',
            'evolucaoMensal',
            'pacotes'
        ));
    }
}
