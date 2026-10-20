@extends('layouts.app')

@section('title', 'Mesclar Zona - Portal Life Church')
@section('page-title', 'Mesclar Zona')
@section('page-subtitle', 'Transfira dados e exclua zonas duplicadas com segurança')

@section('header-actions')
    <a href="{{ route('zones.index') }}"
        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
        <i class="bi bi-arrow-left text-xs"></i>
        <span>Voltar</span>
    </a>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Executive Header Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                    <i class="bi bi-intersect text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-red-600">Fusão Estrutural</span>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">Mesclar Zona</h1>
                    <p class="text-xs text-gray-500">
                        Zona de Origem (será eliminada após transferir vínculos): <strong class="text-gray-900">{{ $sourceZone->name }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Source Summary -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/60">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Resumo da Transferência</h3>
                    <p class="text-[11px] text-gray-500">Estes itens serão realocados para a nova zona de destino.</p>
                </div>
                <div class="p-4 space-y-2.5">
                    <div class="flex items-center justify-between p-3 bg-blue-50/60 border border-blue-100 rounded-lg">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                                <i class="bi bi-diagram-3-fill text-xs"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Supervisões</span>
                        </div>
                        <span class="text-lg font-black text-blue-700">{{ $sourceZone->supervisions()->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-emerald-50/60 border border-emerald-100 rounded-lg">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                                <i class="bi bi-cash-stack text-xs"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Contribuições</span>
                        </div>
                        <span class="text-lg font-black text-emerald-700">{{ $sourceZone->contributions()->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-purple-50/60 border border-purple-100 rounded-lg">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-purple-700">
                                <i class="bi bi-calendar-event text-xs"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Eventos</span>
                        </div>
                        <span class="text-lg font-black text-purple-700">{{ $sourceZone->events()->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-orange-50/60 border border-orange-100 rounded-lg">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-700">
                                <i class="bi bi-file-earmark-text text-xs"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Relatórios</span>
                        </div>
                        <span class="text-lg font-black text-orange-700">{{ $sourceZone->quarterlyReports()->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Merge Form -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/60">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Selecionar Destino</h3>
                        <p class="text-[11px] text-gray-500">Escolha para onde todos os dados serão enviados.</p>
                    </div>
                    <form action="{{ route('zones.process-merge', $sourceZone) }}" method="POST" id="merge-zone-form" class="p-4 space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-700">
                                Zona de Destino <span class="text-red-500">*</span>
                            </label>
                            <select name="target_zone_id" required
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-red-500 focus:ring-red-500">
                                <option value="">Selecione a zona receptora...</option>
                                @foreach($availableZones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rounded-lg border border-amber-200 bg-amber-50/70 p-3 text-xs text-amber-900 space-y-1">
                            <p class="font-bold flex items-center gap-1.5">
                                <i class="bi bi-exclamation-triangle-fill text-amber-600"></i> Atenção à Ação Irreversível
                            </p>
                            <p class="text-[11px] text-amber-800">
                                Todos os registros acima serão migrados permanentemente para a zona de destino e a zona <strong>{{ $sourceZone->name }}</strong> será apagada.
                            </p>
                        </div>
                    </form>
                </div>
                <div class="flex items-center justify-end gap-2 p-4 border-t border-gray-100 bg-gray-50/50">
                    <a href="{{ route('zones.index') }}"
                        class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" form="merge-zone-form"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700 transition-colors">
                        <i class="bi bi-check-lg text-xs"></i>
                        <span>Confirmar e Mesclar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
