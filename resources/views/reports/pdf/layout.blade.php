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
    <div class="header">
        <h1>Portal Life Church</h1>
        <p>@yield('report_type')</p>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        Relatório gerado em {{ now()->format('d/m/Y H:i') }} - Portal Life Church
    </div>
</body>

</html>