@extends('layouts.app')

@section('title', 'Editar Casamento')
@section('page-title', 'Editar Casamento')
@section('page-subtitle', 'Atualizar detalhes do evento')

@section('content')
    <div class="w-full">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('weddings.index') }}"
                class="group flex items-center text-gray-500 hover:text-orange-600 transition-colors">
                <div
                    class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-200 flex items-center justify-center mr-3 group-hover:border-orange-200 group-hover:bg-orange-50 transition-all">
                    <i class="bi bi-arrow-left text-lg"></i>
                </div>
                <span class="font-bold text-sm">Voltar para o Calendário</span>
            </a>

            <button type="button" onclick="confirmDelete('delete-form', 'Deseja excluir este casamento?')"
                class="group flex items-center justify-center px-4 py-2 text-red-500 hover:text-white hover:bg-red-500 rounded-xl transition-all duration-300 font-bold text-xs uppercase tracking-wider border border-red-100 hover:border-red-500">
                <i class="bi bi-trash3-fill mr-2"></i> Remover
            </button>
        </div>

        <div
            class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-100/50 border border-gray-100 relative overflow-hidden">
            <!-- Decorative Header -->
            <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-r from-orange-500 to-amber-500 opacity-10"></div>
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-orange-500 rounded-full blur-[100px] opacity-10 -mr-20 -mt-20 pointer-events-none">
            </div>

            <div class="relative z-10 p-10 md:p-12">
                <div class="flex items-end justify-between mb-12">
                    <div>
                        <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase leading-none mb-2">
                            Editar <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-amber-500">Casamento</span>
                        </h2>
                        <p class="text-gray-400 font-bold tracking-widest text-xs uppercase">Atualize os detalhes da
                            cerimônia</p>
                    </div>
                    <div class="hidden md:block">
                        <div
                            class="w-16 h-16 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500 text-2xl shadow-inner">
                            <i class="bi bi-pencil-fill"></i>
                        </div>
                    </div>
                </div>

                <form action="{{ route('weddings.update', $wedding) }}" method="POST" class="space-y-10">
                    @csrf
                    @method('PUT')

                    <!-- Couple Section -->
                    <div
                        class="bg-gray-50/50 rounded-[2rem] p-8 border border-gray-100 hover:border-orange-100 transition-colors duration-300">
                        <h3 class="text-orange-600 font-black uppercase tracking-widest text-xs mb-8 flex items-center">
                            <span class="w-8 h-px bg-orange-200 mr-3"></span> O Casal
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="group">
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-3 ml-1 group-focus-within:text-orange-500 transition-colors">Nome
                                    do Noivo *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="bi bi-gender-male text-gray-300 group-focus-within:text-orange-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="groom_name"
                                        value="{{ old('groom_name', $wedding->groom_name) }}" required
                                        class="w-full bg-white rounded-xl border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 py-4 pl-11 pr-4 font-bold text-gray-800 placeholder-gray-300 shadow-sm">
                                </div>
                            </div>
                            <div class="group">
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-3 ml-1 group-focus-within:text-orange-500 transition-colors">Nome
                                    da Noiva *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="bi bi-gender-female text-gray-300 group-focus-within:text-orange-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="bride_name"
                                        value="{{ old('bride_name', $wedding->bride_name) }}" required
                                        class="w-full bg-white rounded-xl border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 py-4 pl-11 pr-4 font-bold text-gray-800 placeholder-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-8">
                            <div class="group">
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-3 ml-1 group-focus-within:text-orange-500 transition-colors">Data
                                    e Hora *</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="date" name="date"
                                        value="{{ old('date', $wedding->date->format('Y-m-d')) }}" required
                                        class="w-full bg-white rounded-xl border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 py-4 px-4 font-bold text-gray-800 shadow-sm">
                                    <input type="time" name="time"
                                        value="{{ old('time', $wedding->time ? $wedding->time->format('H:i') : '') }}"
                                        class="w-full bg-white rounded-xl border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 py-4 px-4 font-bold text-gray-800 shadow-sm">
                                </div>
                            </div>

                            <div class="group">
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-3 ml-1 group-focus-within:text-orange-500 transition-colors">Status</label>
                                <select name="status"
                                    class="w-full bg-white rounded-xl border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 py-4 px-4 font-bold text-gray-800 shadow-sm appearance-none custom-select">
                                    <option value="scheduled" {{ $wedding->status == 'scheduled' ? 'selected' : '' }}>Agendado
                                    </option>
                                    <option value="completed" {{ $wedding->status == 'completed' ? 'selected' : '' }}>
                                        Realizado</option>
                                    <option value="cancelled" {{ $wedding->status == 'cancelled' ? 'selected' : '' }}>
                                        Cancelado</option>
                                </select>
                            </div>

                            <div class="group">
                                <label
                                    class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-3 ml-1 group-focus-within:text-orange-500 transition-colors">Local
                                    da Cerimônia</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="bi bi-geo-alt-fill text-gray-300 group-focus-within:text-orange-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="location" value="{{ old('location', $wedding->location) }}"
                                        class="w-full bg-white rounded-xl border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 py-4 pl-11 pr-4 font-bold text-gray-800 placeholder-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div class="group h-full flex flex-col">
                            <label
                                class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-3 ml-1 group-focus-within:text-orange-500 transition-colors">Padrinhos</label>
                            <textarea name="godparents"
                                class="w-full bg-white rounded-xl border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 py-4 px-4 font-medium text-gray-600 placeholder-gray-300 resize-none shadow-sm flex-1 min-h-[140px]">{{ old('godparents', $wedding->godparents) }}</textarea>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="group">
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-3 ml-1 group-focus-within:text-orange-500 transition-colors">Observações
                            Adicionais</label>
                        <textarea name="observations" rows="3"
                            class="w-full bg-white rounded-xl border-gray-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all duration-300 py-4 px-4 font-medium text-gray-600 placeholder-gray-300 resize-none shadow-sm">{{ old('observations', $wedding->observations) }}</textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end pt-8 border-t border-gray-100">
                        <button type="button" onclick="window.history.back()"
                            class="mr-6 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-wider">Cancelar</button>
                        <button type="submit"
                            class="group relative inline-flex items-center justify-center px-10 py-4 font-black text-white transition-all duration-200 bg-gray-900 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 hover:bg-black hover:-translate-y-1 shadow-xl shadow-gray-900/30">
                            <span class="mr-2">Salvar Alterações</span>
                            <i class="bi bi-check-lg transition-transform group-hover:scale-110"></i>
                        </button>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('weddings.destroy', $wedding) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
@endsection