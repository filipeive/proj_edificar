@extends('layouts.app')

@section('title', 'Relatórios Trimestrais - Portal Life Church')
@section('page-title', 'Relatórios Trimestrais')
@section('page-subtitle', 'Análise dinâmica de supervisões, comparação de períodos, movimentos e saúde ministerial')

@section('header-actions')
    <div class="md:hidden">
        <a href="{{ route('quarterly-reports.create') }}"
            class="inline-flex items-center justify-center rounded-lg bg-orange-700 p-2 text-white shadow-sm hover:bg-orange-800 transition">
            <i class="bi bi-plus-lg text-base"></i>
        </a>
    </div>
@endsection

@php
    $calcDelta = function ($current, $prev) {
        $diff = $current - $prev;
        $pct = $prev > 0 ? round(($diff / $prev) * 100, 1) : ($current > 0 ? 100 : 0);
        return ['diff' => $diff, 'pct' => $pct];
    };

    $membersDelta = $calcDelta($currentSummary['members'], $compareSummary['members']);
    $cellsDelta = $calcDelta($currentSummary['cells'], $compareSummary['cells']);
    $savedDelta = $calcDelta($currentSummary['saved'], $compareSummary['saved']);
    $baptizedDelta = $calcDelta($currentSummary['baptized'], $compareSummary['baptized']);
    $healthDelta = $calcDelta($currentSummary['health_pct'], $compareSummary['health_pct']);

    $selectedZoneObj = request('zone_id') ? $zones->firstWhere('id', request('zone_id')) : null;
    $selectedSupervisionObj = request('supervision_id') ? $supervisions->firstWhere('id', request('supervision_id')) : null;

    $supervisionsJson = $supervisions->map(fn($s) => [
        'id' => (string) $s->id,
        'name' => $s->name,
        'zone_id' => (string) $s->zone_id,
        'zone_name' => $s->zone->name ?? '',
    ])->values();
@endphp

@section('content')
    <div class="w-full space-y-5" x-data="{ 
        view: window.innerWidth < 768 ? 'grid' : 'list',
        selected: [],
        showAnalytics: true,
        activeTab: '{{ request('tab', 'overview') }}',
        selectedZone: '{{ request('zone_id', '') }}',
        selectedSupervision: '{{ request('supervision_id', '') }}',
        allSupervisions: {{ Js::from($supervisionsJson) }},
        get filteredSupervisions() {
            if (!this.selectedZone) return this.allSupervisions;
            return this.allSupervisions.filter(s => s.zone_id === String(this.selectedZone));
        },
        onZoneChange() {
            if (this.selectedZone && this.selectedSupervision) {
                const exists = this.filteredSupervisions.some(s => s.id === String(this.selectedSupervision));
                if (!exists) this.selectedSupervision = '';
            }
        },
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
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão Eclesiástica & Inteligência</p>
                <div class="mt-0.5 flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Relatórios Trimestrais</h1>
                    @if($selectedSupervisionObj)
                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 border border-orange-200 px-2.5 py-0.5 text-xs font-black text-orange-800">
                            <i class="bi bi-diagram-3"></i> {{ $selectedSupervisionObj->name }}
                        </span>
                    @elseif($selectedZoneObj)
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 border border-blue-200 px-2.5 py-0.5 text-xs font-black text-blue-800">
                            <i class="bi bi-geo-alt"></i> {{ $selectedZoneObj->name }}
                        </span>
                    @endif
                </div>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                    Análise por supervisão, comparação entre trimestres ({{ $currentSummary['label'] }} vs {{ $compareSummary['label'] }}), movimentos e saúde ministerial.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="showAnalytics = !showAnalytics"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi" :class="showAnalytics ? 'bi-eye-slash' : 'bi-graph-up-arrow'"></i>
                    <span x-text="showAnalytics ? 'Ocultar painel analítico' : 'Mostrar painel analítico'"></span>
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

        <!-- Dynamic Filter Bar (National Style) -->
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm space-y-3">
            <form action="{{ route('quarterly-reports.index') }}" method="GET" class="space-y-3">
                <input type="hidden" name="tab" :value="activeTab">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5">
                    @if($zones->count() > 1)
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Zona</label>
                            <select name="zone_id" x-model="selectedZone" @change="onZoneChange()" data-searchable="false"
                                class="w-full rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold text-gray-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                <option value="">Todas as Zonas</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="{{ $zones->count() > 1 ? '' : 'sm:col-span-2' }}">
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Supervisão Específica</label>
                        <select name="supervision_id" x-model="selectedSupervision" data-searchable="false"
                            class="w-full rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold text-gray-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                            <option value="">Todas as Supervisões</option>
                            <template x-for="sup in filteredSupervisions" :key="sup.id">
                                <option :value="sup.id" :selected="String(sup.id) === String(selectedSupervision)"
                                    x-text="sup.zone_name && !selectedZone ? `${sup.name} (${sup.zone_name})` : sup.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Ano</label>
                        <select name="year" data-searchable="false"
                            class="w-full rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold text-gray-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                            <option value="">Todos os Anos</option>
                            @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Trimestre</label>
                        <select name="quarter" data-searchable="false"
                            class="w-full rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold text-gray-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                            <option value="">Todos os Trimestres</option>
                            @for($q = 1; $q <= 4; $q++)
                                <option value="{{ $q }}" @selected(request('quarter') == $q)>{{ $q }}º Trimestre</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Comparar Com</label>
                        <select name="compare_period" data-searchable="false"
                            class="w-full rounded-lg border border-gray-300 px-2.5 py-2 text-xs font-semibold text-gray-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                            <option value="">Trimestre Anterior ({{ $compareSummary['label'] }})</option>
                            @foreach($availablePeriods as $period)
                                <option value="{{ $period['key'] }}" @selected(request('compare_period') === $period['key'])>
                                    {{ $period['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Pesquisa Livre</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Supervisor, zona..."
                                class="w-full rounded-lg border border-gray-300 pl-7 pr-2.5 py-2 text-xs font-medium text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-gray-100">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-bold text-gray-500">
                            <i class="bi bi-layers text-orange-700 mr-1"></i>
                            Período em análise: <strong class="text-gray-900">{{ $currentSummary['label'] }}</strong>
                            <span class="text-gray-400 mx-1">vs</span>
                            <strong class="text-gray-700">{{ $compareSummary['label'] }}</strong>
                        </span>

                        @if($selectedZoneObj)
                            <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 border border-blue-200 px-2 py-0.5 text-[10px] font-bold text-blue-800">
                                Zona: {{ $selectedZoneObj->name }}
                            </span>
                        @endif
                        @if($selectedSupervisionObj)
                            <span class="inline-flex items-center gap-1 rounded-md bg-orange-50 border border-orange-200 px-2 py-0.5 text-[10px] font-bold text-orange-800">
                                Supervisão: {{ $selectedSupervisionObj->name }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if(request()->anyFilled(['search', 'year', 'quarter', 'zone_id', 'supervision_id', 'compare_period']))
                            <a href="{{ route('quarterly-reports.index') }}"
                                class="inline-flex items-center justify-center gap-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-100 transition">
                                <i class="bi bi-x-circle"></i> Limpar Filtros
                            </a>
                        @endif
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-gray-900 px-4 py-1.5 text-xs font-bold text-white hover:bg-gray-800 transition shadow-sm">
                            <i class="bi bi-funnel"></i> Aplicar Análise
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary KPI Cards with Period Comparison Deltas -->
        <section aria-label="Indicadores trimestrais">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3">
                <!-- Membros -->
                <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Membros ({{ $currentSummary['label'] }})</p>
                        <i class="bi bi-people text-blue-700 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-1 flex items-baseline justify-between gap-2">
                        <p class="text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ number_format($currentSummary['members']) }}</p>
                        <span class="inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[10px] font-black tabular-nums {{ $membersDelta['diff'] > 0 ? 'bg-emerald-50 text-emerald-700' : ($membersDelta['diff'] < 0 ? 'bg-rose-50 text-rose-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $membersDelta['diff'] > 0 ? '+' : '' }}{{ $membersDelta['diff'] }} ({{ $membersDelta['diff'] >= 0 ? '+' : '' }}{{ $membersDelta['pct'] }}%)
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 truncate">Anterior ({{ $compareSummary['label'] }}): {{ number_format($compareSummary['members']) }}</p>
                </article>

                <!-- Células -->
                <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Células Ativas</p>
                        <i class="bi bi-diagram-3 text-indigo-700 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-1 flex items-baseline justify-between gap-2">
                        <p class="text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ number_format($currentSummary['cells']) }}</p>
                        <span class="inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[10px] font-black tabular-nums {{ $cellsDelta['diff'] > 0 ? 'bg-emerald-50 text-emerald-700' : ($cellsDelta['diff'] < 0 ? 'bg-rose-50 text-rose-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $cellsDelta['diff'] > 0 ? '+' : '' }}{{ $cellsDelta['diff'] }} ({{ $cellsDelta['diff'] >= 0 ? '+' : '' }}{{ $cellsDelta['pct'] }}%)
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 truncate">
                        Movimento: <span class="text-emerald-700 font-bold">+{{ $currentSummary['multiplications'] }} mult.</span> / <span class="text-rose-600 font-bold">-{{ $currentSummary['closed_cells'] }} fech.</span>
                    </p>
                </article>

                <!-- Salvações -->
                <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Salvações no Período</p>
                        <i class="bi bi-heart-pulse text-emerald-700 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-1 flex items-baseline justify-between gap-2">
                        <p class="text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">{{ number_format($currentSummary['saved']) }}</p>
                        <span class="inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[10px] font-black tabular-nums {{ $savedDelta['diff'] > 0 ? 'bg-emerald-50 text-emerald-700' : ($savedDelta['diff'] < 0 ? 'bg-rose-50 text-rose-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $savedDelta['diff'] > 0 ? '+' : '' }}{{ $savedDelta['diff'] }}
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 truncate">Acumulado (8 trim.): {{ number_format($totalSaved) }}</p>
                </article>

                <!-- Batismos -->
                <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Batismos no Período</p>
                        <i class="bi bi-droplet text-orange-700 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-1 flex items-baseline justify-between gap-2">
                        <p class="text-lg sm:text-2xl font-black text-orange-700 tracking-tight tabular-nums">{{ number_format($currentSummary['baptized']) }}</p>
                        <span class="inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[10px] font-black tabular-nums {{ $baptizedDelta['diff'] > 0 ? 'bg-emerald-50 text-emerald-700' : ($baptizedDelta['diff'] < 0 ? 'bg-rose-50 text-rose-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $baptizedDelta['diff'] > 0 ? '+' : '' }}{{ $baptizedDelta['diff'] }}
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 truncate">Planificados: {{ number_format($currentSummary['planned_baptism']) }} | Acum.: {{ number_format($totalBaptized) }}</p>
                </article>

                <!-- Saúde Ministerial -->
                <article class="col-span-2 md:col-span-1 rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Saúde Ministerial</p>
                        <i class="bi bi-shield-check text-purple-700 text-sm sm:text-base"></i>
                    </div>
                    <div class="mt-1 flex items-baseline justify-between gap-2">
                        <p class="text-lg sm:text-2xl font-black {{ $currentSummary['health_pct'] >= 75 ? 'text-emerald-700' : ($currentSummary['health_pct'] >= 50 ? 'text-blue-700' : 'text-amber-600') }} tracking-tight tabular-nums">
                            {{ $currentSummary['health_pct'] }}%
                        </p>
                        <span class="inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[10px] font-black tabular-nums {{ $healthDelta['diff'] > 0 ? 'bg-emerald-50 text-emerald-700' : ($healthDelta['diff'] < 0 ? 'bg-rose-50 text-rose-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $healthDelta['diff'] > 0 ? '+' : '' }}{{ $healthDelta['diff'] }} pp
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 truncate">Média: {{ number_format($currentSummary['health_avg'], 1) }} / 3.0 ({{ $currentSummary['reports_count'] }} rel.)</p>
                </article>
            </div>
        </section>

        <!-- Dynamic Analytical Panel (Tabs: Evolução | Comparação | Movimentos | Saúde) -->
        <section x-show="showAnalytics" x-transition class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <!-- Tabs Header -->
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50/70 px-4 py-2.5">
                <div class="flex flex-wrap items-center gap-1">
                    <button type="button" @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200/70'"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold transition">
                        <i class="bi bi-bar-chart-line"></i> Evolução Trimestral
                    </button>
                    <button type="button" @click="activeTab = 'comparison'"
                        :class="activeTab === 'comparison' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200/70'"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold transition">
                        <i class="bi bi-arrow-left-right"></i> Comparação de Períodos
                    </button>
                    <button type="button" @click="activeTab = 'movements'"
                        :class="activeTab === 'movements' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200/70'"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold transition">
                        <i class="bi bi-activity"></i> Movimentos & Dinâmica
                    </button>
                    <button type="button" @click="activeTab = 'health'"
                        :class="activeTab === 'health' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200/70'"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold transition">
                        <i class="bi bi-heart-pulse"></i> Saúde Ministerial (12 Indicadores)
                    </button>
                </div>

                <span class="text-[11px] font-semibold text-gray-500">
                    @if($selectedSupervisionObj)
                        Escopo: <strong class="text-orange-700">{{ $selectedSupervisionObj->name }}</strong>
                    @elseif($selectedZoneObj)
                        Escopo: <strong class="text-blue-700">{{ $selectedZoneObj->name }}</strong>
                    @else
                        Escopo: <strong class="text-gray-800">Geral Consolidado</strong>
                    @endif
                </span>
            </div>

            <!-- TAB 1: EVOLUÇÃO TRIMESTRAL -->
            <div x-show="activeTab === 'overview'" class="p-4 sm:p-5">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <article class="rounded-xl border border-gray-100 bg-gray-50/40 p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h2 class="text-sm font-black text-gray-900">Crescimento de Membros</h2>
                                <p class="text-xs text-gray-500">Evolução por trimestre no escopo selecionado</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-blue-700">
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span> Membros
                            </span>
                        </div>
                        <div class="h-48 relative">
                            <canvas id="membersChart"></canvas>
                        </div>
                    </article>

                    <article class="rounded-xl border border-gray-100 bg-gray-50/40 p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h2 class="text-sm font-black text-gray-900">Células Ativas</h2>
                                <p class="text-xs text-gray-500">Total de células em funcionamento por trimestre</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-indigo-700">
                                <span class="h-2 w-2 rounded-full bg-indigo-600"></span> Células
                            </span>
                        </div>
                        <div class="h-48 relative">
                            <canvas id="cellsChart"></canvas>
                        </div>
                    </article>

                    <article class="rounded-xl border border-gray-100 bg-gray-50/40 p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h2 class="text-sm font-black text-gray-900">Salvações por Trimestre</h2>
                                <p class="text-xs text-gray-500">Novas decisões para Cristo registadas</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                                <span class="h-2 w-2 rounded-full bg-emerald-600"></span> Salvações
                            </span>
                        </div>
                        <div class="h-48 relative">
                            <canvas id="savedChart"></canvas>
                        </div>
                    </article>

                    <article class="rounded-xl border border-gray-100 bg-gray-50/40 p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h2 class="text-sm font-black text-gray-900">Batismos por Trimestre</h2>
                                <p class="text-xs text-gray-500">Batismos em águas realizados no período</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-orange-700">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span> Batismos
                            </span>
                        </div>
                        <div class="h-48 relative">
                            <canvas id="baptizedChart"></canvas>
                        </div>
                    </article>
                </div>
            </div>

            <!-- TAB 2: COMPARAÇÃO DE PERÍODOS -->
            <div x-show="activeTab === 'comparison'" x-cloak class="p-4 sm:p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-gray-100">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Comparativo Direto: {{ $currentSummary['label'] }} vs {{ $compareSummary['label'] }}</h2>
                        <p class="text-xs text-gray-500">Variação absoluta e percentual entre os dois períodos selecionados (use o filtro "Comparar Com" acima para alterar).</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="rounded-md bg-orange-50 border border-orange-200 px-2.5 py-1 font-bold text-orange-800">
                            Atual: {{ $currentSummary['label'] }} ({{ $currentSummary['reports_count'] }} rel.)
                        </span>
                        <span class="rounded-md bg-gray-100 border border-gray-200 px-2.5 py-1 font-bold text-gray-700">
                            Base: {{ $compareSummary['label'] }} ({{ $compareSummary['reports_count'] }} rel.)
                        </span>
                    </div>
                </div>

                @php
                    $comparisonRows = [
                        ['label' => 'Membros em Comunhão', 'key' => 'members', 'icon' => 'bi-people', 'invert' => false],
                        ['label' => 'Visitantes Registados', 'key' => 'visitors', 'icon' => 'bi-person-plus', 'invert' => false],
                        ['label' => 'Participantes nos Cultos', 'key' => 'participants', 'icon' => 'bi-building', 'invert' => false],
                        ['label' => 'Células Ativas', 'key' => 'cells', 'icon' => 'bi-diagram-3', 'invert' => false],
                        ['label' => 'Líderes de Célula', 'key' => 'leaders', 'icon' => 'bi-person-badge', 'invert' => false],
                        ['label' => 'Auxiliares (Timóteos)', 'key' => 'timoteos', 'icon' => 'bi-person-check', 'invert' => false],
                        ['label' => 'Salvações', 'key' => 'saved', 'icon' => 'bi-heart-pulse', 'invert' => false],
                        ['label' => 'Batismos Planificados', 'key' => 'planned_baptism', 'icon' => 'bi-calendar-check', 'invert' => false],
                        ['label' => 'Batismos Realizados', 'key' => 'baptized', 'icon' => 'bi-droplet', 'invert' => false],
                        ['label' => 'Multiplicações de Células', 'key' => 'multiplications', 'icon' => 'bi-arrows-angle-expand', 'invert' => false],
                        ['label' => 'Células Fechadas', 'key' => 'closed_cells', 'icon' => 'bi-x-octagon', 'invert' => true],
                        ['label' => 'Líderes Disciplinados', 'key' => 'disciplined', 'icon' => 'bi-exclamation-triangle', 'invert' => true],
                    ];
                @endphp

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2.5">Indicador Ministerial</th>
                                <th class="px-4 py-2.5 text-right">{{ $compareSummary['label'] }} (Base)</th>
                                <th class="px-4 py-2.5 text-right">{{ $currentSummary['label'] }} (Atual)</th>
                                <th class="px-4 py-2.5 text-right">Diferença</th>
                                <th class="px-4 py-2.5 text-right">Variação (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($comparisonRows as $item)
                                @php
                                    $curVal = $currentSummary[$item['key']];
                                    $prevVal = $compareSummary[$item['key']];
                                    $d = $calcDelta($curVal, $prevVal);
                                    $isPositiveGood = !$item['invert'];
                                    $badgeClass = $d['diff'] === 0
                                        ? 'bg-gray-100 text-gray-600'
                                        : (($d['diff'] > 0 && $isPositiveGood) || ($d['diff'] < 0 && !$isPositiveGood)
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-rose-50 text-rose-700');
                                @endphp
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-2.5 font-bold text-gray-800">
                                        <i class="bi {{ $item['icon'] }} text-gray-400 mr-1.5"></i> {{ $item['label'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right tabular-nums text-gray-600">{{ number_format($prevVal) }}</td>
                                    <td class="px-4 py-2.5 text-right tabular-nums font-black text-gray-950">{{ number_format($curVal) }}</td>
                                    <td class="px-4 py-2.5 text-right tabular-nums font-bold {{ $d['diff'] > 0 ? ($isPositiveGood ? 'text-emerald-700' : 'text-rose-600') : ($d['diff'] < 0 ? ($isPositiveGood ? 'text-rose-600' : 'text-emerald-700') : 'text-gray-400') }}">
                                        {{ $d['diff'] > 0 ? '+' : '' }}{{ number_format($d['diff']) }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-black tabular-nums {{ $badgeClass }}">
                                            {{ $d['diff'] >= 0 ? '+' : '' }}{{ $d['pct'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: MOVIMENTOS & DINÂMICA CELULAR -->
            <div x-show="activeTab === 'movements'" x-cloak class="p-4 sm:p-5 space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-emerald-700">Multiplicações</p>
                        <p class="mt-1 text-xl font-black text-emerald-800 tabular-nums">+{{ number_format($currentSummary['multiplications']) }}</p>
                        <p class="text-[10px] text-emerald-600 mt-0.5">Novas células geradas</p>
                    </div>

                    <div class="rounded-xl border border-rose-200 bg-rose-50/40 p-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-rose-700">Células Fechadas</p>
                        <p class="mt-1 text-xl font-black text-rose-800 tabular-nums">-{{ number_format($currentSummary['closed_cells']) }}</p>
                        <p class="text-[10px] text-rose-600 mt-0.5">Desativadas no trimestre</p>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Saldo Líquido Células</p>
                        <p class="mt-1 text-xl font-black tabular-nums {{ $currentSummary['net_cells'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $currentSummary['net_cells'] >= 0 ? '+' : '' }}{{ number_format($currentSummary['net_cells']) }}
                        </p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Multiplicações vs Fechadas</p>
                    </div>

                    <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-amber-700">Líderes Disciplinados</p>
                        <p class="mt-1 text-xl font-black text-amber-800 tabular-nums">{{ number_format($currentSummary['disciplined']) }}</p>
                        <p class="text-[10px] text-amber-600 mt-0.5">Ocorrências disciplinares</p>
                    </div>

                    <div class="rounded-xl border border-blue-200 bg-blue-50/40 p-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-blue-700">Membros / Célula</p>
                        <p class="mt-1 text-xl font-black text-blue-900 tabular-nums">
                            {{ $currentSummary['cells'] > 0 ? number_format($currentSummary['members'] / $currentSummary['cells'], 1) : '0.0' }}
                        </p>
                        <p class="text-[10px] text-blue-600 mt-0.5">Média por célula ativa</p>
                    </div>

                    <div class="rounded-xl border border-orange-200 bg-orange-50/40 p-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Meta de Batismos</p>
                        <p class="mt-1 text-xl font-black text-orange-800 tabular-nums">
                            {{ $currentSummary['planned_baptism'] > 0 ? round(($currentSummary['baptized'] / $currentSummary['planned_baptism']) * 100) : 0 }}%
                        </p>
                        <p class="text-[10px] text-orange-600 mt-0.5">{{ $currentSummary['baptized'] }} de {{ $currentSummary['planned_baptism'] }} planificados</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <article class="rounded-xl border border-gray-100 bg-gray-50/40 p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h3 class="text-sm font-black text-gray-900">Dinâmica Celular: Multiplicações vs Fechamentos</h3>
                                <p class="text-xs text-gray-500">Histórico de abertura e fecho de células por trimestre</p>
                            </div>
                        </div>
                        <div class="h-48 relative">
                            <canvas id="movementsChart"></canvas>
                        </div>
                    </article>

                    <article class="rounded-xl border border-gray-100 bg-gray-50/40 p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h3 class="text-sm font-black text-gray-900">Funil de Evangelismo: Visitantes vs Salvações</h3>
                                <p class="text-xs text-gray-500">Relação entre fluxo de visitantes e decisões</p>
                            </div>
                        </div>
                        <div class="h-48 relative">
                            <canvas id="visitorsEvangelismChart"></canvas>
                        </div>
                    </article>
                </div>
            </div>

            <!-- TAB 4: SAÚDE MINISTERIAL (12 INDICADORES) -->
            <div x-show="activeTab === 'health'" x-cloak class="p-4 sm:p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-gray-100">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Diagnóstico de Saúde Ministerial ({{ $currentSummary['label'] }})</h2>
                        <p class="text-xs text-gray-500">Média dos 12 indicadores qualitativos (escala de 0 a 3) comparada com {{ $compareSummary['label'] }}.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-lg bg-gray-900 px-3 py-1 text-xs font-black text-white">
                            Índice Global: {{ $currentSummary['health_pct'] }}% ({{ number_format($currentSummary['health_avg'], 2) }} / 3.0)
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                    <!-- 12 Progress Bars with Comparison -->
                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($healthMetricsMap as $field => $label)
                            @php
                                $score = $currentSummary['health_scores'][$field] ?? 0;
                                $prevScore = $compareSummary['health_scores'][$field] ?? 0;
                                $diffScore = round($score - $prevScore, 2);
                                $pct = round(($score / 3) * 100);
                            @endphp
                            <div class="rounded-lg border border-gray-200 bg-gray-50/50 p-3">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <span class="text-xs font-bold text-gray-800 truncate">{{ $label }}</span>
                                    <div class="flex items-center gap-1.5">
                                        @if($diffScore != 0)
                                            <span class="text-[10px] font-black {{ $diffScore > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                                {{ $diffScore > 0 ? '+' : '' }}{{ $diffScore }}
                                            </span>
                                        @endif
                                        <span class="rounded px-1.5 py-0.5 text-[10px] font-black tabular-nums {{ $score >= 2.3 ? 'bg-emerald-100 text-emerald-800' : ($score >= 1.5 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ number_format($score, 1) }} / 3
                                        </span>
                                    </div>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-gray-200 overflow-hidden">
                                    <div class="h-full rounded-full {{ $score >= 2.3 ? 'bg-emerald-600' : ($score >= 1.5 ? 'bg-blue-600' : 'bg-amber-500') }}"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Health Evolution Chart -->
                    <article class="rounded-xl border border-gray-100 bg-gray-50/40 p-4">
                        <div class="mb-3">
                            <h3 class="text-sm font-black text-gray-900">Evolução do Índice de Saúde (%)</h3>
                            <p class="text-xs text-gray-500">Tendência trimestral da qualidade ministerial</p>
                        </div>
                        <div class="h-56 relative">
                            <canvas id="healthTrendChart"></canvas>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- Toolbar above List/Grid -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-600">
                    {{ $reports->total() }} {{ $reports->total() === 1 ? 'relatório listado' : 'relatórios listados' }}
                </span>
                <span class="text-[11px] text-gray-400 hidden sm:inline">• Clique numa Zona ou Supervisão na tabela para filtrar instantaneamente</span>
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

            <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
                <button type="button" @click="view = 'list'"
                    :class="view === 'list' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                    class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-list-ul"></i> Lista
                </button>
                <button type="button" @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                    class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-grid"></i> Cards
                </button>
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
                        <th class="px-4 py-3 text-center">Movimentos</th>
                        <th class="px-4 py-3 text-center">Saúde</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        @php
                            $healthPct = $report->health_percentage;
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-3.5 py-3 text-center">
                                <input type="checkbox" :value="{{ $report->id }}" x-model="selected"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('quarterly-reports.show', $report) }}" class="font-bold text-gray-900 hover:text-orange-700 hover:underline">
                                    {{ $report->quarter }}º Trim / {{ $report->year }}
                                </a>
                                <span class="block text-[11px] text-gray-400">
                                    {{ $report->submitted_at ? $report->submitted_at->format('d/m/Y') : $report->created_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($report->zone_id)
                                    <a href="{{ route('quarterly-reports.index', array_merge(request()->except('page'), ['zone_id' => $report->zone_id])) }}"
                                        title="Filtrar por esta Zona"
                                        class="font-bold text-gray-900 hover:text-blue-700 hover:underline">
                                        {{ $report->zone->name ?? 'Sem Zona' }}
                                    </a>
                                @else
                                    <p class="font-bold text-gray-900">Sem Zona</p>
                                @endif

                                @if($report->supervision)
                                    <a href="{{ route('quarterly-reports.index', array_merge(request()->except('page'), ['zone_id' => $report->zone_id, 'supervision_id' => $report->supervision_id])) }}"
                                        title="Filtrar histórico desta Supervisão"
                                        class="block text-xs text-gray-500 hover:text-orange-700 hover:underline font-medium">
                                        <i class="bi bi-funnel-fill text-[9px] text-orange-600 mr-0.5"></i>{{ $report->supervision->name }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold text-gray-700">{{ $report->supervisor->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums font-semibold text-gray-900">{{ number_format($report->cells_count) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-bold text-gray-900">{{ number_format($report->members_count) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-bold text-emerald-700">+{{ number_format($report->saved_count) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums font-bold text-orange-700">{{ number_format($report->baptized_count) }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold">
                                    <span class="text-emerald-700" title="Multiplicações">+{{ (int) $report->cell_multiplications_count }}</span>
                                    <span class="text-gray-300">/</span>
                                    <span class="text-rose-600" title="Células Fechadas">-{{ (int) $report->closed_cells_count }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-black tabular-nums {{ $healthPct >= 75 ? 'bg-emerald-100 text-emerald-800' : ($healthPct >= 50 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $healthPct }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1">
                                    <a href="{{ route('quarterly-reports.show', $report) }}"
                                        class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:border-orange-300 hover:text-orange-800 hover:bg-orange-50/50 transition"
                                        title="Detalhes">
                                        <i class="bi bi-eye text-orange-700"></i> <span class="hidden xl:inline">Ver</span>
                                    </a>
                                    @can('update', $report)
                                        <a href="{{ route('quarterly-reports.edit', $report) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-gray-600 hover:border-blue-300 hover:text-blue-700 transition"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $report)
                                        <form action="{{ route('quarterly-reports.destroy', $report) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este relatório?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-gray-500 hover:border-red-300 hover:text-red-600 transition"
                                                title="Excluir">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-12 text-center text-gray-500">
                                <i class="bi bi-clipboard-data text-2xl text-gray-300 block mb-2"></i>
                                Nenhum relatório encontrado para os filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($reports->isNotEmpty())
                    <tfoot class="border-t-2 border-gray-200 bg-gray-50 font-black text-xs">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-gray-700">Total da Página ({{ $reports->count() }} relatórios)</td>
                            <td class="px-4 py-3 text-right tabular-nums text-gray-900">{{ number_format($reports->sum('cells_count')) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-gray-900">{{ number_format($reports->sum('members_count')) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-emerald-700">+{{ number_format($reports->sum('saved_count')) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-orange-700">{{ number_format($reports->sum('baptized_count')) }}</td>
                            <td class="px-4 py-3 text-center tabular-nums">
                                <span class="text-emerald-700">+{{ $reports->sum('cell_multiplications_count') }}</span> /
                                <span class="text-rose-600">-{{ $reports->sum('closed_cells_count') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums text-gray-800">
                                {{ round($reports->avg('health_percentage')) }}%
                            </td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </section>

        <!-- GRID VIEW (National Cards Style) -->
        <section x-show="view === 'grid'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @forelse($reports as $report)
                @php
                    $healthPct = $report->health_percentage;
                @endphp
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-orange-300 transition flex flex-col justify-between"
                    :class="{ 'ring-2 ring-orange-500 border-orange-500': selected.includes({{ $report->id }}) }">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <input type="checkbox" :value="{{ $report->id }}" x-model="selected"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                                <div class="min-w-0">
                                    <a href="{{ route('quarterly-reports.index', array_merge(request()->except('page'), ['zone_id' => $report->zone_id, 'supervision_id' => $report->supervision_id])) }}"
                                        class="text-sm font-black text-gray-900 hover:text-orange-700 truncate block">
                                        {{ $report->supervision->name ?? ($report->zone->name ?? 'Sem Zona') }}
                                    </a>
                                    <p class="text-[11px] font-bold text-orange-700">
                                        {{ $report->quarter }}º Trimestre / {{ $report->year }} • {{ $report->zone->name ?? '' }}
                                    </p>
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-black {{ $healthPct >= 75 ? 'bg-emerald-100 text-emerald-800' : ($healthPct >= 50 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                Saúde {{ $healthPct }}%
                            </span>
                        </div>

                        <p class="mt-2 text-xs text-gray-500 truncate">
                            <i class="bi bi-person text-gray-400 mr-1"></i> {{ $report->supervisor->name ?? 'N/A' }}
                        </p>

                        <div class="mt-3 grid grid-cols-2 gap-1.5 rounded-lg bg-gray-50 p-2.5 text-xs">
                            <div>
                                <span class="text-[10px] text-gray-400 block">Membros</span>
                                <strong class="text-gray-900 tabular-nums">{{ number_format($report->members_count) }}</strong>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 block">Células</span>
                                <strong class="text-gray-900 tabular-nums">{{ number_format($report->cells_count) }}</strong>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 block">Salvações</span>
                                <strong class="text-emerald-700 tabular-nums">+{{ number_format($report->saved_count) }}</strong>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 block">Batismos</span>
                                <strong class="text-orange-700 tabular-nums">{{ number_format($report->baptized_count) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between gap-2">
                        <span class="text-[11px] font-bold text-gray-500">
                            Mov: <span class="text-emerald-700">+{{ (int)$report->cell_multiplications_count }}</span> / <span class="text-rose-600">-{{ (int)$report->closed_cells_count }}</span>
                        </span>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('quarterly-reports.show', $report) }}"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:border-orange-300 hover:text-orange-800">
                                <i class="bi bi-eye text-orange-700"></i> Detalhes
                            </a>
                            @can('update', $report)
                                <a href="{{ route('quarterly-reports.edit', $report) }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1 text-xs text-gray-600 hover:border-blue-300 hover:text-blue-700">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-gray-200 bg-white p-10 text-center text-gray-500">
                    Nenhum relatório encontrado para os filtros aplicados.
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
        document.addEventListener('DOMContentLoaded', function() {
            const labels = @json($chartLabels);
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 12 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 10, weight: '600' }, color: '#6b7280' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '600' }, color: '#6b7280' }
                    }
                }
            };

            const initSingleChart = (id, type, data, color, bgColor) => {
                const el = document.getElementById(id);
                if (!el) return;
                new Chart(el.getContext('2d'), {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            borderColor: color,
                            backgroundColor: bgColor,
                            borderWidth: 2,
                            fill: type === 'line',
                            tension: 0.35,
                            borderRadius: type === 'bar' ? 6 : 0,
                            pointRadius: 3
                        }]
                    },
                    options: commonOptions
                });
            };

            initSingleChart('membersChart', 'line', @json($membersData), '#2563eb', 'rgba(37, 99, 235, 0.08)');
            initSingleChart('cellsChart', 'bar', @json($cellsData), '#4f46e5', '#4f46e5');
            initSingleChart('savedChart', 'line', @json($savedData), '#059669', 'rgba(5, 150, 105, 0.08)');
            initSingleChart('baptizedChart', 'bar', @json($baptizedData), '#ea580c', '#ea580c');
            initSingleChart('healthTrendChart', 'line', @json($healthTrendData), '#7c3aed', 'rgba(124, 58, 237, 0.1)');

            // Dual series chart: Movements (Multiplications vs Closed Cells)
            const movEl = document.getElementById('movementsChart');
            if (movEl) {
                new Chart(movEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Multiplicações',
                                data: @json($multiplicationsData),
                                backgroundColor: '#059669',
                                borderRadius: 5
                            },
                            {
                                label: 'Células Fechadas',
                                data: @json($closedCellsData),
                                backgroundColor: '#e11d48',
                                borderRadius: 5
                            }
                        ]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            legend: { display: true, labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } }
                        }
                    }
                });
            }

            // Dual series chart: Visitors vs Saved
            const visEl = document.getElementById('visitorsEvangelismChart');
            if (visEl) {
                new Chart(visEl.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Visitantes',
                                data: @json($visitorsData),
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                                borderWidth: 2,
                                tension: 0.35,
                                fill: true
                            },
                            {
                                label: 'Salvações',
                                data: @json($savedData),
                                borderColor: '#059669',
                                backgroundColor: 'rgba(5, 150, 105, 0.08)',
                                borderWidth: 2,
                                tension: 0.35,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            legend: { display: true, labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } }
                        }
                    }
                });
            }
        });
    </script>
@endsection
