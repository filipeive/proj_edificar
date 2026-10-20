@extends('layouts.app')

@section('title', 'Análise de Cultos - Portal Life Church')
@section('page-title', 'Análise de Cultos')
@section('page-subtitle', 'Tendências de Frequência e Crescimento')

@section('content')
    <div class="space-y-5">
        <!-- Page Header (National Style) -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">
                    <a href="{{ route('services.index') }}" class="hover:underline">Celebrações</a>
                    <i class="bi bi-chevron-right text-[9px]"></i>
                    <span>Inteligência Estratégica</span>
                </div>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Análise de Cultos & Tendências</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                    Análise multidimensional de frequência, engajamento e tendências de crescimento ministerial.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('services.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                    <span>Voltar aos Cultos</span>
                </a>
            </div>
        </header>

        <!-- Intelligence Hub: Filters & Exports -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
            <!-- Left Column: Analytical Filters -->
            <div class="xl:col-span-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm h-full flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2.5 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white text-sm">
                                <i class="bi bi-funnel-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Filtros de Análise</h3>
                                <p class="text-[11px] text-gray-500">Refinar visualização dos gráficos</p>
                            </div>
                        </div>

                        <form action="{{ route('services.report') }}" method="GET" class="space-y-3.5">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Data Início</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm font-medium text-gray-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-600 transition-all outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Data Fim</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm font-medium text-gray-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-600 transition-all outline-none">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Categoria de Culto</label>
                                <select name="service_type" data-searchable="false"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm font-medium text-gray-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-600 transition-all outline-none">
                                    <option value="">Todos os Registos</option>
                                    <option value="1st" {{ request('service_type') == '1st' ? 'selected' : '' }}>1º Culto Dominical</option>
                                    <option value="2nd" {{ request('service_type') == '2nd' ? 'selected' : '' }}>2º Culto Dominical</option>
                                    <option value="3rd" {{ request('service_type') == '3rd' ? 'selected' : '' }}>3º Culto Dominical</option>
                                    <option value="4th" {{ request('service_type') == '4th' ? 'selected' : '' }}>4º Culto Dominical</option>
                                    <option value="normal" {{ request('service_type') == 'normal' ? 'selected' : '' }}>Todos os Cultos Normais</option>
                                    <option value="teaching" {{ request('service_type') == 'teaching' ? 'selected' : '' }}>Ensino e Discipulado</option>
                                    <option value="special" {{ request('service_type') == 'special' ? 'selected' : '' }}>Celebrações Especiais</option>
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full py-2.5 bg-orange-700 text-white rounded-lg font-bold text-xs sm:text-sm hover:bg-orange-800 shadow-sm transition-all flex items-center justify-center gap-1.5">
                                <i class="bi bi-arrow-repeat"></i> Atualizar Painel
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Export Hub -->
            <div class="xl:col-span-8">
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm h-full">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-orange-700 flex items-center justify-center text-white text-sm">
                            <i class="bi bi-cloud-arrow-down-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Relatórios e Exportações</h3>
                            <p class="text-[11px] text-gray-500">Gerar documentação oficial em PDF ou Excel</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Custom Period Report -->
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="bi bi-calendar-range text-blue-600"></i> Período Personalizado
                            </h4>
                            <form action="{{ route('services.export.custom') }}" method="GET" class="space-y-2.5">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Data Início</label>
                                        <input type="date" name="date_from" required
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-blue-600">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Data Fim</label>
                                        <input type="date" name="date_to" required
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-blue-600">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Categoria de Culto</label>
                                    <select name="service_type" required data-searchable="false"
                                        class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-blue-600">
                                        <option value="all">Todos os Cultos</option>
                                        <option value="normal">Cultos Normais</option>
                                        <option value="teaching">Cultos de Ensino</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <button type="submit" formaction="{{ route('services.export.custom') }}"
                                        class="py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </button>
                                    <button type="submit" formaction="{{ route('services.export.custom.excel') }}"
                                        class="py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <i class="bi bi-file-earmark-excel"></i> EXCEL
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Monthly Report -->
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="bi bi-file-earmark-pdf text-orange-600"></i> Reporte Mensal
                            </h4>
                            <form action="{{ route('services.export.monthly') }}" method="GET" class="space-y-2.5">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Mês</label>
                                        <select name="month" data-searchable="false"
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-orange-600">
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Ano</label>
                                        <select name="year" data-searchable="false"
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-orange-600">
                                            @for($y = now()->year; $y >= 2023; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Categoria de Culto</label>
                                    <select name="service_type" data-searchable="false"
                                        class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-orange-600">
                                        <option value="all">Todos os Cultos</option>
                                        <option value="normal">Cultos Normais</option>
                                        <option value="teaching">Cultos de Ensino</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <button type="submit" formaction="{{ route('services.export.monthly') }}"
                                        class="py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </button>
                                    <button type="submit" formaction="{{ route('services.export.monthly.excel') }}"
                                        class="py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <i class="bi bi-file-earmark-excel"></i> EXCEL
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Quarterly Report -->
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="bi bi-layers-half text-emerald-600"></i> Reporte Trimestral
                            </h4>
                            <form action="{{ route('services.export.quarterly') }}" method="GET" class="space-y-2.5">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Trimestre</label>
                                        <select name="quarter" data-searchable="false"
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-emerald-600">
                                            <option value="1">1º Trimestre</option>
                                            <option value="2">2º Trimestre</option>
                                            <option value="3">3º Trimestre</option>
                                            <option value="4">4º Trimestre</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Ano</label>
                                        <select name="year" data-searchable="false"
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-emerald-600">
                                            @for($y = now()->year; $y >= 2023; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <button type="submit" formaction="{{ route('services.export.quarterly') }}"
                                        class="py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </button>
                                    <button type="submit" formaction="{{ route('services.export.quarterly.excel') }}"
                                        class="py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <i class="bi bi-file-earmark-excel"></i> EXCEL
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Annual Report -->
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <i class="bi bi-calendar-check text-purple-600"></i> Reporte Anual
                            </h4>
                            <form action="{{ route('services.export.annual') }}" method="GET" class="space-y-2.5">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Ano</label>
                                        <select name="year" data-searchable="false"
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-purple-600">
                                            @for($y = now()->year; $y >= 2023; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Categoria de Culto</label>
                                        <select name="service_type" data-searchable="false"
                                            class="w-full px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 outline-none focus:border-purple-600">
                                            <option value="all">Todos os Cultos</option>
                                            <option value="normal">Cultos Normais</option>
                                            <option value="teaching">Cultos de Ensino</option>
                                            <option value="special">Cultos Especiais</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <button type="submit" formaction="{{ route('services.export.annual') }}"
                                        class="py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </button>
                                    <button type="submit" formaction="{{ route('services.export.annual.excel') }}"
                                        class="py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                                        <i class="bi bi-file-earmark-excel"></i> EXCEL
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytical Visualizations -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Main Trend Chart -->
            <div class="lg:col-span-8">
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="bi bi-graph-up-arrow text-blue-600"></i>
                                Fluxo de Engajamento
                            </h3>
                            <p class="text-[11px] text-gray-500">
                                Tendência Histórica de Participação (Membros + Visitantes)
                            </p>
                        </div>

                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 rounded-full">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></div>
                            <span class="text-[10px] font-bold text-blue-700 uppercase">Tempo Real</span>
                        </div>
                    </div>

                    <div class="h-[340px] relative w-full">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Audience Composition -->
            <div class="lg:col-span-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm h-full flex flex-col">
                    <div class="mb-4 text-center">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">
                            Composição de Participantes
                        </h3>
                        <p class="text-[11px] text-gray-500">
                            Proporção Membros vs. Visitantes
                        </p>
                    </div>

                    <div class="flex-grow flex items-center justify-center relative py-2">
                        <div class="absolute text-center">
                            <p class="text-[10px] font-bold text-gray-400 uppercase leading-none mb-1">Impacto Total</p>
                            <p class="text-2xl sm:text-3xl font-black text-gray-950 tracking-tight">
                                {{ number_format($trendServices->sum('total_participation'), 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-full aspect-square max-w-[220px]">
                            <canvas id="visitorsChart"></canvas>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-500 uppercase">Membros</p>
                            <p class="text-lg font-black text-blue-600">
                                {{ number_format($trendServices->sum('total_members'), 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-500 uppercase">Visitantes</p>
                            <p class="text-lg font-black text-orange-600">
                                {{ number_format($trendServices->sum('total_visitors'), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytical Table -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Matriz de Dados Analíticos</h3>
                    <p class="text-[11px] text-gray-500">Listagem Detalhada de Registos</p>
                </div>

                <!-- Table Search & Filters -->
                <form action="{{ route('services.report') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="service_type" value="{{ request('service_type') }}">

                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Pesquisar tema ou pregador..."
                            class="pl-8 pr-3 py-2 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-600 transition-all outline-none md:w-64">
                    </div>
                    <button type="submit" class="px-3 py-2 bg-orange-700 text-white rounded-lg hover:bg-orange-800 transition-colors shadow-sm text-xs font-bold">
                        <i class="bi bi-filter"></i>
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">Data e Evento</th>
                            <th class="px-4 py-3">Participantes</th>
                            <th class="px-4 py-3">Visitantes</th>
                            <th class="px-4 py-3">Salvações</th>
                            <th class="px-4 py-3 text-right">Ofertas</th>
                            <th class="px-4 py-3 text-right">Dízimos</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($paginatedServices as $service)
                            @php
                                $salvations = $service->total_salvations;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex flex-col items-center justify-center border border-blue-200 flex-shrink-0">
                                            <span class="text-[8px] font-black text-blue-700 uppercase leading-none">{{ \Carbon\Carbon::parse($service->date)->translatedFormat('M') }}</span>
                                            <span class="text-sm font-black text-blue-800 leading-tight">{{ \Carbon\Carbon::parse($service->date)->format('d') }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-900 truncate">{{ $service->theme ?: '—' }}</p>
                                            <p class="text-[10px] font-medium text-gray-500 uppercase truncate">{{ $service->service_type }} • {{ $service->preacher ? $service->preacher->name : ($service->preacher_name ?: '—') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm font-black text-gray-900">{{ $service->total_participation }}</span>
                                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Total</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                        <span class="text-xs font-bold text-gray-700">{{ $service->total_visitors }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($salvations > 0)
                                        <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-md text-[10px] font-black uppercase border border-green-200">
                                            <i class="bi bi-heart-fill mr-0.5"></i> {{ $salvations }}
                                        </span>
                                    @else
                                        <span class="text-xs font-bold text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-xs font-black text-emerald-700">{{ number_format($service->total_offerings, 0, ',', '.') }} MT</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-xs font-black text-indigo-700">{{ number_format($service->total_tithes, 0, ',', '.') }} MT</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('services.show', $service) }}" class="w-8 h-8 bg-gray-50 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 border border-gray-200 transition-all inline-flex items-center justify-center text-xs">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center">
                                    <p class="text-xs font-semibold text-gray-500">Nenhum registo encontrado para os filtros selecionados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $paginatedServices->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Chart.js Premium Config
                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.color = '#94a3b8';

                // Main Attendance Trend Chart
                const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');

                // Create Gradient
                const blueGradient = trendCtx.createLinearGradient(0, 0, 0, 400);
                blueGradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
                blueGradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($stats['labels']) !!},
                        datasets: [{
                            label: 'Participantes Total',
                            data: {!! json_encode($stats['attendance']) !!},
                            borderColor: '#2563eb',
                            backgroundColor: blueGradient,
                            fill: true,
                            tension: 0.45,
                            borderWidth: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#2563eb',
                            pointBorderWidth: 3,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#2563eb',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 15,
                                displayColors: false,
                                cornerRadius: 12,
                                callbacks: {
                                    label: function(context) {
                                        return ' Impacto: ' + context.parsed.y + ' pessoas';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(148, 163, 184, 0.1)', drawBorder: false },
                                ticks: { padding: 10 }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { padding: 10 }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        }
                    }
                });

                // Visitors Doughnut Chart
                const visitorsCtx = document.getElementById('visitorsChart').getContext('2d');
                new Chart(visitorsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Membros', 'Visitantes'],
                        datasets: [{
                            data: [
                                {{ $trendServices->sum('total_members') }},
                                {{ $trendServices->sum('total_visitors') }}
                            ],
                            backgroundColor: ['#2563eb', '#f97316'],
                            hoverOffset: 15,
                            borderWidth: 0,
                            borderRadius: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '82%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 15,
                                cornerRadius: 12,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data[0] + context.dataset.data[1];
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return ` ${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
