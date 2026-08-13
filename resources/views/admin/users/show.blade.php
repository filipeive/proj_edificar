@extends('layouts.app')

@section('title', 'Detalhes do Utilizador - Portal Life Church')
@section('page-title', 'Detalhes do Utilizador')
@section('page-subtitle', 'Visão consolidada do perfil e atividade de ' . $user->name)

@section('header-actions')
    @php
        $canManagePrivileged = auth()->user()->isSuperAdmin() || !in_array($user->role, ['admin', 'super_admin'], true);
        $canDeleteUser = $user->role !== 'super_admin' && ($user->role !== 'admin' || auth()->user()->isSuperAdmin());
        $roleLabel = match ($user->role) {
            'super_admin' => 'Super Admin',
            'administracao' => 'Administração',
            default => ucwords(str_replace('_', ' ', $user->role)),
        };
    @endphp

    <div class="flex items-center gap-2 md:hidden">
        @if($canManagePrivileged)
            <a href="{{ route('users.edit', $user) }}"
                class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-3 py-2 text-xs font-black uppercase tracking-wider text-blue-700 transition hover:bg-blue-50">
                <i class="bi bi-pencil-square"></i>
                <span>Editar</span>
            </a>
        @endif
        <a href="{{ route('users.activity', $user) }}"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 transition hover:bg-gray-50"
            title="Atividade">
            <i class="bi bi-clock-history"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-5 pb-8">
        <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm md:p-7">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-center gap-4 md:gap-5">
                    <div class="relative">
                        <div
                            class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-3xl font-black text-white shadow-lg shadow-blue-200 md:h-24 md:w-24 md:text-4xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-xl border-2 border-white bg-white text-sm shadow-md">
                            @if($user->is_active)
                                <i class="bi bi-check-circle-fill text-emerald-500"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-red-500"></i>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-black tracking-tight text-gray-900 md:text-3xl">{{ $user->name }}</h1>
                            <span class="rounded-lg bg-blue-600 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-white">
                                {{ $roleLabel }}
                            </span>
                            <span class="rounded-lg {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} px-2.5 py-1 text-[10px] font-black uppercase tracking-wider">
                                {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-gray-600">{{ $user->email }}</p>
                        <p class="text-sm text-gray-500">{{ $user->phone ?? 'Sem contacto registado' }}</p>
                        <div class="flex flex-wrap items-center gap-4 pt-1 text-xs font-semibold text-gray-500">
                            <span><i class="bi bi-calendar3 mr-1"></i>Desde {{ $user->created_at->format('d/m/Y') }}</span>
                            @if($user->cell)
                                <span><i class="bi bi-geo-alt mr-1"></i>{{ $user->cell->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2 md:min-w-[250px]">
                    <a href="{{ route('contributions.create', ['user_id' => $user->id]) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-xs font-black uppercase tracking-wider text-white transition hover:bg-blue-700">
                        <i class="bi bi-plus-circle-fill"></i>
                        Lançar Nova Oferta
                    </a>
                    <a href="{{ route('users.activity', $user) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-xs font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-50">
                        <i class="bi bi-clock-history"></i>
                        Ver Atividades
                    </a>
                    @if($canManagePrivileged)
                        <a href="{{ route('users.edit', $user) }}"
                            class="hidden items-center justify-center gap-2 rounded-xl border border-blue-300 bg-blue-50 px-4 py-3 text-xs font-black uppercase tracking-wider text-blue-700 transition hover:bg-blue-100 md:inline-flex">
                            <i class="bi bi-pencil-square"></i>
                            Editar Perfil
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            @php $activeCommitment = $user->commitments->whereNull('end_date')->first(); @endphp

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Compromisso Ativo</p>
                @if($activeCommitment)
                    <p class="mt-2 text-3xl font-black tracking-tight text-gray-900">{{ number_format($activeCommitment->committed_amount, 0, ',', '.') }} <span class="text-sm text-emerald-600">MT</span></p>
                    <p class="mt-2 inline-flex rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-700">{{ $activeCommitment->package->name ?? 'Pacote' }}</p>
                @else
                    <p class="mt-2 text-xl font-black text-gray-300">Inativo</p>
                @endif
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Total Validado</p>
                <p class="mt-2 text-3xl font-black tracking-tight text-gray-900">{{ number_format($user->contributions->where('status', 'verificada')->sum('amount'), 0, ',', '.') }} <span class="text-sm text-blue-600">MT</span></p>
                <p class="mt-2 text-[10px] font-black uppercase tracking-wider text-gray-500">{{ $user->contributions->where('status', 'verificada')->count() }} ofertas</p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Lançamentos</p>
                <p class="mt-2 text-3xl font-black tracking-tight text-gray-900">{{ $user->contributions->count() }}</p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Último Acesso</p>
                <p class="mt-2 text-lg font-black tracking-tight text-gray-900">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}</p>
            </article>
        </section>

        @if($relatedCells->count() > 0)
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="xl:col-span-2 space-y-6">
                <article class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <div>
                            <h2 class="text-lg font-black tracking-tight text-gray-900">Células Relacionadas</h2>
                            <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Dependendo do papel de {{ $user->name }}</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @foreach($relatedCells as $cell)
                                <a href="{{ route('cells.show', $cell) }}" class="flex items-center gap-3 rounded-2xl border border-gray-100 p-4 transition hover:border-blue-200 hover:bg-blue-50/50">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                        <i class="bi bi-house-door-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-gray-900 line-clamp-1">{{ $cell->name }}</p>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">{{ $cell->supervision->name ?? 'Sem supervisão' }}</p>
                                    </div>
                                    <i class="bi bi-chevron-right ml-auto text-gray-300"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </article>
            </div>
        </section>
        @endif

        <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="xl:col-span-2 space-y-6">
                <article class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <div>
                            <h2 class="text-lg font-black tracking-tight text-gray-900">Histórico de Lançamentos</h2>
                            <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Últimos 10 registos</p>
                        </div>
                        <a href="{{ route('contributions.index', ['user_id' => $user->id]) }}"
                            class="rounded-xl border border-gray-300 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-50">
                            Ver Tudo
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-400">
                                <tr>
                                    <th class="px-6 py-3 text-left">Data</th>
                                    <th class="px-6 py-3 text-left">Valor</th>
                                    <th class="px-6 py-3 text-center">Estado</th>
                                    <th class="px-6 py-3 text-right">Comprovativo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($user->contributions->take(10) as $contribution)
                                    <tr class="text-sm text-gray-700 hover:bg-gray-50">
                                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 font-black text-emerald-600">{{ number_format($contribution->amount, 0, ',', '.') }} MT</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ $contribution->status == 'verificada' ? 'bg-emerald-100 text-emerald-700' : ($contribution->status == 'pendente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $contribution->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($contribution->proof_path)
                                                <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 transition hover:bg-blue-600 hover:text-white">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            @else
                                                <span class="text-xs italic text-gray-300">Nenhum</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-16 text-center text-sm font-semibold text-gray-400">
                                            Nenhuma oferta registada neste perfil.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <div class="space-y-6">
                <article class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-700">Observações</h3>
                        <i class="bi bi-chat-dots text-orange-500"></i>
                    </div>

                    @if($user->observations)
                        <p class="rounded-2xl border border-orange-100 bg-orange-50 p-4 text-sm font-semibold italic text-gray-700">"{{ $user->observations }}"</p>
                    @else
                        <p class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-4 text-xs font-bold uppercase tracking-wider text-gray-400">Sem notas registadas</p>
                    @endif

                    <button
                        onclick="confirmAction('Editar Notas', 'Deseja ir para a página de edição para alterar as observações?', 'info', 'Sim, editar', '{{ route('users.edit', $user) }}')"
                        class="mt-4 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-50">
                        Gerenciar Notas
                    </button>
                </article>

                <article class="rounded-3xl bg-gray-900 p-5 shadow-xl">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400">Centro de Segurança</h3>

                    <div class="mt-4 space-y-3">
                        @if($canManagePrivileged)
                            <a href="{{ route('users.edit', $user) }}"
                                class="flex w-full items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-white transition hover:bg-blue-600">
                                <span>Editar Perfil</span>
                                <i class="bi bi-pencil-square text-blue-300"></i>
                            </a>
                        @endif

                        @if($canDeleteUser)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" id="delete-user-security">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete('delete-user-security', 'Deletar {{ $user->name }}?')"
                                    class="flex w-full items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-white transition hover:bg-red-600">
                                    <span>Eliminar Registo</span>
                                    <i class="bi bi-trash-fill text-red-300"></i>
                                </button>
                            </form>
                        @endif

                        @if($canDeleteUser)
                            <form action="{{ route('users.reset-password', $user) }}" method="POST" id="reset-password-sidebar">
                                @csrf
                                <button type="button"
                                    onclick="confirmAction('Redefinir Senha', 'Redefinir senha de {{ $user->name }} para mudar123?', 'question', 'Sim, redefinir', 'reset-password-sidebar')"
                                    class="flex w-full items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-white transition hover:bg-amber-600">
                                    <span>Resetar Senha</span>
                                    <i class="bi bi-key-fill text-amber-300"></i>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-white transition {{ $user->is_active ? 'hover:bg-red-600' : 'hover:bg-emerald-600' }}">
                                <span>{{ $user->is_active ? 'Inativar Conta' : 'Reativar Conta' }}</span>
                                <i class="bi bi-power {{ $user->is_active ? 'text-red-300' : 'text-emerald-300' }}"></i>
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 border-t border-white/10 pt-4 text-center">
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">ID do Sistema</p>
                        <p class="font-mono text-sm font-bold tracking-widest text-blue-400">#{{ str_pad($user->id, 8, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </article>
            </div>
        </section>

        <div class="fixed bottom-8 right-8 z-40 hidden md:block">
            <a href="{{ route('users.index') }}"
                class="group flex h-14 w-14 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-900 shadow-xl transition hover:bg-gray-900 hover:text-white">
                <i class="bi bi-arrow-left text-xl"></i>
                <span class="pointer-events-none absolute right-16 whitespace-nowrap rounded-xl bg-gray-900 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-white opacity-0 transition group-hover:opacity-100">Voltar</span>
            </a>
        </div>
    </div>
@endsection
