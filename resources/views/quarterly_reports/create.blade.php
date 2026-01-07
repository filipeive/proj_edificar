@extends('layouts.app')

@section('title', 'Novo Relatório Trimestral - Portal Life Church')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8" x-data="reportForm()">
        <!-- Header -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">
                    <a href="{{ route('quarterly-reports.index') }}" class="hover:underline">Relatórios Trimestrais</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span>Submeter Novo</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Relatório de Supervisão</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex flex-col items-end">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Progresso</span>
                    <div class="flex gap-1 mt-1">
                        <template x-for="i in 4">
                            <div class="h-1.5 w-8 rounded-full transition-all duration-500"
                                :class="step >= i ? 'bg-blue-600' : 'bg-gray-100'"></div>
                        </template>
                    </div>
                </div>
                <a href="{{ route('quarterly-reports.index') }}"
                    class="group flex items-center bg-gray-50 text-gray-500 px-6 py-3 rounded-2xl hover:bg-gray-100 transition-all font-bold">
                    <i class="bi bi-x-lg text-sm mr-2"></i>
                    Sair
                </a>
            </div>
        </div>

        <form action="{{ route('quarterly-reports.store') }}" method="POST" id="reportForm" class="space-y-8 pb-12">
            @csrf

            <!-- STEP 1: IDENTIFICATION & STATS -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">1</span>
                            Identificação e Estatísticas
                        </h2>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Zona</label>
                            <select name="zone_id" required x-model="zoneId" @change="updateSupervisions()"
                                class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                                <option value="">Selecione a Zona</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Supervisão</label>
                            <select name="supervision_id" required x-model="supervisionId"
                                class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                                <option value="">Selecione a Supervisão</option>
                                <template x-for="sup in filteredSupervisions" :key="sup.id">
                                    <option :value="sup.id" x-text="sup.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Ano</label>
                            <input type="number" name="year" value="{{ date('Y') }}"
                                class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Trimestre</label>
                            <select name="quarter" required
                                class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                                <option value="1">1º Trimestre</option>
                                <option value="2">2º Trimestre</option>
                                <option value="3">3º Trimestre</option>
                                <option value="4">4º Trimestre</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-8 grid grid-cols-2 md:grid-cols-6 gap-6 pt-0">
                        @php
                            $stats = [
                                'leaders_count' => ['icon' => 'bi-person-badge', 'label' => 'Líderes', 'color' => 'blue'],
                                'cells_count' => ['icon' => 'bi-grid-3x3-gap', 'label' => 'Células', 'color' => 'purple'],
                                'timoteos_count' => ['icon' => 'bi-award', 'label' => 'Timóteos', 'color' => 'indigo'],
                                'members_count' => ['icon' => 'bi-people', 'label' => 'Membros', 'color' => 'green'],
                                'participants_count' => ['icon' => 'bi-graph-up', 'label' => 'Part. Médios', 'color' => 'orange'],
                                'saved_count' => ['icon' => 'bi-heart-pulse', 'label' => 'Novas Almas', 'color' => 'red'],
                            ];
                        @endphp
                        @foreach($stats as $field => $data)
                            <div class="space-y-2">
                                <label
                                    class="text-[9px] font-black uppercase tracking-widest text-gray-400 ml-1 flex items-center gap-1">
                                    <i class="bi {{ $data['icon'] }} text-{{ $data['color'] }}-500"></i>
                                    {{ $data['label'] }}
                                </label>
                                <input type="number" name="{{ $field }}" value="0" min="0" required
                                    class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-{{ $data['color'] }}-500 focus:ring-4 focus:ring-{{ $data['color'] }}-500/10 rounded-xl transition-all font-black text-gray-700 text-center">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 flex justify-end">
                    <button type="button" @click="nextStep()"
                        class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-200">
                        Próximo Passo
                        <i class="bi bi-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: MINISTERIAL RESULTS & EVENTS -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Resultados -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                            <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">2</span>
                                Alvos e Resultados
                            </h2>
                        </div>
                        <div class="p-8 space-y-6">
                            @php
                                $results = [
                                    'planned_baptism_count' => 'Alvo de Batismos',
                                    'baptized_count' => 'Batismos Realizados',
                                    'cell_multiplications_count' => 'Multiplicações de Célula',
                                    'disciplined_leaders_count' => 'Líderes Disciplinados',
                                    'closed_cells_count' => 'Células Fechadas',
                                ];
                            @endphp
                            @foreach($results as $field => $label)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl group hover:bg-white hover:shadow-md transition-all border border-transparent hover:border-blue-100">
                                    <span class="text-sm font-black text-gray-600">{{ $label }}</span>
                                    <input type="number" name="{{ $field }}" value="0" min="0" required
                                        class="w-24 px-4 py-2 bg-white border-transparent focus:ring-4 focus:ring-blue-500/10 rounded-xl font-black text-center text-blue-600">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Eventos -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                            <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                <i class="bi bi-calendar-event text-purple-600"></i>
                                Eventos e Cerimônias
                            </h2>
                        </div>
                        <div class="p-8 space-y-4 max-h-[480px] overflow-y-auto">
                            @foreach($eventTypes as $index => $type)
                                <div class="p-4 bg-gray-50 rounded-2xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $type->name }}</span>
                                        <input type="hidden" name="events[{{ $index }}][event_type_id]" value="{{ $type->id }}">
                                        <input type="number" name="events[{{ $index }}][count]" value="0" min="0"
                                            class="w-16 px-2 py-1 bg-white border-transparent focus:ring-0 rounded-lg text-center font-black text-purple-600">
                                    </div>
                                    <input type="text" name="events[{{ $index }}][description]"
                                        placeholder="Observações específicas..."
                                        class="w-full px-4 py-2 bg-white border-transparent rounded-xl text-sm font-medium text-gray-600">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 flex justify-between">
                    <button type="button" @click="step--"
                        class="bg-gray-100 text-gray-500 px-10 py-4 rounded-2xl font-black hover:bg-gray-200 transition-all">
                        <i class="bi bi-arrow-left mr-2"></i>
                        Voltar
                    </button>
                    <button type="button" @click="nextStep()"
                        class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-200">
                        Próximo Passo
                        <i class="bi bi-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: STRENGTHS & WEAKNESSES (THE 27 QUESTIONS) -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50 flex flex-col md:flex-row justify-between gap-4">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">3</span>
                            Indicadores de Saúde (Pontos Fortes e Fracos)
                        </h2>
                        <div class="flex gap-4">
                            <span
                                class="flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <span class="w-3 h-3 rounded bg-red-100"></span> 0 - Fraco
                            </span>
                            <span
                                class="flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <span class="w-3 h-3 rounded bg-blue-600"></span> 3 - Excelente
                            </span>
                        </div>
                    </div>

                    <div class="p-8 space-y-12">
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
                        @endphp

                        @foreach($sections as $id => $section)
                            <div class="space-y-6">
                                <h3
                                    class="flex items-center gap-2 text-sm font-black text-{{ $section['color'] }}-600 uppercase tracking-widest">
                                    <i class="bi {{ $section['icon'] }}"></i>
                                    {{ $section['title'] }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach($section['questions'] as $field => $question)
                                        <div
                                            class="space-y-4 p-6 bg-gray-50 rounded-[2rem] hover:bg-white hover:shadow-xl transition-all border border-transparent hover:border-{{ $section['color'] }}-100 group">
                                            <p
                                                class="text-sm font-black text-gray-700 group-hover:text-{{ $section['color'] }}-700 transition-colors">
                                                {{ $question }}</p>
                                            <div class="flex gap-2 justify-between">
                                                @for($i = 0; $i <= 3; $i++)
                                                    <label class="flex-1">
                                                        <input type="radio" name="{{ $field }}" value="{{ $i }}" class="hidden peer"
                                                            required @if($i == 2) checked @endif>
                                                        <div class="w-full py-3 text-center rounded-xl bg-white border border-gray-100 text-sm font-black transition-all cursor-pointer
                                                                            peer-checked:bg-{{ $section['color'] }}-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-{{ $section['color'] }}-200
                                                                            hover:border-{{ $section['color'] }}-500 text-gray-400">
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

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 flex justify-between">
                    <button type="button" @click="step--"
                        class="bg-gray-100 text-gray-500 px-10 py-4 rounded-2xl font-black hover:bg-gray-200 transition-all">
                        <i class="bi bi-arrow-left mr-2"></i>
                        Voltar
                    </button>
                    <button type="button" @click="nextStep()"
                        class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-200">
                        Próximo Passo
                        <i class="bi bi-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 4: OBSERVATIONS & SUBMIT -->
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">4</span>
                            Conclusão e Observações
                        </h2>
                    </div>
                    <div class="p-8 space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Relato
                                Descritivo do Trimestre</label>
                            <textarea name="ministerial_observations" rows="10"
                                placeholder="Conte-nos os maiores desafios superados, vitórias alcançadas e o que Deus tem feito nesta supervisão..."
                                class="w-full px-8 py-6 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-[2.5rem] transition-all font-medium text-gray-700 placeholder-gray-300 resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 flex justify-between items-center">
                    <button type="button" @click="step--"
                        class="bg-gray-100 text-gray-500 px-10 py-4 rounded-2xl font-black hover:bg-gray-200 transition-all">
                        <i class="bi bi-arrow-left mr-2"></i>
                        Revisar
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-12 py-5 rounded-[1.5rem] font-black text-lg hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 active:scale-95">
                        <i class="bi bi-send mr-2"></i>
                        ENVIAR RELATÓRIO
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function reportForm() {
            return {
                step: 1,
                zoneId: '',
                supervisionId: '',
                zones: @json($zones),
                supervisions: @json($supervisions),
                filteredSupervisions: [],

                init() {
                    this.updateSupervisions();
                },

                updateSupervisions() {
                    if (!this.zoneId) {
                        this.filteredSupervisions = [];
                        return;
                    }
                    this.filteredSupervisions = this.supervisions.filter(s => s.zone_id == this.zoneId);
                },

                nextStep() {
                    // Simple validation for step 1
                    if (this.step === 1) {
                        if (!this.zoneId || !this.supervisionId) {
                            alert('Por favor, selecione a Zona e Supervisão.');
                            return;
                        }
                    }
                    this.step++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }
    </style>
@endsection