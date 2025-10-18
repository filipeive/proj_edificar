@extends('layouts.app')

@section('title', "Supervisão $supervision->name - Projeto Edificar")
@section('page-title', $supervision->name)
@section('page-subtitle', "Gestão da supervisão e suas células")

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <!-- Info Supervisão -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">SUPERVISÃO</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $supervision->name }}</p>
        <p class="text-xs text-gray-500 mt-2">
            <a href="{{ route('zones.show', $supervision->zone) }}" class="text-blue-600 hover:text-blue-800">
                {{ $supervision->zone->name }}
            </a>
        </p>
    </div>

    <!-- Total Células -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">CÉLULAS</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $supervision->cells->count() }}</p>
    </div>

    <!-- Total Membros -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">MEMBROS</h3>
        @php $memberCount = $supervision->cells->flatMap(function($c) { return $c->members()->where('is_active', true)->get(); })->unique('id')->count(); @endphp
        <p class="text-3xl font-bold text-blue-600">{{ $memberCount }}</p>
    </div>

    <!-- Total Arrecadado -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">TOTAL ESTE MÊS</h3>
        <p class="text-3xl font-bold text-green-600">{{ number_format($supervision->getTotalContributedThisMonth(), 2, ',', '.') }} MT</p>
    </div>
</div>

<!-- Células da Supervisão -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800">Células da Supervisão</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Líder</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membros</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Arrecadado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supervision->cells as $cell)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $cell->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $cell->leader?->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $cell->members()->where('is_active', true)->count() }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-green-600">{{ number_format($cell->getTotalContributedThisMonth(), 2, ',', '.') }} MT</td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('cells.show', $cell) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Ver →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Nenhuma célula nesta supervisão
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Ações Rápidas -->
    <div class="bg-white rounded-lg shadow p-6">
        <h4 class="font-bold text-gray-800 mb-4">Ações</h4>
        <div class="space-y-2">
            <a href="{{ route('supervisions.edit', $supervision) }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center text-sm font-medium">
                <i class="bi bi-pencil mr-2"></i>Editar
            </a>
            <a href="{{ route('supervisions.index') }}" class="block w-full bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 text-center text-sm font-medium">
                <i class="bi bi-arrow-left mr-2"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Resumo -->
    <div class="lg:col-span-2 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 border border-purple-200">
        <h4 class="font-bold text-gray-800 mb-4">
            <i class="bi bi-bar-chart mr-2"></i>Resumo Geral
        </h4>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded p-4">
                <p class="text-xs text-gray-500">Células</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ $supervision->cells->count() }}</p>
            </div>
            <div class="bg-white rounded p-4">
                <p class="text-xs text-gray-500">Membros</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $memberCount }}</p>
            </div>
            <div class="bg-white rounded p-4">
                <p class="text-xs text-gray-500">Zona</p>
                <p class="text-lg font-bold text-gray-600 mt-1">{{ $supervision->zone->name }}</p>
            </div>
            <div class="bg-white rounded p-4">
                <p class="text-xs text-gray-500">Criada em</p>
                <p class="text-lg font-bold text-gray-600 mt-1">{{ $supervision->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection