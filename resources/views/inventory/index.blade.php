@extends('layouts.app')

@section('title', 'Inventário - Portal Life Church')
@section('page-title', 'Inventário')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Inventário Eclesiástico</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Gestão de Patrimônio & Materiais
                </p>
            </div>
            <a href="{{ route('inventory-items.create') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-200">
                <i class="bi bi-plus-lg mr-2"></i> Novo Item
            </a>
        </div>

        <!-- Mobile Grid View (Visible only on mobile) -->
        <div class="grid grid-cols-1 gap-4 md:hidden">
            @forelse($items as $item)
                <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span
                                class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-2">
                                {{ $item->category }}
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $item->name }}</h3>
                        </div>
                        <div class="dropdown relative">
                            <!-- Dropdown Trigger would go here, simplified actions for mobile -->
                            <div class="flex gap-2">
                                <a href="{{ route('inventory-items.edit', $item->id) }}"
                                    class="text-blue-500 hover:text-blue-700"><i class="bi bi-pencil-square text-lg"></i></a>

                                <form action="{{ route('inventory-items.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700"><i
                                            class="bi bi-trash text-lg"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mb-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="bi bi-boxes mr-2 text-gray-400"></i>
                            <span class="font-bold">{{ $item->quantity }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="bi bi-clipboard-pulse mr-2 text-gray-400"></i>
                            <span>{{ $item->condition }}</span>
                        </div>
                    </div>

                    @if($item->location)
                        <div class="mb-3">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Localização</p>
                            <p class="text-sm text-gray-700 font-medium">{{ $item->location }}</p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Valor Estimado</span>
                        <span class="text-sm font-black text-gray-900">{{ number_format($item->value ?? 0, 2, ',', '.') }}
                            MT</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-[2rem]">
                    <p class="text-gray-400 font-bold text-sm">Nenhum item registrado.</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden md:block bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Item / Categoria</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Quantidade</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Condição</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Local</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Valor</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-gray-900">{{ $item->name }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mt-1">
                                        {{ $item->category }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-black">{{ $item->quantity }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                    {{ $item->condition }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                    {{ $item->location ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">
                                    {{ number_format($item->value ?? 0, 2, ',', '.') }} MT
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('inventory-items.edit', $item->id) }}"
                                            class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition"><i
                                                class="bi bi-pencil-fill text-xs"></i></a>
                                        <form action="{{ route('inventory-items.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition"><i
                                                    class="bi bi-trash-fill text-xs"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-8 text-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                                    Nenhum item encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection