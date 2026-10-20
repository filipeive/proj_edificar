@extends('layouts.app')

@section('title', 'Novo Evento')
@section('page-title', 'Registrar Evento')
@section('page-subtitle', 'Preencha os dados do evento ou cerimônia')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Eventos</span>
        </a>
        <a href="{{ route('event-types.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-gray-900 transition">
            <i class="bi bi-gear"></i>
            <span>Gerir Tipos</span>
        </a>
    </div>

    <form action="{{ route('events.store') }}" method="POST" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Formulário de Evento</p>
            <h2 class="text-sm font-bold text-gray-900">Dados do Evento</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Tipo de Evento <span class="text-red-500">*</span></label>
                        <a href="{{ route('event-types.create') }}" class="text-[11px] font-bold text-orange-700 hover:underline">
                            + Novo Tipo
                        </a>
                    </div>
                    <select name="event_type_id" required class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="">Selecione o tipo ({{ $eventTypes->count() }} disponíveis)</option>
                        @foreach($eventTypes as $type)
                            <option value="{{ $type->id }}" {{ old('event_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome do Evento <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Culto da Virada"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data de Início <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', request('date', date('Y-m-d'))) }}" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data de Término (Opcional)</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                    <p class="text-[11px] text-gray-400 mt-1">Preencha apenas se durar mais de um dia.</p>
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Zona (Opcional)</label>
                    <select name="zone_id" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="">Nenhuma / Geral</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Célula (Opcional)</label>
                    <select name="cell_id" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="">Nenhuma / Geral</option>
                        @foreach($cells as $cell)
                            <option value="{{ $cell->id }}" {{ old('cell_id') == $cell->id ? 'selected' : '' }}>
                                {{ $cell->name }} ({{ $cell->type_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Local</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="Ex: Templo Central"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Qtd. Participantes <span class="text-red-500">*</span></label>
                    <input type="number" name="participants_count" value="{{ old('participants_count', 0) }}" min="0" required
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm font-bold focus:border-orange-500 focus:ring-orange-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Descrição / Resumo</label>
                <textarea name="description" rows="2" placeholder="Breve descrição do evento..."
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Observações Adicionais</label>
                <textarea name="observations" rows="2" placeholder="Notas extras..."
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('observations') }}</textarea>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('events.index') }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Salvar Evento</span>
            </button>
        </div>
    </form>
</div>
@endsection
