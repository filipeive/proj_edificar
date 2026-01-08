@extends('layouts.app')

@section('title', 'Novo Culto - Portal Life Church')

@section('content')
    <div class="space-y-8" x-data="{ guestPreacher: {{ old('preacher_id') === 'other' ? 'true' : 'false' }} }">
        <!-- Header -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">
                    <a href="{{ route('services.index') }}" class="hover:underline">Cultos</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span>Registrar Novo</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Ficha de Celebração</h1>
            </div>
            <a href="{{ route('services.index') }}"
                class="group flex items-center bg-gray-50 text-gray-500 px-6 py-3 rounded-2xl hover:bg-gray-100 transition-all font-bold">
                <i class="bi bi-arrow-left text-lg mr-2 group-hover:-translate-x-1 transition-transform"></i>
                Cancelar
            </a>
        </div>

        <form action="{{ route('services.store') }}" method="POST" id="serviceForm" class="space-y-8 pb-12">
            @csrf

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
                        Informações Gerais
                    </h2>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Data do
                            Culto</label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                            class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Tipo de
                            Celebração</label>
                        <select name="service_type" required
                            class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                            <option value="1st">1º Culto</option>
                            <option value="2nd">2º Culto</option>
                            <option value="3rd">3º Culto</option>
                            <option value="4th">4º Culto</option>
                            <option value="special">Especial</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Pregador</label>
                        <div class="space-y-4">
                            <select name="preacher_id" @change="guestPreacher = $event.target.value === 'other'"
                                class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                                <option value="">Selecione o pregador</option>
                                @foreach($preachers as $preacher)
                                    <option value="{{ $preacher->id }}" @selected(old('preacher_id') == $preacher->id)>
                                        {{ $preacher->name }}
                                    </option>
                                @endforeach
                                <option value="other" @selected(old('preacher_id') === 'other')>Outro (Convidado)</option>
                            </select>

                            <template x-if="guestPreacher">
                                <input type="text" name="preacher_name" value="{{ old('preacher_name') }}"
                                    placeholder="Nome do pregador convidado"
                                    class="w-full px-5 py-4 bg-blue-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 placeholder-blue-300">
                            </template>
                        </div>
                    </div>

                    <div class="md:col-span-3 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Tema da
                            Mensagem</label>
                        <input type="text" name="theme" value="{{ old('theme') }}"
                            placeholder="Qual o tema central do culto?"
                            class="w-full px-5 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 placeholder-gray-300">
                    </div>
                </div>
            </div>

            <!-- Section 2: Dados de Participação (O Quadro) -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                        <i class="bi bi-grid-3x3-gap text-purple-600"></i>
                        Dados de Participação
                    </h2>
                    <div
                        class="px-4 py-1.5 bg-purple-50 rounded-full text-[10px] font-black text-purple-600 uppercase tracking-widest">
                        Total Geral: <span id="total_general_participation">0</span>
                    </div>
                </div>
                <div class="overflow-x-auto p-4 md:p-8">
                    <table class="w-full border-separate border-spacing-2">
                        <thead>
                            <tr>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">
                                    Descrição</th>
                                <th
                                    class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center bg-gray-50 rounded-t-2xl">
                                    Membros</th>
                                <th
                                    class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center bg-gray-50 rounded-t-2xl">
                                    Visitantes</th>
                                <th
                                    class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center bg-gray-50 rounded-t-2xl">
                                    Salvação</th>
                                <th class="p-4 text-[10px] font-black text-blue-600 uppercase tracking-widest text-center">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <!-- Adultos -->
                            <tr class="group">
                                <td class="p-4 font-black text-gray-900">Adultos</td>
                                <td class="p-2 bg-gray-50/50 group-hover:bg-white transition-colors">
                                    <input type="number" name="adults_members" value="{{ old('adults_members', 0) }}"
                                        min="0"
                                        class="participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-gray-700">
                                </td>
                                <td class="p-2 bg-gray-50/50 group-hover:bg-white transition-colors">
                                    <input type="number" name="adults_visitors" value="{{ old('adults_visitors', 0) }}"
                                        min="0"
                                        class="participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-gray-700">
                                </td>
                                <td class="p-2 bg-gray-50/50 group-hover:bg-white transition-colors">
                                    <input type="number" name="adults_salvations" value="{{ old('adults_salvations', 0) }}"
                                        min="0"
                                        class="participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-orange-600">
                                </td>
                                <td class="p-4 text-center font-black text-blue-600" id="row_adults_total">0</td>
                            </tr>
                            <!-- Crianças -->
                            <tr class="group">
                                <td class="p-4 font-black text-gray-900">Crianças</td>
                                <td class="p-2 bg-gray-50/50 group-hover:bg-white transition-colors">
                                    <input type="number" name="children_members" value="{{ old('children_members', 0) }}"
                                        min="0"
                                        class="participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-gray-700">
                                </td>
                                <td class="p-2 bg-gray-50/50 group-hover:bg-white transition-colors">
                                    <input type="number" name="children_visitors" value="{{ old('children_visitors', 0) }}"
                                        min="0"
                                        class="participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-gray-700">
                                </td>
                                <td class="p-2 bg-gray-50/50 group-hover:bg-white transition-colors">
                                    <input type="number" name="children_salvations"
                                        value="{{ old('children_salvations', 0) }}" min="0"
                                        class="participation-input w-full bg-transparent border-transparent focus:ring-0 text-center font-black text-orange-600">
                                </td>
                                <td class="p-4 text-center font-black text-blue-600" id="row_children_total">0</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-900 text-white rounded-2xl overflow-hidden">
                                <td class="p-6 font-black uppercase tracking-widest text-[10px] rounded-l-[1.5rem]">Total
                                    Geral</td>
                                <td class="p-6 text-center font-black" id="col_members_total">0</td>
                                <td class="p-6 text-center font-black" id="col_visitors_total">0</td>
                                <td class="p-6 text-center font-black text-orange-400" id="col_salvations_total">0</td>
                                <td class="p-6 text-center font-black bg-blue-600 rounded-r-[1.5rem]"
                                    id="grand_total_display">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

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

                <!-- Dízimos -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                            <i class="bi bi-person-check text-blue-600"></i>
                            Dízimos Individuais
                        </h2>
                        <button type="button" @click="addTithe()" id="addTitheBtn"
                            class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl font-bold text-xs hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i> Adicionar Manual
                        </button>
                    </div>
                    <div class="p-8 flex-1 space-y-4 max-h-[500px] overflow-y-auto" id="tithesContainer">
                        <!-- Tithe rows will be added here -->
                    </div>
                    <div class="p-8 mt-auto bg-gray-50/50 border-t border-gray-50">
                        <div
                            class="p-6 bg-blue-900 rounded-[1.5rem] flex items-center justify-between text-white shadow-lg shadow-blue-100">
                            <span class="text-xs font-black uppercase tracking-widest">Total de Dízimos</span>
                            <span class="text-2xl font-black" id="total_tithes_display">0,00 MT</span>
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
                            <span class="text-sm font-bold opacity-80">Total de Ofertas</span>
                            <span class="text-sm font-black" id="summary_offerings">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-blue-500/50 pb-4">
                            <span class="text-sm font-bold opacity-80">Total de Dízimos</span>
                            <span class="text-sm font-black" id="summary_tithes">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-blue-500/50 pb-4">
                            <span class="text-sm font-bold opacity-80">Especiais</span>
                            <span class="text-sm font-black" id="summary_specials">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
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

    <!-- Template para Dízimo -->
    <template id="titheRowTemplate">
        <div class="tithe-row flex items-center gap-4 group">
            <div class="flex-1 relative">
                <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                <input type="text" name="tithes[INDEX][member_name]" placeholder="Nome do Dizimista"
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:border-blue-500 focus:ring-0 text-sm font-bold text-gray-700">
            </div>
            <div class="relative w-32">
                <input type="number" step="0.01" name="tithes[INDEX][amount]" placeholder="Valor"
                    class="tithe-amount-input w-full pl-8 pr-4 py-2 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:border-blue-500 focus:ring-0 text-sm font-black text-right text-blue-700">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400">MT</span>
            </div>
            <button type="button"
                class="remove-tithe text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 p-2">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tithesContainer = document.getElementById('tithesContainer');
            const titheTemplate = document.getElementById('titheRowTemplate');
            let titheIndex = 0;

            function addTithe() {
                const clone = titheTemplate.content.cloneNode(true);
                const container = clone.querySelector('.tithe-row');

                // Replace INDEX in names
                container.querySelectorAll('input').forEach(input => {
                    input.name = input.name.replace('INDEX', titheIndex);
                    input.addEventListener('input', updateFinancials);
                });

                container.querySelector('.remove-tithe').addEventListener('click', () => {
                    container.remove();
                    updateFinancials();
                });

                tithesContainer.appendChild(container);
                titheIndex++;
            }

            // Add Initial 5 tithe rows
            for (let i = 0; i < 5; i++) addTithe();

            document.getElementById('addTitheBtn').addEventListener('click', addTithe);

            function formatMT(value) {
                return value.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT';
            }

            function updateParticipation() {
                const inputs = {
                    am: parseInt(document.querySelector('[name="adults_members"]').value) || 0,
                    av: parseInt(document.querySelector('[name="adults_visitors"]').value) || 0,
                    as: parseInt(document.querySelector('[name="adults_salvations"]').value) || 0,
                    cm: parseInt(document.querySelector('[name="children_members"]').value) || 0,
                    cv: parseInt(document.querySelector('[name="children_visitors"]').value) || 0,
                    cs: parseInt(document.querySelector('[name="children_salvations"]').value) || 0
                };

                const rows = {
                    adults: inputs.am + inputs.av + inputs.as,
                    children: inputs.cm + inputs.cv + inputs.cs
                };

                const cols = {
                    members: inputs.am + inputs.cm,
                    visitors: inputs.av + inputs.cv,
                    salvations: inputs.as + inputs.cs
                };

                const total = rows.adults + rows.children;

                document.getElementById('row_adults_total').innerText = rows.adults;
                document.getElementById('row_children_total').innerText = rows.children;
                document.getElementById('col_members_total').innerText = cols.members;
                document.getElementById('col_visitors_total').innerText = cols.visitors;
                document.getElementById('col_salvations_total').innerText = cols.salvations;
                document.getElementById('grand_total_display').innerText = total;
                document.getElementById('total_general_participation').innerText = total;
            }

            function updateFinancials() {
                let offeringsTotal = 0;
                document.querySelectorAll('.offering-input').forEach(input => {
                    offeringsTotal += parseFloat(input.value) || 0;
                });

                let tithesTotal = 0;
                document.querySelectorAll('.tithe-amount-input').forEach(input => {
                    tithesTotal += parseFloat(input.value) || 0;
                });

                let specialOffer = parseFloat(document.getElementById('special_offer_input').value) || 0;

                const grandTotal = offeringsTotal + tithesTotal + specialOffer;

                document.getElementById('total_offerings_display').innerText = formatMT(offeringsTotal);
                document.getElementById('total_tithes_display').innerText = formatMT(tithesTotal);

                document.getElementById('summary_offerings').innerText = formatMT(offeringsTotal);
                document.getElementById('summary_tithes').innerText = formatMT(tithesTotal);
                document.getElementById('summary_specials').innerText = formatMT(specialOffer);
                document.getElementById('summary_grand_total').innerText = formatMT(grandTotal);
            }

            // Listeners
            document.querySelectorAll('.participation-input').forEach(input => {
                input.addEventListener('input', updateParticipation);
            });

            document.querySelectorAll('.offering-input, #special_offer_input').forEach(input => {
                input.addEventListener('input', updateFinancials);
            });

            // Initial Calculations
            updateParticipation();
            updateFinancials();
        });
    </script>
@endsection