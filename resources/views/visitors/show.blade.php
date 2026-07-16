@extends('layouts.app')

@section('title', 'Detalhes do Visitante - Portal Life Church')
@section('page-title', 'Detalhes do Visitante')
@section('page-subtitle', 'Ficha completa de ' . $visitor->name)

@section('header-actions')
    {{-- Mobile Actions --}}
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('visitors.index') }}" class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Voltar à lista">
            <i class="bi bi-arrow-left"></i>
        </a>
        @if($visitor->isPending())
            <form method="POST" action="{{ route('visitors.mark-contacted', $visitor) }}" class="inline">
                @csrf
                <button type="submit" class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
                    title="Marcar contatado">
                    <i class="bi bi-telephone-plus"></i>
                </button>
            </form>
        @endif
        @if(!Auth::user()->isSupervisor())
            <a href="{{ route('visitors.edit', $visitor) }}"
                class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="container-fluid space-y-6">
        <!-- Header Section (Desktop) -->
        <div
            class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hidden md:flex">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <a href="{{ route('visitors.index') }}" class="hover:underline">Visitantes</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span>Ficha Cadastral</span>
                </div>
                <h1 class="text-xl font-black text-gray-900 tracking-tight">{{ $visitor->name }}</h1>
                <p class="text-gray-500 font-medium flex items-center gap-2">
                    <i class="bi bi-calendar-event"></i>
                    Visitou em {{ $visitor->visit_date->format('d/m/Y') }}
                    <span class="text-gray-300">|</span>
                    {{ $visitor->visit_date->diffForHumans() }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('visitors.index') }}"
                    class="flex items-center bg-gray-50 text-gray-500 px-5 py-3 rounded-xl hover:bg-gray-100 transition-all font-bold text-xs uppercase tracking-widest">
                    <i class="bi bi-arrow-left text-lg mr-2"></i>
                    Voltar
                </a>
                @if($visitor->isPending())
                    <form method="POST" action="{{ route('visitors.mark-contacted', $visitor) }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center bg-blue-50 text-blue-600 px-5 py-3 rounded-xl hover:bg-blue-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest shadow-sm">
                            <i class="bi bi-telephone-plus text-lg mr-2"></i>
                            Marcar Contatado
                        </button>
                    </form>
                @endif
                @if(!Auth::user()->isSupervisor())
                    <a href="{{ route('visitors.edit', $visitor) }}"
                        class="flex items-center bg-orange-50 text-orange-600 px-5 py-3 rounded-xl hover:bg-orange-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest shadow-sm">
                        <i class="bi bi-pencil-square text-lg mr-2"></i>
                        Editar
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <!-- Left Column: Primary Info -->
            <div class="xl:col-span-8 space-y-6">
                <!-- Dados Pessoais -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h3 class="text-base font-black text-gray-900">Dados Pessoais</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nome Completo</p>
                            <p class="text-base font-bold text-gray-900">{{ $visitor->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Idade</p>
                            <p class="text-base font-bold text-gray-900">
                                {{ $visitor->age ? $visitor->age . ' anos' : 'Não informado' }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sexo</p>
                            <p class="text-base font-bold text-gray-900">
                                {{ $visitor->gender ? ucfirst($visitor->gender) : 'Não informado' }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Culto Visitado</p>
                            @if($visitor->service)
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold uppercase">{{ $visitor->service->service_type }}</span>
                                    <span
                                        class="text-sm font-medium text-gray-500">{{ $visitor->service->date->format('d/m/Y') }}</span>
                                </div>
                            @else
                                <p class="text-gray-500 italic">Não vinculado</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contato e Localização -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-xl">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h3 class="text-base font-black text-gray-900">Contato e Localização</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Telefone</p>
                            @if($visitor->phone)
                                <a href="tel:{{ $visitor->phone }}"
                                    class="text-base font-bold text-blue-600 hover:underline flex items-center gap-2">
                                    <i class="bi bi-whatsapp"></i> {{ $visitor->phone }}
                                </a>
                            @else
                                <p class="text-gray-500 italic">Não informado</p>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bairro</p>
                            <p class="text-base font-bold text-gray-900">{{ $visitor->neighborhood ?? 'Não informado' }}</p>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cidade</p>
                            <p class="text-base font-bold text-gray-900">{{ $visitor->city }}</p>
                        </div>
                    </div>
                </div>

                <!-- Convite e Observações -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @if($visitor->invited_by_someone)
                        <div
                            class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-200 relative overflow-hidden">
                            <i
                                class="bi bi-envelope-paper-heart absolute -right-4 -bottom-4 text-8xl text-white opacity-10"></i>
                            <div class="relative z-10">
                                <p class="text-[10px] font-black text-blue-200 uppercase tracking-widest mb-2">Veio a convite de
                                </p>
                                <h3 class="text-xl font-black">{{ $visitor->inviter_name }}</h3>
                            </div>
                        </div>
                    @endif

                    @if($visitor->notes)
                        <div
                            class="{{ $visitor->invited_by_someone ? '' : 'md:col-span-2' }} bg-yellow-50 rounded-2xl p-6 border border-yellow-100">
                            <div class="flex items-center gap-3 mb-4">
                                <i class="bi bi-chat-quote-fill text-yellow-600 text-xl"></i>
                                <h3 class="text-sm font-black text-yellow-800 uppercase tracking-widest">Observações</h3>
                            </div>
                            <p class="text-yellow-900 font-medium italic leading-relaxed">"{{ $visitor->notes }}"</p>
                        </div>
                    @endif
                </div>

                <!-- Registrar Acompanhamento -->
                @if(Auth::user()->role !== 'secretaria')
                    <div class="mt-6 bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-650 flex items-center justify-center text-xl">
                                <i class="bi bi-chat-text"></i>
                            </div>
                            <h3 class="text-base font-black text-gray-900">Registrar Acompanhamento / Feedback</h3>
                        </div>
                        <form method="POST" action="{{ route('visitors.update-feedback', $visitor) }}" class="p-6 space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado do Contacto</label>
                                    <select name="contact_status" required
                                        class="w-full px-3.5 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 rounded-xl text-sm font-bold text-gray-700 transition-all">
                                        <option value="pendente" {{ $visitor->contact_status === 'pendente' ? 'selected' : '' }}>Pendente (Não contactado)</option>
                                        <option value="contatado" {{ $visitor->contact_status === 'contatado' ? 'selected' : '' }}>Contatado (Em Acompanhamento)</option>
                                        <option value="sem_interesse" {{ $visitor->contact_status === 'sem_interesse' ? 'selected' : '' }}>Sem Interesse</option>
                                        <option value="integrado" {{ $visitor->contact_status === 'integrado' ? 'selected' : '' }}>Integrado (Já é Membro)</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Último Contacto</label>
                                    <div class="px-3.5 py-2.5 bg-gray-50 rounded-xl text-sm font-bold text-gray-500 border border-transparent">
                                        @if($visitor->contacted_at)
                                            {{ $visitor->contacted_at->format('d/m/Y H:i') }}
                                            @if($visitor->contactedBy)
                                                (por {{ $visitor->contactedBy->name }})
                                            @endif
                                        @else
                                            Nenhum contacto registrado
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Notas / Histórico de Conversa</label>
                                <textarea name="notes" rows="4" placeholder="Escreva aqui detalhes sobre o contacto realizado (ex: ligou mas estava ocupado, pediu para re-ligar no sábado, sem interesse, etc.)..."
                                    class="w-full px-4 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 rounded-2xl text-sm font-medium text-gray-700 transition-all placeholder:text-gray-400">{{ $visitor->notes }}</textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-gray-900 hover:bg-black text-white px-6 py-3.5 rounded-2xl transition-all font-black text-xs uppercase tracking-widest flex items-center gap-2 shadow-md">
                                    <i class="bi bi-check-circle"></i> Salvar Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Status e Atribuições -->
            <div class="xl:col-span-4 space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 text-center border-b border-gray-50">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Status Atual</p>
                        <div class="inline-block transform scale-110">
                            {!! $visitor->status_badge !!}
                        </div>
                    </div>
                    @if($visitor->contacted_at)
                        <div class="p-5 bg-gray-50/50">
                            <div class="flex items-start gap-3">
                                <div class="mt-1">
                                    <i class="bi bi-check-circle-fill text-green-500"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-900">Contatado em
                                        {{ $visitor->contacted_at->format('d/m/Y') }}
                                    </p>
                                    <p class="text-[10px] text-gray-500">às {{ $visitor->contacted_at->format('H:i') }}</p>
                                    @if($visitor->contactedBy)
                                        <p class="text-[10px] text-gray-500 mt-1">Por: <span
                                                class="font-bold">{{ $visitor->contactedBy->name }}</span></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Integração (Zona & Célula) -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                            <i class="bi bi-diagram-3-fill text-purple-600"></i>
                            Integração
                        </h3>
                    </div>

                    <div class="p-5 space-y-5">
                        <!-- Zona -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Zona</label>
                            @if($visitor->zone)
                                <div
                                    class="flex items-center justify-between bg-blue-50 p-2.5 rounded-xl border border-blue-100">
                                    <span class="font-bold text-blue-700">{{ $visitor->zone->name }}</span>
                                    @if(Auth::user()->isAdmin() || Auth::user()->isSecretaria() || Auth::user()->isPastorZona() || Auth::user()->isSupervisor())
                                        <button onclick="document.getElementById('edit-zone-form').classList.toggle('hidden')"
                                            class="text-blue-400 hover:text-blue-600">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    @endif
                                </div>
                            @endif

                            @if(!$visitor->zone || Auth::user()->isAdmin() || Auth::user()->isSecretaria() || Auth::user()->isPastorZona() || Auth::user()->isSupervisor())
                                <form method="POST" action="{{ route('visitors.assign-zone', $visitor) }}" id="edit-zone-form"
                                    class="{{ $visitor->zone ? 'hidden' : '' }} space-y-2">
                                    @csrf
                                    <select name="zone_id" required onchange="this.form.submit()"
                                        class="searchable-select w-full px-3.5 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-sm font-bold text-gray-700 transition-all">
                                        <option value="">Selecione a Zona...</option>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" {{ $visitor->zone_id == $zone->id ? 'selected' : '' }}>
                                                {{ $zone->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                        </div>

                        <!-- Célula -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Célula</label>
                            @if($visitor->cell)
                                <div class="space-y-2">
                                    <div
                                        class="flex items-center justify-between bg-green-50 p-2.5 rounded-xl border border-green-100">
                                        <span class="font-bold text-green-700">{{ $visitor->cell->name }}</span>
                                        @if($visitor->zone && (Auth::user()->isAdmin() || Auth::user()->isSecretaria() || Auth::user()->isPastorZona() || Auth::user()->isSupervisor()))
                                            <button onclick="document.getElementById('edit-cell-form').classList.toggle('hidden')"
                                                class="text-green-400 hover:text-green-600">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if($visitor->contact_status !== 'integrado' && in_array(Auth::user()->role, ['super_admin', 'admin', 'pastor_senior', 'pastor', 'secretaria']))
                                        <form method="POST" action="{{ route('visitors.notify-supervisor', $visitor) }}">
                                            @csrf
                                            <button type="submit" class="w-full bg-orange-50 hover:bg-orange-600 text-orange-700 hover:text-white px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 border border-orange-200 shadow-sm active:scale-98">
                                                <i class="bi bi-chat-left-text-fill text-sm"></i> Notificar Liderança
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            @if(($visitor->zone && (Auth::user()->isAdmin() || Auth::user()->isSecretaria() || Auth::user()->isPastorZona() || Auth::user()->isSupervisor())))
                                <form method="POST" action="{{ route('visitors.assign-cell', $visitor) }}" id="edit-cell-form"
                                    class="{{ $visitor->cell ? 'hidden' : '' }} space-y-2">
                                    @csrf
                                    <select name="cell_id" required onchange="this.form.submit()"
                                        class="searchable-select w-full px-3.5 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-sm font-bold text-gray-700 transition-all">
                                        <option value="">Selecione a Célula...</option>
                                        @foreach($cells as $cell)
                                            <option value="{{ $cell->id }}" {{ $visitor->cell_id == $cell->id ? 'selected' : '' }}>
                                                {{ $cell->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @elseif(!$visitor->zone)
                                <p class="text-xs text-gray-400 italic bg-gray-50 p-2.5 rounded-xl text-center">
                                    Atribua uma Zona primeiro para selecionar a célula.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Cadastrado por -->
                <div class="text-center space-y-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Cadastrado por {{ $visitor->creator->name }}
                    </p>
                    <p class="text-[10px] text-gray-300">
                        {{ $visitor->created_at->format('d/m/Y \à\s H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
