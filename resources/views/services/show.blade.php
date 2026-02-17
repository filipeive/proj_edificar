@extends('layouts.app')

@section('title', 'Detalhes da Celebração - Portal Life Church')
@section('page-title', 'Detalhes do Culto')
@section('page-subtitle', 'Dados consolidados da celebração de ' . $service->date->format('d/m/Y'))

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('services.download-pdf', $service) }}"
            class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50"
            title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        @can('update', $service)
            <a href="{{ route('services.edit', $service) }}"
                class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
                title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endcan
        @can('delete', $service)
            <button type="button" onclick="confirmDelete('delete-form', 'Deseja excluir este culto?')"
                class="action-icon text-gray-600 hover:text-red-600 hover:bg-red-50"
                title="Excluir">
                <i class="bi bi-trash"></i>
            </button>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative overflow-hidden transition-all">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="space-y-3 relative z-10">
                <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em]">
                    <a href="{{ route('services.index') }}" class="hover:text-blue-700 dark:hover:text-blue-300 transition-colors">Celebrações</a>
                    <i class="bi bi-chevron-right text-[8px] opacity-30"></i>
                    <span class="opacity-60 text-gray-400 dark:text-gray-500">Detalhes</span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter leading-tight italic">
                    {{ $service->date->translatedFormat('d \d\e F, Y') }}
                </h1>
                <div class="flex items-center gap-4">
                    <span class="px-4 py-1.5 {{ $service->service_type === 'teaching' ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' }} rounded-full text-[10px] font-black uppercase tracking-widest border border-current opacity-80">
                        @switch($service->service_type)
                            @case('1st') 1º Culto @break
                            @case('2nd') 2º Culto @break
                            @case('3rd') 3º Culto @break
                            @case('4th') 4º Culto @break
                            @case('teaching') Culto de Ensino @break
                            @default Especial
                        @endswitch
                    </span>
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter flex items-center gap-2">
                        <i class="bi bi-clock-history"></i>
                        Registado em {{ $service->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 relative z-10">
                <a href="{{ route('services.download-pdf', $service) }}" class="group flex items-center bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-8 py-5 rounded-2xl hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all font-black text-[11px] uppercase tracking-[0.1em] shadow-lg shadow-red-600/10 border border-red-100 dark:border-red-900/50">
                    <i class="bi bi-file-earmark-pdf-fill text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    Exportar PDF
                </a>
                @can('update', $service)
                    <a href="{{ route('services.edit', $service) }}" class="group flex items-center bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-8 py-5 rounded-2xl hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all font-black text-[11px] uppercase tracking-[0.1em] shadow-lg shadow-blue-600/10 border border-blue-100 dark:border-blue-900/50">
                        <i class="bi bi-pencil-fill text-xl mr-3 group-hover:rotate-12 transition-transform"></i>
                        Editar
                    </a>
                @endcan
                @can('delete', $service)
                    <form action="{{ route('services.destroy', $service) }}" method="POST" id="delete-form-main">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete('delete-form-main', 'Deseja excluir este culto?')"
                            class="group flex items-center bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 px-8 py-5 rounded-2xl hover:bg-red-500 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all font-black text-[11px] uppercase tracking-[0.1em] shadow-sm border border-gray-100 dark:border-gray-600">
                            <i class="bi bi-trash3-fill text-xl mr-3 group-hover:shake transition-transform"></i>
                            Eliminar
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <!-- Left Column: Primary Content -->
            <div class="xl:col-span-8 space-y-8">
                <!-- Theme & Message -->
                <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row gap-10">
                    <div class="flex-1 space-y-8">
                        @if($service->theme)
                            <div class="space-y-3">
                                <span class="text-[9px] font-black text-blue-600/60 dark:text-blue-400/60 uppercase tracking-[0.3em] block ml-1"><i class="bi bi-bookmark-star-fill mr-1"></i> TEMA DA CELEBRAÇÃO</span>
                                <h2 class="text-3xl font-black text-gray-900 dark:text-white leading-tight italic tracking-tight">"{{ $service->theme }}"</h2>
                                <div class="w-16 h-1 bg-blue-600 rounded-full"></div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] block ml-1"><i class="bi bi-body-text mr-1"></i> RESUMO DA MENSAGEM</span>
                            <div class="prose prose-blue dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 font-medium leading-relaxed italic text-lg opacity-90">
                                {!! nl2br(e($service->message ?? 'Nenhuma mensagem disponível para esta celebração.')) !!}
                            </div>
                        </div>

                        @if($service->observations)
                            <div class="space-y-3">
                                <span class="text-[9px] font-black text-orange-600/60 uppercase tracking-[0.3em] block ml-1"><i class="bi bi-sticky-fill mr-1"></i> OBSERVAÇÕES DO SECRETARIADO</span>
                                <div class="p-6 bg-orange-50/50 dark:bg-orange-900/10 rounded-2xl border-l-4 border-orange-200 dark:border-orange-800 relative overflow-hidden">
                                    <i class="bi bi-quote absolute right-4 top-2 text-6xl text-orange-100 dark:text-orange-900/20 rotate-180"></i>
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 italic relative z-10 leading-relaxed">
                                        {{ $service->observations }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="w-full md:w-80 flex-shrink-0 space-y-8">
                        <div class="p-8 bg-gray-50/50 dark:bg-gray-700/30 rounded-[2.5rem] border border-gray-100 dark:border-gray-600 shadow-inner relative group transition-all hover:bg-white dark:hover:bg-gray-700">
                            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] block mb-6 px-1">PREGADOR PRINCIPAL</span>
                            <div class="flex flex-col items-center text-center space-y-4">
                                @if($service->preacher)
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($service->preacher->name) }}&background=0284c7&color=fff&bold=true&size=128" class="w-24 h-24 rounded-[3rem] shadow-xl group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 border-4 border-white dark:border-gray-800 rounded-full flex items-center justify-center text-white" title="Membro Ativo">
                                            <i class="bi bi-check-lg text-[10px]"></i>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-lg font-black text-gray-900 dark:text-white leading-tight tracking-tight">{{ $service->preacher->name }}</p>
                                        <span class="inline-block px-3 py-1 bg-blue-50 dark:bg-blue-900/40 text-[9px] font-black text-blue-600 dark:text-blue-400 rounded-lg uppercase tracking-widest">Membro Interno</span>
                                    </div>
                                @else
                                    <div class="relative">
                                        <div class="w-24 h-24 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-[3rem] flex items-center justify-center text-4xl shadow-xl group-hover:rotate-6 transition-transform duration-500 border-2 border-orange-50 dark:border-orange-800">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-orange-500 border-4 border-white dark:border-gray-800 rounded-full flex items-center justify-center text-white" title="Convidado Externo">
                                            <i class="bi bi-star-fill text-[10px]"></i>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-lg font-black text-orange-600 dark:text-orange-400 leading-tight tracking-tight">{{ $service->preacher_name ?? 'Não Informado' }}</p>
                                        <span class="inline-block px-3 py-1 bg-orange-50 dark:bg-orange-900/40 text-[9px] font-black text-orange-600 dark:text-orange-400 rounded-lg uppercase tracking-widest">Convidado Especial</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="p-8 bg-blue-600 rounded-[2.5rem] shadow-xl shadow-blue-600/20 text-center relative overflow-hidden group">
                            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/10 to-transparent"></div>
                            <span class="text-[9px] font-black text-blue-100/60 uppercase tracking-[0.3em] block mb-2 relative z-10">PÚBLICO TOTAL</span>
                            <span class="text-6xl font-black text-white tracking-tighter relative z-10 group-hover:scale-110 transition-transform duration-500 block">{{ $service->total_participation }}</span>
                            <div class="mt-4 pt-4 border-t border-white/10 relative z-10 flex items-center justify-center gap-2">
                                <span class="text-[9px] font-black text-blue-100 uppercase tracking-widest">Impacto da Celebração</span>
                                <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Participation Grid -->
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-10 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                                <i class="bi bi-grid-3x3-gap-fill text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.3em]">
                                    RELATÓRIO DE PRESENÇA
                                </h3>
                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                    {{ $service->service_type === 'teaching' ? 'Distribuição por Zonas Ministeriais' : 'Consolidado de Adultos e Crianças' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        @if($service->service_type === 'teaching')
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-600">
                                        <th class="px-10 py-6 text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Zona Ministerial</th>
                                        <th class="px-6 py-6 text-center text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Membros</th>
                                        <th class="px-6 py-6 text-center text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Visitantes</th>
                                        <th class="px-6 py-6 text-center text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-[0.2em]">Líderes</th>
                                        <th class="px-6 py-6 text-center text-[9px] font-black text-orange-500 dark:text-orange-400 uppercase tracking-[0.2em]">Auxiliares</th>
                                        <th class="px-6 py-6 text-center text-[9px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-[0.2em]">Supervisores</th>
                                        <th class="px-6 py-6 text-center text-[9px] font-black text-red-600 dark:text-red-400 uppercase tracking-[0.2em]">Pastores</th>
                                        <th class="px-10 py-6 text-right text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em]">Total Geral</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    @foreach($service->zoneParticipations as $participation)
                                        <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors group">
                                            <td class="px-10 py-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-2 h-2 rounded-full bg-blue-600 group-hover:scale-150 transition-transform"></div>
                                                    <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight italic">{{ $participation->zone->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-6 text-center text-xs font-bold text-gray-600 dark:text-gray-300">{{ $participation->adults_members + $participation->children_members }}</td>
                                            <td class="px-6 py-6 text-center text-xs font-bold text-gray-600 dark:text-gray-300">{{ $participation->adults_visitors + $participation->children_visitors }}</td>
                                            <td class="px-6 py-6 text-center text-xs font-black text-orange-600 dark:text-orange-400">{{ $participation->leaders }}</td>
                                            <td class="px-6 py-6 text-center text-xs font-black text-orange-500 dark:text-orange-400">{{ $participation->auxiliary_leaders }}</td>
                                            <td class="px-6 py-6 text-center text-xs font-black text-purple-600 dark:text-purple-400">{{ $participation->supervisors }}</td>
                                            <td class="px-6 py-6 text-center text-xs font-black text-red-600 dark:text-red-400">{{ $participation->zone_pastors }}</td>
                                            <td class="px-10 py-6 text-right font-black text-sm text-blue-600 dark:text-blue-400 tracking-tighter">{{ $participation->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-blue-600 dark:bg-blue-700 text-white">
                                    <tr class="rounded-b-[3rem] overflow-hidden">
                                        <td class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em]">TOTAIS DO ENSINO</td>
                                        <td class="px-6 py-8 text-center font-black text-lg tracking-tighter">{{ $service->zoneParticipations->sum('adults_members') + $service->zoneParticipations->sum('children_members') }}</td>
                                        <td class="px-6 py-8 text-center font-black text-lg tracking-tighter">{{ $service->zoneParticipations->sum('adults_visitors') + $service->zoneParticipations->sum('children_visitors') + ($service->adults_visitors ?? 0) + ($service->children_visitors ?? 0) }}</td>
                                        <td class="px-6 py-8 text-center font-black text-lg tracking-tighter">{{ $service->zoneParticipations->sum('leaders') }}</td>
                                        <td class="px-6 py-8 text-center font-black text-lg tracking-tighter">{{ $service->zoneParticipations->sum('auxiliary_leaders') }}</td>
                                        <td class="px-6 py-8 text-center font-black text-lg tracking-tighter">{{ $service->zoneParticipations->sum('supervisors') }}</td>
                                        <td class="px-6 py-8 text-center font-black text-lg tracking-tighter">{{ $service->zoneParticipations->sum('zone_pastors') }}</td>
                                        <td class="px-10 py-8 text-right font-black text-3xl tracking-tighter italic">{{ $service->total_participation }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-600">
                                        <th class="px-10 py-6 text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Categoria de Público</th>
                                        <th class="px-10 py-6 text-center text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Membros Ativos</th>
                                        <th class="px-10 py-6 text-center text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Visitantes</th>
                                        <th class="px-10 py-6 text-center text-[9px] font-black text-green-600/60 dark:text-green-400/60 uppercase tracking-[0.2em]">Reconciliações/Decisões</th>
                                        <th class="px-10 py-6 text-right text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em]">Consolidação</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors group">
                                        <td class="px-10 py-8">
                                            <div class="flex items-center gap-4">
                                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl text-blue-600 dark:text-blue-400 group-hover:rotate-6 transition-transform">
                                                    <i class="bi bi-people-fill text-xl"></i>
                                                </div>
                                                <div>
                                                    <span class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight">Público Adulto</span>
                                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Congregação Geral</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-8 text-center font-black text-gray-700 dark:text-gray-300 text-lg">{{ $service->adults_members }}</td>
                                        <td class="px-10 py-8 text-center font-black text-gray-700 dark:text-gray-300 text-lg">{{ $service->adults_visitors }}</td>
                                        <td class="px-10 py-8 text-center font-black text-green-600 dark:text-green-400 text-lg italic shadow-inner bg-green-50/50 dark:bg-green-900/10">{{ $service->adults_salvations }}</td>
                                        <td class="px-10 py-8 text-right font-black text-blue-600 dark:text-blue-400 text-2xl tracking-tighter">{{ $service->adults_members + $service->adults_visitors + $service->adults_salvations }}</td>
                                    </tr>
                                    <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors group">
                                        <td class="px-10 py-8">
                                            <div class="flex items-center gap-4">
                                                <div class="p-3 bg-pink-50 dark:bg-pink-900/20 rounded-2xl text-pink-600 dark:text-pink-400 group-hover:rotate-6 transition-transform">
                                                    <i class="bi bi-balloon-fill text-xl"></i>
                                                </div>
                                                <div>
                                                    <span class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight italic">Life Kids</span>
                                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Crianças & Adolescentes</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-8 text-center font-black text-gray-700 dark:text-gray-300 text-lg">{{ $service->children_members }}</td>
                                        <td class="px-10 py-8 text-center font-black text-gray-700 dark:text-gray-300 text-lg">{{ $service->children_visitors }}</td>
                                        <td class="px-10 py-8 text-center font-black text-green-600 dark:text-green-400 text-lg italic shadow-inner bg-green-50/50 dark:bg-green-900/10">{{ $service->children_salvations }}</td>
                                        <td class="px-10 py-8 text-right font-black text-blue-600 dark:text-blue-400 text-2xl tracking-tighter">{{ $service->children_members + $service->children_visitors + $service->children_salvations }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-900 dark:bg-gray-950 text-white">
                                    <tr class="rounded-b-[3rem] overflow-hidden">
                                        <td class="px-10 py-10 text-[11px] font-black uppercase tracking-[0.3em]">TOTAIS CONSOLIDADOS</td>
                                        <td class="px-10 py-10 text-center font-black text-2xl tracking-tighter italic border-r border-white/5">{{ $service->adults_members + $service->children_members }}</td>
                                        <td class="px-10 py-10 text-center font-black text-2xl tracking-tighter italic border-r border-white/5">{{ $service->adults_visitors + $service->children_visitors }}</td>
                                        <td class="px-10 py-10 text-center font-black text-green-400 text-2xl tracking-tighter italic border-r border-white/5">{{ $service->adults_salvations + $service->children_salvations }}</td>
                                        <td class="px-10 py-10 text-right font-black text-4xl tracking-tighter italic bg-blue-600">{{ $service->total_participation }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Financial Data -->
            <div class="xl:col-span-4 space-y-8">
                <!-- Financial Summary Box -->
                <div class="bg-gray-900 dark:bg-black rounded-[3rem] p-10 text-white shadow-2xl shadow-gray-200/50 dark:shadow-none border border-gray-800 relative group overflow-hidden transition-all hover:shadow-green-500/5 hover:border-green-500/20">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/10 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-green-500/20 transition-all"></div>
                    <i class="bi bi-wallet2 absolute -right-6 -bottom-6 text-9xl text-white opacity-5 group-hover:scale-110 group-hover:-rotate-12 transition-all duration-700"></i>
                    <div class="relative z-10 space-y-8">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">RECITA TOTAL DA CELEBRAÇÃO</span>
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <h3 class="text-5xl font-black text-green-400 tracking-tighter italic">{{ number_format($service->total_financial, 0, ',', '.') }}</h3>
                                <span class="text-xl font-black text-gray-600 italic">MT</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest block mb-2 opacity-60">OFERTAS GERAIS</span>
                                <span class="text-lg font-black tracking-tight">{{ number_format($service->total_offerings, 0, ',', '.') }} <span class="text-[10px] opacity-40">MT</span></span>
                            </div>
                            <div class="p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest block mb-2 opacity-60">DÍZIMOS</span>
                                <span class="text-lg font-black tracking-tight">{{ number_format($service->total_tithes, 0, ',', '.') }} <span class="text-[10px] opacity-40">MT</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Offerings Breakdown List -->
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 border border-gray-100 dark:border-gray-700 overflow-hidden group">
                    <div class="p-10 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg shadow-green-600/20 group-hover:scale-110 transition-transform">
                                <i class="bi bi-cash-stack text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-[11px] font-black text-green-600 dark:text-green-400 uppercase tracking-[0.3em]">
                                    DISTRIBUIÇÃO DE OFERTAS
                                </h3>
                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                    Consolidado por Categoria
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-10 space-y-6">
                        @forelse($service->offerings as $offering)
                            <div class="flex justify-between items-center group/item hover:translate-x-1 transition-transform">
                                <div class="space-y-1">
                                    <p class="text-[11px] font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest group-hover/item:text-green-600 transition-colors">{{ $offering->offeringType->name }}</p>
                                    @if($offering->notes)
                                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 italic flex items-center gap-1">
                                            <i class="bi bi-info-circle text-[8px]"></i> {{ $offering->notes }}
                                        </p>
                                    @endif
                                </div>
                                <span class="text-sm font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($offering->amount, 0, ',', '.') }} <span class="text-[10px] opacity-40">MT</span></span>
                            </div>
                        @empty
                            <div class="py-10 text-center space-y-3 opacity-40">
                                <i class="bi bi-inbox text-4xl block"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest">Nenhuma oferta registada</p>
                            </div>
                        @endforelse
                        
                        @if($service->special_offerings_total > 0)
                            <div class="flex justify-between items-center pt-6 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-[11px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest">Ofertas Especiais</p>
                                <span class="text-sm font-black text-purple-600 dark:text-purple-400 tracking-tighter">{{ number_format($service->special_offerings_total, 0, ',', '.') }} <span class="text-[10px] opacity-40">MT</span></span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Service Tithes List -->
                @if($service->tithes->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 border border-gray-100 dark:border-gray-700 overflow-hidden group">
                        <div class="p-10 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-orange-600 flex items-center justify-center text-white shadow-lg shadow-orange-600/20 group-hover:rotate-6 transition-transform">
                                    <i class="bi bi-safe2-fill text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-[11px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-[0.3em]">
                                        DÍZIMOS NOMINATIVOS
                                    </h3>
                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                        Contribuições Identificadas
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto custom-scrollbar">
                            <div class="p-10 space-y-4">
                                @foreach($service->tithes as $tithe)
                                    <div class="flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/30 p-5 rounded-3xl border border-transparent hover:border-orange-200 dark:hover:border-orange-800 hover:bg-white dark:hover:bg-gray-700 transition-all group/tithe">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-2xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center text-xs font-black shadow-inner group-hover/tithe:scale-110 transition-transform">
                                                {{ substr($tithe->member_name ?? 'A', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $tithe->member_name ?? 'Dizimista Anónimo' }}</p>
                                                <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Contribuição Digital/Envelope</span>
                                            </div>
                                        </div>
                                        <span class="text-base font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($tithe->amount, 0, ',', '.') }} <span class="text-[10px] opacity-40">MT</span></span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Individual Offerings List -->
                @if($service->individualOfferings->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 border border-gray-100 dark:border-gray-700 overflow-hidden group">
                        <div class="p-10 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/30 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-600/20 group-hover:scale-110 transition-transform">
                                    <i class="bi bi-gift-fill text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-[11px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-[0.3em]">
                                        OFERTAS INDIVIDUAIS
                                    </h3>
                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">
                                        Doações por Membro/Visitante
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto custom-scrollbar">
                            <div class="p-10 space-y-4">
                                @foreach($service->individualOfferings as $offering)
                                    <div class="bg-gray-50/50 dark:bg-gray-700/30 p-5 rounded-3xl border border-transparent hover:border-purple-200 dark:hover:border-purple-800 hover:bg-white dark:hover:bg-gray-700 transition-all group/offering">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-2xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-black group-hover/offering:rotate-3 transition-transform">
                                                    {{ substr($offering->member_name ?? 'O', 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $offering->member_name ?? 'Doador Anónimo' }}</p>
                                                    <span class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $offering->offeringType->name }}</span>
                                                </div>
                                            </div>
                                            <span class="text-sm font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($offering->amount, 0, ',', '.') }} <span class="text-[10px] opacity-40">MT</span></span>
                                        </div>
                                        @if($offering->description)
                                            <div class="pl-12">
                                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 italic px-3 py-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-600">
                                                    "{{ $offering->description }}"
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
