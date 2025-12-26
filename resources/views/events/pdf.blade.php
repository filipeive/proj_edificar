<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Relatório de Culto - {{ $event->date->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #f97316;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #f97316;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .grid {
            display: table;
            width: 100%;
        }

        .grid-col {
            display: table-cell;
            width: 50%;
        }

        .label {
            font-weight: bold;
            font-size: 12px;
            color: #888;
        }

        .value {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .observations {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">PORTAL LIFE CHURCH</div>
        <div class="title">Relatório de Culto / Evento</div>
    </div>

    <div class="section">
        <div class="section-title">Informações Gerais</div>
        <div class="grid">
            <div class="grid-col">
                <div class="label">Tipo de Evento</div>
                <div class="value">{{ $event->eventType->name }}</div>

                <div class="label">Data</div>
                <div class="value">{{ $event->date->format('d/m/Y') }}</div>
            </div>
            <div class="grid-col">
                <div class="label">Local / Zona</div>
                <div class="value">{{ $event->zone->name ?? 'Geral' }}</div>

                <div class="label">Participantes</div>
                <div class="value">{{ $event->participants_count }} pessoas</div>
            </div>
        </div>
    </div>

    @if($event->description)
        <div class="section">
            <div class="section-title">Descrição</div>
            <div class="value" style="font-weight: normal;">{{ $event->description }}</div>
        </div>
    @endif

    @if($event->observations)
        <div class="section">
            <div class="section-title">Observações e Relato</div>
            <div class="observations">
                {!! nl2br(e($event->observations)) !!}
            </div>
        </div>
    @endif

    <div class="footer">
        Gerado em {{ date('d/m/Y H:i') }} pelo Portal Life Church - Gestão Eclesiástica
    </div>
</body>

</html>