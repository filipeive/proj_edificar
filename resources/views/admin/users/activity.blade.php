@extends('layouts.app')

@section('title', 'Atividades - ' . $user->name)
@section('page-title', 'Histórico de Atividades')
@section('page-subtitle', 'Registo completo de ações realizadas por ' . $user->name)

@section('content')
    <div class="space-y-5">
        <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('users.show', $user) }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 transition hover:bg-gray-50">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-gray-900">{{ $user->name }}</h1>
                        <p class="text-sm font-semibold text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                    <form method="GET" class="w-full sm:w-72">
                        <div class="relative">
                            <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Pesquisar atividades..."
                                class="w-full rounded-xl border border-gray-300 py-2.5 pl-10 pr-3 text-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                    </form>
                    <span class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-white">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Total de Atividades</p>
                <p class="mt-2 text-3xl font-black tracking-tight text-gray-900">{{ $activities->total() }}</p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Última Atividade</p>
                <p class="mt-2 text-sm font-bold text-gray-800">
                    {{ $user->activities()->latest()->first() ? $user->activities()->latest()->first()->created_at->diffForHumans() : 'Nenhuma' }}
                </p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Último Login</p>
                <p class="mt-2 text-sm font-bold text-gray-800">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}</p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Ações Rápidas</p>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <a href="{{ route('users.show', $user) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-50">
                        Perfil
                    </a>
                    <a href="{{ route('users.edit', $user) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-blue-300 bg-blue-50 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-blue-700 transition hover:bg-blue-100">
                        Editar
                    </a>
                </div>
            </article>
        </section>

        <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                <h2 class="text-sm font-black uppercase tracking-wider text-gray-700">Linha do Tempo</h2>
            </div>

            @if($activities->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($activities as $activity)
                        <article class="px-6 py-4 transition hover:bg-gray-50">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600">
                                    <i class="bi {{ $activity->icon }}"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex flex-wrap items-center gap-2">
                                        <span class="rounded-lg bg-gray-900 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-white">
                                            {{ $activity->action }}
                                        </span>
                                        <span class="text-xs font-semibold text-gray-400">{{ $activity->created_at->format('d/m/Y H:i') }}</span>
                                        <span class="text-xs font-semibold text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>

                                    <p class="text-sm font-semibold text-gray-700">{{ $activity->description ?? 'Sem descrição' }}</p>

                                    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-400">
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
                    <div class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 md:flex-row md:items-center md:justify-between">
                        <p class="text-xs font-semibold text-gray-500">
                            Mostrando {{ $activities->firstItem() }} a {{ $activities->lastItem() }} de {{ $activities->total() }} atividades
                        </p>
                        {{ $activities->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-300">
                        <i class="bi bi-clock-history text-2xl"></i>
                    </div>
                    <h3 class="text-base font-black text-gray-400">Nenhuma atividade registada</h3>
                    <p class="mt-1 text-sm text-gray-400">Este utilizador ainda não realizou nenhuma atividade no sistema.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
