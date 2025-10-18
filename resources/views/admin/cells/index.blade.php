@extends('layouts.app')

@section('title', 'Gestão de Células - Projeto Edificar')
@section('page-title', 'Células')
@section('page-subtitle', 'Gestão de células da igreja')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    <!-- Filtros -->
    <div class="w-full md:w-auto">
        <form action="{{ route('cells.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Busca por texto -->
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" 
                            placeholder="Buscar por nome, líder ou zona..." 
                            class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            value="{{ request('search') }}">
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>
                </div>
                <!-- Filtros adicionais -->
                <div class="flex gap-2">
                    <select name="zone" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas as Zonas</option>
                        @foreach($zones ?? [] as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nome A-Z</option>
                        <option value="members" {{ request('sort') == 'members' ? 'selected' : '' }}>Qtd. Membros</option>
                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Mais Recentes</option>
                    </select>

                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="bi bi-funnel mr-2"></i>Filtrar
                    </button>
                </div>
            </div>

            @if(request()->hasAny(['search', 'zone', 'sort']))
                <div class="flex items-center gap-2">
                    <a href="{{ route('cells.index') }}" 
                        class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                        <i class="bi bi-x-circle mr-1"></i>Limpar Filtros
                    </a>
                    <span class="text-sm text-gray-400">
                        ({{ $cells->total() }} resultados encontrados)
                    </span>
                </div>
            @endif
        </form>
    </div>

    <!-- Ações -->
    <div class="mb-6 flex justify-between items-center" style="padding: 10px;">
    <p class="text-sm text-gray-600">Total de células: <strong>{{ $cells->count() }}</strong></p> &nbsp;&nbsp;
    <div class="flex gap-2">
        <a href="{{ route('cells.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="bi bi-plus-circle mr-2"></i>Nova Célula
        </a>
        <a href="{{ route('members.create') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="bi bi-person-plus mr-2"></i>Novo Membro
        </a>
        <a href="{{ route('contributions.create') }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="bi bi-cash-coin mr-2"></i>Registar Contribuição
        </a>
    </div>
</div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Célula</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Estrutura</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Líder</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase">Membros</th>
                    {{-- zona --}}
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Zona</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($cells as $cell)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i class="bi bi-people-fill text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $cell->name }}</p>
                                <p class="text-xs text-gray-500">ID: #{{ $cell->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-800 font-medium">{{ $cell->supervision->name }}</p>
                        <p class="text-xs text-gray-500">{{ $cell->supervision->zone->name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($cell->leader)
                            <a href="{{ route('users.show', $cell->leader) }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                {{ $cell->leader->name }}
                            </a>
                        @else
                            <span class="text-sm text-gray-400">- Sem líder -</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">
                            {{ $cell->members->count() }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-800 font-medium">{{ $cell->supervision->zone->name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('cells.show', $cell) }}" class="text-green-600 hover:text-green-800 font-medium text-sm" title="Ver Detalhes">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('cells.edit', $cell) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm" title="Editar">
                                <i class="bi bi-pencil text-lg"></i>
                            </a>
                            <form action="{{ route('cells.destroy', $cell) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja deletar esta célula?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm" title="Deletar">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="bi bi-inbox text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 font-medium">Nenhuma célula encontrada</p>
                            <a href="{{ route('cells.create') }}" class="mt-4 text-blue-600 hover:text-blue-800 text-sm">
                                Criar nova célula →
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="p-6 border-t border-gray-200">
        {{ $cells->links() }}
    </div>
</div>
@endsection