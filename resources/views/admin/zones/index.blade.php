@extends('layouts.app')

@section('title', 'Gestão de Zonas - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Zonas</h1>
                <p class="text-gray-500">Gestão das divisões territoriais e pastorais da igreja.</p>
            </div>
            <a href="{{ route('zones.create') }}"
                class="group flex items-center bg-blue-600 text-white px-6 py-3 rounded-2xl hover:bg-blue-700 transition-all duration-300 font-bold shadow-lg shadow-blue-200">
                <i class="bi bi-plus-circle text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                Nova Zona
            </a>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($zones as $zone)
                <div
                    class="group bg-white rounded-[2rem] p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-100 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl font-black shadow-sm">
                                {{ substr($zone->name, 0, 1) }}
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('zones.edit', $zone) }}"
                                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-blue-50 text-gray-400 hover:text-blue-600 flex items-center justify-center transition-colors"
                                    title="Editar">
                                    <i class="bi bi-pencil-fill text-xs"></i>
                                </a>
                                @if($zone->supervisions->count() === 0)
                                    <form action="{{ route('zones.destroy', $zone) }}" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja deletar esta zona?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-full bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors"
                                            title="Remover">
                                            <i class="bi bi-trash-fill text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 leading-tight mb-2">{{ $zone->name }}</h3>
                        <div class="flex items-center text-sm text-gray-500 mb-6">
                            <i class="bi bi-person-badge mr-2 text-blue-500"></i>
                            <span class="font-medium">{{ $zone->pastor->name ?? 'Sem Pastor Atribuído' }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Supervisões</p>
                                <p class="text-lg font-black text-gray-900">{{ $zone->supervisions->count() }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Células</p>
                                <p class="text-lg font-black text-gray-900">{{ $zone->getTotalCells() }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-400 font-medium">Membros Ativos</span>
                                <span class="font-bold text-gray-900">{{ $zone->getTotalMembers() }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-400 font-medium">Arrecadação (Mês)</span>
                                <span class="font-bold text-green-600">
                                    {{ number_format($zone->getTotalContributedThisMonth(), 2, ',', '.') }} MT
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-50">
                            <a href="{{ route('zones.show', $zone) }}"
                                class="block w-full py-3 rounded-xl bg-blue-50 text-blue-600 font-bold text-center text-sm hover:bg-blue-600 hover:text-white transition-all">
                                Ver Detalhes da Zona
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-[2.5rem] border border-gray-100 border-dashed">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="bi bi-map text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Nenhuma zona encontrada</h3>
                    <p class="text-gray-500 mb-6">Comece criando a primeira zona da igreja.</p>
                    <a href="{{ route('zones.create') }}"
                        class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors font-bold">
                        <i class="bi bi-plus-lg mr-2"></i>
                        Criar Zona
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection