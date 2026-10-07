@extends('layouts.auth')

@section('title', 'Relatório Trimestral - Edificar Life')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background-color: #f8fafc !important;
            color: #0f172a;
        }
        .custom-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1.25rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 3rem;
        }
    </style>

    <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8 relative text-slate-900">
        <div class="max-w-6xl mx-auto space-y-8 relative z-10" x-data="{
                    step: 1,
                    zoneId: '',
                    supervisionId: '',
                    zones: {{ Js::from($zones) }},
                    supervisions: {{ Js::from($supervisions) }},
                    filteredSupervisions: [],
                    init() {
                        if (this.zones.length === 1) {
                            this.zoneId = this.zones[0].id;
                            this.$nextTick(() => this.updateSupervisions());
                        } else {
                            this.updateSupervisions();
                        }
                    },
                    updateSupervisions() {
                        if (!this.zoneId) {
                            this.filteredSupervisions = [];
                            this.supervisionId = '';
                            return;
                        }
                        this.filteredSupervisions = this.supervisions.filter(s => Number(s.zone_id) === Number(this.zoneId));
                        if (this.filteredSupervisions.length === 1) {
                            this.$nextTick(() => {
                                this.supervisionId = this.filteredSupervisions[0].id;
                            });
                        }
                    },
                    nextStep() {
                        if (this.step === 1) {
                            if (!this.zoneId || !this.supervisionId) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Atenção',
                                    text: 'Por favor, selecione a Zona e a Supervisão para prosseguir.',
                                    confirmButtonColor: '#2563eb'
                                });
                                return;
                            }
                        }
                        this.step++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }" x-init="init()">

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 p-5 rounded-3xl shadow-sm animate-fade-in">
                    <h3 class="text-sm font-black uppercase tracking-widest flex items-center gap-2 text-red-700">
                        <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                        Existem campos a corrigir no formulário
                    </h3>
                    <ul class="list-disc pl-5 mt-2 space-y-1 text-sm font-semibold text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Relatório Submetido!',
                            text: "{{ session('success') }}",
                            confirmButtonColor: '#2563eb',
                            confirmButtonText: 'Excelente'
                        });
                    });
                </script>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-6 rounded-3xl shadow-sm text-sm font-bold flex items-center gap-3">
                    <i class="bi bi-check-circle-fill text-2xl text-emerald-600"></i>
                    <div>
                        <div class="font-black text-emerald-900 text-base">Relatório Submetido com Sucesso!</div>
                        <div class="text-emerald-700 font-medium">{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            {{-- HEADER / HERO BANNER --}}
            <div class="bg-white border border-slate-200/80 rounded-[2.5rem] p-8 md:p-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 text-xs font-black text-blue-600 uppercase tracking-widest mb-2">
                        <span>Relatórios Trimestrais</span>
                        <i class="bi bi-chevron-right text-[10px]"></i>
                        <span>Edificar Life</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Relatório de Supervisão</h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">Preencha as informações estatísticas e indicadores de saúde da sua supervisão.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Progresso (Passo <span x-text="step"></span> de 4)</span>
                        <div class="flex gap-1.5 mt-2">
                            <template x-for="i in 4">
                                <div class="h-2 w-10 rounded-full transition-all duration-300"
                                    :class="step >= i ? 'bg-blue-600 shadow-sm' : 'bg-slate-200'"></div>
                            </template>
                        </div>
                    </div>
                    <a href="{{ route('welcome') }}"
                        class="group flex items-center bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200/80 px-6 py-3.5 rounded-2xl transition-all font-bold text-sm">
                        <i class="bi bi-x-lg text-sm mr-2 text-slate-500 group-hover:text-slate-800"></i>
                        Sair
                    </a>
                </div>
            </div>

            <form action="{{ route('public.reports.quarterly.store') }}" method="POST" id="reportForm"
                class="space-y-8 pb-12 relative z-10">
                @csrf

                {{-- STEP 1: IDENTIFICATION & STATS --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    class="space-y-8">
                    <div class="bg-white rounded-[2.5rem] border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                            <h2 class="text-lg font-black text-slate-900 flex items-center gap-3">
                                <span class="w-9 h-9 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-sm font-black shadow-md shadow-blue-500/20">1</span>
                                Identificação e Estatísticas Gerais
                            </h2>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Zona
                                    <span class="text-red-500">*</span></label>
                                <select name="zone_id" required x-model="zoneId" @change="updateSupervisions()"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-slate-900 appearance-none custom-select">
                                    <option value="">Selecione a Zona</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Supervisão
                                    <span class="text-red-500">*</span></label>
                                <select name="supervision_id" required x-model="supervisionId"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-slate-900 appearance-none custom-select">
                                    <option value="">Selecione a Supervisão</option>
                                    <template x-for="sup in filteredSupervisions" :key="sup.id">
                                        <option :value="sup.id" x-text="sup.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Ano</label>
                                <input type="number" name="year" value="{{ date('Y') }}"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-slate-900">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Trimestre
                                    <span class="text-red-500">*</span></label>
                                <select name="quarter" required
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-slate-900 appearance-none custom-select">
                                    <option value="1">1º Trimestre</option>
                                    <option value="2">2º Trimestre</option>
                                    <option value="3">3º Trimestre</option>
                                    <option value="4">4º Trimestre</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-8 pt-0">
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">Métricas Numéricas da Supervisão</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                                @php
                                    $stats = [
                                        'pastors_count' => ['icon' => 'bi-person-workspace', 'label' => 'Pastores', 'color' => 'text-red-500', 'border' => 'focus:border-red-500'],
                                        'supervisors_count' => ['icon' => 'bi-person-check', 'label' => 'Supervisores', 'color' => 'text-purple-600', 'border' => 'focus:border-purple-500'],
                                        'leaders_count' => ['icon' => 'bi-person-badge', 'label' => 'Líderes', 'color' => 'text-blue-600', 'border' => 'focus:border-blue-500'],
                                        'timoteos_count' => ['icon' => 'bi-award', 'label' => 'Auxiliares', 'color' => 'text-indigo-600', 'border' => 'focus:border-indigo-500'],
                                        'members_count' => ['icon' => 'bi-people', 'label' => 'Membros', 'color' => 'text-emerald-600', 'border' => 'focus:border-emerald-500'],
                                        'visitors_count' => ['icon' => 'bi-person-plus', 'label' => 'Visitantes', 'color' => 'text-amber-500', 'border' => 'focus:border-amber-500'],
                                        'saved_count' => ['icon' => 'bi-heart-pulse', 'label' => 'Salvações', 'color' => 'text-rose-500', 'border' => 'focus:border-rose-500'],
                                        'cells_count' => ['icon' => 'bi-grid-3x3-gap', 'label' => 'Células', 'color' => 'text-purple-500', 'border' => 'focus:border-purple-500'],
                                        'participants_count' => ['icon' => 'bi-graph-up', 'label' => 'Participantes', 'color' => 'text-blue-500', 'border' => 'focus:border-blue-500'],
                                    ];
                                @endphp
                                @foreach($stats as $field => $data)
                                    <div class="space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 flex items-center gap-1.5">
                                            <i class="bi {{ $data['icon'] }} {{ $data['color'] }} text-sm"></i>
                                            {{ $data['label'] }}
                                        </label>
                                        <input type="number" name="{{ $field }}" value="0" min="0" required
                                            class="w-full px-4 py-3 bg-white border border-slate-200 focus:ring-4 focus:ring-blue-500/10 rounded-xl transition-all font-black text-slate-900 text-center text-lg shadow-sm {{ $data['border'] }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-slate-200/80 p-6 flex justify-end shadow-sm">
                        <button type="button" @click="nextStep()"
                            class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/25 flex items-center gap-2">
                            <span>Próximo Passo</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>

                {{-- STEP 2: TARGETS & EVENTS --}}
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    class="space-y-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white rounded-[2.5rem] border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
                            <div>
                                <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-sm font-black shadow-md shadow-blue-500/20">2</span>
                                        Alvos e Resultados Alcançados
                                    </h2>
                                </div>
                                <div class="p-8 space-y-4">
                                    @php
                                        $results = [
                                            'planned_baptism_count' => 'Baptismos Planificados',
                                            'baptized_count' => 'Batismos Realizados',
                                            'cell_multiplications_count' => 'Multiplicações de Célula',
                                            'disciplined_leaders_count' => 'Líderes Disciplinados',
                                            'closed_cells_count' => 'Células Fechadas',
                                        ];
                                    @endphp
                                    @foreach($results as $field => $label)
                                        <div class="flex items-center justify-between p-4.5 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-slate-300 transition-all">
                                            <span class="text-sm font-extrabold text-slate-700">{{ $label }}</span>
                                            <input type="number" name="{{ $field }}" value="0" min="0" required
                                                class="w-24 px-4 py-2 bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl font-black text-center text-slate-900 shadow-sm">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-[2.5rem] border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                                    <i class="bi bi-calendar-event text-purple-600 text-xl"></i>
                                    Eventos e Cerimônias
                                </h2>
                            </div>
                            <div class="p-8 space-y-4 max-h-[500px] overflow-y-auto">
                                @foreach($eventTypes as $index => $type)
                                    <div class="p-4 bg-slate-50 rounded-2xl space-y-3 border border-slate-200/80">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-black text-slate-700 uppercase tracking-wider">{{ $type->name }}</span>
                                            <input type="hidden" name="events[{{ $index }}][event_type_id]" value="{{ $type->id }}">
                                            <input type="number" name="events[{{ $index }}][count]" value="0" min="0"
                                                class="w-20 px-3 py-1.5 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-center font-black text-slate-900 shadow-sm text-sm">
                                        </div>
                                        <textarea name="events[{{ $index }}][description]" rows="2"
                                            class="w-full px-3.5 py-2.5 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 shadow-sm"
                                            placeholder="Observações ou detalhes (opcional)"></textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-slate-200/80 p-6 flex justify-between shadow-sm">
                        <button type="button" @click="step--"
                            class="bg-slate-100 text-slate-700 px-8 py-4 rounded-2xl font-black hover:bg-slate-200 border border-slate-200 transition-all">
                            <i class="bi bi-arrow-left mr-2"></i>
                            Voltar
                        </button>
                        <button type="button" @click="nextStep()"
                            class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/25 flex items-center gap-2">
                            <span>Próximo Passo</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>

                {{-- STEP 3: HEALTH INDICATORS (SECÇÃO IV) --}}
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    class="space-y-8">
                    <div class="bg-white rounded-[2.5rem] border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <h2 class="text-lg font-black text-slate-900 flex items-center gap-3">
                                <span class="w-9 h-9 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-sm font-black shadow-md shadow-blue-500/20">3</span>
                                Indicadores de Saúde e Qualidade (Pontuação 0 a 3)
                            </h2>
                            <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-2xl border border-slate-200 text-xs font-bold text-slate-600">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-rose-100 border border-rose-400"></span> 0 - Fraco
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-amber-100 border border-amber-400"></span> 1 - Regular
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-blue-100 border border-blue-400"></span> 2 - Bom
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span> 3 - Excelente
                                </span>
                            </div>
                        </div>

                        <div class="p-8 space-y-10">
                            @php
                                $sections = [
                                    'discipleship' => [
                                        'title' => 'Discipulado e Evangelismo',
                                        'icon' => 'bi-chat-heart',
                                        'color' => 'blue',
                                        'questions' => [
                                            'discipleship_score' => 'Como está o discipulado um a um na supervisão?',
                                            'evangelism_strategy' => 'Existe uma estratégia clara de evangelismo (GEs)?',
                                            'consolidation_growth' => 'Os novos convertidos estão sendo consolidados?',
                                        ]
                                    ],
                                    'pastoral' => [
                                        'title' => 'Cuidado Pastoral',
                                        'icon' => 'bi-heart-pulse',
                                        'color' => 'red',
                                        'questions' => [
                                            'pastoral_score' => 'Qualidade do cuidado pastoral aos líderes?',
                                            'visitation_routine' => 'A rotina de visitação está sendo cumprida?',
                                            'leader_support' => 'Os líderes se sentem apoiados emocional e espiritualmente?',
                                        ]
                                    ],
                                    'participation' => [
                                        'title' => 'Participação e Frequência',
                                        'icon' => 'bi-graph-up',
                                        'color' => 'green',
                                        'questions' => [
                                            'cell_participation_score' => 'Participação média nas reuniões de célula?',
                                            'service_participation_score' => 'Presença dos membros nos cultos de celebração?',
                                            'tadium_participation' => 'Envolvimento dos líderes no TADEL / Reuniões de Liderança?',
                                        ]
                                    ],
                                    'relationship' => [
                                        'title' => 'Comunhão e Relacionamentos',
                                        'icon' => 'bi-people',
                                        'color' => 'purple',
                                        'questions' => [
                                            'communion_in_cells_score' => 'Nível de comunhão interna nas células?',
                                            'relationship_building_score' => 'Os novos se sentem integrados à família da igreja?',
                                            'prayer_intercession_score' => 'A vida de oração e intercessão do grupo?',
                                        ]
                                    ]
                                ];

                                $peerCheckedLightStyles = [
                                    'blue' => 'peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 peer-checked:shadow-md peer-checked:shadow-blue-500/30 hover:border-blue-400 hover:bg-blue-50/50',
                                    'red' => 'peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 peer-checked:shadow-md peer-checked:shadow-rose-500/30 hover:border-rose-400 hover:bg-rose-50/50',
                                    'green' => 'peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 peer-checked:shadow-md peer-checked:shadow-emerald-500/30 hover:border-emerald-400 hover:bg-emerald-50/50',
                                    'purple' => 'peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 peer-checked:shadow-md peer-checked:shadow-purple-500/30 hover:border-purple-400 hover:bg-purple-50/50',
                                ];
                            @endphp

                            @foreach($sections as $id => $section)
                                <div class="space-y-4">
                                    <h3 class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-widest">
                                        <i class="bi {{ $section['icon'] }} text-blue-600 text-sm"></i>
                                        {{ $section['title'] }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($section['questions'] as $field => $question)
                                            <div class="space-y-4 p-6 bg-slate-50 rounded-[2rem] border border-slate-200/80 group">
                                                <p class="text-sm font-extrabold text-slate-800">
                                                    {{ $question }}
                                                </p>
                                                <div class="flex gap-2 justify-between">
                                                    @for($i = 0; $i <= 3; $i++)
                                                        <label class="flex-1 cursor-pointer block select-none">
                                                            <input type="radio" name="{{ $field }}" value="{{ $i }}" class="sr-only peer"
                                                                required @if($i == 2) checked @endif>
                                                            <div class="w-full py-3 text-center rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-black transition-all shadow-sm active:scale-95 cursor-pointer {{ $peerCheckedLightStyles[$section['color']] ?? $peerCheckedLightStyles['blue'] }}">
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
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-slate-200/80 p-6 flex justify-between shadow-sm">
                        <button type="button" @click="step--"
                            class="bg-slate-100 text-slate-700 px-8 py-4 rounded-2xl font-black hover:bg-slate-200 border border-slate-200 transition-all">
                            <i class="bi bi-arrow-left mr-2"></i>
                            Voltar
                        </button>
                        <button type="button" @click="nextStep()"
                            class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/25 flex items-center gap-2">
                            <span>Próximo Passo</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>

                {{-- STEP 4: OBSERVATIONS & SUBMIT --}}
                <div x-show="step === 4" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    class="space-y-8">
                    <div class="bg-white rounded-[2.5rem] border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                            <h2 class="text-lg font-black text-slate-900 flex items-center gap-3">
                                <span class="w-9 h-9 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-sm font-black shadow-md shadow-blue-500/20">4</span>
                                Conclusão e Observações Gerais
                            </h2>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Relato Descritivo do Trimestre</label>
                                <textarea name="ministerial_observations" rows="8"
                                    placeholder="Descreva os maiores desafios superados, testemunhos marcantes, conquistas e visão para a supervisão..."
                                    class="w-full px-6 py-5 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-3xl transition-all font-medium text-slate-900 placeholder-slate-400 resize-none shadow-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-slate-200/80 p-8 flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm">
                        <button type="button" @click="step--"
                            class="w-full sm:w-auto bg-slate-100 text-slate-700 px-8 py-4 rounded-2xl font-black hover:bg-slate-200 border border-slate-200 transition-all">
                            <i class="bi bi-arrow-left mr-2"></i>
                            Revisar Passos
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto bg-blue-600 text-white px-12 py-5 rounded-[1.5rem] font-black text-lg hover:bg-blue-700 transition-all shadow-xl shadow-blue-500/25 active:scale-95 flex items-center justify-center gap-3">
                            <i class="bi bi-send-fill text-xl"></i>
                            <span>SUBMETER RELATÓRIO</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection