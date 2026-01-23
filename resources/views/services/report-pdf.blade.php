@extends('reports.pdf.layout')

@section('title', $title)
@section('report_type', $title)

@section('content')
    @php
        $totalAttendance = 0;
        $totalMembers = 0;
        $totalVisitors = 0;
        $totalSalvations = 0;
        $totalOfferings = 0;

        foreach ($services as $service) {
            $totalAttendance += ($service->adults_members + $service->adults_visitors + $service->children_members + $service->children_visitors);
            $totalMembers += ($service->adults_members + $service->children_members);
            $totalVisitors += ($service->adults_visitors + $service->children_visitors);
            $totalSalvations += ($service->adults_salvations + $service->children_salvations);
            $totalOfferings += $service->offerings->sum('amount') + $service->individualOfferings->sum('amount');
        }
    @endphp

    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ count($services) }}</div>
                    <div class="stats-label">Cultos Realizados</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ $totalAttendance }}</div>
                    <div class="stats-label">Público Total</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-green-600">{{ number_format($totalOfferings, 2, ',', '.') }} MT</div>
                    <div class="stats-label">Total Ofertas</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-blue-600">{{ $totalSalvations }}</div>
                    <div class="stats-label">Decisões</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Detalhamento por Culto</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Público</th>
                <th>Membros</th>
                <th>Habitantes</th>
                <th>Decisões</th>
                <th style="text-align: right">Ofertas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $service)
                @php
                    $attendance = ($service->adults_members + $service->adults_visitors + $service->children_members + $service->children_visitors);
                    $members = ($service->adults_members + $service->children_members);
                    $visitors = ($service->adults_visitors + $service->children_visitors);
                    $salvations = ($service->adults_salvations + $service->children_salvations);
                    $offerings = $service->offerings->sum('amount') + $service->individualOfferings->sum('amount');

                    $typeLabel = match ($service->service_type) {
                        '1st' => '1º Culto',
                        '2nd' => '2º Culto',
                        '3rd' => '3º Culto',
                        '4th' => '4º Culto',
                        'special' => 'Especial',
                        default => $service->service_type
                    };
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($service->date)->format('d/m/Y') }}</td>
                    <td>{{ $typeLabel }}</td>
                    <td style="font-weight: bold">{{ $attendance }}</td>
                    <td>{{ $members }}</td>
                    <td>{{ $visitors }}</td>
                    <td>{{ $salvations }}</td>
                    <td style="text-align: right">{{ number_format($offerings, 2, ',', '.') }} MT</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">TOTAL</td>
                <td>{{ $totalAttendance }}</td>
                <td>{{ $totalMembers }}</td>
                <td>{{ $totalVisitors }}</td>
                <td>{{ $totalSalvations }}</td>
                <td style="text-align: right">{{ number_format($totalOfferings, 2, ',', '.') }} MT</td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Médias por Culto</div>
    <div class="stats-box" style="background-color: #f0f9ff;">
        @php
            $count = count($services) ?: 1;
        @endphp
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ round($totalAttendance / $count, 1) }}</div>
                    <div class="stats-label">Média Público</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ round($totalVisitors / $count, 1) }}</div>
                    <div class="stats-label">Média Visitantes</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ round($totalSalvations / $count, 1) }}</div>
                    <div class="stats-label">Média Decisões</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ number_format($totalOfferings / $count, 2, ',', '.') }} MT</div>
                    <div class="stats-label">Média Ofertas</div>
                </td>
            </tr>
        </table>
    </div>
@endsection