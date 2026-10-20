@extends('layouts.app')

@section('title', 'Gestão de Utilizadores - Portal Life Church')
@section('page-title', 'Utilizadores')
@section('page-subtitle', 'Gestão de membros e líderes da igreja')

@section('header-actions')
    <div class="md:hidden flex items-center gap-2">
        <a href="#users-filters"
            class="text-zinc-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 p-2.5 rounded-xl transition border border-zinc-200 dark:border-zinc-800">
            <i class="bi bi-funnel text-lg"></i>
        </a>
        <x-button :href="route('users.create')" variant="primary" size="sm" icon="bi bi-person-plus-fill" />
    </div>
@endsection

@section('content')
    <div class="space-y-5" x-data="{
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

        <!-- Page Header -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Controle Administrativo</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Utilizadores</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Gerencie membros, lideranças e atribuição de papéis de acesso</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- View Switcher --}}
                <div class="hidden md:flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-md transition flex items-center gap-1.5 font-bold text-xs">
                        <i class="bi bi-list-ul"></i>
                        <span>Lista</span>
                    </button>
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-md transition flex items-center gap-1.5 font-bold text-xs">
                        <i class="bi bi-grid-fill"></i>
                        <span>Cards</span>
                    </button>
                </div>

                <button type="button" x-show="selectedUsers.length > 0" x-cloak @click="bulkDelete()"
                    class="inline-flex items-center justify-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-3.5 py-1.5 sm:py-2 rounded-lg font-bold text-xs sm:text-sm shadow-sm transition">
                    <i class="bi bi-trash-fill"></i>
                    <span>Deletar (<span x-text="selectedUsers.length"></span>)</span>
                </button>

                <a href="{{ route('users.create') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Novo Utilizador</span>
                </a>
            </div>
        </header>

        <!-- Global Stats Row -->
        <div class="hidden md:grid grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3" x-show="view === 'list'">
            <!-- Total -->
            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500">Total Geral</p>
                    <i class="bi bi-people text-orange-700 text-sm"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight">{{ $totalUsers }}</p>
            </article>

            <!-- Membros -->
            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500">Membros</p>
                    <i class="bi bi-person-check text-emerald-700 text-sm"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight">{{ $totalMembers }}</p>
            </article>

            <!-- Liderança -->
            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500">Liderança</p>
                    <i class="bi bi-person-badge text-indigo-700 text-sm"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-indigo-700 tracking-tight">{{ $totalLeaders }}</p>
            </article>

            <!-- Administração -->
            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500">Administração</p>
                    <i class="bi bi-shield-check text-blue-700 text-sm"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-blue-700 tracking-tight">{{ $totalAdministracao }}</p>
            </article>

            <!-- Ativos -->
            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500">Ativos</p>
                    <i class="bi bi-lightning text-amber-700 text-sm"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-amber-700 tracking-tight">{{ $totalActive }}</p>
            </article>
        </div>

        <!-- Filter & Search Bar -->
        <div id="users-filters" class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
            <form action="{{ route('users.index') }}" method="GET" class="w-full">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-5 space-y-1">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Pesquisar</label>
                        <div class="relative group">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" name="search" id="liveSearch" data-live-search="manual" value="{{ request('search') }}" placeholder="Pesquisar por nome, email ou telefone..." 
                                class="w-full pl-8 pr-8 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                            <div id="searchSpinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-3 space-y-1">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Nível de Acesso</label>
                        <select name="role" data-searchable="false" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 transition-all appearance-none cursor-pointer custom-select">
                            <option value="">Todos os Papéis</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
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

                    <div class="md:col-span-2 space-y-1">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Estado</label>
                        <select name="status" data-searchable="false" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 transition-all appearance-none cursor-pointer custom-select">
                            <option value="">Qualquer Estado</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-3 bg-orange-700 hover:bg-orange-800 text-white rounded-lg font-bold text-xs sm:text-sm shadow-sm transition">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('users.index') }}" 
                                class="px-3 py-2 border border-gray-300 bg-white text-gray-600 rounded-lg flex items-center justify-center hover:bg-gray-50 transition-all">
                                <i class="bi bi-x-lg text-xs"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <p class="text-xs font-black text-gray-700 uppercase tracking-wider">Lista de Utilizadores ({{ $users->total() }})</p>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-500">Página {{ $users->currentPage() }} de {{ $users->lastPage() }}</span>
            </div>

            @if($users->count() > 0)
                <div class="table-responsive-container table-responsive-shadows">
                    <table class="w-full table-compact">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-center">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll()" 
                                        class="w-4 h-4 rounded border-gray-300 text-orange-700 focus:ring-orange-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3 text-left">Perfil & Identificação</th>
                                <th class="px-4 py-3 text-left">Comunicação</th>
                                <th class="px-4 py-3 text-center">Papel / Nível</th>
                                <th class="px-4 py-3 text-center">Célula</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50/70 transition-colors group">
                                    <td class="px-4 py-3 text-center">
                                        @if($user->role !== 'super_admin' && ($user->role !== 'admin' || auth()->user()->isSuperAdmin()))
                                            <input type="checkbox" value="{{ $user->id }}" 
                                                class="user-checkbox w-4 h-4 rounded border-gray-300 text-orange-700 focus:ring-orange-500 cursor-pointer"
                                                :checked="selectedUsers.includes({{ $user->id }})" 
                                                @change="toggleUser({{ $user->id }})">
                                        @else
                                            <input type="checkbox" disabled class="w-4 h-4 rounded border-gray-200 cursor-not-allowed opacity-50">
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-orange-700 text-white flex items-center justify-center font-black text-sm shadow-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-xs sm:text-sm font-black text-gray-900 leading-tight group-hover:text-orange-700 transition-colors">{{ $user->name }}</p>
                                                <p class="text-[10px] font-mono text-gray-400 uppercase tracking-wider mt-0.5">ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-gray-700">{{ $user->email }}</span>
                                            <span class="text-[10px] text-gray-500 font-medium mt-0.5">{{ $user->phone ?? 'Sem Telefone' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @switch($user->role)
                                            @case('admin')
                                                <x-badge variant="danger">Admin</x-badge>
                                                @break
                                            @case('super_admin')
                                                <x-badge variant="danger" class="bg-red-100 border-red-200 text-red-700 dark:bg-red-500/20 dark:border-red-500/30">Super Admin</x-badge>
                                                @break
                                            @case('administracao')
                                                <x-badge variant="info">Administração</x-badge>
                                                @break
                                            @case('pastor_senior')
                                                <x-badge variant="danger">Pastor Senior</x-badge>
                                                @break
                                            @case('pastor')
                                                <x-badge variant="primary">Pastor</x-badge>
                                                @break
                                            @case('pastor_zona')
                                                <x-badge variant="primary" class="bg-indigo-50 border-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:border-indigo-500/20">Pastor Zona</x-badge>
                                                @break
                                            @case('supervisor')
                                                <x-badge variant="warning">Supervisor</x-badge>
                                                @break
                                            @case('lider_celula')
                                                <x-badge variant="primary">Líder</x-badge>
                                                @break
                                            @case('secretaria')
                                                <x-badge variant="success" class="bg-teal-50 border-teal-100 text-teal-600 dark:bg-teal-500/10 dark:border-teal-500/20">Secretária</x-badge>
                                                @break
                                            @case('tesouraria')
                                                <x-badge variant="success">Tesouraria</x-badge>
                                                @break
                                            @case('comissao_obra')
                                                <x-badge variant="warning" class="bg-yellow-50 border-yellow-100 text-yellow-600 dark:bg-yellow-500/10 dark:border-yellow-500/20">Comissão Obra</x-badge>
                                                @break
                                            @case('responsavel_pacote')
                                                <x-badge variant="info" class="bg-cyan-50 border-cyan-100 text-cyan-600 dark:bg-cyan-500/10 dark:border-cyan-500/20">Resp. Pacote</x-badge>
                                                @break
                                            @default
                                                <x-badge variant="secondary">Membro</x-badge>
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($user->cell)
                                            <span class="text-[10px] font-black text-gray-700 bg-gray-100 px-2.5 py-1 rounded-lg border border-gray-200 uppercase tracking-wider">{{ $user->cell->name }}</span>
                                        @else
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Sem Célula</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center">
                                            @if($user->is_active)
                                                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                                            @else
                                                <span class="w-2.5 h-2.5 bg-gray-300 rounded-full"></span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5 text-right">
                                            <a href="{{ route('users.show', $user) }}" title="Detalhes"
                                                class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-orange-700 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" title="Editar"
                                                class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-orange-700 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            @if($user->role !== 'super_admin' && ($user->role !== 'admin' || auth()->user()->isSuperAdmin()))
                                                <form action="{{ route('users.reset-password', $user) }}" method="POST" id="reset-password-{{ $user->id }}" class="inline">
                                                    @csrf
                                                    <button type="button" onclick="confirmAction('Redefinir Senha', 'Redefinir senha de {{ $user->name }} para mudar123?', 'question', 'Sim, redefinir', 'reset-password-{{ $user->id }}')" 
                                                        class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-purple-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Redefinir senha">
                                                        <i class="bi bi-key-fill"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" id="delete-user-{{ $user->id }}" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('delete-user-{{ $user->id }}', 'Deletar {{ $user->name }}?')" 
                                                        class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Excluir">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6">
                    <x-empty-state 
                        title="Nenhum utilizador encontrado" 
                        subtitle="Experimente ajustar ou redefinir os filtros de busca para encontrar o membro desejado." 
                        icon="bi-people"
                        :actionHref="route('users.create')" 
                        actionLabel="Adicionar Utilizador" 
                        actionIcon="bi-person-plus-fill"
                    />
                </div>
            @endif

            @if($users->hasPages())
                <div class="p-4 bg-gray-50/50 border-t border-gray-200">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @if($users->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($users as $user)
                        <div class="bg-white p-4 sm:p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col group hover:border-gray-300 transition-all relative overflow-hidden">
                            <!-- User Role and Status row -->
                            <div class="flex justify-between items-start mb-3">
                                <div class="w-10 h-10 rounded-lg bg-orange-700 text-white flex items-center justify-center font-black text-base shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col items-end gap-1.5 text-right">
                                    @if($user->is_active)
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md text-[9px] font-black uppercase tracking-wider border border-emerald-200 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Ativo
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-md text-[9px] font-black uppercase tracking-wider border border-gray-200">Inativo</span>
                                    @endif
                                    
                                    @switch($user->role)
                                        @case('admin')
                                            <x-badge variant="danger">Admin</x-badge>
                                            @break
                                        @case('super_admin')
                                            <x-badge variant="danger" class="bg-red-100 border-red-200 text-red-700 dark:bg-red-500/20 dark:border-red-500/30">Super Admin</x-badge>
                                            @break
                                        @case('administracao')
                                            <x-badge variant="info">Administração</x-badge>
                                            @break
                                        @case('pastor_senior')
                                            <x-badge variant="danger">Pastor Senior</x-badge>
                                            @break
                                        @case('pastor')
                                            <x-badge variant="primary">Pastor</x-badge>
                                            @break
                                        @case('pastor_zona')
                                            <x-badge variant="primary" class="bg-indigo-50 border-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:border-indigo-500/20">Pastor Zona</x-badge>
                                            @break
                                        @case('supervisor')
                                            <x-badge variant="warning">Supervisor</x-badge>
                                            @break
                                        @case('lider_celula')
                                            <x-badge variant="primary">Líder</x-badge>
                                            @break
                                        @case('secretaria')
                                            <x-badge variant="success" class="bg-teal-50 border-teal-100 text-teal-600 dark:bg-teal-500/10 dark:border-teal-500/20">Secretária</x-badge>
                                            @break
                                        @case('tesouraria')
                                            <x-badge variant="success">Tesouraria</x-badge>
                                            @break
                                        @case('comissao_obra')
                                            <x-badge variant="warning" class="bg-yellow-50 border-yellow-100 text-yellow-600 dark:bg-yellow-500/10 dark:border-yellow-500/20">Comissão Obra</x-badge>
                                            @break
                                        @case('responsavel_pacote')
                                            <x-badge variant="info" class="bg-cyan-50 border-cyan-100 text-cyan-600 dark:bg-cyan-500/10 dark:border-cyan-500/20">Resp. Pacote</x-badge>
                                            @break
                                        @default
                                            <x-badge variant="secondary">Membro</x-badge>
                                    @endswitch
                                </div>
                            </div>

                            <!-- Name Info -->
                            <div class="mb-4">
                                <h4 class="text-base font-black text-zinc-900 dark:text-zinc-100 leading-tight mb-1 group-hover:text-orange-500 transition-colors line-clamp-1">{{ $user->name }}</h4>
                                <p class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono uppercase tracking-tighter">ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>

                            <!-- Contact details -->
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800/40 flex items-center justify-center text-sm shrink-0">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <span class="text-xs font-semibold truncate">{{ $user->email }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800/40 flex items-center justify-center text-sm shrink-0">
                                        <i class="bi bi-telephone"></i>
                                    </div>
                                    <span class="text-xs font-semibold">{{ $user->phone ?? 'Sem telefone' }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-zinc-500 dark:text-zinc-400">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-800/40 flex items-center justify-center text-sm shrink-0">
                                        <i class="bi bi-diagram-3"></i>
                                    </div>
                                    <span class="text-xs font-black uppercase text-zinc-700 dark:text-zinc-300">
                                        {{ $user->cell ? $user->cell->name : 'Sem Célula' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons row -->
                            <div class="mt-auto flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-zinc-800/30">
                                <a href="{{ route('users.show', $user) }}" class="flex-1 h-10 bg-zinc-900 dark:bg-zinc-800 hover:bg-orange-500 dark:hover:bg-orange-500 text-white text-center rounded-xl font-black text-[10px] uppercase tracking-widest transition duration-200 flex items-center justify-center">
                                    Ver Perfil
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="w-10 h-10 bg-gray-50 dark:bg-zinc-800/50 text-zinc-400 dark:text-zinc-500 hover:bg-orange-500 hover:text-white dark:hover:bg-orange-500 dark:hover:text-white rounded-xl transition duration-200 flex items-center justify-center" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if($user->role !== 'super_admin' && ($user->role !== 'admin' || auth()->user()->isSuperAdmin()))
                                    <form action="{{ route('users.reset-password', $user) }}" method="POST" id="grid-reset-{{ $user->id }}" class="inline">
                                        @csrf
                                        <button type="button" onclick="confirmAction('Redefinir Senha', 'Redefinir senha de {{ $user->name }} para mudar123?', 'question', 'Sim, redefinir', 'grid-reset-{{ $user->id }}')"
                                            class="w-10 h-10 bg-gray-50 dark:bg-zinc-800/50 text-zinc-400 dark:text-zinc-500 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white rounded-xl transition duration-200 flex items-center justify-center" title="Redefinir Senha">
                                            <i class="bi bi-key-fill"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" id="grid-delete-user-{{ $user->id }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('grid-delete-user-{{ $user->id }}', 'Deletar {{ $user->name }}?')" 
                                            class="w-10 h-10 bg-gray-50 dark:bg-zinc-800/50 text-zinc-400 dark:text-zinc-500 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white rounded-xl transition duration-200 flex items-center justify-center" title="Excluir">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state 
                    title="Nenhum utilizador encontrado" 
                    subtitle="Experimente ajustar ou redefinir os filtros de busca para encontrar o membro desejado." 
                    icon="bi-people"
                    :actionHref="route('users.create')" 
                    actionLabel="Adicionar Utilizador" 
                    actionIcon="bi-person-plus-fill"
                />
            @endif
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
