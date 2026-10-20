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
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Tesouraria & Validação</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Contribuições Pendentes</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">{{ $contributions->total() }} contribuições aguardam validação</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('contributions.index', ['status' => 'pendente']) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-arrow-clockwise"></i> <span>Atualizar</span>
                </a>
            </div>
        </header>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 border border-orange-200 flex items-center justify-center">
                        <i class="bi bi-exclamation-triangle-fill text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-gray-900 uppercase tracking-wider">Registos Pendentes ({{ $contributions->total() }})</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-compact">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Membro</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left">Célula</th>
                            <th class="px-4 py-3 text-left">Valor</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left">Data</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($contributions as $contribution)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-xs sm:text-sm font-bold text-gray-900 line-clamp-1">{{ $contribution->user->name }}</span>
                                        <span class="text-[10px] text-amber-600 font-bold uppercase tracking-wider">Pendente</span>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-4 py-3 text-xs font-semibold text-gray-600">
                                    {{ $contribution->cell->name }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs sm:text-sm font-black text-emerald-700">{{ number_format($contribution->amount, 2, ',', '.') }} MT</span>
                                </td>
                                <td class="hidden md:table-cell px-4 py-3 text-xs font-semibold text-gray-500">
                                    {{ $contribution->contribution_date->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-right">
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
                                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'comissao_obra')
                                            <button type="button"
                                                class="action-icon bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white shadow-sm"
                                                title="Eliminar"
                                                onclick="showDeleteForm({{ $contribution->id }})">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        @endif
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

    <!-- Modal Eliminar -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center">
        <div class="bg-white rounded-[2rem] p-6 max-w-md w-full mx-4 shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Eliminar Contribuição</span>
                </h3>
                <button type="button" onclick="closeDeleteForm()" class="text-gray-400 hover:text-gray-600"
                    aria-label="Fechar">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-4">
                    <label for="delete_notes" class="block text-sm font-medium text-gray-700 mb-2">Motivo da Eliminação</label>
                    <textarea name="notes" id="delete_notes" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-600"></textarea>
                </div>
                <div class="flex space-x-4">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center bg-gray-800 text-white px-4 py-2 rounded-xl hover:bg-gray-900 space-x-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Eliminar</span>
                    </button>
                    <button type="button" onclick="closeDeleteForm()"
                        class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
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

        function showDeleteForm(contributionId) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/contributions/${contributionId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteForm() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
@endsection
