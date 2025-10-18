@extends('layouts.app')

@section('title', 'Dashboard Pastor - Projeto Edificar')
@section('page-title', 'Dashboard do Pastor de Zona')
@section('page-subtitle', 'Visão geral da zona')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Zona Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">ZONA</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $zoneName }}</p>
        <p class="text-sm text-gray-500 mt-2">Total de supervisões: {{ $supervisions->count() }}</p>
    </div>

    <!-- Total Arrecadado -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">TOTAL ESTE MÊS</h3>
        <p class="text-3xl font-bold text-green-600">{{ number_format($total, 2, ',', '.') }} MT</p>
    </div>

    <!-- Meta vs Realizado -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">DESEMPENHO</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $supervisions->count() }}</p>
        <p class="text-xs text-gray-500 mt-2">Supervisões ativas</p>
    </div>
</div>

<!-- Supervisões da Zona -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800">Supervisões da Zona</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisão</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Células</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($supervisions as $supervision)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $supervision['name'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $supervision['cells'] }}</td>
                    <td class="px-6 py-4 font-bold text-green-600">{{ number_format($supervision['total'], 2, ',', '.') }} MT</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                <tr>
                    <td colspan="2" class="px-6 py-4 font-bold text-gray-800">TOTAL DA ZONA</td>
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
            <a href="{{ route('reports.zone') }}" class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center text-sm">
                <i class="bi bi-file-earmark-pdf mr-2"></i>Relatório da Zona
            </a>
            <a href="{{ route('contributions.index') }}" class="block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 text-center text-sm">
                <i class="bi bi-cash-coin mr-2"></i>Minhas Contribuições
            </a>
        </div>
    </div>

    <div class="bg-purple-50 rounded-lg shadow p-6 border border-purple-200">
        <h3 class="text-lg font-bold text-purple-800 mb-4">
            <i class="bi bi-info-circle mr-2"></i>Status
        </h3>
        <p class="text-sm text-purple-700">
            A zona <strong>{{ $zoneName }}</strong> tem <strong>{{ $supervisions->count() }}</strong> 
            supervisão(ões) ativa(s).
        </p>
    </div>
</div>
@endsection
