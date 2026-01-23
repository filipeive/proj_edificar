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
    <a href="{{ route('members.create') }}" 
        class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition-all flex items-center justify-center shadow-lg shadow-green-600/20">
        <i class="bi bi-person-plus-fill text-xl"></i>
    </a>
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
        <div class="bg-white p-4 md:p-8 rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
                <!-- Search & Filters -->
                <div class="flex-1 w-full xl:max-w-4xl">
                    <form method="GET" action="{{ route('members.index') }}" class="flex flex-col md:flex-row gap-3">
                        <div class="flex-1 relative">
                            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Buscar por nome..."
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 font-medium text-sm">
                        </div>

                        @if($userRole !== 'lider_celula' && $availableCells->count() > 1)
                            <select name="cell_id" 
                                class="px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-xs uppercase tracking-widest text-gray-500">
                                <option value="">Todas as Células</option>
                                @foreach($availableCells as $cell)
                                    <option value="{{ $cell->id }}" {{ request('cell_id') == $cell->id ? 'selected' : '' }}>
                                        {{ $cell->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        <div class="flex gap-2">
                            <button type="submit" class="px-8 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex-1 md:flex-none">
                                Filtrar
                            </button>
                            @if(request('search') || request('cell_id'))
                                <a href="{{ route('members.index') }}" class="px-5 py-4 bg-gray-100 text-gray-500 rounded-2xl hover:bg-gray-200 transition-all flex items-center justify-center">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- View Toggles & Actions -->
                <div class="flex items-center gap-3 w-full xl:w-auto">
                    <div class="hidden md:flex bg-gray-50 p-1 rounded-2xl border border-gray-100">
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

                    <a href="{{ route('members.create') }}" 
                        class="hidden md:flex flex-1 xl:flex-none px-8 py-4 bg-green-600 text-white rounded-2xl hover:bg-green-700 transition-all font-black text-xs uppercase tracking-widest items-center justify-center gap-3 shadow-lg shadow-green-100">
                        <i class="bi bi-person-plus-fill text-lg"></i>
                        <span>@if ($userRole === 'lider_celula') Novo Membro @else Novo Membro / Líder @endif</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Global Stats Grid -->
        <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center text-center group hover:bg-blue-50 transition-colors">
                <p class="text-5xl font-black text-blue-600 tracking-tighter">{{ $members->total() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2 group-hover:text-blue-400">Total de Membros</p>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center text-center group hover:bg-green-50 transition-colors">
                <p class="text-5xl font-black text-green-600 tracking-tighter">{{ $members->where('is_active', true)->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2 group-hover:text-green-400">Membros Ativos</p>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center text-center group hover:bg-purple-50 transition-colors">
                <p class="text-5xl font-black text-purple-600 tracking-tighter">{{ $availableCells->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2 group-hover:text-purple-400">Unidades de Células</p>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center text-center group hover:bg-orange-50 transition-colors">
                <p class="text-5xl font-black text-orange-600 tracking-tighter">{{ $members->whereNotNull('commitments')->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2 group-hover:text-orange-400">Com Compromisso</p>
            </div>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Identificação</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Cargo</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Contacto / Email</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Estrutura</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($members as $member)
                            <tr class="hover:bg-gray-50/70 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xl">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">{{ $member->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-mono uppercase tracking-tighter">ID: {{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    @if($member->role === 'lider_celula')
                                        <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100">Líder</span>
                                    @else
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Membro</span>
                                    @endif
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700 leading-tight">{{ $member->email }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium">{{ $member->phone ?? 'Sem contacto' }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    @if($member->cell)
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-900 uppercase">Célula: {{ $member->cell->name }}</span>
                                            <span class="text-[10px] text-gray-400 font-medium">Sup: {{ $member->cell->supervision->name ?? '-' }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-red-400 uppercase">Sem Alocação</span>
                                    @endif
                                </td>
                                <td class="px-10 py-6 text-center">
                                    @if($member->is_active)
                                        <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">Ativo</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 text-right">
                                        <a href="{{ route('members.show', $member) }}" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('members.edit', $member) }}" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-500 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4 text-gray-300">
                                        <i class="bi bi-people text-7xl"></i>
                                        <p class="font-bold text-lg">Nenhum membro encontrado</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @if($members->hasPages())
                <div class="p-8 bg-gray-50/50 border-t border-gray-50">
                    {{ $members->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            @forelse($members as $member)
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            @if($member->is_active)
                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">Ativo</span>
                            @else
                                <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest">Inativo</span>
                            @endif
                            
                            @if($member->role === 'lider_celula')
                                <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100">Líder</span>
                            @else
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Membro</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-xl font-black text-gray-900 leading-tight mb-1 group-hover:text-blue-600 transition-colors">{{ $member->name }}</h4>
                        <p class="text-[10px] text-gray-400 font-mono uppercase tracking-tighter">ID: {{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <span class="text-sm font-bold truncate">{{ $member->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <span class="text-sm font-bold">{{ $member->phone ?? 'Sem contacto' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm font-bold">
                                @if($member->cell)
                                    <i class="bi bi-diagram-3"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle-fill text-red-400"></i>
                                @endif
                            </div>
                            <span class="text-xs font-black uppercase text-gray-900">
                                {{ $member->cell ? $member->cell->name : 'Sem Célula' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto grid grid-cols-2 gap-3">
                        <a href="{{ route('members.show', $member) }}" class="flex-1 bg-gray-900 text-white text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg hover:shadow-blue-200 active:scale-95">
                            Ver
                        </a>
                        <a href="{{ route('members.edit', $member) }}" class="flex-1 bg-gray-50 text-gray-400 text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all active:scale-95">
                            Editar
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white rounded-[2.5rem] border border-dashed border-gray-200 flex flex-col items-center gap-4 text-gray-300">
                    <i class="bi bi-people text-7xl"></i>
                    <p class="font-bold text-lg">Nenhum membro encontrado</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection