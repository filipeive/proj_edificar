@extends('layouts.app')

@section('title', 'Editar Inscrição')
@section('page-title', 'Editar Inscrição')
@section('page-subtitle', 'Atualizar dados do casal inscrito')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('couple-enrollments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Inscrições</span>
        </a>
        <a href="{{ route('couple-enrollments.show', $coupleEnrollment) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">
            <i class="bi bi-eye"></i>
            <span>Ver Detalhes</span>
        </a>
    </div>

    <form action="{{ route('couple-enrollments.update', $coupleEnrollment) }}" method="POST"
        class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Edição de Inscrição</p>
            <h2 class="text-sm font-bold text-gray-900">{{ $coupleEnrollment->husband_name }} & {{ $coupleEnrollment->wife_name }}</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <!-- Nomes e Contatos -->
            <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-3 sm:p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-orange-700 mb-3">Identificação do Casal</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome do Marido *</label>
                        <input type="text" name="husband_name" value="{{ old('husband_name', $coupleEnrollment->husband_name) }}" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white font-bold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome da Esposa *</label>
                        <input type="text" name="wife_name" value="{{ old('wife_name', $coupleEnrollment->wife_name) }}" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white font-bold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Contato do Parceiro</label>
                        <input type="text" name="husband_phone" value="{{ old('husband_phone', $coupleEnrollment->husband_phone) }}" placeholder="Ex: +258 84 000 0000"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Contato da Parceira</label>
                        <input type="text" name="wife_phone" value="{{ old('wife_phone', $coupleEnrollment->wife_phone) }}" placeholder="Ex: +258 84 000 0000"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    </div>
                </div>
            </div>

            <!-- Relacionamento e Endereços -->
            <div x-data="{ 
                relType: '{{ old('relationship_type', $coupleEnrollment->relationship_type) }}', 
                isMember: '{{ old('is_church_member', $coupleEnrollment->is_church_member) }}',
                zoneId: '{{ old('zone_id', $coupleEnrollment->zone_id) }}' || ( '{{ $coupleEnrollment->cell_zone }}' ? 'other' : '' )
            }" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Endereço (Geral / Marido) *</label>
                        <input type="text" name="address" value="{{ old('address', $coupleEnrollment->address) }}" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    </div>

                    <div x-show="relType === 'em_relacionamento' || relType === 'namoro' || relType === 'noivos'" x-transition>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Endereço da Esposa</label>
                        <input type="text" name="wife_address" value="{{ old('wife_address', $coupleEnrollment->wife_address) }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Tipo de Relacionamento *</label>
                        <select name="relationship_type" required x-model="relType"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                            @foreach(['em_relacionamento' => 'Em Relacionamento', 'noivos' => 'Noivos', 'vivendo_maritalmente' => 'Vivendo Maritalmente', 'casados' => 'Casados'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('relationship_type', $coupleEnrollment->relationship_type) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Anos Juntos *</label>
                        <input type="number" name="years_together" value="{{ old('years_together', $coupleEnrollment->years_together) }}" required min="0"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data de Casamento Prevista</label>
                        <input type="date" name="wedding_date" value="{{ old('wedding_date', $coupleEnrollment->wedding_date ? $coupleEnrollment->wedding_date->format('Y-m-d') : '') }}"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    </div>
                </div>

                <!-- Turma e Eclesiástico -->
                <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-3 sm:p-4 space-y-3">
                    <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Alocação de Turma & Eclesiástico</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Turma Alocada</label>
                            <select name="course_class_id"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                                <option value="">Sem Turma (Lista de Espera)</option>
                                @foreach($classes as $cClass)
                                    <option value="{{ $cClass->id }}" {{ old('course_class_id', $coupleEnrollment->course_class_id) == $cClass->id ? 'selected' : '' }}>
                                        {{ $cClass->name }} ({{ $cClass->course->name ?? 'Curso' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Membro da Igreja?</label>
                            <select name="is_church_member" x-model="isMember"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Zona de Pertença</label>
                            <select name="zone_id" x-model="zoneId"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                                <option value="">Selecione a Zona</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id', $coupleEnrollment->zone_id) == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                @endforeach
                                <option value="other">Outra / Não listada</option>
                            </select>
                        </div>

                        <div x-show="zoneId === 'other'" x-transition>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Zona de Célula (Especificar)</label>
                            <input type="text" name="cell_zone" value="{{ old('cell_zone', $coupleEnrollment->cell_zone) }}"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome do Líder</label>
                            <input type="text" name="leader_name" value="{{ old('leader_name', $coupleEnrollment->leader_name) }}"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        </div>

                        <div class="flex items-center gap-2 pt-4">
                            <input type="hidden" name="has_pastoral_recommendation" value="0">
                            <input type="checkbox" name="has_pastoral_recommendation" value="1" {{ old('has_pastoral_recommendation', $coupleEnrollment->has_pastoral_recommendation) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <label class="text-xs font-bold text-gray-700 cursor-pointer">Possui Recomendação Pastoral</label>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Observações</label>
                <textarea name="observations" rows="3"
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">{{ old('observations', $coupleEnrollment->observations) }}</textarea>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('couple-enrollments.index') }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
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
