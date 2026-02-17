@extends('layouts.app')

@section('title', 'Gestão de Cultos - Portal Life Church')
@section('page-title','Gestão de Cultos')
@section('page-subtitle', 'Histórico e registro de celebrações e reuniões de ensino')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('services.report') }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center border border-blue-100 shadow-sm">
            <i class="bi bi-graph-up text-2xl"></i>
        </a>
        @can('create', App\Models\Service::class)
            <a href="{{ route('services.create-teaching') }}"
                class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-orange-600/20">
                <i class="bi bi-book text-2xl"></i>
            </a>
            <a href="{{ route('services.create') }}"
                class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
                <i class="bi bi-calendar-plus text-2xl"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="container-fluid space-y-12" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : 'list',
            selected: [],
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid';
                }
            },
            toggleAll() {
                const allIds = {{ Js::from($services->pluck('id')) }};
                if (this.selected.length === allIds.length) {
                    this.selected = [];
                } else {
                    this.selected = allIds;
                }
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('services_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('services_view') || 'grid')"
        @resize.window.debounce.500ms="updateView()">

        <!-- Bulk Action Bar -->
        <div x-show="selected.length > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
            <div class="bg-gray-900 text-white rounded-2xl shadow-2xl p-4 flex items-center gap-6 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                <div class="flex items-center gap-3 pl-2">
                    <span class="bg-blue-600 text-xs font-black px-2.5 py-1 rounded-lg" x-text="selected.length"></span>
                    <span class="text-sm font-medium">selecionados</span>
                </div>
                
                <div class="h-8 w-px bg-gray-700"></div>
                
                <div class="flex items-center gap-2">
                    <button @click="selected = []" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    @if(auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('services.bulk-delete') }}" 
                              @submit.prevent="
                                Swal.fire({
                                    title: 'Confirmação de Exclusão',
                                    text: 'Tem certeza que deseja excluir ' + selected.length + ' culto(s)? Esta ação é irreversível.',
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
                                <input type="hidden" name="service_ids[]" :value="id">
                            </template>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
                                <i class="bi bi-trash-fill"></i> Excluir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Strategic Hub Header -->
        <div class="relative overflow-hidden bg-white dark:bg-gray-900 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-800 transition-all group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 via-transparent to-orange-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            
            <div class="relative p-8 md:p-12 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                <div class="flex items-start gap-6">
                    <div class="relative hidden sm:block">
                        <div class="absolute inset-0 bg-blue-600 blur-2xl opacity-20 animate-pulse"></div>
                        <div class="relative w-16 h-16 rounded-2xl bg-gray-900 border border-gray-800 flex items-center justify-center shadow-2xl">
                            <i class="bi bi-calendar-check text-2xl text-blue-500"></i>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center gap-1">
                            <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] rounded-lg border border-blue-100 dark:border-blue-800">Módulo Eclesiástico</span>
                            <div class="flex -space-x-2">
                                <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white dark:border-gray-900 flex items-center justify-center text-[8px] font-bold text-white shadow-sm">1</div>
                                <div class="w-6 h-6 rounded-full bg-orange-500 border-2 border-white dark:border-gray-900 flex items-center justify-center text-[8px] font-bold text-white shadow-sm text-xs">A</div>
                            </div>
                        </div>
                        <h1 class="text-4xl lg:text-5xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-none">Gestão de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500">Celebrações</span></h1>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 max-w-xl leading-relaxed">Central de inteligência para monitoramento de participação, engajamento espiritual e métricas financeiras dos cultos.</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <!-- View Toggle -->
                    <div class="hidden md:flex bg-gray-50 dark:bg-gray-800 p-1.5 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <button @click="view = 'grid'" 
                            :class="view === 'grid' ? 'bg-white dark:bg-gray-700 shadow-md text-blue-600 dark:text-blue-400 scale-105' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'"
                            class="p-3 rounded-xl transition-all flex items-center gap-2 font-black text-[10px] uppercase tracking-widest">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            <span>Mosaico</span>
                        </button>
                        <button @click="view = 'list'" 
                            :class="view === 'list' ? 'bg-white dark:bg-gray-700 shadow-md text-blue-600 dark:text-blue-400 scale-105' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'"
                            class="p-3 rounded-xl transition-all flex items-center gap-2 font-black text-[10px] uppercase tracking-widest">
                            <i class="bi bi-list-columns-reverse"></i>
                            <span>Listagem</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('services.report') }}"
                            class="group relative flex items-center px-6 py-4 bg-gray-900 dark:bg-gray-800 text-white rounded-2xl hover:scale-105 transition-all duration-300 shadow-xl shadow-gray-900/10">
                            <i class="bi bi-graph-up-arrow text-blue-500 mr-3 text-lg group-hover:rotate-12 transition-transform"></i>
                            <div class="text-left">
                                <p class="text-[8px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Análise</p>
                                <p class="text-[10px] font-black uppercase tracking-widest leading-none">Tendências</p>
                            </div>
                        </a>
                        
                        @can('create', App\Models\Service::class)
                            <div class="flex gap-3">
                                <a href="{{ route('services.create-teaching') }}"
                                    class="group relative flex items-center px-6 py-4 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-2xl hover:bg-orange-600 hover:text-white transition-all duration-300 border border-orange-100 dark:border-orange-800">
                                    <i class="bi bi-mortarboard mr-3 text-lg group-hover:-rotate-12 transition-transform"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Ensino</p>
                                </a>
                                <a href="{{ route('services.create') }}"
                                    class="group relative flex items-center px-8 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 hover:scale-105 transition-all duration-300 shadow-xl shadow-blue-600/30 overflow-hidden">
                                    <div class="absolute inset-x-0 bottom-0 h-1 bg-blue-400 opacity-30"></div>
                                    <i class="bi bi-plus-lg mr-3 text-lg"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Novo Culto</p>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytical Filtering Hub -->
        <div class="bg-gray-50 dark:bg-gray-950 p-6 md:p-10 rounded-[3rem] border border-gray-100 dark:border-gray-800 transition-colors">
            <form action="{{ route('services.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-4 space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-2">Inteligência de Busca</label>
                    <div class="relative group">
                        <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-14 pr-6 py-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 text-xs font-bold text-gray-700 dark:text-gray-300 placeholder-gray-400 transition-all outline-none shadow-sm"
                            placeholder="Pesquisar por tema, pregador ou data...">
                    </div>
                </div>

                <div class="md:col-span-3 grid grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-2">Início</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-5 py-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 text-xs font-bold text-gray-700 dark:text-gray-300 outline-none transition-all shadow-sm">
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-2">Término</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full px-5 py-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 text-xs font-bold text-gray-700 dark:text-gray-300 outline-none transition-all shadow-sm">
                    </div>
                </div>

                <div class="md:col-span-3 space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] ml-2">Filtro de Natureza</label>
                    <div class="relative">
                        <select name="service_type" 
                            class="appearance-none w-full px-6 py-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 text-xs font-black text-gray-700 dark:text-gray-300 outline-none transition-all shadow-sm cursor-pointer">
                            <option value="">Todas as Naturezas</option>
                            <option value="1st" {{ request('service_type') === '1st' ? 'selected' : '' }}>1º Culto Matutino</option>
                            <option value="2nd" {{ request('service_type') === '2nd' ? 'selected' : '' }}>2º Culto Intermediário</option>
                            <option value="3rd" {{ request('service_type') === '3rd' ? 'selected' : '' }}>3º Culto Vespertino</option>
                            <option value="4th" {{ request('service_type') === '4th' ? 'selected' : '' }}>4º Culto Noturno</option>
                            <option value="teaching" {{ request('service_type') === 'teaching' ? 'selected' : '' }}>Reunião de Ensino</option>
                            <option value="special" {{ request('service_type') === 'special' ? 'selected' : '' }}>Evento Especial</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <div class="md:col-span-2 flex items-center gap-3 h-[52px] mb-[2px]">
                    <button type="submit" 
                        class="flex-1 h-full bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all shadow-lg shadow-blue-600/20">
                        Aplicar
                    </button>
                    @if(request()->anyFilled(['search', 'date_from', 'date_to', 'service_type']))
                        <a href="{{ route('services.index') }}" 
                            class="w-14 h-full flex items-center justify-center bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-2xl hover:bg-gray-300 dark:hover:bg-gray-700 transition-all group">
                            <i class="bi bi-x-lg group-hover:rotate-90 transition-transform"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Services Grid View -->
        <div x-show="view === 'grid'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($services as $service)
                <div class="relative group bg-white dark:bg-gray-900 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 overflow-hidden flex flex-col"
                     :class="{'ring-2 ring-blue-500 bg-blue-50/5 dark:bg-blue-900/5': selected.includes({{ $service->id }})}">
                    
                    <!-- Top Gradient Accents -->
                    <div class="absolute top-0 left-0 right-0 h-1.5 {{ $service->service_type === 'teaching' ? 'bg-gradient-to-r from-orange-400 to-orange-600' : 'bg-gradient-to-r from-blue-400 to-blue-600' }}"></div>
                    
                    <!-- Checkbox for Bulk Actions -->
                    @if(auth()->user()->role === 'admin')
                        <div class="absolute top-6 left-6 z-20">
                            <label class="relative flex items-center cursor-pointer group/check">
                                <input type="checkbox" value="{{ $service->id }}" x-model="selected"
                                    class="peer sr-only">
                                <div class="w-7 h-7 bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-xl peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all shadow-sm"></div>
                                <i class="bi bi-check-lg absolute inset-0 flex items-center justify-center text-white scale-0 peer-checked:scale-100 transition-transform text-sm"></i>
                            </label>
                        </div>
                    @endif

                    <div class="p-8 pb-4 flex-1 flex flex-col space-y-8">
                        <!-- Date & Type -->
                        <div class="flex items-start justify-between">
                            <div class="space-y-4 pt-1">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full {{ $service->service_type === 'teaching' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600' }} border border-current/10">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest">
                                        @switch($service->service_type)
                                            @case('1st') 1º Culto @break
                                            @case('2nd') 2º Culto @break
                                            @case('3rd') 3º Culto @break
                                            @case('4th') 4º Culto @break
                                            @case('teaching') Ensino @break
                                            @default Especial
                                        @endswitch
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $service->date->translatedFormat('F') }}</p>
                                    <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-none">{{ $service->date->format('d') }}, {{ $service->date->format('Y') }}</h3>
                                </div>
                            </div>

                            <div class="relative group/impact">
                                <div class="absolute inset-0 bg-blue-500 blur-xl opacity-0 group-hover/impact:opacity-20 transition-opacity"></div>
                                <div class="relative w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center overflow-hidden transition-all group-hover/impact:-translate-y-1">
                                    <span class="text-[8px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest leading-none mb-1">Impacto</span>
                                    <span class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter leading-none">{{ $service->total_participation }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Preacher & Theme info line -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-4 bg-gray-50/50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700 min-h-[72px]">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-700 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm border border-gray-100 dark:border-gray-600">
                                    <i class="bi bi-mic-fill text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5">Ministrante</p>
                                    <p class="text-sm font-black text-gray-800 dark:text-gray-200 truncate">{{ $service->preacher ? $service->preacher->name : ($service->preacher_name ?? 'N/A') }}</p>
                                </div>
                            </div>

                            @if($service->theme)
                                <div class="px-5 py-3 border-l-4 border-blue-500/20 bg-blue-50/5 dark:bg-blue-900/5">
                                    <p class="text-xs font-black text-gray-600 dark:text-gray-400 leading-relaxed italic line-clamp-2">"{{ $service->theme }}"</p>
                                </div>
                            @endif
                        </div>

                        <!-- Mini Stats Grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-4 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100/50 dark:border-emerald-800/20 text-center group/stat hover:bg-emerald-50 transition-colors">
                                <p class="text-[8px] font-black text-emerald-600/60 uppercase tracking-[0.2em] mb-1 leading-none">Ofertas</p>
                                <p class="text-[13px] font-black text-emerald-700 dark:text-emerald-400 tracking-tighter">{{ number_format($service->total_offerings, 0, ',', '.') }} MT</p>
                            </div>
                            <div class="p-4 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100/50 dark:border-indigo-800/20 text-center group/stat hover:bg-indigo-50 transition-colors">
                                <p class="text-[8px] font-black text-indigo-600/60 uppercase tracking-[0.2em] mb-1 leading-none">Dízimos</p>
                                <p class="text-[13px] font-black text-indigo-700 dark:text-indigo-400 tracking-tighter">{{ number_format($service->total_tithes, 0, ',', '.') }} MT</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="mt-auto p-6 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('services.show', $service) }}" 
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-gray-700 text-gray-400 hover:text-blue-600 hover:scale-110 active:scale-95 transition-all shadow-sm border border-gray-100 dark:border-gray-600" title="Ver Detalhes">
                                <i class="bi bi-window-stack"></i>
                            </a>
                            <a href="{{ route('services.download-pdf', $service) }}"
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-gray-700 text-gray-400 hover:text-red-500 hover:scale-110 active:scale-95 transition-all shadow-sm border border-gray-100 dark:border-gray-600" title="PDF">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            <a href="{{ route('services.edit', $service) }}"
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-gray-700 text-gray-400 hover:text-orange-500 hover:scale-110 active:scale-95 transition-all shadow-sm border border-gray-100 dark:border-gray-600" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>

                        <form action="{{ route('services.destroy', $service) }}" method="POST" id="delete-form-grid-{{ $service->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form-grid-{{ $service->id }}', 'Remover este registro permanentemente?')" 
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-gray-700 text-gray-300 hover:text-white hover:bg-red-500 transition-all shadow-sm border border-gray-100 dark:border-gray-600">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>


        <!-- Services List Hub -->
        <div x-show="view === 'list'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-gray-900 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                            @if(auth()->user()->role === 'admin')
                                <th class="px-8 py-6 w-14">
                                    <label class="relative flex items-center cursor-pointer group/check">
                                        <input type="checkbox" @click="toggleAll()" 
                                            :checked="selected.length === {{ $services->count() }} && selected.length > 0"
                                            class="peer sr-only">
                                        <div class="w-6 h-6 bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-lg peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all"></div>
                                        <i class="bi bi-check-lg absolute inset-0 flex items-center justify-center text-white scale-0 peer-checked:scale-100 transition-transform text-xs"></i>
                                    </label>
                                </th>
                            @endif
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Referência Temporal</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Natureza</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Ministração</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] text-center">Impacto</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] text-right">Volume Financ.</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($services as $service)
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-all duration-300 group border-b border-gray-50 dark:border-gray-800/50 last:border-0"
                                :class="{'bg-blue-50/50 dark:bg-blue-900/20': selected.includes({{ $service->id }})}">
                                @if(auth()->user()->role === 'admin')
                                    <td class="px-8 py-6 relative">
                                        <div class="absolute left-0 top-4 bottom-4 w-1 bg-blue-600 rounded-full opacity-0 transition-opacity" :class="{'opacity-100': selected.includes({{ $service->id }})}"></div>
                                        <label class="relative flex items-center cursor-pointer">
                                            <input type="checkbox" value="{{ $service->id }}" x-model="selected"
                                                class="peer sr-only">
                                            <div class="w-6 h-6 bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-lg peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all"></div>
                                            <i class="bi bi-check-lg absolute inset-0 flex items-center justify-center text-white scale-0 peer-checked:scale-100 transition-transform text-xs"></i>
                                        </label>
                                    </td>
                                @endif
                                <td class="px-8 py-6">
                                    <div class="space-y-0.5">
                                        <p class="text-sm font-black text-gray-900 dark:text-white tracking-tight leading-none">{{ $service->date->format('d/m/Y') }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $service->date->translatedFormat('l') }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full {{ $service->service_type === 'teaching' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600' }} border border-current/10 text-[9px] font-black uppercase tracking-widest">
                                        @switch($service->service_type)
                                            @case('1st') 1º Culto @break
                                            @case('2nd') 2º Culto @break
                                            @case('3rd') 3º Culto @break
                                            @case('4th') 4º Culto @break
                                            @case('teaching') Ensino @break
                                            @default Especial
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-9 h-9 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                                            <i class="bi bi-person-video3"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-black text-gray-800 dark:text-gray-200 truncate">{{ $service->preacher ? $service->preacher->name : ($service->preacher_name ?? 'N/A') }}</p>
                                            @if($service->theme)
                                                <p class="text-[10px] font-medium text-gray-400 truncate max-w-[200px]">"{{ $service->theme }}"</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-xs font-black text-gray-900 dark:text-white">{{ $service->total_participation }}</span>
                                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Pessoas</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <p class="text-xs font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($service->total_financial, 0, ',', '.') }} MT</p>
                                    <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest">{{ number_format($service->total_offerings, 0, ',', '.') }} Ofer. • {{ number_format($service->total_tithes, 0, ',', '.') }} Díz.</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end items-center gap-1.5">
                                        <a href="{{ route('services.show', $service) }}" 
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-blue-600 hover:scale-110 transition-all border border-gray-100 dark:border-gray-700" title="Ver Detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('services.download-pdf', $service) }}"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-red-500 hover:scale-110 transition-all border border-gray-100 dark:border-gray-700" title="PDF">
                                            <i class="bi bi-file-pdf-fill"></i>
                                        </a>
                                        @can('update', $service)
                                            <a href="{{ route('services.edit', $service) }}"
                                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-orange-500 hover:scale-110 transition-all border border-gray-100 dark:border-gray-700" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $service)
                                            <form action="{{ route('services.destroy', $service) }}" method="POST" id="delete-form-list-{{ $service->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('delete-form-list-{{ $service->id }}', 'Remover registro permanentemente?')" 
                                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-white hover:bg-red-500 transition-all border border-gray-100 dark:border-gray-700" title="Excluir">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        <!-- Pagination -->
        <div class="mt-12">
            {{ $services->links() }}
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
    <script>
        // Dynamic Search Script
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('input[name="search"]');
            const filterForm = searchInput ? searchInput.closest('form') : null;

            if (searchInput && filterForm) {
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

                            // Find and replace the content of grid and list views
                            const newGrid = doc.querySelector('[x-show="view === \'grid\'"]');
                            const currentGrid = document.querySelector('[x-show="view === \'grid\'"]');
                            if (newGrid && currentGrid) currentGrid.innerHTML = newGrid.innerHTML;

                            const newList = doc.querySelector('[x-show="view === \'list\'"]');
                            const currentList = document.querySelector('[x-show="view === \'list\'"]');
                            if (newList && currentList) currentList.innerHTML = newList.innerHTML;
                            
                            // Replace Pagination if exists
                            const newPagination = doc.querySelector('.mt-12');
                            const currentPagination = document.querySelector('.mt-12');
                            if(newPagination && currentPagination) currentPagination.innerHTML = newPagination.innerHTML;

                            document.body.style.cursor = 'default';
                        })
                        .catch(err => {
                            console.error('Search failed', err);
                            document.body.style.cursor = 'default';
                        });
                    }, 500);
                });
            }
        });
    </script>
    @endif

@endsection
