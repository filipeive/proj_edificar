@extends('layouts.app')

@section('title', 'Editar Utilizador - Projeto Edificar')
@section('page-title', 'Editar Utilizador')
@section('page-subtitle', 'Atualizar informações do utilizador')

@section('content')
<div class="grid grid-max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">Utilizadores</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Editar</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="bi bi-pencil-square mr-3"></i>
                    Editar: {{ $user->name }}
                </h2>
                <span class="px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium">
                    ID: #{{ $user->id }}
                </span>
            </div>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telefone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Telefone
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                        placeholder="+258 84 123 4567"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Papel/Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Papel no Sistema <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror"
                        {{ $user->role === 'admin' ? 'disabled' : '' }}>
                        <option value="">Selecione o papel</option>
                        <option value="membro" {{ old('role', $user->role) == 'membro' ? 'selected' : '' }}>Membro</option>
                        <option value="lider_celula" {{ old('role', $user->role) == 'lider_celula' ? 'selected' : '' }}>Líder de Célula</option>
                        <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="pastor_zona" {{ old('role', $user->role) == 'pastor_zona' ? 'selected' : '' }}>Pastor de Zona</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @if($user->role === 'admin')
                    <input type="hidden" name="role" value="admin">
                    <p class="mt-1 text-xs text-gray-500">O papel de administrador não pode ser alterado</p>
                    @endif
                    @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Célula -->
                <div>
                    <label for="cell_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Célula
                    </label>
                    <select name="cell_id" id="cell_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('cell_id') border-red-500 @enderror">
                        <option value="">Sem célula</option>
                        @foreach($cells as $cell)
                        <option value="{{ $cell->id }}" {{ old('cell_id', $user->cell_id) == $cell->id ? 'selected' : '' }}>
                            {{ $cell->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('cell_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }}
                                class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Ativo</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }}
                                class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Inativo</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <div class="flex items-start">
                    <i class="bi bi-exclamation-triangle text-yellow-600 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-800">Atenção</h4>
                        <p class="text-sm text-yellow-700 mt-1">
                            Ao alterar o papel do utilizador, ele será notificado por email sobre a mudança.
                            Para alterar a senha, use a opção "Redefinir Senha" na página de detalhes.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Informações do Sistema</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Criado em:</p>
                        <p class="font-medium text-gray-800">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Última atualização:</p>
                        <p class="font-medium text-gray-800">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Último acesso:</p>
                        <p class="font-medium text-gray-800">
                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('users.index') }}" 
                        class="inline-flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        <i class="bi bi-arrow-left mr-2"></i>
                        Voltar
                    </a>
                    <a href="{{ route('users.show', $user) }}" 
                        class="inline-flex items-center px-6 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition font-medium">
                        <i class="bi bi-eye mr-2"></i>
                        Ver Detalhes
                    </a>
                </div>
                <button type="submit" 
                    class="inline-flex items-center px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-lg shadow-blue-500/30">
                    <i class="bi bi-check-circle mr-2"></i>
                    Atualizar Utilizador
                </button>
            </div>
        </form>
    </div>
</div>
@endsection