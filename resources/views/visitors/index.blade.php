@extends('layouts.app')

@section('title', 'Visitantes - Portal Life Church')
@section('page-title', 'Gestão de Visitantes')
@section('page-subtitle', 'Acompanhamento e integração de visitantes')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div x-data="{ 
                        view: window.innerWidth < 768 ? 'grid' : 'list',
                        updateView() {
                            if (window.innerWidth < 768 && this.view === 'list') {
                                this.view = 'grid'; // Optional: force grid on mobile resize
                            }
                        }
                    }"
        x-init="$watch('view', value => localStorage.setItem('visitors_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('visitors_view') || 'list')"
        @resize.window.debounce.500ms="updateView()" x-cloak>
        @section('header-actions')
            <a href="{{ route('visitors.create') }}"
                class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
                <i class="bi bi-person-plus-fill text-xl"></i>
            </a>
        @endsection
        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-50 p-3 rounded-xl">
                        <i class="bi bi-people-fill text-blue-600 text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</span>
                </div>
                <p class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 mt-2">Visitantes cadastrados</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-yellow-50 p-3 rounded-xl">
                        <i class="bi bi-clock-history text-yellow-600 text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pendentes</span>
                </div>
                <p class="text-3xl font-black text-yellow-600">{{ $stats['pending'] }}</p>
                <p class="text-xs text-gray-500 mt-2">Aguardando contato</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-50 p-3 rounded-xl">
                        <i class="bi bi-telephone-fill text-blue-600 text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Contatados</span>
                </div>
                <p class="text-3xl font-black text-blue-600">{{ $stats['contacted'] }}</p>
                <p class="text-xs text-gray-500 mt-2">Já foram contatados</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-50 p-3 rounded-xl">
                        <i class="bi bi-check-circle-fill text-green-600 text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Integrados</span>
                </div>
                <p class="text-3xl font-black text-green-600">{{ $stats['integrated'] }}</p>
                <p class="text-xs text-gray-500 mt-2">Integrados em células</p>
            </div>
        </div>

        <!-- Filtros e Ações -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <h3 class="text-xl font-black text-gray-900">Filtros</h3>
                <div class="flex gap-3">
                    @if(auth()->user()->role === 'admin')
                        <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all hidden">
                            <i class="bi bi-trash-fill mr-2"></i>Remover Selecionados
                        </button>
                    @endif

                    <div class="hidden md:flex bg-gray-50 p-1 rounded-2xl border border-gray-100 mr-2">
                        <button @click="view = 'list'"
                            :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2 text-xs font-black uppercase tracking-widest">
                            <i class="bi bi-list-ul text-sm"></i>
                            <span>Lista</span>
                        </button>
                        <button @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2 text-xs font-black uppercase tracking-widest">
                            <i class="bi bi-grid-fill text-sm"></i>
                            <span>Grelha</span>
                        </button>
                    </div>
                    <div class="flex gap-1 btn-group">
                        <a href="https://chat.whatsapp.com/DxAf8sMvMDYDDhrIV1wRxC" target="_blank"
                            class="bg-[#25D366] text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-[#128C7E] transition-all flex items-center">
                            <i class="bi bi-whatsapp mr-2"></i>Grupo Supervisores
                        </a>
                        <a href="{{ route('visitors.export', request()->all()) }}"
                            class="bg-green-600 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-green-700 transition-all">
                            <i class="bi bi-file-earmark-excel mr-2"></i>Exportar Excel
                        </a>
                        <a href="{{ route('visitors.create') }}"
                            class="flex bg-blue-600 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all items-center">
                            <i class="bi bi-plus-lg mr-2"></i>Novo
                        </a>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('visitors.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="Nome, telefone, bairro...">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="contatado" {{ request('status') == 'contatado' ? 'selected' : '' }}>Contatado</option>
                        <option value="integrado" {{ request('status') == 'integrado' ? 'selected' : '' }}>Integrado</option>
                        <option value="sem_interesse" {{ request('status') == 'sem_interesse' ? 'selected' : '' }}>Sem
                            Interesse
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Zona</label>
                    <select name="zone_id"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Todas</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Data Início</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-orange-600 text-white px-6 py-2 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-orange-700 transition-all">
                        <i class="bi bi-search mr-2"></i>Filtrar
                    </button>
                    <a href="{{ route('visitors.index') }}"
                        class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Listagem -->
        <div class="relative">
            <form id="bulkActionForm" method="POST" action="{{ route('visitors.bulk-delete') }}">
                @csrf
                <!-- Grid View -->
                <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                    @forelse($visitors as $visitor)
                        <div
                            class="bg-white p-4 md:p-8 rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300 relative">
                            <div class="absolute top-4 right-4 md:top-6 md:right-6 flex flex-col items-end gap-2">
                                {!! $visitor->status_badge !!}
                                @if(auth()->user()->role === 'admin')
                                    <input type="checkbox" form="bulkActionForm" name="visitor_ids[]" value="{{ $visitor->id }}"
                                        class="visitor-checkbox rounded-lg border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                @endif
                            </div>

                            <div
                                class="w-10 h-10 md:w-14 md:h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-lg md:text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 mb-4 md:mb-6">
                                {{ strtoupper(substr($visitor->name, 0, 1)) }}
                            </div>

                            <div class="mb-6">
                                <h4
                                    class="text-xl font-black text-gray-900 leading-tight mb-1 group-hover:text-blue-600 transition-colors">
                                    {{ $visitor->name }}
                                </h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                    @if($visitor->age) {{ $visitor->age }} anos @endif
                                    @if($visitor->gender) • {{ ucfirst($visitor->gender) }} @endif
                                </p>
                            </div>

                            <div class="space-y-4 mb-8 flex-1">
                                @if($visitor->phone)
                                    <div class="flex items-center gap-3 text-gray-500">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                            <i class="bi bi-telephone text-blue-500"></i>
                                        </div>
                                        <span class="text-sm font-bold">{{ $visitor->phone }}</span>
                                    </div>
                                @endif

                                <div class="flex items-center gap-3 text-gray-500">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                        <i class="bi bi-calendar-event text-orange-500"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase text-gray-900">Visita:
                                            {{ $visitor->visit_date->format('d/m/Y') }}</span>
                                        <span
                                            class="text-[10px] text-gray-400 font-medium">{{ $visitor->visit_date->diffForHumans() }}</span>
                                    </div>
                                </div>

                                @if($visitor->zone)
                                    <div class="flex items-center gap-3 text-gray-500">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm font-bold">
                                            <i class="bi bi-geo-alt text-red-500"></i>
                                        </div>
                                        <span class="text-xs font-black uppercase text-gray-900">
                                            {{ $visitor->zone->name }}
                                        </span>
                                    </div>
                                @endif

                                @if($visitor->neighborhood)
                                    <div class="flex items-center gap-3 text-gray-500">
                                        <i class="bi bi-house-door text-[10px] ml-2"></i>
                                        <span class="text-[10px] font-medium italic">{{ $visitor->neighborhood }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('visitors.show', $visitor) }}"
                                    class="flex-1 bg-gray-900 text-white text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg hover:shadow-blue-200 active:scale-95 flex items-center justify-center gap-2">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a href="{{ route('visitors.edit', $visitor) }}"
                                    class="flex-1 bg-gray-50 text-gray-400 text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <form id="grid-delete-visitor-{{ $visitor->id }}"
                                        action="{{ route('visitors.destroy', $visitor) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('grid-delete-visitor-{{ $visitor->id }}')"
                                            class="w-12 bg-red-50 text-red-400 text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all active:scale-95 flex items-center justify-center">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-20 bg-white rounded-[2.5rem] border border-dashed border-gray-200 flex flex-col items-center gap-4 text-gray-300">
                            <i class="bi bi-inbox text-7xl"></i>
                            <p class="font-bold text-lg">Nenhum visitante encontrado</p>
                        </div>
                    @endforelse
                </div>

                <!-- List View -->
                <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr
                                    class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                    @if(auth()->user()->role === 'admin')
                                        <th class="px-6 py-4 text-left w-10">
                                            <input type="checkbox" id="selectAllCheckbox"
                                                class="rounded-lg border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                        </th>
                                    @endif
                                    <th class="px-6 py-4 text-left">Visitante</th>
                                    <th class="px-6 py-4 text-left">Contato</th>
                                    <th class="px-6 py-4 text-left">Data Visita</th>
                                    <th class="px-6 py-4 text-left">Culto</th>
                                    <th class="px-6 py-4 text-left">Zona</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($visitors as $visitor)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        @if(auth()->user()->role === 'admin')
                                            <td class="px-6 py-4">
                                                <input type="checkbox" name="visitor_ids[]" value="{{ $visitor->id }}"
                                                    class="visitor-checkbox rounded-lg border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                            </td>
                                        @endif
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $visitor->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    @if($visitor->age) {{ $visitor->age }} anos @endif
                                                    @if($visitor->gender) • {{ ucfirst($visitor->gender) }} @endif
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div>
                                                @if($visitor->phone)
                                                    <p class="text-sm text-gray-900"><i
                                                            class="bi bi-telephone mr-1"></i>{{ $visitor->phone }}</p>
                                                @endif
                                                @if($visitor->neighborhood)
                                                    <p class="text-xs text-gray-500"><i
                                                            class="bi bi-geo-alt mr-1"></i>{{ $visitor->neighborhood }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ $visitor->visit_date->format('d/m/Y') }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $visitor->visit_date->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($visitor->service)
                                                <p class="text-sm text-gray-900">{{ $visitor->service->service_type }}</p>
                                                <p class="text-xs text-gray-500">{{ $visitor->service->date->format('d/m/Y') }}</p>
                                            @else
                                                <span class="text-xs text-gray-400">Não informado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($visitor->zone)
                                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">
                                                    {{ $visitor->zone->name }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">Não atribuído</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {!! $visitor->status_badge !!}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('visitors.show', $visitor) }}"
                                                    class="text-blue-600 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition-all"
                                                    title="Ver detalhes">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                <a href="{{ route('visitors.edit', $visitor) }}"
                                                    class="text-orange-600 hover:text-orange-700 p-2 hover:bg-orange-50 rounded-lg transition-all"
                                                    title="Editar">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                @if(auth()->user()->role === 'admin')
                                                    <form id="list-delete-visitor-{{ $visitor->id }}"
                                                        action="{{ route('visitors.destroy', $visitor) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            onclick="confirmDelete('list-delete-visitor-{{ $visitor->id }}')"
                                                            class="text-red-600 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-all"
                                                            title="Eliminar">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                            <i class="bi bi-inbox text-4xl mb-4 block"></i>
                                            <p class="font-bold">Nenhum visitante encontrado</p>
                                            <p class="text-sm mt-2">Cadastre o primeiro visitante para começar</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>

        @if($visitors->hasPages())
            <div class="mt-6">
                {{ $visitors->links() }}
            </div>
        @endif
    </div>

    <!-- Scripts Section -->
    @if(auth()->user()->role === 'admin')
        <script>
            // Bulk Action Logic with Event Delegation
            document.addEventListener('change', function (e) {
                if (e.target.id === 'selectAllCheckbox') {
                    const checkboxes = document.querySelectorAll('.visitor-checkbox');
                    checkboxes.forEach(cb => cb.checked = e.target.checked);
                    updateBulkBtnState();
                }

                if (e.target.classList.contains('visitor-checkbox')) {
                    updateBulkBtnState();
                }
            });

            function updateBulkBtnState() {
                const bulkBtn = document.getElementById('bulkDeleteBtn');
                const count = document.querySelectorAll('.visitor-checkbox:checked').length;

                if (!bulkBtn) return;

                if (count > 0) {
                    bulkBtn.disabled = false;
                    bulkBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'hidden');
                    bulkBtn.innerHTML = `<i class="bi bi-trash-fill mr-2"></i> Remover ${count}`;
                } else {
                    bulkBtn.disabled = true;
                    bulkBtn.classList.add('opacity-50', 'cursor-not-allowed', 'hidden');
                }
            }

            function bulkDelete() {
                confirmAction(
                    'Confirmação de Remoção',
                    'Deseja remover os visitantes selecionados?',
                    'warning',
                    'Sim, remover!',
                    null
                ).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('bulkActionForm');
                        form.submit();
                    }
                });
            }
        </script>
    @endif

    <!-- Dynamic Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('input[name="search"]');
            const filterForm = searchInput && searchInput.closest('form');

            if (searchInput && filterForm) {
                let timeout = null;
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    timeout = setTimeout(function () {
                        const formData = new FormData(filterForm);
                        const params = new URLSearchParams(formData);

                        document.body.style.cursor = 'wait';

                        fetch(`${filterForm.action}?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');

                                // Replace Grid View Content
                                const newGrid = doc.querySelector('[x-show="view === \'grid\'"]');
                                const currentGrid = document.querySelector('[x-show="view === \'grid\'"]');
                                if (newGrid && currentGrid) currentGrid.innerHTML = newGrid.innerHTML;

                                // Replace List View Content
                                const newList = doc.querySelector('[x-show="view === \'list\'"]');
                                const currentList = document.querySelector('[x-show="view === \'list\'"]');
                                if (newList && currentList) currentList.innerHTML = newList.innerHTML;

                                // Replace Pagination
                                const newPagination = doc.querySelector('.mt-6');
                                const currentPagination = document.querySelector('.mt-6');
                                if (newPagination && currentPagination) currentPagination.innerHTML = newPagination.innerHTML;

                                document.body.style.cursor = 'default';

                                // Update bulk button state after search refresh
                                if (typeof updateBulkBtnState === 'function') updateBulkBtnState();
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
@endsection