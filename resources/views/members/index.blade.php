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
        <a href="{{ route('members.create') }}" 
            class="bg-blue-600 text-white p-2 rounded-xl hover:bg-blue-700 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20 active:scale-95">
            <i class="bi bi-person-plus-fill text-2xl"></i>
        </a>
    @endif
@endsection

@section('content')
    <div class="space-y-8" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : 'list',
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid';
                }
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('members_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('members_view') || 'list')"
        @resize.window.debounce.500ms="updateView()">
        <!-- Search & Actions Header -->
        <div class="bg-white p-4 md:p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
                <!-- Search & Filters -->
                <div class="flex-1 w-full xl:max-w-4xl">
                    <form method="GET" action="{{ route('members.index') }}" class="flex flex-col md:flex-row gap-3" x-data>
                        <div class="flex-1 relative group">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                @input.debounce.500ms="$el.form.submit()"
                                placeholder="Pesquisa dinâmica por nome..."
                                class="w-full pl-14 pr-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 font-medium text-sm transition-all shadow-inner placeholder:text-gray-400">
                        </div>

                        @if($userRole !== 'lider_celula' && $availableCells->count() > 1)
                            <select name="cell_id" 
                                @change="$el.form.submit()"
                                class="px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-xs uppercase tracking-widest text-gray-600 cursor-pointer hover:bg-gray-100 transition-colors">
                                <option value="">Todas as Células</option>
                                @foreach($availableCells as $cell)
                                    <option value="{{ $cell->id }}" {{ request('cell_id') == $cell->id ? 'selected' : '' }}>
                                        {{ $cell->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        @if(request('search') || request('cell_id'))
                            <a href="{{ route('members.index') }}" class="px-5 py-4 bg-red-50 text-red-500 rounded-2xl hover:bg-red-100 transition-all flex items-center justify-center border border-transparent hover:border-red-200">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- View Toggles & Actions -->
                <div class="flex items-center gap-3 w-full xl:w-auto">
                    <div class="hidden md:flex bg-gray-50 p-1.5 rounded-2xl border border-gray-100">
                        <button @click="view = 'list'" 
                            :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="p-3 rounded-xl transition-all duration-300">
                            <i class="bi bi-list-ul text-lg"></i>
                        </button>
                        <button @click="view = 'grid'" 
                            :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="p-3 rounded-xl transition-all duration-300">
                            <i class="bi bi-grid-fill text-lg"></i>
                        </button>
                    </div>

                    @if($userRole !== 'secretaria')
                        <a href="{{ route('members.create') }}" 
                            class="hidden md:flex flex-1 xl:flex-none px-6 py-4 bg-gray-900 text-white rounded-2xl hover:bg-blue-600 transition-all font-black text-[10px] uppercase tracking-widest items-center justify-center gap-3 shadow-lg hover:shadow-blue-600/20 active:scale-95">
                            <i class="bi bi-person-plus-fill text-lg"></i>
                            <span>@if ($userRole === 'lider_celula') Novo Membro @else Novo Membro / Líder @endif</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Global Stats Grid -->
        <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-8 rounded-[2.5rem] shadow-lg shadow-blue-500/20 flex flex-col justify-center text-center text-white relative overflow-hidden group">
                <i class="bi bi-people-fill absolute -right-6 -bottom-6 text-9xl opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
                <p class="text-5xl font-black tracking-tighter relative z-10">{{ $members->total() }}</p>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mt-2 opacity-80 relative z-10">Total de Membros</p>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-center text-center group hover:border-green-200 transition-colors">
                <p class="text-5xl font-black text-green-600 tracking-tighter">{{ $members->where('is_active', true)->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Membros Ativos</p>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-center text-center group hover:border-purple-200 transition-colors">
                <p class="text-5xl font-black text-purple-600 tracking-tighter">{{ $availableCells->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Unidades de Células</p>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-center text-center group hover:border-orange-200 transition-colors">
                <p class="text-5xl font-black text-orange-600 tracking-tighter">{{ $members->whereNotNull('commitments')->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Com Compromisso</p>
            </div>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest pl-10">Membro</th>
                            <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Zona</th>
                            <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Cargo</th>
                            <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Contacto</th>
                            <th class="px-6 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Estrutura</th>
                            <th class="px-6 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest pr-10">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($members as $member)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-8 py-5 pl-10">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-black text-lg shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform duration-300">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">{{ $member->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-mono uppercase tracking-tighter">ID: {{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <!-- COLUNA ZONA -->
                                <td class="px-6 py-5">
                                    @if($member->cell && $member->cell->supervision && $member->cell->supervision->zone)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-[10px]">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <span class="text-xs font-bold text-gray-700">{{ $member->cell->supervision->zone->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray-300 font-bold uppercase tracking-wider">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    @if($member->role === 'lider_celula')
                                        <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100 shadow-sm">Líder</span>
                                    @else
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100 shadow-sm">Membro</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col gap-0.5">
                                        <div class="flex items-center gap-1.5 text-gray-600">
                                            <i class="bi bi-envelope text-[10px]"></i>
                                            <span class="text-xs font-medium truncate max-w-[150px]">{{ $member->email }}</span>
                                        </div>
                                        @if($member->phone)
                                            <div class="flex items-center gap-1.5 text-gray-400">
                                                <i class="bi bi-telephone text-[10px]"></i>
                                                <span class="text-[10px] font-medium">{{ $member->phone }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($member->cell)
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-900 uppercase tracking-tight">{{ $member->cell->name }}</span>
                                            <span class="text-[10px] text-gray-400 font-medium">Sup: {{ $member->cell->supervision->name ?? '-' }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-red-400 uppercase tracking-wider flex items-center gap-1">
                                            <i class="bi bi-exclamation-circle-fill"></i> Sem Alocação
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($member->is_active)
                                        <span class="inline-flex w-2.5 h-2.5 rounded-full bg-green-500 shadow-lg shadow-green-500/30" title="Ativo"></span>
                                    @else
                                        <span class="inline-flex w-2.5 h-2.5 rounded-full bg-red-500 shadow-lg shadow-red-500/30" title="Inativo"></span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right pr-10">
                                    <div class="flex items-center justify-end gap-2 text-right">
                                        <a href="{{ route('members.show', $member) }}" class="w-8 h-8 rounded-xl bg-white border border-gray-100 text-gray-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @if($userRole !== 'secretaria')
                                            <a href="{{ route('members.edit', $member) }}" class="w-8 h-8 rounded-xl bg-white border border-gray-100 text-gray-400 hover:bg-orange-500 hover:text-white hover:border-orange-500 flex items-center justify-center transition-all shadow-sm">
                                                <i class="bi bi-pencil-fill text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-10 py-24 text-center">
                                    <div class="flex flex-col items-center gap-4 text-gray-300">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                                            <i class="bi bi-search text-3xl opacity-50"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-lg text-gray-400">Nenhum membro encontrado</p>
                                            <p class="text-sm">Tente ajustar os filtros da sua pesquisa.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            @forelse($members as $member)
                <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-300 relative overflow-hidden">
                    <!-- Status Indicator -->
                    <div class="absolute top-6 right-6">
                         @if($member->is_active)
                            <span class="inline-flex w-3 h-3 rounded-full bg-green-500 shadow-lg shadow-green-500/30"></span>
                        @else
                            <span class="inline-flex w-3 h-3 rounded-full bg-red-500 shadow-lg shadow-red-500/30"></span>
                        @endif
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform duration-500">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0 pt-1">
                            <h4 class="text-lg font-black text-gray-900 leading-tight mb-1 group-hover:text-blue-600 transition-colors truncate">{{ $member->name }}</h4>
                            <div class="flex items-center gap-2">
                                @if($member->role === 'lider_celula')
                                    <span class="text-[10px] font-black uppercase tracking-wider text-purple-600">Líder</span>
                                @else
                                    <span class="text-[10px] font-black uppercase tracking-wider text-blue-600">Membro</span>
                                @endif
                                <span class="text-gray-300 text-[10px]">•</span>
                                <span class="text-[10px] font-mono text-gray-400">#{{ $member->id }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6 flex-1">
                         <!-- ZONA -->
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 text-sm flex-shrink-0">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                             <div class="flex-1 min-w-0">
                                <p class="text-[9px] font-black uppercase text-gray-400 tracking-wider">Zona</p>
                                <p class="text-xs font-bold text-gray-800 truncate">
                                    {{ $member->cell->supervision->zone->name ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-gray-500 px-1">
                            <div class="w-6 flex justify-center text-sm">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <span class="text-xs font-bold truncate">{{ $member->email }}</span>
                        </div>
                         @if($member->phone)
                            <div class="flex items-center gap-3 text-gray-500 px-1">
                                <div class="w-6 flex justify-center text-sm">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <span class="text-xs font-bold">{{ $member->phone }}</span>
                            </div>
                        @endif
                         <div class="flex items-center gap-3 text-gray-500 px-1">
                            <div class="w-6 flex justify-center text-sm">
                                @if($member->cell)
                                    <i class="bi bi-diagram-3"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle-fill text-red-400"></i>
                                @endif
                            </div>
                            <span class="text-xs font-black uppercase text-gray-900 truncate">
                                {{ $member->cell ? $member->cell->name : 'Sem Célula' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto grid {{ $userRole !== 'secretaria' ? 'grid-cols-2' : 'grid-cols-1' }} gap-3 pt-6 border-t border-gray-50">
                        <a href="{{ route('members.show', $member) }}" class="bg-gray-900 text-white text-center py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg hover:shadow-blue-600/20 flex items-center justify-center gap-2">
                            <i class="bi bi-eye-fill"></i> Ver
                        </a>
                        @if($userRole !== 'secretaria')
                            <a href="{{ route('members.edit', $member) }}" class="bg-white border-2 border-gray-100 text-gray-400 text-center py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-orange-500 hover:text-orange-500 transition-all flex items-center justify-center gap-2">
                                 Editar
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white rounded-[2.5rem] border border-dashed border-gray-200 flex flex-col items-center gap-4 text-gray-300">
                    <i class="bi bi-people text-7xl opacity-20"></i>
                    <p class="font-bold text-lg">Nenhum membro encontrado</p>
                </div>
            @endforelse
        </div>

         <!-- Custom Pagination for Grid -->
        @if($members->hasPages())
            <div class="mt-8">
                {{ $members->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection