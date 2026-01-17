@extends('layouts.app')

@section('title', 'Editar Matrícula - Portal Life Church')
@section('page-title', 'Editar Matrícula')
@section('page-subtitle', 'Dados do Casal e Avaliação do Curso')

@section('content')
    <div class="container-fluid">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                @if($enrollment->course_class_id)
                    <a href="{{ route('course-classes.show', $enrollment->course_class_id) }}"
                        class="text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="bi bi-arrow-left mr-2"></i> Voltar para a turma
                    </a>
                @else
                    <a href="{{ route('courses.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="bi bi-arrow-left mr-2"></i> Voltar para cursos
                    </a>
                @endif
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8">
                    <form action="{{ route('course-enrollments.update', $enrollment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                            <h4 class="text-lg font-black text-gray-900 mb-4">Informações do Casal</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="male_partner_name"
                                        class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Parceiro
                                        Masculino</label>
                                    <input type="text" name="male_partner_name" id="male_partner_name"
                                        value="{{ old('male_partner_name', $enrollment->malePartner->name ?? '') }}"
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-700">
                                </div>
                                <div>
                                    <label for="female_partner_name"
                                        class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Parceiro
                                        Feminino</label>
                                    <input type="text" name="female_partner_name" id="female_partner_name"
                                        value="{{ old('female_partner_name', $enrollment->femalePartner->name ?? '') }}"
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-700">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="status" class="block text-sm font-bold text-gray-700 mb-2">Status da Matrícula
                                    *</label>
                                <select name="status" id="status" required
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="cursando" {{ old('status', $enrollment->status) == 'cursando' ? 'selected' : '' }}>Cursando</option>
                                    <option value="aprovado" {{ old('status', $enrollment->status) == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                                    <option value="reprovado" {{ old('status', $enrollment->status) == 'reprovado' ? 'selected' : '' }}>Reprovado</option>
                                    <option value="desistente" {{ old('status', $enrollment->status) == 'desistente' ? 'selected' : '' }}>Desistente</option>
                                </select>
                            </div>

                            <div>
                                <label for="course_class_id" class="block text-sm font-bold text-gray-700 mb-2">Turma
                                    Atribuída</label>
                                <select name="course_class_id" id="course_class_id"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Nenhuma turma (Lista de espera)</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('course_class_id', $enrollment->course_class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} ({{ $class->status }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="wedding_date" class="block text-sm font-bold text-gray-700 mb-2">Data do
                                    Casamento</label>
                                <input type="date" name="wedding_date" id="wedding_date"
                                    value="{{ old('wedding_date', $enrollment->wedding_date ? $enrollment->wedding_date->format('Y-m-d') : '') }}"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="engagement_date" class="block text-sm font-bold text-gray-700 mb-2">Data do
                                    Noivado</label>
                                <input type="date" name="engagement_date" id="engagement_date"
                                    value="{{ old('engagement_date', $enrollment->engagement_date ? $enrollment->engagement_date->format('Y-m-d') : '') }}"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="is_church_member" class="block text-sm font-bold text-gray-700 mb-2">Membro da
                                    Igreja?</label>
                                <select name="is_church_member" id="is_church_member"
                                    class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1" {{ old('is_church_member', $enrollment->is_church_member) ? 'selected' : '' }}>Sim</option>
                                    <option value="0" {{ !old('is_church_member', $enrollment->is_church_member) ? 'selected' : '' }}>Não</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-lg font-black text-gray-900 mb-4">Frequência</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="attendance_count" class="block text-sm font-bold text-gray-700 mb-2">Total
                                        de Presenças</label>
                                    <input type="number" name="attendance_count" id="attendance_count"
                                        value="{{ old('attendance_count', $enrollment->attendance_count) }}"
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="absence_count" class="block text-sm font-bold text-gray-700 mb-2">Total de
                                        Faltas</label>
                                    <input type="number" name="absence_count" id="absence_count"
                                        value="{{ old('absence_count', $enrollment->absence_count) }}"
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="absence_reasons" class="block text-sm font-bold text-gray-700 mb-2">Motivos
                                        das Faltas</label>
                                    <textarea name="absence_reasons" id="absence_reasons" rows="2"
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">{{ old('absence_reasons', $enrollment->absence_reasons) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-lg font-black text-gray-900 mb-4">Padrinhos e Avaliação</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="godparents_male"
                                        class="block text-sm font-bold text-gray-700 mb-2">Padrinhos (Ele)</label>
                                    <input type="text" name="godparents_male" id="godparents_male"
                                        value="{{ old('godparents_male', $enrollment->godparents_male) }}"
                                        placeholder="Ex: Tio João e Tia Maria"
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="godparents_female"
                                        class="block text-sm font-bold text-gray-700 mb-2">Padrinhos (Ela)</label>
                                    <input type="text" name="godparents_female" id="godparents_female"
                                        value="{{ old('godparents_female', $enrollment->godparents_female) }}"
                                        placeholder="Ex: Avô Pedro e Avó Ana"
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="recommendation"
                                        class="block text-sm font-bold text-gray-700 mb-2">Recomendação Final</label>
                                    <textarea name="recommendation" id="recommendation" rows="2"
                                        placeholder="Parecer dos professores sobre o casal..."
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">{{ old('recommendation', $enrollment->recommendation) }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">Observações
                                        Internas</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $enrollment->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ $enrollment->course_class_id ? route('course-classes.show', $enrollment->course_class_id) : route('courses.index') }}"
                                class="px-6 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition font-bold">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 font-bold">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection