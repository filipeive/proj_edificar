@extends('layouts.app')

@section('title', 'Relatório Global - Projeto Edificar')
@section('page-title', 'Relatório Global')
@section('page-subtitle', 'Visão geral de todas as contribuições')

@section('content')
{{-- Formulário de Filtros --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">🔍 Filtrar Relatório</h3>
    <form action="{{ route('reports.global') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início</label>
            <input type="date" name="start_date" id="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700">Data de Fim</label>
            <input type="date" name="end_date" id="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label for="zone_id" class="block text-sm font-medium text-gray-700">Zona</label>
            <select name="zone_id" id="zone_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Todas as Zonas</option>
                @foreach($allZones as $zone)
                    <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                        {{ $zone->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Todos os Status</option>
                <option value="verificada" {{ request('status') == 'verificada' ? 'selected' : '' }}>Verificada</option>
                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="rejeitada" {{ request('status') == 'rejeitada' ? 'selected' : '' }}>Rejeitada</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 font-medium">Filtrar</button>
            <a href="{{ route('reports.global') }}" class="w-full text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 font-medium">Limpar</a>
        </div>
    </form>
</div>

{{-- Cards de Resumo --}}
<div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500 text-sm">Total Arrecadado (Filtro)</p>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($total, 2, ',', '.') }} MT</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500 text-sm">Período Selecionado</p>
        <p class="text-lg font-bold text-gray-800 mt-2">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Adicionamos request()->query() para manter os filtros na exportação --}}
        <a href="{{ route('reports.export.pdf', array_merge(['type' => 'global'], request()->query())) }}" 
            class="flex-1 bg-red-600 text-white px-4 py-3 rounded hover:bg-red-700 text-center font-medium">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
        <a href="{{ route('reports.export.excel', array_merge(['type' => 'global'], request()->query())) }}" 
            class="flex-1 bg-green-600 text-white px-4 py-3 rounded hover:bg-green-700 text-center font-medium">
            <i class="bi bi-file-excel"></i> Excel
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Contribuições por Zona</h3>
        <div class="space-y-3">
            @forelse($zoneStats as $stat)
            <div class="flex items-center justify-between pb-3 border-b">
                <p class="font-medium text-gray-800">{{ $stat['name'] }}</p>
                <p class="font-bold text-green-600">{{ number_format($stat['total'], 2, ',', '.') }} MT</p>
            </div>
            @empty
            <p class="text-gray-500">Nenhum dado encontrado para o filtro selecionado.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Lista de Contribuições (Filtro)</h3>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($contributions as $contribution)
            <div class="pb-3 border-b">
                <div class="flex justify-between items-center">
                    <p class="text-sm font-medium text-gray-800">{{ $contribution->user->name }}</p>
                    <span class="text-xs px-2 py-1 rounded-full
                        @if($contribution->status == 'verificada') bg-green-100 text-green-800
                        @elseif($contribution->status == 'pendente') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($contribution->status) }}
                    </span>
                </div>
                <div class="flex justify-between items-center mt-1">
                    <p class="text-xs text-gray-500">{{ $contribution->user->cell->name ?? 'N/A' }}</p>
                    <p class="font-bold text-green-600">{{ number_format($contribution->amount, 2, ',', '.') }} MT</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500">Nenhuma contribuição encontrada.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection