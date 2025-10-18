@extends('layouts.app')

@section('title', 'Dashboard Supervisor - Projeto Edificar')
@section('page-title', 'Dashboard do Supervisor')
@section('page-subtitle', 'Monitorize suas supervisões')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Supervisão Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">SUPERVISÃO</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $supervisionName }}</p>
        <p class="text-sm text-gray-500 mt-2">Total de células: {{ $cells->count() }}</p>
    </div>

    <!-- Total Arrecadado -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">TOTAL ESTE MÊS</h3>
        <p class="text-3xl font-bold text-green-600">{{ number_format($total, 2, ',', '.') }} MT</p>
    </div>

    <!-- Taxa Participação -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">DESEMPENHO GERAL</h3>
        <p class="text-3xl font-bold text-blue-600">
            {{ $cells->count() > 0 ? round($cells->sum('contributed') / ($cells->sum('members') > 0 ? $cells->sum('members') : 1) * 100, 1) : 0 }}%
        </p>
    </div>
</div>

<!-- Células sob Supervisão -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800">Células da Supervisão</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Célula</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Líder</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membros</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contribuições</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cells as $cell)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $cell['name'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $cell['leader'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $cell['members'] }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            {{ $cell['contributed'] }}/{{ $cell['members'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-green-600">{{ number_format($cell['total'], 2, ',', '.') }} MT</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                <tr>
                    <td colspan="4" class="px-6 py-4 font-bold text-gray-800">TOTAL</td>
                    <td class="px-6 py-4 font-bold text-green-600">{{ number_format($total, 2, ',', '.') }} MT</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Ações Rápidas</h3>
        <div class="space-y-2">
            <a href="{{ route('reports.supervision') }}" class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center text-sm">
                <i class="bi bi-file-earmark-pdf mr-2"></i>Ver Relatório
            </a>
            <a href="{{ route('contributions.index') }}" class="block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 text-center text-sm">
                <i class="bi bi-cash-coin mr-2"></i>Minhas Contribuições
            </a>
        </div>
    </div>

    <div class="bg-blue-50 rounded-lg shadow p-6 border border-blue-200">
        <h3 class="text-lg font-bold text-blue-800 mb-4">
            <i class="bi bi-info-circle mr-2"></i>Informações
        </h3>
        <p class="text-sm text-blue-700">
            Você está visualizando <strong>{{ $cells->count() }}</strong> célula(s) com 
            <strong>{{ $cells->sum('members') }}</strong> membros ativos no total.
        </p>
    </div>
</div>
@endsection