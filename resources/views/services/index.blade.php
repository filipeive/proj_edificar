@extends('layouts.app')

@section('title', 'Gestão de Cultos - Portal Life Church')
@section('page-title','Gestão de Cultos')
@section('page-subtitle', 'Histórico e registro de celebrações e reuniões de ensino')

@section('header-actions')
    <div class="flex items-center gap-1.5 md:hidden">
        <a href="{{ route('services.report') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <i class="bi bi-graph-up text-base"></i>
        </a>
        @can('create', App\Models\Service::class)
            <a href="{{ route('services.create-teaching') }}"
                class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-orange-50 p-2 text-orange-700 shadow-sm hover:bg-orange-100 transition">
                <i class="bi bi-mortarboard text-base"></i>
            </a>
            <a href="{{ route('services.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-orange-700 p-2 text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-calendar-plus text-base"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="w-full space-y-5" 
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
             class="fixed top-20 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
            <div class="bg-gray-900 text-white rounded-xl shadow-lg p-3 flex items-center gap-4 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-95">
                <div class="flex items-center gap-2 pl-2">
                    <span class="bg-orange-700 text-xs font-black px-2 py-0.5 rounded-md" x-text="selected.length"></span>
                    <span class="text-xs font-semibold">selecionados</span>
                </div>
                
                <div class="h-5 w-px bg-gray-700"></div>
                
                <div class="flex items-center gap-2">
                    <button @click="selected = []" class="px-3 py-1.5 text-xs font-bold text-gray-300 hover:text-white transition-colors">
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
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5">
                                <i class="bi bi-trash-fill"></i> Excluir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Page Header (National Style) -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Módulo Eclesiástico</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Gestão de Celebrações</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Monitoramento de participação, engajamento espiritual e métricas financeiras dos cultos.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- View Toggle -->
                <div class="hidden md:flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white shadow-sm text-orange-700' : 'text-gray-500 hover:text-gray-700'"
                        class="px-2.5 py-1.5 rounded-md transition-all flex items-center gap-1.5 font-bold text-xs">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        <span>Mosaico</span>
                    </button>
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white shadow-sm text-orange-700' : 'text-gray-500 hover:text-gray-700'"
                        class="px-2.5 py-1.5 rounded-md transition-all flex items-center gap-1.5 font-bold text-xs">
                        <i class="bi bi-list-columns-reverse"></i>
                        <span>Listagem</span>
                    </button>
                </div>

                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('services.report') }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-graph-up-arrow text-blue-600"></i>
                        <span>Análise & Tendências</span>
                    </a>
                    
                    @can('create', App\Models\Service::class)
                        <a href="{{ route('services.create-teaching') }}"
                            class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-orange-200 bg-orange-50 px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-orange-700 hover:bg-orange-100 shadow-sm">
                            <i class="bi bi-mortarboard"></i>
                            <span>Estudo</span>
                        </a>
                        <a href="{{ route('services.create') }}"
                            class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                            <i class="bi bi-plus-lg"></i>
                            <span>Novo Culto</span>
                        </a>
                    @endcan
                </div>
            </div>
        </header>

        <!-- Analytical Filtering Hub -->
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
            <form action="{{ route('services.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-4 space-y-1">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Pesquisar</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-8 pr-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-600 text-xs sm:text-sm text-gray-700 placeholder-gray-400 transition-all outline-none"
                            placeholder="Pesquisar por tema, pregador ou data...">
                    </div>
                </div>

                <div class="md:col-span-3 grid grid-cols-2 gap-2.5">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Início</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-600 text-xs sm:text-sm text-gray-700 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Término</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-600 text-xs sm:text-sm text-gray-700 outline-none transition-all">
                    </div>
                </div>

                <div class="md:col-span-3 space-y-1">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Natureza</label>
                    <select name="service_type" data-searchable="false"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-600 text-xs sm:text-sm font-medium text-gray-700 outline-none transition-all cursor-pointer">
                        <option value="">Todas as Naturezas</option>
                        <option value="1st" {{ request('service_type') === '1st' ? 'selected' : '' }}>1º Culto Matutino</option>
                        <option value="2nd" {{ request('service_type') === '2nd' ? 'selected' : '' }}>2º Culto Intermediário</option>
                        <option value="3rd" {{ request('service_type') === '3rd' ? 'selected' : '' }}>3º Culto Vespertino</option>
                        <option value="4th" {{ request('service_type') === '4th' ? 'selected' : '' }}>4º Culto Noturno</option>
                        <option value="teaching" {{ request('service_type') === 'teaching' ? 'selected' : '' }}>Reunião de Ensino</option>
                        <option value="special" {{ request('service_type') === 'special' ? 'selected' : '' }}>Evento Especial</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-2">
                    <button type="submit" 
                        class="flex-1 py-2 px-3 bg-orange-700 text-white rounded-lg font-bold text-xs sm:text-sm hover:bg-orange-800 transition-all shadow-sm">
                        Aplicar
                    </button>
                    @if(request()->anyFilled(['search', 'date_from', 'date_to', 'service_type']))
                        <a href="{{ route('services.index') }}" 
                            class="p-2 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all border border-gray-300" title="Limpar filtros">
                            <i class="bi bi-x-lg text-xs"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Services Grid View -->
        <div x-show="view === 'grid'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($services as $service)
                <div class="relative group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col"
                     :class="{'ring-2 ring-orange-600 bg-orange-50/10': selected.includes({{ $service->id }})}">
                    
                    <!-- Top Accent -->
                    <div class="h-1 {{ $service->service_type === 'teaching' ? 'bg-orange-600' : 'bg-blue-600' }}"></div>
                    
                    <!-- Checkbox for Bulk Actions -->
                    @if(auth()->user()->role === 'admin')
                        <div class="absolute top-3.5 right-3.5 z-20">
                            <label class="relative flex items-center cursor-pointer">
                                <input type="checkbox" value="{{ $service->id }}" x-model="selected" class="rounded border-gray-300 text-orange-700 focus:ring-orange-600 w-4 h-4">
                            </label>
                        </div>
                    @endif

                    <div class="p-4 flex-1 flex flex-col space-y-3.5">
                        <!-- Date & Type -->
                        <div class="flex items-start justify-between pr-6">
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full {{ $service->service_type === 'teaching' ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-blue-50 text-blue-700 border-blue-200' }} border text-[10px] font-black uppercase tracking-wider">
                                    @switch($service->service_type)
                                        @case('1st') 1º Culto @break
                                        @case('2nd') 2º Culto @break
                                        @case('3rd') 3º Culto @break
                                        @case('4th') 4º Culto @break
                                        @case('teaching') Ensino @break
                                        @default Especial
                                    @endswitch
                                </span>
                                <h3 class="mt-1.5 text-lg font-black text-gray-950 tracking-tight">{{ $service->date->format('d/m/Y') }}</h3>
                                <p class="text-[11px] font-medium text-gray-400 capitalize">{{ $service->date->translatedFormat('l') }}</p>
                            </div>

                            <div class="rounded-lg bg-gray-50 border border-gray-200 px-2.5 py-1.5 text-center">
                                <span class="block text-[9px] font-bold text-gray-400 uppercase">Impacto</span>
                                <span class="text-base font-black text-gray-900">{{ $service->total_participation }}</span>
                            </div>
                        </div>

                        <!-- Preacher & Theme info line -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-blue-600 shadow-sm border border-gray-200 shrink-0">
                                    <i class="bi bi-mic-fill text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Ministrante</p>
                                    <p class="text-xs font-bold text-gray-800 truncate">{{ $service->preacher ? $service->preacher->name : ($service->preacher_name ?? 'N/A') }}</p>
                                </div>
                            </div>

                            @if($service->theme)
                                <div class="px-3 py-1.5 border-l-2 border-blue-500 bg-blue-50/30">
                                    <p class="text-xs font-medium text-gray-600 italic line-clamp-1">"{{ $service->theme }}"</p>
                                </div>
                            @endif
                        </div>

                        <!-- Mini Stats Grid -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <div class="p-2.5 bg-emerald-50/60 rounded-lg border border-emerald-100 text-center">
                                <p class="text-[9px] font-bold text-emerald-700 uppercase">Ofertas</p>
                                <p class="text-xs font-black text-emerald-800 mt-0.5">{{ number_format($service->total_offerings, 0, ',', '.') }} MT</p>
                            </div>
                            <div class="p-2.5 bg-indigo-50/60 rounded-lg border border-indigo-100 text-center">
                                <p class="text-[9px] font-bold text-indigo-700 uppercase">Dízimos</p>
                                <p class="text-xs font-black text-indigo-800 mt-0.5">{{ number_format($service->total_tithes, 0, ',', '.') }} MT</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="mt-auto px-4 py-2.5 bg-gray-50 border-t border-gray-200 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('services.show', $service) }}" 
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-gray-600 hover:text-blue-600 transition-all shadow-sm border border-gray-200 text-xs" title="Ver Detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('services.download-pdf', $service) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-gray-600 hover:text-red-600 transition-all shadow-sm border border-gray-200 text-xs" title="PDF">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            <a href="{{ route('services.edit', $service) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-gray-600 hover:text-orange-600 transition-all shadow-sm border border-gray-200 text-xs" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>

                        <form action="{{ route('services.destroy', $service) }}" method="POST" id="delete-form-grid-{{ $service->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form-grid-{{ $service->id }}', 'Remover este registro permanentemente?')" 
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-gray-400 hover:text-white hover:bg-red-600 transition-all shadow-sm border border-gray-200 text-xs">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Services List Hub -->
        <div x-show="view === 'list'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            @if(auth()->user()->role === 'admin')
                                <th class="px-4 py-3 w-10">
                                    <input type="checkbox" @click="toggleAll()" 
                                        :checked="selected.length === {{ $services->count() }} && selected.length > 0"
                                        class="rounded border-gray-300 text-orange-700 focus:ring-orange-600 w-4 h-4">
                                </th>
                            @endif
                            <th class="px-4 py-3">Referência Temporal</th>
                            <th class="px-4 py-3">Natureza</th>
                            <th class="px-4 py-3">Ministração</th>
                            <th class="px-4 py-3 text-center">Impacto</th>
                            <th class="px-4 py-3 text-right">Volume Financ.</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($services as $service)
                            <tr class="hover:bg-gray-50/80 transition-colors group"
                                :class="{'bg-orange-50/40': selected.includes({{ $service->id }})}">
                                @if(auth()->user()->role === 'admin')
                                    <td class="px-4 py-3">
                                        <input type="checkbox" value="{{ $service->id }}" x-model="selected"
                                            class="rounded border-gray-300 text-orange-700 focus:ring-orange-600 w-4 h-4">
                                    </td>
                                @endif
                                <td class="px-4 py-3">
                                    <p class="text-xs sm:text-sm font-black text-gray-900 leading-tight">{{ $service->date->format('d/m/Y') }}</p>
                                    <p class="text-[10px] font-semibold text-gray-400 uppercase">{{ $service->date->translatedFormat('l') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full {{ $service->service_type === 'teaching' ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-blue-50 text-blue-700 border-blue-200' }} border text-[10px] font-black uppercase tracking-wider">
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
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-blue-600 shrink-0 text-xs">
                                            <i class="bi bi-person-video3"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-900 truncate">{{ $service->preacher ? $service->preacher->name : ($service->preacher_name ?? 'N/A') }}</p>
                                            @if($service->theme)
                                                <p class="text-[11px] text-gray-500 truncate max-w-[220px]">"{{ $service->theme }}"</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-black text-gray-900">{{ $service->total_participation }}</span>
                                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Pessoas</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="text-xs font-black text-gray-900">{{ number_format($service->total_financial, 0, ',', '.') }} MT</p>
                                    <p class="text-[10px] font-semibold text-emerald-600">{{ number_format($service->total_offerings, 0, ',', '.') }} Ofer. • {{ number_format($service->total_tithes, 0, ',', '.') }} Díz.</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end items-center gap-1">
                                        <a href="{{ route('services.show', $service) }}" 
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all border border-gray-200 text-xs" title="Ver Detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('services.download-pdf', $service) }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all border border-gray-200 text-xs" title="PDF">
                                            <i class="bi bi-file-pdf-fill"></i>
                                        </a>
                                        @can('update', $service)
                                            <a href="{{ route('services.edit', $service) }}"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-500 hover:text-orange-600 hover:bg-orange-50 transition-all border border-gray-200 text-xs" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $service)
                                            <form action="{{ route('services.destroy', $service) }}" method="POST" id="delete-form-list-{{ $service->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('delete-form-list-{{ $service->id }}', 'Remover registro permanentemente?')" 
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-500 hover:text-white hover:bg-red-600 transition-all border border-gray-200 text-xs" title="Excluir">
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
        </div>

        <!-- Pagination -->
        <div class="mt-4">
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
                            const newPagination = doc.querySelector('.mt-4');
                            const currentPagination = document.querySelector('.mt-4');
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
