<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório Nacional de Congregações</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 10px; }
        h1 { margin: 0 0 5px; font-size: 20px; }
        .period { margin-bottom: 18px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #d1d5db; }
        th { background: #7c2d12; color: white; text-align: left; }
        td.num { text-align: right; }
        tfoot { font-weight: bold; background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Relatório Nacional de Congregações</h1>
    <p class="period">Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Congregação</th>
                <th>Estado</th>
                <th>Membros</th>
                <th>Zonas</th>
                <th>Células</th>
                <th>Cultos</th>
                <th>Contribuições (MT)</th>
                <th>Cultos (MT)</th>
                <th>Total Entradas (MT)</th>
                <th>Despesas (MT)</th>
                <th>Saldo (MT)</th>
                <th>Pendentes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['congregation'] }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td class="num">{{ number_format($row['active_members']) }}</td>
                    <td class="num">{{ number_format($row['zones']) }}</td>
                    <td class="num">{{ number_format($row['cells']) }}</td>
                    <td class="num">{{ number_format($row['services']) }}</td>
                    <td class="num">{{ number_format($row['verified_contributions'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($row['service_income'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($row['total_income'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($row['expenses'] ?? 0, 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($row['net_balance'] ?? 0, 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($row['pending_contributions']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total nacional</td>
                <td class="num">{{ number_format($rows->sum('active_members')) }}</td>
                <td class="num">{{ number_format($rows->sum('zones')) }}</td>
                <td class="num">{{ number_format($rows->sum('cells')) }}</td>
                <td class="num">{{ number_format($rows->sum('services')) }}</td>
                <td class="num">{{ number_format($rows->sum('verified_contributions'), 2, ',', '.') }}</td>
                <td class="num">{{ number_format($rows->sum('service_income'), 2, ',', '.') }}</td>
                <td class="num">{{ number_format($rows->sum('total_income'), 2, ',', '.') }}</td>
                <td class="num">{{ number_format($rows->sum('expenses'), 2, ',', '.') }}</td>
                <td class="num">{{ number_format($rows->sum('net_balance'), 2, ',', '.') }}</td>
                <td class="num">{{ number_format($rows->sum('pending_contributions')) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
