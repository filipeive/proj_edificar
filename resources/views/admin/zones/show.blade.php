@extends('layouts.app')
@section('title', "Zona $zone->name - Portal Life Church")
@section('page-title', 'Detalhes da Zona')
@section('page-subtitle', 'Visão consolidada da área pastoral ' . $zone->name)

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('zones.edit', $zone) }}" class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Configurar zona">
            <i class="bi bi-pencil-square"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8" x-data="{ 
                activeTab: 'supervisions',
                showTransferModal: false,
                selectedSupervision: {},
                transfer(supervision) {
                    this.selectedSupervision = supervision;
                    this.showTransferModal = true;
                }
            }">
        <!-- Header & Stats Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Info Zona -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Zona Pastoral</span>
                </div>
                <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ $zone->name }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span
                        class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold uppercase tracking-widest">
                        Pastor: {{ $zone->pastor->name ?? 'Pendente' }}
                    </span>
                </div>
            </div>

            <!-- Total Supervisões -->
            <div
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                <p class="text-5xl font-black text-purple-600 tracking-tighter">{{ $zone->supervisions->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Supervisões</p>
            </div>

            <!-- Total Células -->
            <div
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                <p class="text-5xl font-black text-blue-600 tracking-tighter">{{ $cells->total() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Unidades de Células</p>
            </div>

            <!-- Total Membros -->
            <div
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-5xl font-black text-green-600 tracking-tighter">{{ $members->total() }}</p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Membros Totais</p>
                </div>
                <div class="absolute -right-4 -bottom-4 text-8xl text-green-50 opacity-50"><i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 space-y-8">
                <!-- Tab Logic -->
                <div class="flex items-center gap-4 bg-white p-2 rounded-[2rem] shadow-sm border border-gray-100 w-fit">
                    <button @click="activeTab = 'supervisions'"
                        :class="activeTab === 'supervisions' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all">
                        Supervisões
                    </button>
                    <button @click="activeTab = 'cells'"
                        :class="activeTab === 'cells' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all">
                        Células
                    </button>
                    <button @click="activeTab = 'members'"
                        :class="activeTab === 'members' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all">
                        Membros
                    </button>
                </div>

                <!-- Tab Content: Supervisões -->
                <div x-show="activeTab === 'supervisions'" x-transition.fade class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($zone->supervisions as $supervision)
                        <div
                            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-blue-100 transition-all group">
                            <div class="flex items-start justify-between mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-2xl">
                                    {{ substr($supervision->name, 0, 1) }}
                                </div>
                                <div class="flex gap-2">
                                    <button @click="transfer({{ Js::from($supervision) }})"
                                        class="text-gray-300 hover:text-amber-500 transition-colors"
                                        title="Transferir para outra zona">
                                        <i class="bi bi-arrow-left-right text-xl"></i>
                                    </button>
                                    <a href="{{ route('supervisions.edit', $supervision) }}"
                                        class="text-gray-300 hover:text-blue-600 transition-colors" title="Editar supervisão">
                                        <i class="bi bi-pencil-square text-xl"></i>
                                    </a>
                                    <a href="{{ route('supervisions.show', $supervision) }}"
                                        class="text-gray-300 hover:text-blue-600 transition-colors">
                                        <i class="bi bi-arrow-up-right-circle text-2xl"></i>
                                    </a>
                                    <form action="{{ route('supervisions.destroy', $supervision) }}" method="POST"
                                        id="delete-supervision-{{ $supervision->id }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            onclick="confirmDelete('delete-supervision-{{ $supervision->id }}', 'Deseja excluir esta supervisão?')"
                                            class="text-gray-300 hover:text-red-600 transition-colors" title="Eliminar supervisão">
                                            <i class="bi bi-trash text-xl"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $supervision->name }}</h3>
                            <div class="flex gap-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-400 uppercase">Células</span>
                                    <span class="text-lg font-black text-gray-900">{{ $supervision->cells->count() }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-400 uppercase">Membros</span>
                                    <span
                                        class="text-lg font-black text-gray-900">{{ $supervision->cells->flatMap(fn($c) => $c->members)->count() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-16 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-bold italic">Nenhuma supervisão registrada.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Tab Content: Células -->
                <div x-show="activeTab === 'cells'" x-transition.fade
                    class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    @if($cells->count() > 0)
                        <div class="md:hidden divide-y divide-gray-50">
                            @foreach($cells as $cell)
                                <div class="p-6 flex items-start justify-between gap-4">
                                    <div class="min-w-0 space-y-2">
                                        <p class="text-sm font-black text-gray-900 leading-tight line-clamp-1">
                                            {{ $cell->name }}
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black text-gray-500 uppercase">
                                                {{ $cell->supervision->name ?? 'Sem supervisão' }}
                                            </span>
                                            <span
                                                class="px-3 py-1 bg-blue-50 rounded-full text-[10px] font-black text-blue-600 uppercase">
                                                {{ $cell->leader->name ?? 'Sem líder' }}
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('cells.show', $cell) }}" title="Detalhes"
                                        class="action-icon text-gray-300 hover:text-blue-600 hover:bg-blue-50">
                                        <i class="bi bi-chevron-right text-lg"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full table-compact">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th
                                            class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Unidade</th>
                                        <th
                                            class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Supervisão</th>
                                        <th
                                            class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Liderança</th>
                                        <th
                                            class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($cells as $cell)
                                        <tr class="hover:bg-gray-50/50 transition-colors group">
                                            <td class="px-10 py-6 font-bold text-gray-900">
                                                <span class="block line-clamp-1">{{ $cell->name }}</span>
                                            </td>
                                            <td class="px-10 py-6 text-sm text-gray-500 font-medium">
                                                <span class="block line-clamp-1">
                                                    {{ $cell->supervision->name ?? 'Sem supervisão' }}
                                                </span>
                                            </td>
                                            <td class="px-10 py-6 text-sm font-bold text-gray-700">
                                                <span class="block line-clamp-1">{{ $cell->leader->name ?? '-' }}</span>
                                            </td>
                                            <td class="px-10 py-6 text-right">
                                                <a href="{{ route('cells.show', $cell) }}" title="Detalhes"
                                                    class="action-icon text-gray-300 hover:text-blue-600 hover:bg-blue-50">
                                                    <i class="bi bi-chevron-right text-lg"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div
                            class="py-16 text-center bg-gray-50/50 rounded-[2.5rem] border-2 border-dashed border-gray-200 m-6">
                            <p class="text-gray-400 font-bold italic">Nenhuma célula registrada nesta zona.</p>
                        </div>
                    @endif
                    @if($cells->hasPages())
                        <div class="p-6 border-t border-gray-50">
                            {{ $cells->links() }}
                        </div>
                    @endif
                </div>

                <!-- Tab Content: Membros -->
                <div x-show="activeTab === 'members'" x-data="{ 
                        memberSearch: '',
                        showTransferMemberModal: false,
                        showObservationsModal: false,
                        selectedMember: {},
                        memberObservations: '',

                        openTransferMember(member) {
                            this.selectedMember = member;
                            this.showTransferMemberModal = true;
                        },
                        openObservations(member) {
                            this.selectedMember = member;
                            this.memberObservations = member.observations || '';
                            this.showObservationsModal = true;
                        }
                    }" x-transition.fade
                    class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">

                    <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                        <div class="relative">
                            <i class="bi bi-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="memberSearch" placeholder="Pesquisar membros por nome ou email..."
                                class="w-full pl-14 pr-6 py-4 bg-white border-transparent focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-compact">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th
                                        class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Membro</th>
                                    <th
                                        class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Célula</th>
                                    <th
                                        class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Status</th>
                                    <th
                                        class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($members as $member)
                                    <tr class="hover:bg-gray-50/50 transition-colors group"
                                        x-show="memberSearch === '' || '{{ strtolower($member->name) }}'.includes(memberSearch.toLowerCase()) || '{{ strtolower($member->email) }}'.includes(memberSearch.toLowerCase())">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center font-bold">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 leading-tight line-clamp-1">
                                                        {{ $member->name }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-medium line-clamp-1">
                                                        {{ $member->email }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black text-gray-500 uppercase">
                                                {{ $member->cell->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <form action="{{ route('users.toggle-status', $member) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 rounded-full text-[10px] font-black uppercase transition-all {{ $member->is_active ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                                                    {{ $member->is_active ? 'Ativo' : 'Inativo' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button @click="openObservations({{ Js::from($member) }})"
                                                    class="action-icon text-gray-300 hover:text-orange-500 hover:bg-orange-50"
                                                    title="Observações">
                                                    <i class="bi bi-chat-dots text-lg"></i>
                                                </button>
                                                <button @click="openTransferMember({{ Js::from($member) }})"
                                                    class="action-icon text-gray-300 hover:text-amber-500 hover:bg-amber-50"
                                                    title="Transferir Célula">
                                                    <i class="bi bi-arrow-left-right text-lg"></i>
                                                </button>
                                                <a href="{{ route('users.show', $member) }}" title="Perfil Completo"
                                                    class="action-icon text-gray-300 hover:text-blue-600 hover:bg-blue-50">
                                                    <i class="bi bi-person-fill text-lg"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($members->hasPages())
                        <div class="p-8 border-t border-gray-50">
                            {{ $members->links() }}
                        </div>
                    @endif

                    <!-- Transfer Member Modal -->
                    <div x-show="showTransferMemberModal"
                        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"
                        x-cloak>
                        <div @click.away="showTransferMemberModal = false"
                            class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden">
                            <div class="p-8 border-b border-gray-100">
                                <h3 class="text-xl font-black text-gray-900 leading-tight">Transferir Membro</h3>
                                <p class="text-sm text-gray-500 mt-1"
                                    x-text="'Mover ' + selectedMember.name + ' para outra célula'"></p>
                            </div>
                            <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/reassign-cell'"
                                method="POST" class="p-8 space-y-6">
                                @csrf
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Selecione
                                        a Célula de Destino</label>
                                    <select name="cell_id" required
                                        class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all custom-select">
                                        <option value="">Escolha uma célula...</option>
                                        @foreach($cells as $availCell)
                                            <option value="{{ $availCell->id }}">{{ $availCell->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="button" @click="showTransferMemberModal = false"
                                        class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 text-gray-500 font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">Cancelar</button>
                                    <button type="submit"
                                        class="flex-1 px-6 py-4 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">Confirmar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Member Observations Modal -->
                    <div x-show="showObservationsModal"
                        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"
                        x-cloak>
                        <div @click.away="showObservationsModal = false"
                            class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden">
                            <div class="p-8 border-b border-gray-100">
                                <h3 class="text-xl font-black text-gray-900 leading-tight">Observações</h3>
                                <p class="text-sm text-gray-500 mt-1" x-text="'Notas sobre ' + selectedMember.name"></p>
                            </div>
                            <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/observations'" method="POST"
                                class="p-8 space-y-6">
                                @csrf
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Conteúdo
                                        das Notas</label>
                                    <textarea name="observations" x-model="memberObservations" rows="5"
                                        class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all resize-none"
                                        placeholder="Escreva observações importantes aqui..."></textarea>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="button" @click="showObservationsModal = false"
                                        class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 text-gray-500 font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">Cancelar</button>
                                    <button type="submit"
                                        class="flex-1 px-6 py-4 rounded-2xl bg-orange-600 text-white font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-200">Salvar
                                        Notas</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna de Ações (Hidden on Mobile) -->
            <div class="space-y-6 hidden md:block">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Gestão da Zona</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('zones.edit', $zone) }}"
                            class="w-full bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-pencil-square"></i> Configurar Zona
                        </a>
                        <a href="{{ route('zones.index') }}"
                            class="w-full bg-gray-50 text-gray-500 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-arrow-left"></i> Voltar à Lista
                        </a>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-green-900 to-emerald-900 rounded-[2.5rem] shadow-xl p-10 text-white relative overflow-hidden">
                    <div class="relative z-10 space-y-6">
                        <p class="text-[10px] font-black text-green-300 uppercase tracking-[0.2em]">Faturamento Zona</p>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-green-100">Arrecadação Mensal</p>
                            <div class="flex items-end gap-2">
                                <span
                                    class="text-4xl font-black tracking-tighter text-white">{{ number_format($zone->getTotalContributedThisMonth(), 0, ',', '.') }}</span>
                                <span class="text-xs font-bold text-green-300 mb-1">MT</span>
                            </div>
                        </div>
                    </div>
                    <i class="bi bi-cash-stack absolute -right-4 -bottom-4 text-9xl text-white opacity-5"></i>
                </div>

                @if($zone->supervisions->count() === 0)
                    <div class="bg-red-50 p-6 rounded-[2rem] border border-red-100">
                        <h4 class="text-sm font-black text-red-900 uppercase mb-2">Zona de Perigo</h4>
                        <form action="{{ route('zones.destroy', $zone) }}" method="POST" id="delete-form">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form', 'Deseja excluir esta zona?')"
                                class="w-full py-3 bg-white text-red-600 rounded-xl font-bold border border-red-100 hover:bg-red-600 hover:text-white transition-all text-xs uppercase">
                                Excluir Zona
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transfer Supervision Modal -->
        <div x-show="showTransferModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;" x-cloak>
            <div @click.away="showTransferModal = false"
                class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-In">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">Transferir Supervisão</h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="'Mover ' + selectedSupervision.name + ' para outra zona'">
                    </p>
                </div>
                <form :action="'{{ url('/admin/supervisions') }}/' + selectedSupervision.id + '/reassign-zone'"
                    method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Selecione a Zona
                            de Destino</label>
                        <select name="zone_id" required
                            class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all custom-select">
                            <option value="">Escolha uma zona...</option>
                            @foreach($availableZones as $availZone)
                                <option value="{{ $availZone->id }}">{{ $availZone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showTransferModal = false"
                            class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 text-gray-500 font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">Cancelar</button>
                        <button type="submit"
                            class="flex-1 px-6 py-4 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
