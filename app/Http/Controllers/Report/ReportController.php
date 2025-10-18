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
use Maatwebsite\Excel\Facades\Excel;

class ReportController
{
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
            if (!$selectedCell) abort(403, 'Você não está associado a uma célula.');
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

    /* public function supervisionReport(Request $request): View
    {
        $user = auth()->user();
        $supervisions = collect();
        $selectedSupervision = null;

        if ($user->role === 'admin') {
            $supervisions = Supervision::orderBy('name')->get();
            if ($request->filled('supervision_id')) {
                $selectedSupervision = Supervision::findOrFail($request->supervision_id);
            }
        } else {
            $selectedSupervision = $user->cell?->supervision;
            if (!$selectedSupervision) abort(403, 'Acesso negado.');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth()->addDays(19);
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->addMonth()->startOfMonth()->addDays(4);

        $contributions = collect();
        if ($selectedSupervision) {
            $contributions = $selectedSupervision->contributions()
                ->whereBetween('contribution_date', [$startDate, $endDate])
                ->with('user.cell')
                ->orderBy('contribution_date', 'desc')
                ->get();
        }
        
        return view('reports.supervision', [
            'supervision' => $selectedSupervision,
            'contributions' => $contributions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total' => $contributions->where('status', 'verificada')->sum('amount'),
            'allSupervisions' => $supervisions,
        ]);
    }
 */
    /**
     * Exibe o relatório de contribuições e compromissos para a Supervisão.
     */
    public function supervisionReport(Request $request): View
    {
        $user = auth()->user();

        // 1. Verificar permissão e hierarquia (mantido)
        if (!in_array($user->role, ['supervisor', 'pastor_zona', 'admin'])) {
            if ($user->role === 'lider_celula') {
                return redirect()->route('reports.cell');
            }
            abort(403, 'Você não tem permissão para ver relatórios de Supervisão.');
        }

        if (!$user->cell || !$user->cell->supervision) {
            return redirect()->route('dashboard')->with('error', 'Sua conta não está associada a uma Supervisão válida.');
        }

        $supervision = $user->cell->supervision;
        // Pega todas as Células que pertencem a esta Supervisão
        $cellIds = $supervision->cells()->pluck('id');

        // 2. Obter Contribuições Agregadas (por mês e por célula)
        $contributions = Contribution::whereIn('cell_id', $cellIds)
            ->selectRaw('YEAR(contribution_date) as year, MONTH(contribution_date) as month, status, SUM(amount) as total_amount')
            ->groupBy('year', 'month', 'status')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $contributionsByCell = Contribution::whereIn('cell_id', $cellIds)
            ->selectRaw('cell_id, status, SUM(amount) as total_amount')
            ->groupBy('cell_id', 'status')
            ->with('cell')
            ->get()
            ->groupBy('cell_id');

        // 3. Obter Total Comprometido (Metas Agregadas)
        // ASSUMIMOS que a coluna `committed_amount` existe em `user_commitments`
        // Filtramos apenas por compromissos ativos (data de início <= hoje E data de fim nula ou futura)
        $totalCommitted = UserCommitment::query()
            ->whereIn('cell_id', $cellIds)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->sum('committed_amount'); // <-- Campo 'committed_amount' assumido

        // 4. Preparação dos Dados para a View
        $totalCells = $supervision->cells->count();

        // Preparar dados de Células
        $cellsData = $supervision->cells->map(function ($cell) use ($contributionsByCell) {

            // Calcular o total comprometido por esta célula
            $cellCommitted = UserCommitment::query()
                ->where('cell_id', $cell->id)
                ->where('start_date', '<=', now())
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>', now());
                })
                ->sum('committed_amount'); // <-- Campo 'committed_amount' assumido

            $data = [
                'name' => $cell->name,
                'lider' => $cell->lider->name ?? 'N/A',
                'committed' => $cellCommitted ?? 0,
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
            'supervision' => $supervision,
            'totalCells' => $totalCells,
            'totalCommitted' => $totalCommitted ?? 0,
            'cellsData' => $cellsData,
            'contributions' => $contributions,
        ]);
    }

    public function zoneReport(Request $request): View
    {
        $user = auth()->user();
        $zones = collect();
        $selectedZone = null;

        if ($user->role === 'admin') {
            $zones = Zone::orderBy('name')->get();
            if ($request->filled('zone_id')) {
                $selectedZone = Zone::findOrFail($request->zone_id);
            }
        } else {
            $selectedZone = $user->cell?->supervision?->zone;
            if (!$selectedZone) abort(403, 'Acesso negado.');
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth()->addDays(19);
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->addMonth()->startOfMonth()->addDays(4);

        $contributions = collect();
        if ($selectedZone) {
            $contributions = $selectedZone->contributions()
                ->whereBetween('contribution_date', [$startDate, $endDate])
                ->with(['user.cell', 'user.cell.supervision'])
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

    /**
     * Gera um HTML para o relatório da célula.
     *
     * @param Cell $cell A célula para a qual o relatório será gerado.
     * @param Collection $contributions As contribuições para a célula.
     * @param DateTime $startDate A data de início do período.
     * @param DateTime $endDate A data de fim do período.
     *
     * @return string O HTML do relatório.
     */
    private function generateCellPdfHtml($cell, $contributions, $startDate, $endDate)
    {
        $total = $contributions->where('status', 'verificada')->sum('amount');

        $html = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #1F2937; border-bottom: 3px solid #2563EB; padding-bottom: 10px; }
                h2 { color: #374151; margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background-color: #F3F4F6; border: 1px solid #D1D5DB; padding: 10px; text-align: left; }
                td { border: 1px solid #D1D5DB; padding: 8px; }
                .header { text-align: center; margin-bottom: 20px; }
                .summary { background-color: #F0F9FF; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .total { font-weight: bold; color: #16A34A; font-size: 18px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Relatório da Célula</h1>
                <p><strong>{$cell->name}</strong></p>
            </div>

            <div class='summary'>
                <p>Período: <strong>" . (new \DateTime($startDate))->format('d/m/Y') . " - " . (new \DateTime($endDate))->format('d/m/Y') . "</strong></p>
                <p>Total Arrecadado: <span class='total'>" . number_format($total, 2, ',', '.') . " MT</span></p>
                <p>Total de Contribuições: <strong>{$contributions->count()}</strong></p>
            </div>

            <h2>Detalhes das Contribuições</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Membro</th>
                        <th>Valor (MT)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($contributions as $contribution) {
            $status = $contribution->status === 'verificada' ? '✓ Verificada' : ($contribution->status === 'pendente' ? '⏱ Pendente' : '✗ Rejeitada');
            $html .= "
                    <tr>
                        <td>{$contribution->contribution_date->format('d/m/Y')}</td>
                        <td>{$contribution->user->name}</td>
                        <td>" . number_format($contribution->amount, 2, ',', '.') . "</td>
                        <td>{$status}</td>
                    </tr>";
        }

        $html .= "
                </tbody>
            </table>

            <div class='summary'>
                <p><strong>TOTAL:</strong> <span class='total'>" . number_format($total, 2, ',', '.') . " MT</span></p>
            </div>

            <p style='text-align: center; color: #6B7280; font-size: 12px; margin-top: 30px;'>
                Gerado em " . now()->format('d/m/Y H:i:s') . " - Projeto Edificar
            </p>
        </body>
        </html>";

        return $html;
    }

    private function generateSupervisionPdfHtml($supervision, $contributions, $startDate, $endDate)
    {
        $total = $contributions->where('status', 'verificada')->sum('amount');

        $html = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #1F2937; border-bottom: 3px solid #2563EB; padding-bottom: 10px; }
                h2 { color: #374151; margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background-color: #F3F4F6; border: 1px solid #D1D5DB; padding: 10px; text-align: left; }
                td { border: 1px solid #D1D5DB; padding: 8px; }
                .header { text-align: center; margin-bottom: 20px; }
                .summary { background-color: #F0F9FF; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .total { font-weight: bold; color: #16A34A; font-size: 18px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Relatório da Supervisão</h1>
                <p><strong>{$supervision->name}</strong></p>
            </div>

            <div class='summary'>
                <p>Período: <strong>" . (new \DateTime($startDate))->format('d/m/Y') . " - " . (new \DateTime($endDate))->format('d/m/Y') . "</strong></p>
                <p>Total Arrecadado: <span class='total'>" . number_format($total, 2, ',', '.') . " MT</span></p>
                <p>Total de Contribuições: <strong>{$contributions->count()}</strong></p>
            </div>

            <h2>Detalhes das Contribuições</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Membro</th>
                        <th>Célula</th>
                        <th>Valor (MT)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($contributions as $contribution) {
            $status = $contribution->status === 'verificada' ? '✓ Verificada' : ($contribution->status === 'pendente' ? '⏱ Pendente' : '✗ Rejeitada');
            $html .= "
                    <tr>
                        <td>{$contribution->contribution_date->format('d/m/Y')}</td>
                        <td>{$contribution->user->name}</td>
                        <td>{$contribution->cell->name}</td>
                        <td>" . number_format($contribution->amount, 2, ',', '.') . "</td>
                        <td>{$status}</td>
                    </tr>";
        }

        $html .= "
                </tbody>
            </table>

            <div class='summary'>
                <p><strong>TOTAL:</strong> <span class='total'>" . number_format($total, 2, ',', '.') . " MT</span></p>
            </div>

            <p style='text-align: center; color: #6B7280; font-size: 12px; margin-top: 30px;'>
                Gerado em " . now()->format('d/m/Y H:i:s') . " - Projeto Edificar
            </p>
        </body>
        </html>";

        return $html;
    }

    private function generateZonePdfHtml($zone, $contributions, $startDate, $endDate)
    {
        $total = $contributions->where('status', 'verificada')->sum('amount');

        $html = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #1F2937; border-bottom: 3px solid #2563EB; padding-bottom: 10px; }
                h2 { color: #374151; margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background-color: #F3F4F6; border: 1px solid #D1D5DB; padding: 10px; text-align: left; }
                td { border: 1px solid #D1D5DB; padding: 8px; }
                .header { text-align: center; margin-bottom: 20px; }
                .summary { background-color: #F0F9FF; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .total { font-weight: bold; color: #16A34A; font-size: 18px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Relatório da Zona</h1>
                <p><strong>{$zone->name}</strong></p>
            </div>

            <div class='summary'>
                <p>Período: <strong>" . (new \DateTime($startDate))->format('d/m/Y') . " - " . (new \DateTime($endDate))->format('d/m/Y') . "</strong></p>
                <p>Total Arrecadado: <span class='total'>" . number_format($total, 2, ',', '.') . " MT</span></p>
                <p>Total de Contribuições: <strong>{$contributions->count()}</strong></p>
            </div>

            <h2>Detalhes das Contribuições</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Membro</th>
                        <th>Supervisão</th>
                        <th>Célula</th>
                        <th>Valor (MT)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($contributions as $contribution) {
            $status = $contribution->status === 'verificada' ? '✓' : ($contribution->status === 'pendente' ? '⏱' : '✗');
            $html .= "
                    <tr>
                        <td>{$contribution->contribution_date->format('d/m/Y')}</td>
                        <td>{$contribution->user->name}</td>
                        <td>{$contribution->supervision->name}</td>
                        <td>{$contribution->cell->name}</td>
                        <td>" . number_format($contribution->amount, 2, ',', '.') . "</td>
                        <td>{$status}</td>
                    </tr>";
        }

        $html .= "
                </tbody>
            </table>

            <div class='summary'>
                <p><strong>TOTAL:</strong> <span class='total'>" . number_format($total, 2, ',', '.') . " MT</span></p>
            </div>

            <p style='text-align: center; color: #6B7280; font-size: 12px; margin-top: 30px;'>
                Gerado em " . now()->format('d/m/Y H:i:s') . " - Projeto Edificar
            </p>
        </body>
        </html>";

        return $html;
    }

    private function generateGlobalPdfHtml($contributions, $startDate, $endDate)
    {
        $total = $contributions->where('status', 'verificada')->sum('amount');

        $html = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #1F2937; border-bottom: 3px solid #2563EB; padding-bottom: 10px; }
                h2 { color: #374151; margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 11px; }
                th { background-color: #F3F4F6; border: 1px solid #D1D5DB; padding: 8px; text-align: left; }
                td { border: 1px solid #D1D5DB; padding: 6px; }
                .header { text-align: center; margin-bottom: 20px; }
                .summary { background-color: #F0F9FF; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .total { font-weight: bold; color: #16A34A; font-size: 18px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Relatório Global - Projeto Edificar</h1>
            </div>

            <div class='summary'>
                <p>Período: <strong>" . (new \DateTime($startDate))->format('d/m/Y') . " - " . (new \DateTime($endDate))->format('d/m/Y') . "</strong></p>
                <p>Total Arrecadado: <span class='total'>" . number_format($total, 2, ',', '.') . " MT</span></p>
                <p>Total de Contribuições: <strong>{$contributions->count()}</strong></p>
            </div>

            <h2>Detalhes de Todas as Contribuições</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Membro</th>
                        <th>Zona</th>
                        <th>Supervisão</th>
                        <th>Célula</th>
                        <th>Valor (MT)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($contributions as $contribution) {
            $status = $contribution->status === 'verificada' ? '✓' : ($contribution->status === 'pendente' ? '⏱' : '✗');
            $html .= "
                    <tr>
                        <td>{$contribution->contribution_date->format('d/m/Y')}</td>
                        <td>{$contribution->user->name}</td>
                        <td>{$contribution->zone->name}</td>
                        <td>{$contribution->supervision->name}</td>
                        <td>{$contribution->cell->name}</td>
                        <td>" . number_format($contribution->amount, 2, ',', '.') . "</td>
                        <td>{$status}</td>
                    </tr>";
        }

        $html .= "
                </tbody>
            </table>

            <div class='summary'>
                <p><strong>TOTAL GERAL:</strong> <span class='total'>" . number_format($total, 2, ',', '.') . " MT</span></p>
            </div>

            <p style='text-align: center; color: #6B7280; font-size: 12px; margin-top: 30px;'>
                Gerado em " . now()->format('d/m/Y H:i:s') . " - Projeto Edificar
            </p>
        </body>
        </html>";

        return $html;
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
            default => abort(400, 'Tipo de relatório inválido'),
        };
    }
}
