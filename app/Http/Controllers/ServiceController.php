<?php

namespace App\Http\Controllers;

use App\Models\OfferingType;
use App\Models\Service;
use App\Models\ServiceOffering;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $query = Service::with(['preacher', 'offerings.offeringType', 'tithes', 'individualOfferings']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('theme', 'like', "%{$search}%")
                    ->orWhere('preacher_name', 'like', "%{$search}%")
                    ->orWhereHas('preacher', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        $services = $query->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        Gate::authorize('create', Service::class);

        $preachers = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor'])->get();
        $offeringTypes = OfferingType::where('is_active', true)->orderBy('order')->get();
        $zones = \App\Models\Zone::orderBy('name')->get();

        return view('services.create', compact('preachers', 'offeringTypes', 'zones'));
    }

    public function createTeaching()
    {
        Gate::authorize('create', Service::class);

        $preachers = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor'])->get();
        $offeringTypes = OfferingType::where('is_active', true)->orderBy('order')->get();
        $zones = \App\Models\Zone::orderBy('name')->get();

        return view('services.create-teaching', compact('preachers', 'offeringTypes', 'zones'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Service::class);

        if ($request->boolean('guest_preacher') || $request->filled('preacher_name')) {
            $request->merge(['preacher_id' => null]);
        } else {
            $request->merge(['preacher_name' => null]);
        }

        $numericFields = [
            'adults_members',
            'adults_visitors',
            'adults_salvations',
            'children_members',
            'children_visitors',
            'children_salvations',
            'special_offerings_total'
        ];

        foreach ($numericFields as $field) {
            if ($request->has($field)) {
                $val = $request->input($field);
                if (is_numeric($val)) {
                    $request->merge([$field => $field === 'special_offerings_total' ? (float) $val : (int) $val]);
                }
            }
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'service_type' => 'required|in:1st,2nd,3rd,4th,special,teaching',
            'preacher_id' => 'nullable|exists:users,id',
            'preacher_name' => 'nullable|string|max:255',
            'theme' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'observations' => 'nullable|string',
            'adults_members' => 'nullable|integer|min:0',
            'adults_visitors' => 'nullable|integer|min:0',
            'adults_salvations' => 'nullable|integer|min:0',
            'children_members' => 'nullable|integer|min:0',
            'children_visitors' => 'nullable|integer|min:0',
            'children_salvations' => 'nullable|integer|min:0',
            'special_offerings_total' => 'nullable|numeric|min:0',
            'offerings' => 'nullable|array',
            'offerings.*.amount' => 'nullable|numeric|min:0',
            'offerings.*.offering_type_id' => 'required|exists:offering_types,id',
            'tithes' => 'nullable|array',
            'tithes.*.amount' => 'nullable|numeric|min:0',
            'tithes.*.member_name' => 'nullable|string|max:255',
            'individual_offerings' => 'nullable|array',
            'individual_offerings.*.amount' => 'nullable|numeric|min:0',
            'individual_offerings.*.member_name' => 'nullable|string|max:255',
            'individual_offerings.*.description' => 'nullable|string|max:255',
            'individual_contributions' => 'nullable|array',
            'individual_contributions.*.type' => 'required_with:individual_contributions.*.amount|in:tithe,offering',
            'individual_contributions.*.amount' => 'nullable|numeric|min:0',
            'individual_contributions.*.member_name' => 'nullable|string|max:255',
            'individual_contributions.*.description' => 'nullable|string|max:255',
            'zone_participations' => 'nullable|array',
            'zone_participations.*.zone_id' => 'required|exists:zones,id',
            'zone_participations.*.adults_members' => 'nullable|integer|min:0',
            'zone_participations.*.adults_visitors' => 'nullable|integer|min:0',
            'zone_participations.*.leaders' => 'nullable|integer|min:0',
            'zone_participations.*.auxiliary_leaders' => 'nullable|integer|min:0',
            'zone_participations.*.supervisors' => 'nullable|integer|min:0',
            'zone_participations.*.zone_pastors' => 'nullable|integer|min:0',
            'zone_participations.*.children_members' => 'nullable|integer|min:0',
            'zone_participations.*.children_visitors' => 'nullable|integer|min:0',
        ]);

        foreach ($numericFields as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }

        DB::transaction(function () use ($validated) {
            $service = Service::create($validated);

            if (isset($validated['offerings'])) {
                foreach ($validated['offerings'] as $offeringData) {
                    if ($offeringData['amount'] > 0) {
                        $service->offerings()->create($offeringData);
                    }
                }
            }

            // Handle Unified Contributions
            if (isset($validated['individual_contributions'])) {
                foreach ($validated['individual_contributions'] as $contribution) {
                    if ($contribution['amount'] > 0) {
                        if ($contribution['type'] === 'tithe') {
                            $service->tithes()->create([
                                'member_name' => $contribution['member_name'],
                                'amount' => $contribution['amount']
                            ]);
                        } else {
                            $service->individualOfferings()->create([
                                'member_name' => $contribution['member_name'],
                                'amount' => $contribution['amount'],
                                'description' => $contribution['description'] ?? null
                            ]);
                        }
                    }
                }
            }

            if (isset($validated['tithes'])) {
                foreach ($validated['tithes'] as $titheData) {
                    if ($titheData['amount'] > 0) {
                        $service->tithes()->create($titheData);
                    }
                }
            }

            if (isset($validated['individual_offerings'])) {
                foreach ($validated['individual_offerings'] as $offeringData) {
                    if ($offeringData['amount'] > 0) {
                        $service->individualOfferings()->create($offeringData);
                    }
                }
            }

            if ($validated['service_type'] === 'teaching' && isset($validated['zone_participations'])) {
                foreach ($validated['zone_participations'] as $partData) {
                    $service->zoneParticipations()->create($partData);
                }
            }
        });

        return redirect()->route('services.index')->with('success', 'Culto registrado com sucesso!');
    }

    public function show(Service $service)
    {
        Gate::authorize('view', $service);

        $service->load(['preacher', 'offerings.offeringType', 'tithes', 'individualOfferings']);

        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        Gate::authorize('update', $service);

        if ($service->service_type === 'teaching') {
            return redirect()->route('services.edit-teaching', $service);
        }

        $preachers = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor'])->get();
        $offeringTypes = OfferingType::where('is_active', true)->orderBy('order')->get();
        $zones = \App\Models\Zone::orderBy('name')->get();
        $service->load(['offerings', 'tithes', 'individualOfferings', 'zoneParticipations']);

        return view('services.edit', compact('service', 'preachers', 'offeringTypes', 'zones'));
    }

    public function editTeaching(Service $service)
    {
        Gate::authorize('update', $service);

        $preachers = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor'])->get();
        $offeringTypes = OfferingType::where('is_active', true)->orderBy('order')->get();
        $zones = \App\Models\Zone::orderBy('name')->get();
        $service->load(['offerings', 'tithes', 'individualOfferings', 'zoneParticipations']);

        return view('services.edit-teaching', compact('service', 'preachers', 'offeringTypes', 'zones'));
    }

    public function update(Request $request, Service $service)
    {
        Gate::authorize('update', $service);

        if ($request->boolean('guest_preacher') || $request->filled('preacher_name')) {
            $request->merge(['preacher_id' => null]);
        } else {
            $request->merge(['preacher_name' => null]);
        }

        $numericFields = [
            'adults_members',
            'adults_visitors',
            'adults_salvations',
            'children_members',
            'children_visitors',
            'children_salvations',
            'special_offerings_total'
        ];

        foreach ($numericFields as $field) {
            if ($request->has($field)) {
                $val = $request->input($field);
                if (is_numeric($val)) {
                    $request->merge([$field => $field === 'special_offerings_total' ? (float) $val : (int) $val]);
                }
            }
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'service_type' => 'required|in:1st,2nd,3rd,4th,special,teaching',
            'preacher_id' => 'nullable|exists:users,id',
            'preacher_name' => 'nullable|string|max:255',
            'theme' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'observations' => 'nullable|string',
            'adults_members' => 'nullable|integer|min:0',
            'adults_visitors' => 'nullable|integer|min:0',
            'adults_salvations' => 'nullable|integer|min:0',
            'children_members' => 'nullable|integer|min:0',
            'children_visitors' => 'nullable|integer|min:0',
            'children_salvations' => 'nullable|integer|min:0',
            'special_offerings_total' => 'nullable|numeric|min:0',
            'offerings' => 'nullable|array',
            'offerings.*.amount' => 'nullable|numeric|min:0',
            'offerings.*.offering_type_id' => 'required|exists:offering_types,id',
            'tithes' => 'nullable|array',
            'tithes.*.amount' => 'nullable|numeric|min:0',
            'tithes.*.member_name' => 'nullable|string|max:255',
            'individual_offerings' => 'nullable|array',
            'individual_offerings.*.amount' => 'nullable|numeric|min:0',
            'individual_offerings.*.member_name' => 'nullable|string|max:255',
            'individual_offerings.*.description' => 'nullable|string|max:255',
            'individual_contributions' => 'nullable|array',
            'individual_contributions.*.type' => 'required_with:individual_contributions.*.amount|in:tithe,offering',
            'individual_contributions.*.amount' => 'nullable|numeric|min:0',
            'individual_contributions.*.member_name' => 'nullable|string|max:255',
            'individual_contributions.*.description' => 'nullable|string|max:255',
            'zone_participations' => 'nullable|array',
            'zone_participations.*.zone_id' => 'required|exists:zones,id',
            'zone_participations.*.adults_members' => 'nullable|integer|min:0',
            'zone_participations.*.adults_visitors' => 'nullable|integer|min:0',
            'zone_participations.*.leaders' => 'nullable|integer|min:0',
            'zone_participations.*.auxiliary_leaders' => 'nullable|integer|min:0',
            'zone_participations.*.supervisors' => 'nullable|integer|min:0',
            'zone_participations.*.zone_pastors' => 'nullable|integer|min:0',
            'zone_participations.*.children_members' => 'nullable|integer|min:0',
            'zone_participations.*.children_visitors' => 'nullable|integer|min:0',
        ]);

        foreach ($numericFields as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }

        DB::transaction(function () use ($validated, $service) {
            $service->update($validated);

            $service->offerings()->delete();
            $service->tithes()->delete();
            $service->individualOfferings()->delete();

            if (isset($validated['offerings'])) {
                foreach ($validated['offerings'] as $offeringData) {
                    if ($offeringData['amount'] > 0) {
                        $service->offerings()->create($offeringData);
                    }
                }
            }

            // Handle Unified Contributions
            if (isset($validated['individual_contributions'])) {
                foreach ($validated['individual_contributions'] as $contribution) {
                    if ($contribution['amount'] > 0) {
                        if ($contribution['type'] === 'tithe') {
                            $service->tithes()->create([
                                'member_name' => $contribution['member_name'],
                                'amount' => $contribution['amount']
                            ]);
                        } else {
                            $service->individualOfferings()->create([
                                'member_name' => $contribution['member_name'],
                                'amount' => $contribution['amount'],
                                'description' => $contribution['description'] ?? null
                            ]);
                        }
                    }
                }
            }

            // Legacy support if needed, but UI will likely use unified array
            if (isset($validated['tithes'])) {
                foreach ($validated['tithes'] as $titheData) {
                    if ($titheData['amount'] > 0) {
                        $service->tithes()->create($titheData);
                    }
                }
            }

            if (isset($validated['individual_offerings'])) {
                foreach ($validated['individual_offerings'] as $offeringData) {
                    if ($offeringData['amount'] > 0) {
                        $service->individualOfferings()->create($offeringData);
                    }
                }
            }

            if ($validated['service_type'] === 'teaching' && isset($validated['zone_participations'])) {
                $service->zoneParticipations()->delete();
                foreach ($validated['zone_participations'] as $partData) {
                    $service->zoneParticipations()->create($partData);
                }
            }
        });

        return redirect()->route('services.index')->with('success', 'Culto atualizado com sucesso!');
    }

    public function destroy(Service $service)
    {
        Gate::authorize('delete', $service);

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Culto excluído com sucesso!');
    }

    public function downloadPdf(Service $service)
    {
        Gate::authorize('view', $service);

        $service->load(['preacher', 'offerings.offeringType', 'tithes', 'individualOfferings']);

        $pdf = Pdf::loadView('services.pdf', compact('service'));

        return $pdf->download("relatorio_culto_" . \Carbon\Carbon::parse($service->date)->format('Y-m-d') . ".pdf");
    }

    public function report(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $query = Service::query();

        // Filters
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('service_type')) {
            if ($request->service_type === 'normal') {
                $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
            } else {
                $query->where('service_type', $request->service_type);
            }
        }

        $trendServices = $query->with('zoneParticipations')->orderBy('date', 'desc')->get()->reverse();

        $stats = [
            'labels' => $trendServices->map(fn($s) => \Carbon\Carbon::parse($s->date)->format('d/m'))->values(),
            'attendance' => $trendServices->map(fn($s) => $s->total_participation)->values(),
            'visitors' => $trendServices->map(fn($s) => $s->total_visitors)->values(),
            'salvations' => $trendServices->map(fn($s) => $s->adults_salvations + $s->children_salvations)->values(),
        ];

        return view('services.report', compact('stats', 'trendServices'));
    }

    public function exportMonthly(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'service_type' => 'nullable|string',
        ]);

        $query = Service::with(['zoneParticipations', 'offerings', 'tithes', 'individualOfferings'])
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month);

        if ($request->service_type === 'normal') {
            $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
        } elseif ($request->service_type === 'teaching') {
            $query->where('service_type', 'teaching');
        }

        $services = $query->orderBy('date', 'asc')->get();

        $typeLabel = match ($request->service_type) {
            'normal' => ' (Cultos Normais)',
            'teaching' => ' (Cultos de Ensino)',
            default => '',
        };

        $title = "Relatório Mensal de Cultos{$typeLabel} - " . \Carbon\Carbon::createFromDate($request->year, $request->month)->translatedFormat('F/Y');

        $pdf = Pdf::loadView('services.report-pdf', compact('services', 'title'));
        return $pdf->download("relatorio_mensal_cultos_{$request->year}_{$request->month}.pdf");
    }

    public function exportQuarterly(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $request->validate([
            'quarter' => 'required|integer|between:1,4',
            'year' => 'required|integer',
            'service_type' => 'nullable|string',
        ]);

        $months = match ((int) $request->quarter) {
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        };

        $query = Service::with(['zoneParticipations', 'offerings', 'tithes', 'individualOfferings'])
            ->whereYear('date', $request->year)
            ->whereIn(DB::raw('MONTH(date)'), $months);

        if ($request->service_type === 'normal') {
            $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
        } elseif ($request->service_type === 'teaching') {
            $query->where('service_type', 'teaching');
        }

        $services = $query->orderBy('date', 'asc')->get();

        $typeLabel = match ($request->service_type) {
            'normal' => ' (Cultos Normais)',
            'teaching' => ' (Cultos de Ensino)',
            default => '',
        };

        $title = "Relatório Trimestral de Cultos{$typeLabel} - {$request->quarter}º Trimestre / {$request->year}";

        $pdf = Pdf::loadView('services.report-pdf', compact('services', 'title'));
        return $pdf->download("relatorio_trimestral_cultos_{$request->year}_Q{$request->quarter}.pdf");
    }

    /**
     * Export custom date range report (Pastor Luis request)
     */
    public function exportCustom(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'service_type' => 'required|string',
        ]);

        $query = Service::with(['zoneParticipations', 'offerings', 'tithes', 'individualOfferings'])
            ->whereBetween('date', [$request->date_from, $request->date_to]);

        // Handle service type filtering
        if ($request->service_type === 'all') {
            // Include all services, no filter
        } elseif ($request->service_type === 'normal') {
            $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
        } elseif (in_array($request->service_type, ['1st', '2nd', '3rd', '4th', 'teaching', 'special'])) {
            $query->where('service_type', $request->service_type);
        }

        $services = $query->orderBy('date', 'asc')->get();

        // Build title based on service type
        $typeLabel = match ($request->service_type) {
            'all' => ' (Todos os Cultos)',
            '1st' => ' (1º Culto)',
            '2nd' => ' (2º Culto)',
            '3rd' => ' (3º Culto)',
            '4th' => ' (4º Culto)',
            'normal' => ' (Cultos Normais)',
            'teaching' => ' (Cultos de Ensino)',
            'special' => ' (Cultos Especiais)',
            default => '',
        };

        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d/m/Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d/m/Y');
        $title = "Relatório Personalizado de Cultos{$typeLabel} - {$dateFrom} a {$dateTo}";

        $pdf = Pdf::loadView('services.report-pdf', compact('services', 'title'));

        $filename = "relatorio_cultos_" . \Carbon\Carbon::parse($request->date_from)->format('Y-m-d') . "_a_" . \Carbon\Carbon::parse($request->date_to)->format('Y-m-d') . ".pdf";

        return $pdf->download($filename);
    }

    /**
     * Export annual report
     */
    public function exportAnnual(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $request->validate([
            'year' => 'required|integer',
            'service_type' => 'nullable|string',
        ]);

        $query = Service::with(['zoneParticipations', 'offerings', 'tithes', 'individualOfferings'])
            ->whereYear('date', $request->year);

        if ($request->service_type === 'normal') {
            $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
        } elseif ($request->service_type === 'teaching') {
            $query->where('service_type', 'teaching');
        } elseif ($request->service_type === 'special') {
            $query->where('service_type', 'special');
        }

        $services = $query->orderBy('date', 'asc')->get();

        $typeLabel = match ($request->service_type) {
            'normal' => ' (Cultos Normais)',
            'teaching' => ' (Cultos de Ensino)',
            'special' => ' (Cultos Especiais)',
            default => '',
        };

        $title = "Relatório Anual de Cultos{$typeLabel} - {$request->year}";

        $pdf = Pdf::loadView('services.report-pdf', compact('services', 'title'));
        return $pdf->download("relatorio_anual_cultos_{$request->year}.pdf");
    }

    /**
     * Export custom date range report to Excel
     */
    public function exportCustomExcel(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'service_type' => 'required|string',
        ]);

        $query = Service::with(['preacher', 'zoneParticipations', 'offerings', 'tithes', 'individualOfferings'])
            ->whereBetween('date', [$request->date_from, $request->date_to]);

        if ($request->service_type === 'all') {
            // Include all services
        } elseif ($request->service_type === 'normal') {
            $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
        } elseif (in_array($request->service_type, ['1st', '2nd', '3rd', '4th', 'teaching', 'special'])) {
            $query->where('service_type', $request->service_type);
        }

        $services = $query->orderBy('date', 'asc')->get();

        $typeLabel = match ($request->service_type) {
            'all' => 'Todos os Cultos',
            '1st' => '1º Culto',
            '2nd' => '2º Culto',
            '3rd' => '3º Culto',
            '4th' => '4º Culto',
            'normal' => 'Cultos Normais',
            'teaching' => 'Cultos de Ensino',
            'special' => 'Cultos Especiais',
            default => '',
        };

        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('Y-m-d');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('Y-m-d');
        $title = "Relatório Personalizado - {$typeLabel}";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ServicesExport($services, $title),
            "relatorio_cultos_{$dateFrom}_a_{$dateTo}.xlsx"
        );
    }

    /**
     * Export monthly report to Excel
     */
    public function exportMonthlyExcel(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'service_type' => 'nullable|string',
        ]);

        $query = Service::with(['preacher', 'zoneParticipations', 'offerings', 'tithes', 'individualOfferings'])
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month);

        if ($request->service_type === 'normal') {
            $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
        } elseif ($request->service_type === 'teaching') {
            $query->where('service_type', 'teaching');
        }

        $services = $query->orderBy('date', 'asc')->get();

        $typeLabel = match ($request->service_type) {
            'normal' => 'Cultos Normais',
            'teaching' => 'Cultos de Ensino',
            default => 'Todos',
        };

        $monthName = \Carbon\Carbon::createFromDate($request->year, $request->month)->translatedFormat('F');
        $title = "Relatório Mensal - {$typeLabel} - {$monthName}/{$request->year}";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ServicesExport($services, $title),
            "relatorio_mensal_cultos_{$request->year}_{$request->month}.xlsx"
        );
    }

    /**
     * Export quarterly report to Excel
     */
    public function exportQuarterlyExcel(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $request->validate([
            'quarter' => 'required|integer|between:1,4',
            'year' => 'required|integer',
            'service_type' => 'nullable|string',
        ]);

        $months = match ((int) $request->quarter) {
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        };

        $query = Service::with(['preacher', 'zoneParticipations', 'offerings', 'tithes', 'individualOfferings'])
            ->whereYear('date', $request->year)
            ->whereIn(DB::raw('MONTH(date)'), $months);

        if ($request->service_type === 'normal') {
            $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
        } elseif ($request->service_type === 'teaching') {
            $query->where('service_type', 'teaching');
        }

        $services = $query->orderBy('date', 'asc')->get();

        $typeLabel = match ($request->service_type) {
            'normal' => 'Cultos Normais',
            'teaching' => 'Cultos de Ensino',
            default => 'Todos',
        };

        $title = "Relatório Trimestral - {$typeLabel} - Q{$request->quarter}/{$request->year}";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ServicesExport($services, $title),
            "relatorio_trimestral_cultos_{$request->year}_Q{$request->quarter}.xlsx"
        );
    }

    /**
     * Export annual report to Excel
     */
    public function exportAnnualExcel(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $request->validate([
            'year' => 'required|integer',
            'service_type' => 'nullable|string',
        ]);

        $query = Service::with(['preacher', 'zoneParticipations', 'offerings', 'tithes', 'individualOfferings'])
            ->whereYear('date', $request->year);

        if ($request->service_type === 'normal') {
            $query->whereIn('service_type', ['1st', '2nd', '3rd', '4th']);
        } elseif ($request->service_type === 'teaching') {
            $query->where('service_type', 'teaching');
        } elseif ($request->service_type === 'special') {
            $query->where('service_type', 'special');
        }

        $services = $query->orderBy('date', 'asc')->get();

        $typeLabel = match ($request->service_type) {
            'normal' => 'Cultos Normais',
            'teaching' => 'Cultos de Ensino',
            'special' => 'Cultos Especiais',
            default => 'Todos',
        };

        $title = "Relatório Anual - {$typeLabel} - {$request->year}";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ServicesExport($services, $title),
            "relatorio_anual_cultos_{$request->year}.xlsx"
        );
    }

    /**
     * Bulk delete services
     */
    public function bulkDestroy(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Apenas administradores podem realizar esta ação.');
        }

        $validated = $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id'
        ]);

        $deletedCount = Service::whereIn('id', $validated['service_ids'])->delete();

        return redirect()->route('services.index')
            ->with('success', "{$deletedCount} culto(s) excluído(s) com sucesso!");
    }
}
