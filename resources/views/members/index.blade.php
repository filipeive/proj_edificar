@extends('layouts.app')

@section('title', 'Lista de Membros')
@section('page-title', 'Gestão de Membros')

@section('page-subtitle')
    @if($userRole === 'lider_celula')
        Membros da sua célula
    @elseif($userRole === 'supervisor')
        Membros da sua supervisão
    @elseif($userRole === 'pastor_zona')
        Membros da sua zona
    @else
        Todos os membros da igreja
    @endif
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <!-- Filtros -->
    <form method="GET" action="{{ route('members.index') }}" class="flex-1 flex gap-3">
        <!-- Busca por nome/email -->
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Buscar por nome ou email..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Filtro por célula (se tiver múltiplas) -->
        @if($userRole !== 'lider_celula' && $availableCells->count() > 1)
            <select name="cell_id" 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Todas as Células</option>
                @foreach($availableCells as $cell)
                    <option value="{{ $cell->id }}" 
                        {{ request('cell_id') == $cell->id ? 'selected' : '' }}>
                        {{ $cell->name }}
                        @if($cell->supervision)
                            ({{ $cell->supervision->name }})
                        @endif
                    </option>
                @endforeach
            </select>
        @endif

        <button type="submit" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="bi bi-search mr-2"></i>Buscar
        </button>

        @if(request('search') || request('cell_id'))
            <a href="{{ route('members.index') }}" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="bi bi-x-circle mr-2"></i>Limpar
            </a>
        @endif
    </form>

    <!-- Botão Novo Membro -->
    <a href="{{ route('members.create') }}" 
        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold whitespace-nowrap">
        <i class="bi bi-person-plus-fill mr-2"></i>Novo Membro
    </a>
</div>

<!-- Estatísticas rápidas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="bi bi-people-fill text-2xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total de Membros</p>
                <p class="text-2xl font-bold text-gray-800">{{ $members->total() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
                <i class="bi bi-check-circle-fill text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Membros Ativos</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ $members->where('is_active', true)->count() }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
                <i class="bi bi-grid-3x3-gap-fill text-2xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">
                    @if($userRole === 'lider_celula')
                        Sua Célula
                    @elseif($userRole === 'supervisor')
                        Células
                    @else
                        Total de Células
                    @endif
                </p>
                <p class="text-2xl font-bold text-gray-800">{{ $availableCells->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="bi bi-handshake-fill text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Com Compromisso</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ $members->whereNotNull('commitments')->count() }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Membros -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-5 border-b border-gray-200">
        <h4 class="text-lg font-semibold text-gray-800">
            Lista de Membros ({{ $members->total() }})
        </h4>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Membro
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Contacto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Hierarquia
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Status
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        Compromisso
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($members as $member)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold text-lg">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $member->id }}</p>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900">{{ $member->email }}</p>
                        <p class="text-xs text-gray-500">
                            <i class="bi bi-telephone mr-1"></i>
                            {{ $member->phone ?? 'Sem telefone' }}
                        </p>
                    </td>

                    <td class="px-6 py-4">
                        @if($member->cell)
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">
                                    <i class="bi bi-people text-blue-500 mr-1"></i>
                                    {{ $member->cell->name }}
                                </p>
                                @if($member->cell->supervision)
                                    <p class="text-xs text-gray-500">
                                        <i class="bi bi-diagram-3 mr-1"></i>
                                        {{ $member->cell->supervision->name }}
                                    </p>
                                @endif
                                @if($member->cell->supervision && $member->cell->supervision->zone)
                                    <p class="text-xs text-gray-400">
                                        <i class="bi bi-map mr-1"></i>
                                        {{ $member->cell->supervision->zone->name }}
                                    </p>
                                @endif
                            </div>
                        @else
                            <span class="text-xs text-red-500 font-medium">Sem célula</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($member->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                Inativo
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center">
                        @php
                            $activeCommitment = $member->commitments->where('end_date', null)->first();
                        @endphp
                        @if($activeCommitment)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                {{ $activeCommitment->package->name ?? 'Sim' }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">Nenhum</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('members.show', $member) }}" 
                                class="text-blue-600 hover:text-blue-800 transition text-sm font-medium"
                                title="Ver detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('members.edit', $member) }}" 
                                class="text-orange-600 hover:text-orange-800 transition text-sm font-medium"
                                title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('members.destroy', $member) }}" method="POST" class="inline"
                                onsubmit="return confirm('Deseja realmente remover este membro?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="text-red-600 hover:text-red-800 transition text-sm font-medium"
                                    title="Remover">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <i class="bi bi-people text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg font-medium">Nenhum membro encontrado</p>
                        <p class="text-gray-400 text-sm mt-2">
                            @if(request('search'))
                                Tente buscar com outros termos
                            @else
                                Comece criando um novo membro
                            @endif
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginação -->
    @if($members->hasPages())
        <div class="p-5 border-t">
            {{ $members->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection