@extends('layouts.auth')

@section('title', 'Inscrição - Curso de Casais')

@section('content')
    <div class="min-h-screen bg-gray-50 flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-5">
            <div class="text-center">
                <a href="{{ route('welcome') }}" class="inline-block mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto mx-auto">
                </a>
                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-orange-700">Portal Life Church • Fortalecendo Famílias</p>
                <h1 class="mt-1 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Inscrição: Curso de Casais</h1>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
                <form action="{{ route('public.courses.casais.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Nome do Noivo / Parceiro <span class="text-red-500">*</span></label>
                            <input type="text" name="husband_name" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                                placeholder="Nome completo">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Nome da Noiva / Parceira <span class="text-red-500">*</span></label>
                            <input type="text" name="wife_name" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                                placeholder="Nome completo">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Tipo de Relação <span class="text-red-500">*</span></label>
                            <select name="relationship_type" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                <option value="">Selecione...</option>
                                <option value="em_relacionamento">Em relacionamento</option>
                                <option value="noivos">Noivos</option>
                                <option value="vivendo_maritalmente">Vivendo Maritalmente</option>
                                <option value="casados">Casados (Reciclagem)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Anos de Relacionamento <span class="text-red-500">*</span></label>
                            <input type="number" name="years_together" required min="0"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                                placeholder="Ex: 5">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Morada <span class="text-red-500">*</span></label>
                        <input type="text" name="address" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                            placeholder="Endereço completo">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Contactos <span class="text-red-500">*</span></label>
                            <input type="text" name="contacts" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                                placeholder="Telemóvel / WhatsApp">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Zona de Célula</label>
                            <input type="text" name="cell_zone"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                                placeholder="Ex: Zona A">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Nome do Líder de Célula / Supervisor</label>
                        <input type="text" name="leader_name"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                            placeholder="Nome do seu líder direto">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-2">Tem Recomendação da Pastoral? <span class="text-red-500">*</span></label>
                        <div class="flex space-x-5">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="has_pastoral_recommendation" value="1" required
                                    class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                <span class="text-xs font-bold text-gray-700">Sim</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="has_pastoral_recommendation" value="0" required
                                    class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                <span class="text-xs font-bold text-gray-700">Não</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Observações</label>
                        <textarea name="observations" rows="3"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                            placeholder="Alguma informação adicional?"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full rounded-lg bg-orange-700 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-orange-800 transition">
                            Finalizar Inscrição
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center">
                <a href="{{ route('welcome') }}"
                    class="text-gray-500 hover:text-gray-900 transition text-xs font-bold">
                    <i class="bi bi-arrow-left mr-1"></i> Voltar para o Início
                </a>
            </div>
        </div>
    </div>
@endsection
