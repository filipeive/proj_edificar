@extends('layouts.app')

@section('title', 'Detalhes da Celebração - Portal Life Church')
@section('page-title', 'Detalhes do Culto')
@section('page-subtitle', 'Dados consolidados da celebração de ' . $service->date->format('d/m/Y'))

@section('header-actions')
    <div class="flex items-center gap-1.5 md:hidden">
        <a href="{{ route('services.download-pdf', $service) }}"
            class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white p-2 text-red-700 shadow-sm hover:bg-red-50"
            title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        @can('update', $service)
            <a href="{{ route('services.edit', $service) }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 shadow-sm hover:bg-gray-50"
                title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endcan
        @can('delete', $service)
            <button type="button" onclick="confirmDelete('delete-form-main', 'Deseja excluir este culto?')"
                class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white p-2 text-red-600 shadow-sm hover:bg-red-50"
                title="Excluir">
                <i class="bi bi-trash"></i>
            </button>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Page Header (National Style) -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">
                    <a href="{{ route('services.index') }}" class="hover:underline">Celebrações</a>
                    <i class="bi bi-chevron-right text-[9px]"></i>
                    <span>Detalhes do Culto</span>
                </div>
                <div class="mt-0.5 flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-950 tracking-tight">
                        {{ $service->date->translatedFormat('d \d\e F, Y') }}
                    </h1>
                    <span class="px-2.5 py-0.5 {{ $service->service_type === 'teaching' ? 'bg-orange-100 text-orange-800 border-orange-200' : 'bg-blue-100 text-blue-800 border-blue-200' }} rounded-full text-xs font-black uppercase border">
                        @switch($service->service_type)
                            @case('1st') 1º Culto @break
                            @case('2nd') 2º Culto @break
                            @case('3rd') 3º Culto @break
                            @case('4th') 4º Culto @break
                            @case('teaching') Culto de Ensino @break
                            @default Especial
                        @endswitch
                    </span>
                </div>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500 flex items-center gap-1.5">
                    <i class="bi bi-clock-history"></i>
                    Registado em {{ $service->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('services.index') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                    <span>Voltar</span>
                </a>
                
                <a href="{{ route('services.download-pdf', $service) }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-red-700 hover:bg-red-50 shadow-sm">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                    <span>Exportar PDF</span>
                </a>
                @can('update', $service)
                    <a href="{{ route('services.edit', $service) }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 sm:py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                        <i class="bi bi-pencil-fill"></i>
                        <span>Editar</span>
                    </a>
                @endcan
                @can('delete', $service)
                    <form action="{{ route('services.destroy', $service) }}" method="POST" id="delete-form-main" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete('delete-form-main', 'Deseja excluir este culto?')"
                            class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-600 hover:bg-red-600 hover:text-white hover:border-red-600 transition-colors shadow-sm">
                            <i class="bi bi-trash3-fill"></i>
                            <span>Eliminar</span>
                        </button>
                    </form>
                @endcan
            </div>
        </header>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
            <!-- Left Column: Primary Content -->
            <div class="xl:col-span-8 space-y-5">
                <!-- Theme & Message -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm flex flex-col md:flex-row gap-5">
                    <div class="flex-1 space-y-4">
                        @if($service->theme)
                            <div class="space-y-1">
                                <span class="text-[10px] font-black text-orange-700 uppercase tracking-wider block"><i class="bi bi-bookmark-star-fill mr-1"></i> Tema da Celebração</span>
                                <h2 class="text-lg sm:text-xl font-black text-gray-950 leading-snug">"{{ $service->theme }}"</h2>
                            </div>
                        @endif

                        <div class="space-y-1.5">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider block"><i class="bi bi-body-text mr-1"></i> Resumo da Mensagem</span>
                            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                                {!! nl2br(e($service->message ?? 'Nenhuma mensagem disponível para esta celebração.')) !!}
                            </div>
                        </div>

                        @if($service->observations)
                            <div class="space-y-1.5 pt-2">
                                <span class="text-[10px] font-black text-orange-700 uppercase tracking-wider block"><i class="bi bi-sticky-fill mr-1"></i> Observações do Secretariado</span>
                                <div class="p-3.5 bg-orange-50/60 rounded-lg border-l-4 border-orange-500">
                                    <p class="text-xs sm:text-sm font-medium text-gray-700 italic leading-relaxed">
                                        {{ $service->observations }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="w-full md:w-64 flex-shrink-0 space-y-3">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-center">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider block mb-3">Pregador Principal</span>
                            <div class="flex flex-col items-center space-y-2.5">
                                @if($service->preacher)
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($service->preacher->name) }}&background=0284c7&color=fff&bold=true&size=128" class="w-16 h-16 rounded-full shadow-sm">
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full flex items-center justify-center text-white" title="Membro Ativo">
                                            <i class="bi bi-check-lg text-[9px]"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-950 leading-tight">{{ $service->preacher->name }}</p>
                                        <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-[10px] font-bold text-blue-800 rounded-md uppercase">Membro Interno</span>
                                    </div>
                                @else
                                    <div class="w-16 h-16 bg-orange-100 text-orange-700 rounded-full flex items-center justify-center text-2xl shadow-sm border border-orange-200">
                                        <i class="bi bi-person-badge-fill"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-orange-700 leading-tight">{{ $service->preacher_name ?? 'Não Informado' }}</p>
                                        <span class="inline-block mt-1 px-2 py-0.5 bg-orange-100 text-[10px] font-bold text-orange-800 rounded-md uppercase">Convidado Especial</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 bg-blue-600 rounded-xl shadow-sm text-center text-white">
                            <span class="text-[10px] font-black text-blue-100 uppercase tracking-wider block">Participantes Total</span>
                            <span class="text-3xl font-black tracking-tight block mt-1">{{ $service->total_participation }}</span>
                            <span class="text-[10px] font-medium text-blue-100 block mt-1">Impacto da Celebração</span>
                        </div>
                    </div>
                </div>

                <!-- Detailed Participation Grid -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white text-sm">
                                <i class="bi bi-grid-3x3-gap-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">
                                    Relatório de Presença
                                </h3>
                                <p class="text-[11px] text-gray-500">
                                    {{ $service->service_type === 'teaching' ? 'Distribuição por Zonas Ministeriais' : 'Consolidado de Adultos e Crianças' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        @if($service->service_type === 'teaching')
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3">Zona Ministerial</th>
                                        <th class="px-4 py-3 text-center">Membros</th>
                                        <th class="px-4 py-3 text-center">Visitantes</th>
                                        <th class="px-4 py-3 text-center text-orange-700">Líderes</th>
                                        <th class="px-4 py-3 text-center text-orange-600">Auxiliares</th>
                                        <th class="px-4 py-3 text-center text-purple-700">Supervisores</th>
                                        <th class="px-4 py-3 text-center text-red-700">Pastores</th>
                                        <th class="px-4 py-3 text-right text-blue-700">Total Geral</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @foreach($service->zoneParticipations as $participation)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 font-bold text-gray-900">{{ $participation->zone->name }}</td>
                                            <td class="px-4 py-3 text-center text-gray-700">{{ $participation->adults_members + $participation->children_members }}</td>
                                            <td class="px-4 py-3 text-center text-gray-700">{{ $participation->adults_visitors + $participation->children_visitors }}</td>
                                            <td class="px-4 py-3 text-center font-bold text-orange-700">{{ $participation->leaders }}</td>
                                            <td class="px-4 py-3 text-center font-bold text-orange-600">{{ $participation->auxiliary_leaders }}</td>
                                            <td class="px-4 py-3 text-center font-bold text-purple-700">{{ $participation->supervisors }}</td>
                                            <td class="px-4 py-3 text-center font-bold text-red-700">{{ $participation->zone_pastors }}</td>
                                            <td class="px-4 py-3 text-right font-black text-blue-700">{{ $participation->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-900 text-white text-xs">
                                    <tr>
                                        <td class="px-4 py-3 font-black uppercase tracking-wider">Totais do Ensino</td>
                                        <td class="px-4 py-3 text-center font-black">{{ $service->zoneParticipations->sum('adults_members') + $service->zoneParticipations->sum('children_members') }}</td>
                                        <td class="px-4 py-3 text-center font-black">{{ $service->zoneParticipations->sum('adults_visitors') + $service->zoneParticipations->sum('children_visitors') + ($service->adults_visitors ?? 0) + ($service->children_visitors ?? 0) }}</td>
                                        <td class="px-4 py-3 text-center font-black">{{ $service->zoneParticipations->sum('leaders') }}</td>
                                        <td class="px-4 py-3 text-center font-black">{{ $service->zoneParticipations->sum('auxiliary_leaders') }}</td>
                                        <td class="px-4 py-3 text-center font-black">{{ $service->zoneParticipations->sum('supervisors') }}</td>
                                        <td class="px-4 py-3 text-center font-black">{{ $service->zoneParticipations->sum('zone_pastors') }}</td>
                                        <td class="px-4 py-3 text-right font-black text-base text-orange-400">{{ $service->total_participation }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3">Categoria de Participante</th>
                                        <th class="px-4 py-3 text-center">Membros Ativos</th>
                                        <th class="px-4 py-3 text-center">Visitantes</th>
                                        <th class="px-4 py-3 text-center text-green-700">Reconciliações/Salvações</th>
                                        <th class="px-4 py-3 text-right text-blue-700">Consolidação</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                                    <i class="bi bi-people-fill"></i>
                                                </div>
                                                <div>
                                                    <span class="text-xs sm:text-sm font-black text-gray-900">Participantes Adultos</span>
                                                    <p class="text-[10px] text-gray-500">Congregação Geral</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $service->adults_members }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $service->adults_visitors }}</td>
                                        <td class="px-4 py-3 text-center font-black text-green-700 bg-green-50/40">{{ $service->adults_salvations }}</td>
                                        <td class="px-4 py-3 text-right font-black text-blue-700 text-base">{{ $service->adults_members + $service->adults_visitors + $service->adults_salvations }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="p-2 bg-pink-50 rounded-lg text-pink-600">
                                                    <i class="bi bi-balloon-fill"></i>
                                                </div>
                                                <div>
                                                    <span class="text-xs sm:text-sm font-black text-gray-900">Life Kids</span>
                                                    <p class="text-[10px] text-gray-500">Crianças & Adolescentes</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $service->children_members }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $service->children_visitors }}</td>
                                        <td class="px-4 py-3 text-center font-black text-green-700 bg-green-50/40">{{ $service->children_salvations }}</td>
                                        <td class="px-4 py-3 text-right font-black text-blue-700 text-base">{{ $service->children_members + $service->children_visitors + $service->children_salvations }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-900 text-white text-xs">
                                    <tr>
                                        <td class="px-4 py-3 font-black uppercase tracking-wider">Totais Consolidados</td>
                                        <td class="px-4 py-3 text-center font-black text-sm">{{ $service->adults_members + $service->children_members }}</td>
                                        <td class="px-4 py-3 text-center font-black text-sm">{{ $service->adults_visitors + $service->children_visitors }}</td>
                                        <td class="px-4 py-3 text-center font-black text-green-400 text-sm">{{ $service->adults_salvations + $service->children_salvations }}</td>
                                        <td class="px-4 py-3 text-right font-black text-lg bg-blue-600">{{ $service->total_participation }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Financial Data -->
            <div class="xl:col-span-4 space-y-5">
                <!-- Financial Summary Box -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Receita Total da Celebração</span>
                        <i class="bi bi-wallet2 text-emerald-700 text-base"></i>
                    </div>
                    <p class="mt-1 text-2xl sm:text-3xl font-black text-emerald-700 tracking-tight">
                        {{ number_format($service->total_financial, 0, ',', '.') }} <span class="text-sm font-bold text-gray-400">MT</span>
                    </p>

                    <div class="grid grid-cols-2 gap-3 mt-4 pt-3 border-t border-gray-100">
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-[10px] font-bold text-gray-500 uppercase block">Ofertas Gerais</span>
                            <span class="text-sm font-black text-gray-900 mt-0.5 block">{{ number_format($service->total_offerings, 0, ',', '.') }} MT</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-[10px] font-bold text-gray-500 uppercase block">Dízimos</span>
                            <span class="text-sm font-black text-gray-900 mt-0.5 block">{{ number_format($service->total_tithes, 0, ',', '.') }} MT</span>
                        </div>
                    </div>
                </div>

                <!-- Offerings Breakdown List -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-gray-200 bg-gray-50/50 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white text-sm">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Distribuição de Ofertas</h3>
                            <p class="text-[11px] text-gray-500">Consolidado por Categoria</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($service->offerings as $offering)
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="text-xs font-bold text-gray-800">{{ $offering->offeringType->name }}</p>
                                    @if($offering->notes)
                                        <p class="text-[10px] text-gray-500 italic">{{ $offering->notes }}</p>
                                    @endif
                                </div>
                                <span class="text-xs font-black text-gray-900">{{ number_format($offering->amount, 0, ',', '.') }} MT</span>
                            </div>
                        @empty
                            <div class="py-6 text-center text-gray-400">
                                <i class="bi bi-inbox text-2xl block mb-1"></i>
                                <p class="text-xs font-semibold">Nenhuma oferta registada</p>
                            </div>
                        @endforelse
                        
                        @if($service->special_offerings_total > 0)
                            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                <p class="text-xs font-black text-purple-700 uppercase">Ofertas Especiais</p>
                                <span class="text-xs font-black text-purple-700">{{ number_format($service->special_offerings_total, 0, ',', '.') }} MT</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Service Tithes List -->
                @if($service->tithes->count() > 0)
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 py-3.5 border-b border-gray-200 bg-gray-50/50 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-orange-600 flex items-center justify-center text-white text-sm">
                                <i class="bi bi-safe2-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Dízimos Nominativos</h3>
                                <p class="text-[11px] text-gray-500">Contribuições Identificadas</p>
                            </div>
                        </div>
                        <div class="max-h-80 overflow-y-auto p-4 space-y-2.5">
                            @foreach($service->tithes as $tithe)
                                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-black">
                                            {{ substr($tithe->member_name ?? 'A', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-800">{{ $tithe->member_name ?? 'Dizimista Anónimo' }}</p>
                                            <span class="text-[10px] text-gray-500">Contribuição Digital/Envelope</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-black text-gray-900">{{ number_format($tithe->amount, 0, ',', '.') }} MT</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Individual Offerings List -->
                @if($service->individualOfferings->count() > 0)
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 py-3.5 border-b border-gray-200 bg-gray-50/50 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center text-white text-sm">
                                <i class="bi bi-gift-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Ofertas Individuais</h3>
                                <p class="text-[11px] text-gray-500">Doações por Membro/Visitante</p>
                            </div>
                        </div>
                        <div class="max-h-80 overflow-y-auto p-4 space-y-2.5">
                            @foreach($service->individualOfferings as $offering)
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 space-y-1.5">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-black">
                                                {{ substr($offering->member_name ?? 'O', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">{{ $offering->member_name ?? 'Doador Anónimo' }}</p>
                                                <span class="text-[10px] text-gray-500">Oferta Individual</span>
                                            </div>
                                        </div>
                                        <span class="text-xs font-black text-gray-900">{{ number_format($offering->amount, 0, ',', '.') }} MT</span>
                                    </div>
                                    @if($offering->description)
                                        <p class="text-[11px] text-gray-600 italic pl-10">"{{ $offering->description }}"</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
