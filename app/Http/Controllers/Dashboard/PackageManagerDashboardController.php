<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CommitmentPackage;
use App\Models\Contribution;
use App\Models\UserCommitment;
use Illuminate\Http\Request;

class PackageManagerDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        // Get packages assigned to this manager
        $packages = CommitmentPackage::where('responsible_id', $user->id)
            ->where('is_active', true)
            ->get();

        if ($packages->isEmpty()) {
            return redirect()->route('dashboard.membro')->with('info', 'Você não possui pacotes atribuídos para gestão.');
        }

        $packageIds = $packages->pluck('id');

        // Statistics
        $stats = [
            'total_members' => UserCommitment::whereIn('package_id', $packageIds)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>', now());
                })->count(),

            'total_contributions' => Contribution::whereIn('package_id', $packageIds)
                ->where('status', 'verificada')
                ->sum('amount'),

            'monthly_contributions' => Contribution::whereIn('package_id', $packageIds)
                ->whereYear('contribution_date', now()->year)
                ->whereMonth('contribution_date', now()->month)
                ->where('status', 'verificada')
                ->sum('amount'),

            'pending_contributions' => Contribution::whereIn('package_id', $packageIds)
                ->where('status', 'pendente')
                ->count(),
        ];

        // Revenue Trend (Last 6 months) - DB agnostic aggregation
        $revenueTrendRows = Contribution::whereIn('package_id', $packageIds)
            ->where('status', 'verificada')
            ->where('contribution_date', '>=', now()->subMonths(6))
            ->orderBy('contribution_date', 'asc')
            ->get(['contribution_date', 'amount']);

        $revenueTrend = $revenueTrendRows
            ->groupBy(function ($item) {
                return $item->contribution_date->format('m/Y');
            })
            ->map(function ($items, $month) {
                return [
                    'month' => $month,
                    'total' => $items->sum('amount'),
                ];
            })
            ->values();

        // Recent Activity
        $recentContributions = Contribution::with(['user', 'package'])
            ->whereIn('package_id', $packageIds)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.packages.dashboard', compact('packages', 'stats', 'revenueTrend', 'recentContributions'));
    }
}
