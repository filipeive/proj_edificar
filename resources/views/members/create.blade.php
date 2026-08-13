@extends('layouts.app')

@section('title', 'Criar Membro / Líder')
@section('page-title', 'Adicionar Novo')
@section('page-subtitle', 'Registar um novo membro ou líder de célula')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                Verifique os campos destacados e tente novamente.
            </div>
        @endif

        <form action="{{ route('members.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="visitor_id" value="{{ request('visitor_id') }}">

            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4 text-sm text-blue-800">
                <p class="font-black uppercase tracking-wider text-[11px] mb-1">Contexto</p>
                @if($userRole === 'lider_celula')
                    O membro será adicionado à sua célula.
                @elseif($userRole === 'supervisor')
                    Você pode adicionar membros às células da sua supervisão.
                @elseif($userRole === 'pastor_zona')
                    Você pode adicionar membros às células da sua zona.
                @else
                    Você pode adicionar membros a qualquer célula.
                @endif
            </div>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-sm font-black uppercase tracking-wider text-gray-500">Estrutura</h2>

                @if($userRole === 'lider_celula' && $selectedCell)
                    <input type="hidden" name="cell_id" value="{{ $selectedCell->id }}">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                        <p class="font-bold">Célula selecionada: {{ $selectedCell->name }}</p>
                        @if($selectedCell->supervision)
                            <p class="text-xs mt-1">{{ $selectedCell->supervision->name }}</p>
                        @endif
                    </div>
                @else
                    <div>
                        <label for="cell_id" class="mb-2 block text-sm font-bold text-gray-700">Célula <span class="text-red-500">*</span></label>
                        @if($availableCells->isEmpty())
                            <div class="rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm font-bold text-yellow-800">
                                Nenhuma célula disponível para o seu perfil.
                            </div>
                        @endif
                        <select name="cell_id" id="cell_id" required data-label="Célula"
                            class="searchable-select custom-select w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('cell_id') border-red-500 @enderror">
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
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-sm font-black uppercase tracking-wider text-gray-500">Dados do Membro</h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-bold text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $prefill['name'] ?? '') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('name') border-red-500 @enderror"
                            placeholder="Nome completo">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="mb-2 block text-sm font-bold text-gray-700">Cargo / Função <span class="text-red-500">*</span></label>
                        <select name="role" id="role" required
                            class="custom-select w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('role') border-red-500 @enderror">
                            @foreach($allowedRoles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-bold text-gray-700">Telefone</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $prefill['phone'] ?? '') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('phone') border-red-500 @enderror"
                            placeholder="823562000">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('email') border-red-500 @enderror"
                            placeholder="nome@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-sm font-black uppercase tracking-wider text-gray-500">Compromisso (Opcional)</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="package_id" class="mb-2 block text-sm font-bold text-gray-700">Pacote</label>
                        <select name="package_id" id="package_id"
                            class="custom-select w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="">Nenhum (definir depois)</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} - {{ number_format($package->min_amount, 2, ',', '.') }} MT
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="committed_amount" class="mb-2 block text-sm font-bold text-gray-700">Valor Comprometido (MT)</label>
                        <input type="number" name="committed_amount" id="committed_amount" step="0.01" min="0" value="{{ old('committed_amount') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            placeholder="0.00">
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-sm font-black uppercase tracking-wider text-gray-500">Credenciais de Acesso</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-bold text-gray-700">Senha <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required minlength="6"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('password') border-red-500 @enderror"
                            placeholder="Mínimo 6 caracteres">
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-bold text-gray-700">Confirmar Senha <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('password_confirmation') border-red-500 @enderror"
                            placeholder="Repita a senha">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('members.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-5 py-3 text-xs font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-xs font-black uppercase tracking-wider text-white transition hover:bg-blue-700">
                    <i class="bi bi-check2-circle mr-2"></i>
                    Salvar Registo
                </button>
            </div>
        </form>
    </div>
@endsection
