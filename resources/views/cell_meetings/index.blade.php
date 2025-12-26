@extends('layouts.app')

@section('title', 'Encontros de Célula')
@section('page-title', 'Encontros de Célula')
@section('page-subtitle', 'Registro e acompanhamento das reuniões de células')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-gray-800">Lista de Encontros</h3>
            @can('create', App\Models\CellMeeting::class)
                <a href="{{ route('cell-meetings.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Encontro
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
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Célula</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Líder do Encontro</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Tema</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Participantes</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($meetings as $meeting)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                                    {{ $meeting->meeting_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-col">
                                        <span class="text-gray-800 font-semibold">{{ $meeting->cell->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $meeting->cell->supervision->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $meeting->leader->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 italic">
                                    {{ $meeting->theme ?? 'Sem tema' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-gray-800 font-semibold">{{ $meeting->adults_count + $meeting->children_count }}</span>
                                        <span class="text-xs text-gray-500">{{ $meeting->adults_count }}A /
                                            {{ $meeting->children_count }}C</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('cell-meetings.show', $meeting) }}"
                                            class="text-blue-600 hover:text-blue-800 p-1" title="Ver Detalhes">
                                            <i class="bi bi-eye text-lg"></i>
                                        </a>
                                        @can('update', $meeting)
                                            <a href="{{ route('cell-meetings.edit', $meeting) }}"
                                                class="text-yellow-600 hover:text-yellow-800 p-1" title="Editar">
                                                <i class="bi bi-pencil text-lg"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $meeting)
                                            <form action="{{ route('cell-meetings.destroy', $meeting) }}" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este encontro?')">
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
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                    Nenhum encontro de célula registrado até o momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($meetings->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $meetings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection