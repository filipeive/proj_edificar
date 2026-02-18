@extends('layouts.app')

@section('title', 'Editar Inscrição Ministerial')
@section('page-title', 'Editar Inscrição')
@section('page-subtitle', 'Atualizar dados do aluno inscrito')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <form action="{{ route('ministerial-enrollments.update', $ministerialEnrollment) }}" method="POST"
            class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700">
            @csrf
            @method('PUT')

            <h3
                class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">
                Dados do Aluno
            </h3>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Nome Completo</label>
                        <input type="text" name="full_name"
                            value="{{ old('full_name', $ministerialEnrollment->full_name) }}" required
                            class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-blue-500">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $ministerialEnrollment->email) }}" required
                            class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Telefone /
                            WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $ministerialEnrollment->phone) }}" required
                            class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-blue-500"
                            placeholder="Ex: +258 84 000 0000">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Curso</label>
                        <select name="course_id" required
                            class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-blue-500">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $ministerialEnrollment->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">É Membro da
                            Igreja?</label>
                        <select name="is_church_member" required
                            class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-blue-500">
                            <option value="1" {{ old('is_church_member', $ministerialEnrollment->is_church_member) ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ !old('is_church_member', $ministerialEnrollment->is_church_member) ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Célula</label>
                        <input type="text" name="cell_name"
                            value="{{ old('cell_name', $ministerialEnrollment->cell_name) }}"
                            class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-blue-500">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Status da Inscrição</label>
                    <select name="status" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-blue-500">
                        <option value="pending" {{ old('status', $ministerialEnrollment->status) === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="enrolled" {{ old('status', $ministerialEnrollment->status) === 'enrolled' ? 'selected' : '' }}>Matriculado</option>
                        <option value="completed" {{ old('status', $ministerialEnrollment->status) === 'completed' ? 'selected' : '' }}>Concluído</option>
                        <option value="rejected" {{ old('status', $ministerialEnrollment->status) === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Observações</label>
                    <textarea name="observations" rows="4"
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-blue-500 shadow-inner p-4">{{ old('observations', $ministerialEnrollment->observations) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('ministerial-enrollments.index') }}"
                    class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold uppercase text-xs rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-black uppercase text-xs rounded-xl hover:shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
@endsection