@extends('layouts.app')

@section('title', 'Central de SMS & Notificações - Portal Life Church')
@section('page-title', 'Central de SMS')
@section('page-subtitle', 'Monitoramento de envios, alertas da rede pastoral e comunicados em massa')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('sms.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-clockwise"></i>
            <span>Atualizar</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-5" x-data="{
    activeTab: '{{ request('tab', 'history') }}',
    targetType: 'all_members',
    messageText: '',
    selectedMessage: null,
    showModal: false,
    recipientCount: {{ $counts['all_members'] ?? 0 }},
    calculatingCount: false,

    get charCount() {
        return this.messageText.length;
    },
    get partsCount() {
        if (this.charCount === 0) return 1;
        return this.charCount <= 160 ? 1 : Math.ceil(this.charCount / 153);
    },
    insertTag(tag) {
        this.messageText += ' ' + tag + ' ';
    },
    openMessage(msg) {
        this.selectedMessage = msg;
        this.showModal = true;
    },
    updateRecipientsCount() {
        this.calculatingCount = true;
        let params = new URLSearchParams();
        params.append('target_type', this.targetType);
        
        let cellSelect = document.getElementById('cell_id_select');
        if (cellSelect && cellSelect.value) params.append('cell_id', cellSelect.value);

        let supSelect = document.getElementById('sup_id_select');
        if (supSelect && supSelect.value) params.append('supervision_id', supSelect.value);

        let zoneSelect = document.getElementById('zone_id_select');
        if (zoneSelect && zoneSelect.value) params.append('zone_id', zoneSelect.value);

        let manualInput = document.getElementById('manual_phone_input');
        if (manualInput && manualInput.value) params.append('manual_phone', manualInput.value);

        fetch('{{ route('sms.recipients-count') }}?' + params.toString())
            .then(res => res.json())
            .then(data => {
                this.recipientCount = data.count;
                this.calculatingCount = false;
            })
            .catch(() => {
                this.calculatingCount = false;
            });
    }
}">

    <!-- Top Navigation & Breadcrumbs (Desktop) -->
    <div class="hidden md:flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-600 dark:text-zinc-400">
            <span class="text-orange-700 dark:text-orange-400">Comunicações</span>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <span class="text-gray-900 dark:text-white">Central de SMS</span>
        </div>
        <div class="flex items-center gap-2">
            <button @click="activeTab = 'send'"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                <i class="bi bi-chat-plus-fill text-xs"></i>
                <span>Novo Comunicado / Envio</span>
            </button>
        </div>
    </div>

    <!-- Executive Header Banner -->
    <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800/40 text-xl shadow-sm">
                    <i class="bi bi-chat-quote-fill"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-orange-700 dark:text-orange-400">Central Telefónica & Mensageria</span>
                        @if($gatewayInfo['configured'])
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Gateway Ativo ({{ strtoupper($gatewayInfo['driver']) }})
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40">
                                <i class="bi bi-exclamation-triangle-fill text-[9px]"></i> Gateway em Modo Teste ({{ $gatewayInfo['driver'] }})
                            </span>
                        @endif
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Central de SMS & Rede Pastoral</h1>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                        Remetente oficial: <strong class="text-gray-700 dark:text-zinc-200">{{ $gatewayInfo['sender'] }}</strong> • Monitoramento em tempo real de mensagens disparadas
                    </p>
                </div>
            </div>

            <!-- Tab Switcher Header -->
            <div class="flex items-center gap-1.5 bg-gray-100 dark:bg-zinc-800 p-1 rounded-xl self-start md:self-auto border border-gray-200 dark:border-zinc-700">
                <button type="button" @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'bg-white dark:bg-zinc-900 text-orange-700 dark:text-orange-400 shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 font-bold hover:text-gray-900'"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs transition">
                    <i class="bi bi-list-check"></i>
                    <span>Monitoramento</span>
                </button>
                <button type="button" @click="activeTab = 'send'"
                    :class="activeTab === 'send' ? 'bg-white dark:bg-zinc-900 text-orange-700 dark:text-orange-400 shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 font-bold hover:text-gray-900'"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs transition">
                    <i class="bi bi-broadcast"></i>
                    <span>Novo Comunicado</span>
                </button>
                <button type="button" @click="activeTab = 'network'"
                    :class="activeTab === 'network' ? 'bg-white dark:bg-zinc-900 text-orange-700 dark:text-orange-400 shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 font-bold hover:text-gray-900'"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs transition">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Rede de Visitantes</span>
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Enviados -->
        <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-zinc-400">Total Enviados</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white mt-0.5">{{ number_format($totalSent) }}</h3>
                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">Com êxito</p>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/40">
                <i class="bi bi-send-check-fill text-lg"></i>
            </div>
        </div>

        <!-- Enviados Hoje -->
        <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-zinc-400">Disparados Hoje</p>
                <h3 class="text-xl sm:text-2xl font-black text-blue-700 dark:text-blue-400 mt-0.5">{{ number_format($totalToday) }}</h3>
                <p class="text-[10px] text-gray-400">Atividade recente</p>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800/40">
                <i class="bi bi-calendar-event-fill text-lg"></i>
            </div>
        </div>

        <!-- Taxa de Sucesso -->
        <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-zinc-400">Taxa de Sucesso</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white mt-0.5">{{ $successRate }}%</h3>
                <p class="text-[10px] text-gray-400 font-medium">{{ $totalAll }} total processadas</p>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 border border-orange-100 dark:border-orange-800/40">
                <i class="bi bi-pie-chart-fill text-lg"></i>
            </div>
        </div>

        <!-- Falhas -->
        <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-zinc-400">Falhas / Erros</p>
                <h3 class="text-xl sm:text-2xl font-black {{ $totalFailed > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} mt-0.5">{{ number_format($totalFailed) }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Requer atenção</p>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $totalFailed > 0 ? 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800/40' : 'bg-gray-50 dark:bg-zinc-800 text-gray-400 border border-gray-200 dark:border-zinc-700' }}">
                <i class="bi bi-exclamation-triangle-fill text-lg"></i>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 1: MONITORAMENTO & HISTÓRICO ==================== -->
    <div x-show="activeTab === 'history'" class="space-y-4">
        <!-- Filter Bar -->
        <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3.5 sm:p-4 shadow-sm">
            <form method="GET" action="{{ route('sms.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <input type="hidden" name="tab" value="history">

                <!-- Busca -->
                <div class="space-y-1 sm:col-span-2">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-zinc-400">Buscar</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Telefone, nome do destinatário ou texto..."
                            class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white pl-8 pr-3 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>

                <!-- Status -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-zinc-400">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white px-3 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Todos os Status</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Enviados</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Falhas</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendentes</option>
                    </select>
                </div>

                <!-- Contexto -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-zinc-400">Contexto</label>
                    <select name="context" class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white px-3 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Todos os Tipos</option>
                        <option value="visitor_network_leader" {{ request('context') === 'visitor_network_leader' ? 'selected' : '' }}>Rede: Líder de Célula</option>
                        <option value="visitor_network_supervisor" {{ request('context') === 'visitor_network_supervisor' ? 'selected' : '' }}>Rede: Supervisor</option>
                        <option value="visitor_network_pastor" {{ request('context') === 'visitor_network_pastor' ? 'selected' : '' }}>Rede: Pastor de Zona</option>
                        <option value="visitor_welcome" {{ request('context') === 'visitor_welcome' ? 'selected' : '' }}>Boas-vindas Visitante</option>
                        <option value="broadcast" {{ request('context') === 'broadcast' ? 'selected' : '' }}>Comunicado Geral</option>
                        <option value="custom" {{ request('context') === 'custom' ? 'selected' : '' }}>Envio Manual</option>
                    </select>
                </div>

                <!-- Botões -->
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                        <i class="bi bi-filter"></i>
                        <span>Filtrar</span>
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'context', 'period']))
                        <a href="{{ route('sms.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-zinc-700 px-2.5 py-1.5 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800"
                            title="Limpar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Messages Table Card -->
        <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800 text-left text-xs">
                    <thead class="bg-gray-50/75 dark:bg-zinc-800/50 uppercase tracking-wider text-[10px] font-black text-gray-500 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Destinatário</th>
                            <th scope="col" class="px-4 py-3">Telefone</th>
                            <th scope="col" class="px-4 py-3">Contexto</th>
                            <th scope="col" class="px-4 py-3">Mensagem</th>
                            <th scope="col" class="px-4 py-3">Data / Hora</th>
                            <th scope="col" class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-800 text-gray-700 dark:text-zinc-300">
                        @forelse($messages as $msg)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                                <!-- Status -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($msg->status === 'sent')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-0.5 text-[10px] font-black text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                                            <i class="bi bi-check2-circle"></i> Enviado
                                        </span>
                                    @elseif($msg->status === 'failed')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 dark:bg-red-950/40 px-2.5 py-0.5 text-[10px] font-black text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/40"
                                            title="{{ $msg->error_message }}">
                                            <i class="bi bi-x-circle"></i> Falha
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 dark:bg-amber-950/40 px-2.5 py-0.5 text-[10px] font-black text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40">
                                            <i class="bi bi-clock-history"></i> Pendente
                                        </span>
                                    @endif
                                </td>

                                <!-- Destinatário -->
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ $msg->recipient_name ?? 'Avulso' }}
                                    </div>
                                    @if($msg->recipient_type)
                                        <span class="text-[10px] text-gray-400 uppercase font-semibold">
                                            {{ ucfirst($msg->recipient_type) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Telefone -->
                                <td class="px-4 py-3 whitespace-nowrap font-mono font-bold text-gray-800 dark:text-zinc-200">
                                    {{ $msg->phone }}
                                </td>

                                <!-- Contexto -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-zinc-800 px-2 py-0.5 text-[10px] font-bold text-gray-700 dark:text-zinc-300">
                                        {{ $msg->context_label }}
                                    </span>
                                </td>

                                <!-- Mensagem -->
                                <td class="px-4 py-3 max-w-xs">
                                    <div class="truncate text-xs text-gray-600 dark:text-zinc-300 cursor-pointer hover:text-orange-700 dark:hover:text-orange-400"
                                        @click="openMessage({{ Js::from($msg) }})">
                                        {{ $msg->message }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        {{ $msg->characters_count }} caracteres • {{ $msg->parts_count }} SMS
                                    </div>
                                </td>

                                <!-- Data -->
                                <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-zinc-400">
                                    <div class="font-bold text-gray-900 dark:text-zinc-200">
                                        {{ $msg->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-[10px]">
                                        {{ $msg->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                <!-- Ações -->
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Ver Detalhes -->
                                        <button type="button" @click="openMessage({{ Js::from($msg) }})"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 transition"
                                            title="Ver Detalhes do SMS">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <!-- Reenviar -->
                                        <form method="POST" action="{{ route('sms.resend', $msg) }}" class="inline"
                                            onsubmit="return confirm('Deseja reenviar esta mensagem para {{ $msg->phone }}?')">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-orange-200 dark:border-orange-800/40 bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400 hover:bg-orange-100 transition"
                                                title="Reenviar SMS">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-zinc-500">
                                    <i class="bi bi-inbox text-3xl mb-2 block"></i>
                                    <p class="font-bold text-sm">Nenhuma mensagem SMS encontrada.</p>
                                    <p class="text-xs">Os envios do sistema ou comunicados manuais serão listados aqui.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($messages->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-zinc-800">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== TAB 2: NOVO COMUNICADO / ENVIO EM MASSA ==================== -->
    <div x-show="activeTab === 'send'" class="space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Coluna do Formulário -->
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6 shadow-sm">
                    <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-gray-100 dark:border-zinc-800">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400">
                            <i class="bi bi-broadcast text-base"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-black text-gray-900 dark:text-white">Criar Envio de SMS</h2>
                            <p class="text-xs text-gray-500 dark:text-zinc-400">Envie avisos, convites ou comunicados pastorais para grupos ou números avulsos</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('sms.send') }}" class="space-y-4"
                        onsubmit="return confirm('Confirma o disparo de SMS para os destinatários selecionados?')">
                        @csrf

                        <!-- Grupo Destinatário -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 dark:text-zinc-300">
                                1. Selecionar Destinatários <span class="text-red-500">*</span>
                            </label>
                            <select name="target_type" x-model="targetType" @change="updateRecipientsCount()" required
                                class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white px-3 py-2 text-xs sm:text-sm font-semibold focus:border-orange-500 focus:ring-orange-500">
                                <optgroup label="Segmentos Gerais">
                                    <option value="all_members">Todos os Membros Ativos ({{ $counts['all_members'] }})</option>
                                    <option value="cell_members">Membros de uma Célula Específica</option>
                                    <option value="supervision_members">Membros de uma Supervisão Específica</option>
                                    <option value="zone_members">Membros de uma Zona Pastoral</option>
                                </optgroup>
                                <optgroup label="Liderança">
                                    <option value="cell_leaders">Todos os Líderes de Célula ({{ $counts['cell_leaders'] }})</option>
                                    <option value="supervisors">Todos os Supervisores ({{ $counts['supervisors'] }})</option>
                                    <option value="pastors">Todos os Pastores ({{ $counts['pastors'] }})</option>
                                </optgroup>
                                <optgroup label="Visitantes">
                                    <option value="pending_visitors">Visitantes Pendentes de Contato ({{ $counts['pending_visitors'] }})</option>
                                    <option value="all_visitors">Todos os Visitantes Cadastrados ({{ $counts['all_visitors'] }})</option>
                                </optgroup>
                                <optgroup label="Envio Avulso">
                                    <option value="manual">Digitar Número Manualmente</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Filtro Dinâmico: Célula -->
                        <div x-show="targetType === 'cell_members'" x-cloak class="space-y-1.5 p-3 rounded-lg bg-orange-50/50 dark:bg-orange-950/20 border border-orange-100 dark:border-orange-900/30">
                            <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300">Escolha a Célula:</label>
                            <select name="cell_id" id="cell_id_select" @change="updateRecipientsCount()"
                                class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white px-3 py-1.5 text-xs">
                                <option value="">Selecione uma Célula...</option>
                                @foreach($cells as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} (Líder: {{ $c->leader->name ?? 'Sem líder' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro Dinâmico: Supervisão -->
                        <div x-show="targetType === 'supervision_members'" x-cloak class="space-y-1.5 p-3 rounded-lg bg-orange-50/50 dark:bg-orange-950/20 border border-orange-100 dark:border-orange-900/30">
                            <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300">Escolha a Supervisão:</label>
                            <select name="supervision_id" id="sup_id_select" @change="updateRecipientsCount()"
                                class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white px-3 py-1.5 text-xs">
                                <option value="">Selecione uma Supervisão...</option>
                                @foreach($supervisions as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} (Zona: {{ $s->zone->name ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro Dinâmico: Zona -->
                        <div x-show="targetType === 'zone_members'" x-cloak class="space-y-1.5 p-3 rounded-lg bg-orange-50/50 dark:bg-orange-950/20 border border-orange-100 dark:border-orange-900/30">
                            <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300">Escolha a Zona Pastoral:</label>
                            <select name="zone_id" id="zone_id_select" @change="updateRecipientsCount()"
                                class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white px-3 py-1.5 text-xs">
                                <option value="">Selecione uma Zona...</option>
                                @foreach($zones as $z)
                                    <option value="{{ $z->id }}">{{ $z->name }} (Pastor: {{ $z->pastor->name ?? 'Pendente' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campo Manual de Telefones -->
                        <div x-show="targetType === 'manual'" x-cloak class="space-y-1.5 p-3 rounded-lg bg-gray-50 dark:bg-zinc-800/50 border border-gray-200 dark:border-zinc-700">
                            <label class="block text-xs font-bold text-gray-700 dark:text-zinc-300">Números de Telefone (separe por vírgula ou espaço):</label>
                            <input type="text" name="manual_phone" id="manual_phone_input" @input.debounce.500ms="updateRecipientsCount()"
                                placeholder="Ex: 841234567, 861234567, +258849876543"
                                class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white px-3 py-2 text-xs">
                        </div>

                        <!-- Indicador de Destinatários Encontrados -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-orange-50/60 dark:bg-orange-950/30 border border-orange-200/70 dark:border-orange-800/40">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-people-fill text-orange-700 dark:text-orange-400"></i>
                                <span class="text-xs font-bold text-gray-800 dark:text-zinc-200">Destinatários estimados com número válido:</span>
                            </div>
                            <span class="inline-flex items-center gap-1 font-black text-sm text-orange-700 dark:text-orange-400">
                                <span x-show="!calculatingCount" x-text="recipientCount"></span>
                                <span x-show="calculatingCount" class="animate-pulse">Calculando...</span>
                                <span class="text-xs font-normal">pessoa(s)</span>
                            </span>
                        </div>

                        <!-- Mensagem -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-black uppercase tracking-wider text-gray-700 dark:text-zinc-300">
                                    2. Conteúdo da Mensagem <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[11px] font-bold"
                                        :class="charCount > 160 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-zinc-400'">
                                        <span x-text="charCount"></span>/160 caracteres
                                    </span>
                                    <span class="rounded bg-gray-100 dark:bg-zinc-800 px-1.5 py-0.5 text-[10px] font-black text-gray-600 dark:text-zinc-400">
                                        <span x-text="partsCount"></span> SMS
                                    </span>
                                </div>
                            </div>

                            <textarea name="message" x-model="messageText" rows="4" required maxlength="1000"
                                placeholder="Digite aqui o comunicado oficial ou mensagem que os destinatários receberão no celular..."
                                class="w-full rounded-lg border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white p-3 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>

                            <!-- Tags Dinâmicas -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                <span class="text-[10px] font-bold text-gray-400">Inserir tag:</span>
                                <button type="button" @click="insertTag('{nome}')"
                                    class="inline-flex items-center gap-1 rounded bg-gray-100 dark:bg-zinc-800 hover:bg-orange-50 dark:hover:bg-zinc-700 px-2 py-0.5 text-[10px] font-bold text-gray-700 dark:text-zinc-300 hover:text-orange-700 dark:hover:text-orange-400 border border-gray-200 dark:border-zinc-700 transition">
                                    <code>{nome}</code> (Primeiro Nome)
                                </button>
                            </div>
                        </div>

                        <!-- Botão de Disparo -->
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-orange-700 hover:bg-orange-800 px-5 py-3 text-sm font-black text-white shadow-md hover:shadow-lg transition">
                                <i class="bi bi-send-fill"></i>
                                <span>Disparar Mensagens SMS</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Coluna de Preview & Informações -->
            <div class="space-y-4">
                <!-- Preview no Celular -->
                <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-3 flex items-center gap-1.5">
                        <i class="bi bi-phone text-orange-700 dark:text-orange-400"></i>
                        <span>Pré-visualização no Smartphone</span>
                    </h3>

                    <div class="rounded-2xl border-4 border-gray-800 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-950 p-3 max-w-xs mx-auto shadow-inner">
                        <!-- Top Notch -->
                        <div class="w-20 h-3 bg-gray-800 dark:bg-zinc-700 rounded-full mx-auto mb-3"></div>

                        <!-- Sender Header -->
                        <div class="text-center pb-2 border-b border-gray-200 dark:border-zinc-800 mb-3">
                            <div class="text-[10px] font-black uppercase text-gray-400">Mensagem SMS</div>
                            <div class="text-xs font-bold text-gray-800 dark:text-white">{{ $gatewayInfo['sender'] }}</div>
                        </div>

                        <!-- SMS Bubble -->
                        <div class="rounded-xl bg-white dark:bg-zinc-800 p-3 shadow-sm border border-gray-200 dark:border-zinc-700 text-xs text-gray-800 dark:text-zinc-100 space-y-1">
                            <p class="whitespace-pre-wrap font-sans leading-relaxed"
                                x-text="messageText ? messageText.replace('{nome}', 'Manuel') : 'A sua mensagem aparecerá aqui em tempo real conforme você digita...'"></p>
                            <div class="text-[9px] text-gray-400 text-right">Agora mesmo</div>
                        </div>
                    </div>
                </div>

                <!-- Dica de Boas Práticas -->
                <div class="rounded-xl border border-blue-200 dark:border-blue-900/40 bg-blue-50/50 dark:bg-blue-950/20 p-4 text-xs text-blue-900 dark:text-blue-300 space-y-2">
                    <div class="flex items-center gap-1.5 font-bold">
                        <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400"></i>
                        <span>Recomendações de Envio:</span>
                    </div>
                    <ul class="list-disc pl-4 space-y-1 text-[11px] text-blue-800 dark:text-blue-400">
                        <li>Mantenha a mensagem direta e concisa até 160 caracteres para economizar custos de créditos SMS.</li>
                        <li>A tag <code>{nome}</code> será substituída automaticamente pelo nome do destinatário.</li>
                        <li>Números moçambicanos são normalizados automaticamente para o formato internacional (+258).</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 3: REDE DE VISITANTES ==================== -->
    <div x-show="activeTab === 'network'" class="space-y-4">
        <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-6 shadow-sm space-y-5">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-orange-700 dark:text-orange-400">Fluxo Inteligente</span>
                <h2 class="text-lg font-black text-gray-900 dark:text-white mt-0.5">Como Funciona a Rede de Acolhimento Pastoral?</h2>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                    Quando um visitante é cadastrado ou alocado a uma célula/zona, o sistema aciona automaticamente uma cadeia de mensageria SMS e notificações em 4 níveis:
                </p>
            </div>

            <!-- Diagrama / Cards das 4 etapas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- 1. Pastor -->
                <div class="rounded-xl border border-purple-200 dark:border-purple-900/40 bg-purple-50/40 dark:bg-purple-950/20 p-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 font-black text-xs">1</span>
                        <h4 class="font-bold text-xs text-purple-900 dark:text-purple-300">Pastor de Zona</h4>
                    </div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-purple-700 dark:text-purple-400">Informe & Cobertura</div>
                    <p class="text-xs text-gray-600 dark:text-zinc-400">
                        Recebe SMS com o informe do novo visitante acolhido na sua zona, qual célula foi atribuída e confirmação de que os líderes foram acionados.
                    </p>
                </div>

                <!-- 2. Supervisor -->
                <div class="rounded-xl border border-blue-200 dark:border-blue-900/40 bg-blue-50/40 dark:bg-blue-950/20 p-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 font-black text-xs">2</span>
                        <h4 class="font-bold text-xs text-blue-900 dark:text-blue-300">Supervisor</h4>
                    </div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-blue-700 dark:text-blue-400">Ação de Acompanhamento</div>
                    <p class="text-xs text-gray-600 dark:text-zinc-400">
                        Recebe SMS com a incumbência pastoral de orientar e acompanhar o líder de célula no acolhimento do visitante até a sua integração.
                    </p>
                </div>

                <!-- 3. Líder da Célula -->
                <div class="rounded-xl border border-orange-200 dark:border-orange-900/40 bg-orange-50/40 dark:bg-orange-950/20 p-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 font-black text-xs">3</span>
                        <h4 class="font-bold text-xs text-orange-900 dark:text-orange-300">Líder da Célula</h4>
                    </div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-orange-700 dark:text-orange-400">Ação Imediata</div>
                    <p class="text-xs text-gray-600 dark:text-zinc-400">
                        Recebe SMS com os dados completos do visitante (nome, telefone e bairro) com chamado imediato para contato e convite para a reunião da célula.
                    </p>
                </div>

                <!-- 4. Visitante -->
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/40 dark:bg-emerald-950/20 p-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-black text-xs">4</span>
                        <h4 class="font-bold text-xs text-emerald-900 dark:text-emerald-300">Visitante</h4>
                    </div>
                    <div class="text-[10px] uppercase font-black tracking-wider text-emerald-700 dark:text-emerald-400">Boas-vindas & Comunhão</div>
                    <p class="text-xs text-gray-600 dark:text-zinc-400">
                        Recebe SMS caloroso da igreja dando boas-vindas e informando o nome e contato do Líder da Célula designada para acolhê-lo.
                    </p>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-between">
                <p class="text-xs text-gray-500 dark:text-zinc-400">
                    Você pode acionar manualmente a rede a qualquer momento na ficha do visitante clicando em <strong>"Notificar Rede Pastoral"</strong>.
                </p>
                <a href="{{ route('visitors.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                    <i class="bi bi-people"></i>
                    <span>Ver Visitantes</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal: Detalhes do SMS -->
    <div x-show="showModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-gray-200 dark:border-zinc-800 space-y-4"
            @click.away="showModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 flex items-center justify-center font-bold">
                        <i class="bi bi-chat-text"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white">Detalhes do Registro SMS</h3>
                        <p class="text-[10px] text-gray-400" x-text="'ID: #' + (selectedMessage?.id ?? '')"></p>
                    </div>
                </div>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <template x-if="selectedMessage">
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 dark:bg-zinc-800/60 rounded-xl">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Destinatário:</span>
                            <div class="font-bold text-gray-900 dark:text-white" x-text="selectedMessage.recipient_name || 'Não especificado'"></div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Telefone:</span>
                            <div class="font-mono font-bold text-orange-700 dark:text-orange-400" x-text="selectedMessage.phone"></div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Status:</span>
                            <div>
                                <span class="uppercase font-black text-[10px]"
                                    :class="selectedMessage.status === 'sent' ? 'text-emerald-600' : (selectedMessage.status === 'failed' ? 'text-red-600' : 'text-amber-600')"
                                    x-text="selectedMessage.status"></span>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Provedor:</span>
                            <div class="font-semibold text-gray-700 dark:text-zinc-300" x-text="selectedMessage.driver"></div>
                        </div>
                    </div>

                    <!-- Mensagem Completa -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-black uppercase text-gray-400">Texto Completo:</span>
                        <div class="p-3 rounded-xl border border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-800 text-gray-800 dark:text-zinc-200 whitespace-pre-wrap leading-relaxed font-sans"
                            x-text="selectedMessage.message"></div>
                    </div>

                    <!-- Erro (se houver) -->
                    <template x-if="selectedMessage.error_message">
                        <div class="p-3 rounded-xl border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 text-xs">
                            <span class="font-bold block">Mensagem de Erro:</span>
                            <span x-text="selectedMessage.error_message"></span>
                        </div>
                    </template>
                </div>
            </template>

            <div class="pt-3 border-t border-gray-100 dark:border-zinc-800 flex justify-end">
                <button @click="showModal = false"
                    class="rounded-lg border border-gray-300 dark:border-zinc-700 px-4 py-2 text-xs font-bold text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800">
                    Fechar
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
