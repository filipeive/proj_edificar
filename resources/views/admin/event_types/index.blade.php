@extends('layouts.app')

@section('title', 'Tipos de Evento')
@section('page-title', 'Tipos de Evento')
@section('page-subtitle', 'Gerenciar categorias de eventos e cerimônias')

@section('header-actions')
    <div class="md:hidden">
        <a href="{{ route('event-types.create') }}" class="inline-flex items-center justify-center rounded-lg bg-orange-700 p-2 text-white shadow-sm hover:bg-orange-800 transition">
            <i class="bi bi-plus-lg"></i>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-5" x-data="{ view: window.innerWidth < 768 ? 'grid' : 'list' }"
    x-init="$watch('view', value => localStorage.setItem('event_types_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('event_types_view') || 'list')">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Eventos</span>
            </a>
            <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-bold text-orange-800">
                {{ $eventTypes->total() }} tipos
            </span>
        </div>

        <div class="flex items-center gap-2 self-end sm:self-auto">
            <!-- View Toggle -->
            <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
                <button @click="view = 'list'"
                    :class="view === 'list' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-list-ul mr-1"></i> Lista
                </button>
                <button @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-grid-fill mr-1"></i> Grelha
                </button>
            </div>

            <a href="{{ route('event-types.create') }}" class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-plus-lg"></i>
                <span>Novo Tipo</span>
            </a>
        </div>
    </div>

    <!-- List View -->
    <div x-show="view === 'list'" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/75 text-[10px] font-black uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">Descrição</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                    @forelse($eventTypes as $type)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $type->name }}</td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $type->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($type->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Ativo</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">Inativo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('event-types.edit', $type) }}" title="Editar" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-orange-700 transition">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('event-types.destroy', $type) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este tipo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-red-600 transition" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-400">
                                Nenhum tipo de evento cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($eventTypes->hasPages())
            <div class="p-3 border-t border-gray-100 bg-gray-50/50">
                {{ $eventTypes->links() }}
            </div>
        @endif
    </div>

    <!-- Grid View -->
    <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
        @forelse($eventTypes as $type)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-orange-700">
                            <i class="bi bi-tag-fill text-sm"></i>
                        </span>
                        @if($type->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Ativo</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">Inativo</span>
                        @endif
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 line-clamp-1">{{ $type->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $type->description ?? 'Sem descrição' }}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end gap-1">
                    <a href="{{ route('event-types.edit', $type) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                        <i class="bi bi-pencil"></i>
                        <span>Editar</span>
                    </a>
                    <form action="{{ route('event-types.destroy', $type) }}" method="POST" class="inline"
                        onsubmit="return confirm('Tem certeza que deseja excluir este tipo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1 text-xs text-red-600 hover:bg-red-50 transition" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-xs text-gray-500">
                Nenhum tipo de evento cadastrado.
            </div>
        @endforelse
    </div>

    @if($eventTypes->hasPages())
        <div x-show="view === 'grid'" class="mt-4">
            {{ $eventTypes->links() }}
        </div>
    @endif
</div>
@endsection
