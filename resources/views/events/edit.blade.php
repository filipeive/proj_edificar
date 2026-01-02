@extends('layouts.app')

@section('title', 'Editar Evento')
@section('page-title', 'Editar Evento')
@section('page-subtitle', 'Atualize os dados do evento ou cerimônia')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
        </div>

        <div class="max-w-4xl mx-auto">
            <form action="{{ route('events.update', $event) }}" method="POST"
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @csrf
                @method('PUT')

                <div class="p-8 space-y-8">
                    <h4 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center">
                        <i class="bi bi-pencil-square mr-3 text-blue-600"></i> Dados do Evento
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Evento *</label>
                            <select name="event_type_id" required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione o tipo</option>
                                @foreach($eventTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('event_type_id', $event->event_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('event_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nome do Evento *</label>
                            <input type="text" name="name" value="{{ old('name', $event->name) }}" required
                                placeholder="Ex: Culto da Virada"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Início *</label>
                            <input type="date" name="date" value="{{ old('date', $event->date->format('Y-m-d')) }}" required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Término (Opcional)</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d') : '') }}"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-400 mt-1">Preencha apenas se o evento durar mais de um dia.</p>
                            @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Zona (Opcional)</label>
                            <select name="zone_id"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Nenhuma / Geral</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id', $event->zone_id) == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Célula (Opcional)</label>
                            <select name="cell_id"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Nenhuma / Geral</option>
                                @foreach($cells as $cell)
                                    <option value="{{ $cell->id }}" {{ old('cell_id', $event->cell_id) == $cell->id ? 'selected' : '' }}>
                                        {{ $cell->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Local</label>
                            <input type="text" name="location" value="{{ old('location', $event->location) }}"
                                placeholder="Ex: Templo Central"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Qtd. Participantes *</label>
                            <input type="number" name="participants_count" value="{{ old('participants_count', $event->participants_count) }}"
                                min="0" required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-center font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Descrição / Resumo</label>
                        <textarea name="description" rows="3" placeholder="Breve descrição do evento..."
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $event->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Observações Adicionais</label>
                        <textarea name="observations" rows="3" placeholder="Notas extras..."
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('observations', $event->observations) }}</textarea>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg transition-all transform hover:-translate-y-1">
                        <i class="bi bi-check-lg mr-2"></i> ATUALIZAR EVENTO
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
