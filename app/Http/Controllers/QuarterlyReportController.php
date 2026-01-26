<?php

namespace App\Http\Controllers;

use App\Exports\AnnualQuarterlyReportExport;
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
    public function index(Request $request)
    {
        Gate::authorize('viewAny', QuarterlyReport::class);

        $user = auth()->user();
        $query = QuarterlyReport::with(['zone', 'supervisor', 'supervision']);

        // Filters
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('quarter')) {
            $query->where('quarter', $request->quarter);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('supervisor', fn($sq) => $sq->where('name', 'LIKE', "%$search%"))
                    ->orWhereHas('zone', fn($zq) => $zq->where('name', 'LIKE', "%$search%"));
            });
        }

        if ($user->role === 'pastor_zona') {
            $zoneId = $user->getZoneId();
            $query->where('zone_id', $zoneId);
        } elseif ($user->role === 'supervisor') {
            $query->where('supervisor_id', $user->id);
        }

        $reports = $query->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Aggregated Analytics for Charts (Last 8 combinations of Year/Quarter)
        $analyticsQuery = QuarterlyReport::query()
            ->select(
                'year',
                'quarter',
                DB::raw('SUM(members_count) as total_members'),
                DB::raw('SUM(cells_count) as total_cells'),
                DB::raw('SUM(saved_count) as total_saved'),
                DB::raw('SUM(baptized_count) as total_baptized')
            );

        if ($user->role === 'pastor_zona') {
            $analyticsQuery->where('zone_id', $user->getZoneId());
        } elseif ($user->role === 'supervisor') {
            $analyticsQuery->where('supervisor_id', $user->id);
        }

        $recentStats = $analyticsQuery->groupBy('year', 'quarter')
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->limit(8)
            ->get();

        // Prepare chart data
        $chartLabels = $recentStats->map(fn($s) => "Q{$s->quarter}/{$s->year}")->reverse()->values();
        $membersData = $recentStats->pluck('total_members')->reverse()->values();
        $cellsData = $recentStats->pluck('total_cells')->reverse()->values();
        $savedData = $recentStats->pluck('total_saved')->reverse()->values();
        $baptizedData = $recentStats->pluck('total_baptized')->reverse()->values();

        // Summary Stats (Latest Period)
        $latest = $recentStats->first();
        $totalMembers = $latest->total_members ?? 0;
        $totalCells = $latest->total_cells ?? 0;
        $totalSaved = $recentStats->sum('total_saved');
        $totalBaptized = $recentStats->sum('total_baptized');

        return view('quarterly_reports.index', compact(
            'reports',
            'chartLabels',
            'membersData',
            'cellsData',
            'savedData',
            'baptizedData',
            'totalMembers',
            'totalCells',
            'totalSaved',
            'totalBaptized'
        ));
    }

    public function bulkDestroy(Request $request)
    {
        Gate::authorize('deleteAny', QuarterlyReport::class);

        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Nenhum relatório selecionado.');
        }

        QuarterlyReport::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' relatórios excluídos com sucesso.');
    }

    public function create()
    {
        Gate::authorize('create', QuarterlyReport::class);

        $user = auth()->user();
        $zones = collect();
        $supervisions = collect();

        // Lógica de seleção baseada no cargo
        if ($user->role === 'admin' || $user->role === 'pastor' || $user->role === 'secretaria') {
            $zones = Zone::with('supervisions')->get();
            $supervisions = Supervision::all();
        } elseif ($user->role === 'pastor_zona') {
            $zoneIds = $user->getManagedZoneIds();
            $zones = Zone::whereIn('id', $zoneIds)->with('supervisions')->get();
            if ($zones->isNotEmpty()) {
                $supervisions = Supervision::whereIn('zone_id', $zoneIds)->get();
            }
        } elseif ($user->role === 'supervisor') {
            $supervisionIds = $user->getManagedSupervisionIds();
            if ($supervisionIds->isNotEmpty()) {
                $supervisions = Supervision::whereIn('id', $supervisionIds)->get();
                $zones = Zone::whereIn('id', $supervisions->pluck('zone_id'))->get();
            }
        }

        $eventTypes = EventType::where('is_active', true)->get();

        return view('quarterly_reports.create', compact('zones', 'supervisions', 'eventTypes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', QuarterlyReport::class);

        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'supervision_id' => 'required|exists:supervisions,id',
            'year' => 'required|integer|min:2000|max:2100',
            'quarter' => 'required|integer|min:1|max:4',
            'leaders_count' => 'required|integer|min:0',
            'cells_count' => 'required|integer|min:0',
            'timoteos_count' => 'required|integer|min:0',
            'members_count' => 'required|integer|min:0',
            'participants_count' => 'required|integer|min:0',
            'pastors_count' => 'required|integer|min:0',
            'supervisors_count' => 'required|integer|min:0',
            'visitors_count' => 'required|integer|min:0',
            'saved_count' => 'required|integer|min:0',
            'planned_baptism_count' => 'required|integer|min:0',
            'baptized_count' => 'required|integer|min:0',
            'cell_multiplications_count' => 'required|integer|min:0',
            'disciplined_leaders_count' => 'required|integer|min:0',
            'closed_cells_count' => 'required|integer|min:0',
            'ministerial_observations' => 'nullable|string',
            'discipleship_score' => 'required|integer|min:0|max:3',
            'evangelism_strategy' => 'required|integer|min:0|max:3',
            'consolidation_growth' => 'required|integer|min:0|max:3',
            'pastoral_score' => 'required|integer|min:0|max:3',
            'visitation_routine' => 'required|integer|min:0|max:3',
            'leader_support' => 'required|integer|min:0|max:3',
            'cell_participation_score' => 'required|integer|min:0|max:3',
            'service_participation_score' => 'required|integer|min:0|max:3',
            'tadium_participation' => 'required|integer|min:0|max:3',
            'communion_in_cells_score' => 'required|integer|min:0|max:3',
            'relationship_building_score' => 'required|integer|min:0|max:3',
            'prayer_intercession_score' => 'required|integer|min:0|max:3',
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
            $user = auth()->user();
            $supervisorId = $user->id;

            // If an Admin or Pastor de Zona is creating the report, get the real supervisor
            if ($user->role !== 'supervisor') {
                $supervision = Supervision::find($validated['supervision_id']);
                $supervisorId = $supervision->supervisor_id ?? $user->id;
            }

            $report = QuarterlyReport::create(array_merge($validated, [
                'supervisor_id' => $supervisorId,
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

        if ($user->role === 'admin' || $user->role === 'pastor' || $user->role === 'secretaria') {
            $zones = Zone::with('supervisions')->get();
            $supervisions = Supervision::all();
        } elseif ($user->role === 'pastor_zona') {
            $zoneIds = $user->getManagedZoneIds();
            $zones = Zone::whereIn('id', $zoneIds)->with('supervisions')->get();
            $supervisions = Supervision::whereIn('zone_id', $zoneIds)->get();
        } elseif ($user->role === 'supervisor') {
            $zoneId = $user->getZoneId();
            $zones = Zone::where('id', $zoneId)->get();
            $supervisions = Supervision::where('supervisor_id', $user->id)->get();
        }

        $eventTypes = EventType::where('is_active', true)->get();
        $report = $quarterlyReport;
        return view('quarterly_reports.edit', compact('report', 'zones', 'supervisions', 'eventTypes'));
    }

    public function update(Request $request, QuarterlyReport $quarterlyReport)
    {
        Gate::authorize('update', $quarterlyReport);

        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'supervision_id' => 'required|exists:supervisions,id',
            'year' => 'required|integer|min:2000|max:2100',
            'quarter' => 'required|integer|min:1|max:4',
            'leaders_count' => 'required|integer|min:0',
            'cells_count' => 'required|integer|min:0',
            'timoteos_count' => 'required|integer|min:0',
            'members_count' => 'required|integer|min:0',
            'participants_count' => 'required|integer|min:0',
            'pastors_count' => 'required|integer|min:0',
            'supervisors_count' => 'required|integer|min:0',
            'visitors_count' => 'required|integer|min:0',
            'saved_count' => 'required|integer|min:0',
            'planned_baptism_count' => 'required|integer|min:0',
            'baptized_count' => 'required|integer|min:0',
            'cell_multiplications_count' => 'required|integer|min:0',
            'disciplined_leaders_count' => 'required|integer|min:0',
            'closed_cells_count' => 'required|integer|min:0',
            'ministerial_observations' => 'nullable|string',
            'discipleship_score' => 'required|integer|min:0|max:3',
            'evangelism_strategy' => 'required|integer|min:0|max:3',
            'consolidation_growth' => 'required|integer|min:0|max:3',
            'pastoral_score' => 'required|integer|min:0|max:3',
            'visitation_routine' => 'required|integer|min:0|max:3',
            'leader_support' => 'required|integer|min:0|max:3',
            'cell_participation_score' => 'required|integer|min:0|max:3',
            'service_participation_score' => 'required|integer|min:0|max:3',
            'tadium_participation' => 'required|integer|min:0|max:3',
            'communion_in_cells_score' => 'required|integer|min:0|max:3',
            'relationship_building_score' => 'required|integer|min:0|max:3',
            'prayer_intercession_score' => 'required|integer|min:0|max:3',
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

    public function exportAnnual(Request $request)
    {
        $year = $request->input('year', date('Y'));

        return \Maatwebsite\Excel\Facades\Excel::download(
            new AnnualQuarterlyReportExport($year),
            "consolidado_anual_{$year}_" . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
