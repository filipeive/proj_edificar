@extends('layouts.app')

@section('title', 'Dashboard Admin - Projeto Edificar')
@section('page-title', 'Dashboard Administrativo')
@section('page-subtitle', 'Visão geral de todas as contribuições')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Card: Total Arrecadado -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Arrecadado</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalContributed, 2, ',', '.') }} MT</p>
                <p class="text-xs text-gray-400 mt-2">Este mês</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <i class="bi bi-cash-coin text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Card: Membros Ativos -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Membros Ativos</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalMembers }}</p>
                <p class="text-xs text-gray-400 mt-2">Total na igreja</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-lg">
                <i class="bi bi-people-fill text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Card: Taxa de Participação -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Taxa de Participação</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $percentageContributed }}%</p>
                <p class="text-xs text-gray-400 mt-2">{{ $membersContributed }} / {{ $totalMembers }}</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <i class="bi bi-percent text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Card: Pendentes Verificação -->
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Pendentes</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $pendingContributions }}</p>
                <p class="text-xs text-gray-400 mt-2">Aguardando verificação</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <i class="bi bi-clock-history text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <a href="{{ route('zones.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
        <i class="bi bi-map text-3xl text-blue-600 mb-3"></i>
        <p class="font-bold text-gray-800">Zonas</p>
        <p class="text-sm text-gray-500">Gestão de zonas</p>
    </a>
    <a href="{{ route('cells.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
        <i class="bi bi-people-fill text-3xl text-green-600 mb-3"></i>
        <p class="font-bold text-gray-800">Células</p>
        <p class="text-sm text-gray-500">Gestão de células</p>
    </a>
    <a href="{{ route('users.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
        <i class="bi bi-person-badge text-3xl text-purple-600 mb-3"></i>
        <p class="font-bold text-gray-800">Utilizadores</p>
        <p class="text-sm text-gray-500">Gestão de membros</p>
    </a>
    <a href="{{ route('contributions.pending') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition text-center">
        <i class="bi bi-clock-history text-3xl text-orange-600 mb-3"></i>
        <p class="font-bold text-gray-800">Pendentes</p>
        <p class="text-sm text-gray-500">Verificar contribuições</p>
    </a>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Contribuições por Zona -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="bi bi-bar-chart mr-2"></i>Contribuições por Zona
        </h3>
        <canvas id="zoneChart"></canvas>
    </div>

    <!-- Top Células -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="bi bi-trophy mr-2"></i>Top 10 Células
        </h3>
        <div class="space-y-3">
            @foreach($topCells as $cell)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <div>
                    <p class="font-medium text-gray-800">{{ $cell['name'] }}</p>
                    <p class="text-xs text-gray-500">{{ $cell['contributed'] }}/{{ $cell['members'] }} membros</p>
                </div>
                <p class="font-bold text-green-600">{{ number_format($cell['total'], 2, ',', '.') }} MT</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Estatísticas Detalhadas -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="bi bi-file-earmark-pdf mr-2"></i>Relatórios
        </h3>
        <div class="space-y-2">
            <a href="{{ route('reports.global') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                Ver Relatório Global →
            </a>
            <a href="{{ route('reports.export.pdf', ['type' => 'global']) }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                Exportar PDF →
            </a>
            <a href="{{ route('reports.export.excel', ['type' => 'global']) }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                Exportar Excel →
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="bi bi-gear mr-2"></i>Administração
        </h3>
        <div class="space-y-2">
            <a href="{{ route('packages.index') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                Gerenciar Pacotes →
            </a>
            <a href="{{ route('supervisions.index') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                Gerenciar Supervisões →
            </a>
        </div>
    </div>

    <div class="bg-blue-50 rounded-lg shadow p-6 border border-blue-200">
        <h3 class="text-lg font-bold text-blue-800 mb-4">
            <i class="bi bi-info-circle mr-2"></i>Informações
        </h3>
        <p class="text-sm text-blue-700">
            Período: <strong>Dia 20 ao 5</strong><br>
            Próxima geração: <strong>05/{{ now()->addMonth()->format('m') }}</strong><br>
            Status: <strong>Ativo</strong>
        </p>
    </div>
</div>

<script>
    // Gráfico de Zonas
    const zoneCtx = document.getElementById('zoneChart').getContext('2d');
    new Chart(zoneCtx, {
        type: 'bar',
        data: {
            labels: @json($zoneStats->pluck('name')),
            datasets: [{
                label: 'Total Arrecadado (MT)',
                data: @json($zoneStats->pluck('total')),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(249, 115, 22, 0.8)',
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(34, 197, 94, 1)',
                    'rgba(168, 85, 247, 1)',
                    'rgba(249, 115, 22, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection