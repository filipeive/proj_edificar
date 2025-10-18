@extends('layouts.app')

@section('title', 'Criar Novo Membro - Projeto Edificar')
@section('page-title', 'Criar Novo Membro')
@section('page-subtitle', 'Registar um novo membro na célula')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ route('members.store') }}" method="POST">
            @csrf

           <!-- Aviso -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="bi bi-info-circle mr-2"></i>
                    <strong>Novo Membro:</strong> Este membro será criado e poderá ter contribuições registadas em seu nome.
                </p>
            </div>

            <!-- Seleção de Célula -->
            @if($userRole !== 'lider_celula')
            <div class="mb-6">
                <label for="cell_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-people-fill mr-2"></i>Célula <span class="text-red-500">*</span>
                </label>
                <select name="cell_id" id="cell_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cell_id') border-red-500 @enderror">
                    <option value="">-- Selecione uma célula --</option>
                    @foreach($availableCells as $cell)
                        <option value="{{ $cell->id }}" @selected(old('cell_id') == $cell->id || ($selectedCell && $selectedCell->id == $cell->id))>
                            {{ $cell->name }}
                            @if($cell->supervision)
                                ({{ $cell->supervision->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('cell_id')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
            @else
            <input type="hidden" name="cell_id" value="{{ $selectedCell->id }}">
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700 font-medium">
                    <i class="bi bi-check-circle mr-2"></i>Célula: <strong>{{ $selectedCell->name }}</strong>
                </p>
            </div>
            @endif

            <!-- Dados Pessoais -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-person mr-2"></i>Nome Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" placeholder="João da Silva" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-telephone mr-2"></i>Telefone (Opcional)
                    </label>
                    <input type="tel" name="phone" id="phone"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        value="{{ old('phone') }}" placeholder="823562000">
                </div>
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-envelope mr-2"></i>Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                    value="{{ old('email') }}" placeholder="email@example.com" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Compromisso -->
            <div class="mb-6">
                <label for="package_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-handshake mr-2"></i>Pacote de Compromisso (Opcional)
                </label>
                <select name="package_id" id="package_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pacote Padrão (Pacote 1) --</option>
                    @php
                        $packages = \App\Models\CommitmentPackage::where('is_active', true)->orderBy('order')->get();
                    @endphp
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" @selected(old('package_id') == $package->id)>
                            {{ $package->name }} - {{ number_format($package->min_amount, 0) }}
                            @if($package->max_amount)
                                - {{ number_format($package->max_amount, 0) }}
                            @else
                                +
                            @endif
                            MT
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Credenciais de Acesso -->
            <div class="mb-6 pb-6 border-b">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="bi bi-lock mr-2"></i>Credenciais de Acesso
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Senha <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                            placeholder="••••••••" required>
                        @error('password')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Senha <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-3">
                    <i class="bi bi-info-circle mr-1"></i>
                    Esta senha permitirá que o membro aceda o sistema para ver suas contribuições
                </p>
            </div>

            <!-- Botões -->
            <div class="flex space-x-4">
                <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-bold">
                    <i class="bi bi-plus-circle mr-2"></i>Criar Membro
                </button>
                <a href="javascript:history.back()" class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-bold text-center">
                    <i class="bi bi-arrow-left mr-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection