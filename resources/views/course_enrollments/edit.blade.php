@extends('layouts.app')

@section('title', 'Editar Matrícula - Portal Life Church')
@section('page-title', 'Editar Matrícula')
@section('page-subtitle', 'Dados do Casal e Avaliação do Curso')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        @if($enrollment->course_class_id)
            <a href="{{ route('course-classes.show', $enrollment->course_class_id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Turma</span>
            </a>
        @else
            <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Cursos</span>
            </a>
        @endif
        <a href="{{ route('course-enrollments.show', $enrollment) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">
            <i class="bi bi-eye"></i>
            <span>Ver Matrícula</span>
        </a>
    </div>

    <form action="{{ route('course-enrollments.update', $enrollment) }}" method="POST" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Edição de Matrícula</p>
            <h2 class="text-sm font-bold text-gray-900">{{ $enrollment->course->name }}</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <!-- Dados do Casal -->
            <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-3 sm:p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-orange-700 mb-3">Informações dos Parceiros</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label for="male_partner_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Parceiro Masculino</label>
                        <input type="text" name="male_partner_name" id="male_partner_name"
                            value="{{ old('male_partner_name', $enrollment->malePartner->name ?? '') }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    </div>
                    <div>
                        <label for="female_partner_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Parceira Feminina</label>
                        <input type="text" name="female_partner_name" id="female_partner_name"
                            value="{{ old('female_partner_name', $enrollment->femalePartner->name ?? '') }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status da Matrícula *</label>
                    <select name="status" id="status" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="cursando" {{ old('status', $enrollment->status) == 'cursando' ? 'selected' : '' }}>Cursando</option>
                        <option value="aprovado" {{ old('status', $enrollment->status) == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                        <option value="reprovado" {{ old('status', $enrollment->status) == 'reprovado' ? 'selected' : '' }}>Reprovado</option>
                        <option value="desistente" {{ old('status', $enrollment->status) == 'desistente' ? 'selected' : '' }}>Desistente</option>
                    </select>
                </div>

                <div>
                    <label for="course_class_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Turma Atribuída</label>
                    <select name="course_class_id" id="course_class_id"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="">Nenhuma turma (Lista de espera)</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('course_class_id', $enrollment->course_class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} ({{ $class->status }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="wedding_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data do Casamento</label>
                    <input type="date" name="wedding_date" id="wedding_date"
                        value="{{ old('wedding_date', $enrollment->wedding_date ? $enrollment->wedding_date->format('Y-m-d') : '') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>

                <div>
                    <label for="engagement_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data do Noivado</label>
                    <input type="date" name="engagement_date" id="engagement_date"
                        value="{{ old('engagement_date', $enrollment->engagement_date ? $enrollment->engagement_date->format('Y-m-d') : '') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>

                <div class="md:col-span-2">
                    <label for="is_church_member" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Membro da Igreja?</label>
                    <select name="is_church_member" id="is_church_member"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="1" {{ old('is_church_member', $enrollment->is_church_member) ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ !old('is_church_member', $enrollment->is_church_member) ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('course-enrollments.show', $enrollment) }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Atualizar Matrícula</span>
            </button>
        </div>
    </form>
</div>
@endsection
