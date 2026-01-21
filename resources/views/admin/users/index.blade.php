@extends('layouts.app')

@section('title', 'Gestão de Utilizadores - Portal Life Church')
@section('page-title', 'Utilizadores')
@section('page-subtitle', 'Gestão de membros e líderes da igreja')

@section('content')
    <div class="space-y-8" x-data="{
        view: 'list',
        selectedUsers: [],
        selectAll: false,
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
    }">
        <!-- Global Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Geral</p>
                    <p class="text-3xl font-black text-gray-900">{{ $totalUsers }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros Ativos</p>
                    <p class="text-3xl font-black text-green-600">{{ $totalMembers }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-2xl group-hover:bg-green-600 group-hover:text-white transition-all">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Corpo de Liderança</p>
                    <p class="text-3xl font-black text-purple-600">{{ $totalLeaders }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl group-hover:bg-purple-600 group-hover:text-white transition-all">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-xl transition-all">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Acessos Ativos</p>
                    <p class="text-3xl font-black text-orange-600">{{ $totalActive }}</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl group-hover:bg-orange-600 group-hover:text-white transition-all">
                    <i class="bi bi-lightning-fill"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <form action="{{ route('users.index') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pb-6 border-b border-gray-50">
                    <div class="md:col-span-4 relative">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Pesquisa Global</label>
                        <input type="text" name="search" id="liveSearch" value="{{ request('search') }}" placeholder="Nome, email ou telefone..." 
                            class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm">
                        <i class="bi bi-search absolute left-5 top-11 text-gray-400"></i>
                        <div id="searchSpinner" class="hidden absolute right-5 top-11">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Papel Hierárquico</label>
                        <select name="role" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm appearance-none">
                            <option value="">Qualquer Role</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="pastor_zona" {{ request('role') == 'pastor_zona' ? 'selected' : '' }}>Pastor</option>
                            <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="lider_celula" {{ request('role') == 'lider_celula' ? 'selected' : '' }}>Líder</option>
                            <option value="membro" {{ request('role') == 'membro' ? 'selected' : '' }}>Membro</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Status da Conta</label>
                        <select name="status" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm appearance-none">
                            <option value="">Qualquer Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex items-end gap-3">
                        <!-- View Toggle -->
                        <div class="flex bg-gray-50 p-1 rounded-2xl border border-gray-100 h-14">
                            <button type="button" @click="view = 'list'" 
                                :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                class="px-4 rounded-xl transition-all duration-300">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button type="button" @click="view = 'grid'" 
                                :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                class="px-4 rounded-xl transition-all duration-300">
                                <i class="bi bi-grid-fill"></i>
                            </button>
                        </div>

                        <button type="submit" id="filterBtn" class="flex-1 h-14 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-100 flex items-center justify-center gap-2 hover:bg-blue-700 transition-all">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                        
                        <!-- Bulk Delete Button -->
                        <button type="button" x-show="selectedUsers.length > 0" x-cloak @click="bulkDelete()"
                            class="flex-1 h-14 bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-red-100 flex items-center justify-center gap-2 hover:bg-red-700 transition-all">
                            <i class="bi bi-trash-fill"></i> Deletar (<span x-text="selectedUsers.length"></span>)
                        </button>
                        
                        <a href="{{ route('users.create') }}" class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm">
                            <i class="bi bi-plus-lg text-xl"></i>
                        </a>
                    </div>
                </div>

                @if(request()->hasAny(['search', 'role', 'status', 'cell_id']))
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Filtros Ativos:</span>
                        <div class="flex flex-wrap gap-2">
                            @if(request('search'))
                                <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">"{{ request('search') }}"</span>
                            @endif
                            @if(request('role'))
                                <span class="px-4 py-1.5 bg-purple-50 text-purple-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100">{{ request('role') }}</span>
                            @endif
                            @if(request('status'))
                                <span class="px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100">{{ request('status') }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
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
                                        @case('pastor_zona')
                                            <span class="px-4 py-1.5 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-purple-100 flex items-center justify-center gap-2">Pastor</span>
                                            @break
                                        @case('supervisor')
                                            <span class="px-4 py-1.5 bg-orange-50 text-orange-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-orange-100 flex items-center justify-center gap-2">Supervisor</span>
                                            @break
                                        @case('lider_celula')
                                            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100 flex items-center justify-center gap-2">Líder</span>
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
                                        <a href="{{ route('users.show', $user) }}" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-500 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if($user->role !== 'admin')
                                            <form action="{{ route('users.reset-password', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="button" onclick="confirmAction('Redefinir senha de {{ $user->name }} para mudar123?', 'Redefinir Senha').then(result => { if(result.isConfirmed) this.closest('form').submit(); })" 
                                                    class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-purple-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Redefinir Senha">
                                                    <i class="bi bi-key-fill"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete('Deletar {{ $user->name }}?').then(result => { if(result.isConfirmed) this.closest('form').submit(); })" 
                                                    class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
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
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            @forelse($users as $user)
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300">
                    <div class="flex justify-between items-start mb-6">
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
                                @case('pastor_zona')
                                    <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100">Pastor</span>
                                    @break
                                @case('supervisor')
                                    <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-100">Supervisor</span>
                                    @break
                                @case('lider_celula')
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Líder</span>
                                    @break
                                @default
                                    <span class="px-3 py-1 bg-gray-50 text-gray-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-100">Membro</span>
                            @endswitch
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-xl font-black text-gray-900 leading-tight mb-1 group-hover:text-blue-600 transition-colors">{{ $user->name }}</h4>
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

                    <div class="mt-auto grid grid-cols-3 gap-2">
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