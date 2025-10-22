@extends('layouts.app')

@section('title', 'Detalhes da Contribuição - Projeto Edificar')
@section('page-title', 'Detalhes da Contribuição Pendente')
@section('page-subtitle', 'Visualize e gerencie esta contribuição')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Contribuição de {{ $contribution->user->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Célula: {{ $contribution->user->cell->name ?? '—' }} •
                    Supervisão: {{ $contribution->user->cell->supervision->name ?? '—' }} •
                    Zona: {{ $contribution->user->cell->supervision->zone->name ?? '—' }}
                </p>
            </div>

            <div class="text-right">
                <p class="text-sm text-gray-500">Registrada em</p>
                <p class="font-medium text-gray-800">{{ $contribution->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-gray-50 rounded">
                <p class="text-sm text-gray-500">Valor</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($contribution->amount, 2, ',', '.') }} MT</p>
            </div>

            <div class="p-4 bg-gray-50 rounded">
                <p class="text-sm text-gray-500">Data da contribuição</p>
                <p class="font-medium text-gray-800">{{ optional($contribution->contribution_date)->format('d/m/Y') }}</p>
            </div>

            <div class="p-4 bg-gray-50 rounded">
                <p class="text-sm text-gray-500">Pacote</p>
                <p class="font-medium text-gray-800">{{ $contribution->package->name ?? '—' }}</p>
            </div>
        </div>

        <div class="mt-6">
            <p class="text-sm text-gray-500">Comprovativo</p>
            @if($contribution->proof_path)
                <a href="{{ \Illuminate\Support\Facades\Storage::url($contribution->proof_path) }}" target="_blank"
                   class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Ver Comprovativo
                </a>
            @else
                <p class="mt-2 text-gray-500">Sem comprovativo anexado.</p>
            @endif
        </div>

        <div class="mt-6 border-t pt-4">
            <p class="text-sm text-gray-500">Status</p>
            <div class="mt-2">
                @php
                    $status = $contribution->status;
                    $badge = match($status) {
                        'pendente' => 'bg-yellow-100 text-yellow-800',
                        'verificada' => 'bg-green-100 text-green-800',
                        'rejeitada' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded {{ $badge }} font-medium text-sm">
                    {{ $contribution->getStatusLabel() }}
                </span>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                    <p class="font-medium text-gray-800">Registrado por</p>
                    <p>{{ $contribution->registeredBy->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Verificado por</p>
                    <p>{{ $contribution->verifiedBy->name ?? '—' }}</p>
                    @if($contribution->verified_at)
                        <p class="text-xs text-gray-400 mt-1">em {{ $contribution->verified_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>

            @if($contribution->notes)
                <div class="mt-4">
                    <p class="font-medium text-gray-800">Notas / Motivo</p>
                    <p class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ $contribution->notes }}</p>
                </div>
            @endif
        </div>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('contributions.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">Voltar</a>

            @if($canManage && $contribution->status === 'pendente')
                <form action="{{ route('contributions.verify', $contribution) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm"
                        onclick="return confirm('Deseja verificar esta contribuição?')">
                        ✓ Verificar
                    </button>
                </form>

                <button type="button" onclick="openRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                    ✗ Rejeitar
                </button>
            @endif
        </div>
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
                <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
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
</script>
@endsection