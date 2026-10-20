@extends('layouts.app')

@section('title', 'Detalhes do Encontro - Portal Life Church')
@section('page-title', 'Detalhes do Encontro')
@section('page-subtitle', 'Informações consolidadas da reunião')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('cell-meetings.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
            <span>Voltar</span>
        </a>
        <button onclick="toggleEmailModal()"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            title="Enviar por email">
            <i class="bi bi-envelope text-blue-600 text-xs"></i>
            <span class="hidden sm:inline">Email</span>
        </button>
        <a href="{{ route('cell-meetings.pdf', $cellMeeting) }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf text-red-600 text-xs"></i>
            <span class="hidden sm:inline">PDF</span>
        </a>
        <a href="{{ route('cell-meetings.export', ['meeting_id' => $cellMeeting->id]) }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            title="Exportar Excel">
            <i class="bi bi-file-earmark-excel text-emerald-600 text-xs"></i>
            <span class="hidden sm:inline">Excel</span>
        </a>
        @can('update', $cellMeeting)
            <a href="{{ route('cell-meetings.edit', $cellMeeting) }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors"
                title="Editar">
                <i class="bi bi-pencil-square text-xs"></i>
                <span>Editar</span>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Top Navigation & Action Bar (Desktop) -->
        <div class="hidden md:flex items-center justify-between gap-3">
            <a href="{{ route('cell-meetings.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:text-orange-700 dark:hover:text-orange-400 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Encontros</span>
            </a>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleEmailModal()"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
                    title="Enviar por email">
                    <i class="bi bi-envelope text-blue-600 text-xs"></i>
                    <span>Email</span>
                </button>
                <a href="{{ route('cell-meetings.pdf', $cellMeeting) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
                    title="Exportar PDF">
                    <i class="bi bi-file-earmark-pdf text-red-600 text-xs"></i>
                    <span>PDF</span>
                </a>
                <a href="{{ route('cell-meetings.export', ['meeting_id' => $cellMeeting->id]) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
                    title="Exportar Excel">
                    <i class="bi bi-file-earmark-excel text-emerald-600 text-xs"></i>
                    <span>Excel</span>
                </a>
                @can('update', $cellMeeting)
                    <a href="{{ route('cell-meetings.edit', $cellMeeting) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors"
                        title="Editar">
                        <i class="bi bi-pencil-square text-xs"></i>
                        <span>Editar</span>
                    </a>
                @endcan
            </div>
        </div>

        <!-- Executive Banner -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        @php
                            $typeStyles = [
                                'normal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'leadership' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'supervision' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'zone' => 'bg-blue-50 text-blue-700 border-blue-200',
                            ];
                            $typeLabel = [
                                'normal' => 'Reunião de Célula',
                                'leadership' => 'Liderança',
                                'supervision' => 'Supervisão',
                                'zone' => 'Zona',
                            ];
                            $style = $typeStyles[$cellMeeting->meeting_type] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                        @endphp
                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-black uppercase tracking-wider border {{ $style }}">
                            {{ $typeLabel[$cellMeeting->meeting_type] ?? 'Encontro Especial' }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-700">
                            <i class="bi bi-calendar3 text-[9px]"></i> {{ $cellMeeting->meeting_date->format('d/m/Y') }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                        {{ $cellMeeting->cell?->name ?? $cellMeeting->zone?->name ?? $cellMeeting->supervision?->name ?? $cellMeeting->meeting_type_label }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 pt-0.5">
                        @if($cellMeeting->leader)
                            <span class="font-semibold text-gray-700">
                                Líder: <strong class="text-gray-900">{{ $cellMeeting->leader->name }}</strong>
                            </span>
                        @endif
                        @if($cellMeeting->cell && $cellMeeting->cell->supervision)
                            <span>Supervisão: <strong class="text-gray-900">{{ $cellMeeting->cell->supervision->name }}</strong></span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3 self-start sm:self-auto">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Participação</p>
                        <p class="text-xl font-black text-orange-700">{{ $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Adultos</p>
                    <p class="mt-1 text-2xl font-black text-blue-700 tracking-tight">{{ $cellMeeting->adults_count }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <i class="bi bi-person text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Crianças</p>
                    <p class="mt-1 text-2xl font-black text-emerald-700 tracking-tight">{{ $cellMeeting->children_count }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i class="bi bi-emoji-smile text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Visitantes</p>
                    <p class="mt-1 text-2xl font-black text-purple-700 tracking-tight">{{ $cellMeeting->visitors_count }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                    <i class="bi bi-person-plus text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Oferta Total</p>
                    <p class="mt-1 text-2xl font-black text-amber-700 tracking-tight">
                        {{ number_format($cellMeeting->offering_amount, 0, ',', '.') }} <span class="text-xs font-bold text-gray-400">MT</span>
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <i class="bi bi-cash-stack text-lg"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Coluna Principal (Conteúdo, Ata, Decisões, Presenças) -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Ministração -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                    <div class="flex items-center gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-book text-orange-700 text-sm"></i>
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Tema e Ministração</h3>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase text-gray-400">Mensagem Central</p>
                        <p class="text-lg font-black text-gray-900 tracking-tight mt-0.5">
                            "{{ $cellMeeting->theme ?? 'Sem tema cadastrado' }}"
                        </p>
                        @if($cellMeeting->biblical_text)
                            <div class="mt-2 inline-flex items-center gap-1.5 rounded-md bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-800 border border-orange-200/50">
                                <i class="bi bi-bookmark-fill text-xs"></i>
                                <span>{{ $cellMeeting->biblical_text }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ata / Ocorrências -->
                @if($cellMeeting->minutes)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-2.5">
                        <div class="flex items-center gap-2 border-b border-gray-100 pb-2">
                            <i class="bi bi-file-earmark-text text-orange-700 text-sm"></i>
                            <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Ata do Encontro</h3>
                        </div>
                        <div class="text-xs text-gray-700 leading-relaxed font-normal whitespace-pre-line bg-gray-50/60 p-3.5 rounded-lg border border-gray-100">
                            {{ $cellMeeting->minutes }}
                        </div>
                    </div>
                @endif

                <!-- Decisões -->
                @if($cellMeeting->decisions)
                    <div class="rounded-xl border border-red-200 bg-red-50/40 p-4 sm:p-5 shadow-sm space-y-2">
                        <div class="flex items-center gap-2 text-red-700">
                            <i class="bi bi-heart-fill text-sm"></i>
                            <h3 class="text-xs font-black uppercase tracking-wider">Decisões e Vidas Entregues</h3>
                        </div>
                        <div class="text-xs text-red-900 leading-relaxed font-semibold italic bg-white p-3 rounded-lg border border-red-200">
                            {{ $cellMeeting->decisions }}
                        </div>
                    </div>
                @endif

                <!-- Lista de Presença -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 bg-gray-50/60">
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Rol de Participantes</h3>
                            <p class="text-[11px] text-gray-500">Membros e visitantes registrados no encontro</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                {{ $cellMeeting->attendances?->count() ?? 0 }} membros
                            </span>
                            <span class="inline-flex items-center rounded-full bg-purple-50 px-2 py-0.5 text-[10px] font-bold text-purple-700">
                                {{ $cellMeeting->visitors?->count() ?? 0 }} visitantes
                            </span>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        <!-- Oficiais (se reunião especial) -->
                        @if($cellMeeting->meeting_type !== 'normal' && $cellMeeting->participants->count() > 0)
                            <div class="space-y-1.5">
                                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Participantes Oficiais</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($cellMeeting->participants as $participant)
                                        <div class="flex items-center gap-2.5 rounded-lg border border-gray-200 p-2.5 bg-gray-50/50">
                                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-50 font-bold text-orange-700 text-xs">
                                                {{ substr($participant->name, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-900 truncate">{{ $participant->name }}</p>
                                                <p class="text-[10px] text-gray-500 uppercase">{{ $participant->role }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Presença de Membros da Célula -->
                        @if($cellMeeting->meeting_type === 'normal' && $cellMeeting->attendances && $cellMeeting->attendances->count() > 0)
                            <div class="space-y-1.5">
                                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Presença de Membros</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($cellMeeting->attendances as $attendance)
                                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-2.5 bg-white">
                                            <div class="min-w-0">
                                                <p class="text-xs font-semibold text-gray-900 truncate">{{ $attendance->member->name ?? 'Membro' }}</p>
                                                @if(!$attendance->status && $attendance->reason)
                                                    <p class="text-[10px] text-red-500 italic truncate">"{{ $attendance->reason }}"</p>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $attendance->status ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                                {{ $attendance->status ? 'Presente' : 'Ausente' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Visitantes -->
                        @if($cellMeeting->visitors && $cellMeeting->visitors->count() > 0)
                            <div class="space-y-1.5 pt-2 border-t border-gray-100">
                                <p class="text-[10px] font-black uppercase tracking-wider text-purple-700">Visitantes</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($cellMeeting->visitors as $visitor)
                                        <div class="rounded-lg border border-purple-200 bg-purple-50/30 p-2.5 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <p class="text-xs font-bold text-gray-900 truncate">{{ $visitor->name }}</p>
                                                @if($visitor->isIntegrated())
                                                    <span class="inline-flex items-center rounded-full bg-red-100 px-1.5 py-0.5 text-[9px] font-black text-red-700">
                                                        Decisão
                                                    </span>
                                                @endif
                                            </div>
                                            @if($visitor->notes)
                                                <p class="text-[11px] text-gray-500 italic">"{{ $visitor->notes }}"</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral (Observações e Ações) -->
            <div class="space-y-5">
                <!-- Observações do Líder -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm space-y-2">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Observações Pastorais</h3>
                    @if($cellMeeting->observations)
                        <p class="text-xs text-gray-600 italic bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed">
                            "{!! nl2br(e($cellMeeting->observations)) !!}"
                        </p>
                    @else
                        <p class="text-xs text-gray-400 italic">Nenhuma observação informada.</p>
                    @endif
                </div>

                <!-- Detalhes de Registro -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Metadados do Registro</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-500">Cadastrado em:</span>
                            <span class="font-bold text-gray-900">{{ $cellMeeting->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-500">Última atualização:</span>
                            <span class="font-bold text-gray-900">{{ $cellMeeting->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/60 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider">Enviar Relatório</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Envio digital do relatório do encontro</p>
                </div>
                <button type="button" onclick="toggleEmailModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>
            <form action="{{ route('cell-meetings.email', $cellMeeting) }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700">Destinatário</label>
                    <input type="email" name="email" required placeholder="exemplo@igreja.com"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="toggleEmailModal()"
                        class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                        <i class="bi bi-send text-xs"></i>
                        <span>Enviar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleEmailModal() {
            const modal = document.getElementById('emailModal');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>
@endsection
