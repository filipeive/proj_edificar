@extends('layouts.app')

@section('title', 'Nova Turma - Portal Life Church')
@section('page-title', 'Criar Nova Turma')
@section('page-subtitle', 'Defina os detalhes da turma e seus líderes')

@section('content')
    <div class="w-full space-y-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('course-classes.index') }}"
                    class="w-10 h-10 rounded-xl bg-white text-gray-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all shadow-sm border border-gray-100">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">Criar Nova Turma</h1>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Configuração completa do curso</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-3">
                <span class="px-4 py-2 rounded-xl bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest">
                    Escola Ministerial
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                        <h2 class="text-lg font-black text-gray-900">Informações da Turma</h2>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Dados essenciais</p>
                    </div>
                    <div class="p-8">
                        <form action="{{ route('course-classes.store') }}" method="POST" class="space-y-8">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="course_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Curso *</label>
                                    <select name="course_id" id="course_id" required
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none custom-select">
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
                                    <label for="type" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Tipo de Turma *</label>
                                    <select name="type" id="type" required
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none custom-select">
                                        <option value="casais_vivendo" {{ old('type') == 'casais_vivendo' ? 'selected' : '' }}>Casais Vivendo Juntos</option>
                                        <option value="pre_nupcial" {{ old('type') == 'pre_nupcial' ? 'selected' : '' }}>Pré-Nupcial</option>
                                    </select>
                                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="name" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Nome da Turma *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        placeholder="Ex: Turma A - 2026"
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="start_date" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Data de Início</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                                </div>

                                <div>
                                    <label for="end_date" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Data de Término</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="teacher_male_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Professor (Esposo)</label>
                                    <select name="teacher_male_id" id="teacher_male_id"
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none custom-select">
                                        <option value="">Selecione...</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_male_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="teacher_female_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Professor (Esposa)</label>
                                    <select name="teacher_female_id" id="teacher_female_id"
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none custom-select">
                                        <option value="">Selecione...</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_female_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="assistant_male_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Auxiliar (Esposo)</label>
                                    <select name="assistant_male_id" id="assistant_male_id"
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none custom-select">
                                        <option value="">Selecione...</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('assistant_male_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="assistant_female_id" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Auxiliar (Esposa)</label>
                                    <select name="assistant_female_id" id="assistant_female_id"
                                        class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none custom-select">
                                        <option value="">Selecione...</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('assistant_female_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="notes" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Observações</label>
                                <textarea name="notes" id="notes" rows="4"
                                    class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-gray-700 resize-none">{{ old('notes') }}</textarea>
                            </div>

                            <div class="flex flex-col md:flex-row justify-end gap-3 pt-2">
                                <a href="{{ route('course-classes.index') }}"
                                    class="px-6 py-3 border border-gray-200 rounded-2xl text-gray-600 hover:bg-gray-50 transition font-bold text-center">
                                    Cancelar
                                </a>
                                <button type="submit"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 font-black uppercase tracking-widest text-xs">
                                    Criar Turma
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Resumo</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-center justify-between">
                            <span>Curso</span>
                            <span class="font-black">{{ $selectedCourseId ? $courses->firstWhere('id', $selectedCourseId)->name ?? '—' : '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Tipo</span>
                            <span class="font-black">Pré‑Nupcial / Casais</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Status</span>
                            <span class="font-black text-emerald-600">Nova turma</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[2rem] shadow-xl p-8 text-white">
                    <h3 class="text-sm font-black uppercase tracking-widest text-blue-100 mb-3">Dica rápida</h3>
                    <p class="text-sm text-white/90 leading-relaxed">
                        Após criar a turma, você poderá atribuir inscrições públicas diretamente pelo curso ou pela turma.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
