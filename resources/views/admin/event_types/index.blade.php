@extends('layouts.app')

@section('title', 'Tipos de Evento')
@section('page-title', 'Tipos de Evento')
@section('page-subtitle', 'Gerenciar categorias de eventos e cerimônias')

@section('header-actions')
    <a href="{{ route('event-types.create') }}"
        class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
        <i class="bi bi-plus-circle text-xl"></i>
    </a>
@endsection

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('events.index') }}" class="text-gray-500 hover:text-gray-700 inline-flex items-center transition">
            <i class="bi bi-arrow-left mr-2"></i> Voltar para Eventos
        </a>
        <a href="{{ route('event-types.create') }}"
            class="hidden md:flex bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg font-bold">
            <i class="bi bi-plus-lg mr-2"></i> Novo Tipo
        </a>
    </div>

    <div x-data="{ view: window.innerWidth < 768 ? 'grid' : 'list' }"
        x-init="$watch('view', value => localStorage.setItem('event_types_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('event_types_view') || 'list')">
        <!-- View Toggle -->
        <div class="mb-6 flex justify-end">
            <div class="bg-gray-100 p-1 rounded-xl flex items-center">
                <button @click="view = 'list'"
                    :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="bi bi-list-ul mr-2"></i> Lista
                </button>
                <button @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                    <i class="bi bi-grid-fill mr-2"></i> Grelha
                </button>
            </div>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold">
                        <th class="px-6 py-4">Nome</th>
                        <th class="px-6 py-4">Descrição</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($eventTypes as $type)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $type->name }}</td>
                            <td class="px-6 py-4 text-gray-600 text-sm">{{ $type->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($type->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Ativo</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">Inativo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('event-types.edit', $type) }}"
                                        class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('event-types.destroy', $type) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este tipo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="bi bi-list-ul text-4xl mb-3"></i>
                                    <span class="font-medium">Nenhum tipo de evento cadastrado.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($eventTypes->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $eventTypes->links() }}
                </div>
            @endif
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($eventTypes as $type)
                <div
                    class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="bi bi-tag-fill text-xl"></i>
                        </div>
                        @if($type->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Ativo</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">Inativo</span>
                        @endif
                    </div>

                    <h3 class="text-lg font-black text-gray-900 mb-2">{{ $type->name }}</h3>
                    <p class="text-sm text-gray-600 mb-4 flex-1">{{ $type->description ?? 'Sem descrição' }}</p>

                    <div class="flex gap-2 pt-4 border-t border-gray-100">
                        <a href="{{ route('event-types.edit', $type) }}"
                            class="flex-1 bg-blue-50 text-blue-600 text-center py-2 rounded-lg font-bold text-xs hover:bg-blue-600 hover:text-white transition-all">
                            <i class="bi bi-pencil mr-1"></i> Editar
                        </a>
                        <form action="{{ route('event-types.destroy', $type) }}" method="POST" class="inline"
                            onsubmit="return confirm('Tem certeza que deseja excluir este tipo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-10 h-10 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-20 bg-white rounded-2xl border border-dashed border-gray-200 flex flex-col items-center gap-4 text-gray-300">
                    <i class="bi bi-tag text-7xl"></i>
                    <p class="font-bold text-lg">Nenhum tipo de evento cadastrado</p>
                </div>
            @endforelse
        </div>

        @if($eventTypes->hasPages())
            <div class="mt-6" x-show="view === 'grid'">
                {{ $eventTypes->links() }}
            </div>
        @endif
    </div>
@endsection