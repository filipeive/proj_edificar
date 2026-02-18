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

            <h3
                class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">
                Dados do Casal
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Nome do Marido</label>
                    <input type="text" name="husband_name"
                        value="{{ old('husband_name', $coupleEnrollment->husband_name) }}" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Nome da Esposa</label>
                    <input type="text" name="wife_name" value="{{ old('wife_name', $coupleEnrollment->wife_name) }}"
                        required
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Contacto do
                        Parceiro</label>
                    <input type="text" name="husband_phone"
                        value="{{ old('husband_phone', $coupleEnrollment->husband_phone) }}"
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500"
                        placeholder="Ex: +258 84 000 0000">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Contacto da
                        Parceira</label>
                    <input type="text" name="wife_phone" value="{{ old('wife_phone', $coupleEnrollment->wife_phone) }}"
                        class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500"
                        placeholder="Ex: +258 84 000 0000">
                </div>
            </div>

            <div x-data="{ 
                    relType: '{{ old('relationship_type', $coupleEnrollment->relationship_type) }}', 
                    isMember: '{{ old('is_church_member', $coupleEnrollment->is_church_member) }}',
                    zoneId: '{{ old('zone_id', $coupleEnrollment->zone_id) }}' || ( '{{ $coupleEnrollment->cell_zone }}' ? 'other' : '' )
                }" class="space-y-8 mt-6">
                {{-- Address Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="space-y-3 p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="bi bi-geo-alt text-orange-500"></i>
                            <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Endereço (Geral /
                                Marido)</label>
                        </div>
                        <input type="text" name="address" value="{{ old('address', $coupleEnrollment->address) }}" required
                            class="w-full bg-white dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500 shadow-sm">
                    </div>

                    <div class="space-y-3 p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700"
                        x-show="relType === 'namoro' || relType === 'noivos'" x-transition>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="bi bi-geo-alt-fill text-pink-500"></i>
                            <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Endereço da
                                Esposa</label>
                        </div>
                        <input type="text" name="wife_address"
                            value="{{ old('wife_address', $coupleEnrollment->wife_address) }}"
                            class="w-full bg-white dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500 shadow-sm">
                    </div>

                    @if($coupleEnrollment->contacts)
                        <div class="space-y-2 md:col-span-2 opacity-60">
                            <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Contactos (legado:
                                {{ $coupleEnrollment->contacts }})</label>
                            <input type="hidden" name="contacts" value="{{ $coupleEnrollment->contacts }}">
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Tipo de
                            Relacionamento</label>
                        <select name="relationship_type" required x-model="relType"
                            class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">
                            @foreach(['namoro', 'noivos', 'vivendo_maritalmente', 'casados'] as $type)
                                <option value="{{ $type }}">
                                    {{ ucfirst(str_replace('_', ' ', $type === 'namoro' ? 'em relacionamento' : $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Tempo Juntos
                            (Anos)</label>
                        <input type="number" name="years_together"
                            value="{{ old('years_together', $coupleEnrollment->years_together) }}" required min="0" step="1"
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

                {{-- Church Membership Section --}}
                <div class="space-y-6">
                    <h3
                        class="text-sm font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 pb-2">
                        Pertença à Igreja
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center gap-3 p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl cursor-pointer border-2 transition-all"
                            :class="isMember == '1' ? 'border-orange-500 bg-orange-50/10' : 'border-transparent opacity-60'"
                            @click="isMember = '1'">
                            <input type="radio" name="is_church_member" value="1" x-model="isMember"
                                class="w-5 h-5 text-orange-600 rounded-full focus:ring-orange-500 border-gray-300">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Sim,
                                Membro</span>
                        </div>

                        <div class="flex items-center gap-3 p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl cursor-pointer border-2 transition-all"
                            :class="isMember == '0' ? 'border-gray-400 bg-gray-50/50' : 'border-transparent opacity-60'"
                            @click="isMember = '0'">
                            <input type="radio" name="is_church_member" value="0" x-model="isMember"
                                class="w-5 h-5 text-orange-600 rounded-full focus:ring-orange-500 border-gray-300">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Não é
                                Membro</span>
                        </div>
                    </div>

                    <div x-show="isMember == '1'" x-transition
                        class="space-y-6 bg-orange-50/5 p-6 rounded-[2rem] border border-orange-500/10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Zona de
                                    Pertença</label>
                                <select name="zone_id" x-model="zoneId"
                                    class="w-full bg-white dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500 shadow-sm">
                                    <option value="">Selecione a Zona</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ old('zone_id', $coupleEnrollment->zone_id) == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                    @endforeach
                                    <option value="other">Outra / Não listada</option>
                                </select>
                            </div>

                            <div class="space-y-2" x-show="zoneId === 'other'" x-transition>
                                <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Zona de Célula
                                    (Especificar)</label>
                                <input type="text" name="cell_zone"
                                    value="{{ old('cell_zone', $coupleEnrollment->cell_zone) }}"
                                    class="w-full bg-white dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500 shadow-sm">
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Nome do
                                    Líder</label>
                                <input type="text" name="leader_name"
                                    value="{{ old('leader_name', $coupleEnrollment->leader_name) }}"
                                    class="w-full bg-white dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500 shadow-sm">
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-3 p-5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl">
                            <input type="hidden" name="has_pastoral_recommendation" value="0">
                            <input type="checkbox" name="has_pastoral_recommendation" value="1" {{ old('has_pastoral_recommendation', $coupleEnrollment->has_pastoral_recommendation) ? 'checked' : '' }}
                                class="w-6 h-6 text-orange-600 rounded focus:ring-orange-500 border-gray-300">
                            <div class="flex flex-col">
                                <label
                                    class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Recomendação
                                    Pastoral?</label>
                                <span class="text-[9px] text-gray-500 uppercase font-medium">Possui recomendação da
                                    supervisão</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2 mt-6">
                <label class="text-[10px] uppercase font-bold text-gray-500 tracking-widest">Observações</label>
                <textarea name="observations" rows="3"
                    class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl font-bold text-gray-800 dark:text-gray-200 focus:ring-orange-500">{{ old('observations', $coupleEnrollment->observations) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('couple-enrollments.index') }}"
                    class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold uppercase text-xs rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-black uppercase text-xs rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition-all transform hover:-translate-y-0.5">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
@endsection