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
    public function index()
    {
        Gate::authorize('viewAny', Service::class);

        $services = Service::with(['preacher', 'offerings.offeringType'])
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('services.index', compact('services'));
    }

    public function create()
    {
        Gate::authorize('create', Service::class);

        $preachers = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor'])->get();
        $offeringTypes = OfferingType::where('is_active', true)->orderBy('order')->get();

        return view('services.create', compact('preachers', 'offeringTypes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Service::class);

        if ($request->preacher_id === 'other') {
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
            'service_type' => 'required|in:1st,2nd,3rd,4th,special',
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
            'individual_contributions.*.type' => 'required|in:tithe,offering',
            'individual_contributions.*.amount' => 'required|numeric|min:0',
            'individual_contributions.*.member_name' => 'nullable|string|max:255',
            'individual_contributions.*.description' => 'nullable|string|max:255',
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

        $preachers = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor'])->get();
        $offeringTypes = OfferingType::where('is_active', true)->orderBy('order')->get();
        $service->load(['offerings', 'tithes', 'individualOfferings']);

        return view('services.edit', compact('service', 'preachers', 'offeringTypes'));
    }

    public function update(Request $request, Service $service)
    {
        Gate::authorize('update', $service);

        if ($request->preacher_id === 'other') {
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
            'service_type' => 'required|in:1st,2nd,3rd,4th,special',
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
            'individual_contributions.*.type' => 'required|in:tithe,offering',
            'individual_contributions.*.amount' => 'required|numeric|min:0',
            'individual_contributions.*.member_name' => 'nullable|string|max:255',
            'individual_contributions.*.description' => 'nullable|string|max:255',
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

    public function report()
    {
        Gate::authorize('viewAny', Service::class);

        // Get last 12 services for trend analysis
        $trendServices = Service::orderBy('date', 'desc')->take(12)->get()->reverse();

        $stats = [
            'labels' => $trendServices->map(fn($s) => \Carbon\Carbon::parse($s->date)->format('d/m')),
            'attendance' => $trendServices->map(fn($s) => $s->adults_members + $s->adults_visitors + $s->children_members + $s->children_visitors),
            'visitors' => $trendServices->map(fn($s) => $s->adults_visitors + $s->children_visitors),
            'salvations' => $trendServices->map(fn($s) => $s->adults_salvations + $s->children_salvations),
        ];

        return view('services.report', compact('stats', 'trendServices'));
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
