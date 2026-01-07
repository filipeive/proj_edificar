@extends('layouts.app')

@section('title', 'Gestão de Células - Portal Life Church')

    @section('content')
        <div class="space-y-8">
            <!-- Header -->
            <div
                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Células</h1>
                    <p class="text-gray-500">Gerencie as células, liderança e membros.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('members.create') }}"
                        class="group flex items-center bg-gray-50 text-gray-600 px-6 py-3 rounded-2xl hover:bg-gray-100 transition-all duration-300 font-bold">
                        <i class="bi bi-person-plus text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                        Novo Membro
                    </a>
                    <a href="{{ route('cells.create') }}"
                        class="group flex items-center bg-blue-600 text-white px-6 py-3 rounded-2xl hover:bg-blue-700 transition-all duration-300 font-bold shadow-lg shadow-blue-200">
                        <i class="bi bi-plus-circle text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                        Nova Célula
                    </a>
                </div>
            </div>

            <!-- Filters & Actions -->
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
                <form action="{{ route('cells.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar por nome, líder ou zona..."
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl transition-all font-medium text-gray-700 placeholder-gray-400">
                    </div>

                    <div class="w-full md:w-64">
                        <select name="zone"
                            class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl transition-all font-medium text-gray-700">
                            <option value="">Todas as Zonas</option>
                            @foreach($zones ?? [] as $zone)
                                <option value="{{ $zone->id }}" {{ request('zone') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="bg-gray-900 text-white px-8 py-3 rounded-xl hover:bg-black transition-colors font-bold">
                        Filtrar
                    </button>

                    @if(request()->hasAny(['search', 'zone']))
                        <a href="{{ route('cells.index') }}"
                            class="flex items-center justify-center px-4 py-3 text-red-500 bg-red-50 hover:bg-red-100 rounded-xl transition-colors font-bold">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-600 text-white p-6 rounded-[2rem] shadow-xl shadow-blue-200 relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-blue-100 font-bold uppercase tracking-widest text-xs mb-1">Total de Células</p>
                        <h3 class="text-4xl font-black">{{ $cells->total() }}</h3>
                    </div>
                    <i class="bi bi-grid-fill absolute -right-4 -bottom-4 text-9xl text-blue-500/30"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                @forelse($cells as $cell)
                    <div
                        class="group bg-white rounded-[2rem] p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-100 relative">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-black shadow-sm group-hover:scale-110 transition-transform duration-300">
                                    {{ substr($cell->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 leading-tight mb-1">{{ $cell->name }}</h3>
                                    <div class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                                        <span class="bg-gray-100 px-2 py-1 rounded-md">{{ $cell->supervision->name }}</span>
                                        <span>•</span>
                                        <span>{{ $cell->supervision->zone->name }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('cells.edit', $cell) }}"
                                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-blue-50 text-gray-400 hover:text-blue-600 flex items-center justify-center transition-colors"
                                    title="Editar">
                                    <i class="bi bi-pencil-fill text-xs"></i>
                                </a>
                                <form action="{{ route('cells.destroy', $cell) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors"
                                        title="Remover">
                                        <i class="bi bi-trash-fill text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Líder</p>
                                @if($cell->leader)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-bold">
                                            {{ substr($cell->leader->name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-700 text-sm truncate">{{ $cell->leader->name }}</span>
                                    </div>
                                @else
                                    <span class="text-red-400 text-xs font-bold italic">Sem líder</span>
                                @endif
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Membros</p>
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-people-fill text-blue-400 text-xs"></i>
                                    <span class="font-bold text-gray-700 text-sm">{{ $cell->members->count() }} ativos</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('cells.show', $cell) }}"
                            class="block w-full py-3 rounded-xl bg-gray-50 text-gray-600 font-bold text-center text-sm hover:bg-blue-600 hover:text-white transition-colors">
                            Ver Detalhes e Relatórios
                        </a>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                            <i class="bi bi-inbox text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Nada encontrado</h3>
                        <p class="text-gray-500">Tente ajustar seus filtros ou crie uma nova célula.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $cells->links() }}
            </div>
        </div>
    @endsection
    <form action="{{ route('cells.index') }}" method="GET" class="space-y-4">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Busca por texto -->
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" placeholder="Buscar por nome, líder ou zona..."
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
                <a href="{{ route('cells.index') }}" class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
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
            <a href="{{ route('cells.create') }}"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="bi bi-plus-circle mr-2"></i>Nova Célula
            </a>
            <a href="{{ route('members.create') }}"
                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="bi bi-person-plus mr-2"></i>Novo Membro
            </a>
            <a href="{{ route('contributions.create') }}"
                class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
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
                                    <a href="{{ route('users.show', $cell->leader) }}"
                                        class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
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
                                    <a href="{{ route('cells.show', $cell) }}"
                                        class="text-green-600 hover:text-green-800 font-medium text-sm" title="Ver Detalhes">
                                        <i class="bi bi-eye text-lg"></i>
                                    </a>
                                    <a href="{{ route('cells.edit', $cell) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium text-sm" title="Editar">
                                        <i class="bi bi-pencil text-lg"></i>
                                    </a>
                                    <form action="{{ route('cells.destroy', $cell) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Tem certeza que deseja deletar esta célula?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm"
                                            title="Deletar">
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
                                    <a href="{{ route('cells.create') }}"
                                        class="mt-4 text-blue-600 hover:text-blue-800 text-sm">
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