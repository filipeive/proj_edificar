<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\OfferingType;
use App\Models\ServiceOffering;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialDashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $scope = $request->get('scope', 'eclesiastico'); // 'eclesiastico' or 'edificar'

        // --- 1. OVERVIEW STATS (CURRENT MONTH) ---

        // Contributions (Dízimos/Ofertas de Membros)
        $contributionsQuery = Contribution::whereMonth('contribution_date', $month)
            ->whereYear('contribution_date', $year)
            ->where('status', 'verificada');

        if ($scope === 'edificar') {
            // Edificar: Packages OR OfferingTypes with scope 'edificar'
            $contributionsQuery->where(function ($q) {
                $q->whereNotNull('package_id')
                    ->orWhereHas('offeringType', function ($q2) {
                        $q2->where('scope', 'edificar');
                    });
            });
        } else {
            // Eclesiastico: No Package AND OfferingType scope 'eclesiastico'
            $contributionsQuery->whereNull('package_id')
                ->whereHas('offeringType', function ($q) {
                    $q->where('scope', 'eclesiastico');
                });
        }

        $contributionsByType = $contributionsQuery
            ->select('offering_type_id', DB::raw('SUM(amount) as total'))
            ->groupBy('offering_type_id')
            ->with('offeringType')
            ->get();

        // Service Offerings (Ofertas de Cultos) - Usually mostly Ecclesiastical, but filter by scope
        $serviceOfferingsByType = ServiceOffering::whereHas('service', function ($q) use ($month, $year) {
            $q->whereMonth('date', $month)->whereYear('date', $year);
        })
            ->whereHas('offeringType', function ($q) use ($scope) {
                $q->where('scope', $scope);
            })
            ->select('offering_type_id', DB::raw('SUM(amount) as total'))
            ->groupBy('offering_type_id')
            ->with('offeringType')
            ->get();

        // Consolidate Totals by Type
        $totals = [];
        // Filter Offering Types by Scope for the list
        $offeringTypes = OfferingType::where('is_active', true)
            ->where('scope', $scope)
            ->orderBy('order')
            ->get();

        // Special handling: If Edificar, we might have contributions without offering_type (orphan package payments?)
        // Usually, package payments have an offering_type (e.g. 'Pacote'). 

        foreach ($offeringTypes as $type) {
            $contributionTotal = $contributionsByType->where('offering_type_id', $type->id)->first()->total ?? 0;
            $serviceTotal = $serviceOfferingsByType->where('offering_type_id', $type->id)->first()->total ?? 0;

            if ($contributionTotal > 0 || $serviceTotal > 0) {
                $totals[] = [
                    'type' => $type->name,
                    'contributions' => $contributionTotal,
                    'services' => $serviceTotal,
                    'total' => $contributionTotal + $serviceTotal
                ];
            }
        }

        // Also capture contributions with Packages that might map to 'Pacote' types
        // The above query grouped by 'offering_type_id'. 

        $grandTotal = collect($totals)->sum('total');

        // Expenses (Saídas)
        $totalExpenses = Expense::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('scope', $scope)
            ->sum('amount');

        $balance = $grandTotal - $totalExpenses;

        // --- 2. CHARTS DATA ---

        // A) Yearly Trend (Income vs Expenses)
        $monthlyLabels = [];
        $monthlyIncome = [];
        $monthlyExpenses = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyLabels[] = date('M', mktime(0, 0, 0, $m, 1));

            // Income
            $mContribQuery = Contribution::whereMonth('contribution_date', $m)->whereYear('contribution_date', $year)->where('status', 'verificada');

            if ($scope === 'edificar') {
                $mContribQuery->where(function ($q) {
                    $q->whereNotNull('package_id')
                        ->orWhereHas('offeringType', function ($q2) {
                            $q2->where('scope', 'edificar'); });
                });
            } else {
                $mContribQuery->whereNull('package_id')
                    ->whereHas('offeringType', function ($q) {
                        $q->where('scope', 'eclesiastico'); });
            }
            $mContrib = $mContribQuery->sum('amount');

            $mService = ServiceOffering::whereHas('service', function ($q) use ($m, $year) {
                $q->whereMonth('date', $m)->whereYear('date', $year);
            })->whereHas('offeringType', function ($q) use ($scope) {
                $q->where('scope', $scope);
            })->sum('amount');

            $monthlyIncome[] = $mContrib + $mService;

            // Expense
            $monthlyExpenses[] = Expense::whereMonth('date', $m)
                ->whereYear('date', $year)
                ->where('scope', $scope)
                ->sum('amount');
        }

        // B) Expense Categories (Current Month)
        $expensesByCategory = Expense::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('scope', $scope)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $expenseLabels = $expensesByCategory->pluck('category');
        $expenseValues = $expensesByCategory->pluck('total');


        // --- 3. RECENT TRANSACTIONS ---
        $recentIncomesQuery = Contribution::where('status', 'verificada')
            ->with('user');

        if ($scope === 'edificar') {
            $recentIncomesQuery->where(function ($q) {
                $q->whereNotNull('package_id')->orWhereHas('offeringType', function ($q2) {
                    $q2->where('scope', 'edificar'); });
            });
        } else {
            $recentIncomesQuery->whereNull('package_id')->whereHas('offeringType', function ($q) {
                $q->where('scope', 'eclesiastico'); });
        }

        $recentIncomes = $recentIncomesQuery
            ->latest('contribution_date')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'date' => $item->contribution_date,
                    'description' => 'Entrada: ' . ($item->user->name ?? 'Membro'),
                    'amount' => $item->amount,
                    'type' => 'income'
                ];
            });

        $recentExpenses = Expense::where('scope', $scope)
            ->latest('date')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'date' => $item->date,
                    'description' => $item->description,
                    'amount' => $item->amount,
                    'type' => 'expense'
                ];
            });

        $recentTransactions = $recentIncomes->merge($recentExpenses)->sortByDesc('date')->take(10);


        return view('financial_dashboard.index', compact(
            'totals',
            'grandTotal',
            'totalExpenses',
            'balance',
            'month',
            'year',
            'scope',
            'monthlyLabels',
            'monthlyIncome',
            'monthlyExpenses',
            'expenseLabels',
            'expenseValues',
            'recentTransactions'
        ));
    }
}
