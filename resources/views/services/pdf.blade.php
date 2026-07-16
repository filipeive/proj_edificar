<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Culto - {{ $service->date->format('d/m/Y') }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            color: #000000;
            background-color: #ffffff;
            font-size: 13px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-img {
            height: 60px;
            display: block;
            margin: 0 auto 10px auto;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 2px;
        }

        .header-subtitle {
            font-size: 10px;
            font-weight: bold;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .header-congregation {
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
        }

        .service-info {
            margin-bottom: 25px;
            font-size: 13px;
        }

        .service-info p {
            margin: 6px 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-top: 25px;
            margin-bottom: 12px;
            text-transform: none;
            border-bottom: none;
            padding-bottom: 0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 6px 0;
            font-size: 12px;
            vertical-align: middle;
        }

        .underline-cell {
            border-bottom: 1.5px solid #000000;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $logoPath = public_path('images/logo-color.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/logo.png');
        }
        $logoExt = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $logoMime = $logoExt === 'svg' ? 'image/svg+xml' : 'image/png';
        $logoData = null;
        if (file_exists($logoPath)) {
            $logoData = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    @endphp

    <div class="header">
        @if($logoData)
            <img src="{{ $logoData }}" alt="Logo" class="logo-img">
        @endif
        <div class="header-title">Comunidade de Vida Cristã - {{ \App\Models\Setting::get('church.name', 'Life Church') }}</div>
        <div class="header-subtitle">MOÇAMBIQUE</div>
        <div class="header-congregation">{{ \App\Models\Setting::get('church.congregation', 'Congregação de Chimoio') }}</div>
    </div>

    <div class="service-info">
        <p>Celebração do culto referente ao dia <strong>{{ $service->date->translatedFormat('d') }}</strong> de <strong>{{ $service->date->translatedFormat('F') }}</strong> de <strong>{{ $service->date->translatedFormat('Y') }}</strong></p>
        <p>Tema: <strong>{{ $service->theme ?? 'Não informado' }}</strong></p>
    </div>

    @if($service->service_type === 'teaching')
        @php
            $teachingAdultsMembers = $service->zoneParticipations->sum(function ($p) {
                return $p->adults_members + $p->leaders + $p->auxiliary_leaders + $p->supervisors + $p->zone_pastors;
            });
            $teachingAdultsVisitors = $service->zoneParticipations->sum('adults_visitors') + ($service->adults_visitors ?? 0);
            $teachingAdultsSalvations = $service->adults_salvations ?? 0;

            $teachingChildrenMembers = $service->zoneParticipations->sum('children_members');
            $teachingChildrenVisitors = $service->zoneParticipations->sum('children_visitors') + ($service->children_visitors ?? 0);
            $teachingChildrenSalvations = $service->children_salvations ?? 0;

            $teachingTotalAdults = $teachingAdultsMembers + $teachingAdultsVisitors + $teachingAdultsSalvations;
            $teachingTotalChildren = $teachingChildrenMembers + $teachingChildrenVisitors + $teachingChildrenSalvations;
            $teachingTotalGeneral = $teachingTotalAdults + $teachingTotalChildren;
        @endphp

        <div class="section-title" style="font-weight: bold;">Detalhes de Participação (Consolidado)</div>
        <table class="details-table" style="width: 100%;">
            <tr>
                <td style="font-weight: bold; width: 140px;">Congregação Geral:</td>
                <td class="underline-cell" style="width: 80px;">
                    {{ $teachingTotalGeneral }}
                </td>
                <td style="width: 40px;">&nbsp;</td>
                <td style="font-weight: bold; text-align: center; width: 100px;">Membros</td>
                <td style="font-weight: bold; text-align: center; width: 100px;">Visitantes</td>
                <td style="font-weight: bold; text-align: center; width: 100px;">Salvos</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Adultos:</td>
                <td class="underline-cell">
                    {{ $teachingTotalAdults }}
                </td>
                <td>&nbsp;</td>
                <td class="underline-cell">
                    {{ $teachingAdultsMembers }}
                </td>
                <td class="underline-cell">
                    {{ $teachingAdultsVisitors }}
                </td>
                <td class="underline-cell">
                    {{ $teachingAdultsSalvations }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Crianças:</td>
                <td class="underline-cell">
                    {{ $teachingTotalChildren }}
                </td>
                <td>&nbsp;</td>
                <td class="underline-cell">
                    {{ $teachingChildrenMembers }}
                </td>
                <td class="underline-cell">
                    {{ $teachingChildrenVisitors }}
                </td>
                <td class="underline-cell">
                    {{ $teachingChildrenSalvations }}
                </td>
            </tr>
        </table>

        <div class="section-title" style="font-weight: bold; margin-top: 20px;">Participação por Zonas Ministeriais</div>
        <table class="details-table" style="width: 100%; border-collapse: collapse; margin-top: 5px;">
            <thead>
                <tr style="border-bottom: 1.5px solid #000000; font-weight: bold;">
                    <th style="text-align: left; padding: 4px 0; font-size: 11px;">Zona</th>
                    <th style="text-align: center; padding: 4px 0; font-size: 11px;">Membros</th>
                    <th style="text-align: center; padding: 4px 0; font-size: 11px;">Visitantes</th>
                    <th style="text-align: center; padding: 4px 0; font-size: 11px;">Líderes</th>
                    <th style="text-align: center; padding: 4px 0; font-size: 11px;">Auxiliares</th>
                    <th style="text-align: center; padding: 4px 0; font-size: 11px;">Supervisores</th>
                    <th style="text-align: center; padding: 4px 0; font-size: 11px;">Pastores</th>
                    <th style="text-align: right; padding: 4px 0; font-size: 11px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($service->zoneParticipations as $participation)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="text-align: left; padding: 5px 0; font-size: 11px;">{{ $participation->zone->name ?? 'N/A' }}</td>
                        <td style="text-align: center; padding: 5px 0; font-size: 11px;">{{ $participation->adults_members + $participation->children_members }}</td>
                        <td style="text-align: center; padding: 5px 0; font-size: 11px;">{{ $participation->adults_visitors + $participation->children_visitors }}</td>
                        <td style="text-align: center; padding: 5px 0; font-size: 11px;">{{ $participation->leaders }}</td>
                        <td style="text-align: center; padding: 5px 0; font-size: 11px;">{{ $participation->auxiliary_leaders }}</td>
                        <td style="text-align: center; padding: 5px 0; font-size: 11px;">{{ $participation->supervisors }}</td>
                        <td style="text-align: center; padding: 5px 0; font-size: 11px;">{{ $participation->zone_pastors }}</td>
                        <td style="text-align: right; padding: 5px 0; font-size: 11px; font-weight: bold;">{{ $participation->total }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top: 1.5px solid #000000; font-weight: bold;">
                    <td style="text-align: left; padding: 6px 0; font-size: 11px;">TOTAIS</td>
                    <td style="text-align: center; padding: 6px 0; font-size: 11px;">{{ $service->zoneParticipations->sum('adults_members') + $service->zoneParticipations->sum('children_members') }}</td>
                    <td style="text-align: center; padding: 6px 0; font-size: 11px;">{{ $service->zoneParticipations->sum('adults_visitors') + $service->zoneParticipations->sum('children_visitors') + ($service->adults_visitors ?? 0) + ($service->children_visitors ?? 0) }}</td>
                    <td style="text-align: center; padding: 6px 0; font-size: 11px;">{{ $service->zoneParticipations->sum('leaders') }}</td>
                    <td style="text-align: center; padding: 6px 0; font-size: 11px;">{{ $service->zoneParticipations->sum('auxiliary_leaders') }}</td>
                    <td style="text-align: center; padding: 6px 0; font-size: 11px;">{{ $service->zoneParticipations->sum('supervisors') }}</td>
                    <td style="text-align: center; padding: 6px 0; font-size: 11px;">{{ $service->zoneParticipations->sum('zone_pastors') }}</td>
                    <td style="text-align: right; padding: 6px 0; font-size: 11px; font-weight: bold;">{{ $service->total_participation }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="section-title" style="font-weight: bold;">Detalhes de Participação</div>
        <table class="details-table" style="width: 100%;">
            <tr>
                <td style="font-weight: bold; width: 140px;">Congregação Geral:</td>
                <td class="underline-cell" style="width: 80px;">
                    {{ $service->adults_members + $service->adults_visitors + $service->adults_salvations + $service->children_members + $service->children_visitors + $service->children_salvations }}
                </td>
                <td style="width: 40px;">&nbsp;</td>
                <td style="font-weight: bold; text-align: center; width: 100px;">Membros</td>
                <td style="font-weight: bold; text-align: center; width: 100px;">Visitantes</td>
                <td style="font-weight: bold; text-align: center; width: 100px;">Salvos</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Adultos:</td>
                <td class="underline-cell">
                    {{ $service->adults_members + $service->adults_visitors + $service->adults_salvations }}
                </td>
                <td>&nbsp;</td>
                <td class="underline-cell">
                    {{ $service->adults_members }}
                </td>
                <td class="underline-cell">
                    {{ $service->adults_visitors }}
                </td>
                <td class="underline-cell">
                    {{ $service->adults_salvations }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Crianças:</td>
                <td class="underline-cell">
                    {{ $service->children_members + $service->children_visitors + $service->children_salvations }}
                </td>
                <td>&nbsp;</td>
                <td class="underline-cell">
                    {{ $service->children_members }}
                </td>
                <td class="underline-cell">
                    {{ $service->children_visitors }}
                </td>
                <td class="underline-cell">
                    {{ $service->children_salvations }}
                </td>
            </tr>
        </table>
    @endif

    <div class="section-title" style="font-weight: bold; margin-top: 30px;">Ofertas e Dízimos</div>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="2" style="font-weight: bold; text-align: center; padding: 4px 0; font-size: 13px;">Descrições</td>
        </tr>
        <tr>
            <td style="width: 140px; font-weight: bold; padding: 8px 0;">Ofertório:</td>
            <td class="underline-cell" style="text-align: left; padding-left: 20px; font-weight: bold;">
                {{ number_format($service->total_offerings, 2, ',', '.') }} Mt
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 8px 0;">Dízimos:</td>
            <td class="underline-cell" style="text-align: left; padding-left: 20px; font-weight: bold;">
                {{ number_format($service->total_tithes, 2, ',', '.') }} Mt
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">&nbsp;</td>
            <td class="underline-cell">&nbsp;</td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">&nbsp;</td>
            <td class="underline-cell">&nbsp;</td>
        </tr>
        <tr>
            <td style="padding: 8px 0;">&nbsp;</td>
            <td class="underline-cell">&nbsp;</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 8px 0;">Of. Especial:</td>
            <td class="underline-cell" style="text-align: left; padding-left: 20px; font-weight: bold;">
                @if($service->special_offerings_total > 0)
                    {{ number_format($service->special_offerings_total, 2, ',', '.') }} Mt
                @else
                    &nbsp;
                @endif
            </td>
        </tr>
    </table>

    @if($service->tithes->count() > 0)
        <div class="section-title" style="font-weight: bold; margin-top: 30px;">Dízimos Registados (Nominativos)</div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="border-bottom: 1.5px solid #000000; font-weight: bold;">
                    <th style="text-align: left; padding: 6px 0; font-size: 12px; width: 60%;">Dizimista</th>
                    <th style="text-align: right; padding: 6px 0; font-size: 12px; width: 40%;">Valor (Mt)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($service->tithes as $tithe)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="text-align: left; padding: 6px 0; font-size: 12px;">{{ $tithe->member_name ?? 'Anónimo' }}</td>
                        <td style="text-align: right; padding: 6px 0; font-size: 12px; font-weight: bold;">{{ number_format($tithe->amount, 2, ',', '.') }} Mt</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($service->individualOfferings->count() > 0)
        <div class="section-title" style="font-weight: bold; margin-top: 30px;">Ofertas Individuais (Nominativas)</div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="border-bottom: 1.5px solid #000000; font-weight: bold;">
                    <th style="text-align: left; padding: 6px 0; font-size: 12px; width: 35%;">Doador</th>
                    <th style="text-align: left; padding: 6px 0; font-size: 12px; width: 35%;">Descrição</th>
                    <th style="text-align: right; padding: 6px 0; font-size: 12px; width: 30%;">Valor (Mt)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($service->individualOfferings as $offering)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="text-align: left; padding: 6px 0; font-size: 12px;">{{ $offering->member_name ?? 'Doador Anónimo' }}</td>
                        <td style="text-align: left; padding: 6px 0; font-size: 12px; color: #4b5563;">{{ $offering->description ?? 'Doação' }}</td>
                        <td style="text-align: right; padding: 6px 0; font-size: 12px; font-weight: bold;">{{ number_format($offering->amount, 2, ',', '.') }} Mt</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 25px;">
        <tr>
            <td style="font-weight: bold; padding: 12px 0; font-size: 14px; width: 140px;">Total:</td>
            <td style="border-bottom: 3px double #000000; padding: 12px 0; font-weight: bold; font-size: 14px; text-align: left; padding-left: 20px;">
                {{ number_format($service->total_financial, 2, ',', '.') }} Mt
            </td>
        </tr>
    </table>

    <div class="comments-section" style="margin-top: 30px;">
        <div style="font-weight: bold; margin-bottom: 8px;">Comentários:</div>
        <div style="border-bottom: 1.5px solid #000000; padding: 8px 0; font-style: italic;">
            {{ $service->observations ?: 'Sem observações registradas.' }}
        </div>
        <div style="border-bottom: 1.5px solid #000000; padding: 8px 0; height: 15px;">&nbsp;</div>
        <div style="border-bottom: 1.5px solid #000000; padding: 8px 0; height: 15px;">&nbsp;</div>
    </div>
</body>

</html>