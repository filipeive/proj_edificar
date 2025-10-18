@extends('layouts.app')

@section('title', 'Detalhes da Contribuição - Projeto Edificar')
@section('page-title', 'Detalhes da Contribuição')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        
        <div class="grid grid-cols-2 gap-6 mb-8 border-b pb-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Membro Contribuinte</p>
                <p class="text-lg font-bold text-gray-800">{{ $contribution->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Célula</p>
                <p class="text-lg font-bold text-gray-800">{{ $contribution->cell->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Data da Contribuição</p>
                <p class="text-lg font-bold text-gray-800">{{ $contribution->contribution_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Valor</p>
                <p class="text-lg font-bold text-green-600">{{ number_format($contribution->amount, 2, ',', '.') }} MT</p>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <div>
                    @if($contribution->status === 'verificada')
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            <i class="bi bi-check-circle"></i> Verificada
                        </span>
                    @elseif($contribution->status === 'pendente')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                            <i class="bi bi-clock"></i> Pendente
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                            <i class="bi bi-x-circle"></i> Rejeitada
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Registrado Por</p>
                <p class="text-gray-800">{{ $contribution->registeredBy->name ?? 'Sistema' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Data de Registro</p>
                <p class="text-gray-800">{{ $contribution->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @if($contribution->status !== 'pendente')
            <div>
                <p class="text-sm text-gray-500 mb-1">Ação tomada por</p>
                <p class="text-gray-800">{{ $contribution->verifiedBy->name ?? 'N/A' }}</p>
            </div>
            @endif
        </div>

        @if($contribution->proof_path)
        <div class="mb-8 pb-8 border-b border-gray-200">
            <p class="text-sm text-gray-500 mb-3">Comprovativo</p>
            <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100">
                <i class="bi bi-download mr-2"></i>Fazer Download
            </a>
        </div>
        @endif

        @if($contribution->notes)
        <div class="mb-8 pb-8 border-b border-gray-200">
            <p class="text-sm text-gray-500 mb-2">Observações/Notas da Verificação</p>
            <p class="text-gray-800 bg-gray-50 p-4 rounded">{{ $contribution->notes }}</p>
        </div>
        @endif
        
        @if($canManage && $contribution->status === 'pendente')
            <h3 class="text-xl font-semibold mb-4 text-gray-700 border-t pt-4">Ações de Gestão (Admin)</h3>
            <div class="flex space-x-4">
                {{-- Botão de Verificar --}}
                <form action="{{ route('contributions.verify', $contribution) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja VERIFICAR esta contribuição? Esta ação não pode ser desfeita.')" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition text-center font-semibold">
                        <i class="bi bi-check-circle mr-2"></i>Verificar Contribuição
                    </button>
                </form>

                {{-- Botão de Rejeitar (Abre Modal) --}}
                <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition text-center font-semibold">
                    <i class="bi bi-x-circle mr-2"></i>Rejeitar
                </button>
            </div>
            <div class="mt-4 text-sm text-gray-500">
                Apenas o Administrador pode Verificar ou Rejeitar a contribuição.
            </div>
        @endif
        
        <div class="border-t pt-4 mt-6">
            <div class="flex space-x-4">
                {{-- Ação de Editar (apenas dono e se for pendente) --}}
                @if($contribution->status === 'pendente' && auth()->id() === $contribution->user_id)
                <a href="{{ route('contributions.edit', $contribution) }}" class="flex-1 bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition text-center">
                    <i class="bi bi-pencil mr-2"></i>Editar
                </a>
                @endif
                
                <a href="{{ route('contributions.index') }}" class="flex-1 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition text-center">
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Rejeitar Contribuição</h3>
        <form action="{{ route('contributions.reject', $contribution) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Motivo da Rejeição (Obrigatório)</label>
                <textarea name="notes" id="notes" rows="4" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Descreva o motivo pelo qual esta contribuição está sendo rejeitada."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Confirmar Rejeição
                </button>
            </div>
        </form>
    </div>
</div>

@endsection