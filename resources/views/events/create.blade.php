@extends('layouts.app')

@section('title', 'Novo Evento')
@section('page-title', 'Registrar Evento')
@section('page-subtitle', 'Preencha os dados do evento ou cerimônia')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
        </div>
        <!-- ocupar toda area -->
        <div class="w-full">
            <form action="{{ route('events.store') }}" method="POST"
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @csrf

                <div class="p-8 space-y-8">
                    <h4 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center">
                        <i class="bi bi-calendar-event mr-3 text-blue-600"></i> Dados do Evento
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-semibold text-gray-700">Tipo de Evento <span
                                        class="text-red-500">*</span></label>
                                <a href="{{ route('event-types.index') }}"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-bold hover:underline transition">
                                    <i class="bi bi-gear-fill mr-1"></i> Gerir Tipos
                                </a>
                            </div>
                            <select name="event_type_id" required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-gray-900 bg-white">
                                <option value="" class="text-gray-500">Selecione o tipo ({{ $eventTypes->count() }}
                                    disponíveis)</option>
                                @foreach($eventTypes as $type)
                                    <option value="{{ $type->id }}" class="text-gray-900" {{ old('event_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nome do Evento <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Ex: Culto da Virada"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Início <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="date" value="{{ old('date', request('date', date('Y-m-d'))) }}"
                                required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Término (Opcional)</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
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
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Célula (Opcional)</label>
                            <select name="cell_id"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Nenhuma / Geral</option>
                                @foreach($cells as $cell)
                                    <option value="{{ $cell->id }}" {{ old('cell_id') == $cell->id ? 'selected' : '' }}>
                                        {{ $cell->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Local</label>
                            <input type="text" name="location" value="{{ old('location') }}"
                                placeholder="Ex: Templo Central"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Qtd. Participantes <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="participants_count" value="{{ old('participants_count', 0) }}"
                                min="0" required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-center font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Descrição / Resumo</label>
                        <textarea name="description" rows="3" placeholder="Breve descrição do evento..."
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Observações Adicionais</label>
                        <textarea name="observations" rows="3" placeholder="Notas extras..."
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('observations') }}</textarea>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg transition-all transform hover:-translate-y-1">
                        <i class="bi bi-check-lg mr-2"></i> SALVAR EVENTO
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection