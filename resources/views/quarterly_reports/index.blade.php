@extends('layouts.app')

@section('title', 'Relatórios Trimestrais')
@section('page-title', 'Relatórios Trimestrais')
@section('page-subtitle', 'Acompanhamento estatístico e ministerial por zona')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-gray-800">Histórico de Relatórios</h3>
            <div class="flex space-x-3">
                <a href="{{ route('quarterly-reports.export') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md">
                    <i class="bi bi-file-earmark-spreadsheet mr-2"></i> Exportar Excel
                </a>
                @can('create', App\Models\QuarterlyReport::class)
                    <a href="{{ route('quarterly-reports.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md">
                        <i class="bi bi-file-earmark-plus mr-2"></i> Novo Relatório
                    </a>
                @endcan
            </div>
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
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Período</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Zona</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Supervisão</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Supervisor</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Células</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Membros</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Status</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-800 font-bold">
                                    {{ $report->quarter }}º Trimestre / {{ $report->year }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $report->zone->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $report->supervision->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $report->supervisor->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center font-semibold">
                                    {{ $report->cells_count }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center font-semibold text-blue-600">
                                    {{ $report->members_count }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                                                @if($report->status == 'submitted') bg-green-100 text-green-700 
                                                                @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ $report->status == 'submitted' ? 'Submetido' : 'Rascunho' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('quarterly-reports.show', $report) }}"
                                            class="text-blue-600 hover:text-blue-800 p-1" title="Ver Detalhes">
                                            <i class="bi bi-eye text-lg"></i>
                                        </a>
                                        @can('update', $report)
                                            <a href="{{ route('quarterly-reports.edit', $report) }}"
                                                class="text-yellow-600 hover:text-yellow-800 p-1" title="Editar">
                                                <i class="bi bi-pencil text-lg"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $report)
                                            <form action="{{ route('quarterly-reports.destroy', $report) }}" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este relatório?')">
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
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">
                                    Nenhum relatório trimestral encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reports->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection