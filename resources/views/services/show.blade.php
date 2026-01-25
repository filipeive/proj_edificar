@extends('layouts.app')

@section('title', 'Detalhes da Celebração - Portal Life Church')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('services.download-pdf', $service) }}"
            class="text-gray-600 hover:text-orange-600 p-2.5 hover:bg-orange-50 rounded-xl transition-all duration-300 border border-transparent hover:border-orange-100"
            title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf text-2xl"></i>
        </a>
        @can('update', $service)
            <a href="{{ route('services.edit', $service) }}"
                class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100"
                title="Editar">
                <i class="bi bi-pencil-square text-2xl"></i>
            </a>
        @endcan
        @can('delete', $service)
            <button type="button" onclick="confirmDelete('delete-form', 'Deseja excluir este culto?')"
                class="text-gray-600 hover:text-red-600 p-2.5 hover:bg-red-50 rounded-xl transition-all duration-300 border border-transparent hover:border-red-100"
                title="Excluir">
                <i class="bi bi-trash text-2xl"></i>
            </button>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <a href="{{ route('services.index') }}" class="hover:underline">Celebrações</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span>Ver Detalhes</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                    {{ $service->date->format('d \d\e F, Y') }}
                </h1>
                <p class="text-gray-500 font-medium">
                    @switch($service->service_type)
                        @case('1st') 1º Culto @break
                        @case('2nd') 2º Culto @break
                        @case('3rd') 3º Culto @break
                        @case('4th') 4º Culto @break
                        @default Especial
                    @endswitch
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 hidden md:flex">
                <a href="{{ route('services.download-pdf', $service) }}" class="flex items-center bg-red-50 text-red-600 px-6 py-4 rounded-2xl hover:bg-red-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest shadow-sm">
                    <i class="bi bi-file-earmark-pdf text-lg mr-2"></i>
                    Exportar PDF
                </a>
                @can('update', $service)
                    <a href="{{ route('services.edit', $service) }}" class="flex items-center bg-blue-50 text-blue-600 px-6 py-4 rounded-2xl hover:bg-blue-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest shadow-sm">
                        <i class="bi bi-pencil-square text-lg mr-2"></i>
                        Editar
                    </a>
                @endcan
                @can('delete', $service)
                    <form action="{{ route('services.destroy', $service) }}" method="POST"
                        id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete('delete-form', 'Deseja excluir este culto?')"
                            class="flex items-center bg-gray-50 text-gray-400 px-6 py-4 rounded-2xl hover:bg-red-500 hover:text-white transition-all font-black text-xs uppercase tracking-widest shadow-sm">
                            <i class="bi bi-trash text-lg mr-2"></i>
                            Excluir
                        </button>
                    </form>
                @endcan
            </div>
            
            <form action="{{ route('services.destroy', $service) }}" method="POST" id="delete-form" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <!-- Left Column: Primary Content -->
            <div class="xl:col-span-8 space-y-8">
                <!-- Theme & Message -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row gap-8">
                    <div class="flex-1 space-y-6">
                        @if($service->theme)
                            <div class="space-y-2">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Tema da Celebração</span>
                                <h2 class="text-2xl font-black text-gray-900 leading-tight">"{{ $service->theme }}"</h2>
                            </div>
                        @endif

                        <div class="space-y-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Resumo da Mensagem</span>
                            <div class="prose prose-blue max-w-none text-gray-600 font-medium leading-relaxed italic">
                                {!! nl2br(e($service->message ?? 'Nenhuma mensagem registrada.')) !!}
                            </div>
                        </div>

                        @if($service->observations)
                            <div class="space-y-2">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Observações do Secretariado</span>
                                <p class="text-sm font-medium text-gray-500 italic px-4 py-3 bg-gray-50 rounded-2xl border-l-4 border-gray-200">
                                    {{ $service->observations }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="w-full md:w-72 flex-shrink-0 space-y-6">
                        <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Pregador Responsável</span>
                            <div class="flex items-center gap-4">
                                @if($service->preacher)
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($service->preacher->name) }}&background=0D6EFD&color=fff" class="w-12 h-12 rounded-full shadow-sm">
                                    <div>
                                        <p class="text-sm font-black text-gray-900 leading-tight">{{ $service->preacher->name }}</p>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Membro Interno</span>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-xl shadow-sm">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-orange-600 leading-tight">{{ $service->preacher_name ?? 'N/A' }}</p>
                                        <span class="text-[10px] font-bold text-orange-400 uppercase tracking-tighter">Convidado Especial</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="p-6 bg-blue-50/50 rounded-[2rem] border border-blue-50 text-center">
                            <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest block mb-1">Participação Total</span>
                            <span class="text-5xl font-black text-blue-600 tracking-tighter">{{ $service->total_participation }}</span>
                        </div>
                    </div>
                </div>

                <!-- Detailed Participation Grid -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">
                            {{ $service->service_type === 'teaching' ? 'Participação por Zona' : 'Grelha de Presença Detalhada' }}
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        @if($service->service_type === 'teaching')
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Zona</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Visit.</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-orange-600 uppercase tracking-widest">Líderes</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-orange-400 uppercase tracking-widest">Timótio</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-purple-600 uppercase tracking-widest">Superv.</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-red-600 uppercase tracking-widest">Pastores Z.</th>
                                        <th class="px-10 py-5 text-right text-[10px] font-black text-blue-600 uppercase tracking-widest">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($service->zoneParticipations as $participation)
                                        <tr class="hover:bg-gray-50/50 transition-colors text-xs font-bold">
                                            <td class="px-10 py-6 font-black text-gray-900 uppercase tracking-tight">{{ $participation->zone->name }}</td>
                                            <td class="px-10 py-6 text-center text-gray-600">{{ $participation->adults_members + $participation->children_members }}</td>
                                            <td class="px-10 py-6 text-center text-gray-600">{{ $participation->adults_visitors + $participation->children_visitors }}</td>
                                            <td class="px-10 py-6 text-center text-orange-600">{{ $participation->leaders }}</td>
                                            <td class="px-10 py-6 text-center text-orange-400">{{ $participation->auxiliary_leaders }}</td>
                                            <td class="px-10 py-6 text-center text-purple-600">{{ $participation->supervisors }}</td>
                                            <td class="px-10 py-6 text-center text-red-600">{{ $participation->zone_pastors }}</td>
                                            <td class="px-10 py-6 text-right font-black text-blue-600">{{ $participation->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-blue-600 text-white">
                                    <tr>
                                        <td class="px-10 py-6 text-[10px] font-black uppercase tracking-widest">TOTAIS ENSINO</td>
                                        <td class="px-10 py-6 text-center font-black">{{ $service->zoneParticipations->sum('adults_members') + $service->zoneParticipations->sum('children_members') }}</td>
                                        <td class="px-10 py-6 text-center font-black">{{ $service->zoneParticipations->sum('adults_visitors') + $service->zoneParticipations->sum('children_visitors') + ($service->adults_visitors ?? 0) + ($service->children_visitors ?? 0) }}</td>
                                        <td class="px-10 py-6 text-center font-black">{{ $service->zoneParticipations->sum('leaders') }}</td>
                                        <td class="px-10 py-6 text-center font-black">{{ $service->zoneParticipations->sum('auxiliary_leaders') }}</td>
                                        <td class="px-10 py-6 text-center font-black">{{ $service->zoneParticipations->sum('supervisors') }}</td>
                                        <td class="px-10 py-6 text-center font-black">{{ $service->zoneParticipations->sum('zone_pastors') }}</td>
                                        <td class="px-10 py-6 text-right font-black text-xl tracking-tighter">{{ $service->total_participation }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Categoria</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Visitantes</th>
                                        <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Decisões</th>
                                        <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-10 py-6 font-black text-gray-900 uppercase tracking-tight">Adultos</td>
                                        <td class="px-10 py-6 text-center font-bold text-gray-600">{{ $service->adults_members }}</td>
                                        <td class="px-10 py-6 text-center font-bold text-gray-600">{{ $service->adults_visitors }}</td>
                                        <td class="px-10 py-6 text-center font-black text-green-600">{{ $service->adults_salvations }}</td>
                                        <td class="px-10 py-6 text-right font-black text-blue-600">{{ $service->adults_members + $service->adults_visitors + $service->adults_salvations }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-10 py-6 font-black text-gray-900 uppercase tracking-tight">Crianças (Life Kids)</td>
                                        <td class="px-10 py-6 text-center font-bold text-gray-600">{{ $service->children_members }}</td>
                                        <td class="px-10 py-6 text-center font-bold text-gray-600">{{ $service->children_visitors }}</td>
                                        <td class="px-10 py-6 text-center font-black text-green-600">{{ $service->children_salvations }}</td>
                                        <td class="px-10 py-6 text-right font-black text-blue-600">{{ $service->children_members + $service->children_visitors + $service->children_salvations }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-900 text-white">
                                    <tr>
                                        <td class="px-10 py-6 text-[10px] font-black uppercase tracking-widest">TOTAIS CONSOLIDADOS</td>
                                        <td class="px-10 py-6 text-center font-black">{{ $service->adults_members + $service->children_members }}</td>
                                        <td class="px-10 py-6 text-center font-black">{{ $service->adults_visitors + $service->children_visitors }}</td>
                                        <td class="px-10 py-6 text-center font-black text-green-400">{{ $service->adults_salvations + $service->children_salvations }}</td>
                                        <td class="px-10 py-6 text-right font-black text-xl tracking-tighter">{{ $service->total_participation }}</td>
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
                <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-gray-200 relative group overflow-hidden">
                    <i class="bi bi-wallet2 absolute -right-4 -bottom-4 text-8xl text-white opacity-5 group-hover:scale-110 transition-transform duration-700"></i>
                    <div class="relative z-10 space-y-6">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-1">Arrecadação Total</span>
                            <h3 class="text-4xl font-black text-green-400 tracking-tighter">{{ number_format($service->total_financial, 0, ',', '.') }}<span class="text-sm ml-1 opacity-50">MT</span></h3>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Ofertas (Geral)</span>
                                <span class="text-sm font-black">{{ number_format($service->total_offerings, 0, ',', '.') }} MT</span>
                            </div>
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Dízimos</span>
                                <span class="text-sm font-black">{{ number_format($service->total_tithes, 0, ',', '.') }} MT</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Offerings Breakdown List -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Detalhamento de Ofertas</h3>
                    </div>
                    <div class="p-8 space-y-4">
                        @foreach($service->offerings as $offering)
                            <div class="flex justify-between items-center group">
                                <div class="space-y-0.5">
                                    <p class="text-sm font-black text-gray-800 uppercase tracking-tight group-hover:text-blue-600 transition-colors">{{ $offering->offeringType->name }}</p>
                                    @if($offering->notes)
                                        <p class="text-[10px] font-bold text-gray-400 italic">{{ $offering->notes }}</p>
                                    @endif
                                </div>
                                <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($offering->amount, 0, ',', '.') }} MT</span>
                            </div>
                        @endforeach
                        
                        @if($service->special_offerings_total > 0)
                            <div class="flex justify-between items-center pt-2 border-t border-gray-50">
                                <p class="text-sm font-black text-purple-600 uppercase tracking-tight">Ofertas Especiais</p>
                                <span class="text-sm font-black text-purple-600 tracking-tighter">{{ number_format($service->special_offerings_total, 0, ',', '.') }} MT</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Service Tithes List -->
                @if($service->tithes->count() > 0)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Dízimos Nominativos</h3>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            <div class="p-8 space-y-4">
                                @foreach($service->tithes as $tithe)
                                    <div class="flex justify-between items-center bg-gray-50/50 p-4 rounded-2xl hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-black">
                                                {{ substr($tithe->member_name ?? 'A', 0, 1) }}
                                            </div>
                                            <p class="text-xs font-bold text-gray-700 uppercase tracking-tighter">{{ $tithe->member_name ?? 'Anônimo' }}</p>
                                        </div>
                                        <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($tithe->amount, 0, ',', '.') }} MT</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Individual Offerings List -->
                @if($service->individualOfferings->count() > 0)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Ofertas Individuais</h3>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            <div class="p-8 space-y-4">
                                @foreach($service->individualOfferings as $offering)
                                    <div class="bg-gray-50/50 p-4 rounded-2xl hover:bg-gray-50 transition-colors space-y-2">
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-[10px] font-black">
                                                    <i class="bi bi-gift-fill"></i>
                                                </div>
                                                <p class="text-xs font-bold text-gray-700 uppercase tracking-tighter">{{ $offering->member_name ?? 'Anônimo' }}</p>
                                            </div>
                                            <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($offering->amount, 0, ',', '.') }} MT</span>
                                        </div>
                                        @if($offering->description)
                                            <p class="text-[10px] font-medium text-gray-500 italic ml-11">"{{ $offering->description }}"</p>
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