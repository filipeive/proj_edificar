<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Calendário de Casamentos {{ $year }}</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #ea580c;
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            color: #666;
            margin: 5px 0 0;
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        th {
            background-color: #fff7ed;
            color: #ea580c;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
        }

        .date {
            font-weight: bold;
            color: #333;
        }

        .couple {
            font-weight: bold;
            font-size: 14px;
        }

        .status-completed {
            color: #16a34a;
            font-weight: bold;
        }

        .status-cancelled {
            color: #dc2626;
            font-weight: bold;
        }

        .status-scheduled {
            color: #ea580c;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Calendário de Casamentos {{ $year }}</h1>
        <p>Life Church - Quelimane</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Data</th>
                <th width="10%">Horário</th>
                <th width="35%">Noivos</th>
                <th width="25%">Local</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($weddings as $wedding)
                <tr>
                    <td class="date">{{ $wedding->date->format('d/m/Y') }}</td>
                    <td>{{ $wedding->time ? $wedding->time->format('H:i') : '-' }}</td>
                    <td>
                        <div class="couple">{{ $wedding->groom_name }}</div>
                        <div class="couple" style="color: #666;">& {{ $wedding->bride_name }}</div>
                    </td>
                    <td>{{ $wedding->location ?? '-' }}</td>
                    <td>
                        @if($wedding->status == 'completed')
                            <span class="status-completed">Realizado</span>
                        @elseif($wedding->status == 'cancelled')
                            <span class="status-cancelled">Cancelado</span>
                        @else
                            <span class="status-scheduled">Agendado</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #999;">
                        Nenhum casamento registrado para este ano.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>