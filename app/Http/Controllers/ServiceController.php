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

        $validated = $request->validate([
            'date' => 'required|date',
            'service_type' => 'required|in:1st,2nd,3rd,4th,special',
            'preacher_id' => 'nullable|exists:users,id',
            'theme' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'observations' => 'nullable|string',
            'adults_count' => 'required|integer|min:0',
            'children_count' => 'required|integer|min:0',
            'offerings' => 'nullable|array',
            'offerings.*.amount' => 'required|numeric|min:0',
            'offerings.*.offering_type_id' => 'required|exists:offering_types,id',
        ]);

        DB::transaction(function () use ($validated) {
            $service = Service::create($validated);

            if (isset($validated['offerings'])) {
                foreach ($validated['offerings'] as $offeringData) {
                    if ($offeringData['amount'] > 0) {
                        $service->offerings()->create($offeringData);
                    }
                }
            }
        });

        return redirect()->route('services.index')->with('success', 'Culto registrado com sucesso!');
    }

    public function show(Service $service)
    {
        Gate::authorize('view', $service);

        $service->load(['preacher', 'offerings.offeringType']);

        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        Gate::authorize('update', $service);

        $preachers = User::whereIn('role', ['admin', 'pastor', 'pastor_zona', 'supervisor'])->get();
        $offeringTypes = OfferingType::where('is_active', true)->orderBy('order')->get();
        $service->load('offerings');

        return view('services.edit', compact('service', 'preachers', 'offeringTypes'));
    }

    public function update(Request $request, Service $service)
    {
        Gate::authorize('update', $service);

        $validated = $request->validate([
            'date' => 'required|date',
            'service_type' => 'required|in:1st,2nd,3rd,4th,special',
            'preacher_id' => 'nullable|exists:users,id',
            'theme' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'observations' => 'nullable|string',
            'adults_count' => 'required|integer|min:0',
            'children_count' => 'required|integer|min:0',
            'offerings' => 'nullable|array',
            'offerings.*.amount' => 'required|numeric|min:0',
            'offerings.*.offering_type_id' => 'required|exists:offering_types,id',
        ]);

        DB::transaction(function () use ($validated, $service) {
            $service->update($validated);

            $service->offerings()->delete();

            if (isset($validated['offerings'])) {
                foreach ($validated['offerings'] as $offeringData) {
                    if ($offeringData['amount'] > 0) {
                        $service->offerings()->create($offeringData);
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

        $service->load(['preacher', 'offerings.offeringType']);

        $pdf = Pdf::loadView('services.pdf', compact('service'));

        return $pdf->download("relatorio_culto_" . \Carbon\Carbon::parse($service->date)->format('Y-m-d') . ".pdf");
    }
}
