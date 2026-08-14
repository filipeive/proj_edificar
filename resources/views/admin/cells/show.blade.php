@extends('layouts.app')

@section('title', "Célula $cell->name - Portal Life Church")
@section('page-title', $cell->name)
@section('page-subtitle', 'Gestão da célula e membros')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('cells.attendance', $cell) }}" class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Ficha de presença"><i class="bi bi-calendar-check"></i></a>
        <a href="{{ route('cells.pdf', $cell) }}" class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50" title="Exportar ficha"><i class="bi bi-file-earmark-pdf"></i></a>
        @if(!auth()->user()->isLider())
            <a href="{{ route('cells.edit', $cell) }}" class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Editar célula"><i class="bi bi-pencil-square"></i></a>
        @endif
    </div>
@endsection

@section('content')
    <div class="space-y-8" x-data="{
            activeTab: (function() {
                try {
                    var params = new URLSearchParams(window.location.search);
                    if (params.has('tab') && ['members','meetings','visitors','stats'].indexOf(params.get('tab')) !== -1) {
                        return params.get('tab');
                    }
                    var stored = localStorage.getItem('cell_active_tab');
                    if (stored && ['members','meetings','visitors','stats'].indexOf(stored) !== -1) {
                        return stored;
                    }
                } catch(e) {}
                return 'members';
            })(),
            showTransferModal: false,
            showObsModal: false,
            selectedMember: {},
            showFeedbackModal: false,
            feedbackVisitorId: null,
            feedbackStatus: '',
            feedbackNotes: '',
            showAddExistingModal: {{ $errors->has('member_id') ? 'true' : 'false' }},
            addExistingSearch: '',
            addExistingMemberId: '',
            addExistingRoleInCell: 'membro',
            addExistingResults: [],
            eligibleMembersUrl: '{{ route('cells.eligible-members', $cell) }}',
            memberSearch: '',
            openAddExistingModal() {
                this.showAddExistingModal = true;
                this.addExistingSearch = '';
                this.addExistingMemberId = '';
                this.addExistingRoleInCell = 'membro';
                this.addExistingResults = [];
                this.addExistingSearchMembers();
            },
            addExistingSearchMembers() {
                const q = (this.addExistingSearch || '').trim();
                fetch(this.eligibleMembersUrl + '?search=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => { this.addExistingResults = data; })
                    .catch(() => { this.addExistingResults = []; });
            },
            transfer(member) {
                this.selectedMember = member || {};
                this.showTransferModal = true;
            },
            openObs(member) {
                this.selectedMember = member || {};
                this.showObsModal = true;
            },
            openFeedback(id, status, notes) {
                this.feedbackVisitorId = id;
                this.feedbackStatus = status || 'pendente';
                this.feedbackNotes = notes || '';
                this.showFeedbackModal = true;
            },
            switchTab(tab) {
                this.activeTab = tab;
                try {
                    localStorage.setItem('cell_active_tab', tab);
                    var url = new URL(window.location.href);
                    url.searchParams.set('tab', tab);
                    window.history.replaceState({}, '', url);
                } catch(e) {}
            }
        }" x-init="$watch('activeTab', value => { try { localStorage.setItem('cell_active_tab', value); } catch(e) {} })">

        <!-- Header Card -->
        <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">
                        <i class="bi bi-layers-fill"></i>
                        <span>Célula</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ $cell->name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        <span class="inline-block px-2.5 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest {{ $cell->type_badge_classes }}">
                            {{ $cell->type_label }}
                        </span>
                        <span class="flex items-center gap-1.5"><i class="bi bi-geo-alt-fill text-blue-500"></i> {{ $cell->supervision->zone->name ?? 'Sem zona' }}</span>
                        <span class="flex items-center gap-1.5"><i class="bi bi-diagram-3-fill text-purple-500"></i> {{ $cell->supervision->name ?? 'Sem supervisão' }}</span>
                        <span class="flex items-center gap-1.5"><i class="bi bi-people-fill text-green-500"></i> {{ $cell->getMembersCount() }} membros</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('cells.attendance', $cell) }}" class="hidden md:inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 transition-colors">
                        <i class="bi bi-calendar-check"></i> Presenças
                    </a>
                    <a href="{{ route('cells.pdf', $cell) }}" class="hidden md:inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-orange-50 text-orange-700 font-bold hover:bg-orange-100 transition-colors">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                    @if(!auth()->user()->isLider())
                        <a href="{{ route('cells.edit', $cell) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                            <i class="bi bi-pencil-square"></i> Editar
                        </a>
                    @endif
                </div>
            </div>
            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-gradient-to-br from-blue-50 to-transparent rounded-full opacity-50"></div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-people-fill"></i><span>Membros Ativos</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $cell->getMembersCount() }}</p>
            </div>
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-green-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-calendar-event-fill"></i><span>Encontros</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $cell->meetings()->count() }}</p>
            </div>
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-purple-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-person-badge-fill"></i><span>Timóteos</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $cell->timoteos()->count() }}</p>
            </div>
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-globe2"></i><span>Visitas</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $visitors->count() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 space-y-6">

                <!-- Hidden radio inputs for CSS-only tabs (must be siblings of panels) -->
                <input type="radio" name="cell_tab" id="tab-members" value="members" class="hidden" checked>
                <input type="radio" name="cell_tab" id="tab-meetings" value="meetings" class="hidden">
                <input type="radio" name="cell_tab" id="tab-visitors" value="visitors" class="hidden">
                <input type="radio" name="cell_tab" id="tab-stats" value="stats" class="hidden">

                <!-- Tab Navigation -->
                <div class="tab-nav flex items-center gap-2 md:gap-4 bg-white p-1.5 md:p-2 rounded-2xl md:rounded-[2rem] shadow-sm border border-gray-100 w-full md:w-fit overflow-x-auto no-scrollbar">
                    <label for="tab-members" class="tab-nav-label px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-[1.5rem] text-[10px] md:text-sm font-black uppercase tracking-widest transition-all whitespace-nowrap cursor-pointer bg-blue-600 text-white shadow-lg shadow-blue-200">Membros</label>
                    <label for="tab-meetings" class="tab-nav-label px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-[1.5rem] text-[10px] md:text-sm font-black uppercase tracking-widest transition-all whitespace-nowrap cursor-pointer text-gray-500 hover:bg-gray-50">Encontros</label>
                    <label for="tab-visitors" class="tab-nav-label px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-[1.5rem] text-[10px] md:text-sm font-black uppercase tracking-widest transition-all whitespace-nowrap cursor-pointer text-gray-500 hover:bg-gray-50">Visitas</label>
                    <label for="tab-stats" class="tab-nav-label px-4 md:px-8 py-2 md:py-3 rounded-xl md:rounded-[1.5rem] text-[10px] md:text-sm font-black uppercase tracking-widest transition-all whitespace-nowrap cursor-pointer text-gray-500 hover:bg-gray-50">Desempenho</label>
                </div>

                <style>
                    #tab-members:checked ~ .tab-panel-members,
                    #tab-meetings:checked ~ .tab-panel-meetings,
                    #tab-visitors:checked ~ .tab-panel-visitors,
                    #tab-stats:checked ~ .tab-panel-stats {
                        display: block;
                    }
                    #tab-members:not(:checked) ~ .tab-panel-members,
                    #tab-meetings:not(:checked) ~ .tab-panel-meetings,
                    #tab-visitors:not(:checked) ~ .tab-panel-visitors,
                    #tab-stats:not(:checked) ~ .tab-panel-stats {
                        display: none;
                    }
                </style>

                <!-- Tab: Members -->
                <div class="tab-panel-members bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                    <!-- Actions Bar -->
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
                        <div class="relative w-full md:w-80">
                            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" x-model="memberSearch" @input="filterMembers()" placeholder="Pesquisar membros..."
                                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
                        </div>
                        <div class="flex gap-3 w-full md:w-auto">
                            @if($cell->type === \App\Models\Cell::TYPE_MEMBROS)
                                <a href="{{ route('members.create') }}?cell_id={{ $cell->id }}" class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-all shadow-sm hover:shadow-md">
                                    <i class="bi bi-person-plus-fill"></i> Adicionar
                                </a>
                            @endif
                            <button type="button" onclick="openAddExistingModal()" class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-green-600 text-white text-sm font-bold hover:bg-green-700 transition-all shadow-sm hover:shadow-md">
                                <i class="bi bi-people-fill"></i> Adicionar existente
                            </button>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                    <th class="text-left py-4 px-4">Nome</th>
                                    <th class="text-left py-4 px-4">Papel</th>
                                    <th class="text-left py-4 px-4">Contacto</th>
                                    <th class="text-left py-4 px-4">Status</th>
                                    <th class="text-right py-4 px-4">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm" x-ref="membersTableBody">
                                @forelse($members as $member)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors group" data-member-name="{{ strtolower($member->name) }}" data-member-role="{{ $member->role }}">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('users.show', $member) }}" class="font-bold text-gray-900 hover:text-blue-600 transition-colors">{{ $member->name }}</a>
                                                    <div class="text-xs text-gray-400">{{ $member->email ?? '—' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $member->role === 'lider_celula' ? 'bg-blue-50 text-blue-600' : ($member->role === 'lider' ? 'bg-purple-50 text-purple-600' : 'bg-gray-100 text-gray-600') }}">
                                                {{ $member->getRoleLabel() }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-gray-600">{{ $member->phone ?? '—' }}</td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-50 text-green-600">
                                                <i class="bi bi-check-circle-fill text-[8px]"></i> Ativo
                                            </span>
                                        </td>
                                        
                                        <td class="py-4 px-4 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-70 hover:opacity-100 transition-all">
                                                @if($cell->type === \App\Models\Cell::TYPE_LIDERES && $member->role !== 'sub_supervisor')
                                                    <form action="{{ route('cells.promote-sub-supervisor', ['cell' => $cell, 'user' => $member]) }}" method="POST" class="inline" onsubmit="return confirm('Promover {{ $member->name }} a Sub-supervisor da supervisão {{ $cell->supervision?->name }}?')">
                                                        @csrf
                                                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-purple-600 hover:bg-purple-50 transition-colors" title="Promover a Sub-supervisor"><i class="bi bi-award-fill"></i></button>
                                                    </form>
                                                @elseif($cell->type === \App\Models\Cell::TYPE_SUPERVISORES && $member->role !== 'subpastor_zona')
                                                    <form action="{{ route('cells.promote-subpastor-zona', ['cell' => $cell, 'user' => $member]) }}" method="POST" class="inline" onsubmit="return confirm('Promover {{ $member->name }} a Sub-pastor de Zona (Auxiliar)?')">
                                                        @csrf
                                                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors" title="Promover a Sub-pastor de Zona"><i class="bi bi-star-fill"></i></button>
                                                    </form>
                                                @endif
                                                <button type="button" @click='openObs({{ json_encode(["id" => $member->id, "name" => $member->name, "observations" => $member->observations ?? ""], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) }})' class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Observações"><i class="bi bi-chat-square-text"></i></button>
                                                <button type="button" @click='transfer({{ json_encode(["id" => $member->id, "name" => $member->name], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) }})' class="p-2 rounded-lg text-gray-400 hover:text-orange-600 hover:bg-orange-50 transition-colors" title="Transferir"><i class="bi bi-arrow-left-right"></i></button>
                                                <form action="{{ route('users.remove-from-cell', $member) }}" method="POST" class="inline" onsubmit="return confirm('Deseja remover este membro desta célula?')">
                                                    @csrf
                                                    <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Remover"><i class="bi bi-person-x"></i></button>
                                                </form>
                                                <a href="{{ route('members.edit', $member) }}" class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-12 text-center text-gray-400 text-sm">Nenhum membro nesta célula.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        @forelse($members as $member)
                            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100" data-member-name="{{ strtolower($member->name) }}" data-member-role="{{ $member->role }}">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('users.show', $member) }}" class="font-bold text-gray-900 hover:text-blue-600 transition-colors">{{ $member->name }}</a>
                                            <div class="text-xs text-gray-400">{{ $member->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $member->role === 'lider_celula' ? 'bg-blue-50 text-blue-600' : ($member->role === 'lider' ? 'bg-purple-50 text-purple-600' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $member->getRoleLabel() }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">{{ $member->phone ?? '—' }}</div>
                                <div class="flex gap-2 pt-3 border-t border-gray-200">
                                    @if($cell->type === \App\Models\Cell::TYPE_LIDERES && $member->role !== 'sub_supervisor')
                                        <form action="{{ route('cells.promote-sub-supervisor', ['cell' => $cell, 'user' => $member]) }}" method="POST" class="flex-1" onsubmit="return confirm('Promover {{ $member->name }} a Sub-supervisor da supervisão {{ $cell->supervision?->name }}?')">
                                            @csrf
                                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-bold text-purple-600 bg-purple-50 hover:bg-purple-100 transition-colors"><i class="bi bi-award-fill mr-1"></i> Sub-sup</button>
                                        </form>
                                    @elseif($cell->type === \App\Models\Cell::TYPE_SUPERVISORES && $member->role !== 'subpastor_zona')
                                        <form action="{{ route('cells.promote-subpastor-zona', ['cell' => $cell, 'user' => $member]) }}" method="POST" class="flex-1" onsubmit="return confirm('Promover {{ $member->name }} a Sub-pastor de Zona (Auxiliar)?')">
                                            @csrf
                                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors"><i class="bi bi-star-fill mr-1"></i> Sub-pastor</button>
                                        </form>
                                    @endif
                                    <button type="button" @click='openObs({{ json_encode(["id" => $member->id, "name" => $member->name, "observations" => $member->observations ?? ""], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) }})' class="flex-1 py-2 rounded-xl text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors"><i class="bi bi-chat-square-text mr-1"></i> Obs</button>
                                    <button type="button" @click='transfer({{ json_encode(["id" => $member->id, "name" => $member->name], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) }})' class="flex-1 py-2 rounded-xl text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors"><i class="bi bi-arrow-left-right mr-1"></i> Transferir</button>
                                    <form action="{{ route('users.remove-from-cell', $member) }}" method="POST" class="flex-1" onsubmit="return confirm('Deseja remover este membro desta célula?')">
                                        @csrf
                                        <button type="submit" class="w-full py-2 rounded-xl text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 transition-colors"><i class="bi bi-person-x mr-1"></i> Remover</button>
                                    </form>
                                    <a href="{{ route('members.edit', $member) }}" class="flex-1 py-2 rounded-xl text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors"><i class="bi bi-pencil-square mr-1"></i> Editar</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400 text-sm">Nenhum membro nesta célula.</div>
                        @endforelse
                    </div>

                    @if($members->hasPages())
                        <div class="mt-6">
                            {{ $members->appends(['tab' => 'members'])->links() }}
                        </div>
                    @endif
                </div>

                <!-- Tab: Meetings -->
                <div class="tab-panel-meetings bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Encontros realizados</h3>
                        <a href="{{ route('cell-meetings.create', ['cell_id' => $cell->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-all shadow-sm">
                            <i class="bi bi-plus-circle"></i> Novo encontro
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($meetings as $meeting)
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-blue-200 transition-colors">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600"><i class="bi bi-calendar-event-fill text-xl"></i></div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900">{{ $meeting->theme ?? 'Encontro da célula' }}</div>
                                    <div class="text-xs text-gray-400">{{ $meeting->meeting_date ? $meeting->meeting_date->format('d/m/Y H:i') : 'Data não definida' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-gray-900">{{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }} <span class="text-xs font-normal text-gray-400">presentes</span></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('cell-meetings.pdf', $meeting) }}" class="p-2 rounded-lg text-gray-400 hover:text-orange-600 hover:bg-orange-50 transition-colors" title="Exportar PDF"><i class="bi bi-file-earmark-pdf"></i></a>
                                    <a href="{{ route('cell-meetings.show', $meeting) }}" class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"><i class="bi bi-chevron-right"></i></a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400 text-sm">Nenhum encontro registado.</div>
                        @endforelse
                    </div>
                    @if($meetings->hasPages())
                        <div class="mt-6">
                            {{ $meetings->appends(['tab' => 'meetings'])->links() }}
                        </div>
                    @endif
                </div>

                <!-- Tab: Visitors -->
                <div class="tab-panel-visitors bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Visitas recentes</h3>
                        <a href="{{ route('visitors.create') }}?cell_id={{ $cell->id }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-all shadow-sm">
                            <i class="bi bi-plus-circle"></i> Nova visita
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($visitors as $visitor)
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-purple-200 transition-colors">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-bold">{{ substr($visitor->name, 0, 1) }}</div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-900">{{ $visitor->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $visitor->visit_date ? $visitor->visit_date->format('d/m/Y') : 'Data não definida' }}</div>
                                </div>
                                <div class="flex gap-2 items-center">
                                    <button type="button"
                                        @click="openFeedback($el.dataset.id, $el.dataset.status, $el.dataset.notes)"
                                        data-id="{{ $visitor->id }}"
                                        data-status="{{ $visitor->contact_status ?? 'pendente' }}"
                                        data-notes="{{ $visitor->notes ?? '' }}"
                                        class="px-4 py-2 rounded-xl text-xs font-bold {{ $visitor->contact_status === 'integrado' ? 'bg-green-50 text-green-600' : ($visitor->contact_status === 'sem_interesse' ? 'bg-red-50 text-red-600' : 'bg-yellow-50 text-yellow-600') }}">
                                        {{ $visitor->contact_status === 'integrado' ? 'Integrado' : ($visitor->contact_status === 'sem_interesse' ? 'Sem interesse' : ($visitor->contact_status === 'contatado' ? 'Contatado' : 'Pendente')) }}
                                    </button>
                                    <a href="{{ route('visitors.edit', $visitor) }}" class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"><i class="bi bi-pencil-square"></i></a>
                                    <a href="{{ route('visitors.show', $visitor) }}" class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Ver acompanhamento"><i class="bi bi-eye"></i></a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400 text-sm">Nenhuma visita registada.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab: Stats -->
                <div class="tab-panel-stats bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Desempenho da célula</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 rounded-2xl bg-blue-50 border border-blue-100 text-center">
                            <div class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-2">Fidelidade Mensal</div>
                            <div class="text-3xl font-black text-gray-900 tracking-tighter">{{ $cell->members()->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="p-6 rounded-2xl bg-green-50 border border-green-100 text-center">
                            <div class="text-[10px] font-black text-green-600 uppercase tracking-[0.2em] mb-2">Pactos Cumpridos</div>
                            <div class="text-3xl font-black text-gray-900 tracking-tighter">{{ $cell->getMembersContributedThisMonth() }}</div>
                        </div>
                        <div class="p-6 rounded-2xl bg-purple-50 border border-purple-100 text-center">
                            @php
                                $total = $cell->members()->where('is_active', true)->count();
                                $contrib = $cell->getMembersContributedThisMonth();
                                $perc = $total > 0 ? round(($contrib / $total) * 100) : 0;
                            @endphp
                            <div class="text-[10px] font-black text-purple-600 uppercase tracking-[0.2em] mb-2">Efetividade</div>
                            <div class="text-3xl font-black text-gray-900 tracking-tighter">{{ $perc }}%</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Ações rápidas</h4>
                    <div class="space-y-2">
                        <a href="{{ route('cells.pdf', $cell) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors"><i class="bi bi-file-earmark-pdf"></i></div>
                            <span class="text-sm font-bold text-gray-700 group-hover:text-orange-600 transition-colors">Exportar ficha PDF</span>
                        </a>
                        <a href="{{ route('cells.attendance', $cell) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors"><i class="bi bi-calendar-check"></i></div>
                            <span class="text-sm font-bold text-gray-700 group-hover:text-blue-600 transition-colors">Ficha de presença</span>
                        </a>
                        <a href="{{ route('cells.attendance', ['cell' => $cell->id, 'tab' => 'baptisms']) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors"><i class="bi bi-droplet-fill"></i></div>
                            <span class="text-sm font-bold text-gray-700 group-hover:text-indigo-600 transition-colors">Baptismos</span>
                        </a>
                        <a href="{{ route('contributions.index') }}?cell_id={{ $cell->id }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors"><i class="bi bi-cash-coin"></i></div>
                            <span class="text-sm font-bold text-gray-700 group-hover:text-green-600 transition-colors">Contribuições</span>
                        </a>
                        @if(!auth()->user()->isLider())
                            <a href="{{ route('cells.edit', $cell) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 group-hover:bg-gray-600 group-hover:text-white transition-colors"><i class="bi bi-pencil-square"></i></div>
                                <span class="text-sm font-bold text-gray-700 group-hover:text-gray-600 transition-colors">Editar célula</span>
                            </a>
                        @endif
                        <a href="{{ route('cells.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 group-hover:bg-gray-600 group-hover:text-white transition-colors"><i class="bi bi-arrow-left"></i></div>
                            <span class="text-sm font-bold text-gray-700 group-hover:text-gray-600 transition-colors">Voltar</span>
                        </a>
                        @if($cell->members->count() == 0)
                            <form action="{{ route('cells.destroy', $cell) }}" method="POST" id="delete-cell-form" onsubmit="return confirm('Deseja excluir esta célula?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors"><i class="bi bi-trash"></i></div>
                                    <span class="text-sm font-bold text-red-600 group-hover:text-red-700 transition-colors">Eliminar</span>
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="alert('Não é possível eliminar célula com membros. Remova ou transfira os membros antes de eliminar.')" class="w-full flex items-center gap-3 p-3 rounded-xl opacity-50 cursor-not-allowed group" disabled>
                                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-300"><i class="bi bi-trash"></i></div>
                                <span class="text-sm font-bold text-red-300">Eliminar</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Leader & Auxiliaries Info -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 space-y-4">
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Equipe de Liderança</h4>
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block mb-2">Líder Principal</span>
                        @if($cell->leader)
                            <a href="{{ route('users.show', $cell->leader) }}" class="flex items-center gap-3 hover:bg-gray-50 p-2 rounded-xl transition-colors">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold">{{ substr($cell->leader->name, 0, 1) }}</div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm">{{ $cell->leader->name }}</div>
                                    <div class="text-xs text-gray-400">Líder da célula</div>
                                </div>
                            </a>
                        @else
                            <div class="text-sm text-gray-400 py-1">Sem líder definido</div>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-3">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block mb-2">Auxiliares (Timóteos)</span>
                        @forelse($cell->timoteos as $auxiliar)
                            <a href="{{ route('users.show', $auxiliar) }}" class="flex items-center gap-3 hover:bg-gray-50 p-2 rounded-xl transition-colors">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-bold">{{ substr($auxiliar->name, 0, 1) }}</div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm">{{ $auxiliar->name }}</div>
                                    <div class="text-[11px] text-gray-400">Auxiliar da célula</div>
                                </div>
                            </a>
                        @empty
                            <div class="text-xs text-gray-400 py-1 italic">Nenhum auxiliar atribuído</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    <!-- Transfer Modal -->
    <div x-show="showTransferModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showTransferModal = false"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl p-8 max-w-lg w-full">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Transferir membro</h3>
            <p class="text-sm text-gray-500 mb-6">Mover <strong x-text="selectedMember.name"></strong> para outra célula.</p>
            <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/reassign-cell'" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Célula de destino</label>
                    <select name="cell_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all" required>
                        <option value="">Selecione uma célula...</option>
                        @foreach($availableCells as $c)
                            <option value="{{ $c->id }}">{{ $c->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showTransferModal = false" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Observations Modal -->
    <div x-show="showObsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showObsModal = false"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl p-8 max-w-lg w-full">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Observações</h3>
            <p class="text-sm text-gray-500 mb-6">Notas sobre <strong x-text="selectedMember.name"></strong>.</p>
            <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/update-observations'" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Observações</label>
                    <textarea name="observations" x-model="selectedMember.observations" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all resize-none" placeholder="Escreva aqui..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showObsModal = false" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Feedback Visitor Modal -->
    <div x-show="showFeedbackModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showFeedbackModal = false"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl p-8 max-w-lg w-full">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Registrar Feedback da Visita</h3>
            <form :action="'{{ url('/visitors') }}/' + feedbackVisitorId + '/update-feedback'" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Estado do Contacto</label>
                    <select name="contact_status" x-model="feedbackStatus" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all" required>
                        <option value="pendente">Pendente (Não contactado)</option>
                        <option value="contatado">Contatado (Em Acompanhamento)</option>
                        <option value="sem_interesse">Sem Interesse</option>
                        <option value="integrado">Integrado (Já é Membro)</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Notas</label>
                    <textarea name="notes" x-model="feedbackNotes" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all resize-none" placeholder="Observações sobre a visita..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showFeedbackModal = false" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Existing Member Modal (no Alpine dependency) -->
    <div id="addExistingModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="closeAddExistingModal()"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl p-8 max-w-lg w-full">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Adicionar membro existente</h3>
            <form action="{{ route('cells.add-member', $cell) }}" method="POST" id="addExistingForm">
                @csrf
                @error('member_id')
                    <p class="text-red-500 text-xs font-bold mb-4">{{ $message }}</p>
                @enderror
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Pesquisar pessoa</label>
                    <input type="text" id="addExistingSearchInput" placeholder="Digite o nome..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
                    <input type="hidden" name="cell_id" value="{{ $cell->id }}">
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Pessoa selecionada</label>
                    <select name="member_id" id="addExistingMemberSelect" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all" required>
                        <option value="">Selecione...</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Papel na célula</label>
                    <select name="role_in_cell" id="addExistingRoleSelect" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-sm outline-none transition-all">
                        <option value="membro">Membro</option>
                        @if($cell->type === 'membros')
                            <option value="lider">Líder</option>
                        @endif
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeAddExistingModal()" class="flex-1 py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button type="submit" id="addExistingSubmitBtn" class="flex-1 py-3 rounded-xl bg-green-600 text-white text-sm font-bold hover:bg-green-700 transition-colors" disabled>Adicionar</button>
                </div>
            </form>
        </div>
    </div>

    </div> {{-- Close x-data scope --}}

    <script>
        function filterMembers() {
            const search = document.querySelector('[x-model="memberSearch"]')?.value.toLowerCase() || '';
            const rows = document.querySelectorAll('[data-member-name]');
            rows.forEach(row => {
                const name = row.dataset.memberName || '';
                row.style.display = name.includes(search) ? '' : 'none';
            });
        }

        // Add Existing Member Modal - vanilla JS, no Alpine dependency
        var addExistingDebounce = null;
        document.getElementById('addExistingSearchInput').addEventListener('input', function() {
            clearTimeout(addExistingDebounce);
            var query = this.value.trim();
            var select = document.getElementById('addExistingMemberSelect');
            select.innerHTML = '<option value="">Selecione...</option>';
            document.getElementById('addExistingSubmitBtn').disabled = true;

            if (query.length < 2) return;

            addExistingDebounce = setTimeout(function() {
                fetch("{{ route('cells.eligible-members', $cell) }}?search=" + encodeURIComponent(query))
                    .then(r => r.json())
                    .then(data => {
                        if (data.length === 0) {
                            var emptyOpt = document.createElement('option');
                            emptyOpt.value = '';
                            emptyOpt.textContent = 'Nenhum membro compatível encontrado';
                            select.appendChild(emptyOpt);
                        }
                        data.forEach(function(member) {
                            var option = document.createElement('option');
                            option.value = member.id;
                            option.textContent = member.name + ' (' + (member.role_label || member.role) + ')' + (member.current_cell ? ' — ' + member.current_cell : '');
                            select.appendChild(option);
                        });
                    })
                    .catch(() => {
                        select.innerHTML = '<option value="">Selecione...</option>';
                    });
            }, 300);
        });

        document.getElementById('addExistingMemberSelect').addEventListener('change', function() {
            document.getElementById('addExistingSubmitBtn').disabled = !this.value;
        });

        function openAddExistingModal() {
            document.getElementById('addExistingSearchInput').value = '';
            document.getElementById('addExistingMemberSelect').innerHTML = '<option value="">Selecione...</option>';
            document.getElementById('addExistingRoleSelect').value = 'membro';
            document.getElementById('addExistingSubmitBtn').disabled = true;
            var modal = document.getElementById('addExistingModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddExistingModal() {
            var modal = document.getElementById('addExistingModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Update tab label styles when radio changes
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('input[name="cell_tab"]');
            const labels = document.querySelectorAll('.tab-nav-label');
            const activeClasses = ['bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-200'];
            const inactiveClasses = ['text-gray-500', 'hover:bg-gray-50'];

            function updateTabLabels(checkedRadio) {
                labels.forEach(label => {
                    label.classList.remove(...activeClasses);
                    label.classList.add(...inactiveClasses);
                });
                const activeLabel = document.querySelector('label[for="tab-' + checkedRadio.value + '"]');
                if (activeLabel) {
                    activeLabel.classList.remove(...inactiveClasses);
                    activeLabel.classList.add(...activeClasses);
                }
            }

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateTabLabels(this);
                });
            });

            // Set initial state
            const checkedRadio = document.querySelector('input[name="cell_tab"]:checked');
            if (checkedRadio) {
                updateTabLabels(checkedRadio);
            }

            // Auto-open add existing modal if validation error exists
            @if($errors->has('member_id'))
                openAddExistingModal();
            @endif
        });
    </script>
@endsection
