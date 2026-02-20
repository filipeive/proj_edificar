@extends('layouts.app')

@section('title', 'Gestão de Utilizadores - Portal Life Church')
@section('page-title', 'Utilizadores')
@section('page-subtitle', 'Gestão de membros e líderes da igreja')

@section('header-actions')
    <div class="md:hidden">
        <a href="{{ route('users.create') }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100">
            <i class="bi bi-person-plus-fill text-2xl"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8" x-data="{
        view: window.innerWidth < 768 ? 'grid' : 'list',
        selectedUsers: [],
        selectAll: false,
        updateView() {
            if (window.innerWidth < 768 && this.view === 'list') {
                this.view = 'grid';
            }
        },
        toggleAll() {
            if (this.selectAll) {
                this.selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:not(:disabled)')).map(cb => parseInt(cb.value));
            } else {
                this.selectedUsers = [];
            }
        },
        toggleUser(userId) {
            const index = this.selectedUsers.indexOf(userId);
            if (index > -1) {
                this.selectedUsers.splice(index, 1);
            } else {
                this.selectedUsers.push(userId);
            }
            this.selectAll = this.selectedUsers.length === document.querySelectorAll('.user-checkbox:not(:disabled)').length;
        },
        async bulkDelete() {
            const result = await confirmDelete(
                `Deseja deletar ${this.selectedUsers.length} utilizador(es) selecionado(s)?`,
                'Exclusão em Massa'
            );
            if (result.isConfirmed) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }
    }"
    x-init="$watch('view', value => localStorage.setItem('users_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('users_view') || 'list')"
    @resize.window.debounce.500ms="updateView()">

        <!-- Header -->
        <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 transition-all">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span>Administração</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Utilizadores</h1>
                <p class="text-gray-500 font-medium">Gestão de membros, líderes e permissões de acesso</p>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                {{-- View Switcher --}}
                <div class="hidden md:flex bg-gray-100/50 p-1.5 rounded-2xl border border-gray-100">
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-list-ul text-sm"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Lista</span>
                    </button>
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-grid-fill text-sm"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Cards</span>
                    </button>
                </div>

                <div class="hidden md:flex gap-3">
                    <button type="button" x-show="selectedUsers.length > 0" x-cloak @click="bulkDelete()"
                        class="group flex items-center bg-red-600 text-white px-8 py-4 rounded-2xl hover:bg-red-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-red-200">
                        <i class="bi bi-trash-fill text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                        Deletar (<span x-text="selectedUsers.length"></span>)
                    </button>

                    <a href="{{ route('users.create') }}" class="group flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200">
                        <i class="bi bi-person-plus-fill text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                        Novo Utilizador
                    </a>
                </div>
            </div>
        </div>

        <!-- Global Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 hidden md:flex" x-show="view === 'list'">
            <div class="flex bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Geral</p>
                    <p class="text-3xl font-black text-gray-900">{{ $totalUsers }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="flex bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros</p>
                    <p class="text-3xl font-black text-green-600">{{ $totalMembers }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-2xl group-hover:bg-green-600 group-hover:text-white transition-all">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
            <div class="flex bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Liderança</p>
                    <p class="text-3xl font-black text-purple-600">{{ $totalLeaders }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:bg-purple-600 group-hover:text-white transition-all">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
            <div class="flex bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Administração</p>
                    <p class="text-3xl font-black text-blue-700">{{ $totalAdministracao }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
            <div class="flex bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ativos</p>
                    <p class="text-3xl font-black text-orange-600">{{ $totalActive }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl group-hover:bg-orange-600 group-hover:text-white transition-all">
                    <i class="bi bi-lightning-fill"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-gray-50/50 p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <form action="{{ route('users.index') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-5 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pesquisar</label>
                        <div class="relative group">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input type="text" name="search" id="liveSearch" data-live-search="manual" value="{{ request('search') }}" placeholder="Pesquisar por nome, email ou telefone..." 
                                class="w-full pl-12 pr-6 py-3 bg-white border-transparent focus:ring-4 focus:ring-blue-100 rounded-xl font-bold text-sm transition-all">
                            <div id="searchSpinner" class="hidden absolute right-5 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-3 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nível</label>
                        <select name="role" class="w-full px-6 py-3 bg-white border-transparent focus:ring-4 focus:ring-blue-100 rounded-xl font-bold text-sm appearance-none transition-all custom-select">
                            <option value="">Todos os Papéis</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="administracao" {{ request('role') == 'administracao' ? 'selected' : '' }}>Administração</option>
                            <option value="pastor_senior" {{ request('role') == 'pastor_senior' ? 'selected' : '' }}>Pastor Senior</option>
                            <option value="pastor" {{ request('role') == 'pastor' ? 'selected' : '' }}>Pastor</option>
                            <option value="pastor_zona" {{ request('role') == 'pastor_zona' ? 'selected' : '' }}>Pastor de Zona</option>
                            <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="lider_celula" {{ request('role') == 'lider_celula' ? 'selected' : '' }}>Líder de Célula</option>
                            <option value="secretaria" {{ request('role') == 'secretaria' ? 'selected' : '' }}>Secretária</option>
                            <option value="tesouraria" {{ request('role') == 'tesouraria' ? 'selected' : '' }}>Tesouraria</option>
                            <option value="comissao_obra" {{ request('role') == 'comissao_obra' ? 'selected' : '' }}>Comissão de Obra</option>
                            <option value="responsavel_pacote" {{ request('role') == 'responsavel_pacote' ? 'selected' : '' }}>Resp. de Pacote</option>
                            <option value="membro" {{ request('role') == 'membro' ? 'selected' : '' }}>Membro</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                        <select name="status" class="w-full px-6 py-3 bg-white border-transparent focus:ring-4 focus:ring-blue-100 rounded-xl font-bold text-sm appearance-none transition-all custom-select">
                            <option value="">Qualquer Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('users.index') }}" class="px-4 py-3 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-100 transition-all">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>


        <!-- List View -->
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-compact">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-6 text-center">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll()" 
                                    class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Perfil & Identificação</th>
                            <th class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Comunicação</th>
                            <th class="px-10 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Nível de Acesso</th>
                            <th class="px-10 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Célula</th>
                            <th class="px-10 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-10 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Menu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/70 transition-colors group">
                                <td class="px-6 py-6 text-center">
                                    @if($user->role !== 'admin')
                                        <input type="checkbox" value="{{ $user->id }}" 
                                            class="user-checkbox w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                            :checked="selectedUsers.includes({{ $user->id }})" 
                                            @change="toggleUser({{ $user->id }})">
                                    @else
                                        <input type="checkbox" disabled class="w-5 h-5 rounded border-gray-200 cursor-not-allowed opacity-50">
                                    @endif
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-blue-100 group-hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">{{ $user->name }}</p>
                                            <p class="text-[10px] font-mono text-gray-400 uppercase tracking-widest">ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-800">{{ $user->email }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium">{{ $user->phone ?? 'Sem Telefone' }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    @switch($user->role)
                                        @case('admin')
                                            <span class="px-4 py-1.5 bg-red-50 text-red-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-red-100 flex items-center justify-center gap-2">Admin</span>
                                            @break
                                        @case('administracao')
                                            <span class="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-200 flex items-center justify-center gap-2">Administração</span>
                                            @break
                                        @case('pastor_senior')
                                            <span class="px-4 py-1.5 bg-red-50 text-red-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-red-200 flex items-center justify-center gap-2">Pastor Senior</span>
                                            @break
                                        @case('pastor')
                                            <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-indigo-100 flex items-center justify-center gap-2">Pastor</span>
                                            @break
                                        @case('pastor_zona')
                                            <span class="px-4 py-1.5 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-purple-100 flex items-center justify-center gap-2">Pastor Zona</span>
                                            @break
                                        @case('supervisor')
                                            <span class="px-4 py-1.5 bg-orange-50 text-orange-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-orange-100 flex items-center justify-center gap-2">Supervisor</span>
                                            @break
                                        @case('lider_celula')
                                            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100 flex items-center justify-center gap-2">Líder</span>
                                            @break
                                        @case('secretaria')
                                            <span class="px-4 py-1.5 bg-teal-50 text-teal-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-teal-100 flex items-center justify-center gap-2">Secretária</span>
                                            @break
                                        @case('tesouraria')
                                            <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-100 flex items-center justify-center gap-2">Tesouraria</span>
                                            @break
                                        @case('comissao_obra')
                                            <span class="px-4 py-1.5 bg-yellow-50 text-yellow-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-yellow-100 flex items-center justify-center gap-2">Comissão Obra</span>
                                            @break
                                        @case('responsavel_pacote')
                                            <span class="px-4 py-1.5 bg-cyan-50 text-cyan-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-cyan-100 flex items-center justify-center gap-2">Resp. Pacote</span>
                                            @break
                                        @default
                                            <span class="px-4 py-1.5 bg-gray-50 text-gray-500 rounded-lg text-[10px] font-black uppercase tracking-widest border border-gray-100 flex items-center justify-center gap-2">Membro</span>
                                    @endswitch
                                </td>
                                <td class="px-10 py-6 text-center">
                                    @if($user->cell)
                                        <span class="text-[10px] font-black text-gray-900 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 uppercase tracking-widest">{{ $user->cell->name }}</span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-300 uppercase">N/A</span>
                                    @endif
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <div class="flex justify-center">
                                        @if($user->is_active)
                                            <span class="w-3 h-3 bg-green-500 rounded-full shadow-[0_0_10px_rgba(34,197,94,0.5)] animate-pulse"></span>
                                        @else
                                            <span class="w-3 h-3 bg-gray-300 rounded-full"></span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 text-right">
                                        <a href="{{ route('users.show', $user) }}" title="Detalhes"
                                            class="action-icon bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white shadow-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" title="Editar"
                                            class="action-icon bg-gray-50 text-gray-400 hover:bg-orange-500 hover:text-white shadow-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if($user->role !== 'admin')
                                            <form action="{{ route('users.reset-password', $user) }}" method="POST" id="reset-password-{{ $user->id }}" class="inline">
                                                @csrf
                                                <button type="button" onclick="confirmAction('Redefinir Senha', 'Redefinir senha de {{ $user->name }} para mudar123?', 'question', 'Sim, redefinir', 'reset-password-{{ $user->id }}')" 
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-purple-600 hover:text-white shadow-sm" title="Redefinir senha">
                                                    <i class="bi bi-key-fill"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" id="delete-user-{{ $user->id }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete('delete-user-{{ $user->id }}', 'Deletar {{ $user->name }}?')" 
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white shadow-sm" title="Excluir">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4 text-gray-200">
                                        <i class="bi bi-people text-7xl"></i>
                                        <p class="font-black text-xl text-gray-300 tracking-tighter uppercase">Nenhum utilizador encontrado no sistema</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-8 bg-gray-50/50 border-t border-gray-50">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($users as $user)
                <div class="bg-white p-5 md:p-8 rounded-[2rem] md:rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300 compact-card">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-black text-2xl group-hover:scale-110 transition-all duration-500 shadow-lg shadow-blue-100">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="flex flex-col items-end gap-2 text-right">
                            @if($user->is_active)
                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-2">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Ativo
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-50 text-gray-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-100">Inativo</span>
                            @endif
                            
                            @switch($user->role)
                                @case('admin')
                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-100">Admin</span>
                                    @break
                                @case('administracao')
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-200">Administração</span>
                                    @break
                                @case('pastor_senior')
                                    <span class="px-3 py-1 bg-red-50 text-red-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-200">Pastor Senior</span>
                                    @break
                                @case('pastor')
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-indigo-100">Pastor</span>
                                    @break
                                @case('pastor_zona')
                                    <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100">Pastor Zona</span>
                                    @break
                                @case('supervisor')
                                    <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-100">Supervisor</span>
                                    @break
                                @case('lider_celula')
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Líder</span>
                                    @break
                                @case('secretaria')
                                    <span class="px-3 py-1 bg-teal-50 text-teal-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-teal-100">Secretária</span>
                                    @break
                                @case('tesouraria')
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">Tesouraria</span>
                                    @break
                                @case('comissao_obra')
                                    <span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-yellow-100">Comissão Obra</span>
                                    @break
                                @case('responsavel_pacote')
                                    <span class="px-3 py-1 bg-cyan-50 text-cyan-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-cyan-100">Resp. Pacote</span>
                                    @break
                                @default
                                    <span class="px-3 py-1 bg-gray-50 text-gray-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-100">Membro</span>
                            @endswitch
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-base font-black text-gray-900 leading-tight mb-1 group-hover:text-blue-600 transition-colors line-clamp-1">{{ $user->name }}</h4>
                        <p class="text-[10px] text-gray-400 font-mono uppercase tracking-tighter">ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <span class="text-sm font-bold truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <span class="text-sm font-bold">{{ $user->phone ?? 'Sem contacto' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-500">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-sm font-bold">
                                @if($user->cell)
                                    <i class="bi bi-diagram-3"></i>
                                @else
                                    <i class="bi bi-exclamation-triangle-fill text-red-400"></i>
                                @endif
                            </div>
                            <span class="text-xs font-black uppercase text-gray-900">
                                {{ $user->cell ? $user->cell->name : 'Sem Célula' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto grid grid-cols-4 gap-2">
                        <a href="{{ route('users.show', $user) }}" class="bg-gray-900 text-white text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg hover:shadow-blue-200 active:scale-95">
                            Ver
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="bg-gray-50 text-gray-400 text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all active:scale-95">
                            Editar
                        </a>
                        @if($user->role !== 'admin')
                            <form action="{{ route('users.reset-password', $user) }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmAction('Redefinir senha para mudar123?', 'Redefinir Senha').then(result => { if(result.isConfirmed) this.closest('form').submit(); })" 
                                    class="w-full bg-purple-50 text-purple-600 text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-purple-600 hover:text-white transition-all active:scale-95" title="Redefinir Senha">
                                    <i class="bi bi-key-fill"></i>
                                </button>
                            </form>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" id="grid-delete-user-{{ $user->id }}">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('grid-delete-user-{{ $user->id }}', 'Deletar {{ $user->name }}?')" 
                                    class="w-full bg-red-50 text-red-600 text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all active:scale-95">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white rounded-[2.5rem] border border-dashed border-gray-200 flex flex-col items-center gap-4 text-gray-300">
                    <i class="bi bi-people text-7xl"></i>
                    <p class="font-bold text-lg text-center uppercase tracking-tighter font-black">Nenhum utilizador encontrado no sistema</p>
                </div>
            @endforelse
        </div>

        <!-- Hidden Bulk Delete Form -->
        <form id="bulkDeleteForm" action="{{ route('users.bulk-destroy') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
            <template x-for="userId in selectedUsers" :key="userId">
                <input type="hidden" name="user_ids[]" :value="userId">
            </template>
        </form>
    </div>

    <script>
        // Live search with debouncing
        const liveSearchInput = document.getElementById('liveSearch');
        const searchSpinner = document.getElementById('searchSpinner');
        const filterBtn = document.getElementById('filterBtn');
        let searchTimeout;

        if (liveSearchInput) {
            liveSearchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                
                // Show spinner
                searchSpinner.classList.remove('hidden');
                
                // Debounce: wait 500ms after user stops typing
                searchTimeout = setTimeout(() => {
                    // Auto-submit the form
                    this.form.submit();
                }, 500);
            });

            // Also trigger on role/status change
            const roleSelect = document.querySelector('select[name="role"]');
            const statusSelect = document.querySelector('select[name="status"]');
            
            if (roleSelect) {
                roleSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
            
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        }
    </script>
@endsection
