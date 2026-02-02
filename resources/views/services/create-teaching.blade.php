@extends('layouts.app')

@section('title', 'Novo Culto de Ensino - Portal Life Church')
@section('page-title', 'Culto de Ensino')
@section('page-subtitle', 'Registro de métricas para a reunião de quarta-feira')

@section('content')
    <div class="space-y-8" x-data="{ 
                                                    guestPreacher: {{ old('preacher_name') ? 'true' : 'false' }},
                                                    serviceType: 'teaching'
                                                }">
        <!-- Header -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">
                    <a href="{{ route('services.index') }}" class="hover:underline">Cultos</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span>Culto de Ensino</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Registro de Culto de Ensino</h1>
            </div>
            <a href="{{ route('services.index') }}"
                class="group flex items-center bg-gray-50 text-gray-500 px-6 py-3 rounded-2xl hover:bg-gray-100 transition-all font-bold">
                <i class="bi bi-arrow-left text-lg mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Cancelar
            </a>
        </div>

        <form action="{{ route('services.store') }}" method="POST" id="serviceForm" class="space-y-8 pb-12">
            @csrf
            <input type="hidden" name="service_type" value="teaching">

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-[2rem] shadow-sm mb-8">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill text-red-500 text-xl mr-3"></i>
                        <h3 class="text-red-900 font-black uppercase tracking-widest text-sm">Erros de Validação</h3>
                    </div>
                    <ul class="list-disc list-inside text-red-700 text-sm font-bold space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Section 1: Cabeçalho -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                        <i class="bi bi-info-circle text-blue-600"></i>
                        Informações Gerais (Quarta-feira)
                    </h2>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Data do
                            Culto</label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                            class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Pregador</label>
                        <div class="space-y-4"
                            x-effect="if (guestPreacher && $refs.preacherSelect) { $refs.preacherSelect.value = ''; }">
                            <select name="preacher_id" x-ref="preacherSelect"
                                class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none custom-select">
                                <option value="">Selecione o pregador</option>
                                @foreach($preachers as $preacher)
                                    <option value="{{ $preacher->id }}" @selected(old('preacher_id') == $preacher->id)>
                                        {{ $preacher->name }}
                                    </option>
                                @endforeach
                            </select>

                            <label
                                class="flex items-center gap-3 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                <input type="checkbox" name="guest_preacher" value="1"
                                    class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    x-model="guestPreacher">
                                Pregador Convidado
                            </label>

                            <template x-if="guestPreacher">
                                <input type="text" name="preacher_name" value="{{ old('preacher_name') }}"
                                    placeholder="Nome do pregador convidado"
                                    class="w-full px-5 py-4 bg-blue-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 placeholder-blue-300">
                            </template>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Tema da
                            Mensagem</label>
                        <input type="text" name="theme" value="{{ old('theme') }}"
                            placeholder="Qual o tema central do culto?"
                            class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 placeholder-gray-300">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Visitantes Gerais
                            (Adultos)</label>
                        <input type="number" name="adults_visitors" value="{{ old('adults_visitors', 0) }}" min="0"
                            class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Visitantes Gerais
                            (Crianças)</label>
                        <input type="number" name="children_visitors" value="{{ old('children_visitors', 0) }}" min="0"
                            class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                    </div>
                </div>
            </div>

            <!-- Section: Participação por Zona (Exclusivo Culto de Ensino) -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-blue-50/30 flex items-center justify-between">
                    <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-blue-600"></i>
                        Participação por Zona
                    </h2>
                </div>
                <div class="overflow-x-auto p-4 md:p-8">
                    <table class="w-full border-separate border-spacing-2">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">
                                    Zona</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Membros</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Visit.</th>
                                <th
                                    class="p-4 text-[10px] font-black text-orange-600 uppercase tracking-widest text-center">
                                    Líderes</th>
                                <th
                                    class="p-4 text-[10px] font-black text-orange-400 uppercase tracking-widest text-center">
                                    Timótio</th>
                                <th
                                    class="p-4 text-[10px] font-black text-purple-600 uppercase tracking-widest text-center">
                                    Superv.</th>
                                <th class="p-4 text-[10px] font-black text-red-600 uppercase tracking-widest text-center">
                                    Pastores Z.</th>
                                <th class="p-4 text-[10px] font-black text-blue-600 uppercase tracking-widest text-center">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($zones as $index => $zone)
                                <tr class="group">
                                    <td class="p-4 font-black text-gray-900">
                                        {{ $zone->name }}
                                        <input type="hidden" name="zone_participations[{{ $index }}][zone_id]"
                                            value="{{ $zone->id }}">
                                    </td>
                                    <td class="p-2 bg-gray-50/50 group-hover:bg-white transition-colors">
                                        <input type="number" name="zone_participations[{{ $index }}][adults_members]"
                                            value="{{ old("zone_participations.{$index}.adults_members", 0) }}" min="0"
                                            class="zone-participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-gray-700">
                                    </td>
                                    <td class="p-2 bg-gray-50/50 group-hover:bg-white transition-colors">
                                        <input type="number" name="zone_participations[{{ $index }}][adults_visitors]"
                                            value="{{ old("zone_participations.{$index}.adults_visitors", 0) }}" min="0"
                                            class="zone-participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-gray-700">
                                    </td>
                                    <td class="p-2 bg-orange-50/30 group-hover:bg-white transition-colors">
                                        <input type="number" name="zone_participations[{{ $index }}][leaders]"
                                            value="{{ old("zone_participations.{$index}.leaders", 0) }}" min="0"
                                            class="zone-participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-orange-600">
                                    </td>
                                    <td class="p-2 bg-orange-50/10 group-hover:bg-white transition-colors">
                                        <input type="number" name="zone_participations[{{ $index }}][auxiliary_leaders]"
                                            value="{{ old("zone_participations.{$index}.auxiliary_leaders", 0) }}" min="0"
                                            class="zone-participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-orange-400">
                                    </td>
                                    <td class="p-2 bg-purple-50/30 group-hover:bg-white transition-colors">
                                        <input type="number" name="zone_participations[{{ $index }}][supervisors]"
                                            value="{{ old("zone_participations.{$index}.supervisors", 0) }}" min="0"
                                            class="zone-participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-purple-600">
                                    </td>
                                    <td class="p-2 bg-red-50/30 group-hover:bg-white transition-colors">
                                        <input type="number" name="zone_participations[{{ $index }}][zone_pastors]"
                                            value="{{ old("zone_participations.{$index}.zone_pastors", 0) }}" min="0"
                                            class="zone-participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-red-600">
                                    </td>
                                    <td class="p-4 text-center font-black text-blue-600 zone-row-total">0</td>
                                    <input type="hidden" name="zone_participations[{{ $index }}][children_members]" value="0">
                                    <input type="hidden" name="zone_participations[{{ $index }}][children_visitors]" value="0">
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-600 text-white rounded-2xl overflow-hidden">
                                <td class="p-6 font-black uppercase tracking-widest text-[10px] rounded-l-[1.5rem]">Total
                                    Ensino</td>
                                <td class="p-6 text-center font-black" id="total_teaching_members">0</td>
                                <td class="p-6 text-center font-black" id="total_teaching_visitors">0</td>
                                <td class="p-6 text-center font-black text-orange-200" id="total_teaching_leaders">0</td>
                                <td class="p-6 text-center font-black text-orange-100" id="total_teaching_aux_leaders">0
                                </td>
                                <td class="p-6 text-center font-black text-purple-200" id="total_teaching_supervisors">0
                                </td>
                                <td class="p-6 text-center font-black text-red-200" id="total_teaching_pastors">0</td>
                                <td class="p-6 text-center font-black bg-blue-700 rounded-r-[1.5rem]"
                                    id="total_teaching_grand">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Offsets Sunday fields to 0 as they might be required in model but not used here -->
            <!-- Offsets Sunday fields to 0 as they might be required in model but not used here -->
            <input type="hidden" name="adults_members" value="0">
            <input type="hidden" name="adults_salvations" value="0">
            <input type="hidden" name="children_members" value="0">
            <input type="hidden" name="children_salvations" value="0">

            <!-- Section 3: Ofertas e Dízimos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Ofertas -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                            <i class="bi bi-wallet2 text-green-600"></i>
                            Ofertas
                        </h2>
                    </div>
                    <div class="p-8 space-y-4">
                        @foreach($offeringTypes as $index => $type)
                            <div
                                class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl transition-all hover:bg-white hover:shadow-md border border-transparent hover:border-green-100 group">
                                <div class="flex-1">
                                    <p
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-green-600 transition-colors">
                                        {{ $type->name }}
                                    </p>
                                </div>
                                <div class="relative w-40">
                                    <input type="hidden" name="offerings[{{ $index }}][offering_type_id]"
                                        value="{{ $type->id }}">
                                    <input type="number" step="0.01" name="offerings[{{ $index }}][amount]"
                                        class="offering-input w-full pl-8 pr-4 py-2 bg-white rounded-xl border-gray-200 focus:border-green-500 focus:ring-green-500/10 font-black text-right text-gray-700"
                                        value="{{ old("offerings.{$index}.amount", 0) }}">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400">MT</span>
                                </div>
                            </div>
                        @endforeach

                        <div class="space-y-4 pt-4 mt-8 border-t border-gray-50">
                            <div class="flex items-center gap-4 group">
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Ofert.
                                        Especial</p>
                                    <p class="text-[9px] text-gray-400 italic">Ex: Campanhas, Semeadeira, etc.</p>
                                </div>
                                <div class="relative w-40">
                                    <input type="number" step="0.01" name="special_offerings_total" id="special_offer_input"
                                        class="w-full pl-8 pr-4 py-2 bg-orange-50 border-transparent focus:bg-white focus:border-orange-500 focus:ring-orange-500/10 rounded-xl font-black text-right text-orange-700 transition-all"
                                        value="{{ old('special_offerings_total', 0) }}">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-orange-400">MT</span>
                                </div>
                            </div>
                            <div
                                class="p-6 bg-green-600 rounded-[1.5rem] flex items-center justify-between text-white shadow-lg shadow-green-100">
                                <span class="text-xs font-black uppercase tracking-widest">Total de Ofertas</span>
                                <span class="text-2xl font-black" id="total_offerings_display">0,00 MT</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contribuições Individuais (Dízimos e Ofertas) -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                            <i class="bi bi-people text-blue-600"></i>
                            Contribuições Individuais
                        </h2>
                        <button type="button" @click="addContribution()" id="addContributionBtn"
                            class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl font-bold text-xs hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i> Adicionar
                        </button>
                    </div>
                    <div class="p-8 flex-1 space-y-4 max-h-[500px] overflow-y-auto" id="contributionsContainer">
                    </div>
                    <div class="p-8 mt-auto bg-gray-50/50 border-t border-gray-50 space-y-3">
                        <div class="flex justify-between items-center text-xs font-bold text-gray-500">
                            <span>Total Dízimos:</span>
                            <span id="total_tithes_display" class="text-blue-600">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-gray-500">
                            <span>Total Ofertas:</span>
                            <span id="total_ind_offerings_display" class="text-orange-600">0,00 MT</span>
                        </div>
                        <div class="p-4 bg-gray-900 rounded-2xl flex items-center justify-between text-white shadow-lg">
                            <span class="text-xs font-black uppercase tracking-widest">Total Geral</span>
                            <span class="text-xl font-black" id="total_contributions_display">0,00 MT</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grand Financial Summary & Comments -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <label
                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1 mb-4 block">Comentários e
                        Observações</label>
                    <textarea name="observations" rows="6"
                        placeholder="Fale sobre o mover, testemunhos ou ocorrências do culto..."
                        class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-[2rem] transition-all font-medium text-gray-700 placeholder-gray-300 resize-none">{{ old('observations') }}</textarea>
                </div>

                <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white space-y-6 shadow-xl shadow-blue-200">
                    <h3 class="text-lg font-black uppercase tracking-widest text-blue-200">Resumo Geral</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-blue-500/50 pb-4">
                            <span class="text-sm font-bold opacity-80">Ofertas Gerais</span>
                            <span class="text-sm font-black" id="summary_offerings">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-blue-500/50 pb-4">
                            <span class="text-sm font-bold opacity-80">Dízimos</span>
                            <span class="text-sm font-black" id="summary_tithes">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-blue-500/50 pb-4">
                            <span class="text-sm font-bold opacity-80">Ofertas Individuais</span>
                            <span class="text-sm font-black" id="summary_ind_offerings">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-blue-500/50 pb-4">
                            <span class="text-sm font-bold opacity-80">Especiais</span>
                            <span class="text-sm font-black" id="summary_specials">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-blue-400 mt-2 pt-4">
                            <span class="text-lg font-black tracking-tighter uppercase">Total Final</span>
                            <span class="text-3xl font-black tabular-nums" id="summary_grand_total">0,00 MT</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-5 bg-white text-blue-600 rounded-[1.5rem] font-black text-lg hover:bg-blue-50 transition-all shadow-xl shadow-blue-800/10 mt-4">
                        REGISTRAR NO SISTEMA
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Template Unificado -->
    <template id="contributionRowTemplate">
        <div
            class="contribution-row space-y-2 p-4 bg-gray-50 rounded-2xl group relative border border-transparent hover:border-blue-100 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-32">
                    <select name="individual_contributions[INDEX][type]"
                        class="contribution-type w-full px-3 py-2 bg-white border-transparent rounded-xl focus:bg-white focus:border-blue-500 focus:ring-0 text-xs font-bold text-gray-700">
                        <option value="offering">Oferta</option>
                        <option value="tithe">Dízimo</option>
                    </select>
                </div>
                <div class="flex-1 relative">
                    <i class="bi bi-person absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                    <input type="text" name="individual_contributions[INDEX][member_name]"
                        placeholder="Nome do Contribuinte"
                        class="w-full pl-8 pr-3 py-2 bg-white border-transparent rounded-xl focus:bg-white focus:border-blue-500 focus:ring-0 text-sm font-bold text-gray-700">
                </div>
                <div class="relative w-28">
                    <input type="number" step="0.01" name="individual_contributions[INDEX][amount]" placeholder="0.00"
                        class="contribution-amount w-full pl-3 pr-8 py-2 bg-white border-transparent rounded-xl focus:bg-white focus:border-blue-500 focus:ring-0 text-sm font-black text-right text-gray-900">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400">MT</span>
                </div>
                <button type="button"
                    class="remove-contribution text-gray-300 hover:text-red-500 transition-colors bg-white hover:bg-red-50 p-2 rounded-lg">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="relative">
                <i class="bi bi-chat-left-text absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                <input type="text" name="individual_contributions[INDEX][description]" placeholder="Observação (Opcional)"
                    class="w-full pl-8 pr-4 py-2 bg-white border-transparent rounded-xl focus:bg-white focus:border-blue-500 focus:ring-0 text-xs font-medium text-gray-500 italic">
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ----- Zone Participation (Teaching) Logic -----
            function updateZoneParticipation() {
                let totalMembers = 0;
                let totalVisitors = 0;
                let totalLeaders = 0;
                let totalAuxLeaders = 0;
                let totalSupervisors = 0;
                let totalPastors = 0;
                let totalChildren = 0;

                document.querySelectorAll('.group').forEach(row => {
                    const membersInput = row.querySelector('input[name*="adults_members"]');
                    const visitorsInput = row.querySelector('input[name*="adults_visitors"]');
                    const leadersInput = row.querySelector('input[name*="leaders"]');
                    const auxLeadersInput = row.querySelector('input[name*="auxiliary_leaders"]');
                    const supervisorsInput = row.querySelector('input[name*="supervisors"]');
                    const pastorsInput = row.querySelector('input[name*="zone_pastors"]');
                    const childrenMembersInput = row.querySelector('input[name*="children_members"]');
                    const childrenVisitorsInput = row.querySelector('input[name*="children_visitors"]');
                    const rowTotalDisplay = row.querySelector('.zone-row-total');

                    if (rowTotalDisplay) {
                        const members = parseInt(membersInput?.value) || 0;
                        const visitors = parseInt(visitorsInput?.value) || 0;
                        const leaders = parseInt(leadersInput?.value) || 0;
                        const auxLeaders = parseInt(auxLeadersInput?.value) || 0;
                        const supervisors = parseInt(supervisorsInput?.value) || 0;
                        const pastors = parseInt(pastorsInput?.value) || 0;
                        const childMembers = parseInt(childrenMembersInput?.value) || 0;
                        const childVisitors = parseInt(childrenVisitorsInput?.value) || 0;

                        // Total per zone includes members, visitors and leadership
                        const rowTotal = members + visitors + leaders + auxLeaders + supervisors + pastors + childMembers + childVisitors;

                        totalMembers += members;
                        totalVisitors += visitors;
                        totalLeaders += leaders;
                        totalAuxLeaders += auxLeaders;
                        totalSupervisors += supervisors;
                        totalPastors += pastors;
                        totalChildren += (childMembers + childVisitors);

                        rowTotalDisplay.innerText = rowTotal;
                    }
                });

                // Add general visitors
                const generalAdultsBtn = document.querySelector('input[name="adults_visitors"]');
                const generalChildrenBtn = document.querySelector('input[name="children_visitors"]');
                const genVisAdults = parseInt(generalAdultsBtn?.value) || 0;
                const genVisChildren = parseInt(generalChildrenBtn?.value) || 0;

                totalVisitors += genVisAdults;
                totalChildren += genVisChildren;

                document.getElementById('total_teaching_members').innerText = totalMembers;
                document.getElementById('total_teaching_visitors').innerText = totalVisitors + totalChildren;
                document.getElementById('total_teaching_leaders').innerText = totalLeaders;
                document.getElementById('total_teaching_aux_leaders').innerText = totalAuxLeaders;
                document.getElementById('total_teaching_supervisors').innerText = totalSupervisors;
                document.getElementById('total_teaching_pastors').innerText = totalPastors;
                document.getElementById('total_teaching_grand').innerText = totalMembers + totalVisitors + totalChildren + totalLeaders + totalAuxLeaders + totalSupervisors + totalPastors;
            }

            document.querySelectorAll('.zone-participation-input, input[name="adults_visitors"], input[name="children_visitors"]').forEach(input => {
                input.addEventListener('input', updateZoneParticipation);
            });
            updateZoneParticipation(); // Initial call

            // ----- Financial Logic -----
            const contributionsContainer = document.getElementById('contributionsContainer');
            const contributionTemplate = document.getElementById('contributionRowTemplate');
            let contributionIndex = 0;

            window.addContribution = function () {
                const clone = contributionTemplate.content.cloneNode(true);
                const row = clone.querySelector('.contribution-row');

                row.querySelectorAll('input, select').forEach(el => {
                    el.name = el.name.replace('INDEX', contributionIndex);
                    el.addEventListener('input', updateFinancials);
                });

                row.querySelector('.remove-contribution').addEventListener('click', () => {
                    row.remove();
                    updateFinancials();
                });

                contributionsContainer.appendChild(row);
                contributionIndex++;
            };

            document.getElementById('addContributionBtn').addEventListener('click', window.addContribution);
            // for (let i = 0; i < 3; i++) window.addContribution();

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

                document.getElementById('total_offerings_display').innerText = formatMT(offeringsTotal);
                document.getElementById('total_tithes_display').innerText = formatMT(tithesTotal);
                document.getElementById('total_ind_offerings_display').innerText = formatMT(indOfferingsTotal);
                document.getElementById('total_contributions_display').innerText = formatMT(tithesTotal + indOfferingsTotal);
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