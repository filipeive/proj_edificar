@extends('layouts.app')

@section('title', 'Visitantes - Portal Life Church')
@section('page-title', 'Gestão de Visitantes')
@section('page-subtitle', 'Acompanhamento e integração de visitantes')

@section('content')
    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-50 p-3 rounded-xl">
                    <i class="bi bi-people-fill text-blue-600 text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</span>
            </div>
            <p class="text-3xl font-black text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Visitantes cadastrados</p>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-yellow-50 p-3 rounded-xl">
                    <i class="bi bi-clock-history text-yellow-600 text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pendentes</span>
            </div>
            <p class="text-3xl font-black text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Aguardando contato</p>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-50 p-3 rounded-xl">
                    <i class="bi bi-telephone-fill text-blue-600 text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Contatados</span>
            </div>
            <p class="text-3xl font-black text-blue-600">{{ $stats['contacted'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Já foram contatados</p>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-50 p-3 rounded-xl">
                    <i class="bi bi-check-circle-fill text-green-600 text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Integrados</span>
            </div>
            <p class="text-3xl font-black text-green-600">{{ $stats['integrated'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Integrados em células</p>
        </div>
    </div>

    <!-- Filtros e Ações -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h3 class="text-xl font-black text-gray-900">Filtros</h3>
            <div class="flex gap-3">
                <a href="https://chat.whatsapp.com/DxAf8sMvMDYDDhrIV1wRxC" target="_blank"
                    class="bg-[#25D366] text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-[#128C7E] transition-all flex items-center">
                    <i class="bi bi-whatsapp mr-2"></i>Grupo Supervisores
                </a>
                <a href="{{ route('visitors.export', request()->all()) }}"
                    class="bg-green-600 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-green-700 transition-all">
                    <i class="bi bi-file-earmark-excel mr-2"></i>Exportar Excel
                </a>
                <a href="{{ route('visitors.create') }}"
                    class="btn-primary text-dark px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all">
                    <i class="bi bi-plus-lg mr-2"></i>Novo Visitante
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('visitors.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    placeholder="Nome, telefone, bairro...">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Status</label>
                <select name="status"
                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Todos</option>
                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="contatado" {{ request('status') == 'contatado' ? 'selected' : '' }}>Contatado</option>
                    <option value="integrado" {{ request('status') == 'integrado' ? 'selected' : '' }}>Integrado</option>
                    <option value="sem_interesse" {{ request('status') == 'sem_interesse' ? 'selected' : '' }}>Sem Interesse</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Zona</label>
                <select name="zone_id"
                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Todas</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                            {{ $zone->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Data Início</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 bg-orange-600 text-white px-6 py-2 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-orange-700 transition-all">
                    <i class="bi bi-search mr-2"></i>Filtrar
                </button>
                <a href="{{ route('visitors.index') }}"
                    class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Listagem -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                        <th class="px-6 py-4 text-left">Visitante</th>
                        <th class="px-6 py-4 text-left">Contato</th>
                        <th class="px-6 py-4 text-left">Data Visita</th>
                        <th class="px-6 py-4 text-left">Culto</th>
                        <th class="px-6 py-4 text-left">Zona</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($visitors as $visitor)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-bold text-gray-900">{{ $visitor->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($visitor->age) {{ $visitor->age }} anos @endif
                                        @if($visitor->gender) • {{ ucfirst($visitor->gender) }} @endif
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    @if($visitor->phone)
                                        <p class="text-sm text-gray-900"><i class="bi bi-telephone mr-1"></i>{{ $visitor->phone }}</p>
                                    @endif
                                    @if($visitor->neighborhood)
                                        <p class="text-xs text-gray-500"><i class="bi bi-geo-alt mr-1"></i>{{ $visitor->neighborhood }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $visitor->visit_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $visitor->visit_date->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($visitor->service)
                                    <p class="text-sm text-gray-900">{{ $visitor->service->service_type }}</p>
                                    <p class="text-xs text-gray-500">{{ $visitor->service->date->format('d/m/Y') }}</p>
                                @else
                                    <span class="text-xs text-gray-400">Não informado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($visitor->zone)
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">
                                        {{ $visitor->zone->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Não atribuído</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                {!! $visitor->status_badge !!}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('visitors.show', $visitor) }}"
                                        class="text-blue-600 hover:text-blue-700 p-2 hover:bg-blue-50 rounded-lg transition-all"
                                        title="Ver detalhes">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('visitors.edit', $visitor) }}"
                                        class="text-orange-600 hover:text-orange-700 p-2 hover:bg-orange-50 rounded-lg transition-all"
                                        title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="bi bi-inbox text-4xl mb-4 block"></i>
                                <p class="font-bold">Nenhum visitante encontrado</p>
                                <p class="text-sm mt-2">Cadastre o primeiro visitante para começar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visitors->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $visitors->links() }}
            </div>
        @endif
    </div>
@endsection
