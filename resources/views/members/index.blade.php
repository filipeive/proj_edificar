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

@section('header-actions')
    @if($userRole !== 'secretaria')
        <div class="md:hidden">
            <a href="{{ route('members.create') }}" 
                class="relative bg-gradient-to-r from-blue-600 to-blue-700 text-white p-3 rounded-2xl hover:from-blue-700 hover:to-blue-800 transition-all flex items-center justify-center shadow-xl shadow-blue-600/30 active:scale-95 group">
                <i class="bi bi-person-plus-fill text-2xl group-hover:scale-110 transition-transform"></i>
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
            </a>
        </div>
    @endif
@endsection

@section('content')
    <div class="space-y-6" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : 'list',
            selected: [],
            showBulkModal: false,
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid';
                }
            },
            toggleAll() {
                const allIds = {{ Js::from($members->pluck('id')) }};
                if (this.selected.length === allIds.length) {
                    this.selected = [];
                } else {
                    this.selected = allIds;
                }
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('members_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('members_view') || 'list')"
        @resize.window.debounce.500ms="updateView()">
        
        <!-- Enhanced Bulk Action Bar with Glassmorphism -->
        <div x-show="selected.length > 0" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
             class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white rounded-3xl shadow-2xl p-5 flex items-center gap-6 pointer-events-auto border border-gray-700/50 backdrop-blur-xl bg-opacity-95 max-w-2xl w-full">
                <div class="flex items-center gap-3 pl-2">
                    <div class="relative">
                        <span class="absolute inset-0 bg-blue-500 rounded-xl blur-md opacity-50 animate-pulse"></span>
                        <span class="relative bg-gradient-to-br from-blue-500 to-blue-600 text-sm font-black px-3.5 py-1.5 rounded-xl shadow-lg" x-text="selected.length"></span>
                    </div>
                    <span class="text-sm font-semibold tracking-wide">selecionado<span x-show="selected.length > 1">s</span></span>
                </div>
                
                <div class="h-10 w-px bg-gradient-to-b from-transparent via-gray-600 to-transparent"></div>
                
                <div class="flex items-center gap-3 flex-1 justify-end">
                    <button @click="selected = []" 
                        class="px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200">
                        Cancelar
                    </button>
                    @if($userRole !== 'secretaria')
                        <form method="POST" action="{{ route('members.bulk-destroy') }}" 
                              @submit.prevent="
                                Swal.fire({
                                    title: 'Tem certeza?',
                                    text: 'Você está prestes a excluir ' + selected.length + ' membro(s). Esta ação não pode ser desfeita!',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#dc2626',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Sim, excluir!',
                                    cancelButtonText: 'Cancelar',
                                    customClass: {
                                        popup: 'rounded-3xl',
                                        confirmButton: 'rounded-xl font-bold',
                                        cancelButton: 'rounded-xl font-bold'
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $el.submit();
                                    }
                                })
                              ">
                            @csrf
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="selected_ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                class="relative bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 shadow-lg shadow-red-600/30 flex items-center gap-2.5 overflow-hidden group">
                                <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                                <i class="bi bi-trash-fill relative z-10 group-hover:scale-110 transition-transform"></i>
                                <span class="relative z-10">Excluir</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- CSS Personalizado para Select Moderno -->
        <style>
            /* Modern Select Styling */
            .modern-select {
                background-image: none !important;
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .modern-select:focus {
                outline: none;
            }
            
            .modern-select option {
                padding: 12px;
                background: white;
                color: #1f2937;
            }
            
            .dark .modern-select option {
                background: #1f2937;
                color: #f3f4f6;
            }
            
            /* Select Arrow Animation */
            .select-arrow {
                transition: transform 0.2s ease;
            }
            
            .modern-select:focus ~ .select-arrow-wrapper .select-arrow {
                transform: rotate(180deg);
            }
            
            /* Smooth Filter Container */
            .filter-container {
                backdrop-filter: blur(10px);
                animation: slideDown 0.3s ease;
            }
            
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Input Focus Glow */
            .search-input:focus {
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }
            
            .modern-select:focus {
                box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
            }

            /* Icon Inside Input */
            .input-icon {
                pointer-events: none;
                transition: all 0.2s ease;
            }

            .search-input:focus ~ .input-icon-left,
            .modern-select:focus ~ .input-icon-left {
                transform: scale(1.1);
            }
        </style>

        <!-- Enhanced Search & Actions Header -->
        <div class="relative bg-white dark:bg-gray-800 p-4 md:p-5 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300 z-30 filter-container">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
                <!-- Search & Filters -->
                <div class="flex-1 w-full xl:max-w-4xl">
                    <form method="GET" action="{{ route('members.index') }}" class="flex flex-col md:flex-row gap-3" x-data>
                        <!-- Search Input - Com ícone integrado -->
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none md:hidden">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                                    <i class="bi bi-search text-blue-600 dark:text-blue-400 text-sm"></i>
                                </div>
                            </div>
                            <input type="text" name="search" data-live-search="manual" value="{{ request('search') }}" 
                                @input.debounce.500ms="$el.form.submit()"
                                placeholder="🔍   Buscar por nome, email..."
                                class="search-input w-full pl-14 md:pl-4 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:border-blue-400 dark:focus:border-blue-500 focus:bg-white dark:focus:bg-gray-700 font-medium text-sm transition-all duration-200 placeholder:text-gray-400 dark:placeholder:text-gray-500 text-gray-900 dark:text-white hover:border-gray-300 dark:hover:border-gray-500">
                        </div>

                        <!-- Cell Filter - Dropdown com ícone integrado -->
                        @if($userRole !== 'lider_celula' && $availableCells->count() > 1)
                            <div class="relative z-50">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <div class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                        <i class="bi bi-funnel-fill text-purple-600 dark:text-purple-400 text-sm"></i>
                                    </div>
                                </div>
                                <select name="cell_id" 
                                    @change="$el.form.submit()"
                                    class="modern-select w-full md:w-64 appearance-none pl-14 pr-10 py-2.5 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-xl font-semibold text-sm text-purple-700 dark:text-purple-300 cursor-pointer hover:bg-purple-100 dark:hover:bg-purple-900/30 hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-200">
                                    <option value="">Todas as Células</option>
                                    @foreach($availableCells as $cell)
                                        <option value="{{ $cell->id }}" {{ request('cell_id') == $cell->id ? 'selected' : '' }}>
                                            {{ $cell->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="select-arrow-wrapper absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="select-arrow bi bi-chevron-down text-purple-600 dark:text-purple-400 text-xs"></i>
                                </div>
                            </div>
                        @endif

                        <!-- Clear Filter Button - Compacto -->
                        @if(request('search') || request('cell_id'))
                            <button type="button" onclick="window.location='{{ route('members.index') }}'"
                                class="w-10 h-10 md:w-auto md:px-4 bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-200 flex items-center justify-center border border-red-200 dark:border-red-800 hover:border-red-300 dark:hover:border-red-700 group">
                                <i class="bi bi-x-lg text-sm group-hover:rotate-90 transition-transform duration-300"></i>
                            </button>
                        @endif
                    </form>
                </div>

                <!-- View Toggles & Actions - Compacto -->
                <div class="flex items-center gap-2.5 w-full xl:w-auto">
                    <!-- View Toggle - Mais Compacto -->
                    <div class="hidden md:flex bg-gray-100 dark:bg-gray-700/50 p-1 rounded-xl border border-gray-200 dark:border-gray-600">
                        <button @click="view = 'list'" 
                            :class="view === 'list' ? 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="p-2 rounded-lg transition-all duration-200"
                            title="Visualização em Lista">
                            <i class="bi bi-list-ul text-base"></i>
                        </button>
                        <button @click="view = 'grid'" 
                            :class="view === 'grid' ? 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="p-2 rounded-lg transition-all duration-200"
                            title="Visualização em Grade">
                            <i class="bi bi-grid-fill text-base"></i>
                        </button>
                    </div>

                    <!-- Add Member Button - Compacto e Moderno -->
                    @if($userRole !== 'secretaria')
                        <a href="{{ route('members.create') }}" 
                            class="hidden md:flex flex-1 xl:flex-none px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 dark:hover:from-blue-600 dark:hover:to-blue-700 transition-all duration-200 font-bold text-xs items-center justify-center gap-2 shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 active:scale-95 group relative overflow-hidden">
                            <span class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                            <i class="bi bi-plus-circle-fill text-base relative z-10"></i>
                            <span class="relative z-10 whitespace-nowrap">@if ($userRole === 'lider_celula') Novo Membro @else Adicionar Membro @endif</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enhanced Global Stats Grid with Gradient Backgrounds -->
        <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Members - Enhanced with Animation -->
            <div class="relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 p-8 rounded-3xl shadow-xl shadow-blue-500/30 flex flex-col justify-center text-center text-white overflow-hidden group hover:shadow-2xl hover:shadow-blue-500/40 transition-all duration-500 hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <i class="bi bi-people-fill absolute -right-8 -bottom-8 text-[10rem] opacity-10 group-hover:scale-110 group-hover:rotate-12 transition-all duration-700"></i>
                <p class="text-6xl font-black tracking-tighter relative z-10 tabular-nums group-hover:scale-110 transition-transform duration-300">{{ $members->total() }}</p>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mt-3 opacity-90 relative z-10">Total de Membros</p>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/20"></div>
            </div>

            <!-- Active Members -->
            <div class="relative bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-lg border-2 border-gray-100 dark:border-gray-700 flex flex-col justify-center text-center group hover:border-green-300 dark:hover:border-green-600 transition-all duration-300 hover:shadow-xl hover:shadow-green-500/10 hover:scale-105 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <i class="bi bi-check-circle-fill absolute -right-6 -bottom-6 text-8xl text-green-500/5 dark:text-green-400/5 group-hover:scale-110 transition-transform duration-500"></i>
                <p class="text-6xl font-black text-green-600 dark:text-green-400 tracking-tighter tabular-nums relative z-10 group-hover:scale-110 transition-transform duration-300">{{ $members->where('is_active', true)->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mt-3 relative z-10 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Membros Ativos</p>
            </div>

            <!-- Cell Units -->
            <div class="relative bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-lg border-2 border-gray-100 dark:border-gray-700 flex flex-col justify-center text-center group hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/10 hover:scale-105 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-fuchsia-50 dark:from-purple-900/10 dark:to-fuchsia-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <i class="bi bi-diagram-3-fill absolute -right-6 -bottom-6 text-8xl text-purple-500/5 dark:text-purple-400/5 group-hover:scale-110 transition-transform duration-500"></i>
                <p class="text-6xl font-black text-purple-600 dark:text-purple-400 tracking-tighter tabular-nums relative z-10 group-hover:scale-110 transition-transform duration-300">{{ $availableCells->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mt-3 relative z-10 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Unidades de Células</p>
            </div>

            <!-- With Commitment -->
            <div class="relative bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-lg border-2 border-gray-100 dark:border-gray-700 flex flex-col justify-center text-center group hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-300 hover:shadow-xl hover:shadow-orange-500/10 hover:scale-105 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/10 dark:to-amber-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <i class="bi bi-award-fill absolute -right-6 -bottom-6 text-8xl text-orange-500/5 dark:text-orange-400/5 group-hover:scale-110 transition-transform duration-500"></i>
                <p class="text-6xl font-black text-orange-600 dark:text-orange-400 tracking-tighter tabular-nums relative z-10 group-hover:scale-110 transition-transform duration-300">{{ $members->whereNotNull('commitments')->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mt-3 relative z-10 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Com Compromisso</p>
            </div>
        </div>

        <!-- Enhanced List View -->
        <div x-show="view === 'list'" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full table-compact">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-700/30 border-b-2 border-gray-200 dark:border-gray-600">
                            <th class="pl-8 py-6 w-16 text-center">
                                <input type="checkbox" @click="toggleAll()" 
                                    :checked="selected.length === {{ $members->count() }} && selected.length > 0"
                                    class="w-5 h-5 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer transition-all hover:scale-110">
                            </th>
                            <th class="px-4 py-6 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Membro</th>
                            <th class="px-6 py-6 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Zona</th>
                            <th class="px-6 py-6 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Cargo</th>
                            <th class="px-6 py-6 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Contacto</th>
                            <th class="px-6 py-6 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Estrutura</th>
                            <th class="px-6 py-6 text-center text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest pr-10">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($members as $member)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-indigo-50/30 dark:hover:from-blue-900/10 dark:hover:to-indigo-900/5 transition-all duration-200 group">
                                <td class="pl-8 py-5 text-center relative">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-500 opacity-0 transition-all duration-200 group-hover:opacity-100" :class="{'opacity-100 shadow-lg shadow-blue-500/50': selected.includes({{ $member->id }})}"></div>
                                    <input type="checkbox" value="{{ $member->id }}" x-model="selected"
                                        class="w-5 h-5 rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer transition-all hover:scale-110">
                                </td>
                                <td class="px-4 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl blur-md opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
                                            <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-black text-lg shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">{{ $member->name }}</p>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-mono uppercase tracking-tighter mt-0.5">ID: {{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($member->cell && $member->cell->supervision && $member->cell->supervision->zone)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-orange-100 to-amber-100 dark:from-orange-900/30 dark:to-amber-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 text-xs shadow-sm group-hover:scale-110 transition-transform">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $member->cell->supervision->zone->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray-300 dark:text-gray-600 font-bold uppercase tracking-wider">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    @if($member->role === 'lider_celula')
                                        <span class="inline-flex items-center px-3.5 py-1.5 bg-gradient-to-r from-purple-50 to-fuchsia-50 dark:from-purple-900/30 dark:to-fuchsia-900/30 text-purple-600 dark:text-purple-400 rounded-full text-[10px] font-black uppercase tracking-widest border-2 border-purple-100 dark:border-purple-800 shadow-sm hover:shadow-md transition-shadow">
                                            <i class="bi bi-star-fill mr-1.5 text-xs"></i>Líder
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3.5 py-1.5 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 text-blue-600 dark:text-blue-400 rounded-full text-[10px] font-black uppercase tracking-widest border-2 border-blue-100 dark:border-blue-800 shadow-sm hover:shadow-md transition-shadow">
                                            Membro
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400 group/email">
                                            <i class="bi bi-envelope-fill text-[10px] group-hover/email:text-blue-500 transition-colors"></i>
                                            <span class="text-xs font-medium truncate max-w-[180px]">{{ $member->email }}</span>
                                        </div>
                                        @if($member->phone)
                                            <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 group/phone">
                                                <i class="bi bi-telephone-fill text-[10px] group-hover/phone:text-green-500 transition-colors"></i>
                                                <span class="text-[10px] font-medium">{{ $member->phone }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($member->cell)
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $member->cell->name }}</span>
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-semibold">Sup: {{ $member->cell->supervision->name ?? '-' }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-red-500 dark:text-red-400 uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Sem Alocação
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($member->is_active)
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 dark:bg-green-900/30">
                                            <span class="relative flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500 shadow-lg shadow-green-500/50"></span>
                                            </span>
                                            <span class="text-[9px] font-black uppercase text-green-600 dark:text-green-400">Ativo</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 dark:bg-red-900/30">
                                            <span class="inline-flex h-3 w-3 rounded-full bg-red-500 shadow-lg shadow-red-500/50"></span>
                                            <span class="text-[9px] font-black uppercase text-red-600 dark:text-red-400">Inativo</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right pr-10">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('members.show', $member) }}" title="Detalhes"
                                            class="action-icon bg-white dark:bg-gray-700 border-2 border-gray-100 dark:border-gray-600 text-gray-400 dark:text-gray-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 dark:hover:border-blue-600 shadow-sm hover:shadow-md hover:scale-110 transition-all duration-200">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @if($userRole !== 'secretaria')
                                            <a href="{{ route('members.edit', $member) }}" title="Editar"
                                                class="action-icon bg-white dark:bg-gray-700 border-2 border-gray-100 dark:border-gray-600 text-gray-400 dark:text-gray-300 hover:bg-orange-500 hover:text-white hover:border-orange-500 dark:hover:border-orange-500 shadow-sm hover:shadow-md hover:scale-110 transition-all duration-200">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-10 py-24 text-center">
                                    <div class="flex flex-col items-center gap-6 text-gray-300 dark:text-gray-600">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-gray-200 dark:bg-gray-700 rounded-full blur-2xl opacity-50"></div>
                                            <div class="relative w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700/50 dark:to-gray-700/30 rounded-full flex items-center justify-center shadow-inner">
                                                <i class="bi bi-search text-4xl opacity-40"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-black text-xl text-gray-400 dark:text-gray-500 mb-2">Nenhum membro encontrado</p>
                                            <p class="text-sm text-gray-400 dark:text-gray-500">Tente ajustar os filtros da sua pesquisa.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enhanced Grid View -->
        <div x-show="view === 'grid'" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($members as $member)
                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg border-2 border-gray-100 dark:border-gray-700 flex flex-col group hover:shadow-2xl hover:shadow-blue-900/20 dark:hover:shadow-black/40 transition-all duration-300 relative overflow-hidden hover:scale-105 hover:border-blue-200 dark:hover:border-blue-700"
                     :class="{'ring-4 ring-blue-500 ring-offset-2 dark:ring-offset-gray-900 bg-blue-50/30 dark:bg-blue-900/20 border-blue-300 dark:border-blue-600 scale-105': selected.includes({{ $member->id }})}">
                    
                    <!-- Background Gradient on Hover -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-indigo-50/30 dark:from-blue-900/10 dark:to-indigo-900/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-3xl"></div>
                    
                    <!-- Selection Checkbox -->
                    <div class="absolute top-6 left-6 z-10">
                        <input type="checkbox" value="{{ $member->id }}" x-model="selected"
                            class="w-6 h-6 rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer shadow-md hover:scale-110 transition-all">
                    </div>

                    <!-- Enhanced Status Indicator -->
                    <div class="absolute top-6 right-6 z-10">
                        @if($member->is_active)
                            <div class="relative">
                                <span class="absolute inset-0 animate-ping inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-green-500 shadow-lg shadow-green-500/50 ring-2 ring-white dark:ring-gray-800"></span>
                            </div>
                        @else
                            <span class="inline-flex rounded-full h-3.5 w-3.5 bg-red-500 shadow-lg shadow-red-500/50 ring-2 ring-white dark:ring-gray-800"></span>
                        @endif
                    </div>

                    <div class="flex items-start gap-4 mb-5 pl-8 relative z-10">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl blur-lg opacity-0 group-hover:opacity-50 transition-opacity duration-500"></div>
                            <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-xl shadow-blue-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 pt-1">
                            <h4 class="text-base font-black text-gray-900 dark:text-white leading-tight mb-1.5 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-1">{{ $member->name }}</h4>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($member->role === 'lider_celula')
                                    <span class="inline-flex items-center text-[10px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 px-2 py-0.5 rounded-full">
                                        <i class="bi bi-star-fill mr-1 text-[8px]"></i>Líder
                                    </span>
                                @else
                                    <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded-full">Membro</span>
                                @endif
                                <span class="text-gray-300 dark:text-gray-600 text-[10px]">•</span>
                                <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500">#{{ $member->id }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6 flex-1 relative z-10">
                        <!-- Enhanced Zone Card -->
                        <div class="flex items-center gap-3 p-3.5 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-2xl border-2 border-orange-100 dark:border-orange-800/50 group-hover:border-orange-300 dark:group-hover:border-orange-700 transition-all shadow-sm hover:shadow-md">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white text-sm flex-shrink-0 shadow-md group-hover:scale-110 transition-transform">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[9px] font-black uppercase text-orange-600/70 dark:text-orange-400/70 tracking-wider mb-0.5">Zona</p>
                                <p class="text-xs font-bold text-orange-900 dark:text-orange-300 truncate">
                                    {{ $member->cell->supervision->zone->name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400 px-1.5 hover:text-blue-600 dark:hover:text-blue-400 transition-colors group/item">
                            <div class="w-7 flex justify-center text-sm group-hover/item:scale-110 transition-transform">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <span class="text-xs font-bold truncate">{{ $member->email }}</span>
                        </div>
                        
                        @if($member->phone)
                            <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400 px-1.5 hover:text-green-600 dark:hover:text-green-400 transition-colors group/item">
                                <div class="w-7 flex justify-center text-sm group-hover/item:scale-110 transition-transform">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <span class="text-xs font-bold">{{ $member->phone }}</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400 px-1.5 group/item">
                            <div class="w-7 flex justify-center text-sm group-hover/item:scale-110 transition-transform">
                                @if($member->cell)
                                    <i class="bi bi-diagram-3-fill"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle-fill text-red-500 dark:text-red-400"></i>
                                @endif
                            </div>
                            <span class="text-xs font-black uppercase text-gray-900 dark:text-white truncate">
                                {{ $member->cell ? $member->cell->name : 'Sem Célula' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto grid {{ $userRole !== 'secretaria' ? 'grid-cols-2' : 'grid-cols-1' }} gap-3 pt-6 border-t-2 border-gray-100 dark:border-gray-700 relative z-10">
                        <a href="{{ route('members.show', $member) }}" 
                            class="relative bg-gradient-to-r from-gray-900 to-gray-800 dark:from-gray-700 dark:to-gray-600 text-white text-center py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:from-blue-600 hover:to-blue-700 dark:hover:from-blue-600 dark:hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-blue-600/30 flex items-center justify-center gap-2.5 overflow-hidden group/btn">
                            <span class="absolute inset-0 bg-white/10 translate-x-full group-hover/btn:translate-x-0 transition-transform duration-500"></span>
                            <i class="bi bi-eye-fill relative z-10 group-hover/btn:scale-110 transition-transform"></i> 
                            <span class="relative z-10">Ver</span>
                        </a>
                        @if($userRole !== 'secretaria')
                            <a href="{{ route('members.edit', $member) }}" 
                                class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-center py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-orange-500 hover:text-orange-500 dark:hover:border-orange-400 dark:hover:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-200 flex items-center justify-center gap-2.5 shadow-sm hover:shadow-md hover:scale-105">
                                <i class="bi bi-pencil-fill"></i>Editar
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-3xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex flex-col items-center gap-6 text-gray-400 dark:text-gray-500 shadow-inner">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gray-300 dark:bg-gray-600 rounded-full blur-2xl opacity-30"></div>
                        <i class="bi bi-people text-8xl opacity-30 relative"></i>
                    </div>
                    <div class="text-center">
                        <p class="font-black text-xl mb-2">Nenhum membro encontrado</p>
                        <p class="text-sm">Comece adicionando novos membros à sua célula</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Enhanced Pagination -->
        @if($members->hasPages())
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-lg border-2 border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                {{ $members->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
