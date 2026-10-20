@extends('layouts.app')

@section('title', 'Gestão de Zonas - Portal Life Church')
@section('page-title', 'Zonas Pastorais')
@section('page-subtitle', 'Divisão territorial e estratégica para o crescimento da igreja.')

@section('header-actions')
    @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria())
        <div class="md:hidden">
            <a href="{{ route('zones.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-orange-700 p-2 text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-plus-lg text-base"></i>
            </a>
        </div>
    @endif
@endsection

@section('content')
    <div class="space-y-5" x-data="{ 
        view: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('zones_view') || 'list'),
        selected: [],
        search: '{{ request('search') }}',
        updateView() {
            if (window.innerWidth < 768 && this.view === 'list') {
                this.view = 'grid';
            }
        },
        toggleAll() {
            const allIds = {{ Js::from($zones->pluck('id')) }};
            if (this.selected.length === allIds.length) {
                this.selected = [];
            } else {
                this.selected = allIds;
            }
        },
        deleteSelected() {
            document.getElementById('bulk-delete-form').submit();
        }
    }" x-init="$watch('view', value => localStorage.setItem('zones_view', value))"
        @resize.window.debounce.500ms="updateView()">

        <!-- Bulk Action Bar -->
        <div x-show="selected.length > 0" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
            <div class="bg-gray-900 text-white rounded-xl shadow-lg p-3.5 flex items-center gap-4 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                <div class="flex items-center gap-2 pl-1">
                    <span class="bg-orange-700 text-xs font-black px-2 py-0.5 rounded-md" x-text="selected.length"></span>
                    <span class="text-xs font-medium">selecionados</span>
                </div>
                <div class="h-6 w-px bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <button @click="selected = []"
                        class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button
                        @click="if(confirm('Deseja excluir as ' + selected.length + ' zonas selecionadas?')) deleteSelected()"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-sm flex items-center gap-1.5">
                        <i class="bi bi-trash-fill"></i> Excluir
                    </button>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Território & Expansão</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Zonas Pastorais</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Divisão territorial e estratégica para o crescimento da igreja.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- View Switcher --}}
                <div class="hidden md:inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
                    <button type="button" @click="view = 'list'"
                        :class="view === 'list' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold transition">
                        <i class="bi bi-list-ul"></i>
                        <span>Lista</span>
                    </button>
                    <button type="button" @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold transition">
                        <i class="bi bi-grid"></i>
                        <span>Cards</span>
                    </button>
                </div>

                @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria())
                    <div class="hidden md:flex gap-2">
                        <a href="{{ route('zones.create') }}"
                            class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm transition">
                            <i class="bi bi-plus-lg"></i>
                            <span>Nova Zona</span>
                        </a>
                    </div>
                @endif
            </div>
        </header>

        <!-- Toolbar / Search -->
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="w-full sm:max-w-md relative">
                <form action="{{ route('zones.index') }}" method="GET" class="contents">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Pesquisar zonas ou pastores..."
                        class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs sm:text-sm font-medium text-gray-900 focus:bg-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </form>
            </div>

            <div class="text-xs font-semibold text-gray-500 self-start sm:self-center">
                {{ $zones->total() }} zonas registadas
            </div>
        </div>

        <form id="bulk-delete-form" action="{{ route('zones.bulk-destroy') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
            <template x-for="id in selected">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </form>

        <!-- Grid View -->
        <template x-if="view === 'grid'">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @forelse($zones as $zone)
                    <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-orange-300 transition-all flex flex-col justify-between"
                        :class="{'ring-2 ring-orange-500 border-orange-500': selected.includes({{ $zone->id }})}">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <input type="checkbox" :value="{{ $zone->id }}" x-model="selected"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                                    <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-200/60 text-orange-700 flex items-center justify-center text-xs font-black shrink-0 relative">
                                        {{ substr($zone->name, 0, 1) }}
                                        @if($zone->latitude && $zone->longitude)
                                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-500 rounded-full border border-white" title="Mapeada"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-black text-gray-900 truncate">{{ $zone->name }}</h3>
                                        <span class="text-[10px] font-bold text-orange-700 uppercase tracking-wider block truncate">
                                            {{ $zone->pastor->name ?? 'Pr. Geral' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <a href="{{ route('zones.edit', $zone) }}" title="Editar"
                                        class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1 text-xs text-gray-600 hover:border-orange-300 hover:text-orange-700 transition">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($zone->supervisions->count() === 0)
                                        <form action="{{ route('zones.destroy', $zone) }}" method="POST" id="delete-form-{{ $zone->id }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $zone->id }}', 'Deseja excluir esta zona?')" title="Eliminar"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1 text-xs text-red-600 hover:bg-red-50 transition">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 rounded-lg bg-gray-50 p-2 text-center text-xs mb-3">
                                <div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">Sup.</span>
                                    <strong class="text-xs font-black text-gray-900 tabular-nums">{{ $zone->supervisions->count() }}</strong>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">Células</span>
                                    <strong class="text-xs font-black text-gray-900 tabular-nums">{{ $zone->getTotalCells() }}</strong>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">Membros</span>
                                    <strong class="text-xs font-black text-gray-900 tabular-nums">{{ $zone->getTotalMembers() }}</strong>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs px-1 mb-2">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Contr. Mês:</span>
                                <span class="font-black text-emerald-700 tabular-nums">{{ number_format($zone->getTotalContributedThisMonth(), 0, ',', '.') }} MT</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-gray-100">
                            <a href="{{ route('zones.show', $zone) }}"
                                class="inline-flex items-center justify-center w-full gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:border-orange-300 hover:text-orange-800 hover:bg-orange-50/50 shadow-sm transition">
                                <i class="bi bi-eye text-orange-700"></i>
                                <span>Ver Zona</span>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-xl border border-gray-200 bg-white p-10 text-center text-gray-500">
                        <i class="bi bi-geo-alt text-2xl text-gray-300 block mb-2"></i>
                        Nenhuma zona cadastrada.
                    </div>
                @endforelse
            </div>
        </template>

        <!-- List View -->
        <template x-if="view === 'list'">
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-3.5 py-3 text-center w-9">
                                <input type="checkbox" @click="toggleAll()"
                                    :checked="selected.length === {{ $zones->count() }} && {{ $zones->count() }} > 0"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-4 py-3">Zona</th>
                            <th class="px-4 py-3">Pastor Coordenador</th>
                            <th class="px-4 py-3 text-center">Supervisões</th>
                            <th class="px-4 py-3 text-center">Células</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($zones as $zone)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-3.5 py-3 text-center">
                                    <input type="checkbox" :value="{{ $zone->id }}" x-model="selected"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('zones.show', $zone) }}" class="font-bold text-gray-900 hover:text-orange-700 hover:underline">
                                        {{ $zone->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-gray-800">{{ $zone->pastor->name ?? 'Geral' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center tabular-nums font-bold text-gray-900">
                                    {{ $zone->supervisions->count() }}
                                </td>
                                <td class="px-4 py-3 text-center tabular-nums font-bold text-gray-900">
                                    {{ $zone->getTotalCells() }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <a href="{{ route('zones.show', $zone) }}" title="Detalhes"
                                            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:border-orange-300 hover:text-orange-800 hover:bg-orange-50/50 shadow-sm transition">
                                            <i class="bi bi-eye text-orange-700"></i> <span>Ver</span>
                                        </a>
                                        <a href="{{ route('zones.edit', $zone) }}" title="Editar"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-gray-600 hover:border-orange-300 hover:text-orange-700 transition">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($zone->supervisions->count() === 0)
                                            <form action="{{ route('zones.destroy', $zone) }}" method="POST" id="list-delete-form-{{ $zone->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="confirmDelete('list-delete-form-{{ $zone->id }}', 'Deseja excluir esta zona?')"
                                                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-red-600 hover:bg-red-50 transition"
                                                    title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </template>

        @if($zones->hasPages())
            <div class="pt-2">
                {{ $zones->links() }}
            </div>
        @endif
    </div>
@endsection
