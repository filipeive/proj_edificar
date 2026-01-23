<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Culto - {{ $service->date->format('d/m/Y') }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
            background-color: #fff;
        }

        .header {
            background-color: #111827;
            color: white;
            padding: 40px 50px;
            text-align: left;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header p {
            margin: 5px 0 0;
            color: #f97316;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .content {
            padding: 40px 50px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #4b5563;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 8px;
            margin-bottom: 20px;
            margin-top: 30px;
        }

        .grid {
            width: 100%;
            margin-bottom: 20px;
        }

        .grid td {
            padding: 10px 0;
            vertical-align: top;
        }

        .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .value {
            font-size: 13px;
            color: #111827;
            font-weight: 500;
        }

        .stats-box {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-grid {
            width: 100%;
        }

        .stats-item {
            text-align: center;
        }

        .stats-value {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
        }

        .stats-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            margin-top: 4px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th {
            text-align: left;
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        table.data-table td {
            padding: 12px 0;
            font-size: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .total-row {
            background-color: #fff7ed;
        }

        .total-row td {
            font-weight: bold;
            color: #ea580c;
            border-bottom: none;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 20px 50px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-orange {
            background-color: #fff7ed;
            color: #ea580c;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Portal Life Church</h1>
        <p>Relatório Oficial de Culto</p>
    </div>

    <div class="content">
        <div class="stats-box">
            <table class="stats-grid">
                <tr>
                    <td class="stats-item">
                        <div class="stats-value">{{ $service->date->format('d/m/Y') }}</div>
                        <div class="stats-label">Data do Culto</div>
                    </td>
                    <td class="stats-item">
                        <div class="stats-value">
                            @php
                                $types = [
                                    '1st' => '1º Culto',
                                    '2nd' => '2º Culto',
                                    '3rd' => '3º Culto',
                                    '4th' => '4º Culto',
                                    'special' => 'Especial'
                                ];
                            @endphp
                            {{ $types[$service->service_type] ?? $service->service_type }}
                        </div>
                        <div class="stats-label">Tipo de Culto</div>
                    </td>
                    <td class="stats-item">
                        <div class="stats-value">{{ $service->total_participation }}</div>
                        <div class="stats-label">Total Presentes</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title">Informações Gerais</div>
        <table class="grid">
            <tr>
                <td width="50%">
                    <div class="label">Pregador / Responsável</div>
                    <div class="value">{{ $service->preacher->name ?? 'Não informado' }}</div>
                </td>
                <td width="50%">
                    <div class="label">Tema da Mensagem</div>
                    <div class="value">{{ $service->theme ?? 'Não informado' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="label">Resumo da Mensagem</div>
                    <div class="value" style="line-height: 1.6;">{{ $service->message ?? 'Sem resumo disponível.' }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">
            {{ $service->service_type === 'teaching' ? 'Participação por Zona' : 'Estatísticas de Presença Detalhada' }}
        </div>

        @if($service->service_type === 'teaching')
            <table class="data-table" style="margin-bottom: 30px;">
                <thead>
                    <tr style="background-color: #f9fafb;">
                        <th style="padding-left: 10px;">Zona</th>
                        <th style="padding-left: 5px; text-align: center;">Membros</th>
                        <th style="padding-left: 5px; text-align: center;">Visit.</th>
                        <th style="padding-left: 5px; text-align: center;">Líderes</th>
                        <th style="padding-left: 5px; text-align: center;">Timótio</th>
                        <th style="padding-left: 5px; text-align: center;">Superv.</th>
                        <th style="padding-left: 5px; text-align: center;">Pastores Z.</th>
                        <th style="text-align: right; padding-right: 10px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($service->zoneParticipations as $participation)
                        <tr>
                            <td style="padding-left: 10px; font-weight: bold;">{{ $participation->zone->name }}</td>
                            <td style="text-align: center;">{{ $participation->adults_members }}</td>
                            <td style="text-align: center;">{{ $participation->adults_visitors }}</td>
                            <td style="text-align: center; color: #f97316;">{{ $participation->leaders }}</td>
                            <td style="text-align: center; color: #f9b17a;">{{ $participation->auxiliary_leaders }}</td>
                            <td style="text-align: center; color: #7c3aed;">{{ $participation->supervisors }}</td>
                            <td style="text-align: center; color: #dc2626;">{{ $participation->zone_pastors }}</td>
                            <td style="text-align: right; padding-right: 10px; font-weight: bold;">{{ $participation->total }}</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f0f9ff; color: #0369a1; font-weight: bold;">
                        <td style="padding-left: 10px;">TOTAL ENSINO</td>
                        <td style="text-align: center;">{{ $service->zoneParticipations->sum('adults_members') }}</td>
                        <td style="text-align: center;">
                            {{ $service->zoneParticipations->sum('adults_visitors') + $service->adults_visitors + $service->children_visitors }}
                        </td>
                        <td style="text-align: center;">{{ $service->zoneParticipations->sum('leaders') }}</td>
                        <td style="text-align: center;">{{ $service->zoneParticipations->sum('auxiliary_leaders') }}</td>
                        <td style="text-align: center;">{{ $service->zoneParticipations->sum('supervisors') }}</td>
                        <td style="text-align: center;">{{ $service->zoneParticipations->sum('zone_pastors') }}</td>
                        <td style="text-align: right; padding-right: 10px;">{{ $service->total_participation }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <table class="data-table" style="margin-bottom: 30px;">
                <thead>
                    <tr style="background-color: #f9fafb;">
                        <th style="padding-left: 10px;">Categoria</th>
                        <th style="text-align: center;">Membros</th>
                        <th style="text-align: center;">Visitantes</th>
                        <th style="text-align: center;">Decisões</th>
                        <th style="text-align: right; padding-right: 10px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-left: 10px; font-weight: bold;">Adultos</td>
                        <td style="text-align: center;">{{ $service->adults_members }}</td>
                        <td style="text-align: center;">{{ $service->adults_visitors }}</td>
                        <td style="text-align: center;">{{ $service->adults_salvations }}</td>
                        <td style="text-align: right; padding-right: 10px; font-weight: bold;">
                            {{ $service->adults_members + $service->adults_visitors + $service->adults_salvations }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px; font-weight: bold;">Crianças</td>
                        <td style="text-align: center;">{{ $service->children_members }}</td>
                        <td style="text-align: center;">{{ $service->children_visitors }}</td>
                        <td style="text-align: center;">{{ $service->children_salvations }}</td>
                        <td style="text-align: right; padding-right: 10px; font-weight: bold;">
                            {{ $service->children_members + $service->children_visitors + $service->children_salvations }}
                        </td>
                    </tr>
                    <tr style="background-color: #f0f9ff; color: #0369a1; font-weight: bold;">
                        <td style="padding-left: 10px;">TOTAL GERAL</td>
                        <td style="text-align: center;">{{ $service->adults_members + $service->children_members }}</td>
                        <td style="text-align: center;">{{ $service->adults_visitors + $service->children_visitors }}</td>
                        <td style="text-align: center;">{{ $service->adults_salvations + $service->children_salvations }}
                        </td>
                        <td style="text-align: right; padding-right: 10px;">
                            {{ $service->total_participation }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        <div class="section-title">Movimentação Financeira</div>

        <p style="font-size: 11px; font-weight: bold; color: #4b5563; margin-bottom: 10px;">OFERTAS POR TIPO</p>
        <table class="data-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th style="text-align: right;">Valor (MT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($service->offerings as $offering)
                    <tr>
                        <td>{{ $offering->offeringType->name }}</td>
                        <td style="text-align: right;">{{ number_format($offering->amount, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                @if($service->special_offerings_total > 0)
                    <tr>
                        <td>Ofertas Especiais</td>
                        <td style="text-align: right;">{{ number_format($service->special_offerings_total, 2, ',', '.') }}
                        </td>
                    </tr>
                @endif
                <tr style="font-weight: bold; background-color: #f9fafb;">
                    <td>Subtotal Ofertas</td>
                    <td style="text-align: right;">
                        {{ number_format($service->total_offerings + $service->special_offerings_total, 2, ',', '.') }}
                        MT
                    </td>
                </tr>
            </tbody>
        </table>

        @if($service->tithes->count() > 0)
            <p style="font-size: 11px; font-weight: bold; color: #4b5563; margin-top: 20px; margin-bottom: 10px;">DÍZIMOS
                REGISTRADOS</p>
            <table class="data-table" style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th>Dizimista</th>
                        <th style="text-align: right;">Valor (MT)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($service->tithes as $tithe)
                        <tr>
                            <td>{{ $tithe->member_name ?? 'Anônimo' }}</td>
                            <td style="text-align: right;">{{ number_format($tithe->amount, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold; background-color: #f9fafb;">
                        <td>Subtotal Dízimos</td>
                        <td style="text-align: right;">{{ number_format($service->total_tithes, 2, ',', '.') }} MT</td>
                    </tr>
                </tbody>
            </table>
        @endif

        <div style="margin-top: 30px; padding: 20px; background-color: #111827; border-radius: 12px; color: white;">
            <table width="100%">
                <tr>
                    <td style="font-size: 14px; font-weight: 300; vertical-align: middle;">TOTAL GERAL ARRECADADO</td>
                    <td style="text-align: right; font-size: 24px; font-weight: 900; color: #f97316;">
                        {{ number_format($service->total_financial, 2, ',', '.') }} MT
                    </td>
                </tr>
            </table>
        </div>

        @if($service->observations)
            <div class="section-title">Observações Adicionais</div>
            <div class="value"
                style="line-height: 1.6; background-color: #fefce8; padding: 15px; border-radius: 8px; border-left: 4px solid #facc15;">
                {{ $service->observations }}
            </div>
        @endif
    </div>

    <div class="footer">
        Este documento é um relatório oficial gerado pelo Sistema de Gestão Portal Life Church em
        {{ now()->format('d/m/Y H:i') }}.
    </div>
</body>

</html>