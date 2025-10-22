@extends('layouts.app')

@section('title', 'Contribuições Pendentes - Projeto Edificar')
@section('page-title', 'Contribuições Pendentes')
@section('page-subtitle', 'Verifique e valide as contribuições')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-600 flex items-center space-x-2">
            <!-- Icona de total -->
            <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v6h6v-6c0-1.657-1.343-3-3-3zM12 4v1m0 14v1m8-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707m12.728 0-.707-.707M6.343 6.343l-.707-.707" />
            </svg>
            <span>Total: <strong>{{ $contributions->total() }}</strong> contribuições</span>
        </p>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    <div class="inline-flex items-center space-x-2">
                        <!-- user icon -->
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.88 6.196 9 9 0 015.12 17.804zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Membro</span>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    <div class="inline-flex items-center space-x-2">
                        <!-- cells icon -->
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m0-4a4 4 0 110-8 4 4 0 010 8z" />
                        </svg>
                        <span>Célula</span>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    <div class="inline-flex items-center space-x-2">
                        <!-- currency icon -->
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-12v12" />
                        </svg>
                        <span>Valor</span>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    <div class="inline-flex items-center space-x-2">
                        <!-- calendar icon -->
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Data</span>
                    </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    <div class="inline-flex items-center space-x-2">
                        <!-- actions icon -->
                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span>Ações</span>
                    </div>
                </th>
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
                    {{-- ver detalhes da contribuição --}}
                    <a href="{{ route('admin.contributions.show', $contribution) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 space-x-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Ver Detalhes</span>
                    </a>
                    {{-- ver comprovativo --}}
                    @if($contribution->proof_path)
                    <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 space-x-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Ver Comprovativo</span>
                    </a>
                    @endif
                    <form action="{{ route('contributions.verify', $contribution) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center text-green-600 hover:text-green-800 space-x-2" onclick="return confirm('Verificar?')">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Verificar</span>
                        </button>
                    </form>
                    <button type="button" class="inline-flex items-center text-red-600 hover:text-red-800 space-x-2" onclick="showRejectForm({{ $contribution->id }})">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Rejeitar</span>
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
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center space-x-2">
                <!-- reject icon -->
                <svg class="w-5 h-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Rejeitar Contribuição</span>
            </h3>
            <button type="button" onclick="closeRejectForm()" class="text-gray-400 hover:text-gray-600" aria-label="Fechar">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
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
                <button type="submit" class="flex-1 inline-flex items-center justify-center bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 space-x-2">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Rejeitar</span>
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