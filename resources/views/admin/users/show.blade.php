@extends('layouts.app')

@section('title', 'Detalhes do Utilizador - Portal Life Church')
@section('page-title', 'Detalhes do Utilizador')
@section('page-subtitle', 'Informações completas do utilizador')

@section('content')
    <div class="space-y-8">
        <!-- Header Profile Card -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10 overflow-hidden relative group">
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-blue-50/30 rounded-full -mr-48 -mt-48 transition-transform group-hover:scale-110 duration-700">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-10">
                <div
                    class="w-32 h-32 rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-black text-5xl shadow-2xl shadow-blue-100 group-hover:rotate-6 transition-transform">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div class="flex-1 space-y-4 text-center md:text-left">
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-4">
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter">{{ $user->name }}</h1>
                        @if($user->is_active)
                            <span
                                class="px-4 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Sistema Ativo
                            </span>
                        @else
                            <span
                                class="px-4 py-1 bg-gray-50 text-gray-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-100">Desativado</span>
                        @endif
                    </div>
                    <p class="text-lg font-bold text-gray-400">{{ $user->email }} •
                        {{ $user->phone ?? 'Sem contato registrado' }}</p>

                    <div class="flex flex-wrap justify-center md:justify-start gap-3">
                        <div
                            class="flex items-center gap-2 px-6 py-3 bg-blue-50 text-blue-600 rounded-2xl text-xs font-black uppercase tracking-widest border border-blue-100">
                            <i class="bi bi-shield-lock-fill"></i> Role: {{ str_replace('_', ' ', $user->role) }}
                        </div>
                        @if($user->cell)
                            <div
                                class="flex items-center gap-2 px-6 py-3 bg-indigo-50 text-indigo-600 rounded-2xl text-xs font-black uppercase tracking-widest border border-indigo-100">
                                <i class="bi bi-geo-alt-fill"></i> Célula: {{ $user->cell->name }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col gap-3 min-w-[240px]">
                    <a href="{{ route('users.edit', $user) }}"
                        class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-100 flex items-center justify-center gap-3 hover:bg-blue-700 transition-all">
                        <i class="bi bi-pencil-square"></i> Editar Perfil
                    </a>
                    @if($user->role !== 'admin')
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="w-full"
                            onsubmit="return confirm('Deletar?');">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full py-4 bg-red-50 text-red-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-100 transition-all">
                                <i class="bi bi-trash-fill mr-2"></i> Eliminar Registro
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Data Detail Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Commitments -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                            <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                                <i class="bi bi-handshake text-green-600"></i>
                                Compromisso Ativo
                            </h2>
                        </div>
                        <div class="p-10">
                            @php $activeCommitment = $user->commitments->whereNull('end_date')->first(); @endphp
                            @if($activeCommitment)
                                <div class="space-y-4">
                                    <div class="p-6 bg-green-50 rounded-3xl border border-green-100">
                                        <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">
                                            {{ $activeCommitment->package->name ?? 'Pacote Selecionado' }}</p>
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                class="text-3xl font-black text-green-700">{{ number_format($activeCommitment->committed_amount, 0, ',', '.') }}</span>
                                            <span class="text-sm font-black text-green-600">MT/MÊS</span>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-center">Início:
                                        {{ $activeCommitment->start_date->format('d/m/Y') }}</p>
                                </div>
                            @else
                                <div class="py-10 text-center space-y-3">
                                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto">
                                        <i class="bi bi-inbox text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-black text-gray-300 uppercase tracking-tighter">Nenhum compromisso
                                        ativo</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                            <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                                <i class="bi bi-graph-up-arrow text-purple-600"></i>
                                Resumo de Ofertas
                            </h2>
                        </div>
                        <div class="p-10 flex flex-col justify-center h-full">
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total
                                        Validado</span>
                                    <span
                                        class="text-2xl font-black text-green-600">{{ number_format($user->contributions->where('status', 'verificada')->sum('amount'), 0, ',', '.') }}<span
                                            class="text-xs ml-1">MT</span></span>
                                </div>
                                <div class="w-full bg-gray-50 h-2 rounded-full overflow-hidden">
                                    <div class="bg-green-500 h-full w-[70%]" style="width: 100%"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Registros
                                        Totais</span>
                                    <span
                                        class="text-xl font-black text-gray-900">{{ $user->contributions->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent History Table -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                            <i class="bi bi-clock-history text-blue-600"></i>
                            Histórico de Lançamentos
                        </h2>
                        <a href="{{ route('contributions.index', ['mine' => 1]) }}"
                            class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Ver
                            Detalhes</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Data</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Valor</th>
                                    <th
                                        class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($user->contributions->take(5) as $contribution)
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="px-8 py-5 text-sm font-bold text-gray-900">
                                            {{ $contribution->contribution_date->format('d/m/Y') }}</td>
                                        <td class="px-8 py-5 text-sm font-black text-green-600">
                                            {{ number_format($contribution->amount, 0, ',', '.') }} MT</td>
                                        <td class="px-8 py-5 text-center">
                                            @if($contribution->status == 'verificada')
                                                <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
                                            @elseif($contribution->status == 'pendente')
                                                <span class="w-2 h-2 bg-yellow-500 rounded-full inline-block animate-pulse"></span>
                                            @else
                                                <span class="w-2 h-2 bg-red-500 rounded-full inline-block"></span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3"
                                            class="px-8 py-10 text-center text-gray-300 font-bold uppercase text-xs tracking-widest">
                                            Nenhum registro recente</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-8">
                <!-- System Intel -->
                <div class="bg-gray-900 text-white rounded-[2.5rem] shadow-xl p-10 space-y-8">
                    <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em]">Inteligência do Sistema</h3>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-xl">
                                <i class="bi bi-fingerprint"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Identificador
                                    Único</p>
                                <p class="text-sm font-mono font-bold text-white">
                                    #{{ str_pad($user->id, 8, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-xl">
                                <i class="bi bi-calendar-plus"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Membro desde</p>
                                <p class="text-sm font-bold text-white">{{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-xl">
                                <i class="bi bi-box-arrow-in-right"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Último Acesso</p>
                                <p class="text-sm font-bold text-white">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Sem registros' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Hub -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-6">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Hub de Ações Rápidas</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('contributions.create', ['user_id' => $user->id]) }}"
                            class="w-full py-4 bg-gray-50 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-green-600 hover:text-white transition-all flex items-center justify-between px-6">
                            <span>Lançar Oferta</span>
                            <i class="bi bi-plus-lg"></i>
                        </a>
                        <form action="{{ route('users.reset-password', $user) }}" method="POST" class="w-full"
                            onsubmit="return confirm('Tem certeza que deseja redefinir a senha deste utilizador? Um email será enviado com as novas credenciais.');">
                            @csrf
                            <button type="submit"
                                class="w-full py-4 bg-gray-50 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all flex items-center justify-between px-6">
                                <span>Redefinir Senha</span>
                                <i class="bi bi-key-fill"></i>
                            </button>
                        </form>
                        <a href="mailto:{{ $user->email }}"
                            class="w-full py-4 bg-gray-50 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all flex items-center justify-between px-6">
                            <span>Enviar Email</span>
                            <i class="bi bi-envelope-fill"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-6">
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center gap-4 text-gray-400 hover:text-blue-600 transition-colors py-4 px-8 bg-white rounded-2xl shadow-sm border border-gray-50 font-black text-xs uppercase tracking-widest">
                <i class="bi bi-arrow-left"></i> Voltar à Lista Global
            </a>
        </div>
    </div>
    </div>

@endsection