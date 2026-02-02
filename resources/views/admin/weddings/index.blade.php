@extends('layouts.app')

@section('title', 'Calendário de Casamentos')
@section('page-title', 'Calendário de Casamentos')
@section('page-subtitle', 'Gestão de casamentos e eventos matrimoniais')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('weddings.pdf', ['year' => now()->year]) }}" target="_blank"
            class="bg-white text-gray-400 p-2 rounded-lg hover:text-orange-600 hover:border-orange-200 transition-all border border-gray-200 shadow-sm flex items-center justify-center">
            <i class="bi bi-file-earmark-pdf-fill text-2xl"></i>
        </a>
        <a href="{{ route('weddings.create') }}"
            class="bg-gray-900 text-white p-2 rounded-lg hover:bg-black transition-all flex items-center justify-center shadow-lg shadow-gray-900/20">
            <i class="bi bi-plus-lg text-2xl"></i>
        </a>
    </div>
@endsection

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div class="container-fluid" x-data="{ 
                    view: window.innerWidth < 768 ? 'grid' : 'grid',
                    selected: [],
                    updateView() {
                        if (window.innerWidth < 768 && this.view === 'list') {
                            this.view = 'grid'; 
                        }
                    },
                    toggleAll() {
                        const allIds = {{ Js::from($weddings->pluck('id')) }};
                        if (this.selected.length === allIds.length) {
                            this.selected = [];
                        } else {
                            this.selected = allIds;
                        }
                    }
                }"
        x-init="$watch('view', value => { if(value !== 'calendar') localStorage.setItem('weddings_view', value) }); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('weddings_view') || 'grid')"
        @resize.window.debounce.500ms="updateView()" x-cloak>

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
                    @if(auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('weddings.bulk-delete') }}" @submit.prevent="
                                                Swal.fire({
                                                    title: 'Confirmação de Exclusão',
                                                    text: 'Tem certeza que deseja excluir ' + selected.length + ' casamento(s)? Esta ação é irreversível.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Sim, excluir!',
                                                    cancelButtonText: 'Cancelar'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $el.submit();
                                                    }
                                                })
                                              ">
                            @csrf
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="wedding_ids[]" :value="id">
                            </template>
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
                                <i class="bi bi-trash-fill"></i> Excluir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <!-- Header Section -->
        <div
            class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6 bg-white p-4 md:p-0 rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-gray-100 md:border-none">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase leading-none">
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-amber-500">Calendário</span>
                    <span class="text-gray-300">de Casamentos</span>
                </h2>
            </div>

            <div class="flex gap-3">
                <!-- Old Bulk Button Removed -->

                <!-- View Toggle -->
                <div class="hidden md:flex bg-gray-100 p-1 rounded-xl items-center">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-list-ul mr-2"></i> Lista
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-grid-fill mr-2"></i> Grelha
                    </button>
                    <button @click="view = 'calendar'; setTimeout(() => initCalendar(), 100)"
                        :class="view === 'calendar' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-calendar-week mr-2"></i> Calendário
                    </button>
                </div>

                <a href="{{ route('weddings.pdf', ['year' => now()->year]) }}" target="_blank"
                    class="hidden md:flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-orange-600 hover:border-orange-200 transition-all duration-300 shadow-sm"
                    title="Exportar PDF do Ano">
                    <i class="bi bi-file-earmark-pdf-fill text-lg"></i>
                </a>

                <a href="{{ route('weddings.create') }}"
                    class="hidden md:flex items-center px-6 py-2 bg-gray-900 hover:bg-black text-white rounded-xl font-bold text-sm transition-all duration-300 shadow-lg shadow-gray-900/20">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Agendamento
                </a>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 mb-8">
            <form action="{{ route('weddings.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Buscar</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" data-live-search="ajax" value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Nome dos noivos, local...">
                    </div>
                </div>
                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full md:w-48 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 appearance-none custom-select">
                        <option value="">Todos</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Realizado</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <button type="submit"
                    class="w-full md:w-auto px-6 py-2 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition-all">
                    Filtrar
                </button>
            </form>
        </div>

        <!-- List View -->
        <div id="view-list" x-show="view === 'list'" class="transition-opacity duration-300">
            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                <!-- Form Removed -->
                <table class="w-full table-compact">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            @if(auth()->user()->role === 'admin')
                                <th class="px-8 py-5 text-left w-10">
                                    <input type="checkbox" @click="toggleAll()"
                                        :checked="selected.length === {{ $weddings->count() }} && selected.length > 0"
                                        class="rounded-lg border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                </th>
                            @endif
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Data</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Noivos</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Local</th>
                            <!-- padrinhos -->
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Padrinhos</th>
                            <th
                                class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Status</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($weddings as $wedding)
                            <tr class="group hover:bg-gray-50/50 transition-colors duration-200">
                                @if(auth()->user()->role === 'admin')
                                    <td class="px-8 py-5 relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-orange-500 opacity-0 transition-opacity"
                                            :class="{'opacity-100': selected.includes({{ $wedding->id }})}"></div>
                                        <input type="checkbox" value="{{ $wedding->id }}" x-model="selected"
                                            class="wedding-checkbox rounded-lg border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                    </td>
                                @endif
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-900">{{ $wedding->date->format('d/m/Y') }}</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase">{{ $wedding->date->translatedFormat('l') }}</span>
                                        @if($wedding->time)
                                            <span
                                                class="text-[10px] font-bold text-orange-500 mt-1">{{ $wedding->time->format('H:i') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mr-4">
                                            <i class="bi bi-heart-fill"></i>
                                        </div>
                                        <div>
                                            <span
                                                class="block text-sm font-bold text-gray-900">{{ $wedding->groom_name }}</span>
                                            <span class="text-xs text-gray-500">& {{ $wedding->bride_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center text-gray-500">
                                        <i class="bi bi-geo-alt-fill mr-2 text-gray-300"></i>
                                        <span
                                            class="text-sm font-medium">{{ $wedding->location ?? 'Local não definido' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        @if($wedding->godparents)
                                            <span
                                                class="text-xs font-bold text-gray-700">{{ Illuminate\Support\Str::limit($wedding->godparents, 30) }}</span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Não informado</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @php
                                        $statusClasses = [
                                            'scheduled' => 'bg-orange-100 text-orange-600',
                                            'completed' => 'bg-green-100 text-green-600',
                                            'cancelled' => 'bg-red-100 text-red-600'
                                        ];
                                        $statusLabels = [
                                            'scheduled' => 'Agendado',
                                            'completed' => 'Realizado',
                                            'cancelled' => 'Cancelado'
                                        ];
                                        $status = $wedding->status ?? 'scheduled';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-lg {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-600' }} text-xs font-bold uppercase tracking-wider">
                                        {{ $statusLabels[$status] ?? $status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <a href="{{ route('weddings.edit', $wedding) }}"
                                            class="action-icon text-gray-400 hover:text-blue-600 hover:bg-blue-50"
                                            title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="{{ route('weddings.show', $wedding) }}"
                                            class="action-icon text-gray-400 hover:text-orange-600 hover:bg-orange-50"
                                            title="Ver detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        </a>
                                        @if(in_array(auth()->user()->role, ['admin', 'secretaria']))
                                            <form id="list-delete-wedding-{{ $wedding->id }}"
                                                action="{{ route('weddings.destroy', $wedding->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                    onclick="confirmDelete('list-delete-wedding-{{ $wedding->id }}')"
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-red-500 hover:text-white"
                                                    title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-12 text-center text-gray-400">
                                    <i class="bi bi-calendar-x text-4xl mb-4 block opacity-20"></i>
                                    <p class="text-sm font-medium">Nenhum casamento encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- </form> Removed -->
            </div>
        </div>
        <div class="mt-6">
            {{ $weddings->links() }}
        </div>

        <!-- Grid View -->
        <div id="view-grid" x-show="view === 'grid'" class="transition-opacity duration-300">
            <div class="flex flex-col xl:flex-row gap-8">
                <!-- Main Grid Area -->
                <div class="flex-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @forelse($weddings->take(6) as $wedding)
                            <div class="bg-white p-6 md:p-8 rounded-2xl md:rounded-[2rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300 relative compact-card"
                                :class="{'ring-2 ring-orange-500 bg-orange-50/10': selected.includes({{ $wedding->id }})}">
                                <div class="absolute top-6 right-6 flex flex-col items-end gap-2">
                                    @php
                                        $status = $wedding->status ?? 'scheduled';
                                        $statusColors = [
                                            'scheduled' => 'bg-orange-50 text-orange-600',
                                            'completed' => 'bg-green-50 text-green-600',
                                            'cancelled' => 'bg-red-50 text-red-600'
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-lg {{ $statusColors[$status] }} text-[10px] font-black uppercase tracking-widest">
                                        {{ $statusLabels[$status] ?? $status }}
                                    </span>
                                    @if(auth()->user()->role === 'admin')
                                        <input type="checkbox" value="{{ $wedding->id }}" x-model="selected"
                                            class="wedding-checkbox rounded-lg border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                    @endif
                                </div>

                                <div
                                    class="w-10 h-10 md:w-14 md:h-14 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center font-black text-lg md:text-2xl group-hover:bg-orange-500 group-hover:text-white transition-all duration-500 mb-6">
                                    <i class="bi bi-heart-fill"></i>
                                </div>

                                <div class="mb-4">
                                    <h4
                                        class="text-lg font-black text-gray-900 leading-tight mb-1 group-hover:text-orange-600 transition-colors">
                                        {{ explode(' ', $wedding->groom_name)[0] }} &
                                        {{ explode(' ', $wedding->bride_name)[0] }}
                                    </h4>
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $wedding->date->diffForHumans() }}</span>
                                </div>

                                <div class="space-y-3 mb-6 flex-1">
                                    <div class="flex items-center gap-3 text-gray-500">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                            <i class="bi bi-calendar-event text-blue-500"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-xs font-black uppercase text-gray-900">{{ $wedding->date->format('d/m/Y') }}</span>
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase">{{ $wedding->date->translatedFormat('l') }}</span>
                                        </div>
                                        <!-- padrinhos -->
                                        <div class="flex items-center gap-3 text-gray-500">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                                <i class="bi bi-people text-green-500"></i>
                                            </div>
                                            <span class="text-xs font-bold text-gray-700">{{ $wedding->godparents}}</span>
                                        </div>
                                    </div>

                                    @if($wedding->time)
                                        <div class="flex items-center gap-3 text-gray-500">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                                <i class="bi bi-clock text-orange-500"></i>
                                            </div>
                                            <span class="text-xs font-bold text-gray-700">{{ $wedding->time->format('H:i') }}</span>
                                        </div>
                                    @endif

                                    @if($wedding->location)
                                        <div class="flex items-center gap-3 text-gray-500">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                                <i class="bi bi-geo-alt text-red-500"></i>
                                            </div>
                                            <span class="text-xs font-bold text-gray-700 truncate">{{ $wedding->location }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 pt-4 border-t border-gray-50">
                                    <a href="{{ route('weddings.show', $wedding) }}"
                                        class="flex-1 bg-gray-50 text-gray-400 text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 hover:text-white transition-all flex items-center justify-center gap-2">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('weddings.edit', $wedding) }}"
                                        class="w-10 h-10 bg-gray-50 text-gray-400 flex items-center justify-center rounded-xl hover:bg-blue-500 hover:text-white transition-all">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(in_array(auth()->user()->role, ['admin', 'secretaria']))
                                        <form id="grid-delete-wedding-{{ $wedding->id }}"
                                            action="{{ route('weddings.destroy', $wedding) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" onclick="confirmDelete('grid-delete-wedding-{{ $wedding->id }}')"
                                                class="w-10 h-10 bg-gray-50 text-gray-400 flex items-center justify-center rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    @endif
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full py-20 bg-white rounded-[2rem] border border-dashed border-gray-200 flex flex-col items-center gap-4 text-gray-300">
                                <i class="bi bi-calendar-x text-7xl opacity-20"></i>
                                <p class="font-bold text-lg">Nenhum casamento encontrado</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-6">
                        {{ $weddings->links() }}
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="w-full xl:w-80 space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-gray-900 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl">
                        <div
                            class="absolute top-0 right-0 w-40 h-40 bg-orange-500 rounded-full blur-[60px] opacity-20 -mr-10 -mt-10">
                        </div>

                        <h3 class="font-bold text-gray-400 text-xs uppercase tracking-widest mb-8">Resumo {{ now()->year }}
                        </h3>

                        <div class="space-y-6 relative z-10">
                            <div class="flex items-center justify-between">
                                <span class="text-3xl font-black">{{ $totalCount }}</span>
                                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Total</span>
                            </div>
                            <div class="w-full h-px bg-white/10"></div>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-green-400">{{ $completedCount }}</span>
                                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Realizados</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-orange-400">{{ $upcomingCount }}</span>
                                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Próximos</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-red-400">{{ $cancelledCount ?? 0 }}</span>
                                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Cancelados</span>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming List -->
                    @if($upcoming->isNotEmpty())
                        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                            <h3 class="text-gray-900 font-black uppercase tracking-widest text-xs mb-6">Próximos Eventos</h3>
                            <div class="space-y-4">
                                @foreach($upcoming as $wedding)
                                    <a href="{{ route('weddings.edit', $wedding) }}" class="block group">
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="flex-shrink-0 w-12 text-center bg-orange-50 rounded-xl py-2 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300">
                                                <span
                                                    class="block text-xs font-bold uppercase opacity-60">{{ $wedding->date->translatedFormat('M') }}</span>
                                                <span
                                                    class="block text-lg font-black leading-none">{{ $wedding->date->format('d') }}</span>
                                            </div>
                                            <div>
                                                <h4
                                                    class="font-bold text-gray-900 text-sm group-hover:text-orange-600 transition-colors">
                                                    {{ explode(' ', $wedding->groom_name)[0] }} &
                                                    {{ explode(' ', $wedding->bride_name)[0] }}
                                                </h4>
                                                <p class="text-xs text-gray-400 mt-1">{{ $wedding->date->diffForHumans() }}</p>
                                                @if($wedding->location)
                                                    <p class="text-xs text-gray-500 mt-1"><i
                                                            class="bi bi-geo-alt-fill mr-1"></i>{{ $wedding->location }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div id="view-calendar" x-show="view === 'calendar'" class="transition-opacity duration-300">
            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden p-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- FullCalendar CSS -->
    <style>
        .fc {
            font-family: inherit;
        }

        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 900 !important;
            text-transform: uppercase;
            letter-spacing: -0.05em;
            color: #111827;
        }

        .fc-button {
            background-color: white !important;
            color: #4b5563 !important;
            border: 1px solid #e5e7eb !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.75rem !important;
            letter-spacing: 0.05em;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            box-shadow: none !important;
        }

        .fc-button:hover {
            background-color: #f9fafb !important;
            color: #ea580c !important;
            border-color: #fed7aa !important;
        }

        .fc-button-active {
            background-color: #ea580c !important;
            color: white !important;
            border-color: #ea580c !important;
        }

        .fc-daygrid-day-number {
            font-weight: 700;
            color: #6b7280;
            text-decoration: none !important;
            padding: 0.5rem !important;
        }

        .fc-col-header-cell-cushion {
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 900;
            letter-spacing: 0.1em;
            color: #9ca3af;
            padding: 1rem 0 !important;
        }

        .fc-event {
            border: none !important;
            border-radius: 0.375rem !important;
            padding: 4px 6px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: all 0.2s;
        }

        .fc-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .fc-day-today {
            background-color: #fff7ed !important;
        }

        .fc-daygrid-event {
            margin: 2px 4px !important;
        }

        /* Tooltip Styles */
        .wedding-tooltip {
            position: absolute;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            min-width: 280px;
            display: none;
        }

        .wedding-tooltip.show {
            display: block;
        }

        .tooltip-header {
            font-weight: 800;
            font-size: 1rem;
            color: #111827;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }

        .tooltip-header i {
            margin-right: 0.5rem;
            color: #ea580c;
        }

        .tooltip-row {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .tooltip-row i {
            margin-right: 0.5rem;
            width: 18px;
            color: #9ca3af;
        }

        .tooltip-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }

        .status-scheduled {
            background: #fed7aa;
            color: #ea580c;
        }

        .status-completed {
            background: #bbf7d0;
            color: #16a34a;
        }

        .status-cancelled {
            background: #fecaca;
            color: #dc2626;
        }
    </style>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/pt.global.min.js'></script>
    <script>
        let calendar;
        let tooltip = null;

        function toggleView(view) {
            // Deprecated: Logic moved to AlpineJS
        }

        function createTooltip() {
            tooltip = document.createElement('div');
            tooltip.className = 'wedding-tooltip';
            document.body.appendChild(tooltip);
        }

        function showTooltip(info) {
            const event = info.event;
            const props = event.extendedProps;

            const statusLabels = {
                'scheduled': 'Agendado',
                'completed': 'Realizado',
                'cancelled': 'Cancelado'
            };

            let tooltipContent = `
                    <div class="tooltip-header">
                        <i class="bi bi-heart-fill"></i>
                        <span>${event.title}</span>
                    </div>
                    <div class="tooltip-row">
                        <i class="bi bi-calendar-event"></i>
                        <span>${event.start.toLocaleDateString('pt-BR', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            })}</span>
                    </div>
                    `;

            if (props.time) {
                tooltipContent += `
                    <div class="tooltip-row">
                        <i class="bi bi-clock-fill"></i>
                        <span>${props.time}</span>
                    </div>
                    `;
            }

            if (props.location) {
                tooltipContent += `
                    <div class="tooltip-row">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>${props.location}</span>
                    </div>
                    `;
            }

            if (props.godparents) {
                tooltipContent += `
                    <div class="tooltip-row">
                        <i class="bi bi-people-fill"></i>
                        <span>Padrinhos: ${props.godparents.substring(0, 50)}${props.godparents.length > 50 ? '...' : ''}</span>
                    </div>
                    `;
            }

            if (props.status) {
                tooltipContent += `
                    <div class="tooltip-status status-${props.status}">
                        ${statusLabels[props.status] || props.status}
                    </div>
                    `;
            }

            tooltip.innerHTML = tooltipContent;
            tooltip.classList.add('show');

            const rect = info.el.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.bottom + 5) + 'px';
        }

        function hideTooltip() {
            if (tooltip) {
                tooltip.classList.remove('show');
            }
        }

        function initCalendar() {
            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,listMonth'
                },
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    list: 'Lista'
                },
                height: 'auto',
                eventDisplay: 'block',
                displayEventTime: false,
                events: '{{ route("weddings.feed") }}',
                eventClick: function (info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                eventMouseEnter: function (info) {
                    showTooltip(info);
                },
                eventMouseLeave: function (info) {
                    hideTooltip();
                },
                eventDidMount: function (info) {
                    const event = info.event;
                    const props = event.extendedProps;

                    // Add heart icon to event
                    const iconEl = document.createElement('i');
                    iconEl.className = 'bi bi-heart-fill mr-1';
                    info.el.querySelector('.fc-event-title').prepend(iconEl);
                },
                dateClick: function (info) {
                    window.location.href = '{{ route("weddings.create") }}?date=' + info.dateStr;
                },
                loading: function (isLoading) {
                    if (isLoading) {
                        calendarEl.style.opacity = '0.5';
                    } else {
                        calendarEl.style.opacity = '1';
                    }
                }
            });
            calendar.render();
        }

        document.addEventListener('DOMContentLoaded', function () {
            createTooltip();
            // view logic handled by alpine

            // Dynamic Search Script
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                const filterForm = searchInput.closest('form');
                let timeout = null;
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    timeout = setTimeout(function () {
                        const formData = new FormData(filterForm);
                        const params = new URLSearchParams(formData);

                        document.body.style.cursor = 'wait';

                        fetch(`${filterForm.action}?${params.toString()}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');

                                // Replace List and Grid Views
                                ['view-list', 'view-grid'].forEach(id => {
                                    const newEl = doc.getElementById(id);
                                    const currentEl = document.getElementById(id);
                                    if (newEl && currentEl) currentEl.innerHTML = newEl.innerHTML;
                                });

                                document.body.style.cursor = 'default';
                            })
                            .catch(err => {
                                console.error('Search failed', err);
                                document.body.style.cursor = 'default';
                            });
                    }, 500);
                });
            }

            // Old bulk logic removed
        });
    </script>
@endsection
