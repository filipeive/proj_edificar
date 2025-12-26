<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Ficha de Célula - {{ $cell->name }}</title>
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

        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-grid td {
            padding: 10px 0;
            vertical-align: top;
        }

        .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .value {
            font-size: 13px;
            color: #111827;
            font-weight: 500;
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

        table.member-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.member-table th {
            text-align: left;
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        table.member-table td {
            padding: 12px 10px;
            font-size: 12px;
            border-bottom: 1px solid #f3f4f6;
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
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-blue {
            background-color: #eff6ff;
            color: #1d4ed8;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Portal Life Church</h1>
        <p>Ficha Oficial de Célula</p>
    </div>

    <div class="content">
        <div class="stats-box">
            <table class="stats-grid">
                <tr>
                    <td class="stats-item">
                        <div class="stats-value">{{ $cell->name }}</div>
                        <div class="stats-label">Nome da Célula</div>
                    </td>
                    <td class="stats-item">
                        <div class="stats-value">{{ $cell->members->count() }}</div>
                        <div class="stats-label">Total de Membros</div>
                    </td>
                    <td class="stats-item">
                        <div class="stats-value">{{ $cell->supervision->name ?? 'N/A' }}</div>
                        <div class="stats-label">Supervisão</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title">Informações de Liderança</div>
        <table class="info-grid">
            <tr>
                <td width="50%">
                    <div class="label">Líder de Célula</div>
                    <div class="value">{{ $cell->leader->name ?? 'Não atribuído' }}</div>
                </td>
                <td width="50%">
                    <div class="label">Pastor de Zona</div>
                    <div class="value">{{ $cell->supervision->zone->pastor->name ?? 'Não atribuído' }}</div>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <div class="label">Dia de Reunião</div>
                    <div class="value">
                        @php
                            $days = [
                                'monday' => 'Segunda-feira',
                                'tuesday' => 'Terça-feira',
                                'wednesday' => 'Quarta-feira',
                                'thursday' => 'Quinta-feira',
                                'friday' => 'Sexta-feira',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo'
                            ];
                        @endphp
                        {{ $days[$cell->meeting_day] ?? $cell->meeting_day }}
                </td>
                </td>
                <td width="50%">
                    <div class="label">Horário</div>
                    <div class="value">{{ $cell->meeting_time ?? 'Não informado' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="label">Endereço da Reunião</div>
                    <div class="value">{{ $cell->address ?? 'Não informado' }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Lista de Membros Ativos</div>
        <table class="member-table">
            <thead>
                <tr>
                    <th width="40%">Nome Completo</th>
                    <th width="30%">Telefone</th>
                    <th width="30%">Data de Nascimento</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cell->members as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->phone ?? 'N/A' }}</td>
                        <td>{{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->format('d/m/Y') : 'N/A' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #9ca3af; padding: 30px;">Nenhum membro registrado
                            nesta célula.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($cell->description)
            <div class="section-title">Observações / Histórico</div>
            <div class="value" style="line-height: 1.6; background-color: #f9fafb; padding: 15px; border-radius: 8px;">
                {{ $cell->description }}
            </div>
        @endif
    </div>

    <div class="footer">
        Documento gerado pelo Portal Life Church em {{ now()->format('d/m/Y H:i') }}.
    </div>
</body>

</html>