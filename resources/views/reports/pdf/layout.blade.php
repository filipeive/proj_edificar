<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
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
            background-color: #ffffff;
            color: #000000;
            padding: 20px 50px 10px 50px;
            text-align: center;
            border-bottom: 2.5px solid #000000;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #000000;
        }

        .header .subtitle {
            font-size: 10px;
            font-weight: bold;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .header .congregation {
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .header .report-type {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        table.data-table td {
            padding: 12px 10px;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
        }

        .total-row {
            background-color: #fff7ed;
        }

        .total-row td {
            font-weight: bold;
            color: #ea580c;
            border-bottom: none;
            font-size: 13px;
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