<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Relatório de Célula - {{ $cellMeeting->meeting_date->format('d/m/Y') }}</title>
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
        <div class="title">Relatório de Encontro de Célula</div>
    </div>

    <div class="section">
        <div class="section-title">Informações da Célula</div>
        <div class="grid">
            <div class="grid-col">
                <div class="label">Célula</div>
                <div class="value">{{ $cellMeeting->cell->name }}</div>

                <div class="label">Líder do Encontro</div>
                <div class="value">{{ $cellMeeting->leader->name }}</div>
            </div>
            <div class="grid-col">
                <div class="label">Data do Encontro</div>
                <div class="value">{{ $cellMeeting->meeting_date->format('d/m/Y') }}</div>

                <div class="label">Zona / Supervisão</div>
                <div class="value">
                    {{ $cellMeeting->cell->supervision->zone->name ?? 'N/A' }} /
                    {{ $cellMeeting->cell->supervision->name ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Conteúdo do Encontro</div>
        <div class="grid">
            <div class="grid-col">
                <div class="label">Tema</div>
                <div class="value">{{ $cellMeeting->theme ?? 'Não informado' }}</div>
            </div>
            <div class="grid-col">
                <div class="label">Texto Bíblico</div>
                <div class="value">{{ $cellMeeting->biblical_text ?? 'Não informado' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Estatísticas e Presenças</div>
        <div class="grid">
            <div class="grid-col">
                <div class="label">Adultos</div>
                <div class="value">{{ $cellMeeting->adults_count }}</div>

                <div class="label">Crianças</div>
                <div class="value">{{ $cellMeeting->children_count }}</div>
            </div>
            <div class="grid-col">
                <div class="label">Visitantes</div>
                <div class="value">{{ $cellMeeting->visitors_count }}</div>

                <div class="label">Total de Participantes</div>
                <div class="value">
                    {{ $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count }}</div>
            </div>
        </div>
    </div>

    @if($cellMeeting->decisions)
        <div class="section">
            <div class="section-title">Decisões / Conversões</div>
            <div class="value" style="font-weight: normal;">{{ $cellMeeting->decisions }}</div>
        </div>
    @endif

    @if($cellMeeting->observations)
        <div class="section">
            <div class="section-title">Observações e Relato</div>
            <div class="observations">
                {!! nl2br(e($cellMeeting->observations)) !!}
            </div>
        </div>
    @endif

    <div class="footer">
        Gerado em {{ date('d/m/Y H:i') }} pelo Portal Life Church - Gestão Eclesiástica
    </div>
</body>

</html>