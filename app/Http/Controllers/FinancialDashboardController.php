<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\OfferingType;
use App\Models\ServiceOffering;
use App\Models\Service;
use App\Models\ServiceIndividualOffering;
use App\Models\ServiceTithe;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class FinancialDashboardController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $month = $filters['month'];
        $year = $filters['year'];
        $scope = $filters['scope'];
        $hasEdificar = $filters['has_edificar'];

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
                ->where(function ($q) {
                    $q->whereHas('offeringType', fn($typeQuery) => $typeQuery->where('scope', 'eclesiastico'))
                        ->orWhereNull('offering_type_id');
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

        $serviceExtraTotals = [];
        if ($scope === 'eclesiastico') {
            $serviceExtraTotals = [
                'Dízimos de cultos' => ServiceTithe::whereHas('service', fn($q) => $q->whereMonth('date', $month)->whereYear('date', $year))->sum('amount'),
                'Ofertas individuais de cultos' => ServiceIndividualOffering::whereHas('service', fn($q) => $q->whereMonth('date', $month)->whereYear('date', $year))->sum('amount'),
                'Ofertas especiais de cultos' => Service::whereMonth('date', $month)->whereYear('date', $year)->sum('special_offerings_total'),
            ];
        }

        // Consolidate Totals by Type
        $totals = [];
        // Filter Offering Types by Scope for the list
        $offeringTypes = OfferingType::where('scope', $scope)
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

        foreach ($serviceExtraTotals as $type => $amount) {
            if ($amount > 0) {
                $totals[] = [
                    'type' => $type,
                    'contributions' => 0,
                    'services' => $amount,
                    'total' => $amount,
                ];
            }
        }

        $untypedContributionTotal = $contributionsByType->firstWhere('offering_type_id', null)->total ?? 0;
        if ($untypedContributionTotal > 0) {
            $totals[] = [
                'type' => 'Contribuições sem tipo',
                'contributions' => $untypedContributionTotal,
                'services' => 0,
                'total' => $untypedContributionTotal,
            ];
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
                    ->where(function ($q) {
                        $q->whereHas('offeringType', fn($typeQuery) => $typeQuery->where('scope', 'eclesiastico'))
                            ->orWhereNull('offering_type_id');
                    });
            }
            $mContrib = $mContribQuery->sum('amount');

            $mService = ServiceOffering::whereHas('service', function ($q) use ($m, $year) {
                $q->whereMonth('date', $m)->whereYear('date', $year);
            })->whereHas('offeringType', function ($q) use ($scope) {
                $q->where('scope', $scope);
            })->sum('amount');

            if ($scope === 'eclesiastico') {
                $mService += ServiceTithe::whereHas('service', fn($q) => $q->whereMonth('date', $m)->whereYear('date', $year))->sum('amount');
                $mService += ServiceIndividualOffering::whereHas('service', fn($q) => $q->whereMonth('date', $m)->whereYear('date', $year))->sum('amount');
                $mService += Service::whereMonth('date', $m)->whereYear('date', $year)->sum('special_offerings_total');
            }

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
            $recentIncomesQuery->whereNull('package_id')->where(function ($q) {
                $q->whereHas('offeringType', fn($typeQuery) => $typeQuery->where('scope', 'eclesiastico'))
                    ->orWhereNull('offering_type_id');
            });
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

        $recentServices = Service::with(['offerings.offeringType', 'tithes', 'individualOfferings'])
            ->latest('date')
            ->take(5)
            ->get()
            ->map(function (Service $service) use ($scope) {
                $amount = $service->offerings
                    ->filter(fn($offering) => $offering->offeringType?->scope === $scope)
                    ->sum('amount');

                if ($scope === 'eclesiastico') {
                    $amount += $service->tithes->sum('amount');
                    $amount += $service->individualOfferings->sum('amount');
                    $amount += $service->special_offerings_total ?? 0;
                }

                return (object) [
                    'date' => $service->date,
                    'description' => 'Culto: ' . $service->service_type,
                    'amount' => $amount,
                    'type' => 'income',
                ];
            })
            ->filter(fn($service) => $service->amount > 0);

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

        $recentTransactions = collect($recentIncomes->all())
            ->merge($recentServices)
            ->merge($recentExpenses)
            ->sortByDesc('date')
            ->take(10);


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
            'recentTransactions',
            'hasEdificar'
        ));
    }

    public function exportExcel(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $rows = $this->buildExportRows($filters['month'], $filters['year'], $filters['scope']);

        return Excel::download(
            new \App\Exports\FinancialDashboardExport($rows, $filters['month'], $filters['year'], $filters['scope']),
            "relatorio_financeiro_{$filters['scope']}_{$filters['year']}-{$filters['month']}.xlsx"
        );
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $rows = collect($this->buildExportRows($filters['month'], $filters['year'], $filters['scope']));
        $income = $rows->sum('income');
        $expenses = $rows->sum('expense');

        return Pdf::loadView('financial_dashboard.report-pdf', [
            'rows' => $rows,
            'month' => $filters['month'],
            'year' => $filters['year'],
            'scope' => $filters['scope'],
            'income' => $income,
            'expenses' => $expenses,
            'balance' => $income - $expenses,
        ])->download("relatorio_financeiro_{$filters['scope']}_{$filters['year']}-{$filters['month']}.pdf");
    }

    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'month' => 'sometimes|integer|between:1,12',
            'year' => 'sometimes|integer|between:2020,2100',
            'scope' => 'sometimes|in:eclesiastico,edificar',
        ]);

        $hasEdificar = app(\App\Services\CongregationContext::class)->hasEdificar();
        $scope = $hasEdificar ? ($validated['scope'] ?? 'eclesiastico') : 'eclesiastico';

        return [
            'month' => (int) ($validated['month'] ?? now()->format('m')),
            'year' => (int) ($validated['year'] ?? now()->format('Y')),
            'scope' => $scope,
            'has_edificar' => $hasEdificar,
        ];
    }

    private function buildExportRows(int $month, int $year, string $scope): array
    {
        $rows = [];
        $contributions = Contribution::with(['user', 'offeringType'])
            ->whereMonth('contribution_date', $month)
            ->whereYear('contribution_date', $year)
            ->where('status', 'verificada');

        if ($scope === 'edificar') {
            $contributions->where(fn($q) => $q->whereNotNull('package_id')->orWhereHas('offeringType', fn($q) => $q->where('scope', 'edificar')));
        } else {
            $contributions->whereNull('package_id')->where(function ($q) {
                $q->whereHas('offeringType', fn($typeQuery) => $typeQuery->where('scope', 'eclesiastico'))
                    ->orWhereNull('offering_type_id');
            });
        }

        foreach ($contributions->get() as $contribution) {
            $rows[] = [
                'date' => $contribution->contribution_date->format('Y-m-d'),
                'source' => 'Contribuição',
                'description' => ($contribution->offeringType->name ?? 'Contribuição sem tipo') . ' - ' . ($contribution->user->name ?? 'Membro'),
                'income' => (float) $contribution->amount,
                'expense' => 0,
                'status' => $contribution->status,
            ];
        }

        $services = Service::with(['offerings.offeringType', 'tithes', 'individualOfferings'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        foreach ($services as $service) {
            foreach ($service->offerings as $offering) {
                if ($offering->offeringType?->scope === $scope) {
                    $rows[] = [
                        'date' => $service->date->format('Y-m-d'),
                        'source' => 'Culto',
                        'description' => $service->service_type . ' - ' . ($offering->offeringType->name ?? 'Oferta'),
                        'income' => (float) $offering->amount,
                        'expense' => 0,
                        'status' => 'Registada',
                    ];
                }
            }

            if ($scope === 'eclesiastico') {
                foreach ($service->tithes as $tithe) {
                    $rows[] = ['date' => $service->date->format('Y-m-d'), 'source' => 'Culto', 'description' => 'Dízimo - ' . ($tithe->member_name ?: $service->service_type), 'income' => (float) $tithe->amount, 'expense' => 0, 'status' => 'Registada'];
                }
                foreach ($service->individualOfferings as $offering) {
                    $rows[] = ['date' => $service->date->format('Y-m-d'), 'source' => 'Culto', 'description' => $offering->description ?: 'Oferta individual - ' . ($offering->member_name ?: $service->service_type), 'income' => (float) $offering->amount, 'expense' => 0, 'status' => 'Registada'];
                }
                if ((float) $service->special_offerings_total > 0) {
                    $rows[] = ['date' => $service->date->format('Y-m-d'), 'source' => 'Culto', 'description' => 'Ofertas especiais - ' . $service->service_type, 'income' => (float) $service->special_offerings_total, 'expense' => 0, 'status' => 'Registada'];
                }
            }
        }

        foreach (Expense::whereMonth('date', $month)->whereYear('date', $year)->where('scope', $scope)->get() as $expense) {
            $rows[] = [
                'date' => $expense->date->format('Y-m-d'),
                'source' => 'Despesa',
                'description' => $expense->description,
                'income' => 0,
                'expense' => (float) $expense->amount,
                'status' => 'Registada',
            ];
        }

        usort($rows, fn($a, $b) => strcmp($b['date'], $a['date']));

        return $rows;
    }
}
