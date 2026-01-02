<?php

namespace App\Http\Controllers\Report;

use App\Models\Contribution;
use App\Models\Supervision;
use App\Models\Zone;
use App\Models\Cell;
use App\Models\UserCommitment;
use App\Exports\CellReportExport;
use App\Exports\SupervisionReportExport;
use App\Exports\ZoneReportExport;
use App\Exports\GlobalReportExport;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;

class ReportController
{
    use AuthorizesRequests;
    public function cellReport(Request $request): View
    {
        $user = auth()->user();
        $cells = collect();
        $selectedCell = null;

        // Se for admin, pode ver todas as células. Se não, só a sua.
        if ($user->role === 'admin') {
            $cells = Cell::orderBy('name')->get();
            if ($request->filled('cell_id')) {
                $selectedCell = Cell::findOrFail($request->cell_id);
            }
        } else {
            $selectedCell = $user->cell;
            if (!$selectedCell)
                abort(403, 'Você não está associado a uma célula.');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth()->addDays(19);
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->addMonth()->startOfMonth()->addDays(4);

        $contributions = collect();
        if ($selectedCell) {
            $contributions = $selectedCell->contributions()
                ->whereBetween('contribution_date', [$startDate, $endDate])
                ->with('user')
                ->orderBy('contribution_date', 'desc')
                ->get();
        }

        return view('reports.cell', [
            'cell' => $selectedCell,
            'contributions' => $contributions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total' => $contributions->where('status', 'verificada')->sum('amount'),
            'allCells' => $cells, // Para o dropdown do admin
        ]);
    }

    /**
     * Exibe o relatório de contribuições e compromissos para a Supervisão.
     */
    public function supervisionReport(Request $request): View
    {
        $user = auth()->user();
        $allSupervisions = collect();
        $selectedSupervision = null;

        // 1. Verificar permissão
        //$this->authorize('viewSupervisionReport', Contribution::class);

        // 2. Obter o escopo disponível
        if ($user->role === 'admin') {
            $allSupervisions = Supervision::with('zone')->orderBy('name')->get();
            if ($request->filled('supervision_id')) {
                $selectedSupervision = Supervision::findOrFail($request->supervision_id);
            } elseif ($allSupervisions->isNotEmpty()) {
                $selectedSupervision = $allSupervisions->first();
            }
        } elseif ($user->role === 'pastor_zona') {
            // Pastor de zona: vê todas as supervisões da sua zona
            $zone = $user->cell->supervision->zone ?? null;
            if (!$zone)
                abort(403, 'Sua conta não está associada a uma Zona válida.');

            $allSupervisions = $zone->supervisions()->orderBy('name')->get();
            if ($request->filled('supervision_id')) {
                $selectedSupervision = $allSupervisions->firstWhere('id', $request->supervision_id);
                if (!$selectedSupervision)
                    abort(403, 'Supervisão inválida para sua zona.');
            } elseif ($allSupervisions->isNotEmpty()) {
                $selectedSupervision = $allSupervisions->first();
            }
        } elseif ($user->role === 'supervisor') {
            // Supervisor: só vê a sua supervisão
            $selectedSupervision = $user->cell->supervision ?? null;
            if (!$selectedSupervision)
                abort(403, 'Sua conta não está associada a uma Supervisão válida.');
        } else {
            // Caso não seja Admin, Pastor ou Supervisor (Líder/Membro), já deveria ter abortado
            abort(403, 'Acesso negado.');
        }

        if (!$selectedSupervision)
            abort(403, 'Nenhuma supervisão encontrada para este perfil.');

        // O restante da lógica de agregação permanece a mesma
        $contributions = collect();
        $cellsData = collect();
        $totalCommitted = 0;

        $cellIds = $selectedSupervision->cells()->pluck('id');

        // Obter Contribuições Agregadas (por mês e por célula)
        $contributions = Contribution::whereIn('cell_id', $cellIds)
            ->selectRaw('YEAR(contribution_date) as year, MONTH(contribution_date) as month, status, SUM(amount) as total_amount')
            ->groupBy('year', 'month', 'status')
            ->orderBy('year', 'desc')->orderBy('month', 'desc')
            ->get();

        $contributionsByCell = Contribution::whereIn('cell_id', $cellIds)
            ->selectRaw('cell_id, status, SUM(amount) as total_amount')
            ->groupBy('cell_id', 'status')
            ->get()
            ->groupBy('cell_id');

        // Obter Total Comprometido (Metas Agregadas)
        $totalCommitted = UserCommitment::whereIn('cell_id', $cellIds)
            ->where('start_date', '<=', now())
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>', now()))
            ->sum('committed_amount');

        // Preparar dados de Células
        $cellsData = $selectedSupervision->cells()->with('leader')->get()->map(function ($cell) use ($contributionsByCell) {
            $cellCommitted = UserCommitment::where('cell_id', $cell->id)
                ->where('start_date', '<=', now())
                ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>', now()))
                ->sum('committed_amount');

            $data = [
                'name' => $cell->name,
                'lider' => $cell->leader->name ?? 'N/A',
                'committed' => $cellCommitted,
                'total_contributed' => 0,
                'verified' => 0,
                'pending' => 0,
            ];

            if ($contributionsByCell->has($cell->id)) {
                foreach ($contributionsByCell[$cell->id] as $contribution) {
                    $data['total_contributed'] += $contribution->total_amount;
                    if ($contribution->status === 'verificada') {
                        $data['verified'] += $contribution->total_amount;
                    } elseif ($contribution->status === 'pendente') {
                        $data['pending'] += $contribution->total_amount;
                    }
                }
            }
            return $data;
        });

        return view('reports.supervision', [
            'supervision' => $selectedSupervision,
            'allSupervisions' => $allSupervisions,
            'totalCells' => $selectedSupervision->cells->count(),
            'totalCommitted' => $totalCommitted,
            'cellsData' => $cellsData,
            'contributions' => $contributions,
        ]);
    }

    public function zoneReport(Request $request): View
    {
        $user = auth()->user();
        $zones = collect();
        $selectedZone = null;

        // 1. Verificar permissão
        //$this->authorize('viewZoneReport', Contribution::class);

        // 2. Obter a zona selecionada com base no perfil e no request
        if ($user->role === 'admin') {
            $zones = Zone::orderBy('name')->get();
            if ($request->filled('zone_id')) {
                $selectedZone = Zone::findOrFail($request->zone_id);
            } elseif ($zones->isNotEmpty()) {
                $selectedZone = $zones->first();
            }
        } elseif ($user->role === 'pastor_zona') {
            // Pastor de Zona: vê apenas a sua zona
            $selectedZone = $user->cell->supervision->zone ?? null;
            if (!$selectedZone)
                abort(403, 'Sua conta não está associada a uma Zona válida.');
        } else {
            abort(403, 'Acesso negado para relatórios de Zona.');
        }

        if (!$selectedZone)
            abort(403, 'Nenhuma zona encontrada para este perfil.');


        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth()->addDays(19);
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->addMonth()->startOfMonth()->addDays(4);

        $contributions = collect();
        if ($selectedZone) {
            $contributions = $selectedZone->contributions()
                ->whereBetween('contribution_date', [$startDate, $endDate])
                ->with(['user.cell', 'user.cell.supervision', 'supervision.zone'])
                ->orderBy('contribution_date', 'desc')
                ->get();
        }

        return view('reports.zone', [
            'zone' => $selectedZone,
            'contributions' => $contributions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total' => $contributions->where('status', 'verificada')->sum('amount'),
            'allZones' => $zones,
        ]);
    }

    public function globalReport(Request $request): View
    {
        // Validação básica das datas para evitar erros
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'zone_id' => 'nullable|exists:zones,id',
            'status' => 'nullable|in:verificada,pendente,rejeitada',
        ]);

        // Define as datas com os valores do request ou os padrões
        $startDate = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date) : now()->startOfMonth()->addDays(19);
        $endDate = $request->filled('end_date') ? \Carbon\Carbon::parse($request->end_date) : now()->addMonth()->startOfMonth()->addDays(4);

        // Construtor de Query para as contribuições
        $contributionsQuery = Contribution::with(['user.cell', 'zone'])
            ->whereBetween('contribution_date', [$startDate, $endDate]);

        // Aplica os filtros opcionais
        if ($request->filled('zone_id')) {
            $contributionsQuery->where('zone_id', $request->zone_id);
        }
        if ($request->filled('status')) {
            $contributionsQuery->where('status', $request->status);
        }

        // Executa a query para obter as contribuições
        $contributions = $contributionsQuery->orderBy('contribution_date', 'desc')->get();

        // Calcula o total apenas das contribuições verificadas dentro do filtro
        $total = $contributions->where('status', 'verificada')->sum('amount');

        // Obtém as zonas para o filtro e para as estatísticas
        $allZones = Zone::orderBy('name')->get();
        $zoneStats = [];

        // Define quais zonas aparecerão nas estatísticas
        $zonesForStats = $request->filled('zone_id')
            ? $allZones->where('id', $request->zone_id)
            : $allZones;

        foreach ($zonesForStats as $zone) {
            // Filtra a coleção já carregada para evitar novas queries ao banco de dados
            $zoneTotal = $contributions
                ->where('zone_id', $zone->id)
                ->where('status', 'verificada')
                ->sum('amount');

            $zoneStats[] = [
                'name' => $zone->name,
                'total' => $zoneTotal,
            ];
        }

        return view('reports.global', [
            'contributions' => $contributions,
            'total' => $total,
            'zoneStats' => collect($zoneStats)->sortByDesc('total'),
            'allZones' => $allZones, // Para popular o dropdown
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $type = $request->input('type', 'cell');
        $startDate = $request->input('start_date', now()->startOfMonth()->addDays(19));
        $endDate = $request->input('end_date', now()->addMonth()->startOfMonth()->addDays(4));

        $user = auth()->user();

        return match ($type) {
            'cell' => Excel::download(
                new \App\Exports\CellReportExport(
                    $user->cell,
                    $startDate,
                    $endDate
                ),
                "relatorio_celula_" . now()->format('Y-m-d') . '.xlsx'
            ),
            'supervision' => Excel::download(
                new \App\Exports\SupervisionReportExport(
                    $user->cell->supervision,
                    $startDate,
                    $endDate
                ),
                "relatorio_supervisao_" . now()->format('Y-m-d') . '.xlsx'
            ),
            'zone' => Excel::download(
                new \App\Exports\ZoneReportExport(
                    $user->cell->supervision->zone,
                    $startDate,
                    $endDate
                ),
                "relatorio_zona_" . now()->format('Y-m-d') . '.xlsx'
            ),
            'global' => Excel::download(
                new \App\Exports\GlobalReportExport($startDate, $endDate),
                "relatorio_global_" . now()->format('Y-m-d') . '.xlsx'
            ),
            'structure' => Excel::download(
                new \App\Exports\ChurchStructureExport(),
                "estrutura_igreja_" . now()->format('Y-m-d') . '.xlsx'
            ),
            default => abort(400, 'Tipo de relatório inválido'),
        };
    }

    public function exportPdf(Request $request)
    {
        $type = $request->input('type', 'cell');
        $id = $request->input('id');
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->addDays(19)));
        $endDate = Carbon::parse($request->input('end_date', now()->addMonth()->startOfMonth()->addDays(4)));

        switch ($type) {
            case 'cell':
                $cell = Cell::findOrFail($id);
                $contributions = $cell->contributions()->whereBetween('contribution_date', [$startDate, $endDate])->with('user')->get();
                $pdf = Pdf::loadView('reports.pdf.cell', compact('cell', 'contributions', 'startDate', 'endDate'));
                return $pdf->download("relatorio_celula_{$cell->name}_" . now()->format('Y-m-d') . '.pdf');

            case 'supervision':
                $supervision = Supervision::findOrFail($id);
                $contributions = $supervision->contributions()->whereBetween('contribution_date', [$startDate, $endDate])->with(['user', 'cell'])->get();
                $pdf = Pdf::loadView('reports.pdf.supervision', compact('supervision', 'contributions', 'startDate', 'endDate'));
                return $pdf->download("relatorio_supervisao_{$supervision->name}_" . now()->format('Y-m-d') . '.pdf');

            case 'zone':
                $zone = Zone::findOrFail($id);
                $contributions = $zone->contributions()->whereBetween('contribution_date', [$startDate, $endDate])->with(['user', 'cell', 'supervision'])->get();
                $pdf = Pdf::loadView('reports.pdf.zone', compact('zone', 'contributions', 'startDate', 'endDate'));
                return $pdf->download("relatorio_zona_{$zone->name}_" . now()->format('Y-m-d') . '.pdf');

            case 'global':
                $query = Contribution::whereBetween('contribution_date', [$startDate, $endDate])->with(['user', 'cell', 'supervision', 'zone']);
                if ($request->filled('zone_id'))
                    $query->where('zone_id', $request->zone_id);
                if ($request->filled('status'))
                    $query->where('status', $request->status);
                $contributions = $query->get();
                $pdf = Pdf::loadView('reports.pdf.global', compact('contributions', 'startDate', 'endDate'));
                return $pdf->download("relatorio_global_" . now()->format('Y-m-d') . '.pdf');

            default:
                abort(400, 'Tipo de relatório inválido');
        }
    }
}
