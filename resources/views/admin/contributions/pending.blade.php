@extends('layouts.app')

@section('title', 'Contribuições Pendentes - Projeto Edificar')
@section('page-title', 'Contribuições Pendentes')
@section('page-subtitle', 'Verifique e valide as contribuições')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <p class="text-sm text-gray-600">Total: <strong>{{ $contributions->total() }}</strong> contribuições</p>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membro</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Célula</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contributions as $contribution)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $contribution->user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $contribution->cell->name }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">{{ number_format($contribution->amount, 2, ',', '.') }} MT</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                <td class="px-6 py-4 text-sm space-x-2">
                    @if($contribution->proof_path)
                    <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        Ver Comprovativo
                    </a>
                    @endif
                    <form action="{{ route('contributions.verify', $contribution) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600 hover:text-green-800" onclick="return confirm('Verificar?')">
                            ✓ Verificar
                        </button>
                    </form>
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="showRejectForm({{ $contribution->id }})">
                        ✗ Rejeitar
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhuma contribuição pendente</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginação -->
<div class="mt-6">
    {{ $contributions->links() }}
</div>

<!-- Modal Rejeitar -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Rejeitar Contribuição</h3>
        <form id="rejectForm" method="POST">
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
                <button type="button" onclick="closeRejectForm()" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
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