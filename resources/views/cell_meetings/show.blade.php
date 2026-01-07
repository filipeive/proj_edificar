@extends('layouts.app')

@section('title', 'Detalhes do Encontro')
@section('page-title', 'Detalhes do Encontro')
@section('page-subtitle', 'Informações completas sobre a reunião de célula')

@section('content')
    <div class="space-y-8">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('cell-meetings.index') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center transition font-bold">
                <i class="bi bi-arrow-left mr-2 font-black"></i> Voltar para Lista
            </a>
            <div class="flex flex-wrap gap-2">
                <button onclick="toggleEmailModal()"
                    class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-5 py-2.5 rounded-2xl flex items-center transition-all font-bold text-sm">
                    <i class="bi bi-envelope mr-2"></i> Enviar por Email
                </button>
                <a href="{{ route('cell-meetings.pdf', $cellMeeting) }}"
                    class="bg-gray-100 text-gray-800 hover:bg-gray-800 hover:text-white px-5 py-2.5 rounded-2xl flex items-center transition-all font-bold text-sm">
                    <i class="bi bi-file-earmark-pdf mr-2"></i> Acta / PDF
                </a>
                @can('update', $cellMeeting)
                    <a href="{{ route('cell-meetings.edit', $cellMeeting) }}"
                        class="bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white px-5 py-2.5 rounded-2xl flex items-center transition-all font-bold text-sm">
                        <i class="bi bi-pencil mr-2"></i> Editar
                    </a>
                @endcan
                @can('delete', $cellMeeting)
                    <form action="{{ route('cell-meetings.destroy', $cellMeeting) }}" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir este encontro?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-5 py-2.5 rounded-2xl flex items-center transition-all font-bold text-sm">
                            <i class="bi bi-trash mr-2"></i> Excluir
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Banner Informativo -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 px-10 py-12 text-white relative">
                        <div class="relative z-10 space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest text-white">
                                    @switch($cellMeeting->meeting_type)
                                        @case('leadership') Reunião de Liderança @break
                                        @case('supervision') Reunião de Supervisão @break
                                        @case('zone') Reunião de Zona @break
                                        @default Encontro de Célula
                                    @endswitch
                                </span>
                                <span class="text-white/60">•</span>
                                <span class="text-sm font-bold text-blue-100">{{ $cellMeeting->meeting_date->format('d/m/Y') }}</span>
                            </div>
                            <h3 class="text-5xl font-black tracking-tighter">{{ $cellMeeting->cell->name }}</h3>
                            <div class="flex flex-wrap gap-6 text-xs font-bold text-blue-100 uppercase tracking-widest">
                                <span class="flex items-center bg-black/10 px-3 py-1 rounded-lg">
                                    <i class="bi bi-diagram-3 mr-2"></i> {{ $cellMeeting->cell->supervision->name }}
                                </span>
                                <span class="flex items-center bg-black/10 px-3 py-1 rounded-lg">
                                    <i class="bi bi-geo-alt mr-2"></i> {{ $cellMeeting->cell->supervision->zone->name }}
                                </span>
                            </div>
                        </div>
                        <i class="bi bi-award absolute right-12 top-1/2 -translate-y-1/2 text-[12rem] text-white opacity-5"></i>
                    </div>

                    <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="space-y-8">
                            <div>
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Ministrante / Líder</h4>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 font-black text-xl">
                                        {{ substr($cellMeeting->leader->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xl font-bold text-gray-900 leading-tight">{{ $cellMeeting->leader->name }}</p>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-tighter">{{ $cellMeeting->leader->role }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Conteúdo Espiritual</h4>
                                <div class="space-y-4">
                                    <p class="text-2xl font-black text-blue-600 italic tracking-tight leading-snug">
                                        "{{ $cellMeeting->theme ?? 'Maturidade Cristã' }}"
                                    </p>
                                    @if($cellMeeting->biblical_text)
                                        <div class="flex items-center gap-3 text-gray-500 font-bold">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                                <i class="bi bi-book"></i>
                                            </div>
                                            <span>{{ $cellMeeting->biblical_text }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-[2rem] p-8 border border-gray-100 flex flex-col justify-center">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center mb-8">Participação</h4>
                            <div class="grid grid-cols-2 gap-y-8 gap-x-4">
                                <div class="text-center">
                                    <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ $cellMeeting->adults_count }}</p>
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-1">Adultos</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ $cellMeeting->children_count }}</p>
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-1">Crianças</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ $cellMeeting->visitors_count }}</p>
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-1">Visitantes</p>
                                </div>
                                <div class="text-center group">
                                    <p class="text-4xl font-black text-blue-600 tracking-tighter group-hover:scale-110 transition-transform">
                                        {{ $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count }}
                                    </p>
                                    <p class="text-[9px] text-blue-500 font-black uppercase tracking-widest mt-1">Total</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Minutos / Ata (Visible only if exists) -->
                @if($cellMeeting->minutes)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-orange-50 px-10 py-6 border-b border-orange-100 flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-black text-orange-900 flex items-center uppercase tracking-tighter">
                                    <i class="bi bi-file-earmark-text-fill mr-3 text-orange-600"></i>
                                    Ata do Encontro
                                </h3>
                                <p class="text-xs font-bold text-orange-600/70 mt-1 uppercase tracking-widest italic">Documento Oficial de Registro</p>
                            </div>
                            <div class="text-orange-400 opacity-20"><i class="bi bi-journal-check text-4xl"></i></div>
                        </div>
                        <div class="p-10">
                            <article class="prose prose-orange max-w-none text-gray-700 font-medium leading-[1.8] text-lg">
                                {!! nl2br(e($cellMeeting->minutes)) !!}
                            </article>
                        </div>
                    </div>
                @endif

                @if($cellMeeting->decisions)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-red-50/50 px-10 py-6 border-b border-red-100">
                            <h4 class="text-xs font-black text-red-500 uppercase tracking-[0.2em] flex items-center">
                                <i class="bi bi-heart-fill mr-3"></i> Decisões e Conversões
                            </h4>
                        </div>
                        <div class="p-10">
                            <div class="p-8 bg-red-50 rounded-[2rem] text-red-900 font-bold leading-relaxed border border-red-100 text-lg">
                                {!! nl2br(e($cellMeeting->decisions)) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Coluna Lateral -->
            <div class="space-y-8">
                <!-- Participants List (Official for non-normal, or just attendance for normal) -->
                @if($cellMeeting->meeting_type !== 'normal' && $cellMeeting->participants->count() > 0)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50 px-8 py-6 border-b border-gray-100">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center">
                                <i class="bi bi-person-badge-fill mr-2 text-blue-600"></i>
                                Participantes Oficiais ({{ $cellMeeting->participants->count() }})
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @foreach($cellMeeting->participants as $participant)
                                <div class="flex items-center gap-4 p-3 rounded-2xl border border-gray-50 hover:bg-gray-50 transition-colors group">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold group-hover:bg-blue-600 group-hover:text-white transition-all">
                                        {{ substr($participant->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $participant->name }}</p>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $participant->role }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($cellMeeting->meeting_type === 'normal' && $cellMeeting->attendances && $cellMeeting->attendances->count() > 0)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-green-50 px-8 py-6 border-b border-green-100">
                            <h3 class="text-sm font-black text-green-900 uppercase tracking-widest flex items-center">
                                <i class="bi bi-check-circle-fill mr-2 text-green-600"></i>
                                Membros Ativos ({{ $cellMeeting->attendances->count() }})
                            </h3>
                        </div>
                        <div class="p-6 space-y-2">
                            @foreach($cellMeeting->attendances as $attendance)
                                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-green-50/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-[10px]">
                                            {{ substr($attendance->member->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-700">{{ $attendance->member->name }}</span>
                                    </div>
                                    <i class="bi bi-shield-check text-green-500"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($cellMeeting->observations)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-4">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Relato do Encontro</h4>
                        <div class="text-gray-600 font-medium leading-relaxed italic text-sm">
                            "{!! nl2br(e($cellMeeting->observations)) !!}"
                        </div>
                    </div>
                @endif

                <div class="p-8 bg-gray-900 rounded-[2.5rem] text-white space-y-4">
                    <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">
                        <span>Timeline</span>
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Registrado</p>
                                <p class="text-sm font-bold">{{ $cellMeeting->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500 mt-1.5"></div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Última Atualização</p>
                                <p class="text-sm font-bold">{{ $cellMeeting->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

            <!-- Participants Section -->
            @if($cellMeeting->attendances && $cellMeeting->attendances->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-green-50 px-8 py-6 border-b border-green-100">
                        <h3 class="text-xl font-black text-green-900 flex items-center">
                            <i class="bi bi-check-circle-fill mr-3 text-green-600"></i>
                            Membros Presentes ({{ $cellMeeting->attendances->count() }})
                        </h3>
                        <p class="text-sm text-green-600 mt-1">Lista de membros que participaram do encontro</p>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($cellMeeting->attendances as $attendance)
                                <div
                                    class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-green-300 transition-colors">
                                    <div
                                        class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold mr-3">
                                        {{ substr($attendance->member->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-900 text-sm">{{ $attendance->member->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $attendance->member->phone ?? 'Sem telefone' }}</p>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-green-500 text-lg"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Visitors Section -->
            @if($cellMeeting->visitors && $cellMeeting->visitors->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-purple-50 px-8 py-6 border-b border-purple-100">
                        <h3 class="text-xl font-black text-purple-900 flex items-center">
                            <i class="bi bi-person-plus-fill mr-3 text-purple-600"></i>
                            Visitantes ({{ $cellMeeting->visitors->count() }})
                        </h3>
                        <p class="text-sm text-purple-600 mt-1">Novos visitantes que participaram do encontro</p>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($cellMeeting->visitors as $visitor)
                                <div class="p-6 bg-purple-50 rounded-xl border border-purple-200">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-12 h-12 rounded-full bg-purple-200 flex items-center justify-center text-purple-700 font-bold text-lg mr-3">
                                                {{ substr($visitor->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900">{{ $visitor->name }}</p>
                                                @if($visitor->phone)
                                                    <p class="text-sm text-gray-600"><i
                                                            class="bi bi-telephone-fill mr-1"></i>{{ $visitor->phone }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($visitor->pivot->converted)
                                            <span
                                                class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold uppercase">
                                                <i class="bi bi-heart-fill mr-1"></i>Convertido
                                            </span>
                                        @endif
                                    </div>
                                    @if($visitor->pivot->notes)
                                        <div class="bg-white p-3 rounded-lg border border-purple-100 mt-3">
                                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Observações:</p>
                                            <p class="text-sm text-gray-700">{{ $visitor->pivot->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Offering Section -->
            @if($cellMeeting->offering_amount && $cellMeeting->offering_amount > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-amber-50 px-8 py-6 border-b border-amber-100">
                        <h3 class="text-xl font-black text-amber-900 flex items-center">
                            <i class="bi bi-cash-coin mr-3 text-amber-600"></i>
                            Oferta do Encontro
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-5xl font-black text-amber-600">
                                    {{ number_format($cellMeeting->offering_amount, 2, ',', '.') }} MT</p>
                                <p class="text-sm text-gray-500 mt-2 uppercase tracking-wider font-bold">Total arrecadado</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-between text-xs text-gray-400 font-medium px-4">
                <span>Registrado em: {{ $cellMeeting->created_at->format('d/m/Y H:i') }}</span>
                <span>Última atualização: {{ $cellMeeting->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
            <div class="bg-blue-600 p-6 text-white">
                <h4 class="text-xl font-bold flex items-center">
                    <i class="bi bi-envelope-paper mr-3"></i> Enviar Relatório
                </h4>
            </div>
            <form action="{{ route('cell-meetings.email', $cellMeeting) }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Endereço de
                        Email</label>
                    <input type="email" name="email" required placeholder="exemplo@email.com"
                        class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 p-4">
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="toggleEmailModal()"
                        class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition">
                        CANCELAR
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                        ENVIAR AGORA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleEmailModal() {
            const modal = document.getElementById('emailModal');
            modal.classList.toggle('hidden');
        }
    </script>
@endsection