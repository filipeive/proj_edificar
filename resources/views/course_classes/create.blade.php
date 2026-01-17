@extends('layouts.app')

@section('title', 'Nova Turma - Portal Life Church')
@section('page-title', 'Criar Nova Turma')
@section('page-subtitle', 'Defina os detalhes da turma e seus líderes')

@section('content')
    <div class="container-fluid">
        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('course-classes.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
                    <i class="bi bi-arrow-left mr-2"></i> Voltar para a lista
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8">
                    <form action="{{ route('course-classes.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label for="course_id" class="block text-sm font-bold text-gray-700 mb-2">Curso *</label>
                                <select name="course_id" id="course_id" required
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione um curso</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (old('course_id') ?? $selectedCourseId) == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-1 text-dark">
                                <label for="type" class="block text-sm font-bold text-gray-700 mb-2">Tipo de Turma *</label>
                                <select name="type" id="type" required
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="casais_vivendo" {{ old('type') == 'casais_vivendo' ? 'selected' : '' }}>Casais Vivendo Juntos</option>
                                    <option value="pre_nupcial" {{ old('type') == 'pre_nupcial' ? 'selected' : '' }}>Pré-Nupcial</option>
                                </select>
                                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nome da Turma *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    placeholder="Ex: Turma A - 2026"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="teacher_male_id" class="block text-sm font-bold text-gray-700 mb-2">Professor (Esposo)</label>
                                <select name="teacher_male_id" id="teacher_male_id"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione...</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_male_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="teacher_female_id" class="block text-sm font-bold text-gray-700 mb-2">Professor (Esposa)</label>
                                <select name="teacher_female_id" id="teacher_female_id"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione...</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_female_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="assistant_male_id" class="block text-sm font-bold text-gray-700 mb-2">Auxiliar (Esposo)</label>
                                <select name="assistant_male_id" id="assistant_male_id"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione...</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('assistant_male_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="assistant_female_id" class="block text-sm font-bold text-gray-700 mb-2">Auxiliar (Esposa)</label>
                                <select name="assistant_female_id" id="assistant_female_id"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione...</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('assistant_female_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="start_date" class="block text-sm font-bold text-gray-700 mb-2">Data de
                                    Início</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-bold text-gray-700 mb-2">Data de
                                    Término</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">Observações</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('course-classes.index') }}"
                                class="px-6 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition font-bold">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 font-bold">
                                Criar Turma
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection