<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <style>
        @page {
            margin: 0.8cm 1cm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
            background-color: #fff;
        }

        .header {
            background-color: #ffffff;
            color: #000000;
            padding: 15px 25px 10px 25px;
            text-align: center;
            border-bottom: 2.5px solid #000000;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
            color: #000000;
        }

        .header .subtitle {
            font-size: 9px;
            font-weight: bold;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .header .congregation {
            font-size: 11px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .header .report-type {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 15px 25px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #4b5563;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 6px;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        .stats-box {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }

        .stats-grid {
            width: 100%;
        }

        .stats-item {
            text-align: center;
        }

        .stats-value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }

        .stats-label {
            font-size: 8.5px;
            color: #6b7280;
            text-transform: uppercase;
            margin-top: 2px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            word-wrap: break-word;
        }

        table.data-table th {
            text-align: left;
            font-size: 8.5px;
            color: #6b7280;
            text-transform: uppercase;
            padding: 8px 5px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        table.data-table td {
            padding: 7px 5px;
            font-size: 9px;
            border-bottom: 1px solid #f3f4f6;
            word-wrap: break-word;
            overflow: hidden;
        }

        .total-row {
            background-color: #fff7ed;
        }

        .total-row td {
            font-weight: bold;
            color: #ea580c;
            border-bottom: none;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding: 10px 25px;
            text-align: center;
            font-size: 8.5px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
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
            <img src="{{ $logoData }}" alt="Logo" style="height: 55px; display: block; margin: 0 auto 10px auto;">
        @endif
        <h1>Comunidade de Vida Cristã - {{ \App\Models\Setting::get('church.name', 'Life Church') }}</h1>
        <div class="subtitle">MOÇAMBIQUE</div>
        <div class="congregation">{{ \App\Models\Setting::get('church.congregation', 'Congregação de Chimoio') }}</div>
        <div class="report-type">@yield('report_type')</div>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        Relatório gerado em {{ now()->format('d/m/Y H:i') }} - Portal Life Church
    </div>
</body>

</html>