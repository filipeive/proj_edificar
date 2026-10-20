<?php

namespace App\Http\Controllers;

use App\Exports\AnnualQuarterlyReportExport;
use App\Models\EventType;
use App\Models\QuarterlyReport;
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

        // Determine accessible zones & supervisions for filter dropdowns
        if ($user->role === 'pastor_zona') {
            $zoneIds = $user->getManagedZoneIds();
            if ($zoneIds->isEmpty() && $user->getZoneId()) {
                $zoneIds = collect([$user->getZoneId()]);
            }
            $zones = Zone::whereIn('id', $zoneIds)->orderBy('name')->get();
            $supervisions = Supervision::with('zone')->whereIn('zone_id', $zoneIds)->orderBy('name')->get();
        } elseif ($user->role === 'supervisor') {
            $zoneId = $user->getZoneId();
            $zones = $zoneId ? Zone::where('id', $zoneId)->orderBy('name')->get() : collect();
            $supervisions = Supervision::with('zone')->where('supervisor_id', $user->id)->orderBy('name')->get();
        } else {
            $zones = Zone::orderBy('name')->get();
            $supervisions = Supervision::with('zone')->orderBy('name')->get();
        }

        // Reusable scope closure (role + zone + supervision + status + search)
        $applyScope = function ($q) use ($request, $user) {
            if ($user->role === 'pastor_zona') {
                $zoneIds = $user->getManagedZoneIds();
                if ($zoneIds->isNotEmpty()) {
                    $q->whereIn('zone_id', $zoneIds);
                } else {
                    $q->where('zone_id', $user->getZoneId());
                }
            } elseif ($user->role === 'supervisor') {
                $q->where('supervisor_id', $user->id);
            }

            if ($request->filled('zone_id')) {
                $q->where('zone_id', $request->zone_id);
            }
            if ($request->filled('supervision_id')) {
                $q->where('supervision_id', $request->supervision_id);
            }
            if ($request->filled('status')) {
                $q->where('status', $request->status);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('supervisor', fn ($sq) => $sq->where('name', 'LIKE', "%$search%"))
                        ->orWhereHas('supervision', fn ($spq) => $spq->where('name', 'LIKE', "%$search%"))
                        ->orWhereHas('zone', fn ($zq) => $zq->where('name', 'LIKE', "%$search%"));
                });
            }
        };

        // Main table query
        $query = QuarterlyReport::with(['zone', 'supervisor', 'supervision']);
        $applyScope($query);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('quarter')) {
            $query->where('quarter', $request->quarter);
        }

        $reports = $query->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Available periods for comparison selector
        $periodsQuery = QuarterlyReport::query()->select('year', 'quarter')->distinct();
        $applyScope($periodsQuery);
        $availablePeriods = $periodsQuery
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->get()
            ->map(fn ($p) => [
                'key' => "{$p->year}-{$p->quarter}",
                'year' => (int) $p->year,
                'quarter' => (int) $p->quarter,
                'label' => "{$p->quarter}º Trimestre / {$p->year}",
            ]);

        // Aggregated Analytics for Charts & Movements (Last 8 combinations of Year/Quarter in current scope)
        $healthFields = array_keys(QuarterlyReport::healthMetricsMap());
        $healthSumExpr = implode(' + ', array_map(fn ($f) => "COALESCE($f, 0)", $healthFields));

        $analyticsQuery = QuarterlyReport::query()
            ->select(
                'year',
                'quarter',
                DB::raw('COUNT(*) as reports_count'),
                DB::raw('SUM(members_count) as total_members'),
                DB::raw('SUM(cells_count) as total_cells'),
                DB::raw('SUM(saved_count) as total_saved'),
                DB::raw('SUM(baptized_count) as total_baptized'),
                DB::raw('SUM(planned_baptism_count) as total_planned_baptism'),
                DB::raw('SUM(visitors_count) as total_visitors'),
                DB::raw('SUM(participants_count) as total_participants'),
                DB::raw('SUM(leaders_count) as total_leaders'),
                DB::raw('SUM(timoteos_count) as total_timoteos'),
                DB::raw('SUM(pastors_count) as total_pastors'),
                DB::raw('SUM(supervisors_count) as total_supervisors'),
                DB::raw('SUM(cell_multiplications_count) as total_multiplications'),
                DB::raw('SUM(closed_cells_count) as total_closed_cells'),
                DB::raw('SUM(disciplined_leaders_count) as total_disciplined'),
                DB::raw("AVG(($healthSumExpr) / 36.0 * 100) as avg_health_pct")
            );
        $applyScope($analyticsQuery);

        $recentStats = $analyticsQuery->groupBy('year', 'quarter')
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->limit(8)
            ->get();

        // Prepare chart data (chronological order)
        $chronological = $recentStats->reverse()->values();
        $chartLabels = $chronological->map(fn ($s) => "Q{$s->quarter}/{$s->year}");
        $membersData = $chronological->pluck('total_members');
        $cellsData = $chronological->pluck('total_cells');
        $savedData = $chronological->pluck('total_saved');
        $baptizedData = $chronological->pluck('total_baptized');
        $multiplicationsData = $chronological->pluck('total_multiplications');
        $closedCellsData = $chronological->pluck('total_closed_cells');
        $visitorsData = $chronological->pluck('total_visitors');
        $healthTrendData = $chronological->map(fn ($s) => round((float) $s->avg_health_pct, 1));

        // Determine Reference Period and Comparison Period
        $refPeriod = null;
        if ($request->filled('year') && $request->filled('quarter')) {
            $refPeriod = ['year' => (int) $request->year, 'quarter' => (int) $request->quarter];
        } elseif ($request->filled('year')) {
            $firstInYear = $availablePeriods->firstWhere('year', (int) $request->year);
            $refPeriod = $firstInYear ?: ['year' => (int) $request->year, 'quarter' => 1];
        } elseif ($availablePeriods->isNotEmpty()) {
            $refPeriod = $availablePeriods->first();
        } else {
            $refPeriod = ['year' => (int) date('Y'), 'quarter' => (int) ceil(date('n') / 3)];
        }

        $compPeriod = null;
        if ($request->filled('compare_period') && str_contains($request->compare_period, '-')) {
            [$cYear, $cQuarter] = explode('-', $request->compare_period);
            $compPeriod = ['year' => (int) $cYear, 'quarter' => (int) $cQuarter];
        } else {
            // Find the period immediately before $refPeriod in $availablePeriods
            $prevAvailable = $availablePeriods->first(function ($p) use ($refPeriod) {
                return ($p['year'] < $refPeriod['year']) ||
                    ($p['year'] === $refPeriod['year'] && $p['quarter'] < $refPeriod['quarter']);
            });
            if ($prevAvailable) {
                $compPeriod = $prevAvailable;
            } else {
                $prevQ = $refPeriod['quarter'] > 1 ? $refPeriod['quarter'] - 1 : 4;
                $prevY = $refPeriod['quarter'] > 1 ? $refPeriod['year'] : $refPeriod['year'] - 1;
                $compPeriod = ['year' => $prevY, 'quarter' => $prevQ];
            }
        }

        // Helper to compute detailed stats (quantitative + 12 health scores) for a given (year, quarter)
        $buildPeriodSummary = function (int $year, int $quarter) use ($applyScope, $healthFields) {
            $q = QuarterlyReport::query()->where('year', $year)->where('quarter', $quarter);
            $applyScope($q);

            $selects = [
                DB::raw('COUNT(*) as reports_count'),
                DB::raw('COALESCE(SUM(members_count), 0) as members'),
                DB::raw('COALESCE(SUM(cells_count), 0) as cells'),
                DB::raw('COALESCE(SUM(saved_count), 0) as saved'),
                DB::raw('COALESCE(SUM(baptized_count), 0) as baptized'),
                DB::raw('COALESCE(SUM(planned_baptism_count), 0) as planned_baptism'),
                DB::raw('COALESCE(SUM(visitors_count), 0) as visitors'),
                DB::raw('COALESCE(SUM(participants_count), 0) as participants'),
                DB::raw('COALESCE(SUM(leaders_count), 0) as leaders'),
                DB::raw('COALESCE(SUM(timoteos_count), 0) as timoteos'),
                DB::raw('COALESCE(SUM(pastors_count), 0) as pastors'),
                DB::raw('COALESCE(SUM(supervisors_count), 0) as supervisors'),
                DB::raw('COALESCE(SUM(cell_multiplications_count), 0) as multiplications'),
                DB::raw('COALESCE(SUM(closed_cells_count), 0) as closed_cells'),
                DB::raw('COALESCE(SUM(disciplined_leaders_count), 0) as disciplined'),
            ];
            foreach ($healthFields as $field) {
                $selects[] = DB::raw("ROUND(COALESCE(AVG($field), 0), 2) as avg_{$field}");
            }

            $row = $q->select($selects)->first();

            $healthScores = [];
            $healthSum = 0;
            foreach ($healthFields as $field) {
                $val = (float) ($row->{"avg_{$field}"} ?? 0);
                $healthScores[$field] = round($val, 2);
                $healthSum += $val;
            }
            $healthAvg = round($healthSum / 12, 2);
            $healthPct = (int) round(($healthSum / 36) * 100);

            return [
                'year' => $year,
                'quarter' => $quarter,
                'label' => "{$quarter}º Trim / {$year}",
                'reports_count' => (int) ($row->reports_count ?? 0),
                'members' => (int) ($row->members ?? 0),
                'cells' => (int) ($row->cells ?? 0),
                'saved' => (int) ($row->saved ?? 0),
                'baptized' => (int) ($row->baptized ?? 0),
                'planned_baptism' => (int) ($row->planned_baptism ?? 0),
                'visitors' => (int) ($row->visitors ?? 0),
                'participants' => (int) ($row->participants ?? 0),
                'leaders' => (int) ($row->leaders ?? 0),
                'timoteos' => (int) ($row->timoteos ?? 0),
                'pastors' => (int) ($row->pastors ?? 0),
                'supervisors' => (int) ($row->supervisors ?? 0),
                'multiplications' => (int) ($row->multiplications ?? 0),
                'closed_cells' => (int) ($row->closed_cells ?? 0),
                'net_cells' => (int) ($row->multiplications ?? 0) - (int) ($row->closed_cells ?? 0),
                'disciplined' => (int) ($row->disciplined ?? 0),
                'health_avg' => $healthAvg,
                'health_pct' => $healthPct,
                'health_scores' => $healthScores,
            ];
        };

        $currentSummary = $buildPeriodSummary($refPeriod['year'], $refPeriod['quarter']);
        $compareSummary = $buildPeriodSummary($compPeriod['year'], $compPeriod['quarter']);

        // Summary Stats for Top KPI Cards
        $totalMembers = $currentSummary['members'];
        $totalCells = $currentSummary['cells'];
        $totalSaved = $request->filled('quarter') ? $currentSummary['saved'] : $recentStats->sum('total_saved');
        $totalBaptized = $request->filled('quarter') ? $currentSummary['baptized'] : $recentStats->sum('total_baptized');

        $healthMetricsMap = QuarterlyReport::healthMetricsMap();

        return view('quarterly_reports.index', compact(
            'reports',
            'zones',
            'supervisions',
            'availablePeriods',
            'chartLabels',
            'membersData',
            'cellsData',
            'savedData',
            'baptizedData',
            'multiplicationsData',
            'closedCellsData',
            'visitorsData',
            'healthTrendData',
            'totalMembers',
            'totalCells',
            'totalSaved',
            'totalBaptized',
            'currentSummary',
            'compareSummary',
            'healthMetricsMap'
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

        return back()->with('success', count($ids).' relatórios excluídos com sucesso.');
    }

    public function create()
    {
        Gate::authorize('create', QuarterlyReport::class);

        $user = auth()->user();
        $zones = collect();
        $supervisions = collect();

        // Lógica de seleção baseada no cargo
        if ($user->isAdmin() || $user->role === 'pastor' || $user->role === 'secretaria') {
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

        \App\Http\Controllers\PublicFormController::normalizeNumericInput($request);

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

        $quarterlyReport->load(['zone', 'supervision', 'supervisor', 'events.eventType']);

        $previousReport = QuarterlyReport::where('supervision_id', $quarterlyReport->supervision_id)
            ->where(function ($q) use ($quarterlyReport) {
                $q->where('year', '<', $quarterlyReport->year)
                    ->orWhere(function ($sq) use ($quarterlyReport) {
                        $sq->where('year', $quarterlyReport->year)
                            ->where('quarter', '<', $quarterlyReport->quarter);
                    });
            })
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->first();

        $supervisionReports = QuarterlyReport::where('supervision_id', $quarterlyReport->supervision_id)
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->limit(8)
            ->get();

        return view('quarterly_reports.show', compact('quarterlyReport', 'previousReport', 'supervisionReports'));
    }

    public function edit(QuarterlyReport $quarterlyReport)
    {
        Gate::authorize('update', $quarterlyReport);

        $user = auth()->user();
        $zones = collect();
        $supervisions = collect();

        if ($user->isAdmin() || $user->role === 'pastor' || $user->role === 'secretaria') {
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

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\QuarterlyReportExport(
                $request->year,
                $request->quarter,
                $request->zone_id,
                $request->supervision_id,
                $request->search
            ),
            'relatorios_trimestrais_'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function exportAnnual(Request $request)
    {
        $year = $request->input('year', date('Y'));

        return \Maatwebsite\Excel\Facades\Excel::download(
            new AnnualQuarterlyReportExport($year),
            "consolidado_anual_{$year}_".now()->format('Y-m-d').'.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $query = QuarterlyReport::with(['zone', 'supervision', 'supervisor', 'events.eventType']);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('quarter')) {
            $query->where('quarter', $request->quarter);
        }
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }
        if ($request->filled('supervision_id')) {
            $query->where('supervision_id', $request->supervision_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($sub) use ($search) {
                $sub->whereHas('supervisor', fn ($sq) => $sq->where('name', 'LIKE', "%$search%"))
                    ->orWhereHas('supervision', fn ($spq) => $spq->where('name', 'LIKE', "%$search%"))
                    ->orWhereHas('zone', fn ($zq) => $zq->where('name', 'LIKE', "%$search%"));
            });
        }

        if ($user->role === 'pastor_zona') {
            $zoneIds = $user->getManagedZoneIds();
            if ($zoneIds->isNotEmpty()) {
                $query->whereIn('zone_id', $zoneIds);
            } else {
                $query->where('zone_id', $user->getZoneId());
            }
        } elseif ($user->role === 'supervisor') {
            $query->where('supervisor_id', $user->id);
        }

        $reports = $query->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->get();

        $title = 'Relatório Trimestral';
        if ($request->filled('year')) {
            $title .= ' - '.$request->year;
        }
        if ($request->filled('quarter')) {
            $title .= ' / '.$request->quarter.'º Trimestre';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quarterly_reports.pdf', compact('reports', 'title', 'request'));

        return $pdf->download('relatorio_trimestral_'.now()->format('Y-m-d').'.pdf');
    }
}
