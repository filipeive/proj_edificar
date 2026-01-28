<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Membros - {{ $package->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .subtitle { font-size: 12px; color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; vertical-align: middle; }
        th { background: #f97316; color: #ffffff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; }
        tr:nth-child(even) td { background: #f9fafb; }
        .badge { font-weight: 700; color: #0f172a; }
        .meta { font-size: 10px; color: #6b7280; }
        .signature { margin-top: 24px; display: flex; justify-content: space-between; }
        .signature .line { width: 40%; border-top: 1px solid #9ca3af; text-align: center; padding-top: 6px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    @php
        $churchName = \App\Models\Setting::get('church.name', 'Life Church');
        $logoSetting = \App\Models\Setting::get('branding.logo_primary', null);
        $logoPath = $logoSetting ? public_path(ltrim($logoSetting, '/')) : public_path('images/logo.png');
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
        <div>
            <div class="title">{{ $churchName }}</div>
            <div class="meta">Relatório de Membros — {{ $package->name }}</div>
        </div>
        @if($logoData)
            <img src="{{ $logoData }}" alt="Logo" style="height:40px;">
        @endif
    </div>

    <div class="subtitle">Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}</div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Membro</th>
                <th>Telefone</th>
                <th>Célula</th>
                <th>Supervisão</th>
                <th>Zona</th>
                <th>Pastor Zona</th>
                <th>Compromisso</th>
                <th>Contribuiu?</th>
                <th>Valor Pago</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['phone'] }}</td>
                    <td>{{ $row['cell'] }}</td>
                    <td>{{ $row['supervision'] }}</td>
                    <td>{{ $row['zone'] }}</td>
                    <td>{{ $row['pastor'] }}</td>
                    <td>{{ $row['committed'] }}</td>
                    <td class="badge">{{ $row['contributed'] }}</td>
                    <td>{{ $row['paid'] }}</td>
                    <td>{{ $row['paid_date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <div class="line">Assinatura do Responsável</div>
        <div class="line">Data / Carimbo</div>
    </div>
</body>
</html>
