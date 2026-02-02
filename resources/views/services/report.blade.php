@extends('layouts.app')

@section('title', 'Análise de Cultos - Portal Life Church')
@section('page-title', 'Análise de Cultos')
@section('page-subtitle', 'Tendências de Frequência e Crescimento')

@section('content')
    <div class="container-fluid space-y-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('services.index') }}"
                class="px-6 py-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-gray-900 transition-all font-bold text-xs uppercase tracking-widest flex items-center gap-2 shadow-sm">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <div class="flex gap-2">
                <!-- Dropdown de Exportação rápida can be added here -->
            </div>
        </div>

        <!-- Filtros e Exportações -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
                <!-- Filtros de Visualização -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-funnel-fill text-blue-600"></i> Filtros de Análise
                    </h3>
                    <form action="{{ route('services.report') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Início</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Fim</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Tipo de Culto</label>
                            <select name="service_type"
                                class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 custom-select">
                                <option value="">Todos os Tipos</option>
                                <option value="1st" {{ request('service_type') == '1st' ? 'selected' : '' }}>1º Culto</option>
                                <option value="2nd" {{ request('service_type') == '2nd' ? 'selected' : '' }}>2º Culto</option>
                                <option value="3rd" {{ request('service_type') == '3rd' ? 'selected' : '' }}>3º Culto</option>
                                <option value="4th" {{ request('service_type') == '4th' ? 'selected' : '' }}>4º Culto</option>
                                <option value="normal" {{ request('service_type') == 'normal' ? 'selected' : '' }}>Todos os
                                    Cultos Normais</option>
                                <option value="teaching" {{ request('service_type') == 'teaching' ? 'selected' : '' }}>Ensino
                                </option>
                                <option value="special" {{ request('service_type') == 'special' ? 'selected' : '' }}>Especial
                                </option>
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full py-3 bg-gray-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all">
                            Atualizar Análise
                        </button>
                    </form>
                </div>

                <!-- Exportação Personalizada (Pastor Luis) -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-calendar-range text-purple-600"></i> Exportação Personalizada
                    </h3>
                    <form action="{{ route('services.export.custom') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Data Início</label>
                                <input type="date" name="date_from" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Data Fim</label>
                                <input type="date" name="date_to" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Tipo de Culto</label>
                            <select name="service_type" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 custom-select">
                                <option value="">Selecione...</option>
                                <option value="all">Todos (Separados)</option>
                                <option value="1st">Apenas 1º Culto</option>
                                <option value="2nd">Apenas 2º Culto</option>
                                <option value="3rd">Apenas 3º Culto</option>
                                <option value="4th">Apenas 4º Culto</option>
                                <option value="normal">Todos os Cultos Normais</option>
                                <option value="teaching">Cultos de Ensino</option>
                                <option value="special">Cultos Especiais</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="submit" formaction="{{ route('services.export.custom') }}"
                                class="w-full py-3 bg-purple-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-purple-700 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-file-pdf"></i> PDF
                            </button>
                            <button type="submit" formaction="{{ route('services.export.custom.excel') }}"
                                class="w-full py-3 bg-green-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-green-700 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Exportação Mensal -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-file-earmark-pdf text-orange-600"></i> Relatório Mensal (PDF)
                    </h3>
                    <form action="{{ route('services.export.monthly') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Mês</label>
                                <select name="month"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 custom-select">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Ano</label>
                                <select name="year"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 custom-select">
                                    @for($y = now()->year; $y >= 2023; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Tipo de Culto</label>
                            <select name="service_type"
                                class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 custom-select">
                                <option value="all">Todos (Separados)</option>
                                <option value="normal">Cultos Normais</option>
                                <option value="teaching">Cultos de Ensino</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="submit" formaction="{{ route('services.export.monthly') }}"
                                class="w-full py-3 bg-orange-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-700 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-file-pdf"></i> PDF
                            </button>
                            <button type="submit" formaction="{{ route('services.export.monthly.excel') }}"
                                class="w-full py-3 bg-green-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-green-700 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Exportação Trimestral -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-layers-half text-green-600"></i> Relatório Trimestral (PDF)
                    </h3>
                    <form action="{{ route('services.export.quarterly') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Trimestre</label>
                                <select name="quarter"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 custom-select">
                                    <option value="1">1º Trimestre</option>
                                    <option value="2">2º Trimestre</option>
                                    <option value="3">3º Trimestre</option>
                                    <option value="4">4º Trimestre</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Ano</label>
                                <select name="year"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 custom-select">
                                    @for($y = now()->year; $y >= 2023; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="submit" formaction="{{ route('services.export.quarterly') }}"
                                class="w-full py-3 bg-green-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-green-700 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-file-pdf"></i> PDF
                            </button>
                            <button type="submit" formaction="{{ route('services.export.quarterly.excel') }}"
                                class="w-full py-3 bg-teal-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-teal-700 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Exportação Anual -->
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-calendar-check text-red-600"></i> Relatório Anual (PDF)
                    </h3>
                    <form action="{{ route('services.export.annual') }}" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">Ano</label>
                            <select name="year"
                                class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 custom-select">
                                @for($y = now()->year; $y >= 2023; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Tipo de Culto</label>
                            <select name="service_type"
                                class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 custom-select">
                                <option value="all">Todos (Separados)</option>
                                <option value="normal">Cultos Normais</option>
                                <option value="teaching">Cultos de Ensino</option>
                                <option value="special">Cultos Especiais</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="submit" formaction="{{ route('services.export.annual') }}"
                                class="w-full py-3 bg-red-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-file-pdf"></i> PDF
                            </button>
                            <button type="submit" formaction="{{ route('services.export.annual.excel') }}"
                                class="w-full py-3 bg-green-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-green-700 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Gráfico Principal -->
            <div class="lg:col-span-8">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="bi bi-graph-up text-blue-600"></i>
                        Tendência de Frequência
                    </h3>
                    <div class="aspect-[16/9] relative">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Stats Rápidas -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 text-center">Configuração de
                        Público</h3>
                    <div class="aspect-square relative flex items-center justify-center">
                        <div class="absolute text-center z-10">
                            <p class="text-[10px] font-black text-gray-400 uppercase leading-none">Total</p>
                            <p class="text-3xl font-black text-gray-900 tracking-tighter">
                                {{ $trendServices->sum('total_participation') }}
                            </p>
                        </div>
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Resumo Detalhado</h3>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ count($trendServices) }}
                    Cultos Analisados</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Público
                                Total</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Visitantes
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Decisões
                            </th>
                            <!-- culto -->
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Culto
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($trendServices as $service)
                            @php
                                $total = $service->total_participation;
                                $members = $service->total_members;
                                $visitors = $service->total_visitors;
                                $salvations = $service->adults_salvations + $service->children_salvations;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($service->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-8 py-4 font-black text-blue-600">{{ $total }}</td>
                                <td class="px-8 py-4 text-gray-600">{{ $members }}</td>
                                <td class="px-8 py-4 text-gray-600">{{ $visitors }}</td>
                                <td class="px-8 py-4">
                                    <span
                                        class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-black">{{ $salvations }}</span>
                                </td>
                                <td class="px-8 py-4 text-gray-600">{{ $service->service_type }}</td>
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
            document.addEventListener('DOMContentLoaded', function () {
                // Main Attendance Trend Chart
                const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($stats['labels']) !!},
                        datasets: [{
                            label: 'Público Total',
                            data: {!! json_encode($stats['attendance']) !!},
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#2563eb',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                            x: { grid: { display: false } }
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
                            backgroundColor: ['#2563eb', '#eab308'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20, font: { weight: 'bold', size: 10 } } }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection