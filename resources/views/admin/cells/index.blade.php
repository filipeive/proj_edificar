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
                                    <span
                                        class="bg-gray-100 px-2 py-1 rounded-md">{{ $cell->supervision->name ?? 'Sem Supervisão' }}</span>
                                    @if($cell->supervision && $cell->supervision->zone)
                                        <span>•</span>
                                        <span>{{ $cell->supervision->zone->name }}</span>
                                    @endif
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
                        <div class="bg-gray-50 p-4 rounded-2xl">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Líder</p>
                            <p class="text-sm font-bold text-gray-700 truncate">
                                {{ $cell->leader->name ?? 'Não atribuído' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-2xl">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Membros</p>
                            <p class="text-sm font-bold text-gray-700">
                                {{ $cell->members->count() }} pessoas
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                        <div class="flex -space-x-2">
                            @foreach($cell->members->take(5) as $member)
                                <div class="w-8 h-8 rounded-full border-2 border-white bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-600"
                                    title="{{ $member->name }}">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                            @endforeach
                            @if($cell->members->count() > 5)
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                    +{{ $cell->members->count() - 5 }}
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('cells.show', $cell) }}"
                            class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                            Ver Dashboard →
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-[2.5rem] border border-gray-100 border-dashed">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="bi bi-people text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Nenhuma célula encontrada</h3>
                    <p class="text-gray-500 mb-6">Comece criando a primeira célula da igreja.</p>
                    <a href="{{ route('cells.create') }}"
                        class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors font-bold">
                        <i class="bi bi-plus-lg mr-2"></i>
                        Criar Célula
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($cells->hasPages())
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                {{ $cells->links() }}
            </div>
        @endif
    </div>
@endsection