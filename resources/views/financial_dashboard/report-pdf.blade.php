<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 11px; }
        h1 { font-size: 20px; margin: 0 0 5px; }
        .meta { color: #64748b; margin-bottom: 20px; }
        .summary { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .summary td { padding: 10px; border: 1px solid #dbe2ea; }
        .summary strong { display: block; font-size: 9px; color: #64748b; text-transform: uppercase; }
        table.transactions { width: 100%; border-collapse: collapse; }
        .transactions th, .transactions td { padding: 7px; border: 1px solid #dbe2ea; text-align: left; }
        .transactions th { background: #155e75; color: white; }
        .number { text-align: right !important; white-space: nowrap; }
        .empty { text-align: center; color: #64748b; padding: 20px; }
    </style>
</head>
<body>
    <h1>Relatório Financeiro</h1>
    <div class="meta">
        {{ $scope === 'eclesiastico' ? 'Eclesiástico' : 'Projeto Edificar' }} ·
        {{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}
    </div>

    <table class="summary">
        <tr>
            <td><strong>Entradas</strong>{{ number_format($income, 2, ',', '.') }} MT</td>
            <td><strong>Saídas</strong>{{ number_format($expenses, 2, ',', '.') }} MT</td>
            <td><strong>Saldo</strong>{{ number_format($balance, 2, ',', '.') }} MT</td>
        </tr>
    </table>

    <table class="transactions">
        <thead>
            <tr>
                <th>Data</th>
                <th>Origem</th>
                <th>Descrição</th>
                <th class="number">Entradas (MT)</th>
                <th class="number">Saídas (MT)</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                    <td>{{ $row['source'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td class="number">{{ number_format($row['income'], 2, ',', '.') }}</td>
                    <td class="number">{{ number_format($row['expense'], 2, ',', '.') }}</td>
                    <td>{{ $row['status'] }}</td>
                </tr>
            @empty
                <tr><td class="empty" colspan="6">Sem movimentos para o período selecionado.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>