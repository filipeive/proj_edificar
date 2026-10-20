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

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:bg-gray-900 dark:border-gray-800"
            x-data="{
                modalOpen: false,
                selectedCongregation: {},
                init() {
                    window.addEventListener('congregation-modal-open', (event) => {
                        this.selectedCongregation = event.detail || {};
                        this.modalOpen = true;
                    });
                }
            }">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-orange-600"></i> Mapa Nacional das Congregações
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Distribuição geográfica oficial das igrejas. Clique em qualquer marcador para ficha completa.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-[11px] font-bold text-gray-600 dark:text-gray-300">
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-amber-500 border-2 border-white shadow-sm"></span> Sede Principal</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-emerald-600 border-2 border-white shadow-sm"></span> Congregação Ativa</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-gray-400 border-2 border-white shadow-sm"></span> Inativa</span>
                </div>
            </div>

            <!-- Container do Mapa Leaflet -->
            <div id="national-congregation-map" data-national-congregation-map data-map-data="national-congregation-map-data"
                class="w-full h-[450px] rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-100 shadow-inner"
                aria-label="Mapa de localização das congregações nacionais"></div>
            <script id="national-congregation-map-data" type="application/json">@json(['congregations' => $mapCongregations])</script>

            <!-- Barra de Seleção Rápida de Congregações -->
            @if($mapCongregations->isNotEmpty())
                <div class="mt-3.5 flex flex-wrap items-center gap-2 border-t border-gray-100 dark:border-gray-800 pt-3">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 mr-1 flex items-center gap-1">
                        <i class="bi bi-compass"></i> Focar:
                    </span>
                    @foreach($mapCongregations as $mc)
                        <button type="button"
                            onclick="window.__focusCongregation({{ $mc['id'] }})"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 px-3 py-1.5 text-xs font-bold text-gray-800 dark:text-gray-200 shadow-sm hover:border-orange-400 hover:bg-orange-50/50 dark:hover:bg-gray-800 transition">
                            <span class="h-2.5 w-2.5 rounded-full shrink-0 shadow-sm" style="background-color: {{ $mc['color'] }};"></span>
                            <span>{{ $mc['name'] }}</span>
                            @if($mc['is_primary'])
                                <span class="rounded bg-amber-100 dark:bg-amber-950/80 px-1.5 py-0.2 text-[9px] font-black uppercase text-amber-800 dark:text-amber-300">SEDE</span>
                            @endif
                            <span class="text-[10px] text-gray-500 font-semibold">({{ $mc['users_count'] }} memb.)</span>
                        </button>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-xs text-gray-500">Ainda não há congregações com coordenadas registadas. Adicione as coordenadas no formulário de edição de cada congregação.</p>
            @endif

            <!-- Modal Rico de Descrição da Congregação (Estilo Visionários) -->
            <div x-show="modalOpen"
                x-cloak
                @keydown.escape.window="modalOpen = false"
                class="fixed inset-0 z-[9999] overflow-y-auto"
                aria-labelledby="modal-title" role="dialog" aria-modal="true">
                
                <div class="fixed inset-0 bg-gray-950/70 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>

                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:bg-gray-900 dark:border-gray-800"
                        @click.outside="modalOpen = false">
                        
                        <!-- Header com Gradiente Colorido -->
                        <div class="p-6 text-white relative"
                            :style="'background: linear-gradient(135deg, ' + (selectedCongregation.color || '#ea580c') + ', #0f172a)'">
                            <button type="button" @click="modalOpen = false"
                                class="absolute top-4 right-4 rounded-full bg-black/30 text-white hover:bg-black/50 p-2 transition">
                                <i class="bi bi-x-lg text-xs"></i>
                            </button>
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span x-show="selectedCongregation.is_primary"
                                    class="rounded-full bg-amber-400 text-amber-950 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm">
                                    ★ Sede Nacional
                                </span>
                                <span x-show="selectedCongregation.has_edificar"
                                    class="rounded-full bg-sky-300 text-sky-950 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm">
                                    Projeto Edificar
                                </span>
                                <span class="rounded-full bg-white/20 text-white px-2.5 py-0.5 text-[10px] font-bold backdrop-blur-xs"
                                    x-text="selectedCongregation.is_active ? 'Ativa' : 'Inativa'"></span>
                            </div>
                            <h3 class="text-xl font-black tracking-tight" x-text="selectedCongregation.name"></h3>
                            <p class="text-xs text-white/80 mt-1 flex items-center gap-1.5">
                                <i class="bi bi-geo-alt-fill text-amber-300"></i>
                                <span x-text="selectedCongregation.city || 'Moçambique'"></span>
                                <span class="text-white/40">•</span>
                                <span class="font-mono text-[11px]" x-text="(selectedCongregation.latitude || '') + ', ' + (selectedCongregation.longitude || '')"></span>
                            </p>
                        </div>

                        <!-- Corpo do Modal -->
                        <div class="p-6 space-y-5">
                            <!-- Métricas Rápidas -->
                            <div class="grid grid-cols-3 gap-3 text-center">
                                <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-3 border border-gray-100 dark:border-gray-700">
                                    <div class="text-xl font-black text-gray-900 dark:text-white" x-text="selectedCongregation.users_count"></div>
                                    <div class="text-[10px] font-bold uppercase text-gray-500">Membros</div>
                                </div>
                                <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-3 border border-gray-100 dark:border-gray-700">
                                    <div class="text-xl font-black text-gray-900 dark:text-white" x-text="selectedCongregation.zones_count"></div>
                                    <div class="text-[10px] font-bold uppercase text-gray-500">Zonas</div>
                                </div>
                                <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-3 border border-gray-100 dark:border-gray-700">
                                    <div class="text-xl font-black text-gray-900 dark:text-white" x-text="selectedCongregation.cells_count"></div>
                                    <div class="text-[10px] font-bold uppercase text-gray-500">Células</div>
                                </div>
                            </div>

                            <!-- Liderança e Contactos -->
                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-2.5 text-xs text-gray-700 dark:text-gray-300 bg-gray-50/50 dark:bg-gray-800/40">
                                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="font-bold text-gray-500 flex items-center gap-1.5">
                                        <i class="bi bi-person-badge text-orange-600"></i> Liderança:
                                    </span>
                                    <span class="font-black text-gray-900 dark:text-white" x-text="selectedCongregation.pastor_name"></span>
                                </div>
                                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="font-bold text-gray-500 flex items-center gap-1.5">
                                        <i class="bi bi-telephone text-orange-600"></i> Contacto:
                                    </span>
                                    <a :href="'tel:' + selectedCongregation.pastor_phone"
                                        class="font-semibold text-orange-700 dark:text-orange-400 hover:underline"
                                        x-text="selectedCongregation.pastor_phone"></a>
                                </div>
                                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="font-bold text-gray-500 flex items-center gap-1.5">
                                        <i class="bi bi-envelope text-orange-600"></i> Email:
                                    </span>
                                    <a :href="'mailto:' + selectedCongregation.pastor_email"
                                        class="font-semibold text-orange-700 dark:text-orange-400 hover:underline"
                                        x-text="selectedCongregation.pastor_email"></a>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-500 flex items-center gap-1.5">
                                        <i class="bi bi-globe text-orange-600"></i> Acesso Web / Domínio:
                                    </span>
                                    <span class="font-medium text-gray-600 dark:text-gray-400" x-text="selectedCongregation.domain"></span>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="flex flex-col sm:flex-row items-center gap-2 pt-2">
                                <a :href="selectedCongregation.show_url"
                                    class="w-full sm:flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-orange-700 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                                    <i class="bi bi-speedometer2"></i> Painel da Congregação &rarr;
                                </a>
                                <a :href="selectedCongregation.edit_url"
                                    class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                                <button type="button" @click="modalOpen = false"
                                    class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                    Fechar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

@push('styles')
<style>
    .custom-congregation-pin {
        background: transparent !important;
        border: none !important;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 16px !important;
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        padding: 0 !important;
    }
    .leaflet-popup-content {
        margin: 12px 14px !important;
        line-height: 1.4 !important;
    }
    .leaflet-popup-tip-container {
        margin-top: -1px;
    }
</style>
@endpush
