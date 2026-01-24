@extends('layouts.app')

@section('title', "Zona $zone->name - Portal Life Church")

@section('content')
    <div class="space-y-8" x-data="{ activeTab: 'supervisions' }">
        <!-- Header & Stats Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Info Zona -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Zona Pastoral</span>
                </div>
                <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ $zone->name }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold uppercase tracking-widest">
                        Pastor: {{ $zone->pastor->name ?? 'Pendente' }}
                    </span>
                </div>
            </div>

            <!-- Total Supervisões -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                <p class="text-5xl font-black text-purple-600 tracking-tighter">{{ $zone->supervisions->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Supervisões</p>
            </div>

            <!-- Total Células -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                <p class="text-5xl font-black text-blue-600 tracking-tighter">{{ $cells->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Unidades de Células</p>
            </div>

            <!-- Total Membros -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-5xl font-black text-green-600 tracking-tighter">{{ $members->count() }}</p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Membros Totais</p>
                </div>
                <div class="absolute -right-4 -bottom-4 text-8xl text-green-50 opacity-50"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 space-y-8">
                <!-- Tab Logic -->
                <div class="flex items-center gap-4 bg-white p-2 rounded-[2rem] shadow-sm border border-gray-100 w-fit">
                    <button @click="activeTab = 'supervisions'"
                        :class="activeTab === 'supervisions' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all">
                        Supervisões
                    </button>
                    <button @click="activeTab = 'cells'"
                        :class="activeTab === 'cells' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all">
                        Células
                    </button>
                    <button @click="activeTab = 'members'"
                        :class="activeTab === 'members' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-8 py-3 rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all">
                        Membros
                    </button>
                </div>

                <!-- Tab Content: Supervisões -->
                <div x-show="activeTab === 'supervisions'" x-transition.fade class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($zone->supervisions as $supervision)
                        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-blue-100 transition-all group">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-2xl">
                                    {{ substr($supervision->name, 0, 1) }}
                                </div>
                                <a href="{{ route('supervisions.show', $supervision) }}" class="text-gray-300 hover:text-blue-600 transition-colors">
                                    <i class="bi bi-arrow-up-right-circle text-2xl"></i>
                                </a>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $supervision->name }}</h3>
                            <div class="flex gap-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-400 uppercase">Células</span>
                                    <span class="text-lg font-black text-gray-900">{{ $supervision->cells->count() }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-400 uppercase">Membros</span>
                                    <span class="text-lg font-black text-gray-900">{{ $supervision->cells->flatMap(fn($c) => $c->members)->count() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-bold italic">Nenhuma supervisão registrada.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Tab Content: Células -->
                <div x-show="activeTab === 'cells'" x-transition.fade class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Unidade</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Supervisão</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Liderança</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($cells as $cell)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6 font-bold text-gray-900">{{ $cell->name }}</td>
                                        <td class="px-10 py-6 text-sm text-gray-500 font-medium">{{ $cell->supervision->name }}</td>
                                        <td class="px-10 py-6 text-sm font-bold text-gray-700">{{ $cell->leader->name ?? '-' }}</td>
                                        <td class="px-10 py-6 text-right">
                                            <a href="{{ route('cells.show', $cell) }}" class="text-gray-300 hover:text-blue-600 transition-colors">
                                                <i class="bi bi-chevron-right text-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Content: Membros -->
                <div x-show="activeTab === 'members'" x-transition.fade class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Membro</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Célula</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($members->take(50) as $member)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center font-bold">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $member->name }}</p>
                                                    <p class="text-[10px] text-gray-400 font-medium">{{ $member->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6">
                                            <span class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black text-gray-500 uppercase">
                                                {{ $member->cell->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-right">
                                            <a href="{{ route('users.show', $member) }}" class="text-gray-300 hover:text-blue-600 transition-colors">
                                                <i class="bi bi-chevron-right text-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Coluna de Ações -->
            <div class="space-y-6">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Gestão da Zona</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('zones.edit', $zone) }}"
                            class="w-full bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-pencil-square"></i> Configurar Zona
                        </a>
                        <a href="{{ route('zones.index') }}"
                            class="w-full bg-gray-50 text-gray-500 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-arrow-left"></i> Voltar à Lista
                        </a>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-900 to-emerald-900 rounded-[2.5rem] shadow-xl p-10 text-white relative overflow-hidden">
                    <div class="relative z-10 space-y-6">
                        <p class="text-[10px] font-black text-green-300 uppercase tracking-[0.2em]">Faturamento Zona</p>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-green-100">Arrecadação Mensal</p>
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-black tracking-tighter text-white">{{ number_format($zone->getTotalContributedThisMonth(), 0, ',', '.') }}</span>
                                <span class="text-xs font-bold text-green-300 mb-1">MT</span>
                            </div>
                        </div>
                    </div>
                    <i class="bi bi-cash-stack absolute -right-4 -bottom-4 text-9xl text-white opacity-5"></i>
                </div>

                @if($zone->supervisions->count() === 0)
                    <div class="bg-red-50 p-6 rounded-[2rem] border border-red-100">
                        <h4 class="text-sm font-black text-red-900 uppercase mb-2">Zona de Perigo</h4>
                        <form action="{{ route('zones.destroy', $zone) }}" method="POST" id="delete-form">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('{{ route('zones.destroy', $zone) }}', 'delete-form')"
                                class="w-full py-3 bg-white text-red-600 rounded-xl font-bold border border-red-100 hover:bg-red-600 hover:text-white transition-all text-xs uppercase">
                                Excluir Zona
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection