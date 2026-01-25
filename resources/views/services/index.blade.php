@extends('layouts.app')

@section('title', 'Gestão de Cultos - Portal Life Church')

@section('content')
@section('header-actions')
    <div class="flex items-center gap-2">
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
            view: window.innerWidth < 768 ? 'grid' : 'grid',
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid';
                }
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('services_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('services_view') || 'grid')"
        @resize.window.debounce.500ms="updateView()">
        <!-- Header Section -->
        <div class="bg-white p-4 md:p-8 rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <span>Eclesiástico</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight tracking-tighter uppercase">Celebrações</h1>
                <p class="text-gray-500 font-medium">Controle de participação e financeiro dos cultos</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <!-- View Toggle -->
                <div class="hidden md:flex bg-gray-100 p-1.5 rounded-2xl">
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                        class="p-2.5 rounded-xl transition-all flex items-center gap-2 font-bold text-xs uppercase tracking-widest">
                        <i class="bi bi-grid-fill"></i>
                        <span class="hidden sm:inline">Grid</span>
                    </button>
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                        class="p-2.5 rounded-xl transition-all flex items-center gap-2 font-bold text-xs uppercase tracking-widest">
                        <i class="bi bi-list-task"></i>
                        <span class="hidden sm:inline">Lista</span>
                    </button>
                </div>

                <div class="hidden md:flex flex-wrap items-center gap-3">
                    @if(auth()->user()->role === 'admin')
                        <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-4 rounded-2xl flex items-center transition shadow-lg shadow-red-600/20 font-black text-xs uppercase tracking-widest hidden">
                            <i class="bi bi-trash-fill mr-2"></i> Excluir Selecionados
                        </button>
                    @endif
                    <a href="{{ route('services.report') }}"
                        class="flex items-center bg-gray-50 text-gray-400 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-xs uppercase tracking-widest border border-gray-100">
                        <i class="bi bi-graph-up text-lg mr-2 text-blue-600"></i>
                        <span class="hidden lg:inline">Análise de Tendência</span>
                        <span class="lg:hidden">Relatório</span>
                    </a>
                    @can('create', App\Models\Service::class)
                        <div class="flex gap-2">
                            <a href="{{ route('services.create-teaching') }}"
                                class="flex items-center bg-orange-50 text-orange-600 px-6 py-4 rounded-2xl hover:bg-orange-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest border border-orange-100">
                                <i class="bi bi-book text-lg mr-2"></i>
                                Culto de Ensino
                            </a>
                            <a href="{{ route('services.create') }}"
                                class="flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-600/20">
                                <i class="bi bi-plus-lg text-lg mr-2"></i>
                                Registrar Culto
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-gray-50 p-6 rounded-[2rem] border border-gray-100">
            <form action="{{ route('services.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[250px] space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pesquisar</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs font-bold text-gray-600"
                            placeholder="Tema ou pregador...">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Início</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="px-4 py-2 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs font-bold text-gray-600">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Fim</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="px-4 py-2 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs font-bold text-gray-600">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tipo de Culto</label>
                    <select name="service_type" 
                        class="px-4 py-2 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs font-bold text-gray-600 min-w-[150px]">
                        <option value="">Todos</option>
                        <option value="1st" {{ request('service_type') === '1st' ? 'selected' : '' }}>1º Culto</option>
                        <option value="2nd" {{ request('service_type') === '2nd' ? 'selected' : '' }}>2º Culto</option>
                        <option value="3rd" {{ request('service_type') === '3rd' ? 'selected' : '' }}>3º Culto</option>
                        <option value="4th" {{ request('service_type') === '4th' ? 'selected' : '' }}>4º Culto</option>
                        <option value="teaching" {{ request('service_type') === 'teaching' ? 'selected' : '' }}>Ensino</option>
                        <option value="special" {{ request('service_type') === 'special' ? 'selected' : '' }}>Especial</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['search', 'date_from', 'date_to', 'service_type']))
                        <a href="{{ route('services.index') }}" 
                            class="px-6 py-2 bg-gray-200 text-gray-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-300 transition-all">
                            Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <form id="bulkActionForm" action="{{ route('services.bulk-delete') }}" method="POST">
            @csrf
        </form>

        <!-- Services Grid View -->
        <div x-show="view === 'grid'" x-transition.fade.duration.300ms class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            @foreach($services as $service)
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:shadow-gray-200/50 transition-all group flex flex-col relative border-t-4 {{ $service->service_type === 'teaching' ? 'border-t-orange-500' : 'border-t-blue-500' }}">
                    <!-- Checkbox for Bulk Actions (Grid) -->
                    @if(auth()->user()->role === 'admin')
                        <div class="absolute top-6 left-6 z-10">
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" form="bulkActionForm"
                                class="service-checkbox rounded-lg border-gray-300 {{ $service->service_type === 'teaching' ? 'text-orange-600 focus:border-orange-300 focus:ring-orange-200' : 'text-blue-600 focus:border-blue-300 focus:ring-blue-200' }} shadow-sm focus:ring focus:ring-opacity-50 transition-all cursor-pointer w-6 h-6 bg-white/80 backdrop-blur-sm">
                        </div>
                    @endif

                    <div class="p-8 space-y-6 flex-1">
                        <!-- Card Header -->
                        <div class="flex justify-between items-start {{ auth()->user()->role === 'admin' ? 'pl-8' : '' }}">
                            <div class="space-y-1">
                                <div class="px-3 py-1 {{ $service->service_type === 'teaching' ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600' }} rounded-full text-[10px] font-black uppercase tracking-widest inline-block mb-1">
                                    @switch($service->service_type)
                                        @case('1st') 1º Culto @break
                                        @case('2nd') 2º Culto @break
                                        @case('3rd') 3º Culto @break
                                        @case('4th') 4º Culto @break
                                        @case('teaching') Ensino @break
                                        @default Especial
                                    @endswitch
                                </div>
                                <h3 class="text-xl font-black text-gray-900">{{ $service->date->format('d/m/Y') }}</h3>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">
                                    Pregador: <span class="text-xs font-black {{ ($service->preacher_id === null && $service->preacher_name) ? 'text-orange-600 bg-orange-50 px-2 py-0.5 rounded-lg' : 'text-gray-600' }}">
                                        @if($service->preacher)
                                            {{ $service->preacher->name }}
                                        @else
                                            {{ $service->preacher_name ?? 'N/A' }}
                                            @if($service->preacher_id === null && $service->preacher_name)
                                                <i class="bi bi-person-badge-fill ml-1" title="Convidado Externo"></i>
                                            @endif
                                        @endif
                                    </span>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Participação</span>
                                <span class="text-2xl font-black {{ $service->service_type === 'teaching' ? 'text-orange-600' : 'text-blue-600' }}">{{ $service->total_participation }}</span>
                            </div>
                        </div>

                        <!-- Theme -->
                        @if($service->theme)
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 min-h-[80px] flex items-center justify-center text-center">
                                <span class="text-sm font-black text-gray-700 italic">"{{ $service->theme }}"</span>
                            </div>
                        @endif

                        <!-- Financial Breakdown -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-green-50 rounded-2xl border border-green-100">
                                <span class="text-[9px] font-black text-green-600 uppercase tracking-widest block mb-1">Ofertas</span>
                                <span class="text-sm font-black text-green-700">{{ number_format($service->total_offerings, 2) }} MT</span>
                            </div>
                            <div class="p-4 {{ $service->service_type === 'teaching' ? 'bg-orange-50 border-orange-100' : 'bg-blue-50 border-blue-100' }} rounded-2xl border">
                                <span class="text-[9px] font-black {{ $service->service_type === 'teaching' ? 'text-orange-600' : 'text-blue-600' }} uppercase tracking-widest block mb-1">Dízimos</span>
                                <span class="text-sm font-black {{ $service->service_type === 'teaching' ? 'text-orange-700' : 'text-blue-700' }}">{{ number_format($service->total_tithes, 2) }} MT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex gap-2">
                            <a href="{{ route('services.show', $service) }}" class="p-2 {{ $service->service_type === 'teaching' ? 'text-orange-600 hover:bg-orange-50' : 'text-blue-600 hover:bg-blue-50' }} rounded-lg transition-colors">
                                <i class="bi bi-info-circle text-lg"></i>
                            </a>
                            <a href="{{ route('services.download-pdf', $service) }}" class="p-3 bg-white text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border border-gray-100" title="Baixar PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <a href="{{ route('services.edit', $service) }}" class="p-3 bg-white {{ $service->service_type === 'teaching' ? 'text-orange-600 hover:bg-orange-600' : 'text-blue-600 hover:bg-blue-600' }} rounded-xl hover:text-white transition-all shadow-sm border border-gray-100" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                        <form action="{{ route('services.destroy', $service) }}" method="POST"
                            id="delete-form-grid-{{ $service->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form-grid-{{ $service->id }}', 'Deseja excluir este culto?')" 
                                class="p-3 bg-white text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border border-gray-100">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Services List View -->
        <div x-show="view === 'list'" x-transition.fade.duration.300ms class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            @if(auth()->user()->role === 'admin')
                                <th class="px-8 py-6 text-[10px] font-black w-10">
                                    <input type="checkbox" id="selectAllCheckbox" 
                                        class="rounded-lg border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                </th>
                            @endif
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pregador</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Partic.</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Financ. Total</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($services as $service)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                @if(auth()->user()->role === 'admin')
                                    <td class="px-8 py-6">
                                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" form="bulkActionForm"
                                            class="service-checkbox rounded-lg border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                    </td>
                                @endif
                                <td class="px-8 py-6 font-black text-gray-900">{{ $service->date->format('d/m/Y') }}</td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 {{ $service->service_type === 'teaching' ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600' }} rounded-full text-[10px] font-bold uppercase">
                                        @switch($service->service_type)
                                            @case('1st') 1º @break
                                            @case('2nd') 2º @break
                                            @case('3rd') 3º @break
                                            @case('4th') 4º @break
                                            @case('teaching') Ensino @break
                                            @default Especial
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="font-bold {{ ($service->preacher_id === null && $service->preacher_name) ? 'text-orange-600 bg-orange-50 px-2 py-0.5 rounded-lg' : 'text-gray-600' }}">
                                        @if($service->preacher)
                                            {{ $service->preacher->name }}
                                        @else
                                            {{ $service->preacher_name ?? 'N/A' }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center font-black {{ $service->service_type === 'teaching' ? 'text-orange-600' : 'text-blue-600' }}">{{ $service->total_participation }}</td>
                                <td class="px-8 py-6 text-right font-black {{ $service->service_type === 'teaching' ? 'text-orange-600' : 'text-blue-600' }}">{{ number_format($service->total_financial, 0, ',', '.') }} MT</td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2 transition-all">
                                        <!-- detalhes do culto -->
                                        <a href="{{ route('services.show', $service) }}" class="p-2 {{ $service->service_type === 'teaching' ? 'text-orange-600 hover:bg-orange-50' : 'text-blue-600 hover:bg-blue-50' }} rounded-lg transition-colors">
                                            <i class="bi bi-info-circle text-lg"></i>
                                        </a>
                                        <!-- download pdf -->
                                        <a href="{{ route('services.download-pdf', $service) }}" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="bi bi-file-earmark-pdf text-lg"></i>
                                        </a>
                                        <!-- editar -->
                                        <a href="{{ route('services.edit', $service) }}" class="p-2 {{ $service->service_type === 'teaching' ? 'text-orange-600 hover:bg-orange-50' : 'text-blue-600 hover:bg-blue-50' }} rounded-lg transition-colors">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </a>
                                        <form action="{{ route('services.destroy', $service) }}" method="POST"
                                            id="delete-form-list-{{ $service->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-list-{{ $service->id }}', 'Deseja excluir este culto?')" 
                                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <i class="bi bi-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Pagination -->
        <div class="mt-12">
            {{ $services->links() }}
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
    <script>
        const selectAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.service-checkbox');
        const bulkBtn = document.getElementById('bulkDeleteBtn');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkBtn();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBtn);
        });

        function updateBulkBtn() {
            const count = document.querySelectorAll('.service-checkbox:checked').length;
            if (count > 0) {
                bulkBtn.disabled = false;
                bulkBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'hidden');
                bulkBtn.innerHTML = `<i class="bi bi-trash-fill mr-2"></i> Excluir ${count} Culto(s)`;
            } else {
                bulkBtn.disabled = true;
                bulkBtn.classList.add('opacity-50', 'cursor-not-allowed', 'hidden');
            }
        }

        function bulkDelete() {
            confirmAction(
                'Confirmação de Exclusão em Massa',
                'Você tem certeza que deseja excluir os registros de culto selecionados? Esta ação é irreversível.',
                'warning',
                'Sim, excluir tudo!',
                null
            ).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulkActionForm').submit();
                }
            });
        }

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
                            
                            // Re-initialize bulk action listeners if needed
                            // In this case, checkboxes are part of the innerHTML so they need new listeners
                            // or we use event delegation
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