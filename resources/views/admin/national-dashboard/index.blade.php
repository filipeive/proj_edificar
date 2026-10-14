@extends('layouts.app')

@section('title', 'Painel Nacional')
@section('page-title', 'Painel Nacional')
@section('page-subtitle', 'Visão eclesiástica consolidada de todas as congregações')

@section('content')
    <div class="space-y-6">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gabinete do Super Admin</p>
                <h1 class="mt-0.5 sm:mt-1 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Panorama das congregações</h1>
                <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-gray-500">Indicadores do mês corrente para congregações ativas.</p>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 w-full sm:w-auto">
                <a href="{{ route('national.reports.index') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-file-earmark-bar-graph"></i> <span>Relatório nacional</span>
                </a>
                <a href="{{ route('national.congregations.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i> <span>Registar congregação</span>
                </a>
            </div>
        </header>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-green-800">{{ session('success') }}</div>
        @endif

        <section class="space-y-4" aria-label="Indicadores nacionais">
            <!-- Estrutura Eclesiástica -->
            <div>
                <h3 class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-gray-500 mb-2">Estrutura Eclesiástica</h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Congregações</p>
                            <i class="bi bi-buildings text-orange-700 text-sm sm:text-base"></i>
                        </div>
                        <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight">{{ $stats['active_congregations'] }} <span class="text-xs font-semibold text-gray-400">/ {{ $stats['congregations'] }}</span></p>
                        <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Igrejas ativas</p>
                    </article>

                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Membros Ativos</p>
                            <i class="bi bi-people text-blue-700 text-sm sm:text-base"></i>
                        </div>
                        <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight">{{ number_format($stats['members']) }}</p>
                        <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Membros registados</p>
                    </article>

                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Células e Zonas</p>
                            <i class="bi bi-diagram-3 text-indigo-700 text-sm sm:text-base"></i>
                        </div>
                        <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight">{{ number_format($stats['cells']) }}</p>
                        <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Em {{ number_format($stats['zones']) }} zonas</p>
                    </article>

                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Cultos do Mês</p>
                            <i class="bi bi-calendar2-week text-amber-700 text-sm sm:text-base"></i>
                        </div>
                        <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight">{{ number_format($stats['services_this_month']) }}</p>
                        <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Celebrações registadas</p>
                    </article>
                </div>
            </div>

            <!-- Finanças Eclesiásticas da Igreja -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-2">
                    <h3 class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-gray-500">Finanças Eclesiásticas da Igreja</h3>
                    <span class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest"><i class="bi bi-shield-check text-green-600 mr-1"></i>Exclui fundos de obras / Edificar</span>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total Entradas</p>
                            <i class="bi bi-cash-coin text-emerald-700 text-sm sm:text-base"></i>
                        </div>
                        <p class="mt-1 text-base sm:text-xl font-black text-emerald-700 tracking-tight">{{ number_format($stats['total_income_this_month'], 0, ',', '.') }} MT</p>
                        <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Dízimos e cultos</p>
                    </article>

                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Despesas</p>
                            <i class="bi bi-arrow-up-right-circle text-rose-700 text-sm sm:text-base"></i>
                        </div>
                        <p class="mt-1 text-base sm:text-xl font-black text-rose-700 tracking-tight">{{ number_format($stats['expenses_this_month'], 0, ',', '.') }} MT</p>
                        <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Custos operacionais</p>
                    </article>

                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Saldo Líquido</p>
                            <i class="bi bi-wallet2 text-teal-700 text-sm sm:text-base"></i>
                        </div>
                        <p class="mt-1 text-base sm:text-xl font-black tracking-tight {{ $stats['net_balance_this_month'] >= 0 ? 'text-teal-700' : 'text-red-700' }}">
                            {{ number_format($stats['net_balance_this_month'], 0, ',', '.') }} MT
                        </p>
                        <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Balanço do mês</p>
                    </article>

                    <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Pendentes</p>
                            <i class="bi bi-hourglass-split text-amber-700 text-sm sm:text-base"></i>
                        </div>
                        <p class="mt-1 text-base sm:text-xl font-black text-amber-700 tracking-tight">{{ number_format($stats['pending_contributions']) }}</p>
                        <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Aguardam validação</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-black text-gray-900">Mapa nacional das congregações</h2>
                    <p class="text-xs text-gray-500">Localização administrativa de cada congregação. Selecione um marcador para detalhes.</p>
                </div>
                <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-green-700"></span>Ativa</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-gray-500"></span>Inativa</span>
                </div>
            </div>
            <div id="national-congregation-map" data-national-congregation-map data-map-data="national-congregation-map-data"
                class="w-full h-[420px] rounded-lg border border-gray-200 overflow-hidden bg-gray-100"
                aria-label="Mapa de localização das congregações nacionais"></div>
            <script id="national-congregation-map-data" type="application/json">@json(['congregations' => $mapCongregations])</script>
            @if($mapCongregations->isEmpty())
                <p class="mt-3 text-xs text-gray-500">Ainda não há congregações com coordenadas registadas. Marque a sede no formulário de edição de cada congregação.</p>
            @endif
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <article class="xl:col-span-2 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Finanças Eclesiásticas da Igreja Nacional</h2>
                        <p class="text-xs text-gray-500">Evolução mensal de Entradas e Despesas eclesiásticas nos últimos 6 meses</p>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">MT</span>
                </div>
                <div class="relative h-64"><canvas id="nationalContributionsChart" aria-label="Evolução financeira da igreja nacional"></canvas></div>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Estado das congregações</h2>
                        <p class="text-xs text-gray-500">Ativas e inativas</p>
                    </div>
                    <a href="{{ route('national.congregations.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Gerir todas</a>
                </div>
                <div class="space-y-3">
                    @forelse($congregations->take(6) as $congregation)
                        <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3 last:border-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-gray-900">{{ $congregation->name }}</p>
                                <p class="text-[11px] text-gray-500">{{ $congregation->users_count }} membros/utilizadores · {{ $congregation->zones_count }} zonas</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-black {{ $congregation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $congregation->is_active ? 'ATIVA' : 'INATIVA' }}
                            </span>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500">Ainda não há congregações.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-gray-200 p-5">
                <div>
                    <h2 class="text-sm font-black text-gray-900">Diretório nacional</h2>
                    <p class="text-xs text-gray-500">Estrutura e estado de cada congregação</p>
                </div>
                <form method="GET" action="{{ route('national.dashboard') }}" class="flex flex-wrap gap-2">
                    <input name="search" value="{{ request('search') }}" placeholder="Pesquisar congregação" class="rounded-lg border-gray-300 text-sm">
                    <select name="status" class="rounded-lg border-gray-300 text-sm">
                        <option value="">Todos os estados</option>
                        <option value="active" @selected(request('status') === 'active')>Ativas</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inativas</option>
                    </select>
                    <button class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-bold text-white">Filtrar</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500">
                        <tr><th class="px-5 py-3">Congregação</th><th class="px-5 py-3">Domínio</th><th class="px-5 py-3 text-right">Membros/Users</th><th class="px-5 py-3 text-right">Zonas</th><th class="px-5 py-3 text-right">Células</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3 text-right">Ação</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($congregations as $congregation)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <a href="{{ route('national.congregations.show', $congregation) }}" class="font-bold text-gray-900 hover:text-orange-800 hover:underline">
                                            {{ $congregation->name }}
                                        </a>
                                        @if($congregation->is_primary)
                                            <span class="rounded bg-orange-100 px-1.5 py-0.5 text-[9px] font-black uppercase text-orange-800">Sede Principal</span>
                                        @endif
                                        @if($congregation->has_edificar_project)
                                            <span class="rounded bg-sky-100 px-1.5 py-0.5 text-[9px] font-black uppercase text-sky-800">Projeto Edificar</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $congregation->slug }}</p>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">{{ $congregation->domain ?? ($congregation->subdomain ? $congregation->subdomain . '.*' : 'Domínio principal') }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ $congregation->users_count }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ $congregation->zones_count }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ $congregation->cells_count ?? 0 }}</td>
                                <td class="px-5 py-4"><span class="rounded-full px-2 py-1 text-[10px] font-black {{ $congregation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $congregation->is_active ? 'Ativa' : 'Inativa' }}</span></td>
                                <td class="px-5 py-4 text-right whitespace-nowrap space-x-3">
                                    <a href="{{ route('national.congregations.show', $congregation) }}" class="inline-flex items-center gap-1 text-xs font-bold text-orange-800 hover:underline"><i class="bi bi-eye"></i> Detalhes</a>
                                    <a href="{{ route('national.congregations.edit', $congregation) }}" class="inline-flex items-center gap-1 text-xs font-bold text-gray-600 hover:text-gray-900 hover:underline"><i class="bi bi-pencil-square"></i> Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-gray-500">Nenhuma congregação corresponde aos filtros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 p-4">{{ $congregations->links() }}</div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('nationalContributionsChart');
        if (!canvas || !window.Chart) return;
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: @json($nationalMonthly['labels']),
                datasets: [
                    {
                        label: 'Entradas da Igreja (MT)',
                        data: @json($nationalMonthly['income'] ?? $nationalMonthly['values']),
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.08)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        borderWidth: 2
                    },
                    {
                        label: 'Despesas Eclesiásticas (MT)',
                        data: @json($nationalMonthly['expenses'] ?? []),
                        borderColor: '#e11d48',
                        backgroundColor: 'rgba(225, 29, 72, 0.05)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + new Intl.NumberFormat('pt-MZ').format(context.parsed.y) + ' MT';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => new Intl.NumberFormat('pt-MZ').format(value)
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush
