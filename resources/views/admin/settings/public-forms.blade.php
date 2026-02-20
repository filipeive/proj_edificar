@extends('layouts.app')

@section('title', 'Configuração de Formulários Públicos')
@section('page-title', 'Formulários Públicos')
@section('page-subtitle', 'Defina os cursos de destino para inscrições externas')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-2">Configuração de Formulários</h1>
            <p class="text-gray-500 dark:text-gray-400 font-bold uppercase text-[10px] tracking-widest">Gerencie para onde
                as inscrições externas são enviadas</p>
        </div>

        @if(session('success'))
            <div
                class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-2xl flex items-center gap-3 text-green-600 dark:text-green-400">
                <i class="bi bi-check-circle-fill"></i>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('settings.public-forms.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Pre-Marital Course Configuration -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 p-10 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-10 opacity-5 pointer-events-none">
                    <i class="bi bi-heart-fill text-9xl"></i>
                </div>

                <div class="flex items-center gap-4 mb-8">
                    <div
                        class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center text-xl">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">Curso Pré-Marital / Casais</h2>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Formulário Público de Inscrição
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Curso de
                            Destino</label>
                        <select name="pre_marital_course_id"
                            class="w-full px-6 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-bold text-gray-900 dark:text-white">
                            <option value="" disabled {{ !$preMaritalCourseId ? 'selected' : '' }}>Selecione o curso para
                                receber inscrições...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $preMaritalCourseId == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pre_marital_course_id')
                            <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                            <i class="bi bi-info-circle mr-1"></i>
                            Todas as novas inscrições feitas através do formulário público
                            <strong>/inscricao-pre-marital</strong> serão associadas a este curso.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ministerial Courses Configuration -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 p-10 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-10 opacity-5 pointer-events-none">
                    <i class="bi bi-mortarboard-fill text-9xl"></i>
                </div>

                <div class="flex items-center gap-4 mb-8">
                    <div
                        class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center text-xl">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">Cursos Ministeriais</h2>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Inscrição Individual Habilitada
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($courses->whereNotIn('id', [$preMaritalCourseId]) as $course)
                            <label
                                class="relative flex items-center p-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border-2 border-transparent cursor-pointer hover:border-orange-500/30 transition-all select-none group">
                                <input type="checkbox" name="ministerial_course_ids[]" value="{{ $course->id }}"
                                    class="w-5 h-5 rounded-lg border-gray-300 text-orange-600 focus:ring-orange-500 transition-all"
                                    {{ in_array($course->id, $ministerialCourseIds) ? 'checked' : '' }}>
                                <div class="ml-4">
                                    <span
                                        class="block text-sm font-bold text-gray-900 dark:text-white group-hover:text-orange-500 transition-colors">
                                        {{ $course->name }}
                                    </span>
                                    <span class="block text-[10px] text-gray-500 uppercase tracking-wider font-medium">
                                        {{ $course->category }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-4 text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                        <i class="bi bi-info-circle mr-1"></i>
                        Cursos selecionados terão o formulário simples disponível em
                        <strong>/inscricao/{slug-do-curso}</strong>.
                    </p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-end gap-4">
                <button type="submit"
                    class="px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white font-black text-sm rounded-2xl shadow-lg shadow-orange-600/20 transition-all hover:-translate-y-1">
                    Salvar Configurações
                </button>
            </div>
        </form>
    </div>
@endsection
