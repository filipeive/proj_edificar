<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use App\Models\QuarterlyReport;
use App\Models\QuarterlyReportEvent;
use App\Models\Supervision;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class QuarterlyReportController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', QuarterlyReport::class);

        $user = auth()->user();
        $query = QuarterlyReport::with(['zone', 'supervisor']);

        if ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $query->where('zone_id', $zoneId);
        } elseif ($user->role === 'supervisor') {
            $query->where('supervisor_id', $user->id);
        }

        $reports = $query->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->paginate(15);

        return view('quarterly_reports.index', compact('reports'));
    }

    public function create()
    {
        Gate::authorize('create', QuarterlyReport::class);

        $user = auth()->user();
        $zones = collect();
        $supervisions = collect();
        $userSupervision = null;

        if ($user->role === 'admin' || $user->role === 'pastor') {
            $zones = Zone::with('supervisions')->get();
        } elseif ($user->role === 'pastor_zona') {
            $zones = Zone::where('pastor_id', $user->id)->with('supervisions')->get();
        } elseif ($user->role === 'supervisor') {
            $zoneId = $user->getZoneId();
            $zones = Zone::where('id', $zoneId)->get();
            $userSupervision = \App\Models\Supervision::where('supervisor_id', $user->id)->first();
            if ($userSupervision) {
                $supervisions = collect([$userSupervision]);
            }
        }

        $eventTypes = EventType::where('is_active', true)->get();

        return view('quarterly_reports.create', compact('zones', 'supervisions', 'userSupervision', 'eventTypes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', QuarterlyReport::class);

        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'supervision_id' => 'required|exists:supervisions,id',
            'year' => 'required|integer|min:2020|max:2100',
            'quarter' => 'required|integer|min:1|max:4',
            'leaders_count' => 'required|integer|min:0',
            'cells_count' => 'required|integer|min:0',
            'timoteos_count' => 'required|integer|min:0',
            'members_count' => 'required|integer|min:0',
            'participants_count' => 'required|integer|min:0',
            'saved_count' => 'required|integer|min:0',
            'planned_baptism_count' => 'required|integer|min:0',
            'baptized_count' => 'required|integer|min:0',
            'cell_multiplications_count' => 'required|integer|min:0',
            'disciplined_leaders_count' => 'required|integer|min:0',
            'closed_cells_count' => 'required|integer|min:0',
            'ministerial_observations' => 'nullable|string',
            'discipleship_score' => 'required|integer|min:1|max:10',
            'pastoral_score' => 'required|integer|min:1|max:10',
            'cell_participation_score' => 'required|integer|min:1|max:10',
            'service_participation_score' => 'required|integer|min:1|max:10',
            'communion_in_cells_score' => 'required|integer|min:1|max:10',
            'relationship_building_score' => 'required|integer|min:1|max:10',
            'prayer_intercession_score' => 'required|integer|min:1|max:10',
            'events' => 'nullable|array',
            'events.*.event_type_id' => 'required|exists:event_types,id',
            'events.*.count' => 'required|integer|min:0',
            'events.*.description' => 'nullable|string',
        ]);

        // Check for duplicate report
        $exists = QuarterlyReport::where('supervision_id', $validated['supervision_id'])
            ->where('year', $validated['year'])
            ->where('quarter', $validated['quarter'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['quarter' => 'Já existe um relatório para esta zona, ano e trimestre.'])->withInput();
        }

        DB::transaction(function () use ($validated) {
            $report = QuarterlyReport::create(array_merge($validated, [
                'supervisor_id' => auth()->id(),
                'status' => 'submitted',
                'submitted_at' => now(),
            ]));

            if (isset($validated['events'])) {
                foreach ($validated['events'] as $eventData) {
                    if ($eventData['count'] > 0) {
                        $report->events()->create($eventData);
                    }
                }
            }
        });

        return redirect()->route('quarterly-reports.index')->with('success', 'Relatório trimestral submetido com sucesso!');
    }

    public function show(QuarterlyReport $quarterlyReport)
    {
        Gate::authorize('view', $quarterlyReport);

        $quarterlyReport->load(['zone', 'supervisor', 'events.eventType']);

        return view('quarterly_reports.show', compact('quarterlyReport'));
    }

    public function edit(QuarterlyReport $quarterlyReport)
    {
        Gate::authorize('update', $quarterlyReport);

        $user = auth()->user();
        $zones = collect();
        $supervisions = collect();

        if ($user->role === 'admin' || $user->role === 'pastor') {
            $zones = Zone::with('supervisions')->get();
        } elseif ($user->role === 'pastor_zona') {
            $zones = Zone::where('pastor_id', $user->id)->with('supervisions')->get();
        } elseif ($user->role === 'supervisor') {
            $zoneId = $user->getZoneId();
            $zones = Zone::where('id', $zoneId)->get();
            $supervisions = Supervision::where('supervisor_id', $user->id)->get();
        }

        $eventTypes = EventType::where('is_active', true)->get();
        $quarterlyReport->load(['events', 'supervision']);

        return view('quarterly_reports.edit', compact('quarterlyReport', 'zones', 'supervisions', 'eventTypes'));
    }

    public function update(Request $request, QuarterlyReport $quarterlyReport)
    {
        Gate::authorize('update', $quarterlyReport);

        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'supervision_id' => 'required|exists:supervisions,id',
            'year' => 'required|integer|min:2020|max:2100',
            'quarter' => 'required|integer|min:1|max:4',
            'leaders_count' => 'required|integer|min:0',
            'cells_count' => 'required|integer|min:0',
            'timoteos_count' => 'required|integer|min:0',
            'members_count' => 'required|integer|min:0',
            'participants_count' => 'required|integer|min:0',
            'saved_count' => 'required|integer|min:0',
            'planned_baptism_count' => 'required|integer|min:0',
            'baptized_count' => 'required|integer|min:0',
            'cell_multiplications_count' => 'required|integer|min:0',
            'disciplined_leaders_count' => 'required|integer|min:0',
            'closed_cells_count' => 'required|integer|min:0',
            'ministerial_observations' => 'nullable|string',
            'discipleship_score' => 'required|integer|min:1|max:10',
            'pastoral_score' => 'required|integer|min:1|max:10',
            'cell_participation_score' => 'required|integer|min:1|max:10',
            'service_participation_score' => 'required|integer|min:1|max:10',
            'communion_in_cells_score' => 'required|integer|min:1|max:10',
            'relationship_building_score' => 'required|integer|min:1|max:10',
            'prayer_intercession_score' => 'required|integer|min:1|max:10',
            'events' => 'nullable|array',
            'events.*.event_type_id' => 'required|exists:event_types,id',
            'events.*.count' => 'required|integer|min:0',
            'events.*.description' => 'nullable|string',
        ]);

        // Check for duplicate report (excluding current)
        $exists = QuarterlyReport::where('supervision_id', $validated['supervision_id'])
            ->where('year', $validated['year'])
            ->where('quarter', $validated['quarter'])
            ->where('id', '!=', $quarterlyReport->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['quarter' => 'Já existe um relatório para esta zona, ano e trimestre.'])->withInput();
        }

        DB::transaction(function () use ($validated, $quarterlyReport) {
            $quarterlyReport->update($validated);

            $quarterlyReport->events()->delete();

            if (isset($validated['events'])) {
                foreach ($validated['events'] as $eventData) {
                    if ($eventData['count'] > 0) {
                        $quarterlyReport->events()->create($eventData);
                    }
                }
            }
        });

        return redirect()->route('quarterly-reports.index')->with('success', 'Relatório trimestral atualizado com sucesso!');
    }

    public function destroy(QuarterlyReport $quarterlyReport)
    {
        Gate::authorize('delete', $quarterlyReport);

        $quarterlyReport->delete();

        return redirect()->route('quarterly-reports.index')->with('success', 'Relatório trimestral excluído com sucesso!');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\QuarterlyReportExport(),
            'relatorios_trimestrais_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
