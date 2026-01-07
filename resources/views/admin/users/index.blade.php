@extends('layouts.app')

@section('title', 'Gestão de Utilizadores - Portal Life Church')
@section('page-title', 'Utilizadores')
@section('page-subtitle', 'Gestão de membros e líderes da igreja')

@section('content')
    <div class="space-y-8">
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
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, email ou telefone..." 
                            class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 font-bold text-sm">
                        <i class="bi bi-search absolute left-5 top-11 text-gray-400"></i>
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
                        <button type="submit" class="flex-1 py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-100 flex items-center justify-center gap-2">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                        <a href="{{ route('users.index') }}" class="w-14 h-14 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center hover:bg-gray-100 transition-all">
                            <i class="bi bi-x-lg"></i>
                        </a>
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

        <!-- Users Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
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
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.show', $user) }}" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-500 hover:text-white flex items-center justify-center transition-all">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if($user->role !== 'admin')
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Deletar?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-10 py-20 text-center">
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
    </div>
@endsection