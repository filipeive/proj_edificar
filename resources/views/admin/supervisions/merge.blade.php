@extends('layouts.app')

@section('title', 'Mesclar Supervisão - Portal Life Church')
@section('page-title', 'Mesclar Supervisão')
@section('page-subtitle', 'Transfira dados e exclua supervisões duplicadas com segurança.')

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-red-600">Ação de Manutenção</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Mesclar Supervisão</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                    Origem a ser excluída: <strong class="text-red-600">{{ $sourceSupervision->name }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('supervisions.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Source Summary -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <div class="border-b border-gray-100 pb-2">
                    <h3 class="font-bold text-sm text-gray-900">Resumo da Transferência</h3>
                    <p class="text-xs text-gray-500">Estes itens serão movidos para a nova supervisão.</p>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div class="flex items-center justify-between p-3 bg-blue-50/60 border border-blue-200/60 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-grid-3x3-gap-fill text-blue-600 text-sm"></i>
                            <span class="text-xs font-bold text-gray-700">Células</span>
                        </div>
                        <span class="text-lg font-black text-blue-700 tabular-nums">{{ $sourceSupervision->cells()->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-emerald-50/60 border border-emerald-200/60 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-cash-stack text-emerald-600 text-sm"></i>
                            <span class="text-xs font-bold text-gray-700">Contribuições</span>
                        </div>
                        <span class="text-lg font-black text-emerald-700 tabular-nums">{{ $sourceSupervision->contributions()->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Merge Form -->
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                <div class="border-b border-gray-100 pb-2">
                    <h3 class="font-bold text-sm text-gray-900">Selecionar Destino</h3>
                    <p class="text-xs text-gray-500">Escolha para onde os dados serão enviados.</p>
                </div>
                <form action="{{ route('supervisions.process-merge', $sourceSupervision) }}" method="POST" class="space-y-4 pt-1">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Supervisão de Destino</label>
                        <select name="target_supervision_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                            <option value="">Selecione uma supervisão...</option>
                            @foreach($targetSupervisions as $supervision)
                                <option value="{{ $supervision->id }}">
                                    {{ $supervision->name }} ({{ $supervision->zone->name }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Todos os dados serão movidos e a supervisão <span class="font-bold text-gray-600">{{ $sourceSupervision->name }}</span> será excluída permanentemente.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" onclick="return confirm('Tem certeza que deseja mesclar esta supervisão? Esta ação é irreversível.')"
                            class="w-full inline-flex justify-center items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 shadow-sm transition">
                            <i class="bi bi-arrow-left-right"></i> Confirmar Mesclagem e Exclusão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
