@extends('layouts.app')

@section('title', 'Gestão de Supervisões - Portal Life Church')
@section('page-title', 'Gestão de Supervisões')
@section('page-subtitle', 'Administração de níveis de supervisão e cobertura ministerial')

@section('header-actions')
    <div class="md:hidden">
        <a href="{{ route('supervisions.create') }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
            <i class="bi bi-plus-circle-fill text-2xl"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8" x-data="{ 
                            view: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('supervisions_view') || 'grid'),
                            selected: [],
                            updateView() {
                                if (window.innerWidth < 768 && this.view === 'list') {
                                    this.view = 'grid';
                                }
                            },
                            toggleAll() {
                                const allIds = {{ Js::from($supervisions->pluck('id')) }};
                                if (this.selected.length === allIds.length) {
                                    this.selected = [];
                                } else {
                                    this.selected = allIds;
                                }
                            },
                            deleteSelected() {
                                document.getElementById('bulk-delete-form').submit();
                            }
                        }" x-init="$watch('view', value => localStorage.setItem('supervisions_view', value))"
        @resize.window.debounce.500ms="updateView()">

        <!-- Bulk Action Bar -->
        <div x-show="selected.length > 0" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
            <div
                class="bg-gray-900 text-white rounded-2xl shadow-2xl p-4 flex items-center gap-6 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                <div class="flex items-center gap-3 pl-2">
                    <span class="bg-blue-600 text-xs font-black px-2.5 py-1 rounded-lg" x-text="selected.length"></span>
                    <span class="text-sm font-medium">selecionados</span>
                </div>
                <div class="h-8 w-px bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <button @click="selected = []"
                        class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button
                        @click="if(confirm('Deseja excluir as ' + selected.length + ' supervisões selecionadas?')) deleteSelected()"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
                        <i class="bi bi-trash-fill"></i> Excluir
                    </button>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div
            class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col lg:flex-row justify-between items-center gap-6 transition-all">
            <div class="w-full lg:max-w-md relative group">
                <form action="{{ route('supervisions.index') }}" method="GET" class="contents">
                    <i
                        class="bi bi-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                    <input type="text" name="search" data-live-search="manual" value="{{ request('search') }}"
                        placeholder="Pesquisar supervisão, zona ou supervisor..."
                        class="w-full pl-14 pr-6 py-4 bg-gray-50/50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all"
                        @input.debounce.500ms="$el.form.submit()">
                </form>
            </div>

            <div class="flex flex-wrap items-center justify-center md:justify-end gap-4 w-full lg:w-auto">
                {{-- View Switcher --}}
                <div class="flex bg-gray-100/50 p-1.5 rounded-2xl border border-gray-100">
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-grid-fill text-sm"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Cards</span>
                    </button>
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-list-ul text-sm"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Lista</span>
                    </button>
                </div>

                <a href="{{ route('supervisions.create') }}"
                    class="group flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200">
                    <i class="bi bi-plus-circle text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                    Nova Supervisão
                </a>
            </div>
        </div>

        <form id="bulk-delete-form" action="{{ route('supervisions.bulk-destroy') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
            <template x-for="id in selected">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </form>

        <!-- Grid View -->
        <template x-if="view === 'grid'">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($supervisions as $supervision)
                    <div class="group bg-white rounded-[2rem] p-7 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-100 relative overflow-hidden compact-card"
                        :class="{'ring-2 ring-blue-500 bg-blue-50/10': selected.includes({{ $supervision->id }})}">

                        <div class="absolute top-4 left-4 z-20">
                            <input type="checkbox" value="{{ $supervision->id }}" x-model="selected"
                                class="w-5 h-5 rounded-lg border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer shadow-sm">
                        </div>

                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500">
                        </div>

                        <div class="relative z-10 flex flex-col h-full">
                            <div class="flex items-start justify-between mb-6">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-black shadow-sm group-hover:rotate-6 transition-transform ml-8 mt-2">
                                    {{ substr($supervision->name, 0, 1) }}
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('supervisions.edit', $supervision) }}"
                                        class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-400 hover:text-blue-600 flex items-center justify-center transition-all">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="space-y-1 mb-6">
                                <span
                                    class="text-[10px] font-black text-blue-600 uppercase tracking-widest">{{ $supervision->zone->name ?? 'Sem Zona' }}</span>
                                <h3 class="text-xl font-black text-gray-900 leading-tight">{{ $supervision->name }}</h3>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50/50 p-4 rounded-2xl">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Células</span>
                                    <span class="text-xl font-black text-gray-900">{{ $supervision->cells->count() }}</span>
                                </div>
                                <div class="bg-gray-50/50 p-4 rounded-2xl">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Líder</span>
                                    <span
                                        class="text-[10px] font-bold text-gray-700 block truncate">{{ $supervision->supervisor->name ?? 'Vago' }}</span>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <a href="{{ route('supervisions.show', $supervision) }}"
                                    class="flex items-center justify-center w-full py-4 rounded-2xl bg-gray-900 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-md">
                                    Aceder Dashboard
                                    <i class="bi bi-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-[2.5rem] border border-gray-100 border-dashed">
                        <div
                            class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <i class="bi bi-diagram-3 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2">Nenhuma supervisão encontrada</h3>
                        <p class="text-gray-500 font-medium mb-8">Tente ajustar seus termos de pesquisa.</p>
                        <a href="{{ route('supervisions.create') }}"
                            class="inline-flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200">
                            <i class="bi bi-plus-lg mr-2"></i>
                            Nova Supervisão
                        </a>
                    </div>
                @endforelse
            </div>
        </template>

        <!-- List View -->
        <template x-if="view === 'list'">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden overflow-x-auto">
                <table class="w-full text-left table-compact">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-6 w-10">
                                <input type="checkbox" @click="toggleAll()"
                                    :checked="selected.length === {{ $supervisions->count() }} && selected.length > 0"
                                    class="w-5 h-5 rounded-lg border-gray-300 text-blue-600 cursor-pointer shadow-sm">
                            </th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Identificação</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Zona
                                Afiliada</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Supervisor
                            </th>
                            <th
                                class="px-6 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Células</th>
                            <th class="px-6 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($supervisions as $supervision)
                            <tr class="hover:bg-gray-50/50 transition-colors group"
                                :class="{'bg-blue-50/30': selected.includes({{ $supervision->id }})}">
                                <td class="px-6 py-6">
                                    <input type="checkbox" value="{{ $supervision->id }}" x-model="selected"
                                        class="w-5 h-5 rounded-lg border-gray-300 text-blue-600 cursor-pointer shadow-sm">
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-black">
                                            {{ substr($supervision->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-900 uppercase tracking-tight leading-none">
                                                {{ $supervision->name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span
                                        class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                        {{ $supervision->zone->name ?? 'Interno' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    <span
                                        class="text-sm font-bold text-gray-700">{{ $supervision->supervisor->name ?? 'Vago' }}</span>
                                </td>
                                <td class="px-6 py-6 text-center font-black text-gray-700">{{ $supervision->cells->count() }}
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="{{ route('supervisions.show', $supervision) }}" title="Detalhes"
                                            class="action-icon bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('supervisions.edit', $supervision) }}" title="Editar"
                                            class="action-icon bg-gray-50 text-gray-400 hover:bg-yellow-500 hover:text-white">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </template>

        @if($supervisions->hasPages())
            <div class="mt-8">
                {{ $supervisions->links() }}
            </div>
        @endif
    </div>
@endsection