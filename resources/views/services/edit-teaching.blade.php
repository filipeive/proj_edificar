@extends('layouts.app')

@section('title', 'Editar Culto de Ensino - Portal Life Church')
@section('page-title', 'Editar Culto de Ensino')
@section('page-subtitle', 'Atualizar métricas da reunião de quarta-feira')

@section('content')
    <div class="space-y-5" x-data="{ 
        guestPreacher: {{ $service->preacher_id === null && $service->preacher_name ? 'true' : 'false' }},
        serviceType: 'teaching'
    }">
        <!-- Page Header (National Style) -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">
                    <a href="{{ route('services.index') }}" class="hover:underline">Cultos</a>
                    <i class="bi bi-chevron-right text-[9px]"></i>
                    <span>Editar Culto de Ensino</span>
                </div>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Editar Culto de Ensino</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Atualizar métricas da reunião de ensino de {{ $service->date->format('d/m/Y') }}.</p>
            </div>
            <a href="{{ route('services.index') }}"
                class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                <i class="bi bi-arrow-left"></i>
                <span>Cancelar</span>
            </a>
        </header>

        <form action="{{ route('services.update', $service) }}" method="POST" id="serviceForm" class="space-y-5 pb-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="service_type" value="teaching">

            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
                    <div class="flex items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill text-red-600 text-base mr-2"></i>
                        <h3 class="text-red-900 font-black uppercase tracking-wider text-xs">Erros de Validação</h3>
                    </div>
                    <ul class="list-disc list-inside text-red-700 text-xs font-semibold space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Section 1: Cabeçalho -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50">
                    <h2 class="text-sm font-black text-gray-900 flex items-center gap-2">
                        <i class="bi bi-info-circle text-blue-600"></i>
                        Informações Gerais
                    </h2>
                </div>
                <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-500">Data do Culto</label>
                        <input type="date" name="date" value="{{ old('date', $service->date->format('Y-m-d')) }}" required
                            class="w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-600 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all text-xs sm:text-sm font-semibold text-gray-700">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-500">Pregador</label>
                        <div class="space-y-2" x-effect="if (guestPreacher && $refs.preacherSelect) { $refs.preacherSelect.value = ''; }">
                            <select name="preacher_id" x-ref="preacherSelect"
                                class="w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-600 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all text-xs sm:text-sm font-semibold text-gray-700 custom-select">
                                <option value="">Selecione o pregador</option>
                                @foreach($preachers as $preacher)
                                    <option value="{{ $preacher->id }}" @selected(old('preacher_id', $service->preacher_id) == $preacher->id)>
                                        {{ $preacher->name }}
                                    </option>
                                @endforeach
                            </select>

                            <label class="flex items-center gap-2 text-xs font-bold text-gray-600">
                                <input type="checkbox" name="guest_preacher" value="1"
                                    class="w-4 h-4 rounded border-gray-300 text-orange-700 focus:ring-orange-600"
                                    x-model="guestPreacher">
                                Pregador Convidado
                            </label>

                            <template x-if="guestPreacher">
                                <input type="text" name="preacher_name" value="{{ old('preacher_name', $service->preacher_name) }}"
                                    placeholder="Nome do pregador convidado"
                                    class="w-full px-3 py-2 bg-orange-50/50 border border-orange-200 focus:bg-white focus:border-orange-600 rounded-lg transition-all text-xs sm:text-sm font-semibold text-gray-700">
                            </template>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-500">Tema da Mensagem</label>
                        <input type="text" name="theme" value="{{ old('theme', $service->theme) }}"
                            placeholder="Qual o tema central do culto?"
                            class="w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-600 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all text-xs sm:text-sm font-semibold text-gray-700">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-500">Visitantes Gerais (Adultos)</label>
                        <input type="number" name="adults_visitors" value="{{ old('adults_visitors', $service->adults_visitors) }}" min="0"
                            class="w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-600 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all text-xs sm:text-sm font-semibold text-gray-700">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-gray-500">Visitantes Gerais (Crianças)</label>
                        <input type="number" name="children_visitors" value="{{ old('children_visitors', $service->children_visitors) }}" min="0"
                            class="w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-600 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all text-xs sm:text-sm font-semibold text-gray-700">
                    </div>
                </div>
            </div>

            <!-- Section: Participação por Zona (Novo Fluxo Wizard com Dados Carregados) -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" 
                 x-data="{ 
                    selectedZone: 0,
                    zones: {{ Js::from($zones) }},
                    participations: [],
                    init() {
                        const oldData = {{ Js::from(old('zone_participations')) }};
                        const serviceData = {{ Js::from($zones->map(function($z) use ($service) {
                            $p = $service->zoneParticipations->where('zone_id', $z->id)->first();
                            return [
                                'zone_id' => $z->id,
                                'name' => $z->name,
                                'adults_members' => $p ? $p->adults_members : 0,
                                'adults_visitors' => $p ? $p->adults_visitors : 0,
                                'leaders' => $p ? $p->leaders : 0,
                                'auxiliary_leaders' => $p ? $p->auxiliary_leaders : 0,
                                'supervisors' => $p ? $p->supervisors : 0,
                                'zone_pastors' => $p ? $p->zone_pastors : 0
                            ];
                        })) }};

                        this.participations = this.zones.map((z, idx) => {
                            const oldP = oldData && oldData[idx] ? oldData[idx] : null;
                            const sP = serviceData[idx];
                            return {
                                zone_id: z.id,
                                name: z.name,
                                adults_members: oldP ? oldP.adults_members : sP.adults_members,
                                adults_visitors: oldP ? oldP.adults_visitors : sP.adults_visitors,
                                leaders: oldP ? oldP.leaders : sP.leaders,
                                auxiliary_leaders: oldP ? oldP.auxiliary_leaders : sP.auxiliary_leaders,
                                supervisors: oldP ? oldP.supervisors : sP.supervisors,
                                zone_pastors: oldP ? oldP.zone_pastors : sP.zone_pastors
                            };
                        });
                    },
                    isFilled(index) {
                        const p = this.participations[index];
                        if (!p) return false;
                        return (p.adults_members > 0 || p.adults_visitors > 0 || p.leaders > 0 || p.auxiliary_leaders > 0 || p.supervisors > 0 || p.zone_pastors > 0);
                    },
                    getZoneTotal(index) {
                        const p = this.participations[index];
                        if (!p) return 0;
                        return Number(p.adults_members) + Number(p.adults_visitors) + Number(p.leaders) + Number(p.auxiliary_leaders) + Number(p.supervisors) + Number(p.zone_pastors);
                    }
                 }">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="text-sm font-black text-gray-900 flex items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-blue-600"></i>
                            Participação por Zona
                        </h2>
                        <p class="text-[11px] text-gray-500">
                            Clique numa zona abaixo para editar os dados correspondentes
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Progresso:</span>
                        <div class="flex gap-1">
                            <template x-for="(zone, index) in zones" :key="zone.id">
                                <div class="w-5 h-1.5 rounded-full transition-all"
                                    :class="isFilled(index) ? 'bg-green-500' : 'bg-gray-200'"></div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-5 space-y-5">
                    <!-- Zone Selection Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5">
                        <template x-for="(zone, index) in zones" :key="zone.id">
                            <button type="button" 
                                @click="selectedZone = index"
                                :class="selectedZone === index ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : (isFilled(index) ? 'bg-green-50 text-green-800 border-green-200' : 'bg-gray-50 text-gray-600 border-gray-200')"
                                class="p-3 rounded-lg border text-center transition-all relative">
                                <div class="absolute top-1.5 right-1.5" x-show="isFilled(index)">
                                    <i class="bi bi-check-circle-fill text-[10px]"></i>
                                </div>
                                <p class="text-xs font-black uppercase truncate" x-text="zone.name"></p>
                                <p class="text-[10px] font-semibold opacity-75 mt-0.5" x-text="getZoneTotal(index) + ' Total'"></p>
                            </button>
                        </template>
                    </div>

                    <!-- Focused Input Area -->
                    <div x-show="selectedZone !== null" 
                         class="bg-gray-50 rounded-xl border border-gray-200 p-4 sm:p-5">
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white rounded-lg border border-gray-200 flex items-center justify-center text-blue-600">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-gray-900" x-text="'Zona: ' + zones[selectedZone].name"></h3>
                                    <p class="text-[10px] text-gray-500">Atualize as métricas desta zona</p>
                                </div>
                            </div>
                            <button type="button" @click="selectedZone = null" class="text-gray-400 hover:text-gray-900 transition-colors">
                                <i class="bi bi-x-lg text-xs"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                            <!-- Hidden Fields for Form Submission -->
                            <input type="hidden" :name="'zone_participations['+selectedZone+'][zone_id]'" :value="participations[selectedZone]?.zone_id">
                            <input type="hidden" :name="'zone_participations['+selectedZone+'][children_members]'" value="0">
                            <input type="hidden" :name="'zone_participations['+selectedZone+'][children_visitors]'" value="0">

                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-gray-500">Membros (A)</label>
                                <input type="number" :name="'zone_participations['+selectedZone+'][adults_members]'" x-model="participations[selectedZone].adults_members" min="0"
                                    class="zone-participation-input w-full px-3 py-2 bg-white border border-gray-300 rounded-lg font-bold text-gray-800 text-center text-sm">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-gray-500">Visitantes (A)</label>
                                <input type="number" :name="'zone_participations['+selectedZone+'][adults_visitors]'" x-model="participations[selectedZone].adults_visitors" min="0"
                                    class="zone-participation-input w-full px-3 py-2 bg-white border border-gray-300 rounded-lg font-bold text-gray-800 text-center text-sm">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-orange-700">Líderes</label>
                                <input type="number" :name="'zone_participations['+selectedZone+'][leaders]'" x-model="participations[selectedZone].leaders" min="0"
                                    class="zone-participation-input w-full px-3 py-2 bg-white border border-gray-300 rounded-lg font-bold text-orange-700 text-center text-sm">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-orange-600">Timóteos</label>
                                <input type="number" :name="'zone_participations['+selectedZone+'][auxiliary_leaders]'" x-model="participations[selectedZone].auxiliary_leaders" min="0"
                                    class="zone-participation-input w-full px-3 py-2 bg-white border border-gray-300 rounded-lg font-bold text-orange-600 text-center text-sm">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-purple-700">Supervisores</label>
                                <input type="number" :name="'zone_participations['+selectedZone+'][supervisors]'" x-model="participations[selectedZone].supervisors" min="0"
                                    class="zone-participation-input w-full px-3 py-2 bg-white border border-gray-300 rounded-lg font-bold text-purple-700 text-center text-sm">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-red-700">Pastores Z.</label>
                                <input type="number" :name="'zone_participations['+selectedZone+'][zone_pastors]'" x-model="participations[selectedZone].zone_pastors" min="0"
                                    class="zone-participation-input w-full px-3 py-2 bg-white border border-gray-300 rounded-lg font-bold text-red-700 text-center text-sm">
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button type="button" @click="selectedZone = (selectedZone + 1 < zones.length ? selectedZone + 1 : null)"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-xs hover:bg-blue-700 transition-all flex items-center gap-1.5">
                                Próxima Zona <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="selectedZone === null" class="py-6 text-center bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <p class="text-xs font-semibold text-gray-500">Selecione uma zona acima para editar os dados</p>
                    </div>

                    <!-- Hidden fields for all zones to ensure they are submitted -->
                    <template x-for="(p, idx) in participations" :key="idx">
                        <div>
                            <input type="hidden" :name="'zone_participations['+idx+'][zone_id]'" :value="p.zone_id">
                            <input type="hidden" :name="'zone_participations['+idx+'][adults_members]'" :value="p.adults_members">
                            <input type="hidden" :name="'zone_participations['+idx+'][adults_visitors]'" :value="p.adults_visitors">
                            <input type="hidden" :name="'zone_participations['+idx+'][leaders]'" :value="p.leaders">
                            <input type="hidden" :name="'zone_participations['+idx+'][auxiliary_leaders]'" :value="p.auxiliary_leaders">
                            <input type="hidden" :name="'zone_participations['+idx+'][supervisors]'" :value="p.supervisors">
                            <input type="hidden" :name="'zone_participations['+idx+'][zone_pastors]'" :value="p.zone_pastors">
                            <input type="hidden" :name="'zone_participations['+idx+'][children_members]'" value="0">
                            <input type="hidden" :name="'zone_participations['+idx+'][children_visitors]'" value="0">
                        </div>
                    </template>
                </div>

                <!-- Footer Totals Summary -->
                <div class="p-4 bg-gray-900 text-white">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        <div class="text-center">
                            <p class="text-[9px] font-bold uppercase text-gray-400">Membros</p>
                            <p class="text-base font-black tabular-nums" x-text="participations.reduce((acc, p) => acc + Number(p.adults_members), 0)"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] font-bold uppercase text-gray-400">Visitantes</p>
                            <p class="text-base font-black tabular-nums" x-text="participations.reduce((acc, p) => acc + Number(p.adults_visitors), 0)"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] font-bold uppercase text-orange-300">Líderes</p>
                            <p class="text-base font-black tabular-nums text-orange-300" x-text="participations.reduce((acc, p) => acc + Number(p.leaders), 0)"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] font-bold uppercase text-orange-200">Timóteos</p>
                            <p class="text-base font-black tabular-nums text-orange-200" x-text="participations.reduce((acc, p) => acc + Number(p.auxiliary_leaders), 0)"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] font-bold uppercase text-purple-300">Superv.</p>
                            <p class="text-base font-black tabular-nums text-purple-300" x-text="participations.reduce((acc, p) => acc + Number(p.supervisors), 0)"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] font-bold uppercase text-red-300">Pastores Z.</p>
                            <p class="text-base font-black tabular-nums text-red-300" x-text="participations.reduce((acc, p) => acc + Number(p.zone_pastors), 0)"></p>
                        </div>
                        <div class="text-center bg-blue-600 rounded-lg p-1.5">
                            <p class="text-[9px] font-bold uppercase text-blue-100">Geral Ensino</p>
                            <p class="text-base font-black tabular-nums" x-text="participations.reduce((acc, p) => acc + Number(p.adults_members) + Number(p.adults_visitors) + Number(p.leaders) + Number(p.auxiliary_leaders) + Number(p.supervisors) + Number(p.zone_pastors), 0)"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sunday metrics (Keep hidden and set to 0 for teaching services) -->
            <input type="hidden" name="adults_members" value="0">
            <input type="hidden" name="adults_salvations" value="0">
            <input type="hidden" name="children_members" value="0">
            <input type="hidden" name="children_salvations" value="0">

            <!-- Section 3: Ofertas e Dízimos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Ofertas -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-sm font-black text-gray-900 flex items-center gap-2">
                            <i class="bi bi-wallet2 text-emerald-600"></i>
                            Ofertas
                        </h2>
                    </div>
                    <div class="p-4 sm:p-5 space-y-3">
                        @foreach($offeringTypes as $index => $type)
                            @php
                                $offering = $service->offerings->where('offering_type_id', $type->id)->first();
                            @endphp
                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-700">{{ $type->name }}</p>
                                </div>
                                <div class="relative w-36">
                                    <input type="hidden" name="offerings[{{ $index }}][offering_type_id]" value="{{ $type->id }}">
                                    <input type="number" step="0.01" name="offerings[{{ $index }}][amount]"
                                        class="offering-input w-full pl-8 pr-3 py-1.5 bg-white rounded-lg border border-gray-300 focus:border-emerald-600 text-xs sm:text-sm font-black text-right text-gray-800"
                                        value="{{ old("offerings.{$index}.amount", $offering ? $offering->amount : 0) }}">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400">MT</span>
                                </div>
                            </div>
                        @endforeach

                        <div class="space-y-3 pt-3 mt-4 border-t border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <p class="text-xs font-black text-orange-700 uppercase">Oferta Especial</p>
                                    <p class="text-[10px] text-gray-500">Ex: Campanhas, Semeadeira, etc.</p>
                                </div>
                                <div class="relative w-36">
                                    <input type="number" step="0.01" name="special_offerings_total" id="special_offer_input"
                                        class="w-full pl-8 pr-3 py-1.5 bg-orange-50 border border-orange-200 focus:bg-white focus:border-orange-600 rounded-lg text-xs sm:text-sm font-black text-right text-orange-800"
                                        value="{{ old('special_offerings_total', $service->special_offerings_total) }}">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-orange-600">MT</span>
                                </div>
                            </div>
                            <div class="p-3.5 bg-emerald-700 rounded-lg flex items-center justify-between text-white">
                                <span class="text-xs font-black uppercase tracking-wider">Total de Ofertas</span>
                                <span class="text-lg font-black" id="total_offerings_display">0,00 MT</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contribuições Individuais -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden flex flex-col">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-sm font-black text-gray-900 flex items-center gap-2">
                            <i class="bi bi-people text-blue-600"></i>
                            Contribuições Individuais
                        </h2>
                        <button type="button" id="addContributionBtn"
                            class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg font-bold text-xs hover:bg-blue-600 hover:text-white transition-all flex items-center gap-1.5">
                            <i class="bi bi-plus-lg"></i> Adicionar
                        </button>
                    </div>
                    <div class="p-4 flex-1 space-y-3 max-h-[420px] overflow-y-auto" id="contributionsContainer">
                        @foreach(old('individual_contributions', $service->tithes->map(fn($t) => ['type' => 'tithe', 'member_name' => $t->member_name, 'amount' => $t->amount, 'description' => $t->description])
                            ->concat($service->individualOfferings->map(fn($o) => ['type' => 'offering', 'member_name' => $o->member_name, 'amount' => $o->amount, 'description' => $o->description]))) as $index => $contribution)
                            <div class="contribution-row space-y-2 p-3 bg-gray-50 rounded-lg border border-gray-200 transition-all">
                                <div class="flex items-center gap-2">
                                    <div class="w-28">
                                        <select name="individual_contributions[{{ $index }}][type]"
                                            class="contribution-type w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-700">
                                            <option value="offering" @selected($contribution['type'] == 'offering')>Oferta</option>
                                            <option value="tithe" @selected($contribution['type'] == 'tithe')>Dízimo</option>
                                        </select>
                                    </div>
                                    <div class="flex-1 relative">
                                        <i class="bi bi-person absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                        <input type="text" name="individual_contributions[{{ $index }}][member_name]"
                                            value="{{ $contribution['member_name'] }}"
                                            placeholder="Nome do Contribuinte"
                                            class="w-full pl-7 pr-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700">
                                    </div>
                                    <div class="relative w-28">
                                        <input type="number" step="0.01" name="individual_contributions[{{ $index }}][amount]" 
                                            value="{{ $contribution['amount'] }}"
                                            placeholder="0.00"
                                            class="contribution-amount w-full pl-2.5 pr-7 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-black text-right text-gray-900">
                                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-bold text-gray-400">MT</span>
                                    </div>
                                    <button type="button" class="remove-contribution text-gray-400 hover:text-red-600 transition-colors bg-white hover:bg-red-50 p-1.5 rounded-lg border border-gray-200">
                                        <i class="bi bi-trash text-xs"></i>
                                    </button>
                                </div>
                                <div class="relative">
                                    <i class="bi bi-chat-left-text absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="text" name="individual_contributions[{{ $index }}][description]" 
                                        value="{{ $contribution['description'] ?? '' }}"
                                        placeholder="Observação (Opcional)"
                                        class="w-full pl-7 pr-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs text-gray-600">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4 mt-auto bg-gray-50 border-t border-gray-200 space-y-2">
                        <div class="flex justify-between items-center text-xs font-bold text-gray-600">
                            <span>Total Dízimos:</span>
                            <span id="total_tithes_display" class="text-blue-700">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-gray-600">
                            <span>Total Ofertas:</span>
                            <span id="total_ind_offerings_display" class="text-orange-700">0,00 MT</span>
                        </div>
                        <div class="p-3 bg-gray-900 rounded-lg flex items-center justify-between text-white">
                            <span class="text-xs font-black uppercase tracking-wider">Total Geral</span>
                            <span class="text-base font-black" id="total_contributions_display">0,00 MT</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grand Financial Summary & Comments -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
                <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white shadow-sm p-4 sm:p-5">
                    <label class="text-[10px] font-black uppercase tracking-wider text-gray-500 mb-2 block">Comentários e Observações</label>
                    <textarea name="observations" rows="6" placeholder="Fale sobre o mover, testemunhos ou ocorrências do culto..."
                        class="w-full px-3.5 py-2.5 bg-white border border-gray-300 focus:border-orange-600 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all text-xs sm:text-sm text-gray-700 resize-none">{{ old('observations', $service->observations) }}</textarea>
                </div>

                <div class="bg-gray-900 p-5 rounded-xl text-white space-y-4 shadow-sm border border-gray-800">
                    <h3 class="text-xs font-black uppercase tracking-wider text-orange-400">Resumo Geral</h3>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center border-b border-gray-800 pb-2">
                            <span class="text-gray-400">Ofertas Gerais</span>
                            <span class="font-bold" id="summary_offerings">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-800 pb-2">
                            <span class="text-gray-400">Dízimos</span>
                            <span class="font-bold" id="summary_tithes">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-800 pb-2">
                            <span class="text-gray-400">Ofertas Individuais</span>
                            <span class="font-bold" id="summary_ind_offerings">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-800 pb-2">
                            <span class="text-gray-400">Especiais</span>
                            <span class="font-bold" id="summary_specials">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm font-black uppercase">Total Final</span>
                            <span class="text-xl font-black text-emerald-400 tabular-nums" id="summary_grand_total">0,00 MT</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-orange-700 text-white rounded-lg font-black text-xs sm:text-sm uppercase tracking-wider hover:bg-orange-800 transition-all shadow-sm mt-2">
                        Atualizar Registro
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Template Unificado -->
    <template id="contributionRowTemplate">
        <div class="contribution-row space-y-2 p-3 bg-gray-50 rounded-lg border border-gray-200 transition-all">
            <div class="flex items-center gap-2">
                <div class="w-28">
                    <select name="individual_contributions[INDEX][type]"
                        class="contribution-type w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-700">
                        <option value="offering">Oferta</option>
                        <option value="tithe">Dízimo</option>
                    </select>
                </div>
                <div class="flex-1 relative">
                    <i class="bi bi-person absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="individual_contributions[INDEX][member_name]"
                        placeholder="Nome do Contribuinte"
                        class="w-full pl-7 pr-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700">
                </div>
                <div class="relative w-28">
                    <input type="number" step="0.01" name="individual_contributions[INDEX][amount]" placeholder="0.00"
                        class="contribution-amount w-full pl-2.5 pr-7 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-black text-right text-gray-900">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] font-bold text-gray-400">MT</span>
                </div>
                <button type="button"
                    class="remove-contribution text-gray-400 hover:text-red-600 transition-colors bg-white hover:bg-red-50 p-1.5 rounded-lg border border-gray-200">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            </div>
            <div class="relative">
                <i class="bi bi-chat-left-text absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="individual_contributions[INDEX][description]" placeholder="Observação (Opcional)"
                    class="w-full pl-7 pr-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs text-gray-600">
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ----- Financial Logic -----
            const contributionsContainer = document.getElementById('contributionsContainer');
            const contributionTemplate = document.getElementById('contributionRowTemplate');
            let contributionIndex = {{ count(old('individual_contributions', $service->tithes->map(fn($t) => ['type' => 'tithe', 'member_name' => $t->member_name, 'amount' => $t->amount, 'description' => $t->description])
                ->concat($service->individualOfferings->map(fn($o) => ['type' => 'offering', 'member_name' => $o->member_name, 'amount' => $o->amount, 'description' => $o->description])))) }};

            window.addContribution = function () {
                const clone = contributionTemplate.content.cloneNode(true);
                const row = clone.querySelector('.contribution-row');

                row.querySelectorAll('input, select').forEach(el => {
                    el.name = el.name.replace('INDEX', contributionIndex);
                    el.addEventListener('input', updateFinancials);
                    el.addEventListener('change', updateFinancials);
                });

                row.querySelector('.remove-contribution').addEventListener('click', () => {
                    row.remove();
                    updateFinancials();
                });

                contributionsContainer.appendChild(row);
                contributionIndex++;
            };

            document.getElementById('addContributionBtn').addEventListener('click', window.addContribution);

            // Re-attach listeners to existing rows
            document.querySelectorAll('.contribution-row').forEach(row => {
                row.querySelectorAll('input, select').forEach(el => {
                    el.addEventListener('input', updateFinancials);
                    el.addEventListener('change', updateFinancials);
                });
                row.querySelector('.remove-contribution').addEventListener('click', () => {
                    row.remove();
                    updateFinancials();
                });
            });

            function formatMT(value) {
                return value.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT';
            }

            function updateFinancials() {
                let offeringsTotal = 0;
                document.querySelectorAll('.offering-input').forEach(input => {
                    offeringsTotal += parseFloat(input.value) || 0;
                });

                let tithesTotal = 0;
                let indOfferingsTotal = 0;
                document.querySelectorAll('.contribution-row').forEach(row => {
                    const type = row.querySelector('.contribution-type').value;
                    const amount = parseFloat(row.querySelector('.contribution-amount').value) || 0;
                    if (type === 'tithe') tithesTotal += amount;
                    else indOfferingsTotal += amount;
                });

                let specialOffer = parseFloat(document.getElementById('special_offer_input').value) || 0;
                const grandTotal = offeringsTotal + tithesTotal + indOfferingsTotal + specialOffer;

                if(document.getElementById('total_offerings_display')) document.getElementById('total_offerings_display').innerText = formatMT(offeringsTotal);
                if(document.getElementById('total_tithes_display')) document.getElementById('total_tithes_display').innerText = formatMT(tithesTotal);
                if(document.getElementById('total_ind_offerings_display')) document.getElementById('total_ind_offerings_display').innerText = formatMT(indOfferingsTotal);
                if(document.getElementById('total_contributions_display')) document.getElementById('total_contributions_display').innerText = formatMT(tithesTotal + indOfferingsTotal);
                document.getElementById('summary_offerings').innerText = formatMT(offeringsTotal);
                document.getElementById('summary_tithes').innerText = formatMT(tithesTotal);
                document.getElementById('summary_ind_offerings').innerText = formatMT(indOfferingsTotal);
                document.getElementById('summary_specials').innerText = formatMT(specialOffer);
                document.getElementById('summary_grand_total').innerText = formatMT(grandTotal);
            }

            document.querySelectorAll('.offering-input, #special_offer_input').forEach(input => {
                input.addEventListener('input', updateFinancials);
            });
            updateFinancials();
        });
    </script>
@endsection
