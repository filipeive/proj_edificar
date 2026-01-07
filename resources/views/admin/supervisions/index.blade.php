@extends('layouts.app')

@section('title', 'Gestão de Supervisões - Portal Life Church')

@section('content')
    <div class="space-y-8" x-data="{ view: 'grid' }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Supervisões</h1>
                <p class="text-gray-500 font-medium">Controle e monitoramento da estrutura de liderança por zonas.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                <!-- View Switcher -->
                <div class="bg-gray-100/50 p-1.5 rounded-2xl flex items-center gap-1">
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-grid-fill"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Cards</span>
                    </button>
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-list-ul"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Listagem</span>
                    </button>
                </div>

                <a href="{{ route('supervisions.create') }}"
                    class="group flex items-center bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all duration-300 font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200">
                    <i class="bi bi-plus-circle text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                    Nova Supervisão
                </a>
            </div>
        </div>

        <!-- Grid View -->
        <template x-if="view === 'grid'">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($supervisions as $supervision)
                    <div class="group bg-white rounded-[2rem] p-7 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>

                        <div class="relative z-10 flex flex-col h-full">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-black shadow-sm group-hover:rotate-6 transition-transform">
                                    {{ substr($supervision->name, 0, 1) }}
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('supervisions.edit', $supervision) }}" class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-400 hover:text-blue-600 flex items-center justify-center transition-all">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('supervisions.destroy', $supervision) }}" method="POST" onsubmit="return confirm('Excluir esta supervisão?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-all">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="space-y-1 mb-6">
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">{{ $supervision->zone->name ?? 'Sem Zona' }}</span>
                                <h3 class="text-xl font-black text-gray-900 leading-tight">{{ $supervision->name }}</h3>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50/50 p-4 rounded-2xl">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Células</span>
                                    <span class="text-xl font-black text-gray-900">{{ $supervision->cells->count() }}</span>
                                </div>
                                <div class="bg-gray-50/50 p-4 rounded-2xl">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Membros</span>
                                    <span class="text-xl font-black text-gray-900">{{ $supervision->cells->sum(fn($c) => $c->members->count()) }}</span>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <a href="{{ route('supervisions.show', $supervision) }}" class="flex items-center justify-center w-full py-4 rounded-2xl bg-gray-900 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-md">
                                    Aceder Dashboard
                                    <i class="bi bi-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-[2.5rem] border border-gray-100 border-dashed">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <i class="bi bi-diagram-3 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2">Nenhuma supervisão encontrada</h3>
                        <p class="text-gray-500 font-medium mb-8">Comece criando a primeira supervisão para organizar a estrutura.</p>
                        <a href="{{ route('supervisions.create') }}" class="inline-flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200">
                            <i class="bi bi-plus-lg mr-2"></i>
                            Criar Agora
                        </a>
                    </div>
                @endforelse
            </div>
        </template>

        <!-- List View -->
        <template x-if="view === 'list'">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Identificação</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Zona Afiliada</th>
                            <th class="px-10 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Células</th>
                            <th class="px-10 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros</th>
                            <th class="px-10 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($supervisions as $supervision)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-black">
                                            {{ substr($supervision->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-900 uppercase tracking-tight leading-none">{{ $supervision->name }}</p>
                                            <span class="text-[10px] font-bold text-gray-400 italic">{{ $supervision->description ? Str::limit($supervision->description, 30) : 'Sem descrição' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                        {{ $supervision->zone->name ?? 'Interno' }}
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-center font-black text-gray-700">{{ $supervision->cells->count() }}</td>
                                <td class="px-10 py-6 text-center font-black text-blue-600">{{ $supervision->cells->sum(fn($c) => $c->members->count()) }}</td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="{{ route('supervisions.show', $supervision) }}" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('supervisions.edit', $supervision) }}" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-all">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </template>
    </div>
@endsection