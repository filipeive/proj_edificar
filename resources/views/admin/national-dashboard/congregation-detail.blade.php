@extends('layouts.app')

@section('title', 'Detalhes da congregação - ' . $congregation->name)
@section('page-title', $congregation->name)
@section('page-subtitle', 'Gestão eclesiástica, cultos, finanças e administração local')

@section('content')
    <div class="space-y-6" x-data="{ activeTab: 'cultos_financas', addAdminOpen: false, resetModalUser: null }">
        <!-- Top Navigation & Actions -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-gray-500 mb-1">
                    <a href="{{ route('national.dashboard') }}" class="hover:text-orange-800 hover:underline"><i class="bi bi-buildings mr-1"></i>Painel nacional</a>
                    <span>/</span>
                    <a href="{{ route('national.reports.index') }}" class="hover:text-orange-800 hover:underline">Relatórios</a>
                    <span>/</span>
                    <span class="text-gray-900 font-extrabold">{{ $congregation->name }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-black text-gray-950">{{ $congregation->name }}</h1>
                    @if($congregation->is_primary)
                        <span class="rounded bg-orange-100 px-2 py-0.5 text-[10px] font-black uppercase text-orange-800">Sede Principal</span>
                    @endif
                    @if($congregation->has_edificar_project)
                        <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] font-black uppercase text-sky-800">Projeto Edificar</span>
                    @else
                        <span class="rounded bg-teal-100 px-2 py-0.5 text-[10px] font-black uppercase text-teal-800">Eclesiástico</span>
                    @endif
                    <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black {{ $congregation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $congregation->is_active ? 'ATIVA' : 'INATIVA' }}
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('national.reports.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    <i class="bi bi-arrow-left"></i> Voltar ao relatório
                </a>
                <a href="{{ route('national.congregations.edit', $congregation) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm transition">
                    <i class="bi bi-pencil-square"></i> Editar congregação
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
        @endif

        <!-- Filtro de Período -->
        <form method="GET" action="{{ route('national.congregations.show', $congregation) }}" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div>
                <label for="start_date" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data inicial</label>
                <input id="start_date" name="start_date" type="date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label for="end_date" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data final</label>
                <input id="end_date" name="end_date" type="date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="rounded-lg border-gray-300 text-sm">
            </div>
            <button class="rounded-lg bg-gray-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-gray-800 transition">Aplicar período</button>
            <a href="{{ route('national.congregations.show', $congregation) }}" class="px-3 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900">Mês corrente</a>
        </form>

        <!-- KPI Cards Grid -->
        <section class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-2.5 sm:gap-3" aria-label="Indicadores da congregação">
            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between text-gray-500 mb-1.5 sm:mb-2">
                    <span class="text-[10px] sm:text-xs font-bold">Membros Ativos</span>
                    <i class="bi bi-people text-blue-700 text-base sm:text-lg"></i>
                </div>
                <p class="text-lg sm:text-2xl font-black text-gray-950">{{ number_format($activeMembersCount) }}</p>
                <p class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 sm:mt-1 truncate">Total utilizadores: {{ $totalUsersCount }}</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between text-gray-500 mb-1.5 sm:mb-2">
                    <span class="text-[10px] sm:text-xs font-bold">Células e Zonas</span>
                    <i class="bi bi-diagram-3 text-indigo-700 text-base sm:text-lg"></i>
                </div>
                <p class="text-lg sm:text-2xl font-black text-gray-950">{{ number_format($totalCellsCount) }}</p>
                <p class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 sm:mt-1 truncate">Em {{ $zones->count() }} zonas ativas</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between text-gray-500 mb-1.5 sm:mb-2">
                    <span class="text-[10px] sm:text-xs font-bold">Cultos Realizados</span>
                    <i class="bi bi-calendar2-week text-amber-700 text-base sm:text-lg"></i>
                </div>
                <p class="text-lg sm:text-2xl font-black text-gray-950">{{ $cultosMetrics['total_services'] }}</p>
                <p class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 sm:mt-1 truncate">Presença: {{ number_format($cultosMetrics['total_members_attendance']) }}</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between text-gray-500 mb-1.5 sm:mb-2">
                    <span class="text-[10px] sm:text-xs font-bold">Visitantes & Decisões</span>
                    <i class="bi bi-person-heart text-purple-700 text-base sm:text-lg"></i>
                </div>
                <p class="text-lg sm:text-2xl font-black text-gray-950">{{ number_format($cultosMetrics['total_visitors_attendance']) }}</p>
                <p class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 sm:mt-1 truncate">{{ number_format($cultosMetrics['total_salvations']) }} conversões/salvações</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between text-gray-500 mb-1.5 sm:mb-2">
                    <span class="text-[10px] sm:text-xs font-bold">Total Entradas</span>
                    <i class="bi bi-cash-coin text-emerald-700 text-base sm:text-lg"></i>
                </div>
                <p class="text-base sm:text-xl font-black text-emerald-700">{{ number_format($totalIncome, 2, ',', '.') }} MT</p>
                <p class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 sm:mt-1 truncate">Cultos: {{ number_format($cultosMetrics['service_total_financial'], 0, ',', '.') }} · Contrib.: {{ number_format($verifiedContributionsTotal, 0, ',', '.') }}</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between text-gray-500 mb-1.5 sm:mb-2">
                    <span class="text-[10px] sm:text-xs font-bold">Saldo Líquido</span>
                    <i class="bi bi-wallet2 text-teal-700 text-base sm:text-lg"></i>
                </div>
                <p class="text-base sm:text-xl font-black {{ $netBalance >= 0 ? 'text-teal-700' : 'text-rose-700' }}">{{ number_format($netBalance, 2, ',', '.') }} MT</p>
                <p class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 sm:mt-1 truncate">Despesas: {{ number_format($totalExpenses, 0, ',', '.') }} MT</p>
            </article>
        </section>

        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 bg-white rounded-t-xl px-3 sm:px-4 pt-2 shadow-sm">
            <nav class="flex space-x-3 sm:space-x-6 overflow-x-auto text-xs sm:text-sm font-bold no-scrollbar">
                <button type="button" @click="activeTab = 'cultos_financas'"
                    :class="activeTab === 'cultos_financas' ? 'border-orange-700 text-orange-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap pb-2.5 sm:pb-3.5 pt-2 border-b-2 font-black transition flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
                    <i class="bi bi-cash-stack"></i> Finanças & Dízimos/Ofertas de Culto
                </button>
                <button type="button" @click="activeTab = 'cultos_presenca'"
                    :class="activeTab === 'cultos_presenca' ? 'border-orange-700 text-orange-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap pb-2.5 sm:pb-3.5 pt-2 border-b-2 font-black transition flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
                    <i class="bi bi-people-fill"></i> Cultos, Presença & Visitantes
                </button>
                <button type="button" @click="activeTab = 'estrutura'"
                    :class="activeTab === 'estrutura' ? 'border-orange-700 text-orange-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap pb-2.5 sm:pb-3.5 pt-2 border-b-2 font-black transition flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
                    <i class="bi bi-diagram-3-fill"></i> Zonas, Células & Mapa
                </button>
                <button type="button" @click="activeTab = 'admins'"
                    :class="activeTab === 'admins' ? 'border-orange-700 text-orange-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap pb-2.5 sm:pb-3.5 pt-2 border-b-2 font-black transition flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
                    <i class="bi bi-shield-lock-fill"></i> Administradores Locais ({{ $admins->count() }})
                </button>
            </nav>
        </div>

        <!-- TAB 1: FINANÇAS & DÍZIMOS/OFERTAS -->
        <div x-show="activeTab === 'cultos_financas'" class="space-y-6">
            <!-- Arrecadação de Cultos Breakdown -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-sm font-black text-gray-950 flex items-center gap-2">
                            <i class="bi bi-coin text-amber-600"></i> Dízimos e Ofertas Arrecadados nos Cultos
                        </h2>
                        <p class="text-xs text-gray-500">Valores colhidos e registados diretamente durante as celebrações eclesiásticas.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('national.congregations.services.report-pdf', [$congregation, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition"
                            title="Baixar Relatório de Cultos (PDF)">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                            <span>Relatório Cultos (PDF)</span>
                        </a>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-800">
                            Total: {{ number_format($cultosMetrics['service_total_financial'], 2, ',', '.') }} MT
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">
                        <span class="text-[11px] font-bold text-gray-500">Dízimos em Culto</span>
                        <p class="text-lg font-black text-gray-900 mt-1">{{ number_format($cultosMetrics['service_tithes'], 2, ',', '.') }} MT</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">
                        <span class="text-[11px] font-bold text-gray-500">Ofertas Gerais & Especiais</span>
                        <p class="text-lg font-black text-gray-900 mt-1">{{ number_format($cultosMetrics['service_offerings'], 2, ',', '.') }} MT</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 border border-amber-100 p-3">
                        <span class="text-[11px] font-bold text-amber-800">Total Arrecadado em Cultos</span>
                        <p class="text-lg font-black text-amber-900 mt-1">{{ number_format($cultosMetrics['service_total_financial'], 2, ',', '.') }} MT</p>
                    </div>
                </div>

                <!-- Tabela de cultos com financeiro -->
                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Tipo de Culto</th>
                                <th class="px-4 py-3">Pregador</th>
                                <th class="px-4 py-3 text-right">Dízimos (MT)</th>
                                <th class="px-4 py-3 text-right">Ofertas (MT)</th>
                                <th class="px-4 py-3 text-right">Total Culto (MT)</th>
                                <th class="px-4 py-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($services as $service)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-3 font-bold text-gray-900 whitespace-nowrap">{{ $service->date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-gray-700 capitalize">{{ $service->service_type }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $service->preacher_name ?: ($service->preacher?->name ?? '—') }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums font-semibold">{{ number_format($service->total_tithes, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums font-semibold">{{ number_format($service->total_offerings, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums font-black text-emerald-700">{{ number_format($service->total_financial, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('national.congregations.services.pdf', [$congregation, $service]) }}"
                                            title="Baixar PDF deste Culto"
                                            class="inline-flex items-center gap-1 rounded bg-gray-100 hover:bg-orange-50 hover:text-orange-700 px-2 py-1 text-[11px] font-bold text-gray-700 transition">
                                            <i class="bi bi-file-earmark-pdf text-orange-600"></i>
                                            <span>PDF</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Nenhum culto com registo financeiro encontrado neste período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Contribuições da Igreja & Despesas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Contribuições Eclesiásticas Gerais -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h2 class="text-sm font-black text-gray-950 flex items-center gap-2">
                                <i class="bi bi-wallet text-emerald-700"></i> Contribuições da Igreja
                            </h2>
                            <p class="text-xs text-gray-500">Dízimos e ofertas gerais de membros (exclui obras/Edificar).</p>
                        </div>
                        <span class="text-xs font-black text-emerald-700">{{ number_format($verifiedContributionsTotal, 2, ',', '.') }} MT</span>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-3 py-2.5">Data</th>
                                    <th class="px-3 py-2.5">Membro</th>
                                    <th class="px-3 py-2.5">Tipo</th>
                                    <th class="px-3 py-2.5 text-right">Valor (MT)</th>
                                    <th class="px-3 py-2.5">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentContributions as $c)
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $c->contribution_date->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2 font-bold text-gray-900 truncate max-w-[120px]">{{ $c->user?->name ?? 'Membro' }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $c->offeringType?->name ?? 'Contribuição' }}</td>
                                        <td class="px-3 py-2 text-right font-bold text-emerald-700 tabular-nums">{{ number_format($c->amount, 2, ',', '.') }}</td>
                                        <td class="px-3 py-2">
                                            <span class="rounded px-1.5 py-0.5 text-[9px] font-black uppercase {{ $c->status === 'verificada' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $c->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">Nenhuma contribuição geral registada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Despesas Locais da Igreja -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h2 class="text-sm font-black text-gray-950 flex items-center gap-2">
                                <i class="bi bi-arrow-up-right-circle text-rose-600"></i> Despesas Eclesiásticas
                            </h2>
                            <p class="text-xs text-gray-500">Gastos operacionais eclesiásticos da congregação.</p>
                        </div>
                        <span class="text-xs font-black text-rose-600">{{ number_format($totalExpenses, 2, ',', '.') }} MT</span>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-3 py-2.5">Data</th>
                                    <th class="px-3 py-2.5">Descrição</th>
                                    <th class="px-3 py-2.5">Categoria</th>
                                    <th class="px-3 py-2.5 text-right">Valor (MT)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentExpenses as $exp)
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $exp->date->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2 font-bold text-gray-900 truncate max-w-[150px]">{{ $exp->description }}</td>
                                        <td class="px-3 py-2 text-gray-600 capitalize">{{ $exp->category ?: 'Operacional' }}</td>
                                        <td class="px-3 py-2 text-right font-black text-rose-600 tabular-nums">{{ number_format($exp->amount, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-6 text-center text-gray-500">Nenhuma despesa registada no período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: CULTOS, PRESENÇA & VISITANTES -->
        <div x-show="activeTab === 'cultos_presenca'" class="space-y-6">
            <!-- Tabela de Cultos Realizados com Presença -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-sm font-black text-gray-950 flex items-center gap-2">
                            <i class="bi bi-calendar2-check text-orange-700"></i> Cultos Realizados e Presença
                        </h2>
                        <p class="text-xs text-gray-500">Métricas de frequência e decisões nos cultos desta congregação.</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('national.congregations.services.report-pdf', [$congregation, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition"
                            title="Baixar Relatório Geral de Cultos do Período em PDF">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                            <span>Baixar Relatório de Cultos (PDF)</span>
                        </a>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">{{ $services->count() }} celebrações</span>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Tema / Pregador</th>
                                <th class="px-4 py-3 text-right">Adultos</th>
                                <th class="px-4 py-3 text-right">Crianças</th>
                                <th class="px-4 py-3 text-right">Total Membros</th>
                                <th class="px-4 py-3 text-right">Visitantes</th>
                                <th class="px-4 py-3 text-right">Salvações</th>
                                <th class="px-4 py-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($services as $s)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-3 font-bold text-gray-900 whitespace-nowrap">{{ $s->date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-gray-700 capitalize">{{ $s->service_type }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        <span class="font-bold text-gray-900">{{ $s->theme ?: 'Sem tema' }}</span>
                                        <span class="block text-[11px] text-gray-400">{{ $s->preacher_name ?: ($s->preacher?->name ?? '—') }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($s->adults_members ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($s->children_members ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right font-black tabular-nums text-gray-900">{{ number_format($s->total_members) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums font-bold text-purple-700">{{ number_format($s->total_visitors) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums font-bold text-green-700">{{ number_format(($s->adults_salvations ?? 0) + ($s->children_salvations ?? 0)) }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('national.congregations.services.pdf', [$congregation, $s]) }}"
                                            title="Baixar PDF deste Culto"
                                            class="inline-flex items-center gap-1 rounded bg-gray-100 hover:bg-orange-50 hover:text-orange-700 px-2 py-1 text-[11px] font-bold text-gray-700 transition">
                                            <i class="bi bi-file-earmark-pdf text-orange-600"></i>
                                            <span>PDF</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">Nenhum culto registado no período selecionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabela de Visitantes Registados -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-sm font-black text-gray-950 flex items-center gap-2">
                            <i class="bi bi-person-lines-fill text-purple-700"></i> Visitantes Registados
                        </h2>
                        <p class="text-xs text-gray-500">Visitantes que frequentaram os cultos ou células e foram cadastrados.</p>
                    </div>
                    <span class="text-xs font-bold text-gray-500">{{ $visitors->count() }} registos</span>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Nome</th>
                                <th class="px-4 py-3">Telefone</th>
                                <th class="px-4 py-3">Bairro / Cidade</th>
                                <th class="px-4 py-3">Data Visita</th>
                                <th class="px-4 py-3">Convidado Por</th>
                                <th class="px-4 py-3">Estado de Contacto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($visitors as $v)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-3 font-bold text-gray-900">{{ $v->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $v->phone ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $v->neighborhood ? $v->neighborhood . ($v->city ? ', ' . $v->city : '') : '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $v->visit_date ? $v->visit_date->format('d/m/Y') : '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $v->inviter_name ?: '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded px-2 py-0.5 text-[10px] font-bold {{ $v->contact_status === 'contactado' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $v->contact_status ?: 'pendente' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhum visitante registado no período selecionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: ESTRUTURA, ZONAS & MAPA -->
        <div x-show="activeTab === 'estrutura'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <!-- Zonas e Células -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h2 class="text-sm font-black text-gray-950 flex items-center gap-2">
                                <i class="bi bi-diagram-3 text-teal-700"></i> Zonas e Células
                            </h2>
                            <p class="text-xs text-gray-500">Distribuição territorial e células desta congregação.</p>
                        </div>
                        <span class="rounded-full bg-teal-50 px-2.5 py-1 text-xs font-black text-teal-800">
                            {{ $zones->count() }} zonas
                        </span>
                    </div>

                    <div class="space-y-2">
                        @forelse($zones as $z)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $z->name }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $z->cells_count }} células associadas</p>
                                </div>
                                <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-black text-indigo-800">
                                    {{ $z->cells_count }} {{ Str::plural('célula', $z->cells_count) }}
                                </span>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-gray-500">Nenhuma zona cadastrada nesta congregação.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Localização & Mapa da Sede -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h2 class="text-sm font-black text-gray-950 flex items-center gap-2">
                                <i class="bi bi-geo-alt text-orange-700"></i> Localização da Sede
                            </h2>
                            <p class="text-xs text-gray-500">Ponto geográfico da congregação no mapa de Moçambique.</p>
                        </div>
                        @if($congregation->latitude && $congregation->longitude)
                            <span class="text-xs font-mono font-bold text-gray-600">{{ number_format($congregation->latitude, 4) }}, {{ number_format($congregation->longitude, 4) }}</span>
                        @endif
                    </div>

                    @if($congregation->latitude && $congregation->longitude)
                        <div class="overflow-hidden rounded-lg border border-gray-200">
                            <x-location-map id="congregation-preview-map" label-text="Localização da sede" help-text="Ponto geográfico da congregação." :latitude="$congregation->latitude" :longitude="$congregation->longitude" />
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center">
                            <i class="bi bi-geo text-gray-400 text-3xl"></i>
                            <p class="mt-2 text-sm font-bold text-gray-700">Coordenadas geográficas ainda não definidas</p>
                            <p class="text-xs text-gray-500 mt-1">Marque a localização da congregação no formulário de edição para visualizar no mapa nacional.</p>
                            <a href="{{ route('national.congregations.edit', $congregation) }}" class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-orange-800">
                                <i class="bi bi-pin-map"></i> Definir localização agora
                            </a>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs border-t border-gray-100">
                        <div>
                            <span class="text-gray-500 font-medium">Slug único:</span>
                            <span class="font-bold text-gray-900 block font-mono">{{ $congregation->slug }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Domínio / Subdomínio:</span>
                            <span class="font-bold text-gray-900 block">{{ $congregation->domain ?: ($congregation->subdomain ? $congregation->subdomain . '.*' : 'Domínio principal') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: ADMINISTRADORES LOCAIS -->
        <div x-show="activeTab === 'admins'" class="space-y-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-base font-black text-gray-950 flex items-center gap-2">
                            <i class="bi bi-shield-lock text-orange-700"></i> Administradores e Pastores da Congregação
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Gestores com permissões de administração local para esta igreja. Não possuem acesso nacional.</p>
                    </div>
                    <button type="button" @click="addAdminOpen = !addAdminOpen" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 transition">
                        <i class="bi bi-person-plus-fill"></i> Registar novo administrador
                    </button>
                </div>

                <!-- Formulário recolhível para adicionar administrador -->
                <div x-show="addAdminOpen" x-cloak class="rounded-xl border border-orange-200 bg-orange-50/50 p-4 transition">
                    <h3 class="text-xs font-black uppercase tracking-wider text-orange-950 mb-3">Registar Administrador Local</h3>
                    <form method="POST" action="{{ route('national.congregations.admins.store', $congregation) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="new_admin_name" class="mb-1 block text-xs font-bold text-gray-700">Nome completo</label>
                                <input id="new_admin_name" name="admin_name" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label for="new_admin_email" class="mb-1 block text-xs font-bold text-gray-700">Email institucional</label>
                                <input id="new_admin_email" type="email" name="admin_email" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label for="new_admin_password" class="mb-1 block text-xs font-bold text-gray-700">Palavra-passe (mín. 10 carateres)</label>
                                <input id="new_admin_password" type="password" name="admin_password" minlength="10" required class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label for="new_admin_password_confirmation" class="mb-1 block text-xs font-bold text-gray-700">Confirmar palavra-passe</label>
                                <input id="new_admin_password_confirmation" type="password" name="admin_password_confirmation" minlength="10" required class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="addAdminOpen = false" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                            <button type="submit" class="rounded-lg bg-orange-700 px-4 py-1.5 text-xs font-bold text-white hover:bg-orange-800">Criar administrador</button>
                        </div>
                    </form>
                </div>

                <!-- Tabela de administradores existentes -->
                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Nome</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Função</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($admins as $adm)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-3 font-bold text-gray-900">{{ $adm->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $adm->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-700">
                                            {{ $adm->role === 'pastor_senior' ? 'Pastor Sénior' : 'Administrador' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $adm->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $adm->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <form method="POST" action="{{ route('national.congregations.admins.toggle-status', [$congregation, $adm]) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 font-bold {{ $adm->is_active ? 'text-gray-500 hover:text-red-700' : 'text-green-700 hover:text-green-800' }} hover:underline">
                                                <i class="bi {{ $adm->is_active ? 'bi-person-slash' : 'bi-person-check' }}"></i>
                                                {{ $adm->is_active ? 'Desativar' : 'Ativar' }}
                                            </button>
                                        </form>
                                        <button type="button" @click="resetModalUser = { id: {{ $adm->id }}, name: '{{ addslashes($adm->name) }}' }" class="inline-flex items-center gap-1 font-bold text-orange-800 hover:underline">
                                            <i class="bi bi-key"></i> Redefinir senha
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhum administrador local registado nesta congregação.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Modal para redefinir senha -->
                <div x-show="resetModalUser" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                    <div @click.away="resetModalUser = null" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-sm font-black text-gray-900">Redefinir Palavra-passe</h3>
                            <button type="button" @click="resetModalUser = null" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                        </div>
                        <p class="text-xs text-gray-600">
                            Definir nova palavra-passe para <strong x-text="resetModalUser?.name" class="text-gray-900"></strong>.
                        </p>
                        <form method="POST" :action="`{{ url('national/congregations/' . $congregation->id . '/admins') }}/${resetModalUser?.id}/reset-password`" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nova Palavra-passe (mín. 10 carateres)</label>
                                <input type="password" name="new_password" required minlength="10" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Confirmar Nova Palavra-passe</label>
                                <input type="password" name="new_password_confirmation" required minlength="10" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="resetModalUser = null" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                                <button type="submit" class="rounded-lg bg-orange-700 px-4 py-1.5 text-xs font-bold text-white hover:bg-orange-800">Guardar nova senha</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

