@extends('layouts.auth')

@section('title', 'Relatório Trimestral')

@section('content')
    <div class="min-h-screen bg-gray-900 flex items-start justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-4xl w-full space-y-8 relative z-10" x-data="publicQuarterlyForm()" x-init="init()">
            <div class="text-center">
                <a href="{{ route('welcome') }}" class="inline-block mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto mx-auto">
                </a>
                <h2 class="text-4xl font-black text-white tracking-tighter">Relatório Trimestral</h2>
                <p class="mt-2 text-gray-400 font-medium uppercase tracking-widest text-xs">Portal Life Church</p>
            </div>

            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-200 p-4 rounded-2xl text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-200 p-4 rounded-2xl text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 md:p-12 shadow-2xl">
                <form action="{{ route('public.reports.quarterly.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Zona</label>
                            <select name="zone_id" required x-model="zoneId" @change="updateSupervisions()"
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none">
                                <option value="" class="bg-gray-900">Selecione a Zona</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" class="bg-gray-900">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Supervisão</label>
                            <select name="supervision_id" required x-model="supervisionId"
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none">
                                <option value="" class="bg-gray-900">Selecione a Supervisão</option>
                                <template x-for="sup in filteredSupervisions" :key="sup.id">
                                    <option :value="sup.id" x-text="sup.name" class="bg-gray-900"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Ano</label>
                            <input type="number" name="year" value="{{ date('Y') }}" required
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Trimestre</label>
                            <select name="quarter" required
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none">
                                <option value="1" class="bg-gray-900">1º Trimestre</option>
                                <option value="2" class="bg-gray-900">2º Trimestre</option>
                                <option value="3" class="bg-gray-900">3º Trimestre</option>
                                <option value="4" class="bg-gray-900">4º Trimestre</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @php
                            $stats = [
                                'leaders_count' => 'Líderes',
                                'cells_count' => 'Células',
                                'timoteos_count' => 'Auxiliares',
                                'members_count' => 'Membros',
                                'participants_count' => 'Participantes',
                                'pastors_count' => 'Pastores',
                                'supervisors_count' => 'Supervisores',
                                'visitors_count' => 'Visitantes',
                                'saved_count' => 'Decisões',
                                'planned_baptism_count' => 'Alvo Batismos',
                                'baptized_count' => 'Batismos',
                                'cell_multiplications_count' => 'Multiplicações',
                                'disciplined_leaders_count' => 'Líderes Disc.',
                                'closed_cells_count' => 'Células Fechadas',
                            ];
                        @endphp
                        @foreach($stats as $field => $label)
                            <div>
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">{{ $label }}</label>
                                <input type="number" name="{{ $field }}" min="0" value="0"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-center font-bold">
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Observações</label>
                        <textarea name="ministerial_observations" rows="5"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder-gray-600"
                            placeholder="Descreva os principais desafios e vitórias do trimestre..."></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-black py-5 rounded-2xl uppercase tracking-widest text-sm shadow-2xl shadow-blue-500/20 hover:scale-[1.02] transition-all duration-300">
                            Enviar Relatório
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function publicQuarterlyForm() {
            return {
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
            }
        }
    </script>
@endsection
