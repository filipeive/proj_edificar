@extends('layouts.app')

@section('title', 'Criar Novo Membro - Portal Life Church')
@section('page-title', 'Criar Novo Membro')
@section('page-subtitle', 'Registar um novo membro na célula')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
        <div>
            <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão Celular</p>
            <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Criar Novo Membro</h1>
            <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Registar um novo membro na célula</p>
        </div>
        <div>
            <a href="javascript:history.back()"
                class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                <i class="bi bi-arrow-left"></i> <span>Voltar</span>
            </a>
        </div>
    </header>

    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
        <form action="{{ route('members.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Aviso -->
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs sm:text-sm text-blue-800">
                    <i class="bi bi-info-circle mr-1.5"></i>
                    <strong>Novo Membro:</strong> Este membro será criado e poderá ter contribuições registadas em seu nome.
                </p>
            </div>

            <!-- Seleção de Célula -->
            @if($userRole !== 'lider_celula')
            <div>
                <label for="cell_id" class="block text-xs font-bold text-gray-700 mb-1.5">
                    <i class="bi bi-people-fill mr-1.5 text-orange-700"></i>Célula <span class="text-red-500">*</span>
                </label>
                <select name="cell_id" id="cell_id" required
                    class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('cell_id') border-red-500 @enderror">
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
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            @else
            <input type="hidden" name="cell_id" value="{{ $selectedCell->id }}">
            <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-xs sm:text-sm text-green-800 font-semibold">
                    <i class="bi bi-check-circle mr-1.5"></i>Célula: <strong>{{ $selectedCell->name }}</strong>
                </p>
            </div>
            @endif

            <!-- Dados Pessoais -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 mb-1.5">
                        <i class="bi bi-person mr-1.5 text-orange-700"></i>Nome Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" placeholder="João da Silva" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-gray-700 mb-1.5">
                        <i class="bi bi-telephone mr-1.5 text-orange-700"></i>Telefone (Opcional)
                    </label>
                    <input type="tel" name="phone" id="phone"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500"
                        value="{{ old('phone') }}" placeholder="823562000">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 mb-1.5">
                    <i class="bi bi-envelope mr-1.5 text-orange-700"></i>Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email"
                    class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('email') border-red-500 @enderror"
                    value="{{ old('email') }}" placeholder="email@example.com" required>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Compromisso -->
            <div>
                <label for="package_id" class="block text-xs font-bold text-gray-700 mb-1.5">
                    <i class="bi bi-handshake mr-1.5 text-orange-700"></i>Pacote de Compromisso (Opcional)
                </label>
                <select name="package_id" id="package_id"
                    class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
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
            <div class="pt-4 border-t border-gray-200">
                <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-gray-700 mb-3">
                    <i class="bi bi-lock mr-1.5 text-orange-700"></i>Credenciais de Acesso
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 mb-1.5">
                            Senha <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('password') border-red-500 @enderror"
                            placeholder="••••••••" required>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1.5">
                            Confirmar Senha <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-2">
                    <i class="bi bi-info-circle mr-1"></i>
                    Esta senha permitirá que o membro aceda o sistema para ver suas contribuições
                </p>
            </div>

            <!-- Botões -->
            <div class="flex gap-3 pt-2 border-t border-gray-200">
                <button type="submit" class="flex-1 bg-orange-700 text-white px-4 py-2.5 rounded-lg hover:bg-orange-800 transition font-bold text-xs sm:text-sm shadow-sm">
                    <i class="bi bi-plus-circle mr-1.5"></i>Criar Membro
                </button>
                <a href="javascript:history.back()" class="flex-1 border border-gray-300 bg-white text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition font-bold text-xs sm:text-sm text-center shadow-sm">
                    <i class="bi bi-arrow-left mr-1.5"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection