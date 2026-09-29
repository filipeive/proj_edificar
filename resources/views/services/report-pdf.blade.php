@extends('reports.pdf.layout')

@section('title', $title)
@section('report_type', $title)

@section('content')
    @php
        $totalAttendance = $services->sum('total_participation');
        $totalMembers = $services->sum('total_members');
        $totalVisitors = $services->sum('total_visitors');
        $totalSalvations = $services->sum(fn($s) => $s->total_salvations);
        $totalOfferings = $services->sum('total_offerings');
        $totalTithes = $services->sum('total_tithes');
        $totalFinancial = $services->sum('total_financial');

        $normalServices = $services->filter(fn($s) => in_array($s->service_type, ['1st', '2nd', '3rd', '4th']));
        $teachingServices = $services->filter(fn($s) => $s->service_type === 'teaching');
        $specialServices = $services->filter(fn($s) => $s->service_type === 'special');
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
                    <div class="stats-label">Total de Participantes</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-green-600">{{ number_format($totalFinancial, 2, ',', '.') }} MT</div>
                    <div class="stats-label">Total Arrecadado</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-blue-600">{{ $totalSalvations }}</div>
                    <div class="stats-label">Salvações</div>
                </td>
            </tr>
        </table>
    </div>

    @if($normalServices->count() > 0)
        <div class="section-title">Cultos Normais (1º ao 4º)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 9%;">Data</th>
                    <th style="width: 5%;">Tipo</th>
                    <th style="width: 20%;">Tema</th>
                    <th style="width: 18%;">Pregador</th>
                    <th style="width: 8%;">Participantes</th>
                    <th style="width: 7%;">Membros</th>
                    <th style="width: 7%;">Visitantes</th>
                    <th style="width: 6%;">Salvações</th>
                    <th style="width: 10%; text-align: right">Ofertas</th>
                    <th style="width: 10%; text-align: right">Dízimos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($normalServices as $service)
                    <tr>
                        <td>{{ $service->date->format('d/m/Y') }}</td>
                        <td>{{ match ($service->service_type) { '1st' => '1º', '2nd' => '2º', '3rd' => '3º', '4th' => '4º', 'special' => 'Especial', default => $service->service_type} }}
                        </td>
                        <td style="font-size: 8.5px;">{{ $service->theme ?: '—' }}</td>
                        <td style="font-size: 8.5px;">{{ $service->preacher ? $service->preacher->name : ($service->preacher_name ?: '—') }}</td>
                        <td style="font-weight: bold">{{ $service->total_participation }}</td>
                        <td>{{ $service->total_members }}</td>
                        <td>{{ $service->total_visitors }}</td>
                        <td>{{ $service->total_salvations }}</td>
                        <td style="text-align: right">{{ number_format($service->total_offerings, 2, ',', '.') }} MT</td>
                        <td style="text-align: right">{{ number_format($service->total_tithes, 2, ',', '.') }} MT</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4">TOTAL NORMAS</td>
                    <td>{{ $normalServices->sum('total_participation') }}</td>
                    <td>{{ $normalServices->sum('total_members') }}</td>
                    <td>{{ $normalServices->sum('total_visitors') }}</td>
                    <td>{{ $normalServices->sum(fn($s) => $s->total_salvations) }}</td>
                    <td style="text-align: right">{{ number_format($normalServices->sum('total_offerings'), 2, ',', '.') }} MT</td>
                    <td style="text-align: right">{{ number_format($normalServices->sum('total_tithes'), 2, ',', '.') }} MT
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if($teachingServices->count() > 0)
        <div class="section-title" style="margin-top: 30px; border-left-color: #f97316;">Cultos de Ensino (Quarta-feira)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 9%;">Data</th>
                    <th style="width: 24%;">Tema</th>
                    <th style="width: 19%;">Pregador</th>
                    <th style="width: 8%;">Participantes</th>
                    <th style="width: 7%;">Membros</th>
                    <th style="width: 7%;">Visitantes</th>
                    <th style="width: 6%;">Salvações</th>
                    <th style="width: 10%; text-align: right">Ofertas</th>
                    <th style="width: 10%; text-align: right">Dízimos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachingServices as $service)
                    <tr>
                        <td>{{ $service->date->format('d/m/Y') }}</td>
                        <td style="font-size: 8.5px;">{{ $service->theme ?: '—' }}</td>
                        <td style="font-size: 8.5px;">{{ $service->preacher ? $service->preacher->name : ($service->preacher_name ?: '—') }}</td>
                        <td style="font-weight: bold">{{ $service->total_participation }}</td>
                        <td>{{ $service->total_members }}</td>
                        <td>{{ $service->total_visitors }}</td>
                        <td>{{ $service->total_salvations }}</td>
                        <td style="text-align: right">{{ number_format($service->total_offerings, 2, ',', '.') }} MT</td>
                        <td style="text-align: right">{{ number_format($service->total_tithes, 2, ',', '.') }} MT</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row" style="background-color: #fff7ed;">
                    <td colspan="3">TOTAL ENSINO</td>
                    <td>{{ $teachingServices->sum('total_participation') }}</td>
                    <td>{{ $teachingServices->sum('total_members') }}</td>
                    <td>{{ $teachingServices->sum('total_visitors') }}</td>
                    <td>{{ $teachingServices->sum(fn($s) => $s->total_salvations) }}</td>
                    <td style="text-align: right">{{ number_format($teachingServices->sum('total_offerings'), 2, ',', '.') }} MT</td>
                    <td style="text-align: right">{{ number_format($teachingServices->sum('total_tithes'), 2, ',', '.') }} MT
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if($specialServices->count() > 0)
        <div class="section-title" style="margin-top: 30px; border-left-color: #6366f1;">Cultos Especiais</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 9%;">Data</th>
                    <th style="width: 24%;">Tema</th>
                    <th style="width: 19%;">Pregador</th>
                    <th style="width: 8%;">Participantes</th>
                    <th style="width: 7%;">Membros</th>
                    <th style="width: 7%;">Visitantes</th>
                    <th style="width: 6%;">Salvações</th>
                    <th style="width: 10%; text-align: right">Ofertas</th>
                    <th style="width: 10%; text-align: right">Dízimos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($specialServices as $service)
                    <tr>
                        <td>{{ $service->date->format('d/m/Y') }}</td>
                        <td style="font-size: 8.5px;">{{ $service->theme ?: '—' }}</td>
                        <td style="font-size: 8.5px;">{{ $service->preacher ? $service->preacher->name : ($service->preacher_name ?: '—') }}</td>
                        <td style="font-weight: bold">{{ $service->total_participation }}</td>
                        <td>{{ $service->total_members }}</td>
                        <td>{{ $service->total_visitors }}</td>
                        <td>{{ $service->total_salvations }}</td>
                        <td style="text-align: right">{{ number_format($service->total_offerings, 2, ',', '.') }} MT</td>
                        <td style="text-align: right">{{ number_format($service->total_tithes, 2, ',', '.') }} MT</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row" style="background-color: #f5f3ff;">
                    <td colspan="3">TOTAL ESPECIAIS</td>
                    <td>{{ $specialServices->sum('total_participation') }}</td>
                    <td>{{ $specialServices->sum('total_members') }}</td>
                    <td>{{ $specialServices->sum('total_visitors') }}</td>
                    <td>{{ $specialServices->sum(fn($s) => $s->total_salvations) }}</td>
                    <td style="text-align: right">{{ number_format($specialServices->sum('total_offerings'), 2, ',', '.') }} MT</td>
                    <td style="text-align: right">{{ number_format($specialServices->sum('total_tithes'), 2, ',', '.') }} MT
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="section-title">Médias Gerais</div>
    <div class="stats-box" style="background-color: #f8fafc;">
        @php
            $count = count($services) ?: 1;
        @endphp
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ round($totalAttendance / $count, 1) }}</div>
                    <div class="stats-label">Média Participantes</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ round($totalVisitors / $count, 1) }}</div>
                    <div class="stats-label">Média Visitantes</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ round($totalSalvations / $count, 1) }}</div>
                    <div class="stats-label">Média Salvações</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value">{{ number_format($totalFinancial / $count, 2, ',', '.') }} MT</div>
                    <div class="stats-label">Média Financeira</div>
                </td>
            </tr>
        </table>
    </div>
@endsection