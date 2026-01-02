@extends('layouts.app')

@section('title', 'Detalhes do Encontro')
@section('page-title', 'Detalhes do Encontro')
@section('page-subtitle', 'Informações completas sobre a reunião de célula')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('cell-meetings.index') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
            <div class="flex space-x-2">
                <button onclick="toggleEmailModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                    <i class="bi bi-envelope mr-2"></i> Partilhar Email
                </button>
                <a href="{{ route('cell-meetings.pdf', $cellMeeting) }}"
                    class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                    <i class="bi bi-file-earmark-pdf mr-2"></i> Exportar PDF
                </a>
                @can('update', $cellMeeting)
                    <a href="{{ route('cell-meetings.edit', $cellMeeting) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                        <i class="bi bi-pencil mr-2"></i> Editar
                    </a>
                @endcan
                @can('delete', $cellMeeting)
                    <form action="{{ route('cell-meetings.destroy', $cellMeeting) }}" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir este encontro?')">
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

        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Card Principal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-blue-600 px-8 py-10 text-white relative">
                    <div class="relative z-10">
                        <p class="text-blue-200 text-sm font-bold uppercase tracking-widest mb-2">Encontro de Célula</p>
                        <h3 class="text-4xl font-black mb-4">{{ $cellMeeting->cell->name }}</h3>
                        <div class="flex flex-wrap gap-6 text-sm">
                            <span class="flex items-center"><i class="bi bi-calendar3 mr-2"></i>
                                {{ $cellMeeting->meeting_date->format('d/m/Y') }}</span>
                            <span class="flex items-center"><i class="bi bi-diagram-3 mr-2"></i>
                                {{ $cellMeeting->cell->supervision->name }}</span>
                            <span class="flex items-center"><i class="bi bi-geo-alt mr-2"></i>
                                {{ $cellMeeting->cell->supervision->zone->name }}</span>
                        </div>
                    </div>
                    <i class="bi bi-people-fill absolute right-8 bottom-4 text-8xl text-white opacity-10"></i>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Líder do Encontro
                            </h4>
                            <p class="text-xl font-bold text-gray-800">{{ $cellMeeting->leader->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tema e Texto Bíblico
                            </h4>
                            <p class="text-xl font-bold text-blue-600 italic">
                                "{{ $cellMeeting->theme ?? 'Sem tema registrado' }}"
                            </p>
                            @if($cellMeeting->biblical_text)
                                <p class="text-sm text-gray-500 mt-1 font-semibold">
                                    <i class="bi bi-book mr-1"></i> {{ $cellMeeting->biblical_text }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 text-center">Participação
                            Total</h4>
                        <div class="flex justify-around items-center">
                            <div class="text-center">
                                <p class="text-2xl font-black text-gray-800">{{ $cellMeeting->adults_count }}</p>
                                <p class="text-[10px] text-gray-500 font-bold uppercase">Adultos</p>
                            </div>
                            <div class="h-10 w-px bg-gray-200"></div>
                            <div class="text-center">
                                <p class="text-2xl font-black text-gray-800">{{ $cellMeeting->children_count }}</p>
                                <p class="text-[10px] text-gray-500 font-bold uppercase">Crianças</p>
                            </div>
                            <div class="h-10 w-px bg-gray-200"></div>
                            <div class="text-center">
                                <p class="text-2xl font-black text-gray-800">{{ $cellMeeting->visitors_count }}</p>
                                <p class="text-[10px] text-gray-500 font-bold uppercase">Visitantes</p>
                            </div>
                            <div class="h-10 w-px bg-gray-200"></div>
                            <div class="text-center">
                                <p class="text-3xl font-black text-blue-600">
                                    {{ $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count }}
                                </p>
                                <p class="text-[10px] text-blue-500 font-bold uppercase">Total</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($cellMeeting->decisions)
                    <div class="px-8 pb-4">
                        <h4 class="text-xs font-bold text-orange-500 uppercase tracking-widest mb-3 flex items-center">
                            <i class="bi bi-heart-fill mr-2"></i> Decisões / Conversões
                        </h4>
                        <div class="bg-orange-50 p-6 rounded-xl text-gray-700 leading-relaxed border border-orange-100">
                            {!! nl2br(e($cellMeeting->decisions)) !!}
                        </div>
                    </div>
                @endif

                @if($cellMeeting->observations)
                    <div class="px-8 pb-8">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Observações / Relato</h4>
                        <div class="bg-gray-50 p-6 rounded-xl text-gray-700 leading-relaxed border-l-4 border-blue-500 italic">
                            {!! nl2br(e($cellMeeting->observations)) !!}
                        </div>
                    </div>
                @endif
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