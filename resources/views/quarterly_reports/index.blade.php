@extends('layouts.app')

@section('title', 'Relatórios Trimestrais - Portal Life Church')
@section('page-title', 'Relatórios Trimestrais')
@section('page-subtitle', 'Análise de crescimento e estatísticas ministeriais')

@section('content')
    <div class="w-full space-y-8">
        <!-- Analytics Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-50 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors">
                        <i class="bi bi-people-fill text-blue-600 text-2xl group-hover:text-white transition-colors"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Membros Atuais</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ number_format($totalMembers) }}</p>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-50 p-4 rounded-2xl group-hover:bg-purple-600 transition-colors">
                        <i
                            class="bi bi-diagram-3-fill text-purple-600 text-2xl group-hover:text-white transition-colors"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Células Ativas</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $totalCells }}</p>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-50 p-4 rounded-2xl group-hover:bg-green-600 transition-colors">
                        <i class="bi bi-heart-fill text-green-600 text-2xl group-hover:text-white transition-colors"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Salvações (Período)</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $totalSaved }}</p>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-50 p-4 rounded-2xl group-hover:bg-orange-600 transition-colors">
                        <i class="bi bi-droplet-fill text-orange-600 text-2xl group-hover:text-white transition-colors"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Batismos (Período)</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $totalBaptized }}</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-xl font-black text-gray-900 mb-6">Crescimento de Membros</h3>
                <div class="h-[300px] relative">
                    <canvas id="membersChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-xl font-black text-gray-900 mb-6">Multiplicação de Células</h3>
                <div class="h-[300px] relative">
                    <canvas id="cellsChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-xl font-black text-gray-900 mb-6">Salvações por Trimestre</h3>
                <div class="h-[300px] relative">
                    <canvas id="savedChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-xl font-black text-gray-900 mb-6">Batismos por Trimestre</h3>
                <div class="h-[300px] relative">
                    <canvas id="baptizedChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Header & Actions -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900">Histórico de Relatórios</h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Todos os períodos registrados</p>
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

        <!-- Reports Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Período</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Zona / Supervisão</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Supervisor</th>
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
                                                    class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false } }
                }
            };

            // Members Chart
            new Chart(document.getElementById('membersChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Membros',
                        data: @json($membersData),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });

            // Cells Chart
            new Chart(document.getElementById('cellsChart'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Células',
                        data: @json($cellsData),
                        backgroundColor: 'rgba(168, 85, 247, 0.8)',
                        borderRadius: 8
                    }]
                },
                options: chartOptions
            });

            // Saved Chart
            new Chart(document.getElementById('savedChart'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Salvações',
                        data: @json($savedData),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderRadius: 8
                    }]
                },
                options: chartOptions
            });

            // Baptized Chart
            new Chart(document.getElementById('baptizedChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Batismos',
                        data: @json($baptizedData),
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        });
    </script>
@endsection