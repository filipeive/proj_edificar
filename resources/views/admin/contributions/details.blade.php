@extends('layouts.app')

@section('title', 'Detalhes da Contribuição - Portal Life Church')
@section('page-title', 'Detalhes da Contribuição Pendente')
@section('page-subtitle', 'Visualize e gerencie esta contribuição')

@section('content')
    <div class="space-y-5 max-w-3xl mx-auto">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Tesouraria & Validação</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Contribuição de {{ $contribution->user->name }}</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                    Célula: {{ $contribution->user->cell->name ?? '—' }} •
                    Supervisão: {{ $contribution->user->cell->supervision->name ?? '—' }} •
                    Zona: {{ $contribution->user->cell->supervision->zone->name ?? '—' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('contributions.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i> <span>Voltar</span>
                </a>
            </div>
        </header>

        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-start justify-between pb-4 border-b border-gray-200">
                <div>
                    <h3 class="text-sm sm:text-base font-black text-gray-900">Dados do Registo</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Registada em {{ $contribution->created_at->format('d/m/Y H:i') }}</p>
                </div>

                @php
                    $status = $contribution->status;
                    $badge = match ($status) {
                        'pendente' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                        'verificada' => 'bg-green-50 text-green-800 border-green-200',
                        'rejeitada' => 'bg-red-50 text-red-800 border-red-200',
                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border {{ $badge }} font-black text-[10px] uppercase tracking-wider">
                    {{ $contribution->getStatusLabel() }}
                </span>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Valor</p>
                    <p class="mt-1 text-lg sm:text-xl font-black text-emerald-700">{{ number_format($contribution->amount, 2, ',', '.') }} MT
                    </p>
                </div>

                <div class="p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Data da contribuição</p>
                    <p class="mt-1 text-sm sm:text-base font-black text-gray-900">{{ optional($contribution->contribution_date)->format('d/m/Y') }}
                    </p>
                </div>

                <div class="p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pacote</p>
                    <p class="mt-1 text-sm sm:text-base font-black text-gray-900">{{ $contribution->package->name ?? '—' }}</p>
                </div>
            </div>

            <div class="mt-5">
                <p class="text-xs font-black uppercase tracking-wider text-gray-500">Comprovativo</p>
                <div class="mt-2 space-y-3">
                    @if($contribution->proof_path)
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 mb-1">Ficheiro Anexado:</p>
                            <a href="{{ route('contributions.receipt', $contribution) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-bold shadow-sm">
                                <i class="bi bi-file-earmark-pdf"></i> Ver Comprovativo
                            </a>
                        </div>
                    @endif

                    @if($contribution->proof_message)
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 mb-1">Mensagem da Operadora:</p>
                            <div
                                class="p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs sm:text-sm text-blue-900 whitespace-pre-line">
                                {{ $contribution->proof_message }}
                            </div>
                        </div>
                    @endif

                    @if(!$contribution->proof_path && !$contribution->proof_message)
                        <p class="text-xs text-gray-500 italic">Sem comprovativo anexado.</p>
                    @endif
                </div>
            </div>

            <div class="mt-5 border-t border-gray-200 pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs sm:text-sm text-gray-600">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Registrado por</p>
                        <p class="font-bold text-gray-900 mt-0.5">{{ $contribution->registeredBy->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Verificado por</p>
                        <p class="font-bold text-gray-900 mt-0.5">{{ $contribution->verifiedBy->name ?? '—' }}</p>
                        @if($contribution->verified_at)
                            <p class="text-[11px] text-gray-400 mt-0.5">em {{ $contribution->verified_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                @if($contribution->notes)
                    <div class="mt-3">
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Notas / Motivo</p>
                        <p class="text-xs sm:text-sm text-gray-700 font-medium mt-0.5 whitespace-pre-line">{{ $contribution->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-5 pt-4 border-t border-gray-200 flex flex-wrap gap-2">
                <a href="{{ route('contributions.index') }}"
                    class="px-3.5 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 text-xs font-bold shadow-sm">Voltar</a>

                @if($contribution->status === 'pendente')
                    <form action="{{ route('contributions.verify', $contribution) }}" method="POST" id="verify-form"
                        class="inline">
                        @csrf
                        <button type="button" class="px-3.5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-xs font-bold shadow-sm"
                            onclick="confirmAction('Deseja verificar esta contribuição?', 'Verificar').then(result => { if(result.isConfirmed) document.getElementById('verify-form').submit(); })">
                            ✓ Verificar
                        </button>
                    </form>

                    <button type="button" onclick="openRejectModal()"
                        class="px-3.5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs font-bold shadow-sm">
                        ✗ Rejeitar
                    </button>

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'comissao_obra')
                        <button type="button" onclick="openDeleteModal()"
                            class="px-3.5 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-xs font-bold shadow-sm">
                            Eliminar
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Eliminar Contribuição</h3>
            <form id="deleteForm" action="{{ route('contributions.destroy', $contribution) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-4">
                    <label for="delete_notes" class="block text-sm font-medium text-gray-700 mb-2">Motivo da Eliminação</label>
                    <textarea name="notes" id="delete_notes" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-700"></textarea>
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
                        Eliminar
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Rejeitar -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Rejeitar Contribuição</h3>
            <form id="rejectForm" action="{{ route('contributions.reject', $contribution) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Motivo da Rejeição</label>
                    <textarea name="notes" id="notes" rows="4" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Rejeitar
                    </button>
                    <button type="button" onclick="closeRejectModal()"
                        class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
@endsection
