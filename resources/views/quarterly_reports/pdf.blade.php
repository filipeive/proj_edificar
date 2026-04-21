@extends('reports.pdf.layout')

@section('title', $title)
@section('report_type', $title)

@section('content')
    @php
        $totalPastors = $reports->sum('pastors_count');
        $totalSupervisors = $reports->sum('supervisors_count');
        $totalLeaders = $reports->sum('leaders_count');
        $totalTimoteos = $reports->sum('timoteos_count');
        $totalMembers = $reports->sum('members_count');
        $totalVisitors = $reports->sum('visitors_count');
        $totalCells = $reports->sum('cells_count');
        $totalParticipants = $reports->sum('participants_count');
        $totalSaved = $reports->sum('saved_count');
        $totalPlannedBaptism = $reports->sum('planned_baptism_count');
        $totalBaptized = $reports->sum('baptized_count');
        $totalMultiplications = $reports->sum('cell_multiplications_count');
        $totalDisciplinedLeaders = $reports->sum('disciplined_leaders_count');
        $totalClosedCells = $reports->sum('closed_cells_count');

        $avgDiscipleship = $reports->avg('discipleship_score');
        $avgEvangelism = $reports->avg('evangelism_strategy');
        $avgConsolidation = $reports->avg('consolidation_growth');
        $avgPastoral = $reports->avg('pastoral_score');
        $avgVisitation = $reports->avg('visitation_routine');
        $avgLeaderSupport = $reports->avg('leader_support');
        $avgCellPart = $reports->avg('cell_participation_score');
        $avgServicePart = $reports->avg('service_participation_score');
        $avgTadium = $reports->avg('tadium_participation');
        $avgCommunion = $reports->avg('communion_in_cells_score');
        $avgRelationship = $reports->avg('relationship_building_score');
        $avgPrayer = $reports->avg('prayer_intercession_score');

        $maxIndicator = 3;
    @endphp

    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ $reports->count() }}</div>
                    <div class="stats-label">Supervisões</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ number_format($totalMembers) }}</div>
                    <div class="stats-label">Membros</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ number_format($totalCells) }}</div>
                    <div class="stats-label">Células</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-blue-600">{{ number_format($totalSaved) }}</div>
                    <div class="stats-label">Salvações</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-green-600">{{ number_format($totalBaptized) }}</div>
                    <div class="stats-label">Batizados</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ $totalLeaders }}</div>
                    <div class="stats-label">Líderes</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ $totalTimoteos }}</div>
                    <div class="stats-label">Auxiliares</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ $totalVisitors }}</div>
                    <div class="stats-label">Visitantes</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ $totalParticipants }}</div>
                    <div class="stats-label">Participantes</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-orange-600">{{ $totalMultiplications }}</div>
                    <div class="stats-label">Multiplicações</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Indicadores de Saúde Pastoral</div>
    <div class="stats-box">
        <table class="indicators-grid">
            <tr>
                <td class="indicator-item">
                    <div class="indicator-label">Discipulado</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgDiscipleship / $maxIndicator) * 100 }}%; background-color: #3b82f6;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgDiscipleship, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Estratégia Evangelismo</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgEvangelism / $maxIndicator) * 100 }}%; background-color: #8b5cf6;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgEvangelism, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Consolidação</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgConsolidation / $maxIndicator) * 100 }}%; background-color: #10b981;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgConsolidation, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Cuidado Pastoral</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgPastoral / $maxIndicator) * 100 }}%; background-color: #ef4444;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgPastoral, 1) }}/3</div>
                </td>
            </tr>
            <tr>
                <td class="indicator-item">
                    <div class="indicator-label">Visitação</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgVisitation / $maxIndicator) * 100 }}%; background-color: #f97316;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgVisitation, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Apoio Líderes</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgLeaderSupport / $maxIndicator) * 100 }}%; background-color: #ec4899;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgLeaderSupport, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Participação Células</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgCellPart / $maxIndicator) * 100 }}%; background-color: #14b8a6;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgCellPart, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Participação Cultos</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgServicePart / $maxIndicator) * 100 }}%; background-color: #6366f1;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgServicePart, 1) }}/3</div>
                </td>
            </tr>
            <tr>
                <td class="indicator-item">
                    <div class="indicator-label">TADEL</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgTadium / $maxIndicator) * 100 }}%; background-color: #a855f7;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgTadium, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Comunhão</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgCommunion / $maxIndicator) * 100 }}%; background-color: #22c55e;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgCommunion, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Integração</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgRelationship / $maxIndicator) * 100 }}%; background-color: #06b6d4;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgRelationship, 1) }}/3</div>
                </td>
                <td class="indicator-item">
                    <div class="indicator-label">Oração</div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ ($avgPrayer / $maxIndicator) * 100 }}%; background-color: #f59e0b;"></div>
                    </div>
                    <div class="indicator-value">{{ number_format($avgPrayer, 1) }}/3</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Relatórios por Supervisão</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Período</th>
                <th>Zona / Supervisão</th>
                <th>Líderes</th>
                <th>Membros</th>
                <th>Células</th>
                <th>Salvações</th>
                <th>Batizados</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td style="font-weight: bold">{{ $report->year }} / {{ $report->quarter }}ºT</td>
                    <td>
                        <div style="font-weight: bold">{{ $report->zone->name ?? 'N/A' }}</div>
                        <div style="font-size: 9px; color: #6b7280;">{{ $report->supervision->name ?? 'N/A' }}</div>
                    </td>
                    <td style="text-align: center">{{ $report->leaders_count }}</td>
                    <td style="text-align: center; font-weight: bold">{{ $report->members_count }}</td>
                    <td style="text-align: center">{{ $report->cells_count }}</td>
                    <td style="text-align: center; color: #2563eb; font-weight: bold">{{ $report->saved_count }}</td>
                    <td style="text-align: center; color: #16a34a; font-weight: bold">{{ $report->baptized_count }}</td>
                    <td>
                        <span class="badge {{ $report->status === 'approved' ? 'badge-success' : ($report->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($reports->filter(fn($r) => $r->ministerial_observations)->count() > 0)
        <div class="section-title" style="margin-top: 30px;">Observações Ministeriais</div>
        @foreach($reports->filter(fn($r) => $r->ministerial_observations) as $report)
            <div style="margin-bottom: 15px; padding: 12px; background-color: #fef3c7; border-radius: 8px; border-left: 3px solid #f59e0b;">
                <div style="font-size: 10px; font-weight: bold; color: #92400e; margin-bottom: 5px;">
                    {{ $report->supervision->name ?? 'N/A' }} - {{ $report->year }} / {{ $report->quarter }}ºT
                </div>
                <div style="font-size: 10px; color: #451a03; line-height: 1.5;">
                    {{ Str::limit($report->ministerial_observations, 300) }}
                </div>
            </div>
        @endforeach
    @endif
@endsection

<push name='styles')
<style>
    .indicators-grid {
        width: 100%;
        border-collapse: separate;
        border-spacing: 10px 15px;
    }

    .indicator-item {
        background-color: white;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .indicator-label {
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .bar-container {
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 4px;
    }

    .bar-fill {
        height: 100%;
        border-radius: 4px;
    }

    .indicator-value {
        font-size: 11px;
        font-weight: bold;
        color: #374151;
        text-align: right;
    }
</style>
@endpush