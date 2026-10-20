@extends('layouts.app')

@section('title', 'Nova Turma - Portal Life Church')
@section('page-title', 'Criar Nova Turma')
@section('page-subtitle', 'Defina os detalhes da turma e seus líderes')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('course-classes.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Turmas</span>
        </a>
    </div>

    <form action="{{ route('course-classes.store') }}" method="POST" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Formulário de Turma</p>
            <h2 class="text-sm font-bold text-gray-900">Configuração da Turma</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="course_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Curso *</label>
                    <select name="course_id" id="course_id" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="">Selecione um curso</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ (old('course_id') ?? $selectedCourseId) == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="type" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Tipo de Turma *</label>
                    <select name="type" id="type" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="casais_vivendo" {{ old('type') == 'casais_vivendo' ? 'selected' : '' }}>Casais Vivendo Juntos</option>
                        <option value="pre_nupcial" {{ old('type') == 'pre_nupcial' ? 'selected' : '' }}>Pré-Nupcial</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome da Turma *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ex: Turma A - 2026"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data de Início</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data de Término</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>
            </div>

            <!-- Equipe Docente -->
            <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-3 sm:p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-orange-700 mb-3">Equipe Docente e Apoio</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label for="teacher_male_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Professor (Esposo)</label>
                        <select name="teacher_male_id" id="teacher_male_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                            <option value="">Selecione...</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_male_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="teacher_female_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Professora (Esposa)</label>
                        <select name="teacher_female_id" id="teacher_female_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                            <option value="">Selecione...</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_female_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="assistant_male_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Auxiliar (Esposo)</label>
                        <select name="assistant_male_id" id="assistant_male_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                            <option value="">Selecione...</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('assistant_male_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="assistant_female_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Auxiliar (Esposa)</label>
                        <select name="assistant_female_id" id="assistant_female_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                            <option value="">Selecione...</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('assistant_female_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Observações</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Informações adicionais..."
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('course-classes.index') }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Criar Turma</span>
            </button>
        </div>
    </form>
</div>
@endsection
