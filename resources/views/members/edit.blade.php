@extends('layouts.app')

@section('title', 'Editar Membro / Líder - Portal Life Church')
@section('page-title', 'Editar Registo')
@section('page-subtitle', $member->name)

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('members.show', ['member' => $member->id]) }}" 
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-eye text-xs"></i>
            <span>Ver Detalhes</span>
        </a>
        <a href="{{ route('members.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
            <span>Voltar</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Top Navigation & Action Bar (Desktop) -->
        <div class="hidden md:flex items-center justify-between gap-3">
            <a href="{{ route('members.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:text-orange-700 dark:hover:text-orange-400 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Membros</span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('members.show', ['member' => $member->id]) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                    <i class="bi bi-eye text-xs text-orange-700 dark:text-orange-400"></i>
                    <span>Ver Detalhes</span>
                </a>
            </div>
        </div>

        <!-- Executive Header Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-person-gear text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">Edição de Membro</span>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">{{ $member->name }}</h1>
                    <p class="text-xs text-gray-500">Atualize dados cadastrais, cargo e alocação de célula</p>
                </div>
            </div>
        </div>

        <form action="{{ route('members.update', ['member' => $member->id]) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Dados Pessoais -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">1. Dados Pessoais e Função</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-bold text-gray-700">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('name') border-red-500 @enderror"
                            value="{{ old('name', $member->name) }}" placeholder="Nome Completo">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="role" class="block text-xs font-bold text-gray-700">
                            Cargo / Função <span class="text-red-500">*</span>
                        </label>
                        <select name="role" id="role" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('role') border-red-500 @enderror">
                            @foreach($allowedRoles as $role)
                                <option value="{{ $role }}" {{ old('role', $member->role) == $role ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="phone" class="block text-xs font-bold text-gray-700">Telefone</label>
                        <input type="tel" name="phone" id="phone"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                            value="{{ old('phone', $member->phone) }}" placeholder="823562000">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Email (Não editável)</label>
                        <input type="email" class="w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-500 cursor-not-allowed"
                            value="{{ $member->email }}" readonly>
                        <input type="hidden" name="email" value="{{ $member->email }}">
                    </div>
                </div>
            </div>

            <!-- Célula -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">2. Atribuição de Célula</h3>

                <div class="space-y-1">
                    <label for="cell_id" class="block text-xs font-bold text-gray-700">
                        Célula Vinculada <span class="text-red-500">*</span>
                    </label>
                    <select name="cell_id" id="cell_id" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('cell_id') border-red-500 @enderror">
                        <option value="">-- Selecione a célula --</option>
                        @foreach($availableCells as $cell)
                            <option value="{{ $cell->id }}" {{ old('cell_id', $member->cell_id) == $cell->id ? 'selected' : '' }}>
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
                    <p class="text-[11px] text-gray-400 mt-1">Célula atual: <strong>{{ $member->cell->name ?? 'Nenhuma' }}</strong></p>
                </div>
            </div>

            <!-- Status -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-900">Status do Membro</p>
                        <p class="text-[11px] text-gray-500">Membro ativo pode aceder ao portal e ter presenças registradas</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" 
                            class="sr-only peer" {{ old('is_active', $member->is_active) ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-600"></div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <a href="{{ route('members.show', ['member' => $member->id]) }}"
                    class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                    class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                    <i class="bi bi-check-lg text-xs"></i>
                    <span>Salvar Alterações</span>
                </button>
            </div>
        </form>
    </div>
@endsection
