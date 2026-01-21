@extends('layouts.app')

@section('title', 'Relatórios Trimestrais - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header & Top Actions -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Relatórios Trimestrais</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Estatísticas e Crescimento
                    Ministerial</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('quarterly-reports.export') }}"
                    class="bg-green-50 text-green-600 px-6 py-4 rounded-2xl hover:bg-green-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center border border-green-100">
                    <i class="bi bi-file-earmark-spreadsheet mr-2"></i> Exportar
                </a>
                @can('create', App\Models\QuarterlyReport::class)
                    <a href="{{ route('quarterly-reports.create') }}"
                        class="bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-100">
                        <i class="bi bi-file-earmark-plus mr-2"></i> Novo Relatório
                    </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-[2rem] flex items-center gap-4 animate-fade-in">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Reports List -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Período / Referência</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Zona / Supervisão</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Supervisor Responsável</th>
                            <th
                                class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Métricas</th>
                            <th
                                class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Estado</th>
                            <th
                                class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">
                                            {{ $report->quarter }}º Trimestre / {{ $report->year }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Relatório
                                            Periódico</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-gray-900 leading-tight">{{ $report->zone->name }}</span>
                                        <span
                                            class="text-[10px] text-gray-400 font-medium uppercase">{{ $report->supervision->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 font-black text-[10px]">
                                            {{ substr($report->supervisor->name, 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-gray-700">{{ $report->supervisor->name }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <div class="flex justify-center gap-4">
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-black text-gray-900">{{ $report->cells_count }}</span>
                                            <span
                                                class="text-[8px] text-gray-400 font-black uppercase tracking-widest">Células</span>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-black text-blue-600">{{ $report->members_count }}</span>
                                            <span
                                                class="text-[8px] text-gray-400 font-black uppercase tracking-widest">Membros</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <span
                                        class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border 
                                                                {{ $report->status == 'submitted' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-yellow-50 text-yellow-600 border-yellow-100' }}">
                                        {{ $report->status == 'submitted' ? 'Submetido' : 'Rascunho' }}
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <a href="{{ route('quarterly-reports.show', $report) }}"
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @can('update', $report)
                                            <a href="{{ route('quarterly-reports.edit', $report) }}"
                                                class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white flex items-center justify-center transition-all">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $report)
                                            <form action="{{ route('quarterly-reports.destroy', $report) }}" method="POST"
                                                id="delete-report-{{ $report->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="confirmDelete('delete-report-{{ $report->id }}', 'Deseja excluir este relatório?')"
                                                    class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all font-black">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4 text-gray-300">
                                        <i class="bi bi-file-earmark-break text-7xl"></i>
                                        <p class="font-bold text-lg">Nenhum relatório trimestral encontrado.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="p-8 bg-gray-50/50 border-t border-gray-50">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection