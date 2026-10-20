@extends('layouts.app')

@section('title', 'Painel de Gestão de Pacotes')
@section('page-title', 'Gestão de Pacotes')
@section('page-subtitle', 'Visão consolidada dos seus pacotes atribuídos')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        @if($packages->isNotEmpty())
            <a href="{{ route('contributions.create') }}?package_id={{ $packages->first()->id }}" 
               class="inline-flex items-center justify-center rounded-lg bg-orange-700 p-2 text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-plus-circle text-base"></i>
            </a>
        @endif
        <a href="{{ route('contributions.index') }}" 
           class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <i class="bi bi-clock-history text-base"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Projecto Edificar</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Painel de Gestão de Pacotes</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Visão consolidada dos pacotes atribuídos, compromissos e contribuições.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('packages.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-people text-orange-700"></i> <span>Gerir Pacotes</span>
                </a>
                <a href="{{ route('contributions.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-list-check text-emerald-700"></i> <span>Histórico</span>
                </a>
                @if($packages->isNotEmpty())
                    <a href="{{ route('packages.export', $packages->first()) }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-green-200 bg-white px-3.5 py-2 text-xs font-bold text-green-800 hover:bg-green-50 shadow-sm">
                        <i class="bi bi-file-earmark-arrow-down"></i> <span>Exportar</span>
                    </a>
                @endif
                <a href="{{ route('contributions.create') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i> <span>Registar</span>
                </a>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Membros Ativos</p>
                    <i class="bi bi-people text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $stats['total_members'] }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Compromissos vigentes</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Arrecadação Total</p>
                    <i class="bi bi-cash-stack text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">{{ number_format($stats['total_contributions'], 2, ',', '.') }} MT</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Histórico global</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Mês Atual</p>
                    <i class="bi bi-calendar-event text-orange-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ number_format($stats['monthly_contributions'], 2, ',', '.') }} MT</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Arrecadado no mês</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Pendentes</p>
                    <i class="bi bi-shield-lock text-amber-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-amber-700 tracking-tight tabular-nums">{{ $stats['pending_contributions'] }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Aguardando validação</p>
            </article>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Gráfico de Evolução -->
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900">Evolução Financeira (Últimos 6 meses)</h3>
                    <p class="text-xs text-gray-500">Tendência mensal de arrecadação</p>
                </div>
                <div class="h-64 relative">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </section>

            <!-- Meus Pacotes -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900">Meus Pacotes Geridos</h3>
                    <p class="text-xs text-gray-500">Pacotes sob sua responsabilidade</p>
                </div>
                <div class="space-y-3">
                    @foreach($packages as $package)
                        <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200/80">
                            <div class="flex justify-between items-start gap-2 mb-1.5">
                                <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $package->name }}</h4>
                                <span class="shrink-0 px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 text-[10px] font-black uppercase">
                                    {{ $package->getActiveMembersCount() }} Membros
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-gray-600 font-semibold tabular-nums">
                                <i class="bi bi-cash text-emerald-700"></i> {{ number_format($package->getTotalContributionsThisMonth(), 2, ',', '.') }} MT este mês
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-gray-200 flex gap-2">
                                <a href="{{ route('packages.show', $package) }}"
                                    class="flex-1 rounded-lg border border-gray-300 bg-white py-1.5 text-center text-xs font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                                    Detalhes
                                </a>
                                <a href="{{ route('packages.export', $package) }}" title="Exportar"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-bold text-green-700 hover:bg-green-50 shadow-sm">
                                    <i class="bi bi-file-earmark-excel"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Atividade Recente -->
        <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Contribuições Recentes</h3>
                    <p class="text-xs text-gray-500">Últimos lançamentos nos pacotes geridos</p>
                </div>
                <a href="{{ route('contributions.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">Data</th>
                            <th class="px-4 py-3">Contribuinte</th>
                            <th class="px-4 py-3">Pacote</th>
                            <th class="px-4 py-3 text-right">Valor</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($recentContributions as $contribution)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3">
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 tabular-nums">{{ $contribution->contribution_date->format('d/m/Y') }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $contribution->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs sm:text-sm font-bold text-gray-900">{{ $contribution->user->name ?? 'Doador Externo' }}</p>
                                    <p class="text-[11px] text-gray-500 truncate">{{ $contribution->member_name }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-semibold text-gray-600">{{ $contribution->package->name }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-xs sm:text-sm font-black text-gray-950 tabular-nums">{{ number_format($contribution->amount, 2, ',', '.') }} MT</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($contribution->status === 'verificada')
                                        <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[10px] font-black uppercase">Verificada</span>
                                    @elseif($contribution->status === 'pendente')
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-black uppercase">Pendente</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-[10px] font-black uppercase">Rejeitada</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @push('scripts')
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
@endsection
