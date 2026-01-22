@extends('layouts.app')

@section('title', 'Detalhes do Visitante - Portal Life Church')
@section('page-title', $visitor->name)
@section('page-subtitle', 'Informações e acompanhamento do visitante')

@section('content')
    <div class="w-full">
        <!-- Ações Rápidas -->
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('visitors.index') }}" class="text-gray-600 hover:text-gray-900 font-bold flex items-center">
                <i class="bi bi-arrow-left mr-2"></i>Voltar
            </a>
            <div class="flex gap-3">
                @if($visitor->isPending())
                    <form method="POST" action="{{ route('visitors.mark-contacted', $visitor) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all">
                            <i class="bi bi-telephone-fill mr-2"></i>Marcar como Contatado
                        </button>
                    </form>
                @endif
                <a href="{{ route('visitors.edit', $visitor) }}"
                    class="px-6 py-3 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700 transition-all">
                    <i class="bi bi-pencil-fill mr-2"></i>Editar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informações Principais -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Dados Pessoais -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-person-fill text-orange-600 mr-3"></i>
                        Dados Pessoais
                    </h3>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nome Completo</p>
                            <p class="text-lg font-bold text-gray-900">{{ $visitor->name }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Idade</p>
                            <p class="text-lg font-bold text-gray-900">{{ $visitor->age ?? 'Não informado' }}
                                @if($visitor->age) anos @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Sexo</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $visitor->gender ? ucfirst($visitor->gender) : 'Não informado' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Data da Visita</p>
                            <p class="text-lg font-bold text-gray-900">{{ $visitor->visit_date->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $visitor->visit_date->diffForHumans() }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Culto que Visitou</p>
                            @if($visitor->service)
                                <p class="text-lg font-bold text-gray-900">{{ $visitor->service->service_type }}</p>
                                <p class="text-xs text-gray-500">{{ $visitor->service->date->format('d/m/Y') }}</p>
                            @else
                                <p class="text-sm text-gray-500">Não informado</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contato e Localização -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-geo-alt-fill text-orange-600 mr-3"></i>
                        Contato e Localização
                    </h3>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Telefone</p>
                            <p class="text-lg font-bold text-gray-900">
                                @if($visitor->phone)
                                    <a href="tel:{{ $visitor->phone }}" class="text-blue-600 hover:text-blue-700">
                                        <i class="bi bi-telephone mr-1"></i>{{ $visitor->phone }}
                                    </a>
                                @else
                                    Não informado
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Bairro</p>
                            <p class="text-lg font-bold text-gray-900">{{ $visitor->neighborhood ?? 'Não informado' }}</p>
                        </div>

                        <div class="col-span-2">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Cidade</p>
                            <p class="text-lg font-bold text-gray-900">{{ $visitor->city }}</p>
                        </div>
                    </div>
                </div>

                <!-- Convite -->
                @if($visitor->invited_by_someone)
                    <div class="bg-blue-50 rounded-[2.5rem] border border-blue-100 p-8">
                        <h3 class="text-xl font-black text-blue-900 mb-4 flex items-center">
                            <i class="bi bi-person-plus-fill mr-3"></i>
                            Veio a Convite
                        </h3>
                        <p class="text-lg font-bold text-blue-900">{{ $visitor->inviter_name }}</p>
                    </div>
                @endif

                <!-- Observações -->
                @if($visitor->notes)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-black text-gray-900 mb-4 flex items-center">
                            <i class="bi bi-chat-left-text-fill text-orange-600 mr-3"></i>
                            Observações
                        </h3>
                        <p class="text-gray-700 leading-relaxed">{{ $visitor->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Status e Atribuições -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6">
                    <h4 class="text-sm font-black text-gray-900 mb-4 uppercase tracking-wider">Status</h4>
                    <div class="text-center">
                        {!! $visitor->status_badge !!}
                    </div>

                    @if($visitor->contacted_at)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Contatado em:</p>
                            <p class="text-sm font-bold text-gray-900">{{ $visitor->contacted_at->format('d/m/Y H:i') }}</p>
                            @if($visitor->contactedBy)
                                <p class="text-xs text-gray-500 mt-2">Por: {{ $visitor->contactedBy->name }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Zona -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6">
                    <h4 class="text-sm font-black text-gray-900 mb-4 uppercase tracking-wider">Zona</h4>
                    @if($visitor->zone)
                        <p class="text-lg font-bold text-blue-600">{{ $visitor->zone->name }}</p>
                    @else
                        <p class="text-sm text-gray-500 mb-4">Não atribuído</p>
                        @if(Auth::user()->isAdmin() || Auth::user()->isSecretaria())
                            <form method="POST" action="{{ route('visitors.assign-zone', $visitor) }}">
                                @csrf
                                <select name="zone_id" required
                                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm mb-3">
                                    <option value="">Selecione...</option>
                                    @foreach(\App\Models\Zone::orderBy('name')->get() as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-xl font-bold text-xs hover:bg-blue-700 transition-all">
                                    Atribuir Zona
                                </button>
                            </form>
                        @endif
                    @endif
                </div>

                <!-- Célula -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6">
                    <h4 class="text-sm font-black text-gray-900 mb-4 uppercase tracking-wider">Célula</h4>
                    @if($visitor->cell)
                        <p class="text-lg font-bold text-green-600">{{ $visitor->cell->name }}</p>
                    @else
                        <p class="text-sm text-gray-500 mb-4">Não atribuído</p>
                        @if($visitor->zone && (Auth::user()->isAdmin() || Auth::user()->isSecretaria() || Auth::user()->isPastorZona()))
                            <form method="POST" action="{{ route('visitors.assign-cell', $visitor) }}">
                                @csrf
                                <select name="cell_id" required
                                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm mb-3">
                                    <option value="">Selecione...</option>
                                    @foreach($cells as $cell)
                                        <option value="{{ $cell->id }}">{{ $cell->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-xl font-bold text-xs hover:bg-green-700 transition-all">
                                    Atribuir Célula
                                </button>
                            </form>
                        @endif
                    @endif
                </div>

                <!-- Cadastrado por -->
                <div class="bg-gray-50 rounded-[2.5rem] p-6">
                    <h4 class="text-xs font-black text-gray-500 mb-3 uppercase tracking-wider">Cadastrado por</h4>
                    <p class="text-sm font-bold text-gray-900">{{ $visitor->creator->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $visitor->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection