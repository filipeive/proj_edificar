@extends('layouts.app')

@section('title', 'Editar Inscrição')
@section('page-title', 'Editar Inscrição')
@section('page-subtitle', 'Atualizar dados do casal inscrito')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <form action="{{ route('couple-enrollments.update', $coupleEnrollment) }}" method="POST"
            class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700">
            @csrf
            @method('PUT')

            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">
                Dados do Casal
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Nome do Marido</label>
                    <input type="text" name="husband_name" value="{{ old('husband_name', $coupleEnrollment->husband_name) }}" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Nome da Esposa</label>
                    <input type="text" name="wife_name" value="{{ old('wife_name', $coupleEnrollment->wife_name) }}" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Contatos</label>
                    <input type="text" name="contacts" value="{{ old('contacts', $coupleEnrollment->contacts) }}" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Endereço</label>
                    <input type="text" name="address" value="{{ old('address', $coupleEnrollment->address) }}" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Tipo de Relacionamento</label>
                    <select name="relationship_type" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                        @foreach(['namoro', 'noivos', 'vivendo_maritalmente', 'casados'] as $type)
                            <option value="{{ $type }}" {{ old('relationship_type', $coupleEnrollment->relationship_type) == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Tempo Juntos (Anos)</label>
                    <input type="number" name="years_together" value="{{ old('years_together', $coupleEnrollment->years_together) }}" required min="0" step="1"
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Curso</label>
                    <select name="course_id" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $coupleEnrollment->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 mt-8 border-b border-gray-100 dark:border-gray-700 pb-2">
                Informações Eclesiásticas
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Zona / Célula</label>
                    <input type="text" name="cell_zone" value="{{ old('cell_zone', $coupleEnrollment->cell_zone) }}"
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Nome do Líder</label>
                    <input type="text" name="leader_name" value="{{ old('leader_name', $coupleEnrollment->leader_name) }}"
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                    <input type="hidden" name="is_church_member" value="0">
                    <input type="checkbox" name="is_church_member" value="1" {{ old('is_church_member', $coupleEnrollment->is_church_member) ? 'checked' : '' }}
                        class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500 border-gray-300">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Membro da Igreja?</label>
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                    <input type="hidden" name="has_pastoral_recommendation" value="0">
                    <input type="checkbox" name="has_pastoral_recommendation" value="1" {{ old('has_pastoral_recommendation', $coupleEnrollment->has_pastoral_recommendation) ? 'checked' : '' }}
                        class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500 border-gray-300">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Recomendação Pastoral?</label>
                </div>
            </div>

            <div class="space-y-2 mt-6">
                <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Observações</label>
                <textarea name="observations" rows="3"
                    class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">{{ old('observations', $coupleEnrollment->observations) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('couple-enrollments.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold uppercase text-xs rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-black uppercase text-xs rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition-all transform hover:-translate-y-0.5">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
@endsection
