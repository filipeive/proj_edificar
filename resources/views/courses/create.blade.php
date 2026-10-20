@extends('layouts.app')

@section('title', 'Novo Curso')
@section('page-title', 'Criar Curso')
@section('page-subtitle', 'Adicione um novo curso à academia')

@section('content')
<div class="space-y-5 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Lista</span>
        </a>
    </div>

    <form action="{{ route('courses.store') }}" method="POST" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Formulário de Cadastro</p>
            <h2 class="text-sm font-bold text-gray-900">Novo Curso</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome do Curso <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Academia de Vida - Nível 1"
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Categoria</label>
                    <select name="category" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="Teologia" {{ old('category') == 'Teologia' ? 'selected' : '' }}>Teologia / Academia</option>
                        <option value="Família" {{ old('category') == 'Família' ? 'selected' : '' }}>Família / Casais</option>
                        <option value="Liderança" {{ old('category') == 'Liderança' ? 'selected' : '' }}>Liderança</option>
                        <option value="Geral" {{ old('category') == 'Geral' ? 'selected' : '' }}>Geral</option>
                    </select>
                    @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Duração</label>
                    <input type="text" name="duration" value="{{ old('duration') }}" placeholder="Ex: 3 meses / 12 aulas"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                    @error('duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nível / Cargo Alvo</label>
                    <select name="target_role" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="" {{ old('target_role') == '' ? 'selected' : '' }}>Todos (Geral)</option>
                        <option value="membro" {{ old('target_role') == 'membro' ? 'selected' : '' }}>Apenas Membros</option>
                        <option value="lider_celula" {{ old('target_role') == 'lider_celula' ? 'selected' : '' }}>Apenas Líderes</option>
                        <option value="supervisor" {{ old('target_role') == 'supervisor' ? 'selected' : '' }}>Apenas Supervisores</option>
                        <option value="pastor_zona" {{ old('target_role') == 'pastor_zona' ? 'selected' : '' }}>Apenas Pastores de Zona</option>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">Define quem será elegível para se inscrever.</p>
                    @error('target_role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data Limite de Inscrição</label>
                    <input type="date" name="registration_deadline" value="{{ old('registration_deadline') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    @error('registration_deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <label for="is_active" class="text-xs font-bold text-gray-700 cursor-pointer">Curso Ativo</label>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="3" placeholder="Descreva o objetivo e conteúdo do curso..."
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('courses.index') }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Criar Curso</span>
            </button>
        </div>
    </form>
</div>
@endsection
