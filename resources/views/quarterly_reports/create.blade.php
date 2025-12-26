@extends('layouts.app')

@section('title', 'Novo Relatório Trimestral')
@section('page-title', 'Submeter Relatório')
@section('page-subtitle', 'Preencha as estatísticas e indicadores do trimestre')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('quarterly-reports.index') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
        </div>

        <form action="{{ route('quarterly-reports.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Coluna Principal -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Identificação -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-info-circle mr-3 text-blue-600"></i> Identificação do Período
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Zona *</label>
                                <select name="zone_id" required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione a zona</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ano *</label>
                                <input type="number" name="year" value="{{ old('year', date('Y')) }}" required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Trimestre *</label>
                                <select name="quarter" required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1" {{ old('quarter') == 1 ? 'selected' : '' }}>1º Trimestre</option>
                                    <option value="2" {{ old('quarter') == 2 ? 'selected' : '' }}>2º Trimestre</option>
                                    <option value="3" {{ old('quarter') == 3 ? 'selected' : '' }}>3º Trimestre</option>
                                    <option value="4" {{ old('quarter') == 4 ? 'selected' : '' }}>4º Trimestre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas Numéricas -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-bar-chart mr-3 text-blue-600"></i> Estatísticas Organizacionais
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Líderes</label>
                                <input type="number" name="leaders_count" value="{{ old('leaders_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Células Ativas</label>
                                <input type="number" name="cells_count" value="{{ old('cells_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Timóteos</label>
                                <input type="number" name="timoteos_count" value="{{ old('timoteos_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Membros</label>
                                <input type="number" name="members_count" value="{{ old('members_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Participantes Médios</label>
                                <input type="number" name="participants_count" value="{{ old('participants_count', 0) }}"
                                    min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Almas Ganhas</label>
                                <input type="number" name="saved_count" value="{{ old('saved_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Resultados Ministeriais -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-trophy mr-3 text-blue-600"></i> Resultados Ministeriais
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Batismos Planejados</label>
                                <input type="number" name="planned_baptism_count"
                                    value="{{ old('planned_baptism_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Batismos Realizados</label>
                                <input type="number" name="baptized_count" value="{{ old('baptized_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Multiplicações de
                                    Célula</label>
                                <input type="number" name="cell_multiplications_count"
                                    value="{{ old('cell_multiplications_count', 0) }}" min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Células Fechadas</label>
                                <input type="number" name="closed_cells_count" value="{{ old('closed_cells_count', 0) }}"
                                    min="0"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Avaliação Qualitativa (Scores) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-star mr-3 text-blue-600"></i> Avaliação Qualitativa (1-10)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @php
                                $scores = [
                                    'discipleship_score' => 'Discipulado',
                                    'pastoral_score' => 'Trabalho Pastoral',
                                    'cell_participation_score' => 'Participação nas Células',
                                    'service_participation_score' => 'Participação nos Cultos',
                                    'communion_in_cells_score' => 'Comunhão nas Células',
                                    'relationship_building_score' => 'Relacionamento',
                                    'prayer_intercession_score' => 'Oração e Intercessão'
                                ];
                            @endphp
                            @foreach($scores as $field => $label)
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="text-sm font-semibold text-gray-700">{{ $label }}</label>
                                        <span id="val_{{ $field }}" class="text-sm font-bold text-blue-600">5</span>
                                    </div>
                                    <input type="range" name="{{ $field }}" min="1" max="10" value="{{ old($field, 5) }}"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                        oninput="document.getElementById('val_{{ $field }}').textContent = this.value">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-8">
                    <!-- Eventos e Cerimônias -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-calendar-check mr-3 text-blue-600"></i> Eventos e Cerimônias
                        </h4>
                        <div class="space-y-4">
                            @foreach($eventTypes as $index => $type)
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">{{ $type->name }}</label>
                                    <input type="hidden" name="events[{{ $index }}][event_type_id]" value="{{ $type->id }}">
                                    <div class="flex gap-2">
                                        <input type="number" name="events[{{ $index }}][count]"
                                            value="{{ old("events.{$index}.count", 0) }}" min="0" placeholder="Qtd"
                                            class="w-20 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-center font-bold">
                                        <input type="text" name="events[{{ $index }}][description]"
                                            value="{{ old("events.{$index}.description") }}" placeholder="Notas..."
                                            class="flex-1 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Observações Finais -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-4">Observações Ministeriais</h4>
                        <textarea name="ministerial_observations" rows="6" placeholder="Pontos fortes, fracos, desafios..."
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('ministerial_observations') }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-1">
                        <i class="bi bi-send mr-2"></i> SUBMETER RELATÓRIO
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection