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
    <div class="space-y-6" x-data="{
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

        <!-- Breadcrumbs & Title block -->
        <div class="space-y-1">
            <x-breadcrumbs :links="[
                'Administração' => null,
                'Utilizadores' => null
            ]" />
            
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl shadow-sm border border-gray-200 dark:border-zinc-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-[10px] font-black text-orange-500 uppercase tracking-[0.2em] mb-1">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20 flex items-center justify-center">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <span>Controle Administrativo</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-zinc-100 tracking-tight">Utilizadores</h1>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium leading-relaxed">Gerencie membros, lideranças e atribuição de papéis de acesso</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
                    {{-- View Switcher --}}
                    <div class="hidden md:flex bg-gray-100 dark:bg-zinc-800/60 p-1 rounded-xl border border-gray-200 dark:border-zinc-800">
                        <button @click="view = 'list'" 
                            :class="view === 'list' ? 'bg-white dark:bg-zinc-900 text-orange-500 shadow-sm' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300'"
                            class="px-3 py-1.5 rounded-lg transition duration-300 flex items-center gap-2 font-bold text-xs">
                            <i class="bi bi-list-ul text-sm"></i>
                            <span>Lista</span>
                        </button>
                        <button @click="view = 'grid'" 
                            :class="view === 'grid' ? 'bg-white dark:bg-zinc-900 text-orange-500 shadow-sm' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-300'"
                            class="px-3 py-1.5 rounded-lg transition duration-300 flex items-center gap-2 font-bold text-xs">
                            <i class="bi bi-grid-fill text-sm"></i>
                            <span>Cards</span>
                        </button>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="button" x-show="selectedUsers.length > 0" x-cloak @click="bulkDelete()"
                            class="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-2xl font-bold text-xs uppercase tracking-wider transition duration-200">
                            <i class="bi bi-trash-fill text-sm"></i>
                            Deletar (<span x-text="selectedUsers.length"></span>)
                        </button>

                        <x-button :href="route('users.create')" variant="primary" size="md" icon="bi bi-person-plus-fill" class="flex-1 md:flex-initial">
                            Novo Utilizador
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Stats Row -->
        <div class="hidden md:grid grid-cols-2 lg:grid-cols-5 gap-4" x-show="view === 'list'">
            <!-- Total -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-3xl shadow-sm border border-gray-200 dark:border-zinc-800 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Total Geral</p>
                    <p class="text-3xl font-black text-zinc-900 dark:text-zinc-100">{{ $totalUsers }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center text-lg shadow-sm border border-orange-100/50 dark:border-orange-500/10">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>

            <!-- Membros -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-3xl shadow-sm border border-gray-200 dark:border-zinc-800 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Membros</p>
                    <p class="text-3xl font-black text-emerald-600 dark:text-emerald-500">{{ $totalMembers }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-lg shadow-sm border border-emerald-100/50 dark:border-emerald-500/10">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>

            <!-- Liderança -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-3xl shadow-sm border border-gray-200 dark:border-zinc-800 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Liderança</p>
                    <p class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $totalLeaders }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-lg shadow-sm border border-indigo-100/50 dark:border-indigo-500/10">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>

            <!-- Administração -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-3xl shadow-sm border border-gray-200 dark:border-zinc-800 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Administração</p>
                    <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $totalAdministracao }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-500 flex items-center justify-center text-lg shadow-sm border border-blue-100/50 dark:border-blue-500/10">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>

            <!-- Ativos -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-3xl shadow-sm border border-gray-200 dark:border-zinc-800 flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Ativos</p>
                    <p class="text-3xl font-black text-orange-600 dark:text-orange-400">{{ $totalActive }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center text-lg shadow-sm border border-orange-100/50 dark:border-orange-500/10">
                    <i class="bi bi-lightning-fill"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div id="users-filters">
            <x-card title="Filtros de Pesquisa" subtitle="Refine a lista por palavra-chave, papel no sistema ou estado da conta">
                <form action="{{ route('users.index') }}" method="GET" class="w-full">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-5 space-y-2">
                            <label class="block text-xs font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">Pesquisar</label>
                            <div class="relative group">
                                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-orange-500 transition-colors"></i>
                                <input type="text" name="search" id="liveSearch" data-live-search="manual" value="{{ request('search') }}" placeholder="Pesquisar por nome, email ou telefone..." 
                                    class="w-full pl-11 pr-10 py-3 bg-gray-50/50 dark:bg-zinc-900/20 border border-gray-200 dark:border-zinc-800 rounded-2xl font-bold text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-orange-500 dark:focus:border-orange-500/50 transition-all">
                                <div id="searchSpinner" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                                    <svg class="animate-spin h-5 w-5 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-3 space-y-2">
                            <label class="block text-xs font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">Nível de Acesso</label>
                            <div class="relative">
                                <select name="role" data-searchable="false" class="w-full px-4 py-3 bg-gray-50/50 dark:bg-zinc-900/20 border border-gray-200 dark:border-zinc-800 rounded-2xl font-bold text-sm text-zinc-900 dark:text-zinc-100 transition-all appearance-none cursor-pointer custom-select">
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
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-xs font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">Estado</label>
                            <div class="relative">
                                <select name="status" data-searchable="false" class="w-full px-4 py-3 bg-gray-50/50 dark:bg-zinc-900/20 border border-gray-200 dark:border-zinc-800 rounded-2xl font-bold text-sm text-zinc-900 dark:text-zinc-100 transition-all appearance-none cursor-pointer custom-select">
                                    <option value="">Qualquer Estado</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                                </select>
                            </div>
                        </div>

                        <div class="md:col-span-2 flex gap-2">
                            <x-button type="submit" variant="secondary" size="md" icon="bi bi-funnel" class="flex-1 py-3 text-xs uppercase tracking-widest">
                                Filtrar
                            </x-button>
                            @if(request()->hasAny(['search', 'role', 'status']))
                                <a href="{{ route('users.index') }}" 
                                    class="px-4 py-3 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-2xl flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-500/20 transition-all border border-red-100 dark:border-red-500/10">
                                    <i class="bi bi-x-lg text-sm"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-zinc-900 rounded-[2.5rem] shadow-sm border border-gray-200 dark:border-zinc-800 overflow-hidden">
            
            <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Lista de Utilizadores</p>
                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $users->total() }} registos encontrados</p>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Página {{ $users->currentPage() }} de {{ $users->lastPage() }}</span>
            </div>

            @if($users->count() > 0)
                <div class="table-responsive-container table-responsive-shadows">
                    <table class="w-full table-compact">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-zinc-900/50 border-b border-gray-100 dark:border-zinc-800">
                                <th class="px-6 py-5 text-center">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll()" 
                                        class="w-5 h-5 rounded border-gray-300 dark:border-zinc-800 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Perfil & Identificação</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Comunicação</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Papel / Nível</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Célula</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Estado</th>
                                <th class="px-8 py-5 text-right text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-zinc-800/40">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-zinc-800/20 transition-colors group">
                                    <td class="px-6 py-5 text-center">
                                        @if($user->role !== 'super_admin' && ($user->role !== 'admin' || auth()->user()->isSuperAdmin()))
                                            <input type="checkbox" value="{{ $user->id }}" 
                                                class="user-checkbox w-5 h-5 rounded border-gray-300 dark:border-zinc-700 text-orange-500 focus:ring-orange-500 cursor-pointer"
                                                :checked="selectedUsers.includes({{ $user->id }})" 
                                                @change="toggleUser({{ $user->id }})">
                                        @else
                                            <input type="checkbox" disabled class="w-5 h-5 rounded border-gray-200 dark:border-zinc-800 cursor-not-allowed opacity-50">
                                        @endif
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center font-black text-lg shadow-md shadow-orange-500/10 group-hover:scale-110 transition-transform">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-zinc-900 dark:text-zinc-200 leading-tight group-hover:text-orange-500 transition-colors">{{ $user->name }}</p>
                                                <p class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mt-0.5">ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">{{ $user->email }}</span>
                                            <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-medium mt-0.5">{{ $user->phone ?? 'Sem Telefone' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
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
                                    <td class="px-8 py-5 text-center">
                                        @if($user->cell)
                                            <span class="text-[10px] font-black text-zinc-700 dark:text-zinc-300 bg-gray-50 dark:bg-zinc-800 px-3 py-1.5 rounded-xl border border-gray-100 dark:border-zinc-800 uppercase tracking-widest">{{ $user->cell->name }}</span>
                                        @else
                                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-600 uppercase">Sem Célula</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="flex justify-center">
                                            @if($user->is_active)
                                                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                            @else
                                                <span class="w-2.5 h-2.5 bg-zinc-300 dark:bg-zinc-700 rounded-full"></span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2 text-right">
                                            <a href="{{ route('users.show', $user) }}" title="Detalhes"
                                                class="w-9 h-9 rounded-xl bg-gray-50 dark:bg-zinc-800/50 text-zinc-400 dark:text-zinc-500 hover:bg-orange-500 hover:text-white dark:hover:bg-orange-500 dark:hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" title="Editar"
                                                class="w-9 h-9 rounded-xl bg-gray-50 dark:bg-zinc-800/50 text-zinc-400 dark:text-zinc-500 hover:bg-orange-500 hover:text-white dark:hover:bg-orange-500 dark:hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            @if($user->role !== 'super_admin' && ($user->role !== 'admin' || auth()->user()->isSuperAdmin()))
                                                <form action="{{ route('users.reset-password', $user) }}" method="POST" id="reset-password-{{ $user->id }}" class="inline">
                                                    @csrf
                                                    <button type="button" onclick="confirmAction('Redefinir Senha', 'Redefinir senha de {{ $user->name }} para mudar123?', 'question', 'Sim, redefinir', 'reset-password-{{ $user->id }}')" 
                                                        class="w-9 h-9 rounded-xl bg-gray-50 dark:bg-zinc-800/50 text-zinc-400 dark:text-zinc-500 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white flex items-center justify-center transition-all shadow-sm" title="Redefinir senha">
                                                        <i class="bi bi-key-fill"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" id="delete-user-{{ $user->id }}" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('delete-user-{{ $user->id }}', 'Deletar {{ $user->name }}?')" 
                                                        class="w-9 h-9 rounded-xl bg-gray-50 dark:bg-zinc-800/50 text-zinc-400 dark:text-zinc-500 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white flex items-center justify-center transition-all shadow-sm" title="Excluir">
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
                <div class="p-8">
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
                <div class="p-5 bg-gray-50/50 dark:bg-zinc-900/50 border-t border-gray-100 dark:border-zinc-800">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @if($users->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($users as $user)
                        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl shadow-sm border border-gray-200 dark:border-zinc-800 flex flex-col group hover:shadow-md transition-all duration-300 relative overflow-hidden">
                            <!-- User Role and Status row -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center font-black text-xl group-hover:scale-110 transition-all duration-500 shadow-md shadow-orange-500/10">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col items-end gap-2 text-right">
                                    @if($user->is_active)
                                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100/50 dark:border-emerald-500/10 flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Ativo
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-zinc-50 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 rounded-lg text-[9px] font-black uppercase tracking-widest border border-gray-100 dark:border-zinc-700/50">Inativo</span>
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
