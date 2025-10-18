@extends('layouts.app')

@section('title', 'Relatório da Zona - Projeto Edificar')
@section('page-title', 'Relatório da Zona')
@section('page-subtitle', $zone ? $zone->name : 'Selecione uma zona para começar')

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">🔍 Filtrar Relatório</h3>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        @if(auth()->user()->role === 'admin')
        <div class="lg:col-span-1">
            <label for="zone_id" class="block text-sm font-medium text-gray-700">Selecionar Zona</label>
            <select name="zone_id" id="zone_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                <option value="">-- Escolha uma zona --</option>
                @foreach($allZones as $z)
                <option value="{{ $z->id }}" @if($zone && $zone->id == $z->id) selected @endif>{{ $z->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700">Data de Fim</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 font-medium">Filtrar</button>
        </div>
    </form>
</div>

@if($zone)
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Arrecadado (Verificado)</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($total, 2, ',', '.') }} MT</p>
                <p class="text-sm text-gray-600 mt-2">Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="#" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 font-medium"><i class="bi bi-file-pdf"></i> PDF</a>
                <a href="#" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 font-medium"><i class="bi bi-file-excel"></i> Excel</a>
            </div>
        </div>
    </div>
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membro</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisão</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Célula</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($contributions as $contribution)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $contribution->user->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $contribution->user->cell?->supervision?->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $contribution->user->cell?->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($contribution->status == 'verificada') bg-green-100 text-green-800
                        @elseif($contribution->status == 'pendente') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($contribution->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800 text-right">{{ number_format($contribution->amount, 2, ',', '.') }} MT</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Nenhuma contribuição encontrada para este período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@else
    @if(auth()->user()->role === 'admin')
    <div class="text-center py-12 bg-white rounded-lg shadow">
        <i class="bi bi-info-circle text-4xl text-blue-500"></i>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma zona selecionada</h3>
        <p class="mt-1 text-sm text-gray-500">Por favor, escolha uma zona no filtro acima para visualizar o relatório.</p>
    </div>
    @endif
@endif
@endsection