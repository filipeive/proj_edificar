@extends('layouts.app')

@section('title', 'Detalhes do Utilizador - Portal Life Church')
@section('page-title', 'Detalhes do Utilizador')
@section('page-subtitle', 'Visão consolidada do perfil e atividade de ' . $user->name)

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('users.edit', $user) }}"
            class="px-6 py-3 bg-white text-blue-600 rounded-2xl font-black text-xs uppercase tracking-widest border border-blue-100 hover:bg-blue-50 transition-all flex items-center gap-2 shadow-sm">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden md:inline">Editar Perfil</span>
        </a>
        @if($user->role !== 'admin')
            <form action="{{ route('users.destroy', $user) }}" method="POST" id="delete-user-form-header" class="inline">
                @csrf @method('DELETE')
                <button type="button" onclick="confirmDelete('delete-user-form-header', 'Deletar {{ $user->name }}?')"
                    class="p-3 bg-red-50 text-red-600 rounded-2xl hover:bg-red-100 transition-all border border-red-100 shadow-sm"
                    title="Eliminar registro">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
    <div class="space-y-8 w-full">
        <!-- Main Profile Banner -->
        <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 p-8 md:p-12 overflow-hidden relative group">
            <div
                class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-50/20 to-transparent pointer-events-none">
            </div>
            <div
                class="absolute -right-20 -top-20 w-96 h-96 bg-blue-600/5 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-1000">
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center lg:items-start gap-12">
                <!-- Profile Avatar Section -->
                <div class="relative">
                    <div
                        class="w-40 h-40 rounded-[3rem] bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-6xl shadow-2xl shadow-blue-200 relative z-10 transform group-hover:-rotate-3 transition-transform duration-500">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div
                        class="absolute -bottom-2 -right-2 w-12 h-12 bg-white rounded-2xl shadow-xl flex items-center justify-center text-2xl border-4 border-white">
                        @if($user->is_active)
                            <i class="bi bi-check-circle-fill text-green-500"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-red-500"></i>
                        @endif
                    </div>
                </div>

                <!-- User Info Section -->
                <div class="flex-1 text-center lg:text-left space-y-6">
                    <div>
                        <div class="flex flex-wrap justify-center lg:justify-start items-center gap-4 mb-2">
                            <h1 class="text-5xl font-black text-gray-900 tracking-tighter">{{ $user->name }}</h1>
                            <span
                                class="px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-200">
                                {{ $user->role === 'administracao' ? 'Administração' : str_replace('_', ' ', $user->role) }}
                            </span>
                        </div>
                        <p class="text-xl font-bold text-gray-400 max-w-2xl">
                            {{ $user->email }}
                            <span class="mx-3 opacity-30">|</span>
                            {{ $user->phone ?? 'Sem contato registrado' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                        @if($user->cell)
                            <div class="px-6 py-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-blue-600">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Célula</p>
                                    <p class="text-sm font-black text-gray-900">{{ $user->cell->name }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="px-6 py-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-purple-600">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Membro Desde</p>
                                <p class="text-sm font-black text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex flex-col gap-3 min-w-[280px]">
                    <a href="{{ route('contributions.create', ['user_id' => $user->id]) }}"
                        class="w-full py-5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-100 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3">
                        <i class="bi bi-plus-circle-fill"></i> Lançar Nova Oferta
                    </a>
                    <a href="{{ route('users.activity', $user) }}"
                        class="w-full py-4 bg-gray-50 border border-gray-200 text-gray-700 rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-gray-100 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3">
                        <i class="bi bi-clock-history"></i> Ver Atividades
                    </a>
                </div>
            </div>
        </div>

        <!-- 4-Column Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Stat 1: Commitment -->
            <div
                class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:border-green-100 transition-all">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Compromisso Ativo</p>
                    @php $activeCommitment = $user->commitments->whereNull('end_date')->first(); @endphp
                    @if($activeCommitment)
                        <div class="flex items-baseline gap-1">
                            <span
                                class="text-4xl font-black text-gray-900 tracking-tighter">{{ number_format($activeCommitment->committed_amount, 0, ',', '.') }}</span>
                            <span class="text-xs font-bold text-green-600">MT</span>
                        </div>
                        <p class="text-[10px] font-bold text-green-500 uppercase mt-2 bg-green-50 px-2 py-1 rounded-lg w-fit">
                            {{ $activeCommitment->package->name ?? 'Pacote' }}
                        </p>
                    @else
                        <p class="text-2xl font-black text-gray-300 italic">Inativo</p>
                    @endif
                </div>
                <i
                    class="bi bi-handshake absolute -right-4 -bottom-4 text-8xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
            </div>

            <!-- Stat 2: Total Validated -->
            <div
                class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:border-blue-100 transition-all">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Total Validado</p>
                    <div class="flex items-baseline gap-1">
                        <span
                            class="text-4xl font-black text-gray-900 tracking-tighter">{{ number_format($user->contributions->where('status', 'verificada')->sum('amount'), 0, ',', '.') }}</span>
                        <span class="text-xs font-bold text-blue-600">MT</span>
                    </div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase mt-2">
                        {{ $user->contributions->where('status', 'verificada')->count() }} Ofertas
                    </p>
                </div>
                <i
                    class="bi bi-graph-up-arrow absolute -right-4 -bottom-4 text-8xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
            </div>

            <!-- Stat 3: Total Logs -->
            <div
                class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:border-purple-100 transition-all">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Registros Totais</p>
                    <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $user->contributions->count() }}</p>
                    <p class="text-[10px] font-bold text-purple-500 uppercase mt-2">Lançamentos</p>
                </div>
                <i
                    class="bi bi-database absolute -right-4 -bottom-4 text-8xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
            </div>

            <!-- Stat 4: Last Access -->
            <div
                class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:border-orange-100 transition-all">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Último Acesso</p>
                    <p class="text-xl font-black text-gray-900 tracking-tighter leading-tight">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                    </p>
                    <p class="text-[10px] font-bold text-orange-500 uppercase mt-2">Atividade</p>
                </div>
                <i
                    class="bi bi-clock-history absolute -right-4 -bottom-4 text-8xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
            </div>
        </div>

        <!-- content Bottom: History & Insights -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
            <!-- Left: History Table (Full Width in its column) -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-10 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tighter">Histórico de Lançamentos</h2>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Últimas 10
                                atividades registradas</p>
                        </div>
                        <a href="{{ route('contributions.index', ['user_id' => $user->id]) }}"
                            class="px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Ver
                            Tudo</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th
                                        class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Data</th>
                                    <th
                                        class="px-10 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Valor</th>
                                    <th
                                        class="px-10 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Estado</th>
                                    <th
                                        class="px-10 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Comprovativo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($user->contributions->take(10) as $contribution)
                                                        <tr class="hover:bg-gray-50/70 transition-colors group">
                                                            <td class="px-10 py-6">
                                                                <p class="text-sm font-bold text-gray-900">
                                                                    {{ $contribution->contribution_date->format('d/m/Y') }}
                                                                </p>
                                                            </td>
                                                            <td class="px-10 py-6">
                                                                <p class="text-sm font-black text-green-600">
                                                                    {{ number_format($contribution->amount, 0, ',', '.') }} MT
                                                                </p>
                                                            </td>
                                                            <td class="px-10 py-6 text-center">
                                                                <span
                                                                    class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ 
                                                                                                                                                                $contribution->status == 'verificada' ? 'bg-green-50 text-green-600' :
                                    ($contribution->status == 'pendente' ? 'bg-yellow-50 text-yellow-600' : 'bg-red-50 text-red-600') 
                                                                                                                                                            }}">
                                                                    {{ $contribution->status }}
                                                                </span>
                                                            </td>
                                                            <td class="px-10 py-6 text-right">
                                                                @if($contribution->proof_path)
                                                                    <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank"
                                                                        class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl inline-flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                                    </a>
                                                                @else
                                                                    <span class="text-gray-300 italic text-xs">Nenhum</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-10 py-20 text-center">
                                            <div class="max-w-xs mx-auto space-y-4">
                                                <div
                                                    class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center mx-auto">
                                                    <i class="bi bi-inbox text-4xl text-gray-200"></i>
                                                </div>
                                                <p class="text-gray-400 font-bold italic">Nenhuma oferta registrada neste perfil
                                                    ainda.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Observations & Extras -->
            <div class="space-y-8">
                <!-- Observations Card -->
                <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 p-10 relative overflow-hidden group">
                    <div class="absolute -right-12 -top-12 w-40 h-40 bg-orange-600/5 rounded-full blur-2xl"></div>
                    <div class="relative z-10 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-black text-gray-900">Observações</h3>
                            <i class="bi bi-chat-dots-fill text-orange-400"></i>
                        </div>
                        @if($user->observations)
                            <div class="p-6 bg-orange-50/50 rounded-3xl border border-orange-100/50">
                                <p class="text-sm font-bold text-gray-700 leading-relaxed italic">
                                    "{{ $user->observations }}"
                                </p>
                            </div>
                        @else
                            <div class="py-6 text-center">
                                <p class="text-gray-300 font-bold uppercase text-[10px] tracking-widest">Sem notas registradas
                                </p>
                            </div>
                        @endif
                        <button
                            onclick="confirmAction('Editar Notas', 'Deseja ir para a página de edição para alterar as observações?', 'info', 'Sim, editar', '{{ route('users.edit', $user) }}')"
                            class="w-full py-4 bg-gray-50 text-gray-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 hover:text-white transition-all">
                            Gerenciar Notas
                        </button>
                    </div>
                </div>

                <!-- Admin Tools Hub -->
                <div class="bg-gray-900 rounded-[3rem] shadow-2xl p-10 space-y-8">
                    <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em]">Centro de Segurança</h3>
                    <div class="space-y-4">
                        <form action="{{ route('users.reset-password', $user) }}" method="POST" id="reset-password-sidebar">
                            @csrf
                            <button type="button"
                                onclick="confirmAction('Redefinir Senha', 'Redefinir senha de {{ $user->name }} para mudar123?', 'question', 'Sim, redefinir', 'reset-password-sidebar')"
                                class="w-full py-5 bg-white/5 border border-white/10 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all flex items-center justify-between px-6">
                                <span>Resetar Senha</span>
                                <i class="bi bi-key-fill text-orange-400"></i>
                            </button>
                        </form>

                        <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full py-5 bg-white/5 border border-white/10 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest {{ $user->is_active ? 'hover:bg-red-600' : 'hover:bg-green-600' }} transition-all flex items-center justify-between px-6">
                                <span>{{ $user->is_active ? 'Inativar Conta' : 'Reativar Conta' }}</span>
                                <i class="bi bi-power {{ $user->is_active ? 'text-red-400' : 'text-green-400' }}"></i>
                            </button>
                        </form>

                        <div class="pt-4 border-t border-white/10">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 text-center">ID do
                                Sistema</p>
                            <p class="text-sm font-mono font-bold text-blue-400 text-center tracking-widest">
                                #{{ str_pad($user->id, 8, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Back Button (Contextual) -->
        <div class="fixed bottom-10 right-10 z-[60]">
            <a href="{{ route('users.index') }}"
                class="flex items-center justify-center w-16 h-16 bg-white text-gray-900 rounded-full shadow-2xl border border-gray-100 hover:bg-gray-900 hover:text-white transition-all transform hover:scale-110 active:scale-90 group">
                <i class="bi bi-arrow-left text-2xl"></i>
                <span
                    class="absolute right-20 bg-gray-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all pointer-events-none whitespace-nowrap">Voltar
                    aos Utilizadores</span>
            </a>
        </div>
    </div>
@endsection