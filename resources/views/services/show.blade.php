@extends('layouts.app')

@section('title', 'Detalhes do Culto')
@section('page-title', 'Detalhes do Culto')
@section('page-subtitle', 'Informações completas sobre o culto e ofertas')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
            <a href="{{ route('services.pdf', $service) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                <i class="bi bi-file-earmark-pdf mr-2"></i> Exportar PDF
            </a>
            @can('update', $service)
                <a href="{{ route('services.edit', $service) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                    <i class="bi bi-pencil mr-2"></i> Editar
                </a>
            @endcan
            @can('delete', $service)
                <form action="{{ route('services.destroy', $service) }}" method="POST"
                    onsubmit="return confirm('Tem certeza que deseja excluir este culto?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                        <i class="bi bi-trash mr-2"></i> Excluir
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Coluna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Card de Cabeçalho -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-6 border-b border-gray-100">
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider 
                                    @if($service->service_type == 'special') bg-purple-100 text-purple-700 
                                    @else bg-blue-100 text-blue-700 @endif mb-2 inline-block">
                            {{ $service->service_type == '1st' ? '1º Culto' :
        ($service->service_type == '2nd' ? '2º Culto' :
            ($service->service_type == '3rd' ? '3º Culto' :
                ($service->service_type == '4th' ? '4º Culto' : 'Especial'))) }}
                        </span>
                        <h3 class="text-3xl font-black text-gray-800">{{ $service->theme ?? 'Sem Tema Definido' }}</h3>
                        <p class="text-gray-500 flex items-center mt-2">
                            <i class="bi bi-calendar3 mr-2"></i> {{ $service->date->format('d \d\e F \d\e Y') }}
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0 text-right">
                        <p class="text-sm text-gray-500 uppercase font-bold tracking-widest">Pregador</p>
                        <p class="text-xl font-bold text-gray-800">{{ $service->preacher->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3">Resumo da Mensagem
                        </h4>
                        <div
                            class="bg-gray-50 p-6 rounded-xl text-gray-700 leading-relaxed italic border-l-4 border-blue-500">
                            {!! nl2br(e($service->message ?? 'Nenhuma mensagem registrada.')) !!}
                        </div>
                    </div>

                    @if($service->observations)
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3">Observações
                                Adicionais</h4>
                            <p class="text-gray-600">{{ $service->observations }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card de Participação -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="bi bi-people mr-2 text-blue-600"></i> Estatísticas de Participação
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100 text-center">
                        <p class="text-sm text-blue-600 font-bold uppercase mb-1">Total</p>
                        <p class="text-4xl font-black text-blue-900">
                            {{ $service->adults_count + $service->children_count }}
                        </p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                        <p class="text-sm text-gray-500 font-bold uppercase mb-1">Adultos</p>
                        <p class="text-4xl font-black text-gray-800">{{ $service->adults_count }}</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                        <p class="text-sm text-gray-500 font-bold uppercase mb-1">Crianças</p>
                        <p class="text-4xl font-black text-gray-800">{{ $service->children_count }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna Lateral (Ofertas) -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-green-600 px-6 py-4">
                    <h4 class="text-lg font-bold text-white flex items-center">
                        <i class="bi bi-cash-coin mr-2"></i> Ofertas Coletadas
                    </h4>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($service->offerings as $offering)
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $offering->offeringType->name }}</p>
                                @if($offering->notes)
                                    <p class="text-xs text-gray-500">{{ $offering->notes }}</p>
                                @endif
                            </div>
                            <p class="font-bold text-gray-800">{{ number_format($offering->amount, 2, ',', '.') }} MT</p>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 italic py-4">Nenhuma oferta registrada.</p>
                    @endforelse

                    <div class="mt-6 pt-6 border-t-2 border-dashed border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-black text-gray-800">TOTAL</span>
                            <span
                                class="text-2xl font-black text-green-600">{{ number_format($service->total_offerings, 2, ',', '.') }}
                                MT</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-xl shadow-lg p-6 text-white">
                <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Registro do Sistema</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Criado em:</span>
                        <span>{{ $service->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Última atualização:</span>
                        <span>{{ $service->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection