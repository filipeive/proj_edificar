@extends('layouts.app')

@section('title', 'Editar Inscrição Ministerial')
@section('page-title', 'Editar Inscrição')
@section('page-subtitle', 'Atualizar dados do aluno inscrito')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('ministerial-enrollments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Inscrições</span>
        </a>
        <a href="{{ route('ministerial-enrollments.show', $ministerialEnrollment) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">
            <i class="bi bi-eye"></i>
            <span>Ver Detalhes</span>
        </a>
    </div>

    <form action="{{ route('ministerial-enrollments.update', $ministerialEnrollment) }}" method="POST"
        class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Edição de Inscrição</p>
            <h2 class="text-sm font-bold text-gray-900">{{ $ministerialEnrollment->full_name }}</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome Completo *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $ministerialEnrollment->full_name) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">E-mail *</label>
                    <input type="email" name="email" value="{{ old('email', $ministerialEnrollment->email) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Telefone / WhatsApp *</label>
                    <input type="text" name="phone" value="{{ old('phone', $ministerialEnrollment->phone) }}" required placeholder="Ex: +258 84 000 0000"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Curso *</label>
                    <select name="course_id" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $ministerialEnrollment->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">É Membro da Igreja? *</label>
                    <select name="is_church_member" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="1" {{ old('is_church_member', $ministerialEnrollment->is_church_member) ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ !old('is_church_member', $ministerialEnrollment->is_church_member) ? 'selected' : '' }}>Não</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Célula</label>
                    <input type="text" name="cell_name" value="{{ old('cell_name', $ministerialEnrollment->cell_name) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status da Inscrição *</label>
                    <select name="status" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="pending" {{ old('status', $ministerialEnrollment->status) === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="enrolled" {{ old('status', $ministerialEnrollment->status) === 'enrolled' ? 'selected' : '' }}>Matriculado</option>
                        <option value="completed" {{ old('status', $ministerialEnrollment->status) === 'completed' ? 'selected' : '' }}>Concluído</option>
                        <option value="rejected" {{ old('status', $ministerialEnrollment->status) === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Observações</label>
                <textarea name="observations" rows="3"
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">{{ old('observations', $ministerialEnrollment->observations) }}</textarea>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('ministerial-enrollments.index') }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Salvar Alterações</span>
            </button>
        </div>
    </form>
</div>
@endsection
