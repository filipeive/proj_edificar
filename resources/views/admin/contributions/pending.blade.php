@extends('layouts.app')

@section('title', 'Contribuições Pendentes - Portal Life Church')
@section('page-title', 'Contribuições Pendentes')
@section('page-subtitle', 'Validar registos pendentes')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('contributions.index', ['status' => 'pendente']) }}"
            class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50"
            title="Atualizar">
            <i class="bi bi-arrow-clockwise"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-[1.5rem] sm:rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Pendentes</p>
                        <p class="text-lg font-black text-gray-900">{{ $contributions->total() }} contribuições</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('contributions.index', ['status' => 'pendente']) }}"
                        class="bg-orange-600 text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-100">
                        <i class="bi bi-arrow-clockwise mr-2"></i>Atualizar
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-compact">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Membro</th>
                            <th class="hidden md:table-cell px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Célula</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Valor</th>
                            <th class="hidden md:table-cell px-6 py-3 text-left text-[10px] font-black uppercase tracking-widest text-gray-400">Data</th>
                            <th class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contributions as $contribution)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/60">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 line-clamp-1">{{ $contribution->user->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Pendente</span>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-xs font-bold text-gray-600">
                                    {{ $contribution->cell->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-black text-gray-900">{{ number_format($contribution->amount, 2, ',', '.') }} MT</span>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-xs font-bold text-gray-500">
                                    {{ $contribution->contribution_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.contributions.show', $contribution) }}"
                                            class="action-icon bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white shadow-sm"
                                            title="Ver detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @if($contribution->proof_path)
                                            <a href="{{ route('contributions.receipt', $contribution) }}" target="_blank"
                                                class="action-icon bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white shadow-sm"
                                                title="Comprovativo">
                                                <i class="bi bi-file-earmark-text-fill"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('contributions.verify', $contribution) }}" method="POST"
                                            id="verify-form-{{ $contribution->id }}">
                                            @csrf
                                            <button type="button"
                                                class="action-icon bg-green-50 text-green-600 hover:bg-green-600 hover:text-white shadow-sm"
                                                title="Verificar"
                                                onclick="confirmAction('Deseja verificar esta contribuição?', 'Verificar').then(result => { if(result.isConfirmed) document.getElementById('verify-form-{{ $contribution->id }}').submit(); })">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <button type="button"
                                            class="action-icon bg-red-50 text-red-600 hover:bg-red-600 hover:text-white shadow-sm"
                                            title="Rejeitar"
                                            onclick="showRejectForm({{ $contribution->id }})">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-xs font-bold uppercase tracking-widest text-gray-400">
                                    Nenhuma contribuição pendente
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($contributions->hasPages())
            <div>
                {{ $contributions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Rejeitar -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center">
        <div class="bg-white rounded-[2rem] p-6 max-w-md w-full mx-4 shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center space-x-2">
                    <!-- reject icon -->
                    <svg class="w-5 h-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Rejeitar Contribuição</span>
                </h3>
                <button type="button" onclick="closeRejectForm()" class="text-gray-400 hover:text-gray-600"
                    aria-label="Fechar">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Motivo da Rejeição</label>
                    <textarea name="notes" id="notes" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex space-x-4">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 space-x-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Rejeitar</span>
                    </button>
                    <button type="button" onclick="closeRejectForm()"
                        class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectForm(contributionId) {
            const form = document.getElementById('rejectForm');
            form.action = `/admin/contributions/${contributionId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectForm() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
@endsection
