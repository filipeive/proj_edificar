@extends('layouts.app')

@section('title', 'Gestão de Cultos - Portal Life Church')

@section('content')
    <div class="space-y-8" x-data="{ view: localStorage.getItem('serviceView') || 'grid' }" x-init="$watch('view', val => localStorage.setItem('serviceView', val))">
        <!-- Header Section -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <i class="bi bi-calendar-event"></i>
                    <span>Eclesiástico</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Celebrações</h1>
                <p class="text-gray-500 font-medium">Controle de participação e financeiro dos cultos</p>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- View Toggle -->
                <div class="flex bg-gray-100 p-1.5 rounded-2xl mr-4">
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                        class="p-2.5 rounded-xl transition-all flex items-center gap-2 font-bold text-xs">
                        <i class="bi bi-grid-fill"></i>
                        <span class="hidden sm:inline">Grid</span>
                    </button>
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                        class="p-2.5 rounded-xl transition-all flex items-center gap-2 font-bold text-xs">
                        <i class="bi bi-list-task"></i>
                        <span class="hidden sm:inline">Lista</span>
                    </button>
                </div>

                <a href="{{ route('services.create') }}" class="group flex items-center bg-blue-600 text-white px-8 py-4 rounded-[1.5rem] hover:bg-blue-700 transition-all font-black shadow-xl shadow-blue-200 hover:-translate-y-1 text-sm md:text-base">
                    <i class="bi bi-plus-lg mr-2 text-lg"></i>
                    Registrar Culto
                </a>
            </div>
        </div>

        <!-- Services Grid View -->
        <div x-show="view === 'grid'" x-transition.fade.duration.300ms class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            @foreach($services as $service)
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:shadow-gray-200/50 transition-all group flex flex-col">
                    <div class="p-8 space-y-6 flex-1">
                        <!-- Card Header -->
                        <div class="flex justify-between items-start">
                            <div class="space-y-1">
                                <div class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest inline-block mb-1">
                                    @switch($service->service_type)
                                        @case('1st') 1º Culto @break
                                        @case('2nd') 2º Culto @break
                                        @case('3rd') 3º Culto @break
                                        @case('4th') 4º Culto @break
                                        @default Especial
                                    @endswitch
                                </div>
                                <h3 class="text-xl font-black text-gray-900">{{ $service->date->format('d/m/Y') }}</h3>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">
                                    Pregador: <span class="text-xs font-black {{ ($service->preacher_id === null && $service->preacher_name) ? 'text-orange-600 bg-orange-50 px-2 py-0.5 rounded-lg' : 'text-gray-600' }}">
                                        @if($service->preacher)
                                            {{ $service->preacher->name }}
                                        @else
                                            {{ $service->preacher_name ?? 'N/A' }}
                                            @if($service->preacher_id === null && $service->preacher_name)
                                                <i class="bi bi-person-badge-fill ml-1" title="Convidado Externo"></i>
                                            @endif
                                        @endif
                                    </span>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Participação</span>
                                <span class="text-2xl font-black text-blue-600">{{ $service->total_participation }}</span>
                            </div>
                        </div>

                        <!-- Theme -->
                        @if($service->theme)
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 min-h-[80px] flex items-center justify-center text-center">
                                <span class="text-sm font-black text-gray-700 italic">"{{ $service->theme }}"</span>
                            </div>
                        @endif

                        <!-- Financial Breakdown -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-green-50 rounded-2xl border border-green-100">
                                <span class="text-[9px] font-black text-green-600 uppercase tracking-widest block mb-1">Ofertas</span>
                                <span class="text-sm font-black text-green-700">{{ number_format($service->total_offerings + $service->special_offerings_total, 2) }} MT</span>
                            </div>
                            <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                                <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest block mb-1">Dízimos</span>
                                <span class="text-sm font-black text-blue-700">{{ number_format($service->total_tithes, 2) }} MT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex gap-2">
                            <a href="{{ route('services.show', $service) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <i class="bi bi-info-circle text-lg"></i>
                                        </a>
                            <a href="{{ route('services.download-pdf', $service) }}" class="p-3 bg-white text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border border-gray-100" title="Baixar PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <a href="{{ route('services.edit', $service) }}" class="p-3 bg-white text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-gray-100" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                        <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este registro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-3 bg-white text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border border-gray-100">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Services List View -->
        <div x-show="view === 'list'" x-transition.fade.duration.300ms class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pregador</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Partic.</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Financ. Total</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($services as $service)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-8 py-6 font-black text-gray-900">{{ $service->date->format('d/m/Y') }}</td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold uppercase">
                                        {{ $service->service_type }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="font-bold {{ ($service->preacher_id === null && $service->preacher_name) ? 'text-orange-600 bg-orange-50 px-2 py-0.5 rounded-lg' : 'text-gray-600' }}">
                                        @if($service->preacher)
                                            {{ $service->preacher->name }}
                                        @else
                                            {{ $service->preacher_name ?? 'N/A' }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center font-black text-blue-600">{{ $service->total_participation }}</td>
                                <td class="px-8 py-6 text-right font-black text-blue-600">{{ number_format($service->total_financial, 0, ',', '.') }} MT</td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2 transition-all">
                                        <!-- detalhes do culto -->
                                        <a href="{{ route('services.show', $service) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <i class="bi bi-info-circle text-lg"></i>
                                        </a>
                                        <!-- download pdf -->
                                        <a href="{{ route('services.download-pdf', $service) }}" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="bi bi-file-earmark-pdf text-lg"></i>
                                        </a>
                                        <!-- editar -->
                                        <a href="{{ route('services.edit', $service) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </a>
                                        <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Excluir?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <i class="bi bi-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $services->links() }}
        </div>
    </div>
@endsection