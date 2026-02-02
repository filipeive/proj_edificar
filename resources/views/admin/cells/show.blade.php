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
            transfer(member) {
                this.selectedMember = member;
                this.showTransferModal = true;
            },
            openObs(member) {
                this.selectedMember = member;
                this.showObsModal = true;
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

            <!-- Líder -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                    <i class="bi bi-person-badge"></i>
                    <span>Liderança</span>
                </div>
                @if ($cell->leader)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 font-black text-2xl">
                            {{ substr($cell->leader->name, 0, 1) }}
                        </div>
                        <a href="{{ route('users.show', $cell->leader) }}" class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors leading-tight">
                            {{ $cell->leader->name }}
                        </a>
                    </div>
                @else
                    <p class="text-lg font-bold text-gray-300 italic">Sem líder designado</p>
                @endif
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
                    <div class="overflow-x-auto">
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
                                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
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
                    <div class="overflow-x-auto">
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
                    @if($meetings->hasPages())
                        <div class="mt-6">
                            {{ $meetings->links() }}
                        </div>
                    @endif
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
            style="display: none;">
            <div @click.away="showTransferModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-In">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">Transferir Membro</h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="'Mover ' + selectedMember.name + ' para outra célula'"></p>
                </div>
                <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/reassign-cell'" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Selecione a Célula de Destino</label>
                        <select name="cell_id" required class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all appearance-none custom-select">
                            <option value="">Escolha uma célula...</option>
                            @foreach($availableCells as $availCell)
                                <option value="{{ $availCell->id }}">{{ $availCell->name }} ({{ $availCell->supervision->name }})</option>
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
    </div>
@endsection
