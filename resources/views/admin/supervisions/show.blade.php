@extends('layouts.app')

@section('title', "Supervisão $supervision->name - Portal Life Church")
@section('page-title', $supervision->name)
@section('page-subtitle', "Gestão da supervisão e suas células")

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('cells.create') }}?supervision_id={{ $supervision->id }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100"
            title="Criar Célula">
            <i class="bi bi-plus-circle text-2xl"></i>
        </a>
        <a href="{{ route('supervisions.edit', $supervision) }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100"
            title="Editar Estrutura">
            <i class="bi bi-pencil-square text-2xl"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8">
        <!-- Header & Stats Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Info Supervisão -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Supervisão</span>
                </div>
                <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ $supervision->name }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                        Zona: {{ $supervision->zone->name }}
                    </span>
                </div>
            </div>

            <!-- Total Células -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                <p class="text-5xl font-black text-purple-600 tracking-tighter">{{ $supervision->cells->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Células Ativas</p>
            </div>

            <!-- Total Membros -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                @php $memberCount = $supervision->cells->flatMap(function($c) { return $c->members()->where('is_active', true)->get(); })->unique('id')->count(); @endphp
                <p class="text-5xl font-black text-blue-600 tracking-tighter">{{ $memberCount }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Corpo de Membros</p>
            </div>

            <!-- Total Arrecadado -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-4xl font-black text-green-600 tracking-tighter">
                        {{ number_format($supervision->getTotalContributedThisMonth(), 0, ',', '.') }}<span class="text-sm ml-1 uppercase">MT</span>
                    </p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Arrecadado este mês</p>
                </div>
                <div class="absolute -right-4 -bottom-4 text-8xl text-green-50 opacity-50"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 space-y-6">
                <!-- Células da Supervisão -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 md:p-10 flex justify-between items-center border-b border-gray-50">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tighter">Unidades de Células</h3>
                            <p class="text-sm font-medium text-gray-400">Distribuição das células sob esta supervisão</p>
                        </div>
                        <a href="{{ route('cells.create') }}?supervision_id={{ $supervision->id }}" 
                           class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-5 py-3 rounded-2xl flex items-center transition-all font-bold text-sm">
                            <i class="bi bi-plus-lg mr-2"></i> Criar Célula
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Unidade</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Liderança</th>
                                    <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Contribuição</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($supervision->cells as $cell)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                                    {{ substr($cell->name, 0, 1) }}
                                                </div>
                                                <p class="text-sm font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">{{ $cell->name }}</p>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6">
                                            @if($cell->leader)
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-800">{{ $cell->leader->name }}</span>
                                                    <span class="text-[10px] text-gray-400 font-medium font-mono uppercase">Líder Principal</span>
                                                </div>
                                            @else
                                                <span class="text-gray-300 italic text-sm">Sem líder</span>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[10px] font-black tracking-widest">
                                                {{ $cell->members()->where('is_active', true)->count() }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-right font-black text-gray-900">
                                            {{ number_format($cell->getTotalContributedThisMonth(), 0, ',', '.') }} MT
                                        </td>
                                        <td class="px-10 py-6 text-right">
                                            <a href="{{ route('cells.show', $cell) }}" class="text-gray-300 hover:text-blue-600 transition-colors">
                                                <i class="bi bi-chevron-right text-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-16 text-center text-gray-400 font-medium italic">
                                            Nenhuma célula registrada nesta supervisão.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Coluna de Ações Rápidas (Hidden on Mobile) -->
            <div class="space-y-6 hidden md:block">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Gestão Regional</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('supervisions.edit', $supervision) }}"
                            class="w-full bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-pencil-square"></i> Editar Estrutura
                        </a>
                        <a href="{{ route('supervisions.index') }}"
                            class="w-full bg-gray-50 text-gray-500 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-arrow-left"></i> Voltar à Lista
                        </a>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-900 to-indigo-900 rounded-[2.5rem] shadow-xl p-10 text-white relative overflow-hidden">
                    <div class="relative z-10 space-y-6">
                        <p class="text-[10px] font-black text-purple-300 uppercase tracking-[0.2em]">Desempenho Geral</p>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-purple-100">Crescimento da Supervisão</p>
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-black tracking-tighter text-white">{{ $supervision->cells->count() }}</span>
                                <span class="text-xs font-bold text-purple-300 mb-1">Células Ativas</span>
                            </div>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 mt-4">
                            <div class="bg-purple-400 h-1.5 rounded-full" style="width: 80%"></div>
                        </div>
                    </div>
                    <i class="bi bi-briefcase-fill absolute -right-4 -bottom-4 text-9xl text-white opacity-5"></i>
                </div>
            </div>
        </div>
    </div>
@endsection