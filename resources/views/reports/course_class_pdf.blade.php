<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Relatório de Turma - {{ $courseClass->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-weight: bold;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-grid td {
            padding: 5px 0;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            border-left: 4px solid #2563eb;
            padding-left: 10px;
            margin-bottom: 15px;
            background: #f8fafc;
            padding: 5px 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #f1f5f9;
            text-align: left;
            padding: 10px;
            border: 1px solid #e2e8f0;
            font-weight: bold;
        }

        td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-aprovado {
            color: #166534;
            background: #dcfce7;
        }

        .status-reprovado {
            color: #991b1b;
            background: #fee2e2;
        }

        .status-cursando {
            color: #854d0e;
            background: #fef9c3;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PORTAL LIFE CHURCH</h1>
        <p>Relatório Oficial de Turma: {{ $courseClass->name }}</p>
    </div>

    <div class="section-title">Informações Gerais</div>
    <table class="info-grid">
        <tr>
            <td width="50%"><strong>Curso:</strong> {{ $courseClass->course->name }}</td>
            <td width="50%"><strong>Tipo:</strong> {{ ucfirst(str_replace('_', ' ', $courseClass->type)) }}</td>
        </tr>
        <tr>
            <td><strong>Professor(a):</strong> {{ $courseClass->teacherMale->name ?? 'N/A' }} /
                {{ $courseClass->teacherFemale->name ?? 'N/A' }}</td>
            <td><strong>Status:</strong> {{ ucfirst($courseClass->status) }}</td>
        </tr>
        <tr>
            <td><strong>Data de Início:</strong>
                {{ $courseClass->start_date ? $courseClass->start_date->format('d/m/Y') : 'N/A' }}</td>
            <td><strong>Data de Conclusão:</strong>
                {{ $courseClass->end_date ? $courseClass->end_date->format('d/m/Y') : 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Lista de Inscritos e Avaliações</div>
    <table>
        <thead>
            <tr>
                <th width="30%">Casal (Ele & Ela)</th>
                <th width="15%">Status</th>
                <th width="15%">Presença</th>
                <th width="20%">Recomendação</th>
                <th width="20%">Observações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courseClass->courseEnrollments as $enrollment)
                <tr>
                    <td>{{ $enrollment->malePartner->name ?? 'N/A' }} & {{ $enrollment->femalePartner->name ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ $enrollment->status }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </td>
                    <td>
                        {{ $enrollment->attendance_count }} Presenças<br>
                        <small>{{ $enrollment->absence_count }} Faltas</small>
                    </td>
                    <td>{{ $enrollment->recommendation ?? '---' }}</td>
                    <td>{{ $enrollment->notes ?? '---' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Gerado pelo Sistema Projeto Edificar em {{ now()->format('d/m/Y H:i') }} • Life Church
    </div>
</body>

</html>