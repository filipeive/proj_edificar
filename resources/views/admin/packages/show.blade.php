@extends('layouts.app')

@section('title', 'Detalhes do Pacote - ' . $package->name)

@section('content')
    <div class="space-y-8" x-data="{ 
                            view: window.innerWidth < 768 ? 'grid' : 'list', 
                            search: '',
                            selected: [], 
                            selectAll: false,
                            updateSelection() {
                                this.selected = this.selectAll ? {{ $package->userCommitments->pluck('user_id') }} : [];
                            }
                        }">
        <!-- Header -->
        <div
            class="bg-white p-4 sm:p-6 md:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col lg:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('packages.index') }}"
                    class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 transition-all">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $package->name }}</h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Gestão de Membros e
                        Contribuições</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:flex-row flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto">
                @if($package->whatsapp_link)
                    <a href="{{ $package->whatsapp_link }}" target="_blank"
                        class="col-span-2 sm:w-auto bg-green-600 text-white px-4 md:px-8 py-3 md:py-4 rounded-xl sm:rounded-2xl hover:bg-green-700 transition-all font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center shadow-lg shadow-green-100">
                        <i class="bi bi-whatsapp mr-2 text-base sm:text-lg"></i> <span class="hidden sm:inline">Grupo</span> WhatsApp
                    </a>
                @endif
                <a href="{{ route('packages.export', $package) }}"
                    class="sm:w-auto bg-blue-600 text-white px-4 md:px-8 py-3 md:py-4 rounded-xl sm:rounded-2xl hover:bg-blue-700 transition-all font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="bi bi-file-earmark-excel mr-2"></i> Exportar
                </a>
                <a href="{{ route('packages.edit', $package) }}"
                    class="sm:w-auto bg-orange-600 text-white px-4 md:px-8 py-3 md:py-4 rounded-xl sm:rounded-2xl hover:bg-orange-700 transition-all font-black text-[10px] sm:text-xs uppercase tracking-widest flex items-center justify-center shadow-lg shadow-orange-100">
                    <i class="bi bi-pencil-square mr-2"></i> Editar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Stats Column -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 md:p-8 rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-xs sm:text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Informações do Pacote</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Valor Mínimo</span>
                            <span
                                class="text-sm font-black text-gray-900">{{ number_format($package->min_amount, 2, ',', '.') }}
                                MT</span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Valor Máximo</span>
                            <span class="text-sm font-black text-gray-900">
                                @if($package->max_amount)
                                    {{ number_format($package->max_amount, 2, ',', '.') }} MT
                                @else
                                    Sem Limite
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Membros Ativos</span>
                            <span class="text-sm font-black text-blue-600">{{ $package->getActiveMembersCount() }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase block mb-2">Descrição</span>
                            <p class="text-sm text-gray-600 leading-relaxed font-medium">
                                {{ $package->description ?? 'Nenhuma descrição fornecida.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- SMS/WhatsApp Bulk Actions -->
                <div class="bg-blue-600 p-6 md:p-8 rounded-[2rem] sm:rounded-[2.5rem] shadow-lg shadow-blue-100 text-white space-y-6">
                    <div>
                        <h3 class="text-xs sm:text-sm font-black uppercase tracking-widest mb-2">Ações de Massa</h3>
                        <p class="text-xs text-blue-100 font-medium">Use as ferramentas abaixo para comunicar com todos os
                            membros deste pacote de uma só vez.</p>
                    </div>

                    <div class="space-y-3">
                        <button
                            onclick="copyToClipboard('{{ $package->whatsapp_template ?? 'Paz do Senhor! Este é um lembrete do Projeto Edificar.' }}', 'Mensagem copiada!', this)"
                            class="w-full bg-white text-blue-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl flex items-center justify-center gap-2">
                            <i class="bi bi-clipboard-check"></i> Copiar Mensagem
                        </button>

                        <form action="{{ route('packages.send-bulk-sms', $package) }}" method="POST">
                            @csrf
                            <button type="button"
                                onclick="confirmAction('Tem certeza que deseja disparar SMS para todos os membros deste pacote?', 'Disparar SMS em Massa').then(result => { if(result.isConfirmed) this.closest('form').submit(); })"
                                class="w-full bg-blue-500 text-white border border-blue-400/30 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl flex items-center justify-center gap-2">
                                <i class="bi bi-broadcast"></i> Disparar SMS em Massa
                            </button>
                        </form>

                        <button
                            onclick="copyToClipboard('{{ $package->userCommitments->pluck('user.phone')->filter()->implode(', ') }}', 'Contactos copiados!', this)"
                            class="w-full bg-blue-500/10 text-white border border-white/20 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500/30 transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-person-lines-fill"></i> Copiar Contactos
                        </button>

                        @if (auth()->user()->managesPackage($package))
                        <form action="{{ route('packages.notify-commission', $package) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full bg-orange-500 text-white py-3 sm:py-4 rounded-xl sm:rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl flex items-center justify-center gap-2 mt-4">
                                <i class="bi bi-bell-fill"></i> Notificar Comissão
                            </button>
                        </form>
                        @endif

                    </div>

                    @if($package->whatsapp_link)
                        <a href="{{ $package->whatsapp_link }}" target="_blank"
                            class="w-full bg-green-500 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-green-600 transition-all shadow-xl flex items-center justify-center gap-2">
                            <i class="bi bi-whatsapp"></i> Ir para o Grupo
                        </a>
                    @endif
                </div>
            </div>

            <script>
                function copyToClipboard(text, successMessage, element = null) {
                    if (!text) {
                        alert('Nenhum conteúdo para copiar.');
                        return;
                    }

                    navigator.clipboard.writeText(text).then(() => {
                        const target = element || event.currentTarget;
                        const originalHTML = target.innerHTML;
                        const originalClass = target.className;

                        target.innerHTML = `<i class="bi bi-check2"></i> ${successMessage || ''}`;
                        if (target.tagName === 'BUTTON') {
                            target.classList.add('bg-green-500', 'text-white');
                        }

                        setTimeout(() => {
                            target.innerHTML = originalHTML;
                            target.className = originalClass;
                        }, 2000);
                    }).catch(err => {
                        console.error('Erro ao copiar: ', err);
                        const textArea = document.createElement("textarea");
                        textArea.value = text;
                        document.body.appendChild(textArea);
                        textArea.select();
                        try {
                            document.execCommand('copy');
                            alert(successMessage || 'Copiado!');
                        } catch (err) {
                            alert('Erro ao copiar. Por favor copie manualmente.');
                        }
                        document.body.removeChild(textArea);
                    });
                }
            </script>

            <!-- Members List Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Add Member Form -->
                <div class="bg-white p-6 md:p-8 rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-xs sm:text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Adicionar Membro ao Pacote
                    </h3>
                    <form action="{{ route('packages.assign', $package) }}" method="POST"
                        class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="flex-1">
                            <label for="user_id"
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Selecionar
                                Membro</label>
                            <select name="user_id" id="user_id"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Escolha um membro...</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full md:w-32">
                            <label for="committed_amount"
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Valor
                                (MT)</label>
                            <input type="number" name="committed_amount" id="committed_amount"
                                value="{{ $package->min_amount }}" step="0.01"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 h-[46px]">
                            <i class="bi bi-person-plus-fill"></i>
                        </button>
                    </form>
                </div>

                <div
                    class="p-4 sm:p-6 md:p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/10">
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest whitespace-nowrap">Membros
                            Comprometidos</h3>

                        <!-- Search Input -->
                        <div class="relative w-full md:w-72">
                            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="search" placeholder="Pesquisar membro ou telefone..."
                                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 shadow-sm focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>

                        <!-- Bulk Actions Toolbar -->
                        <div x-show="selected.length > 0" x-transition
                            class="flex items-center gap-2 bg-blue-50 px-3 py-1 rounded-xl border border-blue-100">
                            <span class="text-[10px] font-black uppercase text-blue-600"
                                x-text="selected.length + ' selecionado(s)'"></span>
                            <form action="{{ route('packages.members.bulk-remove', $package) }}" method="POST"
                                onsubmit="return confirm('Tem certeza que deseja remover os membros selecionados?');">
                                @csrf
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="user_ids[]" :value="id">
                                </template>
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="flex bg-gray-50 p-1 rounded-xl border border-gray-100 shrink-0">
                        <button @click="view = 'list'"
                            :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                            <i class="bi bi-list-ul"></i>
                        </button>
                        <button @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                            <i class="bi bi-grid-fill"></i>
                        </button>
                    </div>
                </div>
                <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-4 md:px-8 py-4 w-10">
                                    <input type="checkbox" x-model="selectAll" @change="updateSelection()"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </th>
                                <th
                                    class="px-4 md:px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Membro</th>
                                <th
                                    class="px-4 md:px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Valor</th>
                                <th
                                    class="hidden lg:table-cell px-4 md:px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Célula</th>
                                <th
                                    class="hidden xl:table-cell px-4 md:px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Zona</th>
                                <th
                                    class="px-4 md:px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Estado Mês</th>
                                <th
                                    class="px-4 md:px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Admissão</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($package->userCommitments as $commitment)
                                <tr class="hover:bg-gray-50/50 transition-colors"
                                    x-show="!search || '{{ strtolower($commitment->user->name) }}'.includes(search.toLowerCase()) || '{{ $commitment->user->phone }}'.includes(search)">
                                    <td class="px-8 py-6">
                                        <input type="checkbox" value="{{ $commitment->user_id }}" x-model="selected"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-400 text-xs">
                                                {{ strtoupper(substr($commitment->user->name, 0, 1)) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-black text-gray-900 leading-tight">{{ $commitment->user->name }}</span>
                                                <span
                                                    class="text-[10px] text-gray-400 font-bold uppercase">{{ $commitment->user->phone }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span
                                            class="text-sm font-black text-gray-900">{{ number_format($commitment->committed_amount, 2, ',', '.') }}
                                            MT</span>
                                    </td>
                                    <td class="hidden lg:table-cell px-4 md:px-8 py-6">
                                        <span class="text-xs font-bold text-gray-600">
                                            {{ $commitment->user->cell->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="hidden xl:table-cell px-4 md:px-8 py-6">
                                        <span class="text-xs font-bold text-gray-600">
                                            {{ $commitment->user->cell->supervision->zone->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- Add Contribution Shortcut -->
                                            <a href="{{ route('contributions.create') }}?user_id={{ $commitment->user_id }}&package_id={{ $package->id }}"
                                                class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center hover:bg-orange-600 hover:text-white transition-all shadow-sm"
                                                title="Adicionar Contribuição">
                                                <i class="bi bi-plus-circle-fill"></i>
                                            </a>

                                            <!-- Edit Member Info Modal Trigger -->
                                            <button type="button" @click="$dispatch('open-edit-member-modal', { 
                                                                                                                userId: {{ $commitment->user_id }}, 
                                                                                                                userName: '{{ $commitment->user->name }}', 
                                                                                                                phone: '{{ $commitment->user->phone }}', 
                                                                                                                cellId: '{{ $commitment->user->cell_id }}',
                                                                                                                amount: '{{ $commitment->committed_amount }}'
                                                                                                            })"
                                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                                title="Editar Dados">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>

                                            <!-- Change Package -->
                                            <button type="button" @click="$dispatch('open-change-package-modal', { 
                                                                                userId: {{ $commitment->user_id }}, 
                                                                                userName: '{{ $commitment->user->name }}',
                                                                                currentAmount: '{{ $commitment->committed_amount }}'
                                                                            })"
                                                class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center hover:bg-purple-600 hover:text-white transition-all shadow-sm"
                                                title="Mudar de Pacote">
                                                <i class="bi bi-arrow-left-right"></i>
                                            </button>

                                            <!-- Remove Member -->
                                            <form
                                                action="{{ route('packages.members.remove', [$package, $commitment->user_id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja remover este membro do pacote?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                                    title="Remover do Pacote">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                            @if($commitment->user->phone)
                                                @php
                                                    $name = $commitment->user->name;
                                                    $smsBody = str_replace('[NOME]', $name, $package->sms_template ?? "Olá [NOME], lembrete de contribuição para o Projetor Edificar.");
                                                    $whatsappBody = str_replace('[NOME]', $name, $package->whatsapp_template ?? "Olá [NOME], este é um lembrete do Projetor Edificar.");
                                                    $cleanPhone = preg_replace('/[^0-9]/', '', $commitment->user->phone);
                                                @endphp
                                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($whatsappBody) }}"
                                                    target="_blank"
                                                    class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm"
                                                    title="Mudar WhatsApp Individual">
                                                    <i class="bi bi-whatsapp"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @php
                                            $currentMonthContribution = $commitment->user->contributions()
                                                ->where('package_id', $package->id)
                                                ->whereMonth('contribution_date', now()->month)
                                                ->whereYear('contribution_date', now()->year)
                                                ->first();
                                        @endphp

                                        @if($currentMonthContribution)
                                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                                {{ $currentMonthContribution->isVerified() ? 'bg-green-50 text-green-600 border-green-100' : 
                                                   ($currentMonthContribution->isRejected() ? 'bg-red-50 text-red-600 border-red-100' : 'bg-yellow-50 text-yellow-600 border-yellow-100') }}">
                                                {{ $currentMonthContribution->getStatusLabel() }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border bg-gray-50 text-gray-400 border-gray-100">
                                                Não Registado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <span
                                            class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                                                                                                                        {{ $commitment->isActive() ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                            {{ $commitment->isActive() ? 'Ativo' : 'Encerrado' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-10 text-center">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nenhum membro
                                            encontrado neste pacote.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Grid View for Members -->
                <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/30">
                    @forelse($package->userCommitments as $commitment)
                        <div x-show="!search || '{{ strtolower($commitment->user->name) }}'.includes(search.toLowerCase()) || '{{ $commitment->user->phone }}'.includes(search)"
                            class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-md transition-all relative">
                            <div class="absolute top-6 left-6 z-10">
                                <input type="checkbox" value="{{ $commitment->user_id }}" x-model="selected"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-5 h-5">
                            </div>
                            <div class="absolute top-6 right-6">
                                <span
                                    class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                                                                {{ $commitment->isActive() ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                    {{ $commitment->isActive() ? 'Ativo' : 'Encerrado' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center font-black text-xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    {{ strtoupper(substr($commitment->user->name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="text-sm font-black text-gray-900">{{ $commitment->user->name }}</h4>
                                    <span
                                        class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $commitment->user->phone }}</span>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-2xl mb-6 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Compromisso</span>
                                    <span
                                        class="text-sm font-black text-gray-900">{{ number_format($commitment->committed_amount, 2, ',', '.') }}
                                        MT</span>
                                </div>
                                <div class="flex justify-between items-center text-[10px] font-bold">
                                    <span class="text-gray-400 uppercase">Célula</span>
                                    <span class="text-gray-900">{{ $commitment->user->cell->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[10px] font-bold pt-2 border-t border-gray-100">
                                    <span class="text-gray-400 uppercase">Estado Mês</span>
                                    @php
                                        $currentMonthContribution = $commitment->user->contributions()
                                            ->where('package_id', $package->id)
                                            ->whereMonth('contribution_date', now()->month)
                                            ->whereYear('contribution_date', now()->year)
                                            ->first();
                                    @endphp

                                    @if($currentMonthContribution)
                                        <span class="{{ $currentMonthContribution->isVerified() ? 'text-green-600' : 
                                               ($currentMonthContribution->isRejected() ? 'text-red-600' : 'text-yellow-600') }}">
                                            {{ $currentMonthContribution->getStatusLabel() }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Não Registado</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 mt-auto">
                                <a href="{{ route('contributions.create') }}?user_id={{ $commitment->user_id }}&package_id={{ $package->id }}"
                                    class="w-full bg-gray-900 text-white text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all flex items-center justify-center gap-2">
                                    <i class="bi bi-plus-circle"></i> Contribuir
                                </a>
                                <div class="flex items-center justify-between gap-1">
                                    <button type="button" @click="$dispatch('open-edit-member-modal', { 
                                                                                            userId: {{ $commitment->user_id }}, 
                                                                                            userName: '{{ $commitment->user->name }}', 
                                                                                            phone: '{{ $commitment->user->phone }}', 
                                                                                            cellId: '{{ $commitment->user->cell_id }}',
                                                                                            amount: '{{ $commitment->committed_amount }}'
                                                                                        })"
                                        class="flex-1 h-10 bg-blue-50 text-blue-600 flex items-center justify-center rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                        title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Change Package -->
                                    <button type="button" @click="$dispatch('open-change-package-modal', { 
                                                                        userId: {{ $commitment->user_id }}, 
                                                                        userName: '{{ $commitment->user->name }}',
                                                                        currentAmount: '{{ $commitment->committed_amount }}'
                                                                    })"
                                        class="flex-1 h-10 bg-purple-50 text-purple-600 flex items-center justify-center rounded-xl hover:bg-purple-600 hover:text-white transition-all shadow-sm"
                                        title="Mudar Pacote">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>

                                    <!-- Remove -->
                                    <form action="{{ route('packages.members.remove', [$package, $commitment->user_id]) }}"
                                        method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja remover este membro do pacote?');"
                                        class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full h-10 bg-red-50 text-red-600 flex items-center justify-center rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                            title="Remover">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                    @if($commitment->user->phone)
                                        @php
                                            $name = $commitment->user->name;
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $commitment->user->phone);
                                            $whatsappBody = str_replace('[NOME]', $name, $package->whatsapp_template ?? "Olá [NOME], este é um lembrete do Projetor Edificar.");
                                        @endphp
                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($whatsappBody) }}"
                                            target="_blank"
                                            class="flex-1 h-10 bg-green-50 text-green-600 flex items-center justify-center rounded-xl hover:bg-green-600 hover:text-white transition-all shadow-sm"
                                            title="WhatsApp">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400">
                            <p class="text-xs font-bold uppercase tracking-widest italic">Nenhum membro encontrado.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div x-data="{ 
                                        show: false, 
                                        userId: '', 
                                        userName: '', 
                                        phone: '', 
                                        cellId: '',
                                        amount: ''
                                    }" @open-edit-member-modal.window="
                                        show = true; 
                                        userId = $event.detail.userId; 
                                        userName = $event.detail.userName; 
                                        phone = $event.detail.phone; 
                                        cellId = $event.detail.cellId;
                                        amount = $event.detail.amount;
                                    " x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="show = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <div
                class="bg-white rounded-[2.5rem] overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full z-10 border border-gray-100">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Editar Dados do Membro
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1" x-text="userName">
                        </p>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('packages.update-member', $package) }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="user_id" :value="userId">

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Telefone</label>
                            <input type="text" name="phone" x-model="phone"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Célula</label>
                            <select name="cell_id" x-model="cellId"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500">
                                <option value="">Sem Célula</option>
                                @foreach(\App\Models\Cell::orderBy('name')->get() as $cell)
                                    <option value="{{ $cell->id }}">{{ $cell->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Valor
                                do Compromisso (MT)</label>
                            <input type="number" name="committed_amount" x-model="amount" step="0.01"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="show = false"
                            class="flex-1 bg-gray-100 text-gray-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Package Modal -->
    <div x-data="{ 
                                show: false, 
                                userId: '', 
                                userName: '', 
                                amount: ''
                            }" @open-change-package-modal.window="
                                show = true; 
                                userId = $event.detail.userId; 
                                userName = $event.detail.userName; 
                                amount = $event.detail.currentAmount;
                            " x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="show = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <div
                class="bg-white rounded-[2.5rem] overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full z-10 border border-gray-100">
                <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Mudar Pacote</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1" x-text="userName"></p>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>

                <form :action="`{{ url('admin/packages/' . $package->id . '/members') }}/${userId}/change-package`"
                    method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Para
                                qual pacote deseja mover?</label>
                            <select name="new_package_id"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                required>
                                @foreach($allPackages as $p)
                                    @if($p->id !== $package->id)
                                        <option value="{{ $p->id }}">{{ $p->name }} (Min: {{ number_format($p->min_amount, 2) }} MT)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Novo
                                Valor do Compromisso (MT)</label>
                            <input type="number" name="committed_amount" x-model="amount" step="0.01"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="show = false"
                            class="flex-1 bg-gray-100 text-gray-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 bg-purple-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-purple-700 transition-all shadow-lg shadow-purple-100">
                            Confirmar Mudança
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection