@extends('layouts.app')

@section('title', 'Dashboard Pastor - Portal Life Church')
@section('page-title', 'Dashboard do Pastor de Zona')
@section('page-subtitle', 'Visão geral da Zona ' . $zoneName)

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Zona Info -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-orange-50 p-4 rounded-2xl group-hover:bg-orange-600 transition-colors duration-500">
                    <i
                        class="bi bi-geo-alt-fill text-orange-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Localização</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Zona</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">{{ $zoneName }}</p>
                <p class="text-xs text-gray-400 mt-2 font-bold uppercase tracking-widest">{{ $supervisions->count() }}
                    Supervisões Ativas</p>
            </div>
        </div>

        <!-- Total Membros -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-50 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors duration-500">
                    <i
                        class="bi bi-people-fill text-blue-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Dimensão</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total de Membros</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">{{ $totalMembers }}</p>
                <p class="text-xs text-gray-400 mt-2 font-bold uppercase tracking-widest">Membros Ativos</p>
            </div>
        </div>

        <!-- Total Arrecadado -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-50 p-4 rounded-2xl group-hover:bg-green-600 transition-colors duration-500">
                    <i
                        class="bi bi-cash-coin text-green-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Financeiro</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total da Zona</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">{{ number_format($total, 2, ',', '.') }}
                    MT</p>
                <p class="text-xs text-gray-400 mt-2 font-bold uppercase tracking-widest">Este Mês</p>
            </div>
        </div>

        <!-- Desempenho -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-50 p-4 rounded-2xl group-hover:bg-purple-600 transition-colors duration-500">
                    <i
                        class="bi bi-grid-3x3-gap-fill text-purple-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Estrutura</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Células</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">{{ $zone->cells()->count() }}</p>
                <div class="flex items-center mt-4 text-xs">
                    <span class="text-purple-500 font-bold flex items-center">
                        <i class="bi bi-activity text-lg mr-1"></i> Em Atividade
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Gráfico de Arrecadação -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 h-fit">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Histórico Financeiro</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Últimos 6 Meses (MT)</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-blue-600 rounded-full"></span>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Contribuições</span>
                </div>
            </div>
            <div class="w-full" style="height: 300px;">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight mb-6">Ações Rápidas</h3>
                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('reports.zone') }}"
                        class="flex items-center p-4 bg-blue-50 rounded-2xl hover:bg-blue-600 group transition-all duration-300">
                        <div
                            class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-blue-600 mr-4 shadow-sm">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <span class="font-black text-blue-900 group-hover:text-white transition-colors">Relatório da
                            Zona</span>
                    </a>
                    <a href="{{ route('cells.index') }}"
                        class="flex items-center p-4 bg-green-50 rounded-2xl hover:bg-green-600 group transition-all duration-300">
                        <div
                            class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-green-600 mr-4 shadow-sm">
                            <i class="bi bi-people"></i>
                        </div>
                        <span class="font-black text-green-900 group-hover:text-white transition-colors">Listar
                            Células</span>
                    </a>
                    <a href="{{ route('contributions.index') }}"
                        class="flex items-center p-4 bg-orange-50 rounded-2xl hover:bg-orange-600 group transition-all duration-300">
                        <div
                            class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-orange-600 mr-4 shadow-sm">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <span
                            class="font-black text-orange-900 group-hover:text-white transition-colors">Contribuições</span>
                    </a>
                </div>
            </div>

            <div class="bg-gray-900 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-600/20 rounded-full -mr-16 -mt-16 blur-3xl"></div>
                <h3 class="text-lg font-black tracking-tight mb-4 relative z-10">Status da Zona</h3>
                <p class="text-sm text-gray-400 leading-relaxed mb-6 relative z-10">
                    A zona <span class="text-orange-500 font-bold">{{ $zoneName }}</span> está operando com <span
                        class="text-white font-bold">{{ $supervisions->count() }}</span> supervisões.
                </p>
                <div class="flex items-center space-x-2 relative z-10">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-green-500">Sistema Online</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Supervisões da Zona -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Supervisões</h3>
                <a href="{{ route('supervisions.index') }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver Todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-50">
                            <th class="px-8 py-4 text-left">Supervisão</th>
                            <th class="px-8 py-4 text-center">Células</th>
                            <th class="px-8 py-4 text-right">Total Arrecadado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($supervisions as $supervision)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-black mr-4 group-hover:bg-orange-600 group-hover:text-white transition-all">
                                            {{ strtoupper(substr($supervision['name'], 0, 1)) }}
                                        </div>
                                        <span class="font-black text-gray-900">{{ $supervision['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span
                                        class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        {{ $supervision['cells'] }} Células
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right font-black text-green-600 tracking-tight">
                                    {{ number_format($supervision['total'], 2, ',', '.') }} MT
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reuniões de Célula Recentes -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Células: Reuniões Recentes</h3>
                <a href="{{ route('cell-meetings.index') }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver Todas</a>
            </div>
            <div class="space-y-6">
                @forelse($recentCellMeetings as $meeting)
                    <div class="flex items-center space-x-6 group">
                        <div class="bg-blue-50 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <span class="block text-xl font-black leading-none">{{ $meeting->meeting_date->format('d') }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest">{{ $meeting->meeting_date->translatedFormat('M') }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-gray-900 group-hover:text-blue-600 transition-colors leading-tight">
                                {{ $meeting->cell->name }}
                            </h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                Líder: {{ $meeting->leader->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="block font-black text-gray-900">{{ ($meeting->adults_count ?? 0) + ($meeting->children_count ?? 0) + ($meeting->visitors_count ?? 0) }}</span>
                            <span class="text-[8px] font-black uppercase tracking-widest text-gray-400">Total</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10 font-bold uppercase tracking-widest text-[10px]">Nenhuma reunião registada recentemente.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Próximos Eventos da Zona -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Próximos Eventos</h3>
                <a href="{{ route('events.index') }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver Todos</a>
            </div>
            <div class="space-y-6">
                @forelse($upcomingEvents as $event)
                    <div class="flex items-center space-x-6 group">
                        <div
                            class="bg-gray-50 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            <span class="block text-xl font-black leading-none">{{ $event->date->format('d') }}</span>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest">{{ $event->date->translatedFormat('M') }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-gray-900 group-hover:text-orange-600 transition-colors">
                                {{ $event->name }}</h4>
                            <p class="text-xs text-gray-500 flex items-center mt-1">
                                <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Life Church' }}
                            </p>
                        </div>
                        <span
                            class="px-3 py-1 bg-gray-100 rounded-full text-[8px] font-black uppercase tracking-widest text-gray-500">
                            {{ $event->eventType->name ?? 'Evento' }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10 font-bold uppercase tracking-widest text-[10px]">Nenhum evento programado para esta zona.</p>
                @endforelse
            </div>
        </div>

        <!-- Últimos Cultos -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Relatórios de Cultos</h3>
                <a href="{{ route('services.index') }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver Todos</a>
            </div>
            <div class="space-y-6">
                @forelse($recentServices as $service)
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-orange-600 shadow-sm">
                                <i class="bi bi-journal-text text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900">Culto de {{ $service->date->translatedFormat('l, d/m') }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                    {{ $service->theme ?? 'Sem Tema Definido' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-gray-900">{{ $service->total_participation }}</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">Presentes</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10 font-bold uppercase tracking-widest text-[10px]">Nenhum relatório de culto registado recentemente.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('financialChart').getContext('2d');
            
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Contribuições (MT)',
                        data: @json($chartData['data']),
                        borderColor: '#2563eb',
                        borderWidth: 4,
                        fill: true,
                        backgroundColor: gradient,
                        tension: 0.4,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }) + ' MT';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                font: { weight: 'bold', size: 10 },
                                color: '#9ca3af',
                                callback: function(value) {
                                    return value.toLocaleString('pt-MZ') + ' MT';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { weight: 'bold', size: 10 },
                                color: '#9ca3af'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush