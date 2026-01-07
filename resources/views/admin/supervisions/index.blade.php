@extends('layouts.app')

@section('title', 'Gestão de Supervisões - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Supervisões</h1>
                <p class="text-gray-500">Gerencie a estrutura de supervisões e zonas.</p>
            </div>
            <a href="{{ route('supervisions.create') }}"
                class="group flex items-center bg-blue-600 text-white px-6 py-3 rounded-2xl hover:bg-blue-700 transition-all duration-300 font-bold shadow-lg shadow-blue-200">
                <i class="bi bi-plus-circle text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                Nova Supervisão
            </a>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($supervisions as $supervision)
                <div
                    class="group bg-white rounded-[2rem] p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-100 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:scale-110 transition-transform duration-500">
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-6">
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl font-black shadow-sm">
                                {{ substr($supervision->name, 0, 1) }}
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('supervisions.edit', $supervision) }}"
                                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-blue-50 text-gray-400 hover:text-blue-600 flex items-center justify-center transition-colors"
                                    title="Editar">
                                    <i class="bi bi-pencil-fill text-xs"></i>
                                </a>
                                <form action="{{ route('supervisions.destroy', $supervision) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors"
                                        title="Remover">
                                        <i class="bi bi-trash-fill text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 leading-tight mb-2">{{ $supervision->name }}</h3>
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2">
                            {{ $supervision->description ?? 'Sem descrição definida.' }}
                        </p>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-400 font-medium">Zona</span>
                                <span class="font-bold text-gray-700 bg-gray-50 px-3 py-1 rounded-lg">
                                    {{ $supervision->zone->name }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-400 font-medium">Células</span>
                                <span class="font-bold text-gray-900">
                                    {{ $supervision->cells->count() }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-50">
                            <a href="{{ route('supervisions.show', $supervision) }}"
                                class="block w-full py-3 rounded-xl bg-blue-50 text-blue-600 font-bold text-center text-sm hover:bg-blue-600 hover:text-white transition-all">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-[2.5rem] border border-gray-100 border-dashed">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="bi bi-diagram-3 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Nenhuma supervisão encontrada</h3>
                    <p class="text-gray-500 mb-6">Comece criando a primeira supervisão da igreja.</p>
                    <a href="{{ route('supervisions.create') }}"
                        class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors font-bold">
                        <i class="bi bi-plus-lg mr-2"></i>
                        Criar Supervisão
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection