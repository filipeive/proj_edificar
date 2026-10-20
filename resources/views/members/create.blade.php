@extends('layouts.app')

@section('title', 'Criar Membro / Líder - Portal Life Church')
@section('page-title', 'Adicionar Novo Membro')
@section('page-subtitle', 'Registar um novo membro ou líder de célula')

@section('header-actions')
    <a href="{{ route('members.index') }}"
        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
        <i class="bi bi-arrow-left text-xs"></i>
        <span>Voltar</span>
    </a>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Top Navigation (Desktop) -->
        <div class="hidden md:flex items-center justify-between gap-3">
            <a href="{{ route('members.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:text-orange-700 dark:hover:text-orange-400 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Membros</span>
            </a>
        </div>

        <!-- Executive Header Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-person-plus text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">Cadastro de Membros</span>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">Adicionar Novo Membro</h1>
                    <p class="text-xs text-gray-500">
                        @if($userRole === 'lider_celula')
                            O membro será cadastrado na sua célula.
                        @elseif($userRole === 'supervisor')
                            Adicionando membro às células sob sua supervisão.
                        @elseif($userRole === 'pastor_zona')
                            Adicionando membro às células da sua zona pastoral.
                        @else
                            Registo geral de membro ou liderança na igreja.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-xs font-bold text-red-700">
                <i class="bi bi-exclamation-triangle-fill mr-1.5"></i>
                Por favor, verifique os campos destacados e tente novamente.
            </div>
        @endif

        <form action="{{ route('members.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="visitor_id" value="{{ request('visitor_id') }}">

            <!-- Seção 1: Estrutura / Célula -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <h2 class="text-xs font-black uppercase tracking-wider text-gray-900">1. Alocação Estrutural</h2>

                @if($userRole === 'lider_celula' && $selectedCell)
                    <input type="hidden" name="cell_id" value="{{ $selectedCell->id }}">
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50/60 p-3 text-xs text-emerald-900">
                        <p class="font-bold">Célula selecionada: {{ $selectedCell->name }}</p>
                        @if($selectedCell->supervision)
                            <p class="text-[11px] text-emerald-700 mt-0.5">{{ $selectedCell->supervision->name }}</p>
                        @endif
                    </div>
                @else
                    <div class="space-y-1">
                        <label for="cell_id" class="block text-xs font-bold text-gray-700">Célula <span class="text-red-500">*</span></label>
                        @if($availableCells->isEmpty())
                            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 text-xs font-bold text-yellow-800">
                                Nenhuma célula disponível para o seu perfil.
                            </div>
                        @endif
                        <select name="cell_id" id="cell_id" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('cell_id') border-red-500 @enderror">
                            <option value="">-- Selecione uma célula --</option>
                            @foreach($availableCells as $cell)
                                <option value="{{ $cell->id }}" {{ (old('cell_id', $prefill['cell_id'] ?? '')) == $cell->id ? 'selected' : '' }}>
                                    {{ $cell->name }} ({{ $cell->type_label }})
                                    @if($cell->supervision)
                                        - {{ $cell->supervision->name }}
                                        @if($cell->supervision->zone)
                                            / {{ $cell->supervision->zone->name }}
                                        @endif
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('cell_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Seção 2: Dados Pessoais -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <h2 class="text-xs font-black uppercase tracking-wider text-gray-900">2. Dados Pessoais e Função</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-bold text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $prefill['name'] ?? '') }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('name') border-red-500 @enderror"
                            placeholder="Nome completo">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="role" class="block text-xs font-bold text-gray-700">Função / Cargo <span class="text-red-500">*</span></label>
                        <select name="role" id="role" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('role') border-red-500 @enderror">
                            @foreach($allowedRoles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="block text-xs font-bold text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('email') border-red-500 @enderror"
                            placeholder="exemplo@email.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="phone" class="block text-xs font-bold text-gray-700">Telefone</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $prefill['phone'] ?? '') }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('phone') border-red-500 @enderror"
                            placeholder="823562000">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Seção 3: Compromisso Financeiro (Opcional) -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <h2 class="text-xs font-black uppercase tracking-wider text-gray-900">3. Compromisso Inicial (Opcional)</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="package_id" class="block text-xs font-bold text-gray-700">Pacote de Contribuição</label>
                        <select name="package_id" id="package_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Nenhum (definir futuramente)</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} - {{ number_format($package->min_amount, 2, ',', '.') }} MT
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="committed_amount" class="block text-xs font-bold text-gray-700">Valor Comprometido (MT)</label>
                        <input type="number" name="committed_amount" id="committed_amount" step="0.01" min="0" value="{{ old('committed_amount') }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                            placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Seção 4: Credenciais de Acesso -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <h2 class="text-xs font-black uppercase tracking-wider text-gray-900">4. Acesso ao Sistema</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="password" class="block text-xs font-bold text-gray-700">Senha <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required minlength="6"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('password') border-red-500 @enderror"
                            placeholder="Mínimo 6 caracteres">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700">Confirmar Senha <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('password_confirmation') border-red-500 @enderror"
                            placeholder="Repita a senha">
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('members.index') }}"
                    class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                    <i class="bi bi-check-lg text-xs"></i>
                    <span>Salvar Membro</span>
                </button>
            </div>
        </form>
    </div>
@endsection
