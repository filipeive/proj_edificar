@extends('layouts.app')

@section('title', 'Gestão de Utilizadores - Projeto Edificar')
@section('page-title', 'Utilizadores')
@section('page-subtitle', 'Gestão de membros e líderes da igreja')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    <!-- Filtros -->
    <div class="w-full md:w-auto">
        <form action="{{ route('users.index') }}" method="GET" class="flex items-center gap-4 bg-white p-2 rounded-lg shadow">
            <select name="role" id="role" class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">-- Todas as Funções --</option>
                <option value="admin" @selected(request('role') == 'admin')>Admin</option>
                <option value="pastor_zona" @selected(request('role') == 'pastor_zona')>Pastor de Zona</option>
                <option value="supervisor" @selected(request('role') == 'supervisor')>Supervisor</option>
                <option value="lider_celula" @selected(request('role') == 'lider_celula')>Líder de Célula</option>
                <option value="membro" @selected(request('role') == 'membro')>Membro</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                <i class="bi bi-funnel mr-1"></i> Filtrar
            </button>
            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-blue-600 text-sm" title="Limpar filtros">
                <i class="bi bi-x-circle"></i>
            </a>
        </form>
    </div>

    <!-- Ações -->
    <div class="flex items-center gap-4">
        <p class="text-sm text-gray-600">Total: <strong>{{ $users->total() }}</strong></p>
        <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
            <i class="bi bi-plus-circle mr-2"></i>Novo Utilizador
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Papel</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Célula</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="bi bi-person text-blue-600"></i>
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
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            @switch($user->role)
                                @case('admin')
                                    <i class="bi bi-shield-check mr-1"></i>Admin
                                    @break
                                @case('pastor_zona')
                                    <i class="bi bi-person-fill mr-1"></i>Pastor
                                    @break
                                @case('supervisor')
                                    <i class="bi bi-diagram-3 mr-1"></i>Supervisor
                                    @break
                                @case('lider_celula')
                                    <i class="bi bi-people-fill mr-1"></i>Líder
                                    @break
                                @default
                                    <i class="bi bi-person-circle mr-1"></i>Membro
                            @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600">
                            @if($user->cell)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $user->cell->name }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="bi bi-check-circle mr-1"></i>Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="bi bi-x-circle mr-1"></i>Inativo
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('users.show', $user) }}" 
                                class="text-green-600 hover:text-green-800 font-medium text-sm" 
                                title="Ver Detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('users.edit', $user) }}" 
                                class="text-blue-600 hover:text-blue-800 font-medium text-sm" 
                                title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->role !== 'admin')
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" 
                                onsubmit="return confirm('Tem certeza que deseja deletar este utilizador?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm" title="Deletar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <i class="bi bi-inbox text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 font-medium">Nenhum utilizador encontrado</p>
                            <a href="{{ route('users.create') }}" class="mt-4 text-blue-600 hover:text-blue-800 text-sm">
                                Criar novo utilizador →
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="p-6 border-t border-gray-200">
        {{ $users->withQueryString()->links() }}
    </div>
</div>

<!-- Resumo de Estatísticas -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Utilizadores</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $users->count() }}</p>
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
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $users->where('role', 'membro')->count() }}</p>
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
                <p class="text-3xl font-bold text-purple-600 mt-2">{{ $users->where('role', 'lider_celula')->count() }}</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <i class="bi bi-people-fill text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Ativos</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">{{ $users->where('is_active', true)->count() }}</p>
            </div>
            <div class="bg-orange-100 p-4 rounded-lg">
                <i class="bi bi-check-circle text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>
@endsection