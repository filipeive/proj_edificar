@extends('layouts.app')

@section('title', 'Configuração de Formulários Públicos')
@section('page-title', 'Formulários Públicos')
@section('page-subtitle', 'Defina os cursos de destino para inscrições externas')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
        <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Integração Externa</p>
        <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">Configuração de Formulários Públicos</h1>
        <p class="text-xs text-gray-500 mt-0.5">Gerencie para quais cursos do sistema as inscrições públicas são destinadas.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-bold text-emerald-800 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('settings.public-forms.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Pre-Marital Course Configuration -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50/60 p-4 sm:p-5 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-100 text-orange-700 font-bold">
                    <i class="bi bi-heart-fill text-sm"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Curso Pré-Marital / Casais</h2>
                    <p class="text-xs text-gray-500">Destino das inscrições públicas de casais</p>
                </div>
            </div>

            <div class="p-4 sm:p-5 space-y-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Curso de Destino</label>
                    <select name="pre_marital_course_id"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="" disabled {{ !$preMaritalCourseId ? 'selected' : '' }}>Selecione o curso para receber inscrições...</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $preMaritalCourseId == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pre_marital_course_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <p class="text-[11px] text-gray-400">
                    <i class="bi bi-info-circle mr-1"></i>
                    Todas as novas inscrições feitas no formulário público <strong>/inscricao-pre-marital</strong> serão vinculadas a este curso.
                </p>
            </div>
        </div>

        <!-- Ministerial Courses Configuration -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50/60 p-4 sm:p-5 flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold">
                    <i class="bi bi-mortarboard-fill text-sm"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Cursos Ministeriais</h2>
                    <p class="text-xs text-gray-500">Inscrição individual pública habilitada</p>
                </div>
            </div>

            <div class="p-4 sm:p-5 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($courses->whereNotIn('id', [$preMaritalCourseId]) as $course)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-gray-50/50 hover:bg-orange-50/30 hover:border-orange-200 transition cursor-pointer">
                            <input type="checkbox" name="ministerial_course_ids[]" value="{{ $course->id }}"
                                class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                                {{ in_array($course->id, $ministerialCourseIds) ? 'checked' : '' }}>
                            <div>
                                <span class="block text-xs font-bold text-gray-900">{{ $course->name }}</span>
                                <span class="block text-[10px] text-gray-400 uppercase font-medium">{{ $course->category }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                <p class="text-[11px] text-gray-400 pt-1">
                    <i class="bi bi-info-circle mr-1"></i>
                    Cursos selecionados terão o formulário simples disponível em <strong>/inscricao/{slug-do-curso}</strong>.
                </p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Salvar Configurações</span>
            </button>
        </div>
    </form>
</div>
@endsection
