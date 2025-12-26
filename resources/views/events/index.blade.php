@extends('layouts.app')

@section('title', 'Eventos e Cerimônias')
@section('page-title', 'Eventos e Cerimônias')
@section('page-subtitle', 'Gestão de batismos, casamentos e eventos especiais')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-gray-800">Lista de Eventos</h3>
            @can('create', App\Models\Event::class)
                <a href="{{ route('events.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md">
                    <i class="bi bi-calendar-plus mr-2"></i> Novo Evento
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
                <i class="bi bi-check-circle-fill mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Data</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Tipo</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Local / Âmbito</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Participantes</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($events as $event)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                                    {{ $event->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $event->eventType->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($event->cell)
                                        <span class="font-bold text-gray-800">Célula:</span> {{ $event->cell->name }}
                                    @elseif($event->zone)
                                        <span class="font-bold text-gray-800">Zona:</span> {{ $event->zone->name }}
                                    @else
                                        <span class="font-bold text-gray-800">Geral:</span> {{ $event->location ?? 'N/A' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-center font-bold text-gray-800">
                                    {{ $event->participants_count }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('events.show', $event) }}"
                                            class="text-blue-600 hover:text-blue-800 p-1" title="Ver Detalhes">
                                            <i class="bi bi-eye text-lg"></i>
                                        </a>
                                        @can('update', $event)
                                            <a href="{{ route('events.edit', $event) }}"
                                                class="text-yellow-600 hover:text-yellow-800 p-1" title="Editar">
                                                <i class="bi bi-pencil text-lg"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $event)
                                            <form action="{{ route('events.destroy', $event) }}" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este evento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Excluir">
                                                    <i class="bi bi-trash text-lg"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    Nenhum evento registrado até o momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($events->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection