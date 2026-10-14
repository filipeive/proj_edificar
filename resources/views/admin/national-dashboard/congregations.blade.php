@extends('layouts.app')

@section('title', 'Congregações - Painel Nacional')
@section('page-title', 'Gestão de Congregações')
@section('page-subtitle', 'Registar e manter as instituições independentes')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
                <a href="{{ route('national.dashboard') }}" class="text-xs font-bold text-orange-800 hover:underline"><i class="bi bi-arrow-left mr-1"></i>Painel nacional</a>
                <h1 class="mt-2 text-2xl font-black text-gray-950">Congregações</h1>
            </div>
            <a href="{{ route('national.congregations.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-800"><i class="bi bi-plus-lg"></i>Registar congregação</a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
        @endif

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('national.congregations.index') }}" class="flex flex-col sm:flex-row gap-2 border-b border-gray-200 p-4">
                <input name="search" value="{{ request('search') }}" placeholder="Nome, slug, domínio" class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm">
                <select name="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todas</option>
                    <option value="active" @selected(request('status') === 'active')>Ativas</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inativas</option>
                </select>
                <button class="rounded-lg bg-gray-900 px-4 py-2 text-xs font-bold text-white">Filtrar</button>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500"><tr><th class="px-5 py-3">Congregação</th><th class="px-5 py-3">Acesso</th><th class="px-5 py-3 text-right">Utilizadores</th><th class="px-5 py-3 text-right">Zonas</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3 text-right">Ação</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($congregations as $congregation)
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <a href="{{ route('national.congregations.show', $congregation) }}" class="font-bold text-gray-900 hover:text-orange-800 hover:underline">
                                            {{ $congregation->name }}
                                        </a>
                                        @if($congregation->is_primary)
                                            <span class="rounded bg-orange-100 px-1.5 py-0.5 text-[9px] font-black uppercase text-orange-800">Principal</span>
                                        @endif
                                        @if($congregation->has_edificar_project)
                                            <span class="rounded bg-sky-100 px-1.5 py-0.5 text-[9px] font-black uppercase text-sky-800">Edificar</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $congregation->slug }}</p>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">{{ $congregation->domain ?? $congregation->subdomain ?? 'Domínio principal' }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ $congregation->users_count }}</td>
                                <td class="px-5 py-4 text-right tabular-nums">{{ $congregation->zones_count }}</td>
                                <td class="px-5 py-4"><span class="rounded-full px-2 py-1 text-[10px] font-black {{ $congregation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">{{ $congregation->is_active ? 'Ativa' : 'Inativa' }}</span></td>
                                <td class="px-5 py-4 text-right whitespace-nowrap space-x-3">
                                    <a href="{{ route('national.congregations.show', $congregation) }}" class="text-xs font-bold text-orange-800 hover:underline"><i class="bi bi-eye mr-1"></i>Detalhes</a>
                                    <a href="{{ route('national.congregations.edit', $congregation) }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 hover:underline"><i class="bi bi-pencil-square mr-1"></i>Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500">Nenhuma congregação encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 p-4">{{ $congregations->links() }}</div>
        </section>
    </div>
@endsection
