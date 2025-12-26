@extends('reports.pdf.layout')

@section('title', 'Relatório de Célula - ' . $cell->name)
@section('report_type', 'Relatório de Contribuições da Célula')

@section('content')
    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ $cell->name }}</div>
                    <div class="stats-label">Célula</div>
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

    <div class="section-title">Detalhes das Contribuições</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Membro</th>
                <th style="text-align: right;">Valor (MT)</th>
                <th style="text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contributions as $contribution)
                <tr>
                    <td>{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                    <td>{{ $contribution->user->name }}</td>
                    <td style="text-align: right;">{{ number_format($contribution->amount, 2, ',', '.') }}</td>
                    <td style="text-align: center;">
                        @if($contribution->status === 'verificada')
                            <span class="badge badge-success">Verificada</span>
                        @elseif($contribution->status === 'pendente')
                            <span class="badge badge-warning">Pendente</span>
                        @else
                            <span class="badge badge-danger">Rejeitada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #9ca3af;">Nenhuma contribuição encontrada
                        neste período.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2">TOTAL GERAL</td>
                <td style="text-align: right;">{{ number_format($contributions->sum('amount'), 2, ',', '.') }} MT</td>
                <td></td>
            </tr>
        </tbody>
    </table>
@endsection