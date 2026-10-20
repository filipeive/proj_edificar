@extends('layouts.app')

@section('title', 'Detalhes do Casamento')
@section('page-title', 'Detalhes do Casamento')
@section('page-subtitle', 'Informações completas sobre o agendamento matrimonial')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('weddings.pdf', ['id' => $wedding->id]) }}" target="_blank"
            class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50" title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
            <a href="{{ route('weddings.edit', $wedding) }}"
                class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
<div class="space-y-5" x-data="{ showCompleteModal: false }">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <a href="{{ route('weddings.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Casamentos</span>
        </a>

        <div class="flex items-center gap-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                @if($wedding->status !== 'completed')
                    <button type="button" @click="showCompleteModal = true"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-800 transition">
                        <i class="bi bi-check-circle"></i>
                        <span>Marcar como Realizado</span>
                    </button>
                @else
                    <button type="button" @click="showCompleteModal = true"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                        <i class="bi bi-pencil-square text-emerald-600"></i>
                        <span>Editar Observações</span>
                    </button>
                @endif

                <a href="{{ route('weddings.edit', $wedding) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                    <i class="bi bi-pencil"></i>
                    <span>Editar</span>
                </a>
            @endif

            <a href="{{ route('weddings.pdf', ['id' => $wedding->id]) }}" target="_blank"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-file-earmark-pdf text-red-600"></i>
                <span>PDF</span>
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50/60 p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">Cerimônia Matrimonial</span>
                    @if($wedding->status === 'completed')
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Realizado</span>
                    @elseif($wedding->status === 'cancelled')
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-800">Cancelado</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">Agendado</span>
                    @endif
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">{{ $wedding->groom_name }} & {{ $wedding->bride_name }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600 mt-2">
                    <span class="flex items-center gap-1.5">
                        <i class="bi bi-calendar3 text-gray-400"></i>
                        {{ $wedding->date->format('d/m/Y') }} ({{ ucfirst($wedding->date->translatedFormat('l')) }})
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="bi bi-clock text-gray-400"></i>
                        {{ $wedding->time ? \Carbon\Carbon::parse($wedding->time)->format('H:i') : 'Hora não definida' }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="bi bi-geo-alt text-gray-400"></i>
                        {{ $wedding->location ?? 'Templo Sede' }}
                    </span>
                </div>
            </div>

            <div class="text-xs text-gray-400 sm:text-right">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Registrado em</p>
                <p class="font-bold text-gray-700">{{ $wedding->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Padrinhos / Testemunhas</p>
                <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-3 text-xs sm:text-sm text-gray-800 min-h-[80px]">
                    {{ $wedding->godparents ?? 'Nenhum padrinho/testemunha informado.' }}
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Observações Adicionais</p>
                    @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                        <button type="button" @click="showCompleteModal = true" class="text-[11px] font-bold text-orange-700 hover:underline">
                            Editar
                        </button>
                    @endif
                </div>
                <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-3 text-xs sm:text-sm text-gray-800 min-h-[80px] whitespace-pre-line">
                    {{ $wedding->observations ?? 'Sem observações adicionais para este agendamento.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Marcar como Realizado e Adicionar Observações -->
    <div x-show="showCompleteModal" 
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" 
         x-cloak>
        
        <div @click.away="showCompleteModal = false" 
             class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden">
            
            <div class="bg-gray-900 px-4 py-3 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle text-emerald-400"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider">Confirmar Realização</h3>
                </div>
                <button @click="showCompleteModal = false" class="text-gray-400 hover:text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('weddings.complete', $wedding) }}" method="POST" class="p-4 sm:p-5 space-y-4">
                @csrf
                @method('PATCH')

                <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-100 text-xs text-emerald-900 leading-relaxed">
                    Você está marcando o casamento de <span class="font-bold text-emerald-950">{{ $wedding->groom_name }} & {{ $wedding->bride_name }}</span> como <span class="uppercase font-bold text-emerald-800">Realizado</span>.
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Observações / Notas do Evento
                    </label>
                    <textarea name="observations" rows="4"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="Insira detalhes ou observações finais sobre a cerimônia (opcional)...">{{ old('observations', $wedding->observations) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showCompleteModal = false"
                        class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-800 transition">
                        <i class="bi bi-check2-circle"></i>
                        <span>Confirmar Realização</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
