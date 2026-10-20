@extends('layouts.app')

@section('title', 'Lista de Membros - Portal Life Church')
@section('page-title', 'Membros')
@section('page-subtitle', 'Membros sob sua alçada hierárquica')

@section('content')
<div class="space-y-5">
    <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
        <div>
            <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão Hierárquica</p>
            <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Membros Registrados ({{ $members->total() }})</h1>
            <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Exibindo membros da sua {{ auth()->user()->role === 'lider_celula' ? 'célula' : (auth()->user()->role === 'supervisor' ? 'supervisão' : (auth()->user()->role === 'pastor_zona' ? 'zona' : 'igreja')) }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-3.5 py-2 text-xs font-black uppercase tracking-wider text-white hover:bg-orange-700 transition shadow-sm">
                <i class="bi bi-person-plus-fill"></i> Novo Membro
            </a>
        </div>
    </header>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">Nome</th>
                        <th class="px-4 py-3 text-left">Email / Telefone</th>
                        <th class="px-4 py-3 text-left">Célula</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 text-sm">
                    @forelse($members as $member)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-900">
                            <div class="flex items-center gap-2.5">
                                <i class="bi bi-person-circle text-lg text-orange-600"></i>
                                {{ $member->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                            <div class="text-xs font-semibold text-gray-800">{{ $member->email }}</div>
                            <div class="text-[11px] text-gray-400">{{ $member->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                            @if($member->cell)
                                <span class="text-xs font-semibold text-gray-800">{{ $member->cell->name }}</span>
                                <div class="text-[11px] text-gray-400">{{ $member->cell->supervision->name ?? '' }}</div>
                            @else
                                <span class="text-red-500 text-xs font-bold">Sem Célula</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($member->is_active)
                                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700">Ativo</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-red-700">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-bold space-x-2">
                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800 transition">
                                Ver
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="text-orange-600 hover:text-orange-800 transition">
                                Editar
                            </a>
                            {{-- Opcional: Adicionar botão de contribuição rápida --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-xs font-semibold text-gray-500">
                            Nenhum membro encontrado sob sua alçada hierárquica.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            {{ $members->links() }}
        </div>
    </div>
</div>
@endsection
