@extends('layouts.app')

@section('title', 'Cursos e Formação - Portal Life Church')
@section('page-title', 'Academia & Cursos')
@section('page-subtitle', 'Gerencie a formação ministerial e cursos da igreja')

@section('header-actions')
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor' || auth()->user()->role === 'pastor_senior')
        <div class="flex items-center gap-2">
            <a href="{{ route('courses.export-excel') ?? route('courses.export-global') }}"
                class="action-icon text-gray-600 hover:text-emerald-600 hover:bg-emerald-50" title="Exportar Excel">
                <i class="bi bi-file-earmark-spreadsheet"></i>
            </a>
            <a href="{{ route('courses.export-pdf') }}"
                class="action-icon text-gray-600 hover:text-rose-600 hover:bg-rose-50" title="Exportar PDF">
                <i class="bi bi-file-earmark-pdf"></i>
            </a>
            <a href="{{ route('courses.create') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-plus-lg"></i>
                <span class="hidden sm:inline">Novo Curso</span>
            </a>
        </div>
    @endif
@endsection

@section('content')
<!-- Print Header -->
<div class="hidden print:block mb-6 text-center border-b pb-4">
    <h1 class="text-xl font-bold uppercase">Comunidade de Vida Cristã - Life Church</h1>
    <p class="text-xs font-semibold text-gray-600 uppercase">Relatório Geral de Cursos e Formação Ministerial</p>
    <p class="text-[10px] text-gray-500 mt-1">Gerado em {{ date('d/m/Y H:i') }}</p>
</div>

<div class="space-y-5">
    <!-- Header Summary Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Módulo de Ensino</span>
                <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-800">
                    {{ $stats['total'] ?? $allCourses->count() }} cursos
                </span>
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">Academia & Formação Ministerial</h1>
            <p class="text-xs text-gray-500 mt-0.5">Gerencie os módulos de capacitação e acompanhe inscrições.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('course-classes.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-mortarboard text-orange-700"></i>
                <span>Gerir Turmas</span>
            </a>
            <a href="{{ route('couple-enrollments.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-heart text-rose-600"></i>
                <span>Inscrições Casais</span>
            </a>
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor' || auth()->user()->role === 'pastor_senior')
                <a href="{{ route('courses.create') }}"
                    class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                    <i class="bi bi-plus-lg"></i>
                    <span>Novo Curso</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Stats KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 print:hidden">
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Total Cursos</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 mt-0.5">{{ $stats['total'] ?? $allCourses->count() }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Cadastrados</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-orange-50 text-orange-700">
                <i class="bi bi-journal-bookmark-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Inscrições Abertas</p>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-700 mt-0.5">{{ $stats['open'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Disponíveis</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700">
                <i class="bi bi-door-open-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Total Alunos</p>
                <h3 class="text-xl sm:text-2xl font-black text-blue-700 mt-0.5">{{ $stats['students'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Matriculados</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-700">
                <i class="bi bi-people-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Turmas Ativas</p>
                <h3 class="text-xl sm:text-2xl font-black text-purple-700 mt-0.5">{{ $stats['classes'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Em andamento</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-purple-50 text-purple-700">
                <i class="bi bi-mortarboard-fill text-lg"></i>
            </span>
        </div>
    </div>

    @if($enrolledCourses->isNotEmpty())
        <!-- Meus Cursos Section -->
        <section class="space-y-3 print:hidden">
            <div class="flex items-center gap-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Minha Formação</span>
                <h2 class="text-sm font-bold text-gray-900">Meus Cursos</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                @foreach($enrolledCourses as $course)
                    @include('courses.partials.course-card', ['course' => $course, 'type' => 'enrolled'])
                @endforeach
            </div>
        </section>
    @endif

    <!-- Cursos Disponíveis Section -->
    <section class="space-y-3 print:hidden">
        <div class="flex items-center gap-2">
            <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Inscrições</span>
            <h2 class="text-sm font-bold text-gray-900">Cursos Disponíveis</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
            @forelse($availableCourses as $course)
                @include('courses.partials.course-card', ['course' => $course, 'type' => 'available'])
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-6 sm:p-8 text-center text-xs text-gray-500">
                    Você já está matriculado nos módulos ativos ou não há novas inscrições abertas no momento.
                </div>
            @endforelse
        </div>
    </section>

    @if((auth()->user()->role === 'admin' || auth()->user()->role === 'pastor' || auth()->user()->role === 'pastor_senior') && $allCourses->isNotEmpty())
        <!-- Admin: Todos os Cursos Monitoramento -->
        <section class="space-y-3 pt-2"
            x-data="{  
                view: window.innerWidth < 768 ? 'grid' : 'list',
                selected: [],
                updateView() {
                    if (window.innerWidth < 768 && this.view === 'list') {
                        this.view = 'grid';
                    }
                },
                toggleAll() {
                    const allIds = {{ Js::from($allCourses->pluck('id')) }};
                    if (this.selected.length === allIds.length) {
                        this.selected = [];
                    } else {
                        this.selected = allIds;
                    }
                }
            }"
            x-init="$watch('view', value => localStorage.setItem('courses_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('courses_view') || 'list')"
            @resize.window.debounce.500ms="updateView()">

            <!-- Bulk Action Bar -->
            <div x-show="selected.length > 0" x-transition
                class="fixed top-20 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none print:hidden">
                <div class="bg-gray-900 text-white rounded-xl shadow-2xl px-4 py-2.5 flex items-center gap-4 pointer-events-auto border border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="bg-orange-600 text-xs font-black px-2 py-0.5 rounded" x-text="selected.length"></span>
                        <span class="text-xs font-medium text-gray-200">selecionados</span>
                    </div>
                    <div class="h-4 w-px bg-gray-700"></div>
                    <button @click="selected = []" class="text-xs font-bold text-gray-400 hover:text-white transition">
                        Cancelar
                    </button>
                    @if(auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('courses.bulk-delete') }}" @submit.prevent="
                            if (confirm('Tem certeza que deseja excluir ' + selected.length + ' curso(s)? Esta ação é irreversível.')) {
                                $el.submit();
                            }
                        ">
                            @csrf
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="course_ids[]" :value="id">
                            </template>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Table Header Control -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Administração</span>
                    <h2 class="text-sm font-bold text-gray-900">Monitorização Global de Cursos</h2>
                </div>

                <div class="flex items-center gap-2 self-end sm:self-auto print:hidden">
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
            </div>

            <!-- List View Table -->
            <div x-show="view === 'list'" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/75 text-[10px] font-black uppercase tracking-wider text-gray-500">
                                @if(auth()->user()->role === 'admin')
                                    <th class="px-4 py-3 w-8 print:hidden">
                                        <input type="checkbox" @click="toggleAll()"
                                            :checked="selected.length === {{ $allCourses->count() }} && selected.length > 0"
                                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                    </th>
                                @endif
                                <th class="px-4 py-3">Curso</th>
                                <th class="px-4 py-3">Categoria</th>
                                <th class="px-4 py-3 text-center">Alunos</th>
                                <th class="px-4 py-3 text-center">Inscrições</th>
                                <th class="px-4 py-3 text-right print:hidden">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                            @foreach($allCourses as $course)
                                <tr class="hover:bg-gray-50/60 transition">
                                    @if(auth()->user()->role === 'admin')
                                        <td class="px-4 py-3 print:hidden">
                                            <input type="checkbox" value="{{ $course->id }}" x-model="selected"
                                                class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                        </td>
                                    @endif
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-orange-50 text-orange-700 font-bold text-xs">
                                                {{ strtoupper(substr($course->name, 0, 1)) }}
                                            </span>
                                            <div>
                                                <h5 class="font-bold text-gray-900 leading-tight">{{ $course->name }}</h5>
                                                <span class="text-[10px] font-medium text-gray-400">{{ $course->slug }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-700">
                                            {{ $course->category ?? 'ACADEMIA' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-900">
                                        {{ $course->enrollments_count + $course->couple_enrollments_count }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($course->registration_open)
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Abertas</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-800">Fechadas</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right print:hidden">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('courses.show', $course) }}" title="Detalhes"
                                                class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-orange-700 transition">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('courses.edit', $course) }}" title="Editar"
                                                class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(auth()->user()->role === 'admin')
                                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir este curso?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-red-600 transition" title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grid View -->
            <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4 print:hidden">
                @foreach($allCourses as $course)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                        :class="{'ring-2 ring-orange-500 bg-orange-50/20': selected.includes({{ $course->id }})}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-orange-700 font-bold text-sm">
                                    {{ strtoupper(substr($course->name, 0, 1)) }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    @if($course->registration_open)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-bold text-emerald-800">Abertas</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-bold text-rose-800">Fechadas</span>
                                    @endif
                                    @if(auth()->user()->role === 'admin')
                                        <input type="checkbox" value="{{ $course->id }}" x-model="selected"
                                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                    @endif
                                </div>
                            </div>

                            <h5 class="font-bold text-gray-900 text-sm line-clamp-1 mb-0.5">{{ $course->name }}</h5>
                            <p class="text-[10px] font-medium text-gray-400 uppercase">{{ $course->category ?? 'ACADEMIA' }}</p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600">
                            <span>Alunos: <strong class="text-gray-900">{{ $course->enrollments_count + $course->couple_enrollments_count }}</strong></span>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('courses.show', $course) }}" class="rounded p-1 text-gray-500 hover:text-orange-700 transition" title="Ver Detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('courses.edit', $course) }}" class="rounded p-1 text-gray-500 hover:text-blue-600 transition" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
