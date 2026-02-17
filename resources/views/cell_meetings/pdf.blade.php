<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Acta de Encontro - {{ $cellMeeting->meeting_date->format('d/m/Y') }}</title>
    <style>
        @page { margin: 40px; }
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; line-height: 1.6; font-size: 11px; }
        .header { border-bottom: 3px solid #3b82f6; padding-bottom: 20px; margin-bottom: 30px; position: relative; }
        .logo { font-size: 24px; font-weight: 900; color: #1e3a8a; text-transform: uppercase; letter-spacing: 2px; }
        .doc-type { font-size: 14px; font-weight: 700; color: #64748b; margin-top: 5px; text-transform: uppercase; }
        
        .section { margin-bottom: 25px; }
        .section-title { font-size: 10px; font-weight: 900; color: #334155; text-transform: uppercase; letter-spacing: 2px; background: #f8fafc; padding: 8px 12px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #3b82f6; }
        
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-grid td { vertical-align: top; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .label { font-weight: 800; color: #94a3b8; font-size: 8px; text-transform: uppercase; margin-bottom: 2px; display: block; }
        .value { font-size: 12px; font-weight: 700; color: #0f172a; }
        
        .stats-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 16px; margin-bottom: 20px; }
        .stat-item { text-align: center; width: 24%; display: inline-block; }
        .stat-val { font-size: 22px; font-weight: 900; color: #3b82f6; }
        .stat-lbl { font-size: 8px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-top: 5px; }
        
        .participants-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .participants-table th { background: #f1f5f9; text-align: left; padding: 8px 12px; font-size: 9px; font-weight: 900; color: #475569; text-transform: uppercase; }
        .participants-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .role-badge { display: inline-block; font-size: 9px; font-weight: 800; color: #3b82f6; background: #eff6ff; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; }
        .status-badge { display: inline-block; font-size: 9px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; }
        .status-present { color: #059669; background: #ecfdf5; }
        .status-absent { color: #dc2626; background: #fef2f2; }

        .minutes-content { background: #fafafa; padding: 20px; border-radius: 12px; font-size: 11px; line-height: 1.8; color: #334155; border: 1px solid #f1f5f9; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 15px; }
        .page-break { page-break-after: always; }
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
                    <div class="label">EMISSÃO DO RELATÓRIO</div>
                    <div class="value" style="font-size: 16px;">{{ $cellMeeting->meeting_date->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Enquadramento Geral</div>
        <table class="info-grid">
            <tr>
                <td style="width: 60%;">
                    <div class="label">UNIDADE / NÚCLEO</div>
                    <div class="value">
                        @if($cellMeeting->cell)
                            {{ $cellMeeting->cell->name }}
                        @elseif($cellMeeting->zone)
                            {{ $cellMeeting->zone->name }}
                        @elseif($cellMeeting->supervision)
                            {{ $cellMeeting->supervision->name }}
                        @else
                            REUNIÃO GERAL
                        @endif
                    </div>
                </td>
                <td>
                    <div class="label">ESTRUTURA SUPERIOR</div>
                    <div class="value">
                        @if($cellMeeting->cell)
                            {{ $cellMeeting->cell->supervision->name }} ({{ $cellMeeting->cell->supervision->zone->name }})
                        @elseif($cellMeeting->supervision)
                            {{ $cellMeeting->supervision->zone->name ?? 'N/D' }}
                        @else
                            ESTRUTURA GERAL
                        @endif
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">RESPONSÁVEL / MINISTRANTE</div>
                    <div class="value">{{ $cellMeeting->leader->name }}</div>
                </td>
                <td>
                    <div class="label">TEMA CENTRAL</div>
                    <div class="value">{{ $cellMeeting->theme ?? 'Maturidade Cristã' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Métricas de Engajamento</div>
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
                <div class="stat-val" style="color: #1e3a8a;">{{ $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count }}</div>
                <div class="stat-lbl">Total Geral</div>
            </div>
        </div>
    </div>

    @if($cellMeeting->meeting_type !== 'normal' && $cellMeeting->participants->count() > 0)
        <div class="section">
            <div class="section-title">Participantes Oficiais</div>
            <table class="participants-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 60%;">NOME DO PARTICIPANTE</th>
                        <th style="text-align: right;">FUNÇÃO / CARGO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cellMeeting->participants as $index => $participant)
                        <tr>
                            <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td><span style="font-weight: 800;">{{ $participant->name }}</span></td>
                            <td style="text-align: right;"><span class="role-badge">{{ $participant->role }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($cellMeeting->meeting_type === 'normal' && $cellMeeting->attendances?->count() > 0)
        <div class="section">
            <div class="section-title">Rol de Membros da Célula</div>
            <table class="participants-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 60%;">NOME DO MEMBRO</th>
                        <th style="text-align: right;">PRESENÇA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cellMeeting->attendances as $index => $attendance)
                        <tr>
                            <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td><span style="font-weight: 700;">{{ $attendance->member->name ?? 'Membro' }}</span></td>
                            <td style="text-align: right;">
                                <span class="status-badge {{ $attendance->status ? 'status-present' : 'status-absent' }}">
                                    {{ $attendance->status ? 'PRESENTE' : 'AUSENTE' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($cellMeeting->visitors?->count() > 0)
        <div class="section">
            <div class="section-title">Visitantes e Convidados</div>
            <table class="participants-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 50%;">NOME</th>
                        <th style="width: 15%; text-align: center;">DECISÃO</th>
                        <th style="width: 25%; text-align: right;">OBSERVAÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cellMeeting->visitors as $index => $visitor)
                        <tr>
                            <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td><span style="font-weight: 700;">{{ $visitor->name }}</span></td>
                            <td style="text-align: center;">
                                @if($visitor->isIntegrated())
                                    <span style="color: #dc2626; font-weight: 800; font-size: 8px;">SIM</span>
                                @else
                                    <span style="color: #94a3b8; font-weight: 400; font-size: 8px;">NÃO</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-size: 9px; color: #64748b; font-style: italic;">
                                {{ $visitor->notes ? Str::limit($visitor->notes, 30) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="page-break"></div>

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
            <div class="section-title">Colheita e Novas Decisões</div>
            <div style="background: #fef2f2; padding: 15px; border-radius: 12px; border: 1px solid #fee2e2; color: #991b1b; font-weight: 700; font-size: 12px; font-style: italic;">
                "{!! nl2br(e($cellMeeting->decisions)) !!}"
            </div>
        </div>
    @endif

    @if($cellMeeting->observations)
        <div class="section">
            <div class="section-title">Parecer Pastoral / Administrativo</div>
            <div class="minutes-content" style="color: #475569; border: 1px dashed #cbd5e1;">
                {!! nl2br(e($cellMeeting->observations)) !!}
            </div>
        </div>
    @endif

    @if($cellMeeting->offering_amount > 0)
        <div class="section" style="text-align: right;">
            <div class="label" style="display: inline-block;">TOTAL DE OFERTAS ARRECADADAS:</div>
            <div class="value" style="font-size: 20px; color: #1e3a8a;">
                {{ number_format($cellMeeting->offering_amount, 2, ',', '.') }} MT
            </div>
        </div>
    @endif

    <div style="margin-top: 60px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 40%; border-top: 1px solid #334155; text-align: center; padding-top: 10px;">
                    <div class="label" style="color: #334155;">Assinatura do Responsável</div>
                    <div style="font-size: 10px; font-weight: 700;">{{ $cellMeeting->leader->name }}</div>
                </td>
                <td style="width: 20%;"></td>
                <td style="width: 40%; border-top: 1px solid #334155; text-align: center; padding-top: 10px;">
                    <div class="label" style="color: #334155;">Secretaria de Supervisão</div>
                    <div style="font-size: 8px; color: #94a3b8;">VALER SE CARIMBADO</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Documento gerado pelo Sistema de Gestão Life Church em {{ date('d/m/Y H:i') }}.
        <br>Integridade, Maturidade e Crescimento &copy; {{ date('Y') }}
    </div>
</body>
</html>