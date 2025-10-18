@extends('layouts.app')

@section('title', 'Novo Membro - Projeto Edificar')
@section('page-title', 'Registar Novo Membro')
@section('page-subtitle', 'Preencha os dados para criar uma conta')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <!-- Mensagem informativa -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
            <p class="text-blue-800 text-sm">
                <i class="bi bi-info-circle mr-2"></i>
                <strong>Nota:</strong> Este formulário é usado pelo Admin para registar novos membros no sistema.
            </p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Nome -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-person mr-2"></i>Nome Completo
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="João da Silva" required>
                @error('name')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-envelope mr-2"></i>Email
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                    placeholder="seu@email.com" required>
                @error('email')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Telefone -->
            <div class="mb-6">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-telephone mr-2"></i>Telefone
                </label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="823562000">
            </div>

            <!-- Célula -->
            <div class="mb-6">
                <label for="cell_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-people-fill mr-2"></i>Célula
                </label>
                <select name="cell_id" id="cell_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cell_id') border-red-500 @enderror"
                    required>
                    <option value="">-- Selecione sua célula --</option>
                    @foreach($cells as $cell)
                    <option value="{{ $cell->id }}" @selected(old('cell_id') == $cell->id)>
                        {{ $cell->name }} (Líder: {{ $cell->leader?->name }})
                    </option>
                    @endforeach
                </select>
                @error('cell_id')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Senha -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-lock mr-2"></i>Senha
                </label>
                <input type="password" name="password" id="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                    placeholder="••••••••" required>
                @error('password')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirmar Senha -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-lock-fill mr-2"></i>Confirmar Senha
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="••••••••" required>
            </div>

            <!-- Botões -->
            <div class="flex space-x-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="bi bi-check-circle mr-2"></i>Registar Membro
                </button>
                <a href="{{ route('users.index') }}" class="flex-1 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition font-medium text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection