@extends('layouts.app')

@section('title', 'Gestão de Utilizadores - Projeto Edificar')
@section('page-title', 'Utilizadores')
@section('page-subtitle', 'Gestão de membros e líderes da igreja')

@section('content')
<div class="mb-6">
    <!-- Barra de Filtros e Pesquisa -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form action="{{ route('users.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Pesquisa -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Nome, email ou telefone..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Filtro por Papel -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Papel</label>
                    <select name="role" id="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos os Papéis</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pastor_zona" {{ request('role') == 'pastor_zona' ? 'selected' : '' }}>Pastor de Zona</option>
                        <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="lider_celula" {{ request('role') == 'lider_celula' ? 'selected' : '' }}>Líder de Célula</option>
                        <option value="membro" {{ request('role') == 'membro' ? 'selected' : '' }}>Membro</option>
                    </select>
                </div>

                <!-- Filtro por Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos os Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
            </div>

            <!-- Filtro por Célula -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label for="cell_id" class="block text-sm font-medium text-gray-700 mb-1">Célula</label>
                    <select name="cell_id" id="cell_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas as Células</option>
                        @foreach($cells as $cell)
                        <option value="{{ $cell->id }}" {{ request('cell_id') == $cell->id ? 'selected' : '' }}>
                            {{ $cell->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botões de Ação -->
                <div class="md:col-span-2 flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="bi bi-funnel mr-2"></i>Filtrar
                    </button>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        <i class="bi bi-x-circle mr-2"></i>Limpar
                    </a>
                    <a href="{{ route('users.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium whitespace-nowrap">
                        <i class="bi bi-plus-circle mr-2"></i>Novo
                    </a>
                </div>
            </div>

            @if(request()->hasAny(['search', 'role', 'status', 'cell_id']))
            <div class="flex items-center space-x-2 text-sm">
                <span class="text-gray-600">Filtros ativos:</span>
                @if(request('search'))
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                    <i class="bi bi-search mr-1"></i>
                    "{{ request('search') }}"
                </span>
                @endif
                @if(request('role'))
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800">
                    <i class="bi bi-person-badge mr-1"></i>
                    {{ ucfirst(str_replace('_', ' ', request('role'))) }}
                </span>
                @endif
                @if(request('status'))
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">
                    <i class="bi bi-check-circle mr-1"></i>
                    {{ request('status') == 'active' ? 'Ativos' : 'Inativos' }}
                </span>
                @endif
                @if(request('cell_id'))
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-orange-100 text-orange-800">
                    <i class="bi bi-geo-alt mr-1"></i>
                    {{ $cells->find(request('cell_id'))->name ?? 'Célula' }}
                </span>
                @endif
            </div>
            @endif
        </form>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Utilizadores</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalUsers }}</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <i class="bi bi-people-fill text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Membros</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalMembers }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <i class="bi bi-person-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Líderes</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $totalLeaders }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg">
                    <i class="bi bi-star-fill text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Ativos</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $totalActive }}</p>
                </div>
                <div class="bg-orange-100 p-4 rounded-lg">
                    <i class="bi bi-check-circle-fill text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Utilizadores -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Papel</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Célula</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mr-3 text-white font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">ID: #{{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">
                            {{ $user->phone ?? '-' }}
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        @switch($user->role)
                            @case('admin')
                                <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    <i class="bi bi-shield-check mr-1"></i>Admin
                                </span>
                                @break
                            @case('pastor_zona')
                                <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                    <i class="bi bi-person-fill-gear mr-1"></i>Pastor
                                </span>
                                @break
                            @case('supervisor')
                                <span class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                    <i class="bi bi-diagram-3 mr-1"></i>Supervisor
                                </span>
                                @break
                            @case('lider_celula')
                                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    <i class="bi bi-people-fill mr-1"></i>Líder
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                    <i class="bi bi-person-circle mr-1"></i>Membro
                                </span>
                        @endswitch
                    </td>
                    <td class="px-6 py-4">
                        @if($user->cell)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="bi bi-geo-alt-fill mr-1"></i>
                                {{ $user->cell->name }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">Sem célula</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                                Inativo
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('users.show', $user) }}" 
                                class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-xs font-medium" 
                                title="Ver Detalhes">
                                <i class="bi bi-eye mr-1"></i>Ver
                            </a>
                            <a href="{{ route('users.edit', $user) }}" 
                                class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-xs font-medium" 
                                title="Editar">
                                <i class="bi bi-pencil mr-1"></i>Editar
                            </a>
                            @if($user->role !== 'admin')
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" 
                                onsubmit="return confirm('Tem certeza que deseja deletar este utilizador? Esta ação não pode ser desfeita.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-xs font-medium" title="Deletar">
                                    <i class="bi bi-trash mr-1"></i>Deletar
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="bg-gray-100 p-6 rounded-full mb-4">
                                <i class="bi bi-inbox text-5xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-600 font-medium text-lg mb-2">Nenhum utilizador encontrado</p>
                            <p class="text-gray-500 text-sm mb-4">Tente ajustar os filtros ou criar um novo utilizador</p>
                            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                <i class="bi bi-plus-circle mr-2"></i>
                                Criar Novo Utilizador
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Mostrando <span class="font-medium">{{ $users->firstItem() }}</span> 
                a <span class="font-medium">{{ $users->lastItem() }}</span> 
                de <span class="font-medium">{{ $users->total() }}</span> resultados
            </div>
            <div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection