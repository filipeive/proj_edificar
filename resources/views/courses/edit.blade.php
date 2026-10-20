@extends('layouts.app')

@section('title', 'Editar Curso')
@section('page-title', 'Editar Curso')
@section('page-subtitle', 'Atualize as informações do curso')

@section('content')
<div class="space-y-5 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Lista</span>
        </a>
        <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">
            <i class="bi bi-eye"></i>
            <span>Ver Detalhes</span>
        </a>
    </div>

    <form action="{{ route('courses.update', $course) }}" method="POST" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Edição de Curso</p>
            <h2 class="text-sm font-bold text-gray-900">{{ $course->name }}</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome do Curso <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $course->name) }}" required placeholder="Ex: Academia de Vida - Nível 1"
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Categoria</label>
                    <select name="category" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="Teologia" {{ old('category', $course->category) == 'Teologia' ? 'selected' : '' }}>Teologia / Academia</option>
                        <option value="Família" {{ old('category', $course->category) == 'Família' ? 'selected' : '' }}>Família / Casais</option>
                        <option value="Liderança" {{ old('category', $course->category) == 'Liderança' ? 'selected' : '' }}>Liderança</option>
                        <option value="Geral" {{ old('category', $course->category) == 'Geral' ? 'selected' : '' }}>Geral</option>
                    </select>
                    @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Duração</label>
                    <input type="text" name="duration" value="{{ old('duration', $course->duration) }}" placeholder="Ex: 3 meses / 12 aulas"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                    @error('duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nível / Cargo Alvo</label>
                    <select name="target_role" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="" {{ old('target_role', $course->target_role) == '' ? 'selected' : '' }}>Todos (Geral)</option>
                        <option value="membro" {{ old('target_role', $course->target_role) == 'membro' ? 'selected' : '' }}>Apenas Membros</option>
                        <option value="lider_celula" {{ old('target_role', $course->target_role) == 'lider_celula' ? 'selected' : '' }}>Apenas Líderes</option>
                        <option value="supervisor" {{ old('target_role', $course->target_role) == 'supervisor' ? 'selected' : '' }}>Apenas Supervisores</option>
                        <option value="pastor_zona" {{ old('target_role', $course->target_role) == 'pastor_zona' ? 'selected' : '' }}>Apenas Pastores de Zona</option>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">Define quem será elegível para se inscrever.</p>
                    @error('target_role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data Limite de Inscrição</label>
                    <input type="date" name="registration_deadline" value="{{ old('registration_deadline', $course->registration_deadline ? \Carbon\Carbon::parse($course->registration_deadline)->format('Y-m-d') : '') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    @error('registration_deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $course->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                    <label for="is_active" class="text-xs font-bold text-gray-700 cursor-pointer">Curso Ativo</label>
                </div>

                <div class="flex items-center gap-2">
                    <input type="hidden" name="registration_open" value="0">
                    <input type="checkbox" name="registration_open" value="1" id="registration_open" {{ old('registration_open', $course->registration_open) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                    <label for="registration_open" class="text-xs font-bold text-gray-700 cursor-pointer">Inscrições Abertas</label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="3" placeholder="Descreva o objetivo e conteúdo do curso..."
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('description', $course->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('courses.index') }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Atualizar Curso</span>
            </button>
        </div>
    </form>
</div>
@endsection
