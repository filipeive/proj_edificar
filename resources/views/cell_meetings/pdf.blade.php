<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Acta de Encontro - {{ $cellMeeting->meeting_date->format('d/m/Y') }}</title>
    <style>
        @page { margin: 40px; }
        body { font-family: 'Helvetica', sans-serif; color: #1a202c; line-height: 1.5; font-size: 11px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 25px; }
        .logo { font-size: 20px; font-weight: 900; color: #2563eb; text-transform: uppercase; letter-spacing: 1px; }
        .doc-type { font-size: 14px; font-weight: 700; color: #64748b; margin-top: 5px; }
        
        .section { margin-bottom: 20px; }
        .section-title { font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px; margin-bottom: 12px; }
        
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-grid td { vertical-align: top; padding: 5px 0; }
        .label { font-weight: 800; color: #64748b; font-size: 9px; text-transform: uppercase; display: block; }
        .value { font-size: 12px; font-weight: 700; color: #0f172a; }
        
        .stats-box { background: #f8fafc; border: 1px solid #f1f5f9; padding: 15px; border-radius: 12px; }
        .stat-item { text-align: center; width: 25%; display: inline-block; }
        .stat-val { font-size: 18px; font-weight: 900; color: #2563eb; }
        .stat-lbl { font-size: 8px; font-weight: 800; color: #64748b; text-transform: uppercase; }
        
        .minutes-content { background: #fff; border-left: 3px solid #e2e8f0; padding: 0 0 0 15px; font-size: 11px; text-align: justify; }
        .participants-list { font-size: 10px; font-weight: 700; color: #334155; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>

<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="logo">PORTAL LIFE CHURCH</div>
                    <div class="doc-type">
                        @switch($cellMeeting->meeting_type)
                            @case('leadership') ACTA DE REUNIÃO DE LIDERANÇA @break
                            @case('supervision') ACTA DE REUNIÃO DE SUPERVISÃO @break
                            @case('zone') ACTA DE REUNIÃO DE ZONA @break
                            @default RELATÓRIO DE ENCONTRO DE CÉLULA
                        @endswitch
                    </div>
                </td>
                <td style="text-align: right;">
                    <div class="label">DATA DO DOCUMENTO</div>
                    <div class="value">{{ $cellMeeting->meeting_date->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Informações Gerais</div>
        <table class="info-grid">
            <tr>
                <td style="width: 60%;">
                    <div class="label">UNIDADE / CÉLULA</div>
                    <div class="value">{{ $cellMeeting->cell->name }}</div>
                </td>
                <td>
                    <div class="label">SUPERVISÃO / ZONA</div>
                    <div class="value">{{ $cellMeeting->cell->supervision->name }} ({{ $cellMeeting->cell->supervision->zone->name }})</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">MINISTRANTE / RESPONSÁVEL</div>
                    <div class="value">{{ $cellMeeting->leader->name }}</div>
                </td>
                <td>
                    <div class="label">CONTEÚDO / TEMA</div>
                    <div class="value">{{ $cellMeeting->theme ?? 'Maturidade Cristã' }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($cellMeeting->meeting_type !== 'normal' && $cellMeeting->participants->count() > 0)
        <div class="section">
            <div class="section-title">Participantes Oficiais</div>
            <div class="participants-list">
                @foreach($cellMeeting->participants as $index => $participant)
                    {{ $participant->name }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </div>
        </div>
    @endif

    <div class="section">
        <div class="section-title">Dados de Participação</div>
        <div class="stats-box">
            <div class="stat-item">
                <div class="stat-val">{{ $cellMeeting->adults_count }}</div>
                <div class="stat-lbl">Adultos</div>
            </div>
            <div class="stat-item">
                <div class="stat-val">{{ $cellMeeting->children_count }}</div>
                <div class="stat-lbl">Crianças</div>
            </div>
            <div class="stat-item">
                <div class="stat-val">{{ $cellMeeting->visitors_count }}</div>
                <div class="stat-lbl">Visitantes</div>
            </div>
            <div class="stat-item">
                <div class="stat-val text-blue-600">{{ $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count }}</div>
                <div class="stat-lbl">Total Geral</div>
            </div>
        </div>
    </div>

    @if($cellMeeting->minutes)
        <div class="section">
            <div class="section-title">Acta do Encontro / Deliberações</div>
            <div class="minutes-content">
                {!! nl2br(e($cellMeeting->minutes)) !!}
            </div>
        </div>
    @endif

    @if($cellMeeting->decisions)
        <div class="section">
            <div class="section-title">Decisões e Conversões</div>
            <div class="value" style="font-weight: normal; color: #dc2626;">{{ $cellMeeting->decisions }}</div>
        </div>
    @endif

    @if($cellMeeting->observations)
        <div class="section">
            <div class="section-title">Observações Complementares</div>
            <div class="minutes-content" style="font-style: italic; color: #64748b;">
                {!! nl2br(e($cellMeeting->observations)) !!}
            </div>
        </div>
    @endif

    <div style="margin-top: 50px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 45%; border-top: 1px solid #1a202c; text-align: center; padding-top: 5px;">
                    <div class="label" style="color: #1a202c;">Assinatura do Responsável</div>
                    <div style="font-size: 9px;">{{ $cellMeeting->leader->name }}</div>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 45%; border-top: 1px solid #1a202c; text-align: center; padding-top: 5px;">
                    <div class="label" style="color: #1a202c;">Secretaria / Supervisão</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Este documento foi gerado electronicamente pelo Portal Life Church em {{ date('d/m/Y H:i') }}.
        <br>Gestão Eclesiástica Inteligente &copy; {{ date('Y') }}
    </div>
</body>
</html>