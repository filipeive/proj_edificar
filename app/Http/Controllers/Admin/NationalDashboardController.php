<?php

namespace App\Http\Controllers\Admin;

use App\Models\Congregation;
use App\Models\Contribution;
use App\Models\Expense;
use App\Models\Service;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Zone;
use App\Services\CongregationContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NationalDashboardController
{
    public function index(Request $request): View
    {
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();

        $congregations = Congregation::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($filter) use ($search) {
                    $filter->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('domain', 'like', "%{$search}%")
                        ->orWhere('subdomain', 'like', "%{$search}%");
                });
            })
            ->when($request->input('status') === 'active', fn($query) => $query->where('is_active', true))
            ->when($request->input('status') === 'inactive', fn($query) => $query->where('is_active', false))
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $this->attachCongregationCounts($congregations->getCollection());

        $mapCongregations = Congregation::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get();
        $this->attachCongregationCounts($mapCongregations);
        $mapCongregations = $mapCongregations->map(fn(Congregation $congregation) => [
            'id' => $congregation->id,
            'name' => $congregation->name,
            'slug' => $congregation->slug,
            'latitude' => (float) $congregation->latitude,
            'longitude' => (float) $congregation->longitude,
            'is_active' => $congregation->is_active,
            'is_primary' => $congregation->is_primary,
            'has_edificar' => (bool) $congregation->has_edificar_project,
            'users_count' => $congregation->users_count,
            'zones_count' => $congregation->zones_count,
            'cells_count' => $congregation->cells_count,
            'show_url' => route('national.congregations.show', $congregation),
            'edit_url' => route('national.congregations.edit', $congregation),
        ])->values();

        $activeIds = Congregation::where('is_active', true)->pluck('id');
        $statsRows = collect($this->congregationReportRows($periodStart, $periodEnd))
            ->whereIn('congregation_id', $activeIds->all());

        $stats = [
            'congregations' => Congregation::count(),
            'active_congregations' => Congregation::where('is_active', true)->count(),
            'inactive_congregations' => Congregation::where('is_active', false)->count(),
            'members' => $statsRows->sum('active_members'),
            'zones' => $statsRows->sum('zones'),
            'cells' => $statsRows->sum('cells'),
            'services_this_month' => $statsRows->sum('services'),
            'total_income_this_month' => $statsRows->sum('total_income'),
            'verified_contributions_this_month' => $statsRows->sum('verified_contributions'),
            'service_income_this_month' => $statsRows->sum('service_income'),
            'expenses_this_month' => $statsRows->sum('expenses'),
            'net_balance_this_month' => $statsRows->sum('net_balance'),
            'pending_contributions' => $statsRows->sum('pending_contributions'),
        ];

        $nationalMonthly = $this->monthlyContributionTrend();

        return view('admin.national-dashboard.index', compact('congregations', 'stats', 'nationalMonthly', 'mapCongregations'));
    }

    public function congregations(Request $request): View
    {
        $congregations = Congregation::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(fn($filter) => $filter->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%")
                    ->orWhere('subdomain', 'like', "%{$search}%"));
            })
            ->when($request->input('status') === 'active', fn($query) => $query->where('is_active', true))
            ->when($request->input('status') === 'inactive', fn($query) => $query->where('is_active', false))
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $this->attachCongregationCounts($congregations->getCollection());

        return view('admin.national-dashboard.congregations', compact('congregations'));
    }

    public function create(): View
    {
        return view('admin.national-dashboard.congregation-form', ['congregation' => null, 'admins' => collect()]);
    }

    public function show(Congregation $congregation, Request $request): View
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->date('start_date')?->startOfDay() ?? now()->startOfMonth();
        $endDate = $request->date('end_date')?->endOfDay() ?? now()->endOfMonth();

        return app(CongregationContext::class)->run($congregation, function () use ($congregation, $startDate, $endDate) {
            // Estrutura
            $zones = Zone::withCount('cells')->orderBy('name')->get();
            $activeMembersCount = User::where('role', 'membro')->where('is_active', true)->count();
            $totalUsersCount = User::count();
            $totalCellsCount = $zones->sum('cells_count');

            // Cultos e finanças de cultos no período
            $services = Service::whereBetween('date', [$startDate, $endDate])
                ->with(['preacher', 'offerings.offeringType', 'tithes', 'individualOfferings'])
                ->orderByDesc('date')
                ->get();

            $cultosMetrics = [
                'total_services' => $services->count(),
                'total_members_attendance' => $services->sum(fn($s) => $s->total_members),
                'total_visitors_attendance' => $services->sum(fn($s) => $s->total_visitors),
                'total_salvations' => $services->sum(fn($s) => ($s->adults_salvations ?? 0) + ($s->children_salvations ?? 0)),
                'service_tithes' => (float) $services->sum(fn($s) => $s->total_tithes),
                'service_offerings' => (float) $services->sum(fn($s) => $s->total_offerings),
                'service_total_financial' => (float) $services->sum(fn($s) => $s->total_financial),
            ];

            // Contribuições gerais da congregação no período (sem pacotes de obra)
            $contributionsQuery = Contribution::whereNull('package_id')
                ->whereBetween('contribution_date', [$startDate, $endDate]);

            $verifiedContributionsTotal = (float) (clone $contributionsQuery)
                ->where('status', 'verificada')
                ->sum('amount');

            $pendingContributionsCount = (int) (clone $contributionsQuery)
                ->where('status', 'pendente')
                ->count();

            $recentContributions = (clone $contributionsQuery)
                ->with(['user', 'offeringType'])
                ->orderByDesc('contribution_date')
                ->take(20)
                ->get();

            // Despesas eclesiásticas locais no período
            $expensesQuery = Expense::where('scope', 'eclesiastico')
                ->whereBetween('date', [$startDate, $endDate]);

            $totalExpenses = (float) (clone $expensesQuery)->sum('amount');
            $recentExpenses = (clone $expensesQuery)
                ->orderByDesc('date')
                ->take(20)
                ->get();

            // Totais e Saldo Líquido Eclesiástico
            $totalIncome = $verifiedContributionsTotal + $cultosMetrics['service_total_financial'];
            $netBalance = $totalIncome - $totalExpenses;

            // Visitantes registados no período
            $visitors = Visitor::whereBetween('visit_date', [$startDate, $endDate])
                ->orderByDesc('visit_date')
                ->take(25)
                ->get();

            // Administradores e pastores locais
            $admins = User::whereIn('role', ['admin', 'pastor_senior'])
                ->orderBy('name')
                ->get();

            return view('admin.national-dashboard.congregation-detail', compact(
                'congregation',
                'startDate',
                'endDate',
                'zones',
                'activeMembersCount',
                'totalUsersCount',
                'totalCellsCount',
                'services',
                'cultosMetrics',
                'verifiedContributionsTotal',
                'pendingContributionsCount',
                'recentContributions',
                'totalExpenses',
                'recentExpenses',
                'totalIncome',
                'netBalance',
                'visitors',
                'admins'
            ));
        });
    }

    public function edit(Congregation $congregation): View
    {
        $admins = User::withoutGlobalScope('congregation')
            ->where('congregation_id', $congregation->id)
            ->whereIn('role', ['admin', 'pastor_senior'])
            ->orderBy('name')
            ->get();

        return view('admin.national-dashboard.congregation-form', compact('congregation', 'admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'required|string|max:100|alpha_dash|unique:congregations,slug',
            'domain' => 'nullable|string|max:190|unique:congregations,domain',
            'subdomain' => 'nullable|string|max:100|alpha_dash|unique:congregations,subdomain',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'has_edificar_project' => 'nullable|boolean',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:10|confirmed',
        ]);

        DB::transaction(function () use ($validated) {
            $congregation = Congregation::create([
                'name' => $validated['name'],
                'slug' => strtolower($validated['slug']),
                'domain' => filled($validated['domain'] ?? null) ? strtolower($validated['domain']) : null,
                'subdomain' => filled($validated['subdomain'] ?? null) ? strtolower($validated['subdomain']) : null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'has_edificar_project' => (bool) ($validated['has_edificar_project'] ?? false),
                'is_active' => true,
                'is_primary' => false,
            ]);

            app(CongregationContext::class)->run($congregation, function () use ($validated) {
                User::create([
                    'name' => $validated['admin_name'],
                    'email' => strtolower($validated['admin_email']),
                    'password' => $validated['admin_password'],
                    'role' => 'admin',
                    'is_active' => true,
                ]);
            });
        });

        return redirect()->route('national.congregations.index')
            ->with('success', 'Congregação e administrador local registados com sucesso.');
    }

    public function update(Request $request, Congregation $congregation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'required|string|max:100|alpha_dash|unique:congregations,slug,' . $congregation->id,
            'domain' => 'nullable|string|max:190|unique:congregations,domain,' . $congregation->id,
            'subdomain' => 'nullable|string|max:100|alpha_dash|unique:congregations,subdomain,' . $congregation->id,
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'required|boolean',
            'has_edificar_project' => 'nullable|boolean',
        ]);

        if ($congregation->is_primary && !$validated['is_active']) {
            return back()->withErrors(['is_active' => 'A congregação primária não pode ser desativada.'])->withInput();
        }

        $hasEdificar = $congregation->is_primary ? true : (bool) ($validated['has_edificar_project'] ?? false);

        $congregation->update([
            'name' => $validated['name'],
            'slug' => strtolower($validated['slug']),
            'domain' => filled($validated['domain'] ?? null) ? strtolower($validated['domain']) : null,
            'subdomain' => filled($validated['subdomain'] ?? null) ? strtolower($validated['subdomain']) : null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'has_edificar_project' => $hasEdificar,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()->route('national.congregations.index')
            ->with('success', 'Congregação atualizada.');
    }

    public function addAdmin(Request $request, Congregation $congregation)
    {
        $validated = $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:10|confirmed',
        ]);

        app(CongregationContext::class)->run($congregation, function () use ($validated) {
            User::create([
                'name' => $validated['admin_name'],
                'email' => strtolower($validated['admin_email']),
                'password' => $validated['admin_password'],
                'role' => 'admin',
                'is_active' => true,
            ]);
        });

        return back()->with('success', 'Novo administrador registado para esta congregação.');
    }

    public function resetAdminPassword(Request $request, Congregation $congregation, $adminUser)
    {
        $user = $adminUser instanceof User
            ? $adminUser
            : User::withoutGlobalScope('congregation')->findOrFail($adminUser);

        if ((int) $user->congregation_id !== (int) $congregation->id) {
            abort(403, 'Utilizador não pertence a esta congregação.');
        }

        $validated = $request->validate([
            'new_password' => 'required|string|min:10|confirmed',
        ]);

        app(CongregationContext::class)->run($congregation, function () use ($user, $validated) {
            $user->update([
                'password' => $validated['new_password'],
            ]);
        });

        return back()->with('success', "Palavra-passe de {$user->name} redefinida com sucesso.");
    }

    public function toggleAdminStatus(Request $request, Congregation $congregation, $adminUser)
    {
        $user = $adminUser instanceof User
            ? $adminUser
            : User::withoutGlobalScope('congregation')->findOrFail($adminUser);

        if ((int) $user->congregation_id !== (int) $congregation->id) {
            abort(403, 'Utilizador não pertence a esta congregação.');
        }

        app(CongregationContext::class)->run($congregation, function () use ($user) {
            $user->update([
                'is_active' => !$user->is_active,
            ]);
        });

        $status = $user->fresh()->is_active ? 'ativada' : 'desativada';
        return back()->with('success', "Conta de {$user->name} foi {$status} com sucesso.");
    }

    public function nationalReport(Request $request): View
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->date('start_date')?->startOfDay() ?? now()->startOfMonth();
        $endDate = $request->date('end_date')?->endOfDay() ?? now()->endOfMonth();
        $rows = collect($this->congregationReportRows($startDate, $endDate));

        return view('admin.national-dashboard.report', compact('rows', 'startDate', 'endDate'));
    }

    public function exportNationalReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->date('start_date')?->startOfDay() ?? now()->startOfMonth();
        $endDate = $request->date('end_date')?->endOfDay() ?? now()->endOfMonth();
        $rows = collect($this->congregationReportRows($startDate, $endDate));

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\NationalCongregationReportExport($rows),
            'relatorio-nacional-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xlsx'
        );
    }

    public function exportNationalPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->date('start_date')?->startOfDay() ?? now()->startOfMonth();
        $endDate = $request->date('end_date')?->endOfDay() ?? now()->endOfMonth();
        $rows = collect($this->congregationReportRows($startDate, $endDate));
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.national-dashboard.report-pdf', compact('rows', 'startDate', 'endDate'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('relatorio-nacional-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf');
    }

    public function switchToLocal()
    {
        session(['portal_context' => 'local']);
        return redirect()->route('dashboard.admin')->with('info', 'Contexto alterado para o Portal Local (Life Church).');
    }

    public function switchToNational()
    {
        session(['portal_context' => 'national']);
        return redirect()->route('national.dashboard')->with('info', 'De volta à Consola Nacional.');
    }

    public function downloadServicePdf(Congregation $congregation, $service)
    {
        return app(CongregationContext::class)->run($congregation, function () use ($service, $congregation) {
            $serviceModel = $service instanceof Service
                ? $service
                : Service::findOrFail($service);

            abort_unless((int) $serviceModel->congregation_id === (int) $congregation->id, 404, 'Culto não pertence a esta congregação.');

            $serviceModel->load(['preacher', 'offerings.offeringType', 'tithes', 'individualOfferings', 'zoneParticipations.zone']);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('services.pdf', [
                'service' => $serviceModel,
                'congregation' => $congregation,
            ]);

            $dateStr = \Carbon\Carbon::parse($serviceModel->date)->format('Y-m-d');
            $slug = \Illuminate\Support\Str::slug($congregation->name);
            return $pdf->download("relatorio_culto_{$slug}_{$dateStr}.pdf");
        });
    }

    public function downloadServicesReportPdf(Congregation $congregation, Request $request)
    {
        return app(CongregationContext::class)->run($congregation, function () use ($congregation, $request) {
            $startDate = $request->query('start_date') ?: now()->startOfMonth()->toDateString();
            $endDate = $request->query('end_date') ?: now()->endOfMonth()->toDateString();

            $query = Service::with(['zoneParticipations', 'offerings', 'tithes', 'individualOfferings', 'preacher'])
                ->whereBetween('date', [$startDate, $endDate]);

            if ($request->filled('service_type') && $request->service_type !== 'all') {
                if ($request->service_type === 'normal') {
                    $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
                } else {
                    $query->where('service_type', $request->service_type);
                }
            }

            $services = $query->orderBy('date', 'asc')->get();

            $formattedStart = \Carbon\Carbon::parse($startDate)->format('d/m/Y');
            $formattedEnd = \Carbon\Carbon::parse($endDate)->format('d/m/Y');
            $title = "Relatório de Cultos - {$congregation->name} ({$formattedStart} a {$formattedEnd})";

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('services.report-pdf', compact('services', 'title', 'congregation'))
                ->setPaper('a4', 'landscape');

            $slug = \Illuminate\Support\Str::slug($congregation->name);
            return $pdf->download("relatorio_cultos_{$slug}_{$startDate}_a_{$endDate}.pdf");
        });
    }

    private function monthlyContributionTrend(): array
    {
        $labels = [];
        $incomeValues = [];
        $expenseValues = [];

        for ($offset = 5; $offset >= 0; $offset--) {
            $month = now()->subMonths($offset);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            // Entradas da igreja: contribuições eclesiásticas (sem package_id)
            $churchContributions = (float) DB::table('contributions')
                ->join('congregations', 'congregations.id', '=', 'contributions.congregation_id')
                ->where('congregations.is_active', true)
                ->where('contributions.status', 'verificada')
                ->whereNull('contributions.package_id')
                ->whereBetween('contributions.contribution_date', [$start, $end])
                ->sum('contributions.amount');

            // Ofertas de culto eclesiásticas
            $cultosOfferings = (float) DB::table('service_offerings')
                ->join('congregations', 'congregations.id', '=', 'service_offerings.congregation_id')
                ->join('services', 'services.id', '=', 'service_offerings.service_id')
                ->join('offering_types', 'offering_types.id', '=', 'service_offerings.offering_type_id')
                ->where('congregations.is_active', true)
                ->where('offering_types.scope', 'eclesiastico')
                ->whereBetween('services.date', [$start, $end])
                ->sum('service_offerings.amount');

            // Dízimos de culto
            $cultosTithes = (float) DB::table('service_tithes')
                ->join('congregations', 'congregations.id', '=', 'service_tithes.congregation_id')
                ->join('services', 'services.id', '=', 'service_tithes.service_id')
                ->where('congregations.is_active', true)
                ->whereBetween('services.date', [$start, $end])
                ->sum('service_tithes.amount');

            // Ofertas especiais de culto
            $cultosSpecial = (float) DB::table('services')
                ->join('congregations', 'congregations.id', '=', 'services.congregation_id')
                ->where('congregations.is_active', true)
                ->whereBetween('services.date', [$start, $end])
                ->sum('services.special_offerings_total');

            // Ofertas individuais de culto
            $cultosIndividual = (float) DB::table('service_individual_offerings')
                ->join('congregations', 'congregations.id', '=', 'service_individual_offerings.congregation_id')
                ->join('services', 'services.id', '=', 'service_individual_offerings.service_id')
                ->where('congregations.is_active', true)
                ->whereBetween('services.date', [$start, $end])
                ->sum('service_individual_offerings.amount');

            $totalIncome = $churchContributions + $cultosOfferings + $cultosTithes + $cultosSpecial + $cultosIndividual;

            $expenses = (float) DB::table('expenses')
                ->join('congregations', 'congregations.id', '=', 'expenses.congregation_id')
                ->where('congregations.is_active', true)
                ->where('expenses.scope', 'eclesiastico')
                ->whereBetween('expenses.date', [$start, $end])
                ->sum('expenses.amount');

            $labels[] = $month->translatedFormat('M');
            $incomeValues[] = $totalIncome;
            $expenseValues[] = $expenses;
        }

        return [
            'labels' => $labels,
            'income' => $incomeValues,
            'expenses' => $expenseValues,
            'values' => $incomeValues, // Retrocompatibilidade com testes e gráficos
        ];
    }

    private function congregationReportRows($startDate, $endDate): array
    {
        $memberCounts = DB::table('users')->select('congregation_id', DB::raw('COUNT(*) as aggregate'))
            ->where('role', 'membro')->where('is_active', true)->groupBy('congregation_id')->pluck('aggregate', 'congregation_id');
        $zoneCounts = DB::table('zones')->select('congregation_id', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('congregation_id')->pluck('aggregate', 'congregation_id');
        $cellCounts = DB::table('cells')->select('congregation_id', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('congregation_id')->pluck('aggregate', 'congregation_id');
        $serviceCounts = DB::table('services')->select('congregation_id', DB::raw('COUNT(*) as aggregate'))
            ->whereBetween('date', [$startDate, $endDate])->groupBy('congregation_id')->pluck('aggregate', 'congregation_id');

        // Contribuições eclesiásticas verificadas (exclui Projeto Edificar / pacotes de obra)
        $verifiedAmounts = DB::table('contributions')->select('congregation_id', DB::raw('SUM(amount) as aggregate'))
            ->where('status', 'verificada')
            ->whereNull('package_id')
            ->whereBetween('contribution_date', [$startDate, $endDate])
            ->groupBy('congregation_id')->pluck('aggregate', 'congregation_id');

        $serviceOfferings = DB::table('service_offerings')
            ->join('services', 'services.id', '=', 'service_offerings.service_id')
            ->join('offering_types', 'offering_types.id', '=', 'service_offerings.offering_type_id')
            ->select('service_offerings.congregation_id', DB::raw('SUM(service_offerings.amount) as aggregate'))
            ->where('offering_types.scope', 'eclesiastico')
            ->whereBetween('services.date', [$startDate, $endDate])
            ->groupBy('service_offerings.congregation_id')->pluck('aggregate', 'congregation_id');

        $serviceTithes = DB::table('service_tithes')
            ->join('services', 'services.id', '=', 'service_tithes.service_id')
            ->select('service_tithes.congregation_id', DB::raw('SUM(service_tithes.amount) as aggregate'))
            ->whereBetween('services.date', [$startDate, $endDate])
            ->groupBy('service_tithes.congregation_id')->pluck('aggregate', 'congregation_id');

        $individualOfferings = DB::table('service_individual_offerings')
            ->join('services', 'services.id', '=', 'service_individual_offerings.service_id')
            ->select('service_individual_offerings.congregation_id', DB::raw('SUM(service_individual_offerings.amount) as aggregate'))
            ->whereBetween('services.date', [$startDate, $endDate])
            ->groupBy('service_individual_offerings.congregation_id')->pluck('aggregate', 'congregation_id');

        $specialOfferings = DB::table('services')->select('congregation_id', DB::raw('SUM(special_offerings_total) as aggregate'))
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('congregation_id')->pluck('aggregate', 'congregation_id');

        // Contribuições pendentes eclesiásticas
        $pendingCounts = DB::table('contributions')->select('congregation_id', DB::raw('COUNT(*) as aggregate'))
            ->where('status', 'pendente')
            ->whereNull('package_id')
            ->whereBetween('contribution_date', [$startDate, $endDate])
            ->groupBy('congregation_id')->pluck('aggregate', 'congregation_id');

        // Despesas eclesiásticas da igreja
        $expenseAmounts = DB::table('expenses')->select('congregation_id', DB::raw('SUM(amount) as aggregate'))
            ->where('scope', 'eclesiastico')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('congregation_id')->pluck('aggregate', 'congregation_id');

        return Congregation::orderByDesc('is_primary')->orderBy('name')->get()->map(function (Congregation $congregation) use ($memberCounts, $zoneCounts, $cellCounts, $serviceCounts, $verifiedAmounts, $serviceOfferings, $serviceTithes, $individualOfferings, $specialOfferings, $pendingCounts, $expenseAmounts) {
            $serviceIncome = (float) ($serviceOfferings[$congregation->id] ?? 0)
                + (float) ($serviceTithes[$congregation->id] ?? 0)
                + (float) ($individualOfferings[$congregation->id] ?? 0)
                + (float) ($specialOfferings[$congregation->id] ?? 0);
            $verifiedContributions = (float) ($verifiedAmounts[$congregation->id] ?? 0);
            $totalIncome = $verifiedContributions + $serviceIncome;
            $expenses = (float) ($expenseAmounts[$congregation->id] ?? 0);
            $netBalance = $totalIncome - $expenses;

            return [
                'congregation_id' => $congregation->id,
                'congregation' => $congregation->name,
                'slug' => $congregation->slug,
                'status' => $congregation->is_active ? 'Ativa' : 'Inativa',
                'has_edificar' => (bool) $congregation->has_edificar_project,
                'active_members' => (int) ($memberCounts[$congregation->id] ?? 0),
                'zones' => (int) ($zoneCounts[$congregation->id] ?? 0),
                'cells' => (int) ($cellCounts[$congregation->id] ?? 0),
                'services' => (int) ($serviceCounts[$congregation->id] ?? 0),
                'verified_contributions' => $verifiedContributions,
                'service_income' => $serviceIncome,
                'total_income' => $totalIncome,
                'expenses' => $expenses,
                'net_balance' => $netBalance,
                'pending_contributions' => (int) ($pendingCounts[$congregation->id] ?? 0),
            ];
        })->all();
    }

    private function attachCongregationCounts($congregations): void
    {
        $ids = $congregations->pluck('id')->all();
        if ($ids === []) {
            return;
        }

        $countByTenant = function (string $table) use ($ids) {
            return DB::table($table)->select('congregation_id', DB::raw('COUNT(*) as aggregate'))
                ->whereIn('congregation_id', $ids)
                ->groupBy('congregation_id')
                ->pluck('aggregate', 'congregation_id');
        };

        $userCounts = $countByTenant('users');
        $zoneCounts = $countByTenant('zones');
        $cellCounts = $countByTenant('cells');

        $congregations->each(function (Congregation $congregation) use ($userCounts, $zoneCounts, $cellCounts) {
            $congregation->setAttribute('users_count', (int) ($userCounts[$congregation->id] ?? 0));
            $congregation->setAttribute('zones_count', (int) ($zoneCounts[$congregation->id] ?? 0));
            $congregation->setAttribute('cells_count', (int) ($cellCounts[$congregation->id] ?? 0));
        });
    }
}
