@extends('layouts.app')

@section('title', 'Detalhes da Contribuição - Portal Life Church')
@section('page-title', 'Detalhes da Contribuição')
@section('page-subtitle', 'Visão consolidada do registo financeiro e histórico de validação')

@section('header-actions')
    @php
        $isAdminDetails = request()->routeIs('admin.contributions.show');
        $backRoute = $isAdminDetails ? route('contributions.pending') : route('contributions.index');
    @endphp

    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ $backRoute }}"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 transition hover:bg-gray-50"
            title="Voltar">
            <i class="bi bi-arrow-left"></i>
        </a>

        @if($contribution->status === 'pendente' && auth()->id() === $contribution->user_id)
            <a href="{{ route('contributions.edit', $contribution) }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-orange-200 bg-orange-50 text-orange-600 transition hover:bg-orange-100"
                title="Corrigir registo">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endif

        @if($canManage && $contribution->status === 'pendente')
            <form action="{{ route('contributions.verify', $contribution) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 transition hover:bg-emerald-100"
                    title="Validar oferta">
                    <i class="bi bi-patch-check"></i>
                </button>
            </form>

            <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100"
                title="Rejeitar">
                <i class="bi bi-x-circle"></i>
            </button>
        @endif

        @if(($canDelete ?? false))
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100"
                title="Eliminar registo">
                <i class="bi bi-trash3"></i>
            </button>
        @endif
    </div>
@endsection

@section('content')
    @php
        $isAdminDetails = request()->routeIs('admin.contributions.show');
        $backRoute = $isAdminDetails ? route('contributions.pending') : route('contributions.index');

        $statusMeta = match ($contribution->status) {
            'verificada' => ['label' => 'Validada', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
            'pendente' => ['label' => 'Pendente', 'class' => 'bg-amber-100 text-amber-700 border-amber-200'],
            'rejeitada' => ['label' => 'Rejeitada', 'class' => 'bg-red-100 text-red-700 border-red-200'],
            'cancelada' => ['label' => 'Cancelada', 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
            default => ['label' => ucfirst($contribution->status), 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
        };
    @endphp

    <div class="space-y-6 pb-8">
        <section class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm md:p-7">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-2xl font-black text-white shadow-lg shadow-blue-200 md:h-20 md:w-20 md:text-3xl">
                        {{ strtoupper(substr($contribution->user->name, 0, 1)) }}
                    </div>

                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl font-black tracking-tight text-gray-900 md:text-2xl">{{ $contribution->user->name }}</h1>
                            <span class="rounded-lg border px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ $statusMeta['class'] }}">
                                {{ $statusMeta['label'] }}
                            </span>
                            @if($isAdminDetails)
                                <span class="rounded-lg bg-blue-600 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-white">
                                    Visão Administrativa
                                </span>
                            @endif
                        </div>

                        <p class="text-sm font-semibold text-gray-600">{{ $contribution->user->email }}</p>
                        <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-gray-500">
                            <span><i class="bi bi-geo-alt mr-1"></i>{{ $contribution->cell->name ?? 'Sem célula' }}</span>
                            <span><i class="bi bi-calendar3 mr-1"></i>{{ $contribution->contribution_date->format('d/m/Y') }}</span>
                            <span><i class="bi bi-hash mr-1"></i>REF {{ str_pad($contribution->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2 md:min-w-[260px]">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center">
                        <p class="text-[10px] font-black uppercase tracking-wider text-emerald-700">Valor da Oferta</p>
                        <p class="mt-1 text-3xl font-black tracking-tight text-emerald-700">{{ number_format($contribution->amount, 0, ',', '.') }} <span class="text-sm">MT</span></p>
                    </div>

                    <a href="{{ $backRoute }}"
                        class="hidden items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-xs font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-50 md:inline-flex">
                        <i class="bi bi-arrow-left"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <article class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                        <h2 class="text-sm font-black uppercase tracking-wider text-gray-700">Rastreabilidade</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Registado em</p>
                            <p class="mt-1 text-sm font-bold text-gray-900">{{ $contribution->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Operador</p>
                            <p class="mt-1 text-sm font-bold text-gray-900">{{ $contribution->registeredBy->name ?? 'Sistema Automático' }}</p>
                        </div>

                        @if($contribution->status !== 'pendente')
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Atualizado/validado em</p>
                                <p class="mt-1 text-sm font-bold text-gray-900">{{ $contribution->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Validador</p>
                                <p class="mt-1 text-sm font-bold text-gray-900">{{ $contribution->verifiedBy->name ?? 'N/A' }}</p>
                            </div>
                        @endif
                    </div>
                </article>

                @if($contribution->proof_path)
                    <article class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h2 class="text-sm font-black uppercase tracking-wider text-gray-700">Comprovativo</h2>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col items-start gap-4 rounded-2xl border border-blue-100 bg-blue-50 p-4 md:flex-row md:items-center md:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-blue-600">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900">Documento anexado</p>
                                        <p class="text-xs font-semibold text-gray-500">PDF/Imagem</p>
                                    </div>
                                </div>

                                <a href="{{ route('contributions.receipt', $contribution) }}" target="_blank"
                                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-[10px] font-black uppercase tracking-wider text-white transition hover:bg-blue-700">
                                    <i class="bi bi-eye"></i>
                                    Visualizar
                                </a>
                            </div>
                        </div>
                    </article>
                @endif

                @if($contribution->proof_message)
                    <article class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h2 class="text-sm font-black uppercase tracking-wider text-gray-700">Mensagem de Confirmação</h2>
                        </div>

                        <div class="p-6">
                            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                                <p class="whitespace-pre-line text-sm font-medium leading-relaxed text-blue-900">{{ $contribution->proof_message }}</p>
                            </div>
                        </div>
                    </article>
                @endif
            </div>

            <div class="space-y-6">
                @if($contribution->notes)
                    <article class="rounded-3xl border {{ $contribution->status === 'cancelada' ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white' }} p-6 shadow-sm">
                        <h3 class="text-[10px] font-black uppercase tracking-wider {{ $contribution->status === 'cancelada' ? 'text-red-500' : 'text-gray-500' }}">
                            {{ $contribution->status === 'cancelada' ? 'Motivo do Cancelamento' : ($contribution->status === 'rejeitada' ? 'Motivo da Rejeição' : 'Notas') }}
                        </h3>
                        <p class="mt-3 text-sm font-semibold italic leading-relaxed {{ $contribution->status === 'cancelada' ? 'text-red-900' : 'text-gray-700' }}">
                            "{{ $contribution->notes }}"
                        </p>
                    </article>
                @endif

                @if($canManage && $contribution->status === 'pendente')
                    <article class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-[10px] font-black uppercase tracking-wider text-gray-500">Validação</h3>
                        <div class="mt-4 space-y-3">
                            <form action="{{ route('contributions.verify', $contribution) }}" method="POST">
                                @csrf
                                <button type="button"
                                    onclick="confirmAction('Deseja validar esta oferta?', 'Validar Oferta').then(result => { if(result.isConfirmed) this.closest('form').submit(); })"
                                    class="flex w-full items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-emerald-700 transition hover:bg-emerald-100">
                                    <span>Validar Oferta</span>
                                    <i class="bi bi-patch-check"></i>
                                </button>
                            </form>

                            <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                                class="flex w-full items-center justify-between rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-red-700 transition hover:bg-red-100">
                                <span>Rejeitar</span>
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </article>
                @endif

                @if(auth()->user()->isAdmin() && $contribution->status !== 'cancelada')
                    <article class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-[10px] font-black uppercase tracking-wider text-gray-500">Gestão de Histórico</h3>
                        <p class="mt-2 text-xs font-semibold text-gray-500">Mantém o registo para auditoria e retira o impacto dos totais.</p>
                        <button onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                            class="mt-4 flex w-full items-center justify-between rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-100">
                            <span>Cancelar Lançamento</span>
                            <i class="bi bi-slash-circle"></i>
                        </button>
                    </article>
                @endif

                @if(($canDelete ?? false))
                    <article class="rounded-3xl border border-red-200 bg-red-50 p-6 shadow-sm">
                        <h3 class="text-[10px] font-black uppercase tracking-wider text-red-600">Eliminação de Registo</h3>
                        <p class="mt-2 text-xs font-semibold text-red-700">Disponível apenas para estado pendente, cancelado ou rejeitado.</p>
                        <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                            class="mt-4 flex w-full items-center justify-between rounded-xl border border-red-300 bg-white px-4 py-3 text-[10px] font-black uppercase tracking-wider text-red-700 transition hover:bg-red-100">
                            <span>Eliminar Registo</span>
                            <i class="bi bi-trash3"></i>
                        </button>
                    </article>
                @endif

                @if($contribution->status === 'pendente' && auth()->id() === $contribution->user_id)
                    <a href="{{ route('contributions.edit', $contribution) }}"
                        class="flex w-full items-center justify-between rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-orange-700 transition hover:bg-orange-100">
                        <span>Corrigir Registo</span>
                        <i class="bi bi-pencil-square"></i>
                    </a>
                @endif
            </div>
        </section>
    </div>

    <div id="rejectModal"
        class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/80 p-5 backdrop-blur-sm">
        <div class="w-full max-w-xl rounded-3xl border border-gray-200 bg-white p-6 shadow-2xl">
            <h3 class="text-xl font-black tracking-tight text-gray-900">Rejeitar Contribuição</h3>
            <p class="mt-1 text-sm text-gray-500">Informe o motivo de rejeição para histórico.</p>

            <form action="{{ route('contributions.reject', $contribution) }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <textarea name="notes" rows="5" required
                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm font-medium text-gray-700 focus:border-red-400 focus:ring-red-200"
                    placeholder="Ex: comprovativo ilegível, valor não identificado no extrato..."></textarea>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-700">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-red-600 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-white hover:bg-red-700">
                        Confirmar Rejeição
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/80 p-5 backdrop-blur-sm">
        <div class="w-full max-w-xl rounded-3xl border border-gray-200 bg-white p-6 shadow-2xl">
            <h3 class="text-xl font-black tracking-tight text-gray-900">Eliminar Contribuição</h3>
            <p class="mt-1 text-sm text-gray-500">O sistema vai guardar o motivo no histórico de atividades para auditoria.</p>

            <form action="{{ route('contributions.destroy', $contribution) }}" method="POST" class="mt-5 space-y-4">
                @csrf
                @method('DELETE')
                <textarea name="notes" rows="5" required
                    class="w-full rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-gray-700 focus:border-red-400 focus:ring-red-200"
                    placeholder="Explique o motivo da eliminação deste registo..."></textarea>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-700">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-red-600 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-white hover:bg-red-700">
                        Confirmar Eliminação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="cancelModal"
        class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-gray-900/80 p-5 backdrop-blur-sm">
        <div class="w-full max-w-xl rounded-3xl border border-gray-200 bg-white p-6 shadow-2xl">
            <h3 class="text-xl font-black tracking-tight text-gray-900">Cancelar Contribuição (Histórico)</h3>
            <p class="mt-1 text-sm text-gray-500">O valor deixa de impactar totais, mas o registo permanece para auditoria.</p>

            <form action="{{ route('contributions.cancel', $contribution) }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <textarea name="notes" rows="5" required
                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm font-medium text-gray-700 focus:border-gray-400 focus:ring-gray-200"
                    placeholder="Explique o motivo do cancelamento deste registo..."></textarea>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-[10px] font-black uppercase tracking-wider text-gray-700">
                        Voltar
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-gray-900 px-4 py-3 text-[10px] font-black uppercase tracking-wider text-white hover:bg-black">
                        Confirmar Cancelamento
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
