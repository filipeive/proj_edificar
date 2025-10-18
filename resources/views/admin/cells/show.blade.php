@extends('layouts.app')

@section('title', "Célula $cell->name - Projeto Edificar")
@section('page-title', $cell->name)
@section('page-subtitle', 'Gestão da célula e membros')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        {{-- Botao de Actualizar pagina --}}
        <div class="bg-white rounded-lg shadow p-6">
            <a href="{{ route('cells.show', $cell) }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center text-sm font-medium">
                <i class="bi bi-refresh mr-2"></i>Atualizar
            </a>
        </div>

        <!-- Info Célula -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">CÉLULA</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $cell->name }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $cell->supervision->name }}</p>
        </div>

        <!-- Líder -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">LÍDER</h3>
            @if ($cell->leader)
                <a href="{{ route('users.show', $cell->leader) }}"
                    class="text-lg font-bold text-blue-600 hover:text-blue-800">
                    {{ $cell->leader->name }}
                </a>
            @else
                <p class="text-lg text-gray-400">Sem líder</p>
            @endif
        </div>

        <!-- Total Membros -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">MEMBROS</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $cell->members()->where('is_active', true)->count() }}</p>
        </div>

        <!-- Total Arrecadado -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">TOTAL ESTE MÊS</h3>
            <p class="text-3xl font-bold text-green-600">
                {{ number_format($cell->getTotalContributedThisMonth(), 2, ',', '.') }} MT</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Membros -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="flex justify-between items-center" style="padding: 20px">
                <h3 class="text-lg font-bold text-gray-800">Membros da Célula</h3>
                <div class="flex gap-2">
                    <a href="{{ route('members.create') }}?cell_id={{ $cell->id }}"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-medium">
                        <i class="bi bi-plus-circle mr-2"></i>+ Novo Membro
                    </a>
                    <a href="{{ route('contributions.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                        <i class="bi bi-cash-coin mr-2"></i>Registar Contribuição
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pacote</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contribuição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cell->members()->where('is_active', true)->get() as $member)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $member->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $member->email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($member->getActiveCommitment())
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                            {{ $member->getActiveCommitment()->package->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-green-600">
                                    {{ number_format($member->getTotalContributedThisMonth(), 2, ',', '.') }} MT
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('users.show', $member) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                        Ver →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Nenhum membro nesta célula
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h4 class="font-bold text-gray-800 mb-4">Ações</h4>
                <div class="space-y-2">
                    <a href="{{ route('cells.edit', $cell) }}"
                        class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center text-sm font-medium">
                        <i class="bi bi-pencil mr-2"></i>Editar Célula
                    </a>
                    <a href="{{ route('contributions.index') }}?cell_id={{ $cell->id }}"
                        class="block w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-center text-sm font-medium">
                        <i class="bi bi-cash-coin mr-2"></i>Ver Contribuições
                    </a>
                    <a href="{{ route('cells.index') }}"
                        class="block w-full bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 text-center text-sm font-medium">
                        <i class="bi bi-arrow-left mr-2"></i>Voltar
                    </a>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border border-green-200">
                <h4 class="font-bold text-gray-800 mb-4">
                    <i class="bi bi-bar-chart mr-2"></i>Resumo
                </h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-3 border-b border-green-200">
                        <span class="text-sm text-gray-700">Membros Ativos</span>
                        <span
                            class="font-bold text-blue-600">{{ $cell->members()->where('is_active', true)->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-green-200">
                        <span class="text-sm text-gray-700">Contribuíram</span>
                        <span class="font-bold text-green-600">{{ $cell->getMembersContributedThisMonth() }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-green-200">
                        <span class="text-sm text-gray-700">Taxa</span>
                        <span class="font-bold text-purple-600">
                            {{ $cell->members()->where('is_active', true)->count() > 0 ? round(($cell->getMembersContributedThisMonth() / $cell->members()->where('is_active', true)->count()) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Total Arrecadado</span>
                        <span
                            class="font-bold text-green-600">{{ number_format($cell->getTotalContributedThisMonth(), 2, ',', '.') }}
                            MT</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
