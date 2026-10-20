@extends('layouts.app')

@section('title', 'Atividades - ' . $user->name)
@section('page-title', 'Histórico de Atividades')
@section('page-subtitle', 'Registo completo de ações realizadas por ' . $user->name)

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div class="flex items-start gap-3">
                <a href="{{ route('users.show', $user) }}"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition hover:bg-gray-50 mt-0.5">
                    <i class="bi bi-arrow-left text-sm"></i>
                </a>
                <div>
                    <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Histórico de Atividades</p>
                    <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">{{ $user->name }}</h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>

            <div class="flex w-full flex-col gap-2 sm:flex-row md:w-auto items-center">
                <form method="GET" class="w-full sm:w-64">
                    <div class="relative">
                        <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                        <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Pesquisar atividades..."
                            class="w-full rounded-lg border border-gray-300 py-2 pl-8 pr-3 text-xs sm:text-sm text-gray-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-100">
                    </div>
                </form>
                <span class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-white">
                    {{ str_replace('_', ' ', $user->role) }}
                </span>
            </div>
        </header>

        <section class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total de Atividades</p>
                <p class="mt-1 text-lg sm:text-xl font-black tracking-tight text-gray-950">{{ $activities->total() }}</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Última Atividade</p>
                <p class="mt-1 text-xs sm:text-sm font-black text-gray-800">
                    {{ $user->activities()->latest()->first() ? $user->activities()->latest()->first()->created_at->diffForHumans() : 'Nenhuma' }}
                </p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Último Login</p>
                <p class="mt-1 text-xs sm:text-sm font-black text-gray-800">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Ações Rápidas</p>
                <div class="mt-1.5 grid grid-cols-2 gap-1.5">
                    <a href="{{ route('users.show', $user) }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-2.5 py-1.5 text-[10px] font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-50">
                        Perfil
                    </a>
                    <a href="{{ route('users.edit', $user) }}"
                        class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-orange-50 px-2.5 py-1.5 text-[10px] font-black uppercase tracking-wider text-orange-700 transition hover:bg-orange-100">
                        Editar
                    </a>
                </div>
            </article>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
                <h2 class="text-xs font-black uppercase tracking-wider text-gray-700">Linha do Tempo</h2>
            </div>

            @if($activities->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($activities as $activity)
                        <article class="px-4 py-3.5 transition hover:bg-gray-50">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600">
                                    <i class="bi {{ $activity->icon }} text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex flex-wrap items-center gap-2">
                                        <span class="rounded-md bg-gray-900 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">
                                            {{ $activity->action }}
                                        </span>
                                        <span class="text-xs font-semibold text-gray-400">{{ $activity->created_at->format('d/m/Y H:i') }}</span>
                                        <span class="text-xs font-semibold text-gray-400">({{ $activity->created_at->diffForHumans() }})</span>
                                    </div>

                                    <p class="text-xs sm:text-sm font-semibold text-gray-700">{{ $activity->description ?? 'Sem descrição' }}</p>

                                    <div class="mt-1.5 flex flex-wrap items-center gap-3 text-[11px] text-gray-400">
                                        @if($activity->model_type && $activity->model_id)
                                            <span><i class="bi bi-database mr-1"></i>{{ class_basename($activity->model_type) }} #{{ $activity->model_id }}</span>
                                        @endif
                                        @if($activity->ip_address)
                                            <span><i class="bi bi-globe mr-1"></i>{{ $activity->ip_address }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($activities->hasPages())
                    <div class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50 px-4 py-3 md:flex-row md:items-center md:justify-between">
                        <p class="text-xs font-semibold text-gray-500">
                            Mostrando {{ $activities->firstItem() }} a {{ $activities->lastItem() }} de {{ $activities->total() }} atividades
                        </p>
                        {{ $activities->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                        <i class="bi bi-clock-history text-xl"></i>
                    </div>
                    <h3 class="text-sm font-black text-gray-700">Nenhuma atividade registada</h3>
                    <p class="mt-1 text-xs text-gray-500">Este utilizador ainda não realizou nenhuma atividade no sistema.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
