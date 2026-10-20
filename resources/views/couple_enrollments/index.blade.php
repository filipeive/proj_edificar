@extends('layouts.app')

@section('title', 'Inscrições Públicas (Casais)')
@section('page-title', 'Inscrições Públicas (Casais)')
@section('page-subtitle', 'Gestão, alocação e relatórios de inscritos via formulário público')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('couple-enrollments.export-pdf', request()->all()) }}"
            class="action-icon text-gray-600 hover:text-rose-600 hover:bg-rose-50" title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        <a href="{{ route('couple-enrollments.export', request()->all()) }}"
            class="action-icon text-gray-600 hover:text-emerald-600 hover:bg-emerald-50" title="Exportar CSV">
            <i class="bi bi-file-earmark-spreadsheet"></i>
        </a>
    </div>
@endsection

@section('content')
<!-- Print Header -->
<div class="hidden print:block mb-6 text-center border-b pb-4">
    <h1 class="text-xl font-bold uppercase">Comunidade de Vida Cristã - Life Church</h1>
    <p class="text-xs font-semibold text-gray-600 uppercase">Relatório de Inscrições Públicas de Casais</p>
    <p class="text-[10px] text-gray-500 mt-1">Gerado em {{ date('d/m/Y H:i') }}</p>
</div>

<div x-data="{ 
        view: window.innerWidth < 768 ? 'grid' : 'list',
        showCommunicatorModal: false,
        customMessage: 'Graça e Paz estimado casal {casal}! Convidamos-vos a participar do nosso Encontro de Orientação para Casais no Portal Life Church.',
        updateView() {
            if (window.innerWidth < 768 && this.view === 'list') {
                this.view = 'grid'; 
            }
        }
    }"
    x-init="$watch('view', value => localStorage.setItem('couple_enrollments_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('couple_enrollments_view') || 'list')"
    @resize.window.debounce.500ms="updateView()"
    class="space-y-5">

    <!-- Header Actions Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Módulo de Casais</span>
                <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-800">
                    {{ $enrollments->total() }} inscrições
                </span>
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">Gestão de Inscrições de Casais</h1>
            <p class="text-xs text-gray-500 mt-0.5">Acompanhe novos inscritos, aloque turmas e envie notificações.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button @click="showCommunicatorModal = true"
                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-700 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-blue-800 transition">
                <i class="bi bi-chat-dots"></i>
                <span>Comunicar Casais</span>
            </button>
            <a href="{{ route('couple-enrollments.export', request()->all()) }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>CSV</span>
            </a>
            <a href="{{ route('couple-enrollments.export-pdf', request()->all()) }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-rose-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-file-earmark-pdf"></i>
                <span>PDF</span>
            </a>
            <button onclick="window.print()"
                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-black transition">
                <i class="bi bi-printer"></i>
                <span>Imprimir</span>
            </button>
        </div>
    </div>

    <!-- Stats KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 print:hidden">
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Total Casais</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 mt-0.5">{{ $stats['total'] ?? $enrollments->total() }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Inscrições</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-orange-50 text-orange-700">
                <i class="bi bi-people-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Pendentes</p>
                <h3 class="text-xl sm:text-2xl font-black text-amber-700 mt-0.5">{{ $stats['pending'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Sem turma</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-amber-50 text-amber-700">
                <i class="bi bi-hourglass-split text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Alocados</p>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-700 mt-0.5">{{ $stats['assigned'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Em turmas</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700">
                <i class="bi bi-check-circle-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Cursos</p>
                <h3 class="text-xl sm:text-2xl font-black text-blue-700 mt-0.5">{{ $stats['courses'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Com casais</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-700">
                <i class="bi bi-journal-bookmark-fill text-lg"></i>
            </span>
        </div>
    </div>

    <!-- Filters & View Mode -->
    <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm print:hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-3">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-500">Filtrar Inscrições</span>
            <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
                <button @click="view = 'list'"
                    :class="view === 'list' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-list-ul mr-1"></i> Lista
                </button>
                <button @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-grid-fill mr-1"></i> Grelha
                </button>
            </div>
        </div>

        <form action="{{ route('couple-enrollments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nome, telefone ou contato..."
                    class="w-full rounded-lg border-gray-300 pl-8 pr-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div>
                <select name="course_id" onchange="this.form.submit()"
                    class="w-full rounded-lg border-gray-300 px-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    <option value="">Todos os Cursos</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()"
                    class="w-full rounded-lg border-gray-300 px-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    <option value="">Todos os Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente (Sem Turma)</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Alocado (Com Turma)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-gray-900 px-4 py-1.5 text-xs font-bold text-white hover:bg-black transition">
                    Filtrar
                </button>
                @if(request()->anyFilled(['search', 'course_id', 'status']))
                    <a href="{{ route('couple-enrollments.index') }}" class="rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition" title="Limpar">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table View -->
    <div x-show="view === 'list'" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/75 text-[10px] font-black uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3">Casal / Nomes</th>
                        <th class="px-4 py-3">Curso Pretendido</th>
                        <th class="px-4 py-3">Relação & Tempo</th>
                        <th class="px-4 py-3">Contatos / Endereço</th>
                        <th class="px-4 py-3">Turma Alocada</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right print:hidden">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                    @forelse($enrollments as $enrollment)
                        @php
                            $relLabel = $enrollment->relationship_type === 'namoro' || $enrollment->relationship_type === 'em_relacionamento' ? 'Em relacionamento' : ucfirst(str_replace('_', ' ', $enrollment->relationship_type));
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900">{{ $enrollment->husband_name }}</div>
                                <div class="text-xs text-gray-600">& {{ $enrollment->wife_name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-bold text-orange-800 border border-orange-100">
                                    {{ $enrollment->course->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <p class="font-bold text-gray-800">{{ $relLabel }}</p>
                                <p class="text-[10px] text-gray-400">{{ $enrollment->years_together }} anos juntos</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($enrollment->husband_phone)
                                    <div class="flex items-center gap-1">
                                        <i class="bi bi-person text-blue-500"></i>
                                        <span>{{ $enrollment->husband_phone }}</span>
                                    </div>
                                @endif
                                @if($enrollment->wife_phone)
                                    <div class="flex items-center gap-1">
                                        <i class="bi bi-person text-pink-500"></i>
                                        <span>{{ $enrollment->wife_phone }}</span>
                                    </div>
                                @endif
                                @if($enrollment->address)
                                    <div class="text-[10px] text-gray-400 truncate max-w-[180px] mt-0.5">
                                        <i class="bi bi-geo-alt"></i> {{ $enrollment->address }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($enrollment->courseClass)
                                    <span class="inline-flex items-center gap-1.5 font-bold text-gray-900 text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $enrollment->courseClass->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                                        Aguardando Turma
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($enrollment->course_class_id)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                        Alocado
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right print:hidden">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('couple-enrollments.show', $enrollment) }}" title="Ver Detalhes"
                                        class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-orange-700 transition">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('couple-enrollments.edit', $enrollment) }}" title="Editar"
                                        class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">
                                Nenhuma inscrição de casal encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($enrollments->hasPages())
            <div class="p-3 border-t border-gray-100 bg-gray-50/50 print:hidden">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>

    <!-- Grid View -->
    <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4 print:hidden">
        @forelse($enrollments as $enrollment)
            @php
                $relLabel = $enrollment->relationship_type === 'namoro' || $enrollment->relationship_type === 'em_relacionamento' ? 'Em relacionamento' : ucfirst(str_replace('_', ' ', $enrollment->relationship_type));
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-bold text-orange-800 border border-orange-100">
                            {{ $enrollment->course->name ?? 'N/A' }}
                        </span>
                        @if($enrollment->course_class_id)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-bold text-emerald-800">Alocado</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-bold text-amber-800">Pendente</span>
                        @endif
                    </div>

                    <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">{{ $relLabel }} • {{ $enrollment->years_together }} anos</p>

                    <div class="mt-3 pt-2 border-t border-gray-100 text-xs text-gray-600 space-y-1">
                        @if($enrollment->courseClass)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-[11px]">Turma:</span>
                                <span class="font-bold text-gray-900">{{ $enrollment->courseClass->name }}</span>
                            </div>
                        @endif
                        @if($enrollment->husband_phone)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-[11px]">Ele:</span>
                                <span>{{ $enrollment->husband_phone }}</span>
                            </div>
                        @endif
                        @if($enrollment->wife_phone)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-[11px]">Ela:</span>
                                <span>{{ $enrollment->wife_phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end gap-1">
                    <a href="{{ route('couple-enrollments.show', $enrollment) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                    <a href="{{ route('couple-enrollments.edit', $enrollment) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-blue-700 hover:bg-blue-50 transition">
                        <i class="bi bi-pencil"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-xs text-gray-500">
                Nenhuma inscrição de casal encontrada.
            </div>
        @endforelse
    </div>

    <!-- Communication Modal -->
    <div x-show="showCommunicatorModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm print:hidden">
        <div @click.away="showCommunicatorModal = false" class="bg-white rounded-xl max-w-2xl w-full p-4 sm:p-5 shadow-2xl border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <i class="bi bi-chat-dots text-blue-600 text-lg"></i>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Comunicação com Casais via WhatsApp</h3>
                </div>
                <button @click="showCommunicatorModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-black uppercase tracking-wider text-gray-500">Modelos Prontos</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <button type="button" @click="customMessage = 'Graça e Paz estimado casal {casal}! Convidamos-vos com alegria a participar do nosso Encontro de Orientação para Casais no Portal Life Church.'"
                        class="p-2.5 bg-blue-50 border border-blue-200 rounded-lg text-left hover:bg-blue-100 transition text-xs font-bold text-blue-800">
                        <i class="bi bi-megaphone mr-1"></i> Orientação
                    </button>
                    <button type="button" @click="customMessage = 'Olá {casal}! As aulas do vosso curso de casais vão iniciar em breve. Favor fiquem atentos à programação e ao início da turma no Portal Life Church.'"
                        class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-left hover:bg-emerald-100 transition text-xs font-bold text-emerald-800">
                        <i class="bi bi-journal-check mr-1"></i> Início Turma
                    </button>
                    <button type="button" @click="customMessage = 'Paz e Graça {casal}! Recordamos que a vossa inscrição no curso de casais necessita da confirmação da turma. Por favor entrem em contato conosco.'"
                        class="p-2.5 bg-amber-50 border border-amber-200 rounded-lg text-left hover:bg-amber-100 transition text-xs font-bold text-amber-800">
                        <i class="bi bi-bell mr-1"></i> Lembrete
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">
                    Mensagem (use {casal} para substituição automática)
                </label>
                <textarea x-model="customMessage" rows="2"
                    class="w-full rounded-lg border-gray-300 p-2.5 text-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="space-y-2">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Casais da Lista ({{ count($enrollments) }})</p>
                <div class="max-h-52 overflow-y-auto space-y-1.5 pr-1 divide-y divide-gray-100">
                    @foreach($enrollments as $enrollment)
                        @php
                            $coupleName = $enrollment->husband_name . ' & ' . $enrollment->wife_name;
                            $hPhone = preg_replace('/\D/', '', $enrollment->husband_phone ?? '');
                            $wPhone = preg_replace('/\D/', '', $enrollment->wife_phone ?? '');
                        @endphp
                        <div class="pt-1.5 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ $coupleName }}</p>
                                <p class="text-[10px] text-gray-400 truncate">{{ $enrollment->course->name ?? 'Curso' }}</p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                @if($hPhone)
                                    <a :href="'https://api.whatsapp.com/send?phone={{ $hPhone }}&text=' + encodeURIComponent(customMessage.replace('{casal}', '{{ addslashes($coupleName) }}'))"
                                       target="_blank"
                                       class="px-2 py-1 bg-emerald-600 text-white rounded text-[10px] font-bold hover:bg-emerald-700">
                                        <i class="bi bi-whatsapp"></i> Ele
                                    </a>
                                @endif
                                @if($wPhone)
                                    <a :href="'https://api.whatsapp.com/send?phone={{ $wPhone }}&text=' + encodeURIComponent(customMessage.replace('{casal}', '{{ addslashes($coupleName) }}'))"
                                       target="_blank"
                                       class="px-2 py-1 bg-pink-600 text-white rounded text-[10px] font-bold hover:bg-pink-700">
                                        <i class="bi bi-whatsapp"></i> Ela
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
