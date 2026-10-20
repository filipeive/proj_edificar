@extends('layouts.app')

@section('title', 'Relatórios Trimestrais - Portal Life Church')
@section('page-title', 'Relatórios Trimestrais')
@section('page-subtitle', 'Análise de crescimento e estatísticas ministeriais')

@section('header-actions')
    <div class="md:hidden">
        <a href="{{ route('quarterly-reports.create') }}"
            class="inline-flex items-center justify-center rounded-lg bg-orange-700 p-2 text-white shadow-sm hover:bg-orange-800 transition">
            <i class="bi bi-plus-lg text-base"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="w-full space-y-5" x-data="{ 
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

        <!-- Page Header (National Style) -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão Eclesiástica</p>
                <h1 class="mt-0.5 sm:mt-1 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Relatórios Trimestrais</h1>
                <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-gray-500">Indicadores trimestrais de células, discipulado, salvações e batismos por supervisão.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="showCharts = !showCharts"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi" :class="showCharts ? 'bi-eye-slash' : 'bi-bar-chart-line'"></i>
                    <span x-text="showCharts ? 'Ocultar gráficos' : 'Ver gráficos'"></span>
                </button>
                <a href="{{ route('quarterly-reports.export-pdf', request()->query()) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-red-700 hover:bg-red-50 shadow-sm">
                    <i class="bi bi-file-earmark-pdf"></i> <span>PDF</span>
                </a>
                <a href="{{ route('quarterly-reports.export', request()->query()) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-green-200 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-green-800 hover:bg-green-50 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i> <span>Excel</span>
                </a>
                <a href="{{ route('quarterly-reports.create') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i> <span>Novo Relatório</span>
                </a>
            </div>
        </header>

        <!-- Summary KPI Cards (National Style) -->
        <section aria-label="Indicadores trimestrais">
            <h3 class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-gray-500 mb-2">Resumo Consolidado</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Membros Atuais</p>
                        <i class="bi bi-people text-blue-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ number_format($totalMembers) }}</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Último trimestre registado</p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Células Ativas</p>
                        <i class="bi bi-diagram-3 text-indigo-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ number_format($totalCells) }}</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Unidades em funcionamento</p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Salvações (Acum.)</p>
                        <i class="bi bi-heart-pulse text-emerald-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">{{ number_format($totalSaved) }}</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Decisões contabilizadas</p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Batismos (Acum.)</p>
                        <i class="bi bi-droplet text-orange-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-orange-700 tracking-tight tabular-nums">{{ number_format($totalBaptized) }}</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Batismos em águas</p>
                </article>
            </div>
        </section>

        <!-- Charts Section (National Style) -->
        <section x-show="showCharts" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Crescimento de Membros</h2>
                        <p class="text-xs text-gray-500">Evolução por trimestre</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-blue-700">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span> Membros
                    </span>
                </div>
                <div class="h-52 relative">
                    <canvas id="membersChart"></canvas>
                </div>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Multiplicação de Células</h2>
                        <p class="text-xs text-gray-500">Total de células ativas por trimestre</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-indigo-700">
                        <span class="h-2 w-2 rounded-full bg-indigo-600"></span> Células
                    </span>
                </div>
                <div class="h-52 relative">
                    <canvas id="cellsChart"></canvas>
                </div>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Salvações por Trimestre</h2>
                        <p class="text-xs text-gray-500">Novas decisões registadas</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-600"></span> Salvações
                    </span>
                </div>
                <div class="h-52 relative">
                    <canvas id="savedChart"></canvas>
                </div>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Batismos por Trimestre</h2>
                        <p class="text-xs text-gray-500">Batismos realizados no período</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-orange-700">
                        <span class="h-2 w-2 rounded-full bg-orange-600"></span> Batismos
                    </span>
                </div>
                <div class="h-52 relative">
                    <canvas id="baptizedChart"></canvas>
                </div>
            </article>
        </section>

        <!-- Search, Filters & View Switcher -->
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm space-y-3">
            <form action="{{ route('quarterly-reports.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="flex-1">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Pesquisar</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Supervisor, supervisão ou zona..."
                            class="w-full rounded-lg border border-gray-300 pl-8 pr-3 py-2 text-xs sm:text-sm font-medium text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-end gap-2.5">
                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Ano</label>
                        <select name="year" data-searchable="false"
                            class="w-full sm:w-auto rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-700">
                            <option value="">Todos os Anos</option>
                            @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Trimestre</label>
                        <select name="quarter" data-searchable="false"
                            class="w-full sm:w-auto rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-700">
                            <option value="">Todos os Trimestres</option>
                            @for($q = 1; $q <= 4; $q++)
                                <option value="{{ $q }}" @selected(request('quarter') == $q)>{{ $q }}º Trimestre</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-1 flex items-center gap-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-gray-900 px-4 py-2 text-xs font-bold text-white hover:bg-gray-800 transition">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>

                        @if(request()->anyFilled(['search', 'year', 'quarter']))
                            <a href="{{ route('quarterly-reports.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 transition">
                                Limpar
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500">
                        {{ $reports->total() }} {{ $reports->total() === 1 ? 'relatório encontrado' : 'relatórios encontrados' }}
                    </span>
                    <div x-show="selected.length > 0" class="flex items-center gap-2" x-cloak x-transition>
                        <span class="rounded-full bg-orange-50 border border-orange-200 px-2.5 py-0.5 text-[11px] font-bold text-orange-800">
                            <span x-text="selected.length"></span> selecionado(s)
                        </span>
                        @can('deleteAny', App\Models\QuarterlyReport::class)
                            <button type="button" @click="if(confirm('Deseja excluir os relatórios selecionados?')) deleteSelected()"
                                class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 hover:bg-red-600 hover:text-white transition">
                                <i class="bi bi-trash3"></i> Excluir
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-0.5">
                    <button type="button" @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold transition">
                        <i class="bi bi-list-ul"></i> Lista
                    </button>
                    <button type="button" @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold transition">
                        <i class="bi bi-grid"></i> Cards
                    </button>
                </div>
            </div>
        </div>

        <form id="bulk-delete-form" action="{{ route('quarterly-reports.bulk-destroy') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
            <template x-for="id in selected">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </form>

        <!-- LIST VIEW (National Table Style) -->
        <section x-show="view === 'list'" x-cloak class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-3.5 py-3 text-center w-9">
                            <input type="checkbox" @click="toggleAll()"
                                :checked="selected.length === {{ $reports->count() }} && {{ $reports->count() }} > 0"
                                class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="px-4 py-3">Período</th>
                        <th class="px-4 py-3">Zona / Supervisão</th>
                        <th class="px-4 py-3">Supervisor</th>
                        <th class="px-4 py-3 text-right">Células</th>
                        <th class="px-4 py-3 text-right">Membros</th>
                        <th class="px-4 py-3 text-right">Salvações</th>
                        <th class="px-4 py-3 text-right">Batismos</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-3.5 py-3 text-center">
                                <input type="checkbox" :value="{{ $report->id }}" x-model="selected"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('quarterly-reports.show', $report) }}" class="font-bold text-gray-900 hover:text-orange-700 hover:underline">
                                    {{ $report->quarter }}º Trimestre / {{ $report->year }}
                                </a>
                                <span class="block text-[11px] text-gray-400">
                                    {{ $report->submitted_at ? $report->submitted_at->format('d/m/Y') : $report->created_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $report->zone->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $report->supervision->name ?? 'Sem supervisão' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-orange-50 text-[11px] font-black text-orange-700 border border-orange-200/60">
                                        {{ strtoupper(substr($report->supervisor->name ?? 'S', 0, 1)) }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-700">{{ $report->supervisor->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-gray-800">{{ number_format($report->cells_count) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-bold text-blue-700">{{ number_format($report->members_count) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-emerald-700">{{ number_format($report->saved_count) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-orange-700">{{ number_format($report->baptized_count) }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase {{ $report->status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $report->status === 'submitted' ? 'Submetido' : 'Rascunho' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('quarterly-reports.show', $report) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:border-orange-300 hover:text-orange-800 hover:bg-orange-50/50 shadow-sm transition"
                                        title="Ver detalhes">
                                        <i class="bi bi-eye text-orange-700"></i> <span class="hidden xl:inline">Detalhes</span>
                                    </a>
                                    @can('update', $report)
                                        <a href="{{ route('quarterly-reports.edit', $report) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-gray-600 hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50/50 shadow-sm transition"
                                            title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $report)
                                        <form action="{{ route('quarterly-reports.destroy', $report) }}" method="POST"
                                            id="delete-report-{{ $report->id }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-report-{{ $report->id }}', 'Deseja excluir este relatório?')"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-red-600 hover:border-red-300 hover:bg-red-50 shadow-sm transition"
                                                title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-500">
                                <i class="bi bi-file-earmark-bar-graph text-3xl text-gray-300 block mb-2"></i>
                                Nenhum relatório trimestral encontrado para os filtros selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <!-- GRID VIEW (National Card Style) -->
        <section x-show="view === 'grid'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
            @forelse($reports as $report)
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300 transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2 pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" :value="{{ $report->id }}" x-model="selected"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">{{ $report->quarter }}º Trimestre • {{ $report->year }}</span>
                                    <h3 class="text-sm font-black text-gray-950 leading-snug">{{ $report->supervision->name ?? 'Sem Supervisão' }}</h3>
                                    <p class="text-xs text-gray-500"><i class="bi bi-geo-alt text-gray-400 mr-1"></i>{{ $report->zone->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-black uppercase {{ $report->status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $report->status === 'submitted' ? 'Submetido' : 'Rascunho' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-4 gap-1.5 my-3 text-center">
                            <div class="rounded-lg bg-gray-50 p-2 border border-gray-100">
                                <span class="block text-sm font-black text-blue-700 tabular-nums">{{ $report->members_count }}</span>
                                <span class="block text-[9px] font-bold uppercase text-gray-400">Membros</span>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-2 border border-gray-100">
                                <span class="block text-sm font-black text-indigo-700 tabular-nums">{{ $report->cells_count }}</span>
                                <span class="block text-[9px] font-bold uppercase text-gray-400">Células</span>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-2 border border-gray-100">
                                <span class="block text-sm font-black text-emerald-700 tabular-nums">{{ $report->saved_count }}</span>
                                <span class="block text-[9px] font-bold uppercase text-gray-400">Salvos</span>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-2 border border-gray-100">
                                <span class="block text-sm font-black text-orange-700 tabular-nums">{{ $report->baptized_count }}</span>
                                <span class="block text-[9px] font-bold uppercase text-gray-400">Batismos</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 text-[10px] font-black text-gray-600">
                                {{ strtoupper(substr($report->supervisor->name ?? 'S', 0, 1)) }}
                            </span>
                            <span class="text-xs font-semibold text-gray-600 truncate">{{ $report->supervisor->name ?? 'N/A' }}</span>
                        </div>

                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <a href="{{ route('quarterly-reports.show', $report) }}"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:border-orange-300 hover:text-orange-800 shadow-sm transition">
                                <i class="bi bi-eye text-orange-700"></i> Ver
                            </a>
                            @can('update', $report)
                                <a href="{{ route('quarterly-reports.edit', $report) }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-gray-600 hover:border-blue-300 hover:text-blue-700 shadow-sm transition"
                                    title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            @endcan
                            @can('delete', $report)
                                <form action="{{ route('quarterly-reports.destroy', $report) }}" method="POST"
                                    id="grid-delete-{{ $report->id }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmDelete('grid-delete-{{ $report->id }}', 'Deseja excluir este relatório?')"
                                        class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-red-600 hover:border-red-300 hover:bg-red-50 shadow-sm transition"
                                        title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-gray-200 bg-white p-8 text-center text-gray-500">
                    Nenhum relatório trimestral encontrado.
                </div>
            @endforelse
        </section>

        @if($reports->hasPages())
            <div class="pt-2">
                {{ $reports->links() }}
            </div>
        @endif
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

            new Chart(document.getElementById('membersChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Membros',
                        data: @json($membersData),
                        borderColor: '#1d4ed8',
                        backgroundColor: 'rgba(29, 78, 216, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: chartOptions
            });

            new Chart(document.getElementById('cellsChart'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Células',
                        data: @json($cellsData),
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        borderRadius: 6
                    }]
                },
                options: chartOptions
            });

            new Chart(document.getElementById('savedChart'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Salvações',
                        data: @json($savedData),
                        backgroundColor: 'rgba(5, 150, 105, 0.8)',
                        borderRadius: 6
                    }]
                },
                options: chartOptions
            });

            new Chart(document.getElementById('baptizedChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Batismos',
                        data: @json($baptizedData),
                        borderColor: '#c2410c',
                        backgroundColor: 'rgba(194, 65, 12, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: chartOptions
            });
        });
    </script>
@endsection
