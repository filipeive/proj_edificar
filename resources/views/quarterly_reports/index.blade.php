@extends('layouts.app')

@section('title', 'Relatórios Trimestrais - Portal Life Church')
@section('page-title', 'Relatórios Trimestrais')
@section('page-subtitle', 'Análise de crescimento e estatísticas ministeriais')

@section('header-actions')
    <a href="{{ route('quarterly-reports.create') }}"
        class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100">
        <i class="bi bi-plus-circle text-2xl"></i>
    </a>
@endsection

@section('content')
    <div class="w-full space-y-8" x-data="{ 
                view: window.innerWidth < 768 ? 'grid' : 'list',
                updateView() {
                    if (window.innerWidth < 768 && this.view === 'list') {
                        this.view = 'grid';
                    }
                }
            }"
        x-init="$watch('view', value => localStorage.setItem('quarterly_reports_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('quarterly_reports_view') || 'list')"
        @resize.window.debounce.500ms="updateView()">
        <!-- Analytics Dashboard (Hidden on Mobile) -->
        <div class="hidden md:grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-gray-800 p-7 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all group relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-blue-50/50 dark:bg-blue-900/10 rounded-full -mr-16 -mt-16 group-hover:bg-blue-600/10 transition-colors">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-6">
                        <div
                            class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors shadow-sm">
                            <i
                                class="bi bi-people-fill text-blue-600 dark:text-blue-400 text-2xl group-hover:text-white transition-colors"></i>
                        </div>
                    </div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-[0.2em]">Membros
                        Atuais</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white mt-2 tracking-tight">
                        {{ number_format($totalMembers) }}
                    </p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-7 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all group relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-purple-50/50 dark:bg-purple-900/10 rounded-full -mr-16 -mt-16 group-hover:bg-purple-600/10 transition-colors">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-6">
                        <div
                            class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-2xl group-hover:bg-purple-600 transition-colors shadow-sm">
                            <i
                                class="bi bi-diagram-3-fill text-purple-600 dark:text-purple-400 text-2xl group-hover:text-white transition-colors"></i>
                        </div>
                    </div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-[0.2em]">Células
                        Ativas</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white mt-2 tracking-tight">{{ $totalCells }}</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-7 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all group relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-green-50/50 dark:bg-green-900/10 rounded-full -mr-16 -mt-16 group-hover:bg-green-600/10 transition-colors">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-6">
                        <div
                            class="bg-green-50 dark:bg-green-900/30 p-4 rounded-2xl group-hover:bg-green-600 transition-colors shadow-sm">
                            <i
                                class="bi bi-heart-fill text-green-600 dark:text-green-400 text-2xl group-hover:text-white transition-colors"></i>
                        </div>
                    </div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-[0.2em]">Salvações
                        (Acum.)</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white mt-2 tracking-tight">{{ $totalSaved }}</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-7 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all group relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-orange-50/50 dark:bg-orange-900/10 rounded-full -mr-16 -mt-16 group-hover:bg-orange-600/10 transition-colors">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-6">
                        <div
                            class="bg-orange-50 dark:bg-orange-900/30 p-4 rounded-2xl group-hover:bg-orange-600 transition-colors shadow-sm">
                            <i
                                class="bi bi-droplet-fill text-orange-600 dark:text-orange-400 text-2xl group-hover:text-white transition-colors"></i>
                        </div>
                    </div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-[0.2em]">Batismos
                        (Acum.)</p>
                    <p class="text-4xl font-black text-gray-900 dark:text-white mt-2 tracking-tight">{{ $totalBaptized }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Charts Section (Collapsible) -->
        <div x-data="{ open: true }" class="space-y-6">
            <button @click="open = !open"
                class="flex items-center justify-between w-full px-8 py-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all">
                <span>Visualizar Gráficos de Desempenho</span>
                <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </button>
            <div x-show="open" x-collapse class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3
                        class="text-sm font-black text-gray-900 dark:text-white mb-6 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        Crescimento de Membros
                    </h3>
                    <div class="h-[250px] relative">
                        <canvas id="membersChart"></canvas>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3
                        class="text-sm font-black text-gray-900 dark:text-white mb-6 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                        Multiplicação de Células
                    </h3>
                    <div class="h-[250px] relative">
                        <canvas id="cellsChart"></canvas>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3
                        class="text-sm font-black text-gray-900 dark:text-white mb-6 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                        Salvações por Trimestre
                    </h3>
                    <div class="h-[250px] relative">
                        <canvas id="savedChart"></canvas>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3
                        class="text-sm font-black text-gray-900 dark:text-white mb-6 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span>
                        Batismos por Trimestre
                    </h3>
                    <div class="h-[250px] relative">
                        <canvas id="baptizedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header & Top Actions -->
        <div
            class="bg-white dark:bg-gray-800 p-4 md:p-8 rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center md:flex-row justify-between gap-6 transition-colors">
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Relatórios Trimestrais</h1>
                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Análise de
                    Crescimento e Estatísticas Ministeriais</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('quarterly-reports.create') }}"
                    class="bg-blue-600 text-white px-8 py-4 rounded-2xl flex items-center transition shadow-lg shadow-blue-100 font-black text-xs uppercase tracking-widest hover:bg-blue-700">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Relatório
                </a>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Histórico de Relatórios</h2>
                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Todos os
                    períodos registrados
                </p>
            </div>
            <div class="h-10 w-[1px] bg-gray-100 dark:bg-gray-700 hidden md:block"></div>
            <div class="flex bg-gray-100 dark:bg-gray-700 p-1.5 rounded-2xl">
                <button @click="view = 'list'"
                    :class="view === 'list' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-md' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                    class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                    <i class="bi bi-list-ul"></i> Lista
                </button>
                <button @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-md' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                    class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                    <i class="bi bi-grid-fill"></i> Grid
                </button>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <form action="{{ route('quarterly-reports.export-annual') }}" method="GET"
                class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 p-2 rounded-2xl border border-gray-100 dark:border-gray-700">
                <select name="year"
                    class="bg-transparent border-transparent focus:ring-0 text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest px-4">
                    @for($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" class="text-gray-900">{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit"
                    class="bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 px-6 py-2.5 rounded-xl hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white transition-all font-black text-[10px] uppercase tracking-widest flex items-center border border-purple-100 dark:border-purple-800">
                    <i class="bi bi-calendar-check mr-2"></i> Consolidado
                </button>
            </form>

            <a href="{{ route('quarterly-reports.export') }}"
                class="bg-white dark:bg-gray-800 text-green-600 dark:text-green-400 px-6 py-4 rounded-2xl hover:bg-green-600 hover:text-white dark:hover:bg-green-600 dark:hover:text-white transition-all font-black text-[10px] uppercase tracking-widest flex items-center border border-green-100 dark:border-green-800 shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet mr-2"></i> Exportar
            </a>

            @can('create', App\Models\QuarterlyReport::class)
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('quarterly-reports.create') }}"
                        class="bg-blue-600 text-white px-8 py-4 rounded-2xl flex items-center transition shadow-lg shadow-blue-200 font-black text-xs uppercase tracking-widest group">
                        <i class="bi bi-plus-lg mr-2 group-hover:rotate-90 transition-transform"></i> Novo Relatório
                    </a>
                </div>
            @endcan
        </div>

    <!-- LIST VIEW -->
    <div x-show="view === 'list'" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-700/50">
                        <th
                            class="px-10 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            Período</th>
                        <th
                            class="px-10 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            Zona / Supervisão</th>
                        <th
                            class="px-10 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            Supervisor</th>
                        <th
                            class="px-10 py-5 text-center text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            Métricas</th>
                        <th
                            class="px-10 py-5 text-center text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            Estado</th>
                        <th
                            class="px-10 py-5 text-right text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                            <td class="px-10 py-6">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-black text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $report->quarter }}º Trimestre / {{ $report->year }}
                                    </span>
                                    <span
                                        class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">Relatório
                                        Periódico</span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $report->zone->name }}</span>
                                    <span
                                        class="text-[10px] text-gray-400 dark:text-gray-500 font-medium uppercase">{{ $report->supervision->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400 font-black text-[11px] shadow-sm border border-white dark:border-gray-600">
                                        {{ substr($report->supervisor->name, 0, 1) }}
                                    </div>
                                    <span
                                        class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $report->supervisor->name }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <div class="flex justify-center gap-6">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-sm font-black text-gray-900 dark:text-white">{{ $report->cells_count }}</span>
                                        <span
                                            class="text-[8px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest">Células</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="text-sm font-black text-blue-600 dark:text-blue-400">{{ $report->members_count }}</span>
                                        <span
                                            class="text-[8px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest">Membros</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <span
                                    class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border shadow-sm
                                                                                                                            {{ $report->status == 'submitted' ? 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 border-green-100 dark:border-green-800' : 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 border-yellow-100 dark:border-yellow-800' }}">
                                    {{ $report->status == 'submitted' ? 'Submetido' : 'Rascunho' }}
                                </span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div
                                    class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <a href="{{ route('quarterly-reports.show', $report) }}"
                                        class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white flex items-center justify-center transition-all shadow-sm">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    @can('update', $report)
                                        <a href="{{ route('quarterly-reports.edit', $report) }}"
                                            class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 hover:bg-orange-600 hover:text-white dark:hover:bg-orange-600 dark:hover:text-white flex items-center justify-center transition-all shadow-sm">
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
                                                class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white flex items-center justify-center transition-all shadow-sm">
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
                                <div class="flex flex-col items-center gap-4 text-gray-300 dark:text-gray-600">
                                    <i class="bi bi-file-earmark-break text-7xl"></i>
                                    <p class="font-bold text-lg text-gray-400 dark:text-gray-500">Nenhum relatório trimestral
                                        encontrado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- GRID VIEW -->
    <div x-show="view === 'grid'" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($reports as $report)
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:-translate-y-2 transition-all group overflow-hidden flex flex-col">
                <!-- Card Header -->
                <div class="p-8 pb-0 flex justify-between items-start">
                    <div class="flex flex-col">
                        <span
                            class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-1">{{ $report->quarter }}º
                            Trimestre</span>
                        <h3 class="text-2xl font-black text-gray-900 leading-tight">{{ $report->year }}</h3>
                    </div>
                    <span
                        class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border shadow-sm
                                                                                                                                    {{ $report->status == 'submitted' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-yellow-50 text-yellow-600 border-yellow-100' }}">
                        {{ $report->status == 'submitted' ? 'Submetido' : 'Rascunho' }}
                    </span>
                </div>

                <!-- Location info -->
                <div class="px-8 pt-4">
                    <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-blue-600 shadow-sm">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-gray-900">{{ $report->zone->name }}</span>
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $report->supervision->name ?? 'Sem Supervisão' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Metrics -->
                <div class="p-8 grid grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50/30 rounded-2xl border border-blue-50 flex flex-col items-center">
                        <span class="text-xl font-black text-blue-600">{{ $report->members_count }}</span>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Membros</span>
                    </div>
                    <div class="p-4 bg-purple-50/30 rounded-2xl border border-purple-50 flex flex-col items-center">
                        <span class="text-xl font-black text-purple-600">{{ $report->cells_count }}</span>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Células</span>
                    </div>
                    <div class="p-4 bg-green-50/30 rounded-2xl border border-green-50 flex flex-col items-center">
                        <span class="text-xl font-black text-green-600">{{ $report->saved_count }}</span>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Decisões</span>
                    </div>
                    <div class="p-4 bg-orange-50/30 rounded-2xl border border-orange-50 flex flex-col items-center">
                        <span class="text-xl font-black text-orange-600">{{ $report->baptized_count }}</span>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Batismos</span>
                    </div>
                </div>

                <div class="mt-auto">
                    <!-- Supervisor -->
                    <div class="px-8 py-4 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] font-black">
                                {{ substr($report->supervisor->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Supervisor</span>
                                <span class="text-xs font-bold text-gray-700">{{ $report->supervisor->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="p-6 bg-gray-50/50 border-t border-gray-50 flex gap-2">
                        <a href="{{ route('quarterly-reports.show', $report) }}"
                            class="flex-1 bg-white text-blue-600 py-3 rounded-xl border border-blue-100 hover:bg-blue-600 hover:text-white transition-all font-black text-[10px] uppercase tracking-widest text-center shadow-sm">
                            Visualizar
                        </a>
                        @can('update', $report)
                            <a href="{{ route('quarterly-reports.edit', $report) }}"
                                class="w-12 h-12 bg-white text-orange-600 rounded-xl border border-orange-100 hover:bg-orange-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        @endcan
                        @can('delete', $report)
                            <form action="{{ route('quarterly-reports.destroy', $report) }}" method="POST"
                                id="grid-delete-{{ $report->id }}" class="contents">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete('grid-delete-{{ $report->id }}', 'Deseja excluir este relatório?')"
                                    class="w-12 h-12 bg-white text-red-600 rounded-xl border border-red-100 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-[2.5rem] p-20 border border-gray-100 text-center">
                <div class="flex flex-col items-center gap-4 text-gray-300">
                    <i class="bi bi-file-earmark-break text-7xl"></i>
                    <p class="font-bold text-lg text-gray-400">Nenhum relatório trimestral encontrado.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reports->hasPages())
        <div class="flex justify-center mt-12">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                {{ $reports->links() }}
            </div>
        </div>
    @endif
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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