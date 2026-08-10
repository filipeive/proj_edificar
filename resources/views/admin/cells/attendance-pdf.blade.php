<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Ficha de Presença - {{ $cell->name }}</title>
    <style>
        @page { margin: 18px 20px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #111827; font-size: 9px; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 12px; }
        .brand { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .subtitle { font-size: 10px; color: #ea580c; font-weight: bold; text-transform: uppercase; letter-spacing: .8px; margin-top: 3px; }
        .meta { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .meta td { padding: 4px 8px; border: 1px solid #e5e7eb; }
        .label { color: #6b7280; font-size: 7px; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 2px; }
        .value { font-size: 9px; font-weight: bold; }
        table.attendance { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.attendance th, table.attendance td { border: 1px solid #d1d5db; padding: 4px 3px; text-align: center; vertical-align: middle; }
        table.attendance th { background: #f3f4f6; font-size: 7px; text-transform: uppercase; }
        .name { width: 20%; text-align: left !important; font-weight: bold; }
        .obs { width: 13%; text-align: left !important; }
        .section-cell { background: #fff7ed !important; color: #c2410c; }
        .section-service { background: #f4f4f5 !important; color: #27272a; }
        .section-wed { background: #ffedd5 !important; color: #9a3412; }
        .mark { font-weight: bold; font-size: 10px; }
        .empty { color: #d1d5db; }
        .footer { position: fixed; bottom: 0; left: 20px; right: 20px; font-size: 7px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 4px; }
    </style>
</head>

<body>
    <div class="header">
        <div class="brand">Portal Life Church</div>
        <div class="subtitle">Ficha Guia de Presença - {{ $date->translatedFormat('F') }} / {{ $year }}</div>
        <table class="meta">
            <tr>
                <td>
                    <span class="label">Célula</span>
                    <span class="value">{{ $cell->name }}</span>
                </td>
                <td>
                    <span class="label">Líder</span>
                    <span class="value">{{ $cell->leader->name ?? 'Não atribuído' }}</span>
                </td>
                <td>
                    <span class="label">Supervisão</span>
                    <span class="value">{{ $cell->supervision->name ?? 'N/A' }}</span>
                </td>
                <td>
                    <span class="label">Zona</span>
                    <span class="value">{{ $cell->supervision->zone->name ?? 'N/A' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="attendance">
        <thead>
            <tr>
                <th class="name" rowspan="2">Nome do Membro</th>
                <th class="section-cell" colspan="{{ max(count($saturdays), 1) }}">Sábados - Célula</th>
                <th class="section-service" colspan="{{ max(count($sundays), 1) }}">Domingos - Culto</th>
                <th class="section-wed" colspan="{{ max(count($wednesdays), 1) }}">4ª Feira - Doutrina</th>
                <th class="obs" rowspan="2">Observações</th>
            </tr>
            <tr>
                @forelse($saturdays as $sat)
                    <th>{{ $sat->format('d/m') }}</th>
                @empty
                    <th>-</th>
                @endforelse
                @forelse($sundays as $sun)
                    <th>{{ $sun->format('d/m') }}</th>
                @empty
                    <th>-</th>
                @endforelse
                @forelse($wednesdays as $wed)
                    <th>{{ $wed->format('d/m') }}</th>
                @empty
                    <th>-</th>
                @endforelse
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
                @php
                    $reasonVal = '';
                    if (isset($attendances[$member->id])) {
                        foreach ($attendances[$member->id] as $type => $dates) {
                            foreach ($dates as $records) {
                                $firstRecord = $records->first();
                                if ($firstRecord && $firstRecord->reason) {
                                    $reasonVal = $firstRecord->reason;
                                    break 2;
                                }
                            }
                        }
                    }
                @endphp
                <tr>
                    <td class="name">{{ $member->name }}</td>
                    @forelse($saturdays as $sat)
                        <td class="mark">{{ isset($attendances[$member->id]['cell'][$sat->format('Y-m-d')]) && $attendances[$member->id]['cell'][$sat->format('Y-m-d')]->first()->status ? 'P' : '' }}</td>
                    @empty
                        <td class="empty">-</td>
                    @endforelse
                    @forelse($sundays as $sun)
                        <td class="mark">{{ isset($attendances[$member->id]['service'][$sun->format('Y-m-d')]) && $attendances[$member->id]['service'][$sun->format('Y-m-d')]->first()->status ? 'P' : '' }}</td>
                    @empty
                        <td class="empty">-</td>
                    @endforelse
                    @forelse($wednesdays as $wed)
                        <td class="mark">{{ isset($attendances[$member->id]['wednesday'][$wed->format('Y-m-d')]) && $attendances[$member->id]['wednesday'][$wed->format('Y-m-d')]->first()->status ? 'P' : '' }}</td>
                    @empty
                        <td class="empty">-</td>
                    @endforelse
                    <td class="obs">{{ $reasonVal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento gerado pelo Portal Life Church em {{ now()->format('d/m/Y H:i') }}. P = Presente.
    </div>
</body>

</html>
