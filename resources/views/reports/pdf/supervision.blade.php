@extends('reports.pdf.layout')

@section('title', 'Relatório de Supervisão - ' . $supervision->name)
@section('report_type', 'Relatório de Contribuições da Supervisão')

@section('content')
    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ $supervision->name }}</div>
                    <div class="stats-label">Supervisão</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</div>
                    <div class="stats-label">Período</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">
                        {{ number_format($contributions->where('status', 'verificada')->sum('amount'), 2, ',', '.') }} MT
                    </div>
                    <div class="stats-label">Total Verificado</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Resumo por Célula</div>
    <table class="data-table" style="margin-bottom: 30px;">
        <thead>
            <tr>
                <th>Célula</th>
                <th style="text-align: right;">Total (MT)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contributions->groupBy('cell_id') as $cellId => $cellContributions)
                <tr>
                    <td>{{ $cellContributions->first()->cell->name ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($cellContributions->sum('amount'), 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Detalhes das Contribuições</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Membro</th>
                <th>Célula</th>
                <th style="text-align: right;">Valor (MT)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contributions as $contribution)
                <tr>
                    <td>{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                    <td>{{ $contribution->user->name }}</td>
                    <td>{{ $contribution->cell->name ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($contribution->amount, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #9ca3af;">Nenhuma contribuição encontrada
                        neste período.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3">TOTAL GERAL</td>
                <td style="text-align: right;">{{ number_format($contributions->sum('amount'), 2, ',', '.') }} MT</td>
            </tr>
        </tbody>
    </table>
@endsection