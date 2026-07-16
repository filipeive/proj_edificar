@extends('layouts.app')

@section('title', 'Análise de Cultos - Portal Life Church')
@section('page-title', 'Análise de Cultos')
@section('page-subtitle', 'Tendências de Frequência e Crescimento')

    @section('content')
        <div class="relative min-h-screen bg-[#f8fafc] dark:bg-gray-950">
            <!-- Premium Header Section -->
            <div
                class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 pt-12 pb-16 px-4 sm:px-10 mb-8 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-blue-50/50 to-transparent dark:from-blue-900/10 dark:to-transparent pointer-events-none">
                </div>

                <div class="w-full relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-4">
                            <a href="{{ route('services.index') }}"
                                class="inline-flex items-center gap-2 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] hover:text-blue-600 dark:hover:text-blue-400 transition-colors group">
                                <i
                                    class="bi bi-arrow-left-circle-fill text-lg group-hover:-translate-x-1 transition-transform"></i>
                                Voltar aos Cultos
                            </a>

                            <div class="space-y-1">
                                <h1
                                    class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tighter italic">
                                    Inteligência <span class="text-blue-600">Estratégica</span>
                                </h1>
                                <p
                                    class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest max-w-2xl">
                                    Análise multidimensional de frequência, engajamento e tendências de crescimento ministerial.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex -space-x-3">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/40 border-4 border-white dark:border-gray-900 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm"
                                    title="Análise em Tempo Real">
                                    <i class="bi bi-cpu-fill text-xl"></i>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/40 border-4 border-white dark:border-gray-900 flex items-center justify-center text-green-600 dark:text-green-400 shadow-sm"
                                    title="Exportação de Big Data">
                                    <i class="bi bi-cloud-arrow-down-fill text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4 sm:px-10 pb-20 space-y-10">

                <!-- Intelligence Hub: Filters & Exports -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                    <!-- Left Column: Analytical Filters -->
                    <div class="xl:col-span-4">
                        <div
                            class="bg-white dark:bg-gray-800 p-8 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700 h-full">
                            <div class="flex items-center gap-3 mb-8">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                                    <i class="bi bi-funnel-fill text-xl"></i>
                                </div>
                                <div>
                                    <h3
                                        class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.3em]">
                                        FILTROS DE ANÁLISE
                                    </h3>
                                    <p
                                        class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                        Refinar Visualização dos Gráficos
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('services.report') }}" method="GET" class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Data
                                            Início</label>
                                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                                            class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl text-sm font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Data
                                            Fim</label>
                                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                                            class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl text-sm font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Categoria
                                        de Culto</label>
                                    <select name="service_type" data-searchable="false"
                                        class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl text-sm font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none appearance-none">
                                        <option value="">Todos os Registos</option>
                                        <option value="1st" {{ request('service_type') == '1st' ? 'selected' : '' }}>1º Culto
                                            Dominical</option>
                                        <option value="2nd" {{ request('service_type') == '2nd' ? 'selected' : '' }}>2º Culto
                                            Dominical</option>
                                        <option value="3rd" {{ request('service_type') == '3rd' ? 'selected' : '' }}>3º Culto
                                            Dominical</option>
                                        <option value="4th" {{ request('service_type') == '4th' ? 'selected' : '' }}>4º Culto
                                            Dominical</option>
                                        <option value="normal" {{ request('service_type') == 'normal' ? 'selected' : '' }}>Todos
                                            os Cultos Normais</option>
                                        <option value="teaching" {{ request('service_type') == 'teaching' ? 'selected' : '' }}>
                                            Ensino e Discipulado</option>
                                        <option value="special" {{ request('service_type') == 'special' ? 'selected' : '' }}>
                                            Celebrações Especiais</option>
                                    </select>
                                </div>
                                <button type="submit"
                                    class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
                                    <i class="bi bi-arrow-repeat text-lg"></i> Atualizar Painel
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right Column: Export Hub -->
                    <div class="xl:col-span-8">
                        <div
                            class="bg-gray-900 dark:bg-black p-10 rounded-[3rem] shadow-xl border border-gray-800 h-full relative overflow-hidden group">
                            <div
                                class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-blue-600/20 transition-all">
                            </div>

                            <div class="flex items-center gap-3 mb-10 relative z-10">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-white border border-white/10">
                                    <i class="bi bi-cloud-arrow-down-fill text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-[11px] font-black text-blue-400 uppercase tracking-[0.3em]">
                                        RELATÓRIOS E EXPORTAÇÕES
                                    </h3>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">
                                        Gerar Documentação Oficial em PDF ou Excel
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                                <!-- Custom Period Report -->
                                <div
                                    class="p-6 bg-white/5 rounded-[2rem] border border-white/5 hover:border-blue-500/30 transition-all">
                                    <h4
                                        class="text-[10px] font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <i class="bi bi-calendar-range text-blue-400"></i> Período Personalizado
                                    </h4>
                                    <form action="{{ route('services.export.custom') }}" method="GET" class="space-y-4">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Data Início</label>
                                                <input type="date" name="date_from" required
                                                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-blue-500 transition-colors">
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Data Fim</label>
                                                <input type="date" name="date_to" required
                                                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-blue-500 transition-colors">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoria de Culto</label>
                                            <select name="service_type" required data-searchable="false"
                                                class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-blue-500 transition-colors appearance-none">
                                                <option value="all" class="bg-gray-900">Todos os Cultos</option>
                                                <option value="normal" class="bg-gray-900">Cultos Normais</option>
                                                <option value="teaching" class="bg-gray-900">Cultos de Ensino</option>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 pt-2">
                                            <button type="submit" formaction="{{ route('services.export.custom') }}"
                                                class="py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                                <i class="bi bi-file-pdf text-sm"></i> PDF
                                            </button>
                                            <button type="submit" formaction="{{ route('services.export.custom.excel') }}"
                                                class="py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                                <i class="bi bi-file-earmark-excel text-sm"></i> EXCEL
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Monthly Report -->
                                <div
                                    class="p-6 bg-white/5 rounded-[2rem] border border-white/5 hover:border-orange-500/30 transition-all">
                                    <h4
                                        class="text-[10px] font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <i class="bi bi-file-earmark-pdf text-orange-400"></i> Reporte Mensal
                                    </h4>
                                    <form action="{{ route('services.export.monthly') }}" method="GET" class="space-y-4">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Mês</label>
                                                <select name="month" data-searchable="false"
                                                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-orange-500 transition-colors appearance-none">
                                                    @for($m = 1; $m <= 12; $m++)
                                                        <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}
                                                            class="bg-gray-900">
                                                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Ano</label>
                                                <select name="year" data-searchable="false"
                                                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-orange-500 transition-colors appearance-none">
                                                    @for($y = now()->year; $y >= 2023; $y--)
                                                        <option value="{{ $y }}" class="bg-gray-900">{{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoria de Culto</label>
                                            <select name="service_type" data-searchable="false"
                                                class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-orange-500 transition-colors appearance-none">
                                                <option value="all" class="bg-gray-900">Todos os Cultos</option>
                                                <option value="normal" class="bg-gray-900">Cultos Normais</option>
                                                <option value="teaching" class="bg-gray-900">Cultos de Ensino</option>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 pt-2">
                                            <button type="submit" formaction="{{ route('services.export.monthly') }}"
                                                class="py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                                <i class="bi bi-file-pdf text-sm"></i> PDF
                                            </button>
                                            <button type="submit" formaction="{{ route('services.export.monthly.excel') }}"
                                                class="py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                                <i class="bi bi-file-earmark-excel text-sm"></i> EXCEL
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Quarterly Report -->
                                <div
                                    class="p-6 bg-white/5 rounded-[2rem] border border-white/5 hover:border-green-500/30 transition-all">
                                    <h4
                                        class="text-[10px] font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <i class="bi bi-layers-half text-green-400"></i> Reporte Trimestral
                                    </h4>
                                    <form action="{{ route('services.export.quarterly') }}" method="GET" class="space-y-4">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Trimestre</label>
                                                <select name="quarter" data-searchable="false"
                                                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-green-500 transition-colors appearance-none">
                                                    <option value="1" class="bg-gray-900">1º Trimestre</option>
                                                    <option value="2" class="bg-gray-900">2º Trimestre</option>
                                                    <option value="3" class="bg-gray-900">3º Trimestre</option>
                                                    <option value="4" class="bg-gray-900">4º Trimestre</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Ano</label>
                                                <select name="year" data-searchable="false"
                                                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-green-500 transition-colors appearance-none">
                                                    @for($y = now()->year; $y >= 2023; $y--)
                                                        <option value="{{ $y }}" class="bg-gray-900">{{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 pt-2">
                                            <button type="submit" formaction="{{ route('services.export.quarterly') }}"
                                                class="py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                                <i class="bi bi-file-pdf text-sm"></i> PDF
                                            </button>
                                            <button type="submit" formaction="{{ route('services.export.quarterly.excel') }}"
                                                class="py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                                <i class="bi bi-file-earmark-excel text-sm"></i> EXCEL
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Annual Report -->
                                <div
                                    class="p-6 bg-white/5 rounded-[2rem] border border-white/5 hover:border-red-500/30 transition-all">
                                    <h4
                                        class="text-[10px] font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <i class="bi bi-calendar-check text-red-400"></i> Reporte Anual
                                    </h4>
                                    <form action="{{ route('services.export.annual') }}" method="GET" class="space-y-4">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Ano</label>
                                                <select name="year" data-searchable="false"
                                                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-red-500 transition-colors appearance-none">
                                                    @for($y = now()->year; $y >= 2023; $y--)
                                                        <option value="{{ $y }}" class="bg-gray-900">{{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoria de Culto</label>
                                                <select name="service_type" data-searchable="false"
                                                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-white outline-none focus:border-red-500 transition-colors appearance-none">
                                                    <option value="all" class="bg-gray-900">Todos os Cultos</option>
                                                    <option value="normal" class="bg-gray-900">Cultos Normais</option>
                                                    <option value="teaching" class="bg-gray-900">Cultos de Ensino</option>
                                                    <option value="special" class="bg-gray-900">Cultos Especiais</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 pt-2">
                                            <button type="submit" formaction="{{ route('services.export.annual') }}"
                                                class="py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                                <i class="bi bi-file-pdf text-sm"></i> PDF
                                            </button>
                                            <button type="submit" formaction="{{ route('services.export.annual.excel') }}"
                                                class="py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                                <i class="bi bi-file-earmark-excel text-sm"></i> EXCEL
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytical Visualizations -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Main Trend Chart -->
                    <div class="lg:col-span-8">
                        <div
                            class="bg-white dark:bg-gray-900 p-8 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-800 relative overflow-hidden">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
                                <div>
                                    <h3
                                        class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                                        <i class="bi bi-graph-up-arrow text-blue-600"></i>
                                        Fluxo de Engajamento
                                    </h3>
                                    <p
                                        class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                        Tendência Histórica de Participação (Membros + Visitantes)
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                                        <div class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></div>
                                        <span
                                            class="text-[9px] font-black text-blue-700 dark:text-blue-400 uppercase tracking-widest">Tempo
                                            Real</span>
                                    </div>
                                </div>
                            </div>

                            <div class="h-[400px] relative w-full">
                                <canvas id="attendanceTrendChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Audience Composition -->
                    <div class="lg:col-span-4">
                        <div
                            class="bg-white dark:bg-gray-900 p-8 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-800 h-full flex flex-col">
                            <div class="mb-10 text-center">
                                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">
                                    Composição de Público
                                </h3>
                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                    Proporção Membros vs. Visitantes
                                </p>
                            </div>

                            <div class="flex-grow flex items-center justify-center relative">
                                <div class="absolute text-center">
                                    <p
                                        class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase leading-none mb-1">
                                        Impacto Total</p>
                                    <p class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">
                                        {{ number_format($trendServices->sum('total_participation'), 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="w-full aspect-square max-w-[280px]">
                                    <canvas id="visitorsChart"></canvas>
                                </div>
                            </div>

                            <div class="mt-10 grid grid-cols-2 gap-4">
                                <div
                                    class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                    <p
                                        class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">
                                        Membros</p>
                                    <p class="text-xl font-black text-blue-600 dark:text-blue-400">
                                        {{ number_format($trendServices->sum('total_members'), 0, ',', '.') }}
                                    </p>
                                </div>
                                <div
                                    class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                    <p
                                        class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">
                                        Visitantes</p>
                                    <p class="text-xl font-black text-orange-500">
                                        {{ number_format($trendServices->sum('total_visitors'), 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Analytical Table -->
                        <div class="bg-white dark:bg-gray-900 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                            <div class="p-8 border-b border-gray-50 dark:border-gray-800 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div>
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Matriz de Dados Analíticos</h3>
                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">Listagem Detalhada de Registos</p>
                                </div>

                                <!-- Table Search & Filters -->
                                <form action="{{ route('services.report') }}" method="GET" class="flex items-center gap-3">
                                    {{-- Preserve existing filters --}}
                                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                                    <input type="hidden" name="service_type" value="{{ request('service_type') }}">

                                    <div class="relative group">
                                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                                        <input type="text" name="search" value="{{ request('search') }}" 
                                            placeholder="Pesquisar tema ou pregador..."
                                            class="pl-11 pr-5 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none md:w-64">
                                    </div>
                                    <button type="submit" class="p-2.5 bg-gray-900 dark:bg-blue-600 text-white rounded-xl hover:scale-105 transition-transform shadow-lg shadow-blue-600/20">
                                        <i class="bi bi-filter"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Data e Evento</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Impacto Total</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Visitantes</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Decisões</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Dízimos e Ofertas</th>
                                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 text-right">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                        @forelse($paginatedServices as $service)
                                            @php
                                                $salvations = ($service->adults_salvations ?? 0) + ($service->children_salvations ?? 0);
                                                $totalFinancial = $service->total_financial;
                                            @endphp
                                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/50 transition-colors group">
                                                <td class="px-8 py-6">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex flex-col items-center justify-center border border-blue-100 dark:border-blue-800 group-hover:scale-110 transition-transform">
                                                            <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase leading-none">{{ \Carbon\Carbon::parse($service->date)->translatedFormat('M') }}</span>
                                                            <span class="text-xl font-black text-blue-700 dark:text-blue-300 leading-none">{{ \Carbon\Carbon::parse($service->date)->format('d') }}</span>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-black text-gray-900 dark:text-white mb-0.5 leading-tight">{{ $service->theme ?: 'Cálice de Celebração' }}</p>
                                                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $service->service_type }} • {{ $service->preacher_name }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6">
                                                    <div class="flex flex-col">
                                                        <span class="text-lg font-black text-gray-900 dark:text-white leading-none">{{ $service->total_participation }}</span>
                                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">Participantes</span>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $service->total_visitors }}</span>
                                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Visitantes</span>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6">
                                                    @if($salvations > 0)
                                                        <span class="px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-green-200 dark:border-green-800/50">
                                                            <i class="bi bi-heart-fill mr-1"></i> {{ $salvations }} Decisões
                                                        </span>
                                                    @else
                                                        <span class="text-[10px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-widest">Nenhuma</span>
                                                    @endif
                                                </td>
                                                <td class="px-8 py-6">
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">MT {{ number_format($totalFinancial, 2, ',', '.') }}</span>
                                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Total Coletado</span>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6 text-right">
                                                    <a href="{{ route('services.show', $service) }}" class="p-2.5 bg-gray-50 dark:bg-gray-800 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl hover:bg-white dark:hover:bg-gray-700 border border-transparent hover:border-blue-100 dark:hover:border-blue-900 transition-all shadow-sm">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-8 py-20 text-center">
                                                    <div class="flex flex-col items-center gap-4">
                                                        <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-300 dark:text-gray-600">
                                                            <i class="bi bi-search text-3xl"></i>
                                                        </div>
                                                        <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Nenhum registo encontrado para os filtros selecionados.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Section -->
                            <div class="px-8 py-6 bg-gray-50 dark:bg-gray-950/50 border-t border-gray-100 dark:border-gray-800">
                                {{ $paginatedServices->links() }}
                            </div>
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
                                        label: 'Público Total',
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