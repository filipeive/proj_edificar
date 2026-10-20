@extends('layouts.app')

@section('title', 'Detalhes do Relatório Trimestral - Portal Life Church')
@section('page-title', 'Detalhes do Relatório')
@section('page-subtitle', "{$quarterlyReport->quarter}º Trimestre de {$quarterlyReport->year}")

@section('header-actions')
    <div class="flex items-center gap-1.5 md:hidden">
        @can('update', $quarterlyReport)
            <a href="{{ route('quarterly-reports.edit', $quarterlyReport) }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-xs text-gray-700 shadow-sm hover:bg-gray-50"
                title="Editar dados">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endcan
        <a href="{{ route('quarterly-reports.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-xs text-gray-700 shadow-sm hover:bg-gray-50"
            title="Voltar">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Header (National Style) -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <a href="{{ route('quarterly-reports.index') }}" class="text-xs font-bold text-orange-800 hover:underline">
                    <i class="bi bi-arrow-left mr-1"></i>Relatórios Trimestrais
                </a>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-950 tracking-tight">
                        {{ $quarterlyReport->quarter }}º Trimestre / {{ $quarterlyReport->year }}
                    </h1>
                    <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase {{ $quarterlyReport->status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $quarterlyReport->status === 'submitted' ? 'Submetido' : 'Rascunho' }}
                    </span>
                </div>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                    Supervisão: <span class="font-bold text-gray-800">{{ $quarterlyReport->supervision->name ?? 'N/A' }}</span>
                    • Zona: <span class="font-bold text-gray-800">{{ $quarterlyReport->zone->name ?? 'N/A' }}</span>
                    • Supervisor: <span class="font-bold text-gray-800">{{ $quarterlyReport->supervisor->name ?? 'N/A' }}</span>
                </p>
            </div>

            <div class="hidden md:flex flex-wrap items-center gap-2">
                @if($quarterlyReport->supervision_id)
                    <a href="{{ route('quarterly-reports.index', ['zone_id' => $quarterlyReport->zone_id, 'supervision_id' => $quarterlyReport->supervision_id]) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-orange-200 bg-orange-50 px-3.5 py-2 text-xs font-bold text-orange-800 hover:bg-orange-100 shadow-sm transition">
                        <i class="bi bi-graph-up-arrow"></i> Histórico da Supervisão
                    </a>
                @endif
                @can('update', $quarterlyReport)
                    <a href="{{ route('quarterly-reports.edit', $quarterlyReport) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm transition">
                        <i class="bi bi-pencil-square"></i> Editar Dados
                    </a>
                @endcan
                <a href="{{ route('quarterly-reports.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </header>

        <!-- Supervision Period Switcher -->
        @if(isset($supervisionReports) && $supervisionReports->count() > 1)
            <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 shadow-sm">
                <span class="text-xs font-bold text-gray-500">
                    <i class="bi bi-clock-history text-orange-700 mr-1"></i> Outros períodos de <strong class="text-gray-800">{{ $quarterlyReport->supervision->name ?? 'Supervisão' }}</strong>:
                </span>
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach($supervisionReports as $sRep)
                        <a href="{{ route('quarterly-reports.show', $sRep) }}"
                            class="rounded-lg px-2.5 py-1 text-xs font-bold transition {{ $sRep->id === $quarterlyReport->id ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-orange-50 hover:text-orange-800' }}">
                            {{ $sRep->quarter }}º Trim / {{ $sRep->year }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Primary KPI Grid (National Style) -->
        <section aria-label="Métricas de crescimento e estrutura">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <h3 class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-gray-500">Métricas de Crescimento e Estrutura</h3>
                @if(isset($previousReport) && $previousReport)
                    <span class="text-[11px] font-semibold text-gray-500">
                        Comparado com <strong class="text-gray-800">{{ $previousReport->quarter }}º Trim / {{ $previousReport->year }}</strong>
                    </span>
                @endif
            </div>
            @php
                $stats = [
                    ['label' => 'Pastores', 'field' => 'pastors_count', 'value' => $quarterlyReport->pastors_count, 'icon' => 'bi-person-workspace', 'color' => 'text-rose-700', 'invert' => false],
                    ['label' => 'Supervisores', 'field' => 'supervisors_count', 'value' => $quarterlyReport->supervisors_count, 'icon' => 'bi-person-check', 'color' => 'text-purple-700', 'invert' => false],
                    ['label' => 'Líderes Ativos', 'field' => 'leaders_count', 'value' => $quarterlyReport->leaders_count, 'icon' => 'bi-person-badge', 'color' => 'text-blue-700', 'invert' => false],
                    ['label' => 'Timóteos (Aux.)', 'field' => 'timoteos_count', 'value' => $quarterlyReport->timoteos_count, 'icon' => 'bi-award', 'color' => 'text-indigo-700', 'invert' => false],
                    ['label' => 'Células Totais', 'field' => 'cells_count', 'value' => $quarterlyReport->cells_count, 'icon' => 'bi-diagram-3', 'color' => 'text-indigo-700', 'invert' => false],
                    ['label' => 'Membros', 'field' => 'members_count', 'value' => $quarterlyReport->members_count, 'icon' => 'bi-people', 'color' => 'text-emerald-700', 'invert' => false],
                    ['label' => 'Visitantes', 'field' => 'visitors_count', 'value' => $quarterlyReport->visitors_count, 'icon' => 'bi-person-plus', 'color' => 'text-amber-700', 'invert' => false],
                    ['label' => 'Participantes', 'field' => 'participants_count', 'value' => $quarterlyReport->participants_count, 'icon' => 'bi-graph-up', 'color' => 'text-orange-700', 'invert' => false],
                    ['label' => 'Salvações', 'field' => 'saved_count', 'value' => $quarterlyReport->saved_count, 'icon' => 'bi-heart-pulse', 'color' => 'text-rose-700', 'invert' => false],
                    ['label' => 'Multiplicações', 'field' => 'cell_multiplications_count', 'value' => $quarterlyReport->cell_multiplications_count, 'icon' => 'bi-arrows-angle-expand', 'color' => 'text-teal-700', 'invert' => false],
                    ['label' => 'Líderes Discip.', 'field' => 'disciplined_leaders_count', 'value' => $quarterlyReport->disciplined_leaders_count, 'icon' => 'bi-shield-exclamation', 'color' => 'text-gray-700', 'invert' => true],
                    ['label' => 'Células Fechadas', 'field' => 'closed_cells_count', 'value' => $quarterlyReport->closed_cells_count, 'icon' => 'bi-x-octagon', 'color' => 'text-gray-600', 'invert' => true],
                ];
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3">
                @foreach($stats as $stat)
                    @php
                        $diff = isset($previousReport) && $previousReport ? ((int)$stat['value'] - (int)$previousReport->{$stat['field']}) : null;
                        $good = !$stat['invert'];
                    @endphp
                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-3.5 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] font-bold text-gray-500 truncate">{{ $stat['label'] }}</p>
                            <i class="bi {{ $stat['icon'] }} {{ $stat['color'] }} text-sm"></i>
                        </div>
                        <div class="mt-1 flex items-baseline justify-between gap-1">
                            <p class="text-lg sm:text-xl font-black text-gray-950 tracking-tight tabular-nums">{{ number_format($stat['value']) }}</p>
                            @if($diff !== null && $diff !== 0)
                                <span class="rounded-full px-1.5 py-0.5 text-[10px] font-black tabular-nums {{ ($diff > 0 && $good) || ($diff < 0 && !$good) ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                </span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
            <!-- Left Column -->
            <div class="xl:col-span-8 space-y-4">
                <!-- Health Indicators -->
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-2 pb-3 mb-4 border-b border-gray-100">
                        <div>
                            <h2 class="text-sm font-black text-gray-900">Avaliação de Saúde Ministerial</h2>
                            <p class="text-xs text-gray-500">Indicadores qualitativos avaliados pelo supervisor ({{ $quarterlyReport->health_total_score }}/36 pts)</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-md px-2.5 py-1 text-xs font-black {{ $quarterlyReport->health_percentage >= 75 ? 'bg-emerald-100 text-emerald-800' : ($quarterlyReport->health_percentage >= 50 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                Saúde: {{ $quarterlyReport->health_percentage }}%
                            </span>
                            <span class="rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-gray-600">Escala 0 a 3</span>
                        </div>
                    </div>

                    @php
                        $assessments = [
                            'discipleship_score' => 'Discipulado um a um',
                            'evangelism_strategy' => 'Estratégia de Evangelismo',
                            'consolidation_growth' => 'Consolidação de Novos',
                            'pastoral_score' => 'Cuidado Pastoral (Líderes)',
                            'visitation_routine' => 'Rotina de Visitação',
                            'leader_support' => 'Apoio aos Líderes',
                            'cell_participation_score' => 'Participação em Células',
                            'service_participation_score' => 'Presença nos Cultos',
                            'tadium_participation' => 'Envolvimento no TADEL',
                            'communion_in_cells_score' => 'Comunhão Interna',
                            'relationship_building_score' => 'Integração de Novos',
                            'prayer_intercession_score' => 'Vida de Oração',
                        ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3.5">
                        @foreach($assessments as $field => $label)
                            @php
                                $score = (int) $quarterlyReport->$field;
                                $barColor = match($score) {
                                    3 => 'bg-emerald-600',
                                    2 => 'bg-blue-600',
                                    1 => 'bg-amber-500',
                                    default => 'bg-rose-500',
                                };
                            @endphp
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-bold text-gray-700">{{ $label }}</span>
                                    <span class="font-black text-gray-900 tabular-nums">{{ $score }}<span class="text-[10px] text-gray-400 font-semibold">/3</span></span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $barColor }} rounded-full transition-all duration-500"
                                        style="width: {{ ($score / 3) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Charts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                        <div class="mb-3">
                            <h2 class="text-sm font-black text-gray-900">Saúde Ministerial (Radar)</h2>
                            <p class="text-xs text-gray-500">Pontuação vs meta mínima (2/3)</p>
                        </div>
                        <div class="h-60 relative">
                            <canvas id="healthRadarChart"></canvas>
                        </div>
                    </article>

                    <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                        <div class="mb-3">
                            <h2 class="text-sm font-black text-gray-900">Estrutura da Supervisão</h2>
                            <p class="text-xs text-gray-500">Distribuição de líderes, células e membros</p>
                        </div>
                        <div class="h-60 relative">
                            <canvas id="structureBarChart"></canvas>
                        </div>
                    </article>
                </div>

                <!-- Observations -->
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <h2 class="text-sm font-black text-gray-900 mb-2 flex items-center gap-1.5">
                        <i class="bi bi-journal-text text-orange-700"></i>
                        Parecer Ministerial do Supervisor
                    </h2>
                    <div class="rounded-lg bg-gray-50 border border-gray-200/80 p-3.5 text-xs sm:text-sm text-gray-700 leading-relaxed">
                        {!! nl2br(e($quarterlyReport->ministerial_observations ?: 'Nenhuma observação adicional registada neste período.')) !!}
                    </div>
                </section>
            </div>

            <!-- Right Column -->
            <div class="xl:col-span-4 space-y-4">
                <!-- Baptism Target Card -->
                @php
                    $target = max(1, (int) $quarterlyReport->planned_baptism_count);
                    $perc = $quarterlyReport->planned_baptism_count > 0
                        ? min(100, round(($quarterlyReport->baptized_count / $target) * 100))
                        : ($quarterlyReport->baptized_count > 0 ? 100 : 0);
                @endphp
                <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Alvo de Batismos</p>
                            <h3 class="text-sm font-black text-gray-900">Batismos em Águas</h3>
                        </div>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-orange-50 text-orange-700 border border-orange-200/60">
                            <i class="bi bi-droplet-half text-base"></i>
                        </span>
                    </div>

                    <div class="flex items-baseline justify-between mb-2">
                        <p class="text-2xl font-black text-gray-950 tabular-nums">
                            {{ number_format($quarterlyReport->baptized_count) }}
                            <span class="text-xs font-semibold text-gray-400">/ {{ number_format($quarterlyReport->planned_baptism_count) }} planificados</span>
                        </p>
                        <span class="rounded-full bg-orange-50 px-2 py-0.5 text-[11px] font-black text-orange-800">{{ $perc }}%</span>
                    </div>

                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-600 rounded-full transition-all duration-500" style="width: {{ $perc }}%"></div>
                    </div>
                </article>

                <!-- Events Activity -->
                <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="pb-3 mb-3 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-900">Eventos e Cerimónias</h3>
                        <p class="text-xs text-gray-500">Atividades realizadas no trimestre</p>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($quarterlyReport->events as $event)
                            <div class="flex items-start gap-3 rounded-lg bg-gray-50 p-2.5 border border-gray-100">
                                <span class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md bg-indigo-50 text-xs font-black text-indigo-700 border border-indigo-200/60 tabular-nums">
                                    {{ $event->count }}
                                </span>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-gray-900 truncate">{{ $event->eventType->name ?? 'Evento' }}</h4>
                                    <p class="text-[11px] text-gray-500 leading-snug">
                                        {{ $event->description ?: 'Atividade concluída sem observações.' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-center text-xs text-gray-400">Nenhum evento registado neste trimestre.</p>
                        @endforelse
                    </div>
                </article>

                <!-- Submission Audit -->
                <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-500 mb-3">Registo de Submissão</h3>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-orange-600 text-sm font-black text-white">
                            {{ strtoupper(substr($quarterlyReport->supervisor->name ?? 'S', 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-black text-gray-900 truncate">{{ $quarterlyReport->supervisor->name ?? 'Supervisor do Sistema' }}</p>
                            <p class="text-xs text-gray-500">
                                <i class="bi bi-clock-history mr-1"></i>
                                {{ $quarterlyReport->submitted_at ? $quarterlyReport->submitted_at->format('d/m/Y \à\s H:i') : $quarterlyReport->created_at->format('d/m/Y \à\s H:i') }}
                            </p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const radarCtx = document.getElementById('healthRadarChart').getContext('2d');
                new Chart(radarCtx, {
                    type: 'radar',
                    data: {
                        labels: [
                            'Discipulado', 'Pastoreio', 'Freq. Células',
                            'Freq. Cultos', 'Comunhão', 'Consolidação', 'Oração'
                        ],
                        datasets: [{
                            label: 'Pontuação',
                            data: [
                                {{ $quarterlyReport->discipleship_score }},
                                {{ $quarterlyReport->pastoral_score }},
                                {{ $quarterlyReport->cell_participation_score }},
                                {{ $quarterlyReport->service_participation_score }},
                                {{ $quarterlyReport->communion_in_cells_score }},
                                {{ $quarterlyReport->relationship_building_score }},
                                {{ $quarterlyReport->prayer_intercession_score }}
                            ],
                            backgroundColor: 'rgba(194, 65, 12, 0.15)',
                            borderColor: '#c2410c',
                            pointBackgroundColor: '#c2410c',
                            pointBorderColor: '#fff',
                        }, {
                            label: 'Meta Mínima',
                            data: [2, 2, 2, 2, 2, 2, 2],
                            backgroundColor: 'rgba(156, 163, 175, 0.08)',
                            borderColor: 'rgba(156, 163, 175, 0.6)',
                            borderDash: [4, 4],
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 3,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                const barCtx = document.getElementById('structureBarChart').getContext('2d');
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Líderes', 'Células', 'Timóteos', 'Membros'],
                        datasets: [{
                            label: 'Quantidade',
                            data: [
                                {{ $quarterlyReport->leaders_count }},
                                {{ $quarterlyReport->cells_count }},
                                {{ $quarterlyReport->timoteos_count }},
                                {{ $quarterlyReport->members_count }}
                            ],
                            backgroundColor: [
                                'rgba(29, 78, 216, 0.75)',
                                'rgba(79, 70, 229, 0.75)',
                                'rgba(109, 40, 217, 0.75)',
                                'rgba(5, 150, 105, 0.75)'
                            ],
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
