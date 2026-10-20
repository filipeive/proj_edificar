@extends('layouts.app')

@section('title', 'Editar Relatório Trimestral - Portal Life Church')
@section('page-title', 'Editar Relatório Trimestral')
@section('page-subtitle', 'Atualize as informações e métricas do relatório')

@section('content')
    <div class="w-full space-y-5" x-data="reportForm()">
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-3.5 text-xs sm:text-sm text-red-800 shadow-sm">
                <div class="flex items-start gap-2.5">
                    <i class="bi bi-exclamation-triangle-fill text-red-600 mt-0.5"></i>
                    <div>
                        <p class="font-black uppercase tracking-wider text-xs text-red-800">Existem erros no formulário</p>
                        <ul class="list-disc pl-4 mt-1 space-y-0.5 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header (National Style) -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <a href="{{ route('quarterly-reports.index') }}" class="text-xs font-bold text-orange-800 hover:underline">
                    <i class="bi bi-arrow-left mr-1"></i>Relatórios Trimestrais
                </a>
                <h1 class="mt-1 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">
                    Editar Relatório #{{ $report->id }} ({{ $report->quarter }}º Trim / {{ $report->year }})
                </h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                    Supervisão: <span class="font-bold text-gray-800">{{ $report->supervision->name ?? 'N/A' }}</span>
                    • Zona: <span class="font-bold text-gray-800">{{ $report->zone->name ?? 'N/A' }}</span>
                </p>
            </div>
            <div class="flex items-center justify-between md:justify-end gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-500">Passo <span x-text="step"></span>/4</span>
                    <div class="flex gap-1">
                        <template x-for="i in 4">
                            <button type="button" @click="step = i"
                                class="h-2 w-6 rounded-full transition-all"
                                :class="step >= i ? 'bg-orange-700' : 'bg-gray-200'"></button>
                        </template>
                    </div>
                </div>
                <a href="{{ route('quarterly-reports.show', $report) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </header>

        <form action="{{ route('quarterly-reports.update', $report) }}" method="POST" id="reportForm" class="space-y-5 pb-8">
            @csrf
            @method('PUT')

            <!-- STEP 1: IDENTIFICATION & STATS -->
            <div x-show="step === 1" x-transition class="space-y-4">
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-orange-700 text-xs font-black text-white">1</span>
                            <div>
                                <h2 class="text-sm font-black text-gray-900">Identificação e Estatísticas Gerais</h2>
                                <p class="text-xs text-gray-500">Zona, supervisão e quantitativos do trimestre</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Zona *</label>
                            <select name="zone_id" required x-model="zoneId" @change="updateSupervisions()" data-searchable="false"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                <option value="">Selecione a Zona</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Supervisão *</label>
                            <select name="supervision_id" required x-model="supervisionId" data-searchable="false"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                <option value="">Selecione a Supervisão</option>
                                <template x-for="sup in filteredSupervisions" :key="sup.id">
                                    <option :value="sup.id" x-text="sup.name" :selected="sup.id == supervisionId"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Ano *</label>
                            <input type="number" name="year" value="{{ old('year', $report->year) }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                        </div>

                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Trimestre *</label>
                            <select name="quarter" required data-searchable="false"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                <option value="1" @selected(old('quarter', $report->quarter) == 1)>1º Trimestre</option>
                                <option value="2" @selected(old('quarter', $report->quarter) == 2)>2º Trimestre</option>
                                <option value="3" @selected(old('quarter', $report->quarter) == 3)>3º Trimestre</option>
                                <option value="4" @selected(old('quarter', $report->quarter) == 4)>4º Trimestre</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-2.5">Métricas da Estrutura</h3>
                        @php
                            $stats = [
                                'pastors_count' => ['icon' => 'bi-person-workspace', 'label' => 'Pastores', 'color' => 'text-rose-700'],
                                'supervisors_count' => ['icon' => 'bi-person-check', 'label' => 'Supervisores', 'color' => 'text-purple-700'],
                                'leaders_count' => ['icon' => 'bi-person-badge', 'label' => 'Líderes', 'color' => 'text-blue-700'],
                                'timoteos_count' => ['icon' => 'bi-award', 'label' => 'Auxiliares', 'color' => 'text-indigo-700'],
                                'members_count' => ['icon' => 'bi-people', 'label' => 'Membros', 'color' => 'text-emerald-700'],
                                'visitors_count' => ['icon' => 'bi-person-plus', 'label' => 'Visitantes', 'color' => 'text-amber-700'],
                                'saved_count' => ['icon' => 'bi-heart-pulse', 'label' => 'Salvações', 'color' => 'text-rose-700'],
                                'cells_count' => ['icon' => 'bi-diagram-3', 'label' => 'Células', 'color' => 'text-indigo-700'],
                                'participants_count' => ['icon' => 'bi-graph-up', 'label' => 'Participantes', 'color' => 'text-orange-700'],
                            ];
                        @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-9 gap-2.5">
                            @foreach($stats as $field => $data)
                                <div class="rounded-lg border border-gray-200 bg-gray-50/70 p-2.5">
                                    <label class="mb-1 flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-gray-500 truncate">
                                        <i class="bi {{ $data['icon'] }} {{ $data['color'] }}"></i>
                                        <span class="truncate">{{ $data['label'] }}</span>
                                    </label>
                                    <input type="number" name="{{ $field }}" value="{{ old($field, $report->$field) }}" min="0" required
                                        class="w-full rounded-md border border-gray-300 bg-white px-2 py-1.5 text-center text-sm font-black text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="button" @click="nextStep()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-5 py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm transition">
                        <span>Próximo Passo</span> <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: MINISTERIAL RESULTS & EVENTS -->
            <div x-show="step === 2" x-cloak x-transition class="space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                        <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-orange-700 text-xs font-black text-white">2</span>
                            <div>
                                <h2 class="text-sm font-black text-gray-900">Alvos e Resultados</h2>
                                <p class="text-xs text-gray-500">Batismos, multiplicações e movimento celular</p>
                            </div>
                        </div>

                        @php
                            $results = [
                                'planned_baptism_count' => 'Batismos Planificados',
                                'baptized_count' => 'Batismos Realizados',
                                'cell_multiplications_count' => 'Multiplicações de Célula',
                                'disciplined_leaders_count' => 'Líderes Disciplinados',
                                'closed_cells_count' => 'Células Fechadas',
                            ];
                        @endphp
                        <div class="space-y-2">
                            @foreach($results as $field => $label)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50/70 px-3.5 py-2">
                                    <span class="text-xs sm:text-sm font-bold text-gray-700">{{ $label }}</span>
                                    <input type="number" name="{{ $field }}" value="{{ old($field, $report->$field) }}" min="0" required
                                        class="w-20 rounded-md border border-gray-300 bg-white px-2.5 py-1 text-center text-sm font-black text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                        <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                            <i class="bi bi-calendar-event text-indigo-700 text-base"></i>
                            <div>
                                <h2 class="text-sm font-black text-gray-900">Eventos e Cerimónias</h2>
                                <p class="text-xs text-gray-500">Quantidade e observações por tipo de evento</p>
                            </div>
                        </div>

                        <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
                            @foreach($eventTypes as $index => $type)
                                @php
                                    $event = $report->events->where('event_type_id', $type->id)->first();
                                @endphp
                                <div class="rounded-lg border border-gray-200 bg-gray-50/70 p-3 space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-bold text-gray-800">{{ $type->name }}</span>
                                        <input type="hidden" name="events[{{ $index }}][event_type_id]" value="{{ $type->id }}">
                                        <input type="number" name="events[{{ $index }}][count]"
                                            value="{{ old("events.{$index}.count", $event ? $event->count : 0) }}" min="0"
                                            class="w-16 rounded-md border border-gray-300 bg-white px-2 py-1 text-center text-xs font-black text-indigo-700">
                                    </div>
                                    <input type="text" name="events[{{ $index }}][description]"
                                        value="{{ old("events.{$index}.description", $event ? $event->description : '') }}"
                                        placeholder="Observações específicas (opcional)..."
                                        class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-xs text-gray-700">
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" @click="step--"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-arrow-left"></i> Anterior
                    </button>
                    <button type="button" @click="nextStep()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-5 py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                        <span>Próximo Passo</span> <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: HEALTH INDICATORS -->
            <div x-show="step === 3" x-cloak x-transition class="space-y-4">
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-orange-700 text-xs font-black text-white">3</span>
                            <div>
                                <h2 class="text-sm font-black text-gray-900">Indicadores de Saúde Ministerial</h2>
                                <p class="text-xs text-gray-500">Avalie de 0 (Fraco) a 3 (Excelente)</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>0 Fraco</span>
                            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>1 Regular</span>
                            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>2 Bom</span>
                            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>3 Excelente</span>
                        </div>
                    </div>

                    @php
                        $sections = [
                            'discipleship' => [
                                'title' => 'Discipulado e Evangelismo',
                                'icon' => 'bi-chat-heart',
                                'questions' => [
                                    'discipleship_score' => 'Como está o discipulado um a um na supervisão?',
                                    'evangelism_strategy' => 'Existe uma estratégia clara de evangelismo (GEs)?',
                                    'consolidation_growth' => 'Os novos convertidos estão sendo consolidados?',
                                ]
                            ],
                            'pastoral' => [
                                'title' => 'Cuidado Pastoral',
                                'icon' => 'bi-heart-pulse',
                                'questions' => [
                                    'pastoral_score' => 'Qualidade do cuidado pastoral aos líderes?',
                                    'visitation_routine' => 'A rotina de visitação está sendo cumprida?',
                                    'leader_support' => 'Os líderes se sentem apoiados emocional e espiritualmente?',
                                ]
                            ],
                            'participation' => [
                                'title' => 'Participação e Frequência',
                                'icon' => 'bi-graph-up',
                                'questions' => [
                                    'cell_participation_score' => 'Participação média nas reuniões de célula?',
                                    'service_participation_score' => 'Presença dos membros nos cultos de celebração?',
                                    'tadium_participation' => 'Envolvimento dos líderes no TADEL / Reuniões de Liderança?',
                                ]
                            ],
                            'relationship' => [
                                'title' => 'Comunhão e Relacionamentos',
                                'icon' => 'bi-people',
                                'questions' => [
                                    'communion_in_cells_score' => 'Nível de comunhão interna nas células?',
                                    'relationship_building_score' => 'Os novos se sentem integrados à família da igreja?',
                                    'prayer_intercession_score' => 'A vida de oração e intercessão do grupo?',
                                ]
                            ]
                        ];
                    @endphp

                    <div class="space-y-5">
                        @foreach($sections as $section)
                            <div class="space-y-2.5">
                                <h3 class="text-xs font-black uppercase tracking-wider text-gray-500 flex items-center gap-1.5">
                                    <i class="bi {{ $section['icon'] }} text-orange-700"></i>
                                    {{ $section['title'] }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    @foreach($section['questions'] as $field => $question)
                                        <div class="rounded-lg border border-gray-200 bg-gray-50/70 p-3 flex flex-col justify-between gap-2.5">
                                            <p class="text-xs font-bold text-gray-800 leading-snug">{{ $question }}</p>
                                            <div class="grid grid-cols-4 gap-1.5">
                                                @for($i = 0; $i <= 3; $i++)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="{{ $field }}" value="{{ $i }}" class="sr-only peer"
                                                            required @checked(old($field, $report->$field) == $i)>
                                                        <div class="py-1.5 text-center rounded-md bg-white border border-gray-200 text-xs font-black text-gray-500 peer-checked:bg-orange-700 peer-checked:text-white peer-checked:border-orange-700 transition">
                                                            {{ $i }}
                                                        </div>
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="flex items-center justify-between">
                    <button type="button" @click="step--"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-arrow-left"></i> Anterior
                    </button>
                    <button type="button" @click="nextStep()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-5 py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                        <span>Próximo Passo</span> <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 4: OBSERVATIONS & SUBMIT -->
            <div x-show="step === 4" x-cloak x-transition class="space-y-4">
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                    <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-orange-700 text-xs font-black text-white">4</span>
                        <div>
                            <h2 class="text-sm font-black text-gray-900">Conclusão e Observações Ministeriais</h2>
                            <p class="text-xs text-gray-500">Desafios superados, testemunhos e observações do trimestre</p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Relato Descritivo do Trimestre</label>
                        <textarea name="ministerial_observations" rows="6"
                            placeholder="Descreva os principais desafios, vitórias alcançadas e observações pastorais desta supervisão..."
                            class="w-full rounded-lg border border-gray-300 p-3 text-xs sm:text-sm text-gray-800 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">{{ old('ministerial_observations', $report->ministerial_observations) }}</textarea>
                    </div>
                </section>

                <div class="flex items-center justify-between">
                    <button type="button" @click="step--"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-arrow-left"></i> Revisar
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-orange-700 px-6 py-2.5 text-xs sm:text-sm font-black uppercase tracking-wider text-white hover:bg-orange-800 shadow-sm transition">
                        <i class="bi bi-check2-circle"></i> Guardar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function reportForm() {
            return {
                step: 1,
                zoneId: '{{ old('zone_id', $report->zone_id) }}',
                supervisionId: '{{ old('supervision_id', $report->supervision_id) }}',
                zones: {{ Js::from($zones) }},
                supervisions: {{ Js::from($supervisions) }},
                filteredSupervisions: [],

                init() {
                    this.updateSupervisions();
                },

                updateSupervisions() {
                    if (!this.zoneId) {
                        this.filteredSupervisions = [];
                        return;
                    }
                    this.filteredSupervisions = this.supervisions.filter(s => Number(s.zone_id) === Number(this.zoneId));
                },

                nextStep() {
                    this.step++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }
    </script>
@endsection
