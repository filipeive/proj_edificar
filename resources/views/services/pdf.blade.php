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
        <div class="header-title">Comunidade de Vida Cristã - Life Church</div>
        <div class="header-subtitle">MOÇAMBIQUE</div>
        <div class="header-congregation">Congregação de Chimoio</div>
    </div>

    <div class="service-info">
        <p>Celebração do culto referente ao dia <strong>{{ $service->date->translatedFormat('d') }}</strong> de <strong>{{ $service->date->translatedFormat('F') }}</strong> de <strong>{{ $service->date->translatedFormat('Y') }}</strong></p>
        <p>Tema: <strong>{{ $service->theme ?? 'Não informado' }}</strong></p>
    </div>

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
        <tr>
            <td style="font-weight: bold; padding: 12px 0; font-size: 14px;">Total:</td>
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