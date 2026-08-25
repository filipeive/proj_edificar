@extends('layouts.app')

@section('title', 'Detalhes do Casamento')
@section('page-title', 'Detalhes do Casamento')
@section('page-subtitle', 'Informações completas sobre o agendamento matrimonial')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('weddings.pdf', ['id' => $wedding->id]) }}"
            class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50"
            title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
            <a href="{{ route('weddings.edit', $wedding) }}"
                class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
                title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endif
        <a href="{{ route('weddings.index') }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Voltar à lista">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="w-full" x-data="{ showCompleteModal: false }">
        <!-- Header / Actions Bar -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <a href="{{ route('weddings.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center font-bold text-sm transition">
                <i class="bi bi-arrow-left mr-2 text-lg"></i> Voltar para Lista
            </a>

            <div class="flex items-center gap-3">
                @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                    @if($wedding->status !== 'completed')
                        <button type="button" @click="showCompleteModal = true"
                            class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-600/20 transition-all gap-2 cursor-pointer">
                            <i class="bi bi-check-circle-fill text-sm"></i> Marcar como Realizado
                        </button>
                    @else
                        <button type="button" @click="showCompleteModal = true"
                            class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-all gap-2 border border-gray-200 cursor-pointer">
                            <i class="bi bi-pencil-square text-sm text-emerald-600"></i> Editar Observações
                        </button>
                    @endif

                    <a href="{{ route('weddings.edit', $wedding) }}"
                        class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 hover:bg-blue-50 text-gray-700 hover:text-blue-600 font-bold text-xs uppercase tracking-wider rounded-xl transition-all gap-2 shadow-sm">
                        <i class="bi bi-pencil-fill text-sm"></i> Editar
                    </a>
                @endif

                <a href="{{ route('weddings.pdf', ['id' => $wedding->id]) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 hover:bg-orange-50 text-gray-700 hover:text-orange-600 font-bold text-xs uppercase tracking-wider rounded-xl transition-all gap-2 shadow-sm"
                    title="Exportar PDF">
                    <i class="bi bi-file-earmark-pdf-fill text-sm text-red-500"></i> PDF
                </a>
            </div>
        </div>

        <div class="w-full">
            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                <!-- Banner Header -->
                <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-8 py-10 text-white relative">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-orange-100 text-xs font-black uppercase tracking-widest bg-white/20 px-3 py-1 rounded-lg backdrop-blur-md">
                                Cerimônia Matrimonial
                            </span>
                            @if($wedding->status === 'completed')
                                <span class="text-emerald-100 text-xs font-black uppercase tracking-widest bg-emerald-600/80 px-3 py-1 rounded-lg backdrop-blur-md flex items-center gap-1">
                                    <i class="bi bi-check-circle-fill"></i> Realizado
                                </span>
                            @elseif($wedding->status === 'cancelled')
                                <span class="text-red-100 text-xs font-black uppercase tracking-widest bg-red-600/80 px-3 py-1 rounded-lg backdrop-blur-md flex items-center gap-1">
                                    <i class="bi bi-x-circle-fill"></i> Cancelado
                                </span>
                            @else
                                <span class="text-amber-100 text-xs font-black uppercase tracking-widest bg-amber-600/80 px-3 py-1 rounded-lg backdrop-blur-md flex items-center gap-1">
                                    <i class="bi bi-clock-history"></i> Agendado
                                </span>
                            @endif
                        </div>
                        <h3 class="text-4xl font-black mb-4 tracking-tight">{{ $wedding->groom_name }} & {{ $wedding->bride_name }}</h3>
                        <div class="flex flex-wrap gap-6 text-sm font-semibold text-orange-50">
                            <span class="flex items-center"><i class="bi bi-calendar3 mr-2 text-white"></i>
                                {{ $wedding->date->format('d/m/Y') }} ({{ ucfirst($wedding->date->translatedFormat('l')) }})</span>
                            <span class="flex items-center"><i class="bi bi-clock mr-2 text-white"></i>
                                {{ $wedding->time ? \Carbon\Carbon::parse($wedding->time)->format('H:i') : 'Hora não definida' }}</span>
                            <span class="flex items-center"><i class="bi bi-geo-alt mr-2 text-white"></i>
                                {{ $wedding->location ?? 'Templo Sede' }}</span>
                        </div>
                    </div>
                    <i class="bi bi-heart-fill absolute right-8 bottom-4 text-9xl text-white opacity-10 pointer-events-none"></i>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="bi bi-people-fill text-orange-500"></i> Padrinhos / Testemunhas
                            </h4>
                            <div class="bg-gray-50/80 p-6 rounded-2xl border border-gray-100 min-h-[100px]">
                                <p class="text-gray-800 leading-relaxed font-medium">
                                    {{ $wedding->godparents ?? 'Nenhum padrinho/testemunha informado.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                    <i class="bi bi-journal-text text-orange-500"></i> Observações Adicionais
                                </h4>
                                @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                                    <button type="button" @click="showCompleteModal = true" class="text-xs font-bold text-orange-600 hover:text-orange-800 transition cursor-pointer">
                                        <i class="bi bi-pencil-square mr-1"></i> Editar
                                    </button>
                                @endif
                            </div>
                            <div class="bg-gray-50/80 p-6 rounded-2xl border border-gray-100 min-h-[100px]">
                                <p class="text-gray-800 leading-relaxed font-medium whitespace-pre-line">
                                    {{ $wedding->observations ?? 'Sem observações adicionais para este agendamento.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 px-8 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                        Registrado em: {{ $wedding->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="flex items-center gap-2">
                        @if($wedding->status === 'completed')
                            <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-xl text-xs font-black uppercase tracking-widest border border-emerald-200 flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-sm"></i> Realizado
                            </span>
                        @elseif($wedding->status === 'cancelled')
                            <span class="px-4 py-1.5 bg-red-100 text-red-700 rounded-xl text-xs font-black uppercase tracking-widest border border-red-200 flex items-center gap-1.5">
                                <i class="bi bi-x-circle-fill text-sm"></i> Cancelado
                            </span>
                        @else
                            <span class="px-4 py-1.5 bg-orange-100 text-orange-700 rounded-xl text-xs font-black uppercase tracking-widest border border-orange-200 flex items-center gap-1.5">
                                <i class="bi bi-clock-history text-sm"></i> Agendado
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Marcar como Realizado e Adicionar Observações -->
        <div x-show="showCompleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" 
             x-cloak>
            
            <div @click.away="showCompleteModal = false" 
                 class="bg-white rounded-3xl shadow-2xl border border-gray-100 max-w-lg w-full p-6 md:p-8 relative overflow-hidden">
                
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-xl">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 leading-tight">Marcar como Realizado</h3>
                            <p class="text-xs font-medium text-gray-400">Confirmar a realização do casamento</p>
                        </div>
                    </div>
                    <button @click="showCompleteModal = false" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-100 transition">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('weddings.complete', $wedding) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100">
                        <p class="text-xs text-emerald-900 font-bold leading-relaxed">
                            Você está marcando o casamento de <span class="font-black text-emerald-950">{{ $wedding->groom_name }} & {{ $wedding->bride_name }}</span> como <span class="uppercase font-black text-emerald-700">Realizado</span>.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2 ml-1">
                            Observações / Notas do Evento
                        </label>
                        <textarea name="observations" rows="4"
                            class="w-full bg-white rounded-2xl border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-300 p-4 font-medium text-gray-800 placeholder-gray-400 text-sm shadow-sm"
                            placeholder="Insira quaisquer detalhes ou observações finais sobre a cerimônia (opcional)...">{{ old('observations', $wedding->observations) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showCompleteModal = false"
                            class="px-5 py-3 text-xs font-bold text-gray-500 hover:text-gray-700 uppercase tracking-wider rounded-xl hover:bg-gray-100 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2 cursor-pointer">
                            <i class="bi bi-check2-circle text-base"></i> Salvar & Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
