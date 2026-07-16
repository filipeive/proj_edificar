@extends('layouts.app')

@section('title', "Célula $cell->name - Portal Life Church")
@section('page-title', $cell->name)
@section('page-subtitle', 'Gestão da célula e membros')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('cells.pdf', $cell) }}"
            class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50"
            title="Exportar ficha">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        <a href="{{ route('cells.attendance', $cell) }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Ficha de presença">
            <i class="bi bi-calendar-check"></i>
        </a>
        <a href="{{ route('cells.edit', $cell) }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Editar célula">
            <i class="bi bi-pencil-square"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8" x-data="{ 
            activeTab: localStorage.getItem('cell_active_tab') || 'members',
            showTransferModal: false,
            showObsModal: false,
            selectedMember: {},
            showFeedbackModal: false,
            feedbackVisitorId: null,
            feedbackStatus: '',
            feedbackNotes: '',
            transfer(member) {
                this.selectedMember = member;
                this.showTransferModal = true;
            },
            openObs(member) {
                this.selectedMember = member;
                this.showObsModal = true;
            },
            openFeedback(id, status, notes) {
                this.feedbackVisitorId = id;
                this.feedbackStatus = status;
                this.feedbackNotes = notes || '';
                this.showFeedbackModal = true;
            }
        }" x-init="$watch('activeTab', value => localStorage.setItem('cell_active_tab', value))">
        <!-- Header & Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Info Célula -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-layers-fill"></i>
                    <span>Célula</span>
                </div>
                <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ $cell->name }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $cell->supervision->name }}</span>
                </div>
            </div>

            <!-- Líder e Timóteos -->
            <div class="bg-gray-900 rounded-[2rem] shadow-xl border border-gray-800 p-8 flex flex-col justify-center text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">
                            <i class="bi bi-person-badge-fill text-orange-500"></i>
                            <span>Equipe de Liderança</span>
                        </div>
                        <a href="{{ route('cells.edit', $cell) }}" class="text-[9px] font-black text-blue-400 hover:text-blue-300 uppercase tracking-widest transition-colors">
                            <i class="bi bi-pencil-square mr-1"></i> Gerir
                        </a>
                    </div>

                    @if ($cell->leader)
                        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-white/5">
                            <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 font-black text-2xl shadow-inner border border-white/5">
                                {{ substr($cell->leader->name, 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('users.show', $cell->leader) }}" class="text-xl font-black text-white hover:text-blue-400 transition-colors leading-tight line-clamp-1 tracking-tight">
                                    {{ $cell->leader->name }}
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest bg-blue-500/10 px-2 py-0.5 rounded">Líder</span>
                                    <div class="flex gap-2">
                                        @if($cell->leader->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cell->leader->phone) }}" target="_blank" class="text-gray-500 hover:text-green-500 transition-colors">
                                                <i class="bi bi-whatsapp text-xs"></i>
                                            </a>
                                        @endif
                                        <a href="mailto:{{ $cell->leader->email }}" class="text-gray-500 hover:text-blue-400 transition-colors">
                                            <i class="bi bi-envelope text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        @forelse($cell->timoteos as $timoteo)
                            <div class="flex items-center gap-3 bg-white/5 p-3 rounded-2xl border border-white/5 hover:bg-white/10 transition-all group/item">
                                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400 font-black text-sm">
                                    {{ substr($timoteo->name, 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('users.show', $timoteo) }}" class="text-sm font-bold text-gray-200 hover:text-orange-400 transition-colors line-clamp-1">
                                        {{ $timoteo->name }}
                                    </a>
                                    <p class="text-[8px] font-black text-orange-500/50 uppercase tracking-widest mt-0.5">Auxiliar / Timóteo</p>
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover/item:opacity-100 transition-opacity">
                                    @if($timoteo->phone)
                                        <a href="tel:{{ $timoteo->phone }}" class="w-7 h-7 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                                            <i class="bi bi-telephone text-[10px]"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            @if(!$cell->leader)
                                <div class="py-4 text-center">
                                    <p class="text-sm font-bold text-gray-600 italic">Sem liderança designada</p>
                                    <a href="{{ route('cells.edit', $cell) }}" class="text-[10px] font-black text-blue-500 uppercase tracking-widest mt-2 block">Atribuir Agora</a>
                                </div>
                            @endif
                        @endforelse
                    </div>
                </div>
                <!-- Background decoration -->
                <div class="absolute -right-8 -bottom-8 text-9xl text-white/5 rotate-12 group-hover:scale-110 transition-transform duration-500">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>

            <!-- Total Membros -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                <p class="text-5xl font-black text-blue-600 tracking-tighter">{{ $cell->members()->where('is_active', true)->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Membros Ativos</p>
            </div>

            <!-- Total Arrecadado -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-4xl font-black text-green-600 tracking-tighter">
                        {{ number_format($cell->getTotalContributedThisMonth(), 0, ',', '.') }}<span class="text-sm ml-1 uppercase">MT</span>
                    </p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Arrecadado este mês</p>
                </div>
                <div class="absolute -right-4 -bottom-4 text-8xl text-green-50 opacity-50"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>

        <!-- Main Content Area with Tabs -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 space-y-6">
                <!-- Tab Navigation -->
                <div class="flex items-center gap-2 md:gap-4 bg-white p-1.5 md:p-2 rounded-2xl md:rounded-[2rem] shadow-sm border border-gray-100 w-full md:w-fit overflow-x-auto no-scrollbar">
                    <button @click="activeTab = 'members'"
                        :class="activeTab === 'members' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-[1.5rem] text-[10px] md:text-sm font-black uppercase tracking-widest transition-all whitespace-nowrap">
                        Membros
                    </button>
                    <button @click="activeTab = 'meetings'"
                        :class="activeTab === 'meetings' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-[1.5rem] text-[10px] md:text-sm font-black uppercase tracking-widest transition-all whitespace-nowrap">
                        Encontros
                    </button>
                    <button @click="activeTab = 'visitors'"
                        :class="activeTab === 'visitors' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-[1.5rem] text-[10px] md:text-sm font-black uppercase tracking-widest transition-all whitespace-nowrap">
                        Visitas
                    </button>
                    <button @click="activeTab = 'stats'"
                        :class="activeTab === 'stats' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-[1.5rem] text-[10px] md:text-sm font-black uppercase tracking-widest transition-all whitespace-nowrap">
                        Desempenho
                    </button>
                </div>

                <!-- Tab: Members -->
                <div x-show="activeTab === 'members'" x-transition.fade class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 md:p-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-50">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tighter">Corpo de Membros</h3>
                            <p class="text-sm font-medium text-gray-400">Pessoas vinculadas diretamente a esta célula</p>
                        </div>
                        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                            <form action="{{ route('cells.show', $cell) }}" method="GET" class="w-full md:w-80">
                                <div class="relative">
                                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Pesquisar membro por nome ou email..."
                                        class="w-full pl-11 pr-4 py-3 bg-gray-50/60 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all">
                                </div>
                            </form>
                            <a href="{{ route('members.create') }}?cell_id={{ $cell->id }}"
                                class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-5 py-3 rounded-2xl flex items-center transition-all font-bold text-sm">
                                <i class="bi bi-person-plus mr-2"></i> Adicionar
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto hidden md:block">
                        <table class="w-full table-compact">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Membro</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Compromisso</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Contribuição</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($members as $member)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 font-bold group-hover:bg-blue-50 group-hover:text-blue-600 transition-all">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition-colors line-clamp-1">{{ $member->name }}</p>
                                                    <p class="text-[10px] font-medium text-gray-400 line-clamp-1">{{ $member->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6">
                                            @if ($member->getActiveCommitment())
                                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                                    {{ $member->getActiveCommitment()->package->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-300 text-[10px] font-black uppercase tracking-widest">Sem pacto</span>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6 text-right font-black text-gray-900">
                                            <span class="{{ $member->getTotalContributedThisMonth() > 0 ? 'text-green-600' : 'text-gray-300' }}">
                                                {{ number_format($member->getTotalContributedThisMonth(), 0, ',', '.') }} MT
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-right">
                                            <div class="flex justify-end gap-2 opacity-70 hover:opacity-100 transition-all">
                                                <button @click="openObs({ id: {{ $member->id }}, name: '{{ $member->name }}', obs: '{{ addslashes($member->observations) }}' })"
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600"
                                                    title="Observações">
                                                    <i class="bi bi-chat-dots{{ $member->observations ? '-fill' : '' }}"></i>
                                                </button>
                                                <button @click="transfer({ id: {{ $member->id }}, name: '{{ $member->name }}' })"
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600"
                                                    title="Transferir Célula">
                                                    <i class="bi bi-arrow-left-right"></i>
                                                </button>
                                                <form action="{{ route('users.remove-from-cell', $member) }}" method="POST" class="inline" onsubmit="return confirm('Deseja remover este membro desta célula? O membro continuará no sistema, mas sem vínculo a esta célula.')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition-all"
                                                        title="Remover da Célula">
                                                        <i class="bi bi-person-x"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('users.show', $member) }}"
                                                    class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-10 py-16 text-center text-gray-400 font-medium italic">
                                            Nenhum membro ativo nesta célula.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Grid View for Members -->
                    <div class="grid grid-cols-1 gap-4 md:hidden">
                        @forelse($members as $member)
                            <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4 hover:shadow-lg transition-shadow">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-500 font-black text-lg">
                                        {{ substr($member->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-black text-gray-900 leading-tight">{{ $member->name }}</h4>
                                        <p class="text-[10px] font-bold text-gray-400 mt-0.5">{{ $member->email }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between border-t border-b border-gray-50/80 py-3.5">
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Compromisso</p>
                                        <div>
                                            @if ($member->getActiveCommitment())
                                                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full text-[9px] font-black uppercase tracking-wider">
                                                    {{ $member->getActiveCommitment()->package->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-300 text-[9px] font-black uppercase tracking-wider">Sem pacto</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right space-y-1">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Contribuição</p>
                                        <p class="text-xs font-black {{ $member->getTotalContributedThisMonth() > 0 ? 'text-green-600' : 'text-gray-300' }}">
                                            {{ number_format($member->getTotalContributedThisMonth(), 0, ',', '.') }} MT
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-1">
                                    <button @click="openObs({ id: {{ $member->id }}, name: '{{ $member->name }}', obs: '{{ addslashes($member->observations) }}' })"
                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600 flex items-center justify-center transition-all"
                                        title="Observações">
                                        <i class="bi bi-chat-dots{{ $member->observations ? '-fill' : '' }} text-lg"></i>
                                    </button>
                                    <button @click="transfer({ id: {{ $member->id }}, name: '{{ $member->name }}' })"
                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all"
                                        title="Transferir Célula">
                                        <i class="bi bi-arrow-left-right text-lg"></i>
                                    </button>
                                    <form action="{{ route('users.remove-from-cell', $member) }}" method="POST" class="inline" onsubmit="return confirm('Deseja remover este membro desta célula? O membro continuará no sistema, mas sem vínculo a esta célula.')">
                                        @csrf
                                        <button type="submit"
                                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition-all"
                                            title="Remover da Célula">
                                            <i class="bi bi-person-x text-lg"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('users.show', $member) }}"
                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all">
                                        <i class="bi bi-chevron-right text-lg"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white border border-gray-100 rounded-3xl p-12 text-center text-gray-400 font-medium italic">
                                Nenhum membro ativo nesta célula.
                            </div>
                        @endforelse
                    </div>
                    @if($members->hasPages())
                        <div class="mt-6">
                            {{ $members->links() }}
                        </div>
                    @endif
                </div>

                <!-- Tab: Meetings -->
                <div x-show="activeTab === 'meetings'" x-transition.fade class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 md:p-10 flex justify-between items-center border-b border-gray-50">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tighter">Histórico de Encontros</h3>
                            <p class="text-sm font-medium text-gray-400">Registros de reuniões, liderança e atos</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('cell-meetings.create') }}?cell_id={{ $cell->id }}"
                                class="bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white px-5 py-3 rounded-2xl flex items-center transition-all font-bold text-sm">
                                <i class="bi bi-calendar-event mr-2"></i> Novo Encontro
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto hidden md:block">
                        <table class="w-full text-sm table-compact">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tema</th>
                                    <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Presença</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($meetings as $meeting)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6 font-bold text-gray-900">
                                            {{ $meeting->meeting_date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-10 py-6 uppercase font-black text-[10px] tracking-widest">
                                            @if($meeting->meeting_type === 'normal')
                                                <span class="text-blue-600">Reunião de Célula</span>
                                            @else
                                                <span class="text-orange-600">
                                                    <i class="bi bi-award mr-1"></i>
                                                    @switch($meeting->meeting_type)
                                                        @case('leadership') Liderança @break
                                                        @case('supervision') Supervisão @break
                                                        @case('zone') Zona @break
                                                        @default Especial
                                                    @endswitch
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6 text-gray-600 italic font-medium">
                                            {{ $meeting->theme ?? 'Sem tema registrado' }}
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[10px] font-black tracking-widest">
                                                {{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('cell-meetings.pdf', $meeting) }}" title="PDF"
                                                    class="action-icon text-gray-300 hover:text-orange-600 hover:bg-orange-50">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                                <a href="{{ route('cell-meetings.show', $meeting) }}" title="Detalhes"
                                                    class="action-icon text-gray-300 hover:text-blue-600 hover:bg-blue-50">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-16 text-center text-gray-400 font-medium italic">
                                            Nenhum encontro registrado ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Grid View for Meetings -->
                    <div class="grid grid-cols-1 gap-4 md:hidden">
                        @forelse($meetings as $meeting)
                            <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4 hover:shadow-lg transition-shadow">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-black text-gray-900">{{ $meeting->meeting_date->format('d/m/Y') }}</span>
                                    <span class="uppercase font-black text-[9px] tracking-widest">
                                        @if($meeting->meeting_type === 'normal')
                                            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full">Reunião de Célula</span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-orange-50 text-orange-600 rounded-full">
                                                <i class="bi bi-award mr-0.5"></i>
                                                @switch($meeting->meeting_type)
                                                    @case('leadership') Liderança @break
                                                    @case('supervision') Supervisão @break
                                                    @case('zone') Zona @break
                                                    @default Especial
                                                @endswitch
                                            </span>
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Tema</p>
                                    <p class="text-xs text-gray-600 italic font-medium">
                                        {{ $meeting->theme ?? 'Sem tema registrado' }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between border-t border-gray-50/80 pt-4">
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Presença Total</p>
                                        <span class="inline-block bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-widest">
                                            {{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }}
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('cell-meetings.pdf', $meeting) }}" title="PDF"
                                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600 flex items-center justify-center transition-all">
                                            <i class="bi bi-file-earmark-pdf text-lg"></i>
                                        </a>
                                        <a href="{{ route('cell-meetings.show', $meeting) }}" title="Detalhes"
                                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all">
                                            <i class="bi bi-chevron-right text-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white border border-gray-100 rounded-3xl p-12 text-center text-gray-400 font-medium italic">
                                Nenhum encontro registrado ainda.
                            </div>
                        @endforelse
                    </div>
                    @if($meetings->hasPages())
                        <div class="mt-6">
                            {{ $meetings->links() }}
                        </div>
                    @endif
                </div>

                <!-- Tab: Visitors -->
                <div x-show="activeTab === 'visitors'" x-transition.fade class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 md:p-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-50">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tighter">Visitas do Culto</h3>
                            <p class="text-sm font-medium text-gray-400">Visitantes integrados e em acompanhamento nesta célula</p>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="overflow-x-auto hidden md:block">
                        <table class="w-full table-compact">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Visitante</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Contacto</th>
                                    <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Data da Visita</th>
                                    <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($visitors as $visitor)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-650 flex items-center justify-center font-bold">
                                                    {{ substr($visitor->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900 leading-tight group-hover:text-orange-650 transition-colors">{{ $visitor->name }}</p>
                                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">
                                                        @if($visitor->age) {{ $visitor->age }} anos @endif
                                                        @if($visitor->gender) • {{ ucfirst($visitor->gender) }} @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6">
                                            <div class="text-sm font-bold text-gray-800">{{ $visitor->phone ?: 'Sem telefone' }}</div>
                                            @if($visitor->neighborhood)
                                                <div class="text-[10px] text-gray-400 font-medium">{{ $visitor->neighborhood }}</div>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <span class="text-sm font-bold text-gray-700">
                                                {{ $visitor->visit_date->format('d/m/Y') }}
                                            </span>
                                            <span class="block text-[10px] text-gray-400 font-medium">
                                                {{ $visitor->visit_date->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            {!! $visitor->status_badge !!}
                                        </td>
                                        <td class="px-10 py-6 text-right">
                                            <div class="flex justify-end items-center gap-2 opacity-70 hover:opacity-100 transition-all">
                                                @if($visitor->contact_status !== 'integrado' && auth()->user()->role !== 'secretaria')
                                                    <a href="{{ route('members.create') }}?visitor_id={{ $visitor->id }}"
                                                        class="px-4 py-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm flex items-center gap-1.5"
                                                        title="Tornar Membro">
                                                        <i class="bi bi-person-check-fill"></i> Tornar Membro
                                                    </a>
                                                @endif
                                                @if(auth()->user()->role !== 'secretaria')
                                                    <button @click="openFeedback($el.dataset.id, $el.dataset.status, $el.dataset.notes)"
                                                        data-id="{{ $visitor->id }}"
                                                        data-status="{{ $visitor->contact_status }}"
                                                        data-notes="{{ $visitor->notes }}"
                                                        class="action-icon bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600" title="Registar Feedback">
                                                        <i class="bi bi-chat-text"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('visitors.show', $visitor) }}"
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="Ver Acompanhamento">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-16 text-center text-gray-400 font-medium italic">
                                            Nenhum visitante registrado para esta célula.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card Grid -->
                    <div class="grid grid-cols-1 gap-4 md:hidden p-4">
                        @forelse($visitors as $visitor)
                            <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4 hover:shadow-lg transition-shadow">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-650 flex items-center justify-center font-black text-sm uppercase flex-shrink-0">
                                            {{ substr($visitor->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-black text-gray-900 leading-tight truncate">{{ $visitor->name }}</h4>
                                            <p class="text-[10px] font-bold text-gray-400 mt-0.5 truncate">
                                                @if($visitor->age) {{ $visitor->age }} anos @endif
                                                @if($visitor->gender) • {{ ucfirst($visitor->gender) }} @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        {!! $visitor->status_badge !!}
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 border-t border-gray-50/80 pt-4">
                                    <div class="flex-1 space-y-0.5">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Contacto</p>
                                        <p class="text-xs font-bold text-gray-700">{{ $visitor->phone ?: 'Sem telefone' }}</p>
                                    </div>
                                    <div class="flex-1 space-y-0.5 text-right">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Visita</p>
                                        <p class="text-xs font-bold text-gray-700">{{ $visitor->visit_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>

                                <div class="flex gap-2 border-t border-gray-50/80 pt-4">
                                    @if($visitor->contact_status !== 'integrado' && auth()->user()->role !== 'secretaria')
                                        <a href="{{ route('members.create') }}?visitor_id={{ $visitor->id }}"
                                            class="flex-1 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white h-10 rounded-xl flex items-center justify-center text-[10px] font-black uppercase tracking-widest transition-all gap-1.5 shadow-sm">
                                            <i class="bi bi-person-check-fill text-sm"></i> Tornar Membro
                                        </a>
                                    @endif
                                    @if(auth()->user()->role !== 'secretaria')
                                        <button @click="openFeedback($el.dataset.id, $el.dataset.status, $el.dataset.notes)"
                                            data-id="{{ $visitor->id }}"
                                            data-status="{{ $visitor->contact_status }}"
                                            data-notes="{{ $visitor->notes }}"
                                            class="w-12 h-10 bg-gray-50 text-gray-500 hover:bg-orange-50 hover:text-orange-650 rounded-xl flex items-center justify-center transition-all"
                                            title="Registar Feedback">
                                            <i class="bi bi-chat-text text-lg"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('visitors.show', $visitor) }}"
                                        class="w-12 h-10 bg-gray-50 text-gray-500 hover:bg-blue-50 hover:text-blue-600 rounded-xl flex items-center justify-center transition-all">
                                        <i class="bi bi-eye text-lg"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white border border-gray-100 rounded-3xl p-12 text-center text-gray-400 font-medium italic">
                                Nenhum visitante registrado para esta célula.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab: Stats -->
                <div x-show="activeTab === 'stats'" x-transition.fade class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 text-center space-y-2">
                            <p class="text-5xl font-black text-blue-600 tracking-tighter">{{ $cell->members()->where('is_active', true)->count() }}</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Fidelidade Mensal</p>
                        </div>
                        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 text-center space-y-2">
                            <p class="text-5xl font-black text-green-600 tracking-tighter">{{ $cell->getMembersContributedThisMonth() }}</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pactos Cumpridos</p>
                        </div>
                        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-gray-100 text-center space-y-2">
                            @php
                                $total = $cell->members()->where('is_active', true)->count();
                                $contrib = $cell->getMembersContributedThisMonth();
                                $perc = $total > 0 ? round(($contrib / $total) * 100) : 0;
                            @endphp
                            <p class="text-5xl font-black text-purple-600 tracking-tighter">{{ $perc }}%</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Efetividade</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna de Ações Rápidas (Hidden on Mobile) -->
            <div class="space-y-6 hidden md:block">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Gestão da Unidade</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('cells.pdf', $cell) }}"
                            class="w-full bg-orange-600 text-white px-6 py-4 rounded-2xl hover:bg-orange-700 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-file-earmark-pdf"></i> Exportar Ficha
                        </a>
                        <a href="{{ route('cells.attendance', $cell) }}"
                            class="w-full bg-gray-900 text-white px-6 py-4 rounded-2xl hover:bg-black transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-calendar-check"></i> Ficha de Presença
                        </a>
                        <a href="{{ route('contributions.index') }}?cell_id={{ $cell->id }}"
                            class="w-full bg-white text-gray-800 border-2 border-gray-100 px-6 py-4 rounded-2xl hover:border-blue-600 hover:text-blue-600 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-cash-coin"></i> Contribuições
                        </a>
                        <div class="pt-4 border-t border-gray-50 flex gap-2">
                            <a href="{{ route('cells.edit', $cell) }}"
                                class="flex-1 bg-gray-50 text-gray-500 px-4 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-[10px] uppercase tracking-widest text-center">
                                Editar
                            </a>
                            <a href="{{ route('cells.index') }}"
                                class="flex-1 bg-gray-50 text-gray-500 px-4 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-[10px] uppercase tracking-widest text-center">
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-900 to-blue-900 rounded-[2.5rem] shadow-xl p-10 text-white relative overflow-hidden">
                    <div class="relative z-10 space-y-6">
                        <p class="text-[10px] font-black text-blue-300 uppercase tracking-[0.2em]">Visão Geral</p>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-blue-100">Crescimento Mensal</p>
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-black tracking-tighter text-white">+{{ $cell->members()->where('created_at', '>=', now()->startOfMonth())->count() }}</span>
                                <span class="text-xs font-bold text-blue-300 mb-1">Novos Membros</span>
                            </div>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 mt-4">
                            <div class="bg-blue-400 h-1.5 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    <i class="bi bi-lightning-charge-fill absolute -right-4 -bottom-4 text-9xl text-white opacity-5"></i>
                </div>
            </div>
        </div>

        <!-- Transfer Member Modal -->
        <div x-show="showTransferModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none; margin-top: -15px">
            <div @click.away="showTransferModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-In">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">Transferir Membro</h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="'Mover ' + selectedMember.name + ' para outra célula'"></p>
                </div>
                <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/reassign-cell'" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Selecione a Célula de Destino</label>
                        <select name="cell_id" required data-searchable="false" class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all appearance-none custom-select">
                            <option value="">Escolha uma célula...</option>
                            @foreach($availableCells as $availCell)
                                <option value="{{ $availCell->id }}">{{ $availCell->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showTransferModal = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 text-gray-500 font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">Cancelar</button>
                        <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Observations Modal -->
        <div x-show="showObsModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div @click.away="showObsModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-In">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">Observações do Membro</h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="selectedMember.name"></p>
                </div>
                <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/update-observations'" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Notas e Status (ex: Desistente, Afastado)</label>
                        <textarea name="observations" x-model="selectedMember.obs" rows="5" class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all placeholder:text-gray-300" placeholder="Digite as observações aqui..."></textarea>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showObsModal = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 text-gray-500 font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">Cancelar</button>
                        <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-green-600 text-white font-black text-xs uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-200">Salvar Notas</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Visitor Feedback Modal -->
        <div x-show="showFeedbackModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div @click.away="showFeedbackModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-In">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">Registrar Feedback da Visita</h3>
                    <p class="text-sm text-gray-500 mt-1">Atualize o estado do contacto e observações de acompanhamento</p>
                </div>
                <form :action="'{{ url('/visitors') }}/' + feedbackVisitorId + '/update-feedback'" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Estado do Contacto</label>
                        <select name="contact_status" x-model="feedbackStatus" required class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-orange-100 rounded-2xl text-sm font-bold transition-all appearance-none custom-select">
                            <option value="pendente">Pendente (Não contactado)</option>
                            <option value="contatado">Contatado (Em Acompanhamento)</option>
                            <option value="sem_interesse">Sem Interesse</option>
                            <option value="integrado">Integrado (Já é Membro)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Notas / Histórico de Conversa</label>
                        <textarea name="notes" x-model="feedbackNotes" rows="5" class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-orange-100 rounded-2xl text-sm font-medium transition-all placeholder:text-gray-300" placeholder="Escreva aqui detalhes sobre o contacto realizado..."></textarea>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showFeedbackModal = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 text-gray-500 font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">Cancelar</button>
                        <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-orange-600 text-white font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-200">Salvar Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
