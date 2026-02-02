@extends('layouts.app')

@section('page-title', 'Mesclar Supervisão')
@section('page-subtitle', 'Transfira dados e exclua supervisões duplicadas com segurança.')

@section('content')
    <div class="w-full space-y-8">
        <!-- Header Card -->
        <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-red-50/50 rounded-full -mr-32 -mt-32 transition-transform group-hover:scale-110 duration-700">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                    <div
                        class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-4xl shadow-2xl shadow-red-100">
                        <i class="bi bi-intersect"></i>
                    </div>
                    <div>
                        <div
                            class="flex items-center justify-center md:justify-start gap-2 text-xs font-bold text-red-600 uppercase tracking-widest mb-2">
                            <a href="{{ route('supervisions.index') }}" class="hover:underline">Supervisões</a>
                            <i class="bi bi-chevron-right text-[10px]"></i>
                            <span>Mesclar</span>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Mesclar Supervisão</h1>
                        <p class="text-gray-500 font-medium tracking-tight">Supervisão de Origem (será excluída): <span
                                class="text-red-600 font-black italic">{{ $sourceSupervision->name }}</span></p>
                    </div>
                </div>

                <a href="{{ route('supervisions.index') }}"
                    class="group flex items-center bg-gray-50 text-gray-500 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-bold text-xs uppercase tracking-widest">
                    <i class="bi bi-arrow-left text-lg mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    Cancelar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Source Summary -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900 text-lg">Resumo da Transferência</h3>
                    <p class="text-gray-500 text-sm">Estes itens serão movidos para a nova supervisão.</p>
                </div>
                <div class="p-8 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="bi bi-grid-3x3-gap-fill"></i>
                            </div>
                            <span class="font-bold text-gray-700">Células</span>
                        </div>
                        <span class="text-2xl font-black text-blue-600">{{ $sourceSupervision->cells()->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <span class="font-bold text-gray-700">Contribuições</span>
                        </div>
                        <span
                            class="text-2xl font-black text-green-600">{{ $sourceSupervision->contributions()->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Merge Form -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900 text-lg">Selecionar Destino</h3>
                    <p class="text-gray-500 text-sm">Escolha para onde os dados serão enviados.</p>
                </div>
                <form action="{{ route('supervisions.process-merge', $sourceSupervision) }}" method="POST"
                    class="p-8 space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Supervisão de
                            Destino</label>
                        <select name="target_supervision_id" required
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-lg rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-4">
                            <option value="">Selecione uma supervisão...</option>
                            @foreach($targetSupervisions as $supervision)
                                <option value="{{ $supervision->id }}">
                                    {{ $supervision->name }}
                                    ({{ $supervision->zone->name }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400">Todos os dados serão movidos para esta supervisão e a supervisão
                            <span class="font-bold">{{ $sourceSupervision->name }}</span> será excluída permanentemente.</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-red-100 hover:shadow-red-200 flex items-center justify-center gap-2">
                            <i class="bi bi-arrow-left-right"></i>
                            Confirmar Mesclagem e Exclusão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection