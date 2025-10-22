@extends('layouts.app')

@section('title', $pageTitle . ' - Projeto Edificar') 
@section('page-title', $pageTitle)
@section('page-subtitle', 'Histórico de contribuições')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div class="mb-6 flex justify-between items-center">
            <div></div>
            <div class="flex gap-2">
                {{-- O botão de "+ Novo Membro" deve ser colocado na view de Membros --}}
                @if (in_array(auth()->user()->role, ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin']))
                    <a href="{{ route('contributions.create') }}"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="bi bi-plus-circle mr-2"></i>+ Nova Contribuição
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                    
                    {{-- Mostrar a coluna de Usuário/Membro se não for a rota 'Minhas Contribuições' e não for 'membro' --}}
                    @if (isset($showUserColumn) && $showUserColumn)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membro</th>
                    @endif
                    
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comprovativo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $contribution)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $contribution->contribution_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ number_format($contribution->amount, 2, ',', '.') }} MT</td>
                        
                        {{-- Mostrar a célula com o nome do usuário se a coluna for exibida --}}
                        @if (isset($showUserColumn) && $showUserColumn)
                            <td class="px-6 py-4 text-sm text-gray-800">
                                {{ $contribution->user->name }}
                                <span class="text-xs text-gray-500 block">Célula: {{ $contribution->cell->name ?? 'N/A' }}</span>
                            </td>
                        @endif

                        <td class="px-6 py-4 text-sm">
                            @if ($contribution->status === 'verificada')
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
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($contribution->proof_path)
                                <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="bi bi-download"></i> Ver
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-3">
                            <a href="{{ route('contributions.show', $contribution) }}"
                                class="text-blue-600 hover:text-blue-800">
                                Ver
                            </a>
                            {{-- Apenas o dono pode editar e apenas se estiver pendente --}}
                            @if ($contribution->status === 'pendente' && auth()->id() === $contribution->user_id)
                                <a href="{{ route('contributions.edit', $contribution) }}"
                                    class="text-orange-600 hover:text-orange-800">
                                    Editar
                                </a>
                            @endif
                            
                            {{-- Admin/Pastor/Supervisor/Líder pode verificar a contribuição de outros --}}
                            @if (auth()->user()->role === 'admin' || 'pastor' || 'supervisor' && $contribution->status === 'pendente')
                                {{-- Ações de Admin (Verificar/Rejeitar) devem estar na rota 'show' --}}
                                <a href="{{ route('contributions.show', $contribution) }}"
                                    class="text-purple-600 hover:text-purple-800">
                                    Gerir
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (isset($showUserColumn) && $showUserColumn) ? 6 : 5 }}" class="px-6 py-4 text-center text-gray-500">
                            Nenhuma contribuição registada para esta visualização.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $contributions->appends(request()->query())->links() }}
    </div>
@endsection