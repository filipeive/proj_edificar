@extends('layouts.auth')

@section('title', 'Relatório Trimestral')

@section('content')
    <div class="min-h-screen bg-gray-900 py-10 px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-6xl mx-auto space-y-8 relative z-10"
            x-data="{
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
                            alert('Por favor, selecione a Zona e Supervisão.');
                            return;
                        }
                    }
                    this.step++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }"
            x-init="init()">
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-200 p-4 rounded-2xl">
                    <h3 class="text-sm font-black uppercase tracking-widest">Existem erros no formulário</h3>
                    <ul class="list-disc pl-5 mt-2 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-200 p-4 rounded-2xl text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white/5 border border-white/10 rounded-[2.5rem] p-8 md:p-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10">
                <div>
                    <div class="flex items-center gap-2 text-xs font-bold text-blue-300 uppercase tracking-widest mb-2">
                        <span>Relatórios Trimestrais</span>
                        <i class="bi bi-chevron-right text-[10px]"></i>
                        <span>Submeter Novo</span>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tight">Relatório de Supervisão</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Progresso</span>
                        <div class="flex gap-1 mt-1">
                            <template x-for="i in 4">
                                <div class="h-1.5 w-8 rounded-full transition-all duration-500"
                                    :class="step >= i ? 'bg-blue-500' : 'bg-white/10'"></div>
                            </template>
                        </div>
                    </div>
                    <a href="{{ route('welcome') }}"
                        class="group flex items-center bg-white/10 text-white px-6 py-3 rounded-2xl hover:bg-white/20 transition-all font-bold">
                        <i class="bi bi-x-lg text-sm mr-2"></i>
                        Sair
                    </a>
                </div>
            </div>

            <form action="{{ route('public.reports.quarterly.store') }}" method="POST" id="reportForm" class="space-y-8 pb-12 relative z-10">
                @csrf

                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                    class="space-y-8">
                    <div class="bg-white/5 rounded-[2.5rem] border border-white/10 overflow-hidden">
                        <div class="p-8 border-b border-white/10 bg-white/5">
                            <h2 class="text-lg font-black text-white flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">1</span>
                                Identificação e Estatísticas
                            </h2>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-4 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Zona</label>
                                <select name="zone_id" required x-model="zoneId" @change="updateSupervisions()"
                                    class="w-full px-5 py-4 bg-white/10 border border-white/10 focus:bg-white/10 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-white appearance-none custom-select">
                                    <option value="">Selecione a Zona</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Supervisão</label>
                                <select name="supervision_id" required x-model="supervisionId"
                                    class="w-full px-5 py-4 bg-white/10 border border-white/10 focus:bg-white/10 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-white appearance-none custom-select">
                                    <option value="">Selecione a Supervisão</option>
                                    <template x-for="sup in filteredSupervisions" :key="sup.id">
                                        <option :value="sup.id" x-text="sup.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Ano</label>
                                <input type="number" name="year" value="{{ date('Y') }}"
                                    class="w-full px-5 py-4 bg-white/10 border border-white/10 focus:bg-white/10 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-white">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Trimestre</label>
                                <select name="quarter" required
                                    class="w-full px-5 py-4 bg-white/10 border border-white/10 focus:bg-white/10 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-white appearance-none custom-select">
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
                                    'pastors_count' => ['icon' => 'bi-person-workspace', 'label' => 'Pastores', 'color' => 'red'],
                                    'supervisors_count' => ['icon' => 'bi-person-check', 'label' => 'Supervisores', 'color' => 'purple'],
                                    'leaders_count' => ['icon' => 'bi-person-badge', 'label' => 'Líderes', 'color' => 'blue'],
                                    'timoteos_count' => ['icon' => 'bi-award', 'label' => 'Auxiliares', 'color' => 'indigo'],
                                    'members_count' => ['icon' => 'bi-people', 'label' => 'Membros', 'color' => 'green'],
                                    'visitors_count' => ['icon' => 'bi-person-plus', 'label' => 'Visitantes', 'color' => 'orange'],
                                    'saved_count' => ['icon' => 'bi-heart-pulse', 'label' => 'Decisões', 'color' => 'red'],
                                    'cells_count' => ['icon' => 'bi-grid-3x3-gap', 'label' => 'Células', 'color' => 'purple'],
                                    'participants_count' => ['icon' => 'bi-graph-up', 'label' => 'Part. Médios', 'color' => 'orange'],
                                ];
                            @endphp
                            @foreach($stats as $field => $data)
                                <div class="space-y-2">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 ml-1 flex items-center gap-1">
                                        <i class="bi {{ $data['icon'] }} text-{{ $data['color'] }}-400"></i>
                                        {{ $data['label'] }}
                                    </label>
                                    <input type="number" name="{{ $field }}" value="0" min="0" required
                                        class="w-full px-4 py-3 bg-white/10 border border-white/10 focus:bg-white/10 focus:border-{{ $data['color'] }}-500 focus:ring-4 focus:ring-{{ $data['color'] }}-500/10 rounded-xl transition-all font-black text-white text-center">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white/5 rounded-[2.5rem] border border-white/10 p-8 flex justify-end">
                        <button type="button" @click="nextStep()"
                            class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-200">
                            Próximo Passo
                            <i class="bi bi-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                    class="space-y-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white/5 rounded-[2.5rem] border border-white/10 overflow-hidden">
                            <div class="p-8 border-b border-white/10 bg-white/5">
                                <h2 class="text-lg font-black text-white flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm">2</span>
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
                                    <div class="flex items-center justify-between p-4 bg-white/10 rounded-2xl group hover:bg-white/20 transition-all border border-white/10">
                                        <span class="text-sm font-black text-gray-200">{{ $label }}</span>
                                        <input type="number" name="{{ $field }}" value="0" min="0" required
                                            class="w-24 px-4 py-2 bg-white/10 border border-white/10 focus:ring-4 focus:ring-blue-500/10 rounded-xl font-black text-center text-white">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-white/5 rounded-[2.5rem] border border-white/10 overflow-hidden">
                            <div class="p-8 border-b border-white/10 bg-white/5">
                                <h2 class="text-lg font-black text-white flex items-center gap-2">
                                    <i class="bi bi-calendar-event text-purple-400"></i>
                                    Eventos e Cerimônias
                                </h2>
                            </div>
                            <div class="p-8 space-y-4 max-h-[480px] overflow-y-auto">
                                @foreach($eventTypes as $index => $type)
                                    <div class="p-4 bg-white/10 rounded-2xl space-y-3 border border-white/10">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-black text-gray-300 uppercase tracking-widest">{{ $type->name }}</span>
                                            <input type="hidden" name="events[{{ $index }}][event_type_id]" value="{{ $type->id }}">
                                            <input type="number" name="events[{{ $index }}][count]" value="0" min="0"
                                                class="w-16 px-2 py-1 bg-white/10 border border-white/10 focus:ring-0 rounded-lg text-center font-black text-white">
                                        </div>
                                        <textarea name="events[{{ $index }}][description]" rows="2"
                                            class="w-full px-3 py-2 bg-white/10 border border-white/10 focus:ring-0 rounded-lg text-xs text-white placeholder-gray-500"
                                            placeholder="Observações (opcional)"></textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 rounded-[2.5rem] border border-white/10 p-8 flex justify-between items-center">
                        <button type="button" @click="step--"
                            class="bg-white/10 text-white px-10 py-4 rounded-2xl font-black hover:bg-white/20 transition-all">
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
    </div>

@endsection
