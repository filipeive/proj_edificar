@extends('layouts.app')

@section('title', 'Detalhes do Visitante - Portal Life Church')
@section('page-title', 'Detalhes do Visitante')
@section('page-subtitle', 'Ficha completa de ' . $visitor->name)

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('visitors.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
            <span>Voltar</span>
        </a>
        @if($visitor->isPending())
            <form method="POST" action="{{ route('visitors.mark-contacted', $visitor) }}" class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm hover:bg-blue-100 transition-colors"
                    title="Marcar contatado">
                    <i class="bi bi-telephone-plus text-xs"></i>
                    <span class="hidden sm:inline">Marcar Contatado</span>
                </button>
            </form>
        @endif
        @if(in_array(Auth::user()->role, ['super_admin', 'admin', 'secretaria', 'pastor_senior', 'pastor', 'pastor_zona']))
            <a href="{{ route('visitors.edit', $visitor) }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors"
                title="Editar">
                <i class="bi bi-pencil-square text-xs"></i>
                <span class="hidden sm:inline">Editar</span>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Top Navigation & Action Bar (Desktop) -->
        <div class="hidden md:flex items-center justify-between gap-3">
            <a href="{{ route('visitors.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:text-orange-700 dark:hover:text-orange-400 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Visitantes</span>
            </a>
            <div class="flex items-center gap-2">
                @if($visitor->isPending())
                    <form method="POST" action="{{ route('visitors.mark-contacted', $visitor) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm hover:bg-blue-100 transition-colors">
                            <i class="bi bi-telephone-plus text-xs"></i>
                            <span>Marcar Contatado</span>
                        </button>
                    </form>
                @endif
                @if(in_array(Auth::user()->role, ['super_admin', 'admin', 'secretaria', 'pastor_senior', 'pastor', 'pastor_zona']))
                    <a href="{{ route('visitors.edit', $visitor) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                        <i class="bi bi-pencil-square text-xs"></i>
                        <span>Editar Visitante</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Executive Banner -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 font-black text-orange-700 text-lg border border-orange-200/50">
                        {{ strtoupper(substr($visitor->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $visitor->name }}</h1>
                            <x-visitor-status-badge :status="$visitor->contact_status" />
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mt-0.5">
                            <span class="inline-flex items-center gap-1"><i class="bi bi-calendar-event text-gray-400"></i> Visitou em {{ $visitor->visit_date->format('d/m/Y') }} ({{ $visitor->visit_date->diffForHumans() }})</span>
                            @if($visitor->phone)
                                <span class="inline-flex items-center gap-1"><i class="bi bi-telephone text-gray-400"></i> {{ $visitor->phone }}</span>
                            @endif
                            @if($visitor->neighborhood)
                                <span class="inline-flex items-center gap-1"><i class="bi bi-geo-alt text-gray-400"></i> {{ $visitor->neighborhood }}, {{ $visitor->city }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                    @if($visitor->zone)
                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 border border-blue-200">
                            <i class="bi bi-geo text-xs"></i> {{ $visitor->zone->name }}
                        </span>
                    @endif
                    @if($visitor->cell)
                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                            <i class="bi bi-house text-xs"></i> {{ $visitor->cell->name }}
                        </span>
                    @endif
                    @if($visitor->isPending())
                        <form method="POST" action="{{ route('visitors.mark-contacted', $visitor) }}" class="inline sm:hidden">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 shadow-sm hover:bg-blue-100 transition-colors">
                                <i class="bi bi-telephone-plus text-xs"></i> Contatar
                            </button>
                        </form>
                    @endif
                    @if(in_array(Auth::user()->role, ['super_admin', 'admin', 'secretaria', 'pastor_senior', 'pastor', 'pastor_zona']))
                        <a href="{{ route('visitors.edit', $visitor) }}"
                            class="inline-flex sm:hidden items-center gap-1 rounded-md bg-orange-700 px-2.5 py-1 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                            <i class="bi bi-pencil-square text-xs"></i> Editar
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Coluna Principal (Dados, Convite, Feedback) -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Informações Cadastrais -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">1. Informações Cadastrais</h3>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                            <span class="text-[10px] font-bold uppercase text-gray-400 block">Idade</span>
                            <span class="font-bold text-gray-900 mt-0.5 block">{{ $visitor->age ? $visitor->age . ' anos' : 'Não informado' }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                            <span class="text-[10px] font-bold uppercase text-gray-400 block">Sexo</span>
                            <span class="font-bold text-gray-900 mt-0.5 block">{{ $visitor->gender ? ucfirst($visitor->gender) : 'Não informado' }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-100 sm:col-span-2">
                            <span class="text-[10px] font-bold uppercase text-gray-400 block">Culto Visitado</span>
                            <span class="font-bold text-gray-900 mt-0.5 block">
                                @if($visitor->service)
                                    {{ $visitor->service->service_type }} ({{ $visitor->service->date->format('d/m/Y') }})
                                @else
                                    Não vinculado
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($visitor->invited_by_someone)
                        <div class="p-3 rounded-lg border border-blue-200 bg-blue-50/60 flex items-center justify-between text-xs">
                            <span class="text-blue-900 font-medium">Veio a convite de:</span>
                            <strong class="text-blue-900 font-black">{{ $visitor->inviter_name }}</strong>
                        </div>
                    @endif

                    @if($visitor->notes)
                        <div class="space-y-1 pt-1">
                            <span class="text-[10px] font-bold uppercase text-gray-400">Observações Cadastrais</span>
                            <p class="text-xs text-gray-700 italic bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                "{{ $visitor->notes }}"
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Registrar Acompanhamento / Feedback -->
                @if(Auth::user()->role !== 'secretaria')
                    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                            <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Acompanhamento e Histórico de Contato</h3>
                            @if($visitor->contacted_at)
                                <span class="text-[10px] font-bold text-gray-400">
                                    Último contato: {{ $visitor->contacted_at->format('d/m/Y H:i') }}
                                    @if($visitor->contactedBy) por {{ $visitor->contactedBy->name }} @endif
                                </span>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('visitors.update-feedback', $visitor) }}" class="space-y-3">
                            @csrf
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Estado do Contacto</label>
                                <select name="contact_status" required
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="pendente" {{ $visitor->contact_status === 'pendente' ? 'selected' : '' }}>Pendente (Não contactado)</option>
                                    <option value="contatado" {{ $visitor->contact_status === 'contatado' ? 'selected' : '' }}>Contatado (Em Acompanhamento)</option>
                                    <option value="sem_interesse" {{ $visitor->contact_status === 'sem_interesse' ? 'selected' : '' }}>Sem Interesse</option>
                                    <option value="integrado" {{ $visitor->contact_status === 'integrado' ? 'selected' : '' }}>Integrado (Já é Membro)</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Notas / Conversa de Acompanhamento</label>
                                <textarea name="notes" rows="3" placeholder="Detalhes da ligação ou visita..."
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 resize-none">{{ $visitor->notes }}</textarea>
                            </div>

                            <div class="flex justify-end pt-1">
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                                    <i class="bi bi-check-lg text-xs"></i>
                                    <span>Salvar Feedback</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Coluna Lateral (Integração de Zona e Célula) -->
            <div class="space-y-5">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Integração Pastoral</h3>

                    <!-- Zona -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700">Zona Pastoral</label>
                        @if($visitor->zone)
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-blue-200 bg-blue-50/50">
                                <span class="text-xs font-bold text-blue-900">{{ $visitor->zone->name }}</span>
                                @if(Auth::user()->isAdmin() || Auth::user()->isSecretaria() || Auth::user()->isPastorZona() || Auth::user()->isSupervisor())
                                    <button onclick="document.getElementById('edit-zone-form').classList.toggle('hidden')"
                                        class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                                        Alterar
                                    </button>
                                @endif
                            </div>
                        @endif

                        @if(!$visitor->zone || Auth::user()->isAdmin() || Auth::user()->isSecretaria() || Auth::user()->isPastorZona() || Auth::user()->isSupervisor())
                            <form method="POST" action="{{ route('visitors.assign-zone', $visitor) }}" id="edit-zone-form"
                                class="{{ $visitor->zone ? 'hidden' : '' }} space-y-2">
                                @csrf
                                <select name="zone_id" required onchange="this.form.submit()"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs focus:border-orange-500 focus:ring-orange-500">
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
                    <div class="space-y-1.5 pt-2 border-t border-gray-100">
                        <label class="block text-xs font-bold text-gray-700">Célula</label>
                        @if($visitor->cell)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-2.5 rounded-lg border border-emerald-200 bg-emerald-50/50">
                                    <span class="text-xs font-bold text-emerald-900">{{ $visitor->cell->name }}</span>
                                    @if($visitor->zone && (Auth::user()->isAdmin() || Auth::user()->isSecretaria() || Auth::user()->isPastorZona() || Auth::user()->isSupervisor()))
                                        <button onclick="document.getElementById('edit-cell-form').classList.toggle('hidden')"
                                            class="text-emerald-700 hover:text-emerald-900 text-xs font-semibold">
                                            Alterar
                                        </button>
                                    @endif
                                </div>
                                @if($visitor->contact_status !== 'integrado' && in_array(Auth::user()->role, ['super_admin', 'admin', 'pastor_senior', 'pastor', 'secretaria']))
                                    <form method="POST" action="{{ route('visitors.notify-supervisor', $visitor) }}">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-bold text-orange-700 hover:bg-orange-100 transition-colors">
                                            <i class="bi bi-chat-left-text-fill text-xs"></i>
                                            <span>Notificar Liderança</span>
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
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Selecione a Célula...</option>
                                    @foreach($cells as $cell)
                                        <option value="{{ $cell->id }}" {{ $visitor->cell_id == $cell->id ? 'selected' : '' }}>
                                            {{ $cell->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif(!$visitor->zone)
                            <p class="text-[11px] text-gray-400 italic bg-gray-50 p-2.5 rounded-lg border border-gray-100 text-center">
                                Atribua uma Zona primeiro para selecionar a célula.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Metadados de Cadastro -->
                <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm text-center space-y-1">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        Cadastrado por {{ $visitor->creator->name ?? 'Sistema' }}
                    </p>
                    <p class="text-[11px] text-gray-400">
                        {{ $visitor->created_at->format('d/m/Y \à\s H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
