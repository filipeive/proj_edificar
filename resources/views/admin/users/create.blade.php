@extends('layouts.app')

@section('title', 'Criar Utilizador - Projeto Edificar')
@section('page-title', 'Criar Novo Utilizador')
@section('page-subtitle', 'Adicionar um novo membro ou líder ao sistema')

@section('content')
<div class="grid grid-max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">Utilizadores</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Criar</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="bi bi-person-plus-fill mr-3"></i>
                Informações do Utilizador
            </h2>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
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
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
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
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
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
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                        <option value="">Selecione o papel</option>
                        <option value="membro" {{ old('role') == 'membro' ? 'selected' : '' }}>Membro</option>
                        <option value="lider_celula" {{ old('role') == 'lider_celula' ? 'selected' : '' }}>Líder de Célula</option>
                        <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="pastor_zona" {{ old('role') == 'pastor_zona' ? 'selected' : '' }}>Pastor de Zona</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
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
                        <option value="{{ $cell->id }}" {{ old('cell_id') == $cell->id ? 'selected' : '' }}>
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
                            <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Ativo</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}
                                class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Inativo</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Seção de Senha -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-lock-fill mr-2 text-blue-600"></i>
                    Credenciais de Acesso
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Senha -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Senha <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                            <button type="button" onclick="togglePassword('password')" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Mínimo de 6 caracteres</p>
                    </div>

                    <!-- Confirmar Senha -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Senha <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="button" onclick="togglePassword('password_confirmation')" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="bi bi-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex items-start">
                    <i class="bi bi-info-circle text-blue-600 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800">Informação Importante</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            O utilizador receberá um email com as credenciais de acesso. 
                            Certifique-se de que o endereço de email está correto.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('users.index') }}" 
                    class="inline-flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Cancelar
                </a>
                <button type="submit" 
                    class="inline-flex items-center px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-lg shadow-blue-500/30">
                    <i class="bi bi-check-circle mr-2"></i>
                    Criar Utilizador
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection