@extends('layouts.app')

@section('title', "Zona $zone->name - Portal Life Church")

@section('content')
    <div class="space-y-8">
        <!-- Header & Breadcrumbs -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">
                    <a href="{{ route('zones.index') }}" class="hover:underline">Zonas</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span>Detalhes da Zona</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">{{ $zone->name }}</h1>
                <p class="text-gray-500">{{ $zone->description ?? 'Gestão detalhada da zona e sua estrutura.' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('zones.edit', $zone) }}"
                    class="group flex items-center bg-gray-50 text-gray-600 px-6 py-3 rounded-2xl hover:bg-gray-100 transition-all duration-300 font-bold">
                    <i class="bi bi-pencil text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                    Editar Zona
                </a>
                <a href="{{ route('zones.index') }}"
                    class="group flex items-center bg-white text-gray-600 px-6 py-3 rounded-2xl border border-gray-100 hover:bg-gray-50 transition-all duration-300 font-bold">
                    <i class="bi bi-arrow-left text-lg mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    Voltar
                </a>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pastor -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Pastor</p>
                    <p class="text-sm font-bold text-gray-900 leading-tight">
                        {{ $zone->pastor->name ?? 'Não atribuído' }}
                    </p>
                </div>
            </div>

            <!-- Supervisões -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Supervisões</p>
                    <p class="text-2xl font-black text-gray-900">{{ $zone->supervisions->count() }}</p>
                </div>
            </div>

            <!-- Células -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl">
                    <i class="bi bi-grid-fill"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Células</p>
                    <p class="text-2xl font-black text-gray-900">{{ $cells->count() }}</p>
                </div>
            </div>

            <!-- Membros -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-2xl">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Membros</p>
                    <p class="text-2xl font-black text-gray-900">{{ $members->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Supervisões Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-4">
                <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                    <i class="bi bi-diagram-3 text-blue-600"></i>
                    Supervisões
                </h2>
                <a href="{{ route('supervisions.create', ['zone_id' => $zone->id]) }}"
                    class="text-sm font-bold text-blue-600 hover:underline">
                    + Nova Supervisão
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($zone->supervisions as $supervision)
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-md transition-all">
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black">
                                {{ substr($supervision->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $supervision->name }}</h3>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ $supervision->cells->count() }} Células
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('supervisions.show', $supervision) }}"
                            class="block w-full text-center py-2 bg-gray-50 rounded-xl text-xs font-bold text-gray-600 hover:bg-blue-600 hover:text-white transition-all">
                            Ver Detalhes
                        </a>
                    </div>
                @empty
                    <div
                        class="col-span-full py-8 text-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200">
                        <p class="text-gray-400 font-medium">Nenhuma supervisão vinculada.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Células & Membros Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Células Section -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                        <i class="bi bi-grid text-orange-500"></i>
                        Células da Zona
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th
                                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">
                                    Célula</th>
                                <th
                                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">
                                    Líder</th>
                                <th
                                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center text-sm">
                                    Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($cells as $cell)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <p class="font-bold text-gray-900">{{ $cell->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $cell->supervision->name }}</p>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <p class="text-sm font-medium text-gray-700">
                                            {{ $cell->leader->name ?? 'Não atribuído' }}</p>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('cells.show', $cell) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white transition-all">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-12 text-center text-gray-400">Nenhuma célula encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Membros Section -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                        <i class="bi bi-people text-green-500"></i>
                        Membros da Zona
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th
                                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">
                                    Membro</th>
                                <th
                                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">
                                    Célula</th>
                                <th
                                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center">
                                    Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($members->take(20) as $member)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <p class="font-bold text-gray-900">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $member->email }}</p>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <span
                                            class="text-xs font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-md">{{ $member->cell->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('users.show', $member) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all">
                                            <i class="bi bi-person"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-12 text-center text-gray-400">Nenhum membro encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($members->count() > 20)
                    <div class="p-4 bg-gray-50 text-center">
                        <p class="text-xs text-gray-500">Exibindo os primeiros 20 de {{ $members->count() }} membros.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Danger Zone (Optional but good for completeness) -->
        <div class="pt-8 mt-8 border-t border-gray-100">
            <div
                class="bg-red-50 p-8 rounded-[2rem] border border-red-100 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-red-900 font-black text-lg mb-1">Zona de Perigo</h3>
                    <p class="text-red-600/80 text-sm">Ações irreversíveis relacionadas a esta zona.</p>
                </div>
                @if($zone->supervisions->count() === 0)
                    <form action="{{ route('zones.destroy', $zone) }}" method="POST"
                        onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir esta zona permanentemente?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-white text-red-600 px-8 py-3 rounded-2xl font-bold border border-red-100 hover:bg-red-600 hover:text-white transition-all">
                            Excluir Zona
                        </button>
                    </form>
                @else
                    <button disabled class="bg-gray-100 text-gray-400 px-8 py-3 rounded-2xl font-bold cursor-not-allowed"
                        title="Não é possível excluir zona com supervisões">
                        Excluir Zona (Bloqueado)
                    </button>
                @endif
            </div>
        </div>
    </div>
@endsection