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
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">
                    <a href="{{ route('users.index') }}" class="hover:underline">Utilizadores</a> • Perfil e Atividade
                </p>
                <div class="mt-0.5 flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-950 tracking-tight">{{ $user->name }}</h1>
                    <span class="rounded-lg bg-orange-700 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">
                        {{ $roleLabel }}
                    </span>
                    <span class="rounded-lg border {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }} px-2 py-0.5 text-[10px] font-black uppercase tracking-wider">
                        {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                    {{ $user->email }} • {{ $user->phone ?? 'Sem contacto registado' }} • Desde {{ $user->created_at->format('d/m/Y') }}
                    @if($user->cell) • {{ $user->cell->name }} @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('users.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                    <span>Voltar</span>
                </a>
                <a href="{{ route('users.activity', $user) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-clock-history"></i>
                    <span>Atividades</span>
                </a>
                @if($canManagePrivileged)
                    <a href="{{ route('users.edit', $user) }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-pencil-square"></i>
                        <span>Editar</span>
                    </a>
                @endif
                <a href="{{ route('contributions.create', ['user_id' => $user->id]) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i>
                    <span>Lançar Oferta</span>
                </a>
            </div>
        </header>

        <section class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            @php $activeCommitment = $user->commitments->whereNull('end_date')->first(); @endphp

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <p class="text-[11px] sm:text-xs font-bold text-gray-500">Compromisso Ativo</p>
                @if($activeCommitment)
                    <p class="mt-1 text-lg sm:text-2xl font-black tracking-tight text-gray-950">{{ number_format($activeCommitment->committed_amount, 0, ',', '.') }} <span class="text-xs text-emerald-700">MT</span></p>
                    <p class="mt-1 inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700">{{ $activeCommitment->package->name ?? 'Pacote' }}</p>
                @else
                    <p class="mt-1 text-base sm:text-xl font-black text-gray-400">Inativo</p>
                @endif
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <p class="text-[11px] sm:text-xs font-bold text-gray-500">Total Validado</p>
                <p class="mt-1 text-lg sm:text-2xl font-black tracking-tight text-emerald-700">{{ number_format($user->contributions->where('status', 'verificada')->sum('amount'), 0, ',', '.') }} <span class="text-xs">MT</span></p>
                <p class="mt-0.5 text-[10px] sm:text-[11px] text-gray-400">{{ $user->contributions->where('status', 'verificada')->count() }} ofertas</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <p class="text-[11px] sm:text-xs font-bold text-gray-500">Lançamentos</p>
                <p class="mt-1 text-lg sm:text-2xl font-black tracking-tight text-gray-950">{{ $user->contributions->count() }}</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 shadow-sm">
                <p class="text-[11px] sm:text-xs font-bold text-gray-500">Último Acesso</p>
                <p class="mt-1 text-base sm:text-lg font-black tracking-tight text-gray-950">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}</p>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            <div class="xl:col-span-2 space-y-4">
                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50/50 px-4 py-3">
                        <div>
                            <h2 class="text-xs sm:text-sm font-black uppercase tracking-wider text-gray-700">Histórico de Lançamentos</h2>
                            <p class="text-[10px] font-semibold text-gray-400">Últimos 10 registos</p>
                        </div>
                        <a href="{{ route('contributions.index', ['user_id' => $user->id]) }}"
                            class="rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">
                            Ver Tudo
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left">Data</th>
                                    <th class="px-4 py-3 text-left">Valor</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    <th class="px-4 py-3 text-right">Comprovativo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($user->contributions->take(10) as $contribution)
                                    <tr class="text-gray-700 hover:bg-gray-50">
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 font-black text-emerald-700">{{ number_format($contribution->amount, 0, ',', '.') }} MT</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $contribution->status == 'verificada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($contribution->status == 'pendente' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-red-50 text-red-700 border border-red-200') }}">
                                                {{ $contribution->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($contribution->proof_path)
                                                <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 transition hover:bg-blue-600 hover:text-white">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            @else
                                                <span class="text-xs italic text-gray-400">Nenhum</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-xs font-bold uppercase tracking-wider text-gray-400">
                                            Nenhuma oferta registada neste perfil.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50/50 px-4 py-3">
                        <div>
                            <h2 class="text-xs sm:text-sm font-black uppercase tracking-wider text-gray-700">Células Vinculadas</h2>
                            <p class="text-[10px] font-semibold text-gray-400">Relação e atribuições por papel de {{ $user->name }}</p>
                        </div>
                        <span class="rounded-lg border border-orange-200 bg-orange-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-orange-700">
                            {{ $relatedCells->count() }} {{ $relatedCells->count() === 1 ? 'célula' : 'células' }}
                        </span>
                    </div>
                    <div class="p-4 sm:p-5">
                        @if($relatedCells->count() > 0)
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                @foreach($relatedCells as $cell)
                                    <a href="{{ route('cells.show', $cell) }}" class="group flex flex-col justify-between rounded-xl border border-gray-200 p-4 transition-all hover:border-orange-300 hover:bg-orange-50/20 shadow-sm">
                                        <div>
                                            <div class="flex items-start justify-between gap-2.5">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-700 text-white shadow-sm">
                                                        <i class="bi bi-house-door-fill text-sm"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h3 class="text-sm font-black tracking-tight text-gray-900 group-hover:text-orange-700 truncate">{{ $cell->name }}</h3>
                                                        <p class="text-[11px] font-semibold text-gray-500 flex items-center gap-1 mt-0.5">
                                                            <i class="bi bi-diagram-3 text-gray-400"></i>
                                                            <span>{{ $cell->supervision->name ?? 'Sem supervisão' }}</span>
                                                            @if($cell->supervision && $cell->supervision->zone)
                                                                <span class="text-gray-300">•</span>
                                                                <span class="text-gray-400">{{ $cell->supervision->zone->name }}</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <i class="bi bi-chevron-right text-gray-300 transition group-hover:translate-x-1 group-hover:text-orange-700"></i>
                                            </div>
                                        </div>

                                        <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 pt-2.5">
                                            <div class="flex flex-wrap items-center gap-1">
                                                @foreach($cell->user_roles as $roleTag)
                                                    @php
                                                        $tagClasses = match($roleTag) {
                                                            'Líder' => 'bg-blue-50 text-blue-800 border-blue-200',
                                                            'Timóteo' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                                            'Supervisor' => 'bg-purple-50 text-purple-800 border-purple-200',
                                                            'Sub-supervisor' => 'bg-pink-50 text-pink-800 border-pink-200',
                                                            'Pastor de Zona' => 'bg-amber-50 text-amber-800 border-amber-200',
                                                            'Membro da Célula' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                                                        };
                                                    @endphp
                                                    <span class="rounded-md border px-2 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $tagClasses }}">
                                                        {{ $roleTag }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            <span class="text-[11px] font-bold text-gray-500">
                                                <i class="bi bi-people-fill text-gray-400 mr-1"></i>{{ $cell->members_count ?? $cell->members()->count() }} membros
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50/50 p-6 text-center">
                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                                    <i class="bi bi-house-door text-lg"></i>
                                </div>
                                <p class="mt-2 text-xs font-bold uppercase tracking-wider text-gray-500">Nenhuma célula vinculada a este perfil</p>
                                <p class="mt-1 text-xs text-gray-400">Este utilizador ainda não possui atribuição de liderança, supervisão ou pertença a uma célula.</p>
                            </div>
                        @endif
                    </div>
                </article>
            </div>

            <div class="space-y-4">
                <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-700">Observações</h3>
                        <i class="bi bi-chat-dots text-orange-700"></i>
                    </div>

                    @if($user->observations)
                        <p class="rounded-xl border border-orange-200 bg-orange-50 p-3.5 text-xs sm:text-sm font-semibold italic text-gray-700">"{{ $user->observations }}"</p>
                    @else
                        <p class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Sem notas registadas</p>
                    @endif

                    <button
                        onclick="confirmAction('Editar Notas', 'Deseja ir para a página de edição para alterar as observações?', 'info', 'Sim, editar', '{{ route('users.edit', $user) }}')"
                        class="mt-3 w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 transition hover:bg-gray-50">
                        Gerenciar Notas
                    </button>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-700">Centro de Segurança</h3>

                    <div class="mt-3 space-y-2">
                        @if($canManagePrivileged)
                            <a href="{{ route('users.edit', $user) }}"
                                class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-xs font-bold text-gray-700 transition hover:bg-gray-50">
                                <span>Editar Perfil</span>
                                <i class="bi bi-pencil-square text-orange-700"></i>
                            </a>
                        @endif

                        @if($canDeleteUser)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" id="delete-user-security">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    onclick="confirmDelete('delete-user-security', 'Deletar {{ $user->name }}?')"
                                    class="flex w-full items-center justify-between rounded-lg border border-red-200 bg-red-50 px-3.5 py-2.5 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                    <span>Eliminar Registo</span>
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        @endif

                        @if($canDeleteUser)
                            <form action="{{ route('users.reset-password', $user) }}" method="POST" id="reset-password-sidebar">
                                @csrf
                                <button type="button"
                                    onclick="confirmAction('Redefinir Senha', 'Redefinir senha de {{ $user->name }} para mudar123?', 'question', 'Sim, redefinir', 'reset-password-sidebar')"
                                    class="flex w-full items-center justify-between rounded-lg border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-xs font-bold text-amber-800 transition hover:bg-amber-100">
                                    <span>Resetar Senha</span>
                                    <i class="bi bi-key-fill"></i>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center justify-between rounded-lg border {{ $user->is_active ? 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} px-3.5 py-2.5 text-xs font-bold transition">
                                <span>{{ $user->is_active ? 'Inativar Conta' : 'Reativar Conta' }}</span>
                                <i class="bi bi-power"></i>
                            </button>
                        </form>
                    </div>

                    <div class="mt-3 border-t border-gray-200 pt-3 text-center">
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">ID do Sistema</p>
                        <p class="font-mono text-xs font-bold tracking-widest text-orange-700">#{{ str_pad($user->id, 8, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </article>
            </div>
        </section>
    </div>
@endsection
