@extends('layouts.app')

@section('title', 'Detalhes do Pacote - ' . $package->name)

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        @if($package->whatsapp_link)
            <a href="{{ $package->whatsapp_link }}" target="_blank"
                class="action-icon text-gray-600 hover:text-green-600 hover:bg-green-50" title="Grupo WhatsApp">
                <i class="bi bi-whatsapp"></i>
            </a>
        @endif
        <a href="{{ route('packages.export', $package) }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Exportar">
            <i class="bi bi-file-earmark-excel"></i>
        </a>
        <a href="{{ route('packages.edit', $package) }}"
            class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50" title="Editar">
            <i class="bi bi-pencil-square"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6" x-data="{ 
                                view: window.innerWidth < 768 ? 'grid' : 'list', 
                                search: '',
                                selected: [], 
                                selectAll: false,
                                updateSelection() {
                                    this.selected = this.selectAll ? {{ $commitmentUserIds }} : [];
                                }
                            }">
        <!-- New Compact Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('packages.index') }}"
                    class="w-10 h-10 rounded-xl bg-white text-gray-400 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-all shadow-sm border border-gray-100">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $package->name }}</h1>
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">
                        <span>Gestão de Membros</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span
                            class="{{ $package->is_active ? 'text-green-500' : 'text-red-500' }}">{{ $package->is_active ? 'Ativo' : 'Inativo' }}</span>
                    </div>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-2">
                @if($package->whatsapp_link)
                    <a href="{{ $package->whatsapp_link }}" target="_blank"
                        class="px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-all text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                @endif
                <div class="h-8 w-px bg-gray-200 mx-2"></div>
                <a href="{{ route('packages.export', $package) }}"
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-all text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
                <a href="{{ route('packages.export-pdf', $package) }}"
                    class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-all text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
                <a href="{{ route('packages.edit', $package) }}"
                    class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-all text-xs font-bold uppercase tracking-widest flex items-center gap-2 shadow-lg shadow-gray-200">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>
            </div>
        </div>

        <!-- Top Stats Bar (Horizontal) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Min Amount -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor Mínimo</span>
                <div class="flex items-end justify-between mt-2">
                    <span
                        class="text-xl font-black text-gray-900">{{ number_format($package->min_amount, 2, ',', '.') }}</span>
                    <span class="text-[10px] font-bold text-gray-400 mb-1">MT</span>
                </div>
            </div>

            <!-- Max Amount -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor Máximo</span>
                <div class="flex items-end justify-between mt-2">
                    <span class="text-xl font-black text-gray-900">
                        @if($package->max_amount)
                            {{ number_format($package->max_amount, 2, ',', '.') }}
                        @else
                            ∞
                        @endif
                    </span>
                    <span class="text-[10px] font-bold text-gray-400 mb-1">MT</span>
                </div>
            </div>

            <!-- Active Members -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros Ativos</span>
                <div class="flex items-end justify-between mt-2">
                    <span class="text-xl font-black text-blue-600">{{ $package->getActiveMembersCount() }}</span>
                    <div class="w-6 h-6 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                        <i class="bi bi-people-fill text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Responsavel -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Responsável</span>
                <div class="mt-2">
                    <span class="text-sm font-bold text-gray-900 line-clamp-1">{{ $package->getResponsavelName() }}</span>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: Members List (Span 2) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Toolbar -->
                <div
                    class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="relative w-full sm:w-64">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" x-model="search" placeholder="Pesquisar membro..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500 rounded-xl text-sm font-bold transition-all">
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <!-- Bulk Actions -->
                        <div x-show="selected.length > 0" x-transition
                            class="flex items-center gap-2 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 mr-auto sm:mr-0">
                            <span class="text-[10px] font-black uppercase text-red-600"
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

                        <!-- View Toggles -->
                        <div class="flex bg-gray-100 p-1 rounded-lg ml-auto">
                            <button @click="view = 'list'"
                                :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                class="w-8 h-8 rounded-md flex items-center justify-center transition-all">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button @click="view = 'grid'"
                                :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                class="w-8 h-8 rounded-md flex items-center justify-center transition-all">
                                <i class="bi bi-grid-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div x-show="view === 'list'" x-transition
                    class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full table-compact">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 w-10">
                                        <input type="checkbox" x-model="selectAll" @change="updateSelection()"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-6 py-4 text-left">Membro</th>
                                    <th class="px-6 py-4 text-left">Valor</th>
                                    <th class="px-6 py-4 text-left hidden sm:table-cell">Célula</th>
                                    <th class="px-6 py-4 text-center">Estado Campanha</th>
                                    <th class="px-6 py-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($commitments as $commitment)
                                    <tr class="hover:bg-gray-50/50 transition-colors"
                                        x-show="!search || '{{ strtolower($commitment->user->name) }}'.includes(search.toLowerCase()) || '{{ $commitment->user->phone }}'.includes(search)">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" value="{{ $commitment->user_id }}" x-model="selected"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center font-bold text-gray-500 text-xs">
                                                    {{ strtoupper(substr($commitment->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $commitment->user->name }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 font-mono">
                                                        {{ $commitment->user->phone }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                            {{ number_format($commitment->committed_amount, 2, ',', '.') }} MT
                                        </td>
                                        <td class="px-6 py-4 text-xs font-medium text-gray-500 hidden sm:table-cell">
                                            {{ $commitment->user->cell->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $status = $commitment->getCampaignStatus();
                                                $totalContributed = $commitment->getTotalContributed();
                                                $progress = $commitment->getProgressPercentage();
                                            @endphp
                                            
                                            <div class="flex flex-col items-center gap-2">
                                                @if($status === 'surplus')
                                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide bg-blue-100 text-blue-700">
                                                        Pago com Acréscimo
                                                    </span>
                                                    <span class="text-[9px] text-blue-500 font-bold">
                                                        + {{ number_format($commitment->getSurplusAmount(), 2, ',', '.') }} MT
                                                    </span>
                                                @elseif($status === 'paid')
                                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide bg-green-100 text-green-700">
                                                        Pago ✓
                                                    </span>
                                                @elseif($status === 'partial')
                                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide bg-yellow-100 text-yellow-700">
                                                        Parcial
                                                    </span>
                                                    <span class="text-[9px] text-yellow-600 font-bold">
                                                        {{ number_format($totalContributed, 2, ',', '.') }} MT
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-400">
                                                        Pendente
                                                    </span>
                                                @endif

                                                <!-- Mini Progress Bar -->
                                                <div class="w-full max-w-[80px] h-1 bg-gray-100 rounded-full overflow-hidden mt-1">
                                                    <div class="h-full bg-blue-500 transition-all duration-500" style="width: {{ $progress }}%"></div>
                                                </div>
                                                <span class="text-[9px] font-bold text-gray-400">{{ $progress }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-1">
                                                <button type="button" @click="$dispatch('open-edit-member-modal', { 
                                                            userId: {{ $commitment->user_id }}, 
                                                            userName: '{{ $commitment->user->name }}', 
                                                            phone: '{{ $commitment->user->phone }}', 
                                                            cellId: '{{ $commitment->user->cell_id }}',
                                                            amount: '{{ $commitment->committed_amount }}'
                                                        })" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>

                                                        <button type="button" @click="$dispatch('open-change-package-modal', { 
                                                            userId: {{ $commitment->user_id }}, 
                                                            userName: '{{ $commitment->user->name }}',
                                                            currentAmount: '{{ $commitment->committed_amount }}'
                                                        })" class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Mudar Pacote">
                                                            <i class="bi bi-arrow-left-right"></i>
                                                        </button>

                                                        @if($commitment->user->phone)
                                                            @php
                                                                $name = $commitment->user->name;
                                                                $smsBody = str_replace('[NOME]', $name, $package->sms_template ?? "Olá [NOME], lembrete de contribuição para o Projetor Edificar.");
                                                            @endphp
                                                            <button type="button" @click="$dispatch('open-sms-member-modal', { 
                                                                userId: {{ $commitment->user_id }}, 
                                                                userName: '{{ $commitment->user->name }}',
                                                                message: '{{ $smsBody }}'
                                                            })" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Enviar SMS/WhatsApp">
                                                                <i class="bi bi-chat-dots-fill"></i>
                                                            </button>
                                                        @endif

                                                        <a href="{{ route('contributions.create') }}?user_id={{ $commitment->user_id }}&package_id={{ $package->id }}" 
                                                           class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors" title="Adicionar Pagamento">
                                                            <i class="bi bi-plus-circle-fill"></i>
                                                        </a>

                                                        <a href="{{ route('users.show', $commitment->user_id) }}" 
                                                           class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Ver Perfil">
                                                            <i class="bi bi-person-fill"></i>
                                                        </a>

                                                        <form action="{{ route('packages.members.remove', [$package, $commitment->user_id]) }}" method="POST"
                                                            onsubmit="return confirm('Remover membro?');" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Remover">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                                                Nenhum membro encontrado
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Grid View -->
                    <div x-show="view === 'grid'" x-transition class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @forelse($commitments as $commitment)
                            <div x-show="!search || '{{ strtolower($commitment->user->name) }}'.includes(search.toLowerCase()) || '{{ $commitment->user->phone }}'.includes(search)"
                                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative group compact-card">
                                <div class="absolute top-4 right-4 flex items-center gap-2">
                                    @php
                                        $status = $commitment->getCampaignStatus();
                                    @endphp
                                    @if($status === 'surplus' || $status === 'paid')
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                    @elseif($status === 'partial')
                                        <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                    @endif

                                    <form action="{{ route('packages.members.remove', [$package, $commitment->user_id]) }}" method="POST"
                                        onsubmit="return confirm('Remover membro?');" class="inline opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center font-black text-gray-500 text-sm">
                                        {{ strtoupper(substr($commitment->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-900 line-clamp-1">{{ $commitment->user->name }}</h4>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $commitment->user->phone }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="text-gray-400 font-bold uppercase">Compromisso</span>
                                        <span class="font-black text-gray-900">{{ number_format($commitment->committed_amount, 2, ',', '.') }} MT</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="text-gray-400 font-bold uppercase">Célula</span>
                                        <span class="font-medium text-gray-600 truncate max-w-[100px]">{{ $commitment->user->cell->name ?? '-' }}</span>
                                    </div>
                                    @php $progress = $commitment->getProgressPercentage(); @endphp
                                    <div class="pt-2">
                                        <div class="flex justify-between items-center text-[9px] mb-1">
                                            <span class="text-gray-400 font-bold uppercase text-[8px]">Progresso</span>
                                            <span class="font-black text-blue-600">{{ $progress }}%</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 mb-2">
                                    <a href="{{ route('contributions.create') }}?user_id={{ $commitment->user_id }}&package_id={{ $package->id }}"
                                        class="py-2.5 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest text-center hover:bg-gray-800 transition-all">
                                        Contribuir
                                    </a>
                                    <button type="button" @click="$dispatch('open-edit-member-modal', { 
                                        userId: {{ $commitment->user_id }}, 
                                        userName: '{{ $commitment->user->name }}', 
                                        phone: '{{ $commitment->user->phone }}', 
                                        cellId: '{{ $commitment->user->cell_id }}',
                                        amount: '{{ $commitment->committed_amount }}'
                                    })" class="py-2.5 bg-gray-100 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                                        Editar
                                    </button>
                                </div>

                                <div class="flex items-center justify-center gap-4 pt-1 border-t border-gray-50">
                                    <button type="button" @click="$dispatch('open-change-package-modal', { 
                                        userId: {{ $commitment->user_id }}, 
                                        userName: '{{ $commitment->user->name }}',
                                        currentAmount: '{{ $commitment->committed_amount }}'
                                    })" class="text-purple-400 hover:text-purple-600 text-[10px] font-bold uppercase" title="Mudar Pacote">
                                        Mudar
                                    </button>
                                    <div class="w-px h-3 bg-gray-200"></div>
                                    @if($commitment->user->phone)
                                        @php
                                            $name = $commitment->user->name;
                                            $smsBody = str_replace('[NOME]', $name, $package->sms_template ?? "Olá [NOME], lembrete de contribuição para o Projetor Edificar.");
                                        @endphp
                                        <button type="button" @click="$dispatch('open-sms-member-modal', { 
                                            userId: {{ $commitment->user_id }}, 
                                            userName: '{{ $commitment->user->name }}',
                                            message: '{{ $smsBody }}'
                                        })" class="text-green-400 hover:text-green-600 text-[10px] font-bold uppercase" title="SMS">
                                            SMS
                                        </button>
                                    @endif
                                    <div class="w-px h-3 bg-gray-200"></div>
                                    <a href="{{ route('users.show', $commitment->user_id) }}" class="text-gray-400 hover:text-gray-600 text-[10px] font-bold uppercase" title="Perfil">
                                        Perfil
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-gray-400 text-xs font-bold uppercase">
                                Nenhum membro encontrado
                            </div>
                        @endforelse
                    </div>

                    @if($commitments->hasPages())
                        <div class="mt-4">
                            {{ $commitments->links() }}
                        </div>
                    @endif
                </div>

                <!-- Right Column: Actions & Form (Span 1) -->
                <div class="lg:col-span-1 space-y-6">

                    <!-- Add Member Box -->
                    <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">Adicionar Membro</h3>
                        <form action="{{ route('packages.assign', $package) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Membro</label>
                                <select name="user_id" required class="w-full bg-gray-50 border-none rounded-xl py-2.5 px-3 text-sm font-bold text-gray-900 appearance-none custom-select">
                                    <option value="">Selecionar...</option>
                                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Valor (MT)</label>
                                <input type="number" name="committed_amount" value="{{ $package->min_amount }}" step="0.01" required
                                    class="w-full bg-gray-50 border-none rounded-xl py-2.5 px-3 text-sm font-bold text-gray-900">
                            </div>
                            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                                Adicionar
                            </button>
                        </form>

                        <!-- Quick add button -->
                        @if(auth()->user()->isAdmin() || auth()->user()->isComissaoObra() || auth()->user()->isResponsavelPacote())
                            <button type="button" @click="$dispatch('open-quick-member-modal')"
                                class="w-full mt-3 py-3 bg-blue-50 text-blue-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-100 transition-all">
                                Novo Membro Rápido
                            </button>
                        @endif
                    </div>

                    <!-- Bulk Communication -->
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-6 rounded-[2rem] shadow-lg shadow-blue-200 text-white">
                        <h3 class="text-xs font-black uppercase tracking-widest mb-4 text-blue-100">Comunicação</h3>

                        <div class="space-y-3">
                            <button onclick="copyToClipboard('{{ $package->whatsapp_template ?? 'Paz do Senhor!' }}', 'Copiado!', this)"
                                class="w-full py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition-all">
                                <i class="bi bi-clipboard"></i> Copiar Msg
                            </button>

                            <form action="{{ route('packages.send-bulk-sms', $package) }}" method="POST">
                                @csrf
                                <button type="button" 
                                    onclick="confirmAction('Enviar SMS para todos?', 'Disparar SMS').then(r => { if(r.isConfirmed) this.closest('form').submit(); })"
                                    class="w-full py-3 bg-white text-blue-600 rounded-xl font-bold text-xs flex items-center justify-center gap-2 hover:bg-blue-50 transition-all shadow-lg">
                                    <i class="bi bi-chat-dots-fill"></i> SMS em Massa
                                </button>
                            </form>

                            <button onclick="copyToClipboard('{{ $commitmentPhones }}', 'Contactos copiados!', this)"
                                class="w-full py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition-all">
                                <i class="bi bi-person-lines-fill"></i> Copiar Contactos
                            </button>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100">
                         <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-2">Sobre</h3>
                         <p class="text-xs text-gray-500 leading-relaxed">
                            {{ $package->description ?? 'Sem descrição.' }}
                         </p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modals (Edit, Quick Member, etc) -->
        <!-- Edit Member Modal -->
        <div x-data="{ show: false, userId: '', userName: '', phone: '', cellId: '', amount: '' }" 
             @open-edit-member-modal.window="show = true; userId = $event.detail.userId; userName = $event.detail.userName; phone = $event.detail.phone; cellId = $event.detail.cellId; amount = $event.detail.amount;" 
             x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="show = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md z-10 relative overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900">Editar Membro</h3>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="bi bi-x-lg"></i></button>
                    </div>
                    @if($errors->any())
                        <div class="p-4 bg-red-50 text-red-600 text-xs font-bold border-b border-red-100">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('packages.update-member', $package) }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="user_id" :value="userId">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Telefone</label>
                            <input type="text" name="phone" x-model="phone" class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Célula</label>
                            <select name="cell_id" x-model="cellId" class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900 custom-select">
                                <option value="">Sem Célula</option>
                                @foreach(\App\Models\Cell::orderBy('name')->get() as $cell)
                                    <option value="{{ $cell->id }}">{{ $cell->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Valor (MT)</label>
                            <input type="number" name="committed_amount" x-model="amount" step="0.01" required
                                 class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900">
                        </div>
                        <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">Salvar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Member Modal -->
        <div x-data="{ show: false }" @open-quick-member-modal.window="show = true" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="show = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md z-10 relative overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900">Novo Membro Rápido</h3>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <form action="{{ route('packages.quick-member', $package) }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Nome</label>
                            <input type="text" name="name" required placeholder="João Silva" class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Telefone</label>
                            <input type="text" name="phone" placeholder="82..." class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Célula</label>
                            <select name="cell_id" required class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900 custom-select">
                                <option value="">Selecionar...</option>
                                @foreach($availableCells as $cell)
                                    <option value="{{ $cell->id }}">{{ $cell->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Valor (MT)</label>
                            <input type="number" name="committed_amount" value="{{ $package->min_amount }}" step="0.01" required
                                 class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900">
                        </div>
                        <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">Criar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Package Modal -->
        <div x-data="{ show: false, userId: '', userName: '', amount: '' }" 
             @open-change-package-modal.window="show = true; userId = $event.detail.userId; userName = $event.detail.userName; amount = $event.detail.currentAmount;" 
             x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="show = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md z-10 relative overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900">Mudar Pacote</h3>
                        <div class="text-right">
                            <p class="text-[10px] uppercase font-bold text-gray-400" x-text="userName"></p>
                        </div>
                    </div>
                    <!-- Note: The action URL needs to be dynamic or handled via JS. Blade compiles once. 
                         We can use a placeholder and replace it on submit or use a JS form submission. 
                         Here I'll use a dynamic action approach compatible with Alpine. -->
                    <form :action="`{{ url('admin/packages/' . $package->id . '/members') }}/${userId}/change-package`" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Novo Pacote</label>
                            <select name="new_package_id" required class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900 custom-select">
                                @foreach($allPackages as $p)
                                    @if($p->id !== $package->id)
                                        <option value="{{ $p->id }}">{{ $p->name }} (Min: {{ number_format($p->min_amount, 2) }} MT)</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Valor (MT)</label>
                            <input type="number" name="committed_amount" x-model="amount" step="0.01" required
                                 class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-bold text-gray-900">
                        </div>
                        <button type="submit" class="w-full py-3 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition">Confirmar Mudança</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SMS Modal -->
        <div x-data="{ show: false, userId: '', userName: '', message: '' }" 
             @open-sms-member-modal.window="show = true; userId = $event.detail.userId; userName = $event.detail.userName; message = $event.detail.message;" 
             x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="show = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md z-10 relative overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900">Enviar SMS</h3>
                        <div class="text-right">
                            <p class="text-[10px] uppercase font-bold text-gray-400" x-text="userName"></p>
                        </div>
                    </div>
                    <form :action="`{{ url('admin/packages/' . $package->id . '/members') }}/${userId}/send-sms`" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Mensagem</label>
                            <textarea name="message" x-model="message" rows="4" required class="w-full mt-1 bg-gray-50 border-none rounded-xl text-sm font-medium text-gray-900" placeholder="Escreva a mensagem aqui..."></textarea>
                            <p class="text-[10px] text-gray-400 mt-1">Use [NOME] para personalizar</p>
                        </div>
                        <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">Enviar</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function copyToClipboard(text, successMessage, element) {
                if (!text) return alert('Nada para copiar');
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = element.innerHTML;
                    element.innerHTML = `<i class="bi bi-check2"></i> ${successMessage}`;
                    element.classList.add('bg-green-500', 'text-white', 'border-transparent');
                    setTimeout(() => {
                        element.innerHTML = originalText;
                        element.classList.remove('bg-green-500', 'text-white', 'border-transparent');
                    }, 2000);
                });
            }
        </script>
@endsection
