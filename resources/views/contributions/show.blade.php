@extends('layouts.app')

@section('title', 'Detalhes da Contribuição - Portal Life Church')
@section('page-title', 'Detalhes da Contribuição')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('contributions.index') }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100"
            title="Voltar à Lista">
            <i class="bi bi-arrow-left text-2xl"></i>
        </a>
        @if($contribution->status === 'pendente' && auth()->id() === $contribution->user_id)
            <a href="{{ route('contributions.edit', $contribution) }}"
                class="text-gray-600 hover:text-orange-600 p-2.5 hover:bg-orange-50 rounded-xl transition-all duration-300 border border-transparent hover:border-orange-100"
                title="Corrigir Registro">
                <i class="bi bi-pencil-square text-2xl"></i>
            </a>
        @endif
        @if($canManage && $contribution->status === 'pendente')
            <form action="{{ route('contributions.verify', $contribution) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="text-gray-600 hover:text-green-600 p-2.5 hover:bg-green-50 rounded-xl transition-all duration-300 border border-transparent hover:border-green-100"
                    title="Validar Oferta">
                    <i class="bi bi-patch-check text-2xl"></i>
                </button>
            </form>
            <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                class="text-gray-600 hover:text-red-600 p-2.5 hover:bg-red-50 rounded-xl transition-all duration-300 border border-transparent hover:border-red-100"
                title="Rejeitar">
                <i class="bi bi-x-circle text-2xl"></i>
            </button>
        @endif
    </div>
@endsection

@section('content')
    <div class="space-y-8">
        <!-- Header & Primary Info -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Member & Cell Card -->
            <div
                class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-10 flex items-center gap-8">
                <div
                    class="w-32 h-32 rounded-[2.5rem] bg-blue-50 text-blue-600 flex items-center justify-center font-black text-5xl shadow-lg shadow-blue-50">
                    {{ strtoupper(substr($contribution->user->name, 0, 1)) }}
                </div>
                <div class="space-y-2">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] leading-tight">Membro
                        Contribuinte</p>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tighter">{{ $contribution->user->name }}</h1>
                    <div class="flex items-center gap-4 pt-2">
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl text-xs font-bold text-gray-700">
                            <i class="bi bi-people-fill text-blue-500"></i>
                            Célula: {{ $contribution->cell->name }}
                        </div>
                        @if($contribution->status === 'verificada')
                            <span
                                class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">Validado</span>
                        @elseif($contribution->status === 'pendente')
                            <span
                                class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-[10px] font-black uppercase tracking-widest">Em
                                Análise</span>
                        @else
                            <span
                                class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest">Rejeitado</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Money Card -->
            <div
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center group hover:bg-green-50 transition-colors">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-hover:text-green-400">
                    Valor da Oferta</p>
                <p class="text-5xl font-black text-green-600 tracking-tighter">
                    {{ number_format($contribution->amount, 0, ',', '.') }}<span class="text-sm ml-1 uppercase">MT</span>
                </p>
                <p class="text-[10px] font-bold text-gray-400 mt-2 uppercase tracking-widest">
                    {{ $contribution->contribution_date->format('d/m/Y') }}
                </p>
            </div>

            <!-- Global Action (Hidden on Mobile) -->
            <div
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center gap-3 hidden md:flex">
                <a href="{{ route('contributions.index') }}"
                    class="w-full py-4 bg-gray-50 text-gray-500 rounded-2xl hover:bg-gray-100 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                    <i class="bi bi-arrow-left"></i> Voltar à Lista
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Data Detail Card -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h2 class="text-xl font-black text-gray-900 flex items-center gap-3">
                            <i class="bi bi-info-circle text-blue-600"></i>
                            Rastreabilidade do Registro
                        </h2>
                    </div>
                    <div class="p-10 grid grid-cols-2 gap-10">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Registrado em</p>
                            <p class="text-lg font-black text-gray-900">{{ $contribution->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Operador Responsável
                            </p>
                            <p class="text-lg font-black text-gray-900">
                                {{ $contribution->registeredBy->name ?? 'Sistema Automático' }}
                            </p>
                        </div>
                        @if($contribution->status !== 'pendente')
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Validado em</p>
                                <p class="text-lg font-black text-gray-900">{{ $contribution->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Validador /
                                    Autorizador</p>
                                <p class="text-lg font-black text-gray-900 italic text-blue-600">
                                    {{ $contribution->verifiedBy->name ?? 'N/A' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Proof Document -->
                @if($contribution->proof_path)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                            <h2 class="text-xl font-black text-gray-900 flex items-center gap-3">
                                <i class="bi bi-file-earmark-medical text-purple-600"></i>
                                Comprovativo Bancário / Digital
                            </h2>
                        </div>
                        <div class="p-10 flex flex-col items-center gap-6">
                            <div
                                class="w-20 h-20 rounded-3xl bg-purple-50 text-purple-600 flex items-center justify-center text-3xl">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-black text-gray-900 leading-tight">Documento Digital Anexado</p>
                                <p class="text-xs font-medium text-gray-400 mt-1 uppercase tracking-widest">Formato: PDF/Imagem
                                </p>
                            </div>
                            <a href="{{ route('contributions.receipt', $contribution) }}" target="_blank"
                                class="px-10 py-5 bg-purple-600 text-white rounded-2xl hover:bg-purple-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-purple-100">
                                Visualizar Documento
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Management & Notes -->
            <div class="space-y-8">
                @if($contribution->notes)
                    <div class="bg-gray-900 text-white rounded-[2.5rem] shadow-xl p-10 relative overflow-hidden">
                        <div class="relative z-10 space-y-4">
                            <p class="text-[10px] font-black text-blue-300 uppercase tracking-[0.2em]">Notas de Verificação</p>
                            <p class="text-sm font-medium leading-relaxed italic text-gray-300">"{{ $contribution->notes }}"</p>
                        </div>
                        <i class="bi bi-quote absolute -right-4 -bottom-4 text-9xl text-white opacity-5"></i>
                    </div>
                @endif

                @if($canManage && $contribution->status === 'pendente')
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-6">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Controlo Administrativo</h3>
                        <div class="space-y-3">
                            <form action="{{ route('contributions.verify', $contribution) }}" method="POST">
                                @csrf
                                <button type="button"
                                    onclick="confirmAction('Deseja validar esta oferta?', 'Validar Oferta').then(result => { if(result.isConfirmed) this.closest('form').submit(); })"
                                    class="w-full py-5 bg-green-600 text-white rounded-2xl hover:bg-green-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100">
                                    <i class="bi bi-patch-check mr-2"></i> Validar Oferta
                                </button>
                            </form>
                            <button onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                                class="w-full py-5 bg-red-50 text-red-600 rounded-2xl hover:bg-red-100 transition-all font-black text-xs uppercase tracking-widest">
                                <i class="bi bi-x-circle mr-2"></i> Rejeitar
                            </button>
                        </div>
                    </div>
                @endif

                @if($contribution->status === 'pendente' && auth()->id() === $contribution->user_id)
                    <a href="{{ route('contributions.edit', $contribution) }}"
                        class="block w-full py-5 bg-orange-600 text-white rounded-2xl hover:bg-orange-700 transition-all font-black text-xs uppercase tracking-widest text-center shadow-lg shadow-orange-100">
                        <i class="bi bi-pencil-square mr-2"></i> Corrigir Registro
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal Refactored -->
    <div id="rejectModal"
        class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm hidden flex items-center justify-center z-[100] p-6">
        <div class="bg-white rounded-[3rem] shadow-2xl p-10 w-full max-w-xl border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 tracking-tighter mb-2">Rejeitar Contribuição</h3>
            <p class="text-sm text-gray-400 font-medium mb-8">Por favor, descreva detalhadamente o motivo da não validação
                deste registro financeiro.</p>

            <form action="{{ route('contributions.reject', $contribution) }}" method="POST">
                @csrf
                <div class="mb-8">
                    <textarea name="notes" rows="6" required
                        class="w-full p-6 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-red-500 font-medium text-sm text-gray-700"
                        placeholder="Ex: Valor não identificado no extrato, comprovativo ilegível..."></textarea>
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="flex-1 py-5 bg-gray-100 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 py-5 bg-red-600 text-white rounded-2xl hover:bg-red-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-red-100">
                        Confirmar Rejeição
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection