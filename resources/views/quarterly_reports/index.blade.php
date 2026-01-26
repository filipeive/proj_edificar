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
                selected: [],
                showCharts: window.innerWidth >= 768,
                updateView() {
                    if (window.innerWidth < 768 && this.view === 'list') {
                        this.view = 'grid';
                    }
                },
                toggleAll() {
                    const allIds = {{ Js::from($reports->pluck('id')) }};
                    if (this.selected.length === allIds.length) {
                        this.selected = [];
                    } else {
                        this.selected = allIds;
                    }
                },
                deleteSelected() {
                    document.getElementById('bulk-delete-form').submit();
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
                    <p class="text-4xl font-black text-gray-900 dark:text-white mt-2 tracking-tight">
                        {{ number_format($totalCells) }}
                    </p>
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
                    <p class="text-4xl font-black text-gray-900 dark:text-white mt-2 tracking-tight">
                        {{ number_format($totalSaved) }}
                    </p>
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
                    <p class="text-4xl font-black text-gray-900 dark:text-white mt-2 tracking-tight">
                        {{ number_format($totalBaptized) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Charts Section (Hidden on Mobile) -->
        <div x-show="showCharts" class="hidden md:block space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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

        <!-- Search & Filters -->
        <div
            class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
            <form action="{{ route('quarterly-reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-6">
                <!-- Search -->
                <div class="flex-1 relative group">
                    <i
                        class="bi bi-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Pesquisar por supervisor ou zona..."
                        class="w-full pl-14 pr-6 py-4 bg-gray-50/50 dark:bg-gray-700/50 border-transparent focus:bg-white dark:focus:bg-gray-700 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 rounded-2xl text-sm font-bold transition-all">
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-4">
                    <select name="year"
                        class="bg-gray-50/50 dark:bg-gray-700/50 border-transparent focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 rounded-2xl text-xs font-black uppercase tracking-widest px-6 py-4">
                        <option value="">Todos os Anos</option>
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                        @endfor
                    </select>

                    <select name="quarter"
                        class="bg-gray-50/50 dark:bg-gray-700/50 border-transparent focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 rounded-2xl text-xs font-black uppercase tracking-widest px-6 py-4">
                        <option value="">Todos os Trimestres</option>
                        @for($q = 1; $q <= 4; $q++)
                            <option value="{{ $q }}" @selected(request('quarter') == $q)>{{ $q }}º Trimestre</option>
                        @endfor
                    </select>

                    <button type="submit"
                        class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-100 dark:shadow-none">
                        Filtrar
                    </button>

                    @if(request()->anyFilled(['search', 'year', 'quarter']))
                        <a href="{{ route('quarterly-reports.index') }}"
                            class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition flex items-center">
                            Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Bulk Actions & View Switcher -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <div x-show="selected.length > 0" class="flex items-center gap-4" x-cloak x-transition>
                    <span
                        class="text-xs font-black uppercase tracking-widest text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-4 py-2 rounded-full">
                        <span x-text="selected.length"></span> selecionados
                    </span>
                    @can('deleteAny', App\Models\QuarterlyReport::class)
                        <button @click="if(confirm('Deseja excluir os relatórios selecionados?')) deleteSelected()"
                            class="bg-red-50 text-red-600 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all border border-red-100">
                            <i class="bi bi-trash3-fill mr-2"></i> Excluir Massa
                        </button>
                    @endcan
                </div>

                <div class="flex bg-gray-100 dark:bg-gray-700 p-1.5 rounded-2xl">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-md' : 'text-gray-400 dark:text-gray-400'"
                        class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        <i class="bi bi-list-ul mr-2"></i> Lista
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-md' : 'text-gray-400 dark:text-gray-400'"
                        class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        <i class="bi bi-grid-fill mr-2"></i> Grid
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('quarterly-reports.export', request()->query()) }}"
                    class="bg-white dark:bg-gray-800 text-green-600 border border-green-100 dark:border-green-800 px-6 py-3.5 rounded-2xl hover:bg-green-600 hover:text-white transition-all font-black text-[10px] uppercase tracking-widest flex items-center">
                    <i class="bi bi-file-earmark-spreadsheet mr-2"></i> Exportar Atual
                </a>
                <a href="{{ route('quarterly-reports.create') }}"
                    class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-100 dark:shadow-none">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Relatório
                </a>
            </div>
        </div>

        <form id="bulk-delete-form" action="{{ route('quarterly-reports.bulk-destroy') }}" method="POST"
            style="display: none;">
            @csrf
            @method('DELETE')
            <template x-for="id in selected">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </form>

        <!-- LIST VIEW -->
        <div x-show="view === 'list'" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/50">
                            <th class="px-6 py-5 text-center w-10">
                                <input type="checkbox" @click="toggleAll()"
                                    :checked="selected.length === {{ $reports->count() }} && {{ $reports->count() }} > 0"
                                    class="rounded-lg border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5 transition-all cursor-pointer">
                            </th>
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
                                <td class="px-6 py-5 text-center">
                                    <input type="checkbox" :value="{{ $report->id }}" x-model="selected"
                                        class="rounded-lg border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5 transition-all cursor-pointer">
                                </td>
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
                                        <p class="font-bold text-lg text-gray-400 dark:text-gray-500">Nenhum relatório
                                            trimestral
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
                    <div class="p-8 pb-0 flex justify-between items-start relative">
                        <div class="absolute top-8 right-8">
                            <input type="checkbox" :value="{{ $report->id }}" x-model="selected"
                                class="rounded-lg border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5 transition-all cursor-pointer shadow-sm">
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-1">{{ $report->quarter }}º
                                Trimestre</span>
                            <h3 class="text-2xl font-black text-gray-900 leading-tight dark:text-white">{{ $report->year }}</h3>
                        </div>
                        <span
                            class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border shadow-sm mr-10
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
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Supervisor</span>
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