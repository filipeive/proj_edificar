@extends('layouts.app')

@section('title', 'Detalhes do Evento')
@section('page-title', 'Detalhes do Evento')
@section('page-subtitle', 'Informações completas sobre o culto ou evento')

@section('content')
    <div class="w-full">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
            <div class="flex space-x-2">
                <button onclick="toggleEmailModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                    <i class="bi bi-envelope mr-2"></i> Partilhar Email
                </button>
                <a href="{{ route('events.pdf', $event) }}"
                    class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                    <i class="bi bi-file-earmark-pdf mr-2"></i> Exportar PDF
                </a>
                @can('update', $event)
                    <a href="{{ route('events.edit', $event) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                        <i class="bi bi-pencil mr-2"></i> Editar
                    </a>
                @endcan
            </div>
        </div>

        <div class="w-full">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-orange-600 px-8 py-10 text-white relative">
                    <div class="relative z-10">
                        <p class="text-orange-200 text-sm font-bold uppercase tracking-widest mb-2">
                            {{ $event->eventType->name }}</p>
                        <h3 class="text-4xl font-black mb-4">{{ $event->name ?? $event->eventType->name }}</h3>
                        <div class="flex flex-wrap gap-6 text-sm">
                            <span class="flex items-center"><i class="bi bi-calendar3 mr-2"></i>
                                {{ $event->date->format('d/m/Y') }}</span>
                            <span class="flex items-center"><i class="bi bi-geo-alt mr-2"></i>
                                {{ $event->location ?? 'Sede' }}</span>
                            <span class="flex items-center"><i class="bi bi-diagram-3 mr-2"></i>
                                {{ $event->zone->name ?? 'Geral' }}</span>
                        </div>
                    </div>
                    <i class="bi bi-calendar-check absolute right-8 bottom-4 text-8xl text-white opacity-10"></i>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Descrição</h4>
                            <p class="text-lg text-gray-800">{{ $event->description ?? 'Sem descrição adicional.' }}</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 text-center">Participação
                        </h4>
                        <div class="text-center">
                            <p class="text-5xl font-black text-orange-600">{{ $event->participants_count }}</p>
                            <p class="text-xs text-gray-500 font-bold uppercase mt-2">Pessoas Presentes</p>
                        </div>
                    </div>
                </div>

                @if($event->observations)
                    <div class="px-8 pb-8">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Observações / Relato</h4>
                        <div
                            class="bg-gray-50 p-6 rounded-xl text-gray-700 leading-relaxed border-l-4 border-orange-500 italic">
                            {!! nl2br(e($event->observations)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Email Modal -->
    <div id="emailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
            <div class="bg-blue-600 p-6 text-white">
                <h4 class="text-xl font-bold flex items-center">
                    <i class="bi bi-envelope-paper mr-3"></i> Enviar Relatório
                </h4>
            </div>
            <form action="{{ route('events.email', $event) }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Endereço de Email</label>
                    <input type="email" name="email" required placeholder="exemplo@email.com"
                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 p-4">
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="toggleEmailModal()" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition">
                        CANCELAR
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                        ENVIAR AGORA
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