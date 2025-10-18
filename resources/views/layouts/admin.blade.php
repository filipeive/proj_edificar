@extends('layouts.app')

@section('title', 'Dashboard Admin - Projeto Edificar')
@section('page-title', 'Dashboard Administrativo')
@section('page-subtitle', 'Visão geral de todas as contribuições')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Card: Total Arrecadado -->
    <div class="bg-white rounded-lg shadow p-6">
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
    <div class="bg-white rounded-lg shadow p-6">
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
    <div class="bg-white rounded-lg shadow p-6">
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
    <div class="bg-white rounded-lg shadow p-6">
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

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Contribuições por Zona -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Contribuições por Zona</h3>
        <canvas id="zoneChart"></canvas>
    </div>

    <!-- Top Células -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Top 10 Células</h3>
        <div class="space-y-3">
            @foreach($topCells as $cell)
            <div class="flex items-center justify-between">
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
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
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