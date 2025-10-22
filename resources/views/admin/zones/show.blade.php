@extends('layouts.app')

@section('title', "Zona $zone->name - Projeto Edificar")
@section('page-title', $zone->name)
@section('page-subtitle', "Gestão detalhada da zona e suas supervisões")

@section('content')
@php
    // Cálculo do total de células (para evitar o erro, usamos a lógica corrigida)
    // Usar o método getTotalCells() (que agora não tem o filtro 'is_active' em Cell)
    $cellCount = $zone->getTotalCells();
    $memberCount = $zone->getTotalMembers();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card Principal: Pastor e Descrição -->
    <div class="lg:col-span-1 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-xl p-6 border-t-4 border-blue-600">
        <h3 class="text-lg font-bold text-gray-800 mb-3">Pastor Responsável</h3>
        <div class="flex items-center space-x-3">
            <i class="bi bi-person-circle text-4xl text-blue-600"></i>
            <div>
                <p class="font-bold text-gray-900">{{ $zone->pastor->name ?? 'Ninguém atribuído' }}</p>
                <p class="text-sm text-gray-500">{{ $zone->pastor ? $zone->pastor->email : '-' }}</p>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-600">Descrição:</p>
            <p class="text-sm text-gray-800 italic">{{ $zone->description ?? 'Nenhuma descrição fornecida.' }}</p>
        </div>
    </div>

    <!-- Indicadores de Performance -->
    <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-600">
            <h3 class="text-sm font-medium text-gray-500 mb-2">TOTAL SUPERVISÕES</h3>
            <p class="text-3xl font-extrabold text-blue-800">{{ $zone->supervisions->count() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-600">
            <h3 class="text-sm font-medium text-gray-500 mb-2">TOTAL CÉLULAS</h3>
            <p class="text-3xl font-extrabold text-purple-800">{{ $cellCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Total de membros: {{ $memberCount }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-600">
            <h3 class="text-sm font-medium text-gray-500 mb-2">TOTAL ARRECADADO (Mês)</h3>
            <p class="text-3xl font-extrabold text-green-800">{{ number_format($zone->getTotalContributedThisMonth(), 2, ',', '.') }} MT</p>
            <p class="text-xs text-gray-500 mt-1">Contribuições verificadas neste mês.</p>
        </div>
    </div>
</div>

<!-- Tabela de Supervisões da Zona -->
<div class="bg-white rounded-xl shadow-xl mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Supervisões Ativas ({{ $zone->supervisions->count() }})</h3>
        <a href="{{ route('supervisions.create', ['zone_id' => $zone->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
             <i class="bi bi-plus-circle mr-1"></i> Adicionar Supervisão
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Células</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Arrecadado (Mês)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse($zone->supervisions as $supervision)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">
                        <i class="bi bi-diagram-3-fill text-blue-400 mr-2"></i> {{ $supervision->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-center text-gray-600">{{ $supervision->cells->count() }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-center text-green-600">{{ number_format($supervision->getTotalContributedThisMonth(), 2, ',', '.') }} MT</td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('supervisions.show', $supervision) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Ver Detalhes →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        Nenhuma supervisão registrada nesta zona.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Ações da Zona -->
<div class="flex justify-end space-x-4">
    <a href="{{ route('zones.index') }}" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-bold shadow-md">
        <i class="bi bi-arrow-left mr-2"></i>Voltar à Lista
    </a>
    <a href="{{ route('zones.edit', $zone) }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition font-bold shadow-md">
        <i class="bi bi-pencil mr-2"></i>Editar Informações
    </a>
</div>
@endsection
