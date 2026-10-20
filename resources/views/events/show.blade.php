@extends('layouts.app')

@section('title', 'Detalhes do Evento')
@section('page-title', 'Detalhes do Evento')
@section('page-subtitle', 'Informações completas sobre o culto ou evento')

@section('header-actions')
    <div class="flex items-center gap-2">
        <button onclick="toggleEmailModal()" class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Partilhar email">
            <i class="bi bi-envelope"></i>
        </button>
        <a href="{{ route('events.pdf', $event) }}" class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50" title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        @can('update', $event)
            <a href="{{ route('events.edit', $event) }}" class="action-icon text-gray-600 hover:text-yellow-600 hover:bg-yellow-50" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Eventos</span>
        </a>
        <div class="hidden sm:flex items-center gap-2">
            <button onclick="toggleEmailModal()" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-envelope text-blue-600"></i>
                <span>Partilhar Email</span>
            </button>
            <a href="{{ route('events.pdf', $event) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-file-earmark-pdf text-red-600"></i>
                <span>Exportar PDF</span>
            </a>
            @can('update', $event)
                <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                    <i class="bi bi-pencil"></i>
                    <span>Editar Evento</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Main Card -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50/60 p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">{{ $event->eventType->name }}</span>
                    @if($event->isPassed())
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">Realizado</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Agendado</span>
                    @endif
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">{{ $event->name ?? $event->eventType->name }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600 mt-2">
                    <span class="flex items-center gap-1.5">
                        <i class="bi bi-calendar3 text-gray-400"></i>
                        {{ $event->date->format('d/m/Y') }}
                        @if($event->end_date)
                            - {{ $event->end_date->format('d/m/Y') }}
                        @endif
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="bi bi-geo-alt text-gray-400"></i>
                        {{ $event->location ?? 'Sede' }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="bi bi-diagram-3 text-gray-400"></i>
                        {{ $event->zone->name ?? 'Geral' }}
                    </span>
                </div>
            </div>

            <div class="rounded-lg bg-orange-50 border border-orange-100 px-4 py-3 text-center sm:text-right">
                <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Participantes</p>
                <p class="text-2xl font-black text-orange-700">{{ $event->participants_count }}</p>
                <p class="text-[10px] text-orange-600 font-bold">Presentes</p>
            </div>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Descrição</p>
                <p class="text-xs sm:text-sm text-gray-800 bg-gray-50/50 p-3 rounded-lg border border-gray-100">
                    {{ $event->description ?? 'Sem descrição adicional.' }}
                </p>
            </div>

            @if($event->observations)
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Observações / Relato</p>
                    <div class="bg-amber-50/60 p-3 rounded-lg text-xs sm:text-sm text-gray-700 border-l-4 border-amber-500 italic">
                        {!! nl2br(e($event->observations)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Email Modal -->
<div id="emailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-200">
        <div class="bg-gray-900 px-4 py-3 text-white flex justify-between items-center">
            <h4 class="text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-envelope-paper text-orange-400"></i> Enviar Relatório por Email
            </h4>
            <button type="button" onclick="toggleEmailModal()" class="text-gray-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="{{ route('events.email', $event) }}" method="POST" class="p-4 sm:p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Endereço de Email</label>
                <input type="email" name="email" required placeholder="exemplo@email.com"
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="toggleEmailModal()" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                    <i class="bi bi-send"></i>
                    <span>Enviar Agora</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleEmailModal() {
        const modal = document.getElementById('emailModal');
        modal.classList.toggle('hidden');
    }
</script>
@endsection
