@extends('layouts.auth')

@section('title', 'Inscrição - Curso de Casais')

@section('content')
    <div class="min-h-screen bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
        <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-2xl w-full space-y-8 relative z-10">
            <div class="text-center">
                <a href="{{ route('welcome') }}" class="inline-block mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto mx-auto">
                </a>
                <h2 class="text-4xl font-black text-white tracking-tighter">Inscrição: Curso de Casais</h2>
                <p class="mt-2 text-gray-400 font-medium uppercase tracking-widest text-xs">Portal Life Church -
                    Fortalecendo Famílias</p>
            </div>

            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 md:p-12 shadow-2xl">
                <form action="{{ route('public.courses.casais.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nome do Esposo -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nome
                                do Noivo / Parceiro</label>
                            <input type="text" name="husband_name" required
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                                placeholder="Nome completo">
                        </div>

                        <!-- Nome da Esposa -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nome
                                da Noiva / Parceira</label>
                            <input type="text" name="wife_name" required
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                                placeholder="Nome completo">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo de Relação -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Tipo
                                de Relação</label>
                            <select name="relationship_type" required
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition appearance-none">
                                <option value="" class="bg-gray-900">Selecione...</option>
                                <option value="namoro" class="bg-gray-900">Em relacionamento (Namoro)</option>
                                <option value="noivos" class="bg-gray-900">Noivos</option>
                                <option value="vivendo_maritalmente" class="bg-gray-900">Vivendo Maritalmente</option>
                                <option value="casados" class="bg-gray-900">Casados (Reciclagem)</option>
                            </select>
                        </div>

                        <!-- Anos de Relacionamento -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Anos
                                de Relacionamento</label>
                            <input type="number" name="years_together" required min="0"
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                                placeholder="Ex: 5">
                        </div>
                    </div>

                    <!-- Morada -->
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Morada</label>
                        <input type="text" name="address" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                            placeholder="Endereço completo">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contactos -->
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Contactos</label>
                            <input type="text" name="contacts" required
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                                placeholder="Telemóvel / WhatsApp">
                        </div>

                        <!-- Zona de Célula -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Zona
                                de Célula</label>
                            <input type="text" name="cell_zone"
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                                placeholder="Ex: Zona A">
                        </div>
                    </div>

                    <!-- Líder / Supervisor -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nome do
                            Líder de Célula / Supervisor</label>
                        <input type="text" name="leader_name"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                            placeholder="Nome do seu líder direto">
                    </div>

                    <!-- Recomendação Pastoral -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Tem
                            Recomendação da Pastoral?</label>
                        <div class="flex space-x-6">
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="radio" name="has_pastoral_recommendation" value="1" required
                                    class="w-5 h-5 text-orange-600 bg-white/5 border-white/10 focus:ring-orange-500">
                                <span class="text-gray-300 group-hover:text-white transition">Sim</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <input type="radio" name="has_pastoral_recommendation" value="0" required
                                    class="w-5 h-5 text-orange-600 bg-white/5 border-white/10 focus:ring-orange-500">
                                <span class="text-gray-300 group-hover:text-white transition">Não</span>
                            </label>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Observações</label>
                        <textarea name="observations" rows="3"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                            placeholder="Alguma informação adicional?"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-700 text-white font-black py-5 rounded-2xl uppercase tracking-widest text-sm shadow-2xl shadow-orange-500/20 hover:scale-[1.02] transition-all duration-300">
                            Finalizar Inscrição
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center">
                <a href="{{ route('welcome') }}"
                    class="text-gray-500 hover:text-white transition text-xs font-bold uppercase tracking-widest">
                    <i class="bi bi-arrow-left mr-2"></i> Voltar para o Início
                </a>
            </div>
        </div>
    </div>
@endsection