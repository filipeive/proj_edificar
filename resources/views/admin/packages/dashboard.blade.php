@extends('layouts.app')

@section('title', 'Painel de Gestão de Pacotes')
@section('page-title', 'Gestão de Pacotes')
@section('page-subtitle', 'Visão consolidada dos seus pacotes atribuídos')

@section('header-actions')
    <div class="flex items-center gap-3">
        @if($packages->isNotEmpty())
            <a href="{{ route('contributions.create') }}?package_id={{ $packages->first()->id }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all font-bold text-sm flex items-center gap-2 shadow-lg shadow-blue-100">
                <i class="bi bi-plus-circle"></i>
                <span>Registar Contribuição</span>
            </a>
        @endif
        <a href="{{ route('contributions.index') }}" 
           class="bg-white text-gray-700 border border-gray-100 px-6 py-3 rounded-xl hover:bg-gray-50 transition-all font-bold text-sm flex items-center gap-2 shadow-sm">
            <i class="bi bi-clock-history"></i>
            <span>Histórico Completo</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid space-y-6 md:space-y-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="bi bi-people-fill text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">MembrosAtivos</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $stats['total_members'] }}</p>
                <div class="mt-4 flex items-center gap-2 text-xs font-bold text-gray-400">
                    <i class="bi bi-bookmark-check text-blue-500"></i> Compromissos Vigentes
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center">
                        <i class="bi bi-cash-stack text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">ArrecadaçãoTotal</span>
                </div>
                <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ number_format($stats['total_contributions'], 2, ',', '.') }} <small class="text-xs">MT</small></p>
                <div class="mt-4 flex items-center gap-2 text-xs font-bold text-gray-400">
                    <i class="bi bi-calendar-check text-green-500"></i> Histórico Global
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <i class="bi bi-calendar-event text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">MêsAtual</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ number_format($stats['monthly_contributions'], 2, ',', '.') }} <small class="text-xs">MT</small></p>
                <div class="mt-4 flex items-center gap-2 text-xs font-bold text-gray-400">
                    <i class="bi bi-graph-up text-orange-500"></i> Previsto em Crescimento
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                        <i class="bi bi-shield-lock text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pendentes</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $stats['pending_contributions'] }}</p>
                <div class="mt-4 flex items-center gap-2 text-xs font-bold text-gray-400">
                    <i class="bi bi-clock-history text-yellow-500"></i> Aguardando Validação
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <a href="{{ route('contributions.create') }}" class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2rem] border border-gray-100 flex flex-col items-center justify-center gap-2 md:gap-3 hover:shadow-lg transition-all group">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="bi bi-plus-lg text-xl md:text-2xl"></i>
                </div>
                <span class="text-[9px] md:text-[10px] font-black text-gray-900 uppercase tracking-widest">Registrar</span>
            </a>
            
            <a href="{{ route('contributions.index') }}" class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2rem] border border-gray-100 flex flex-col items-center justify-center gap-2 md:gap-3 hover:shadow-lg transition-all group">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-all">
                    <i class="bi bi-list-check text-xl md:text-2xl"></i>
                </div>
                <span class="text-[9px] md:text-[10px] font-black text-gray-900 uppercase tracking-widest">Histórico</span>
            </a>

            <a href="{{ route('members.index') }}" class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2rem] border border-gray-100 flex flex-col items-center justify-center gap-2 md:gap-3 hover:shadow-lg transition-all group">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all">
                    <i class="bi bi-people text-xl md:text-2xl"></i>
                </div>
                <span class="text-[9px] md:text-[10px] font-black text-gray-900 uppercase tracking-widest">Membros</span>
            </a>

            @if($packages->isNotEmpty())
            <a href="{{ route('packages.export', $packages->first()) }}" class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2rem] border border-gray-100 flex flex-col items-center justify-center gap-2 md:gap-3 hover:shadow-lg transition-all group">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all">
                    <i class="bi bi-file-earmark-arrow-down text-xl md:text-2xl"></i>
                </div>
                <span class="text-[9px] md:text-[10px] font-black text-gray-900 uppercase tracking-widest">Exportar</span>
            </a>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Gráfico de Evolução -->
            <div class="lg:col-span-8">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 h-full">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-10 flex items-center gap-2">
                        <i class="bi bi-graph-up text-blue-600"></i>
                        Evolução Financeira (Últimos 6 meses)
                    </h3>
                    <div class="aspect-[16/9] relative">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Meus Pacotes -->
            <div class="lg:col-span-4">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 h-full">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-8">Meus Pacotes Geridos</h3>
                    <div class="space-y-4">
                        @foreach($packages as $package)
                            <div class="group p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-lg transition-all">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-gray-900">{{ $package->name }}</h4>
                                    <span class="px-2 py-0.5 rounded-lg bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-widest">{{ $package->getActiveMembersCount() }} Membros</span>
                                </div>
                                <div class="flex items-center gap-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                    <i class="bi bi-cash"></i> {{ number_format($package->getTotalContributionsThisMonth(), 2, ',', '.') }} MT este mês
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                                    <a href="{{ route('packages.show', $package) }}" class="flex-1 py-2 bg-white rounded-xl text-center text-[10px] font-black uppercase tracking-widest border border-gray-100 hover:bg-blue-600 hover:text-white transition-all shadow-sm">Detalhes</a>
                                    <a href="{{ route('packages.export', $package) }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100 hover:text-green-600 transition-all shadow-sm"><i class="bi bi-file-earmark-excel"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Atividade Recente -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Contribuições Recentes</h3>
                <a href="{{ route('contributions.index') }}" class="text-xs font-black text-blue-600 uppercase tracking-widest hover:underline">Ver Todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Contribuinte</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pacote</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentContributions as $contribution)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <p class="text-sm font-bold text-gray-900">{{ $contribution->contribution_date->format('d/m/Y') }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase">{{ $contribution->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-black text-gray-900 leading-none">{{ $contribution->user->name ?? 'Doador Externo' }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">{{ $contribution->member_name }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-xs font-bold text-gray-600">{{ $contribution->package->name }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm font-black text-blue-600">{{ number_format($contribution->amount, 2, ',', '.') }} MT</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @if($contribution->status === 'verificada')
                                        <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-green-100">Verificada</span>
                                    @elseif($contribution->status === 'pendente')
                                        <span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-yellow-100">Pendente</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-red-100">Rejeitada</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('revenueTrendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($revenueTrend->pluck('month')) !!},
                        datasets: [{
                            label: 'Arrecadação Mensal',
                            data: {!! json_encode($revenueTrend->pluck('total')) !!},
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#2563eb',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { borderDash: [5, 5], color: '#f3f4f6' },
                                ticks: { font: { weight: 'bold', size: 10 } }
                            },
                            x: { 
                                grid: { display: false },
                                ticks: { font: { weight: 'bold', size: 10 } }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
    </div>
    </div>
@endsection
