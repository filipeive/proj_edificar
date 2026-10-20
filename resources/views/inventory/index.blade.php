@extends('layouts.app')

@section('title', 'Inventário - Portal Life Church')
@section('page-title', 'Inventário')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão de Património & Materiais</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Inventário Eclesiástico</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Controlo de bens, equipamentos e materiais da igreja</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory-items.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-3.5 py-2 text-xs font-black uppercase tracking-wider text-white hover:bg-orange-700 transition shadow-sm">
                    <i class="bi bi-plus-lg"></i> Novo Item
                </a>
            </div>
        </header>

        <!-- Mobile Grid View (Visible only on mobile) -->
        <div class="grid grid-cols-1 gap-3 md:hidden">
            @forelse($items as $item)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start mb-2.5">
                        <div>
                            <span
                                class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-wider rounded-md mb-1">
                                {{ $item->category }}
                            </span>
                            <h3 class="text-sm font-black text-gray-900 leading-tight">{{ $item->name }}</h3>
                        </div>
                        <div class="dropdown relative">
                            <div class="flex gap-1.5">
                                <a href="{{ route('inventory-items.edit', $item->id) }}"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100"><i class="bi bi-pencil-square text-xs"></i></a>

                                <form action="{{ route('inventory-items.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100"><i
                                            class="bi bi-trash text-xs"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mb-2.5 text-xs text-gray-600">
                        <div class="flex items-center">
                            <i class="bi bi-boxes mr-1.5 text-gray-400"></i>
                            <span class="font-bold">{{ $item->quantity }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="bi bi-clipboard-pulse mr-1.5 text-gray-400"></i>
                            <span>{{ $item->condition }}</span>
                        </div>
                    </div>

                    @if($item->location)
                        <div class="mb-2.5">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Localização</p>
                            <p class="text-xs text-gray-700 font-semibold">{{ $item->location }}</p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-gray-100">
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Valor Estimado</span>
                        <span class="text-xs font-black text-gray-900">{{ number_format($item->value ?? 0, 2, ',', '.') }}
                            MT</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 bg-white rounded-xl border border-gray-200">
                    <p class="text-gray-400 font-bold text-xs">Nenhum item registrado.</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden md:block rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                Item / Categoria</th>
                            <th class="px-4 py-3 text-left">
                                Quantidade</th>
                            <th class="px-4 py-3 text-left">
                                Condição</th>
                            <th class="px-4 py-3 text-left">
                                Local</th>
                            <th class="px-4 py-3 text-right">
                                Valor</th>
                            <th class="px-4 py-3 text-right">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-bold text-gray-900">{{ $item->name }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mt-0.5">
                                        {{ $item->category }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="bg-orange-50 text-orange-700 px-2.5 py-0.5 rounded-md text-xs font-black">{{ $item->quantity }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs sm:text-sm font-medium text-gray-600">
                                    {{ $item->condition }}
                                </td>
                                <td class="px-4 py-3 text-xs sm:text-sm font-medium text-gray-600">
                                    {{ $item->location ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">
                                    {{ number_format($item->value ?? 0, 2, ',', '.') }} MT
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <a href="{{ route('inventory-items.edit', $item->id) }}"
                                            class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition"><i
                                                class="bi bi-pencil-fill text-xs"></i></a>
                                        <form action="{{ route('inventory-items.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition"><i
                                                    class="bi bi-trash-fill text-xs"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-4 py-8 text-center text-gray-400 text-xs font-bold uppercase tracking-wider">
                                    Nenhum item encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection