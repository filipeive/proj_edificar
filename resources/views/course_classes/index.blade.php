@extends('layouts.app')

@section('title', 'Gestão de Turmas - Portal Life Church')
@section('page-title', 'Turmas dos Cursos')
@section('page-subtitle', 'Organização e acompanhamento de alunos e casais')

@section('header-actions')
    @php
        $canManageClasses = !auth()->user()->isPastorZona();
    @endphp
    @if($canManageClasses)
        <div class="flex items-center gap-2">
            <a href="{{ route('course-classes.export-all') }}"
                class="action-icon text-gray-600 hover:text-emerald-600 hover:bg-emerald-50" title="Exportar Excel">
                <i class="bi bi-file-earmark-spreadsheet"></i>
            </a>
            <a href="{{ route('course-classes.export-all-pdf', request()->all()) }}"
                class="action-icon text-gray-600 hover:text-rose-600 hover:bg-rose-50" title="Exportar PDF">
                <i class="bi bi-file-earmark-pdf"></i>
            </a>
            <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-plus-lg"></i>
                <span class="hidden sm:inline">Nova Turma</span>
            </a>
        </div>
    @endif
@endsection

@section('content')
<!-- Print Header -->
<div class="hidden print:block mb-6 text-center border-b pb-4">
    <h1 class="text-xl font-bold uppercase">Comunidade de Vida Cristã - Life Church</h1>
    <p class="text-xs font-semibold text-gray-600 uppercase">Relatório Geral de Turmas e Módulos</p>
    <p class="text-[10px] text-gray-500 mt-1">Gerado em {{ date('d/m/Y H:i') }}</p>
</div>

<div x-data="{ 
        view: window.innerWidth < 768 ? 'grid' : 'list',
        selected: [],
        updateView() {
            if (window.innerWidth < 768 && this.view === 'list') {
                this.view = 'grid'; 
            }
        },
        toggleAll() {
            const allIds = {{ Js::from($groupedClasses->flatten()->pluck('id')) }};
            if (this.selected.length === allIds.length) {
                this.selected = [];
            } else {
                this.selected = allIds;
            }
        }
    }"
    x-init="$watch('view', value => localStorage.setItem('course_classes_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('course_classes_view') || 'list')"
    @resize.window.debounce.500ms="updateView()"
    class="space-y-5">

    <!-- Bulk Action Bar -->
    <div x-show="selected.length > 0" x-transition
        class="fixed top-20 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none print:hidden">
        <div class="bg-gray-900 text-white rounded-xl shadow-2xl px-4 py-2.5 flex items-center gap-4 pointer-events-auto border border-gray-700">
            <div class="flex items-center gap-2">
                <span class="bg-orange-600 text-xs font-black px-2 py-0.5 rounded" x-text="selected.length"></span>
                <span class="text-xs font-medium text-gray-200">selecionadas</span>
            </div>
            <div class="h-4 w-px bg-gray-700"></div>
            <button @click="selected = []" class="text-xs font-bold text-gray-400 hover:text-white transition">
                Cancelar
            </button>
        </div>
    </div>

    <!-- Header Actions Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Módulo de Ensino</span>
                <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-800">
                    {{ $stats['total'] ?? 0 }} turmas
                </span>
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">Gestão de Turmas & Aulas</h1>
            <p class="text-xs text-gray-500 mt-0.5">Organização de turmas ativas, líderes e acompanhamento de alunos.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('courses.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-journal-bookmark text-orange-700"></i>
                <span>Ver Cursos</span>
            </a>
            <a href="{{ route('course-classes.upcoming-weddings') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-heart text-rose-600"></i>
                <span>Casamentos Próximos</span>
            </a>
            @if($canManageClasses)
                <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                    class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                    <i class="bi bi-plus-lg"></i>
                    <span>Nova Turma</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Stats KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 print:hidden">
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Total Turmas</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 mt-0.5">{{ $stats['total'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Todas as turmas</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-orange-50 text-orange-700">
                <i class="bi bi-mortarboard-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Em Andamento</p>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-700 mt-0.5">{{ $stats['active'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Turmas ativas</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700">
                <i class="bi bi-play-circle-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Alunos Alocados</p>
                <h3 class="text-xl sm:text-2xl font-black text-blue-700 mt-0.5">{{ $stats['students'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Membros em turmas</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-700">
                <i class="bi bi-people-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Concluídas</p>
                <h3 class="text-xl sm:text-2xl font-black text-purple-700 mt-0.5">{{ $stats['completed'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Finalizadas</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-purple-50 text-purple-700">
                <i class="bi bi-check-circle-fill text-lg"></i>
            </span>
        </div>
    </div>

    <!-- Filter & View Controls -->
    <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3 print:hidden">
        <form action="{{ route('course-classes.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-56">
                <select name="course_id" onchange="this.form.submit()"
                    class="w-full rounded-lg border-gray-300 px-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    <option value="">Todos os Cursos</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-44">
                <select name="status" onchange="this.form.submit()"
                    class="w-full rounded-lg border-gray-300 px-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    <option value="">Todos os Status</option>
                    <option value="em_andamento" {{ $status == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                    <option value="concluida" {{ $status == 'concluida' ? 'selected' : '' }}>Concluída</option>
                    <option value="cancelada" {{ $status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
        </form>

        <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm self-end md:self-auto">
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

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-bold text-emerald-800 flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Grouped Listing (List View) -->
    <div x-show="view === 'list'" class="space-y-4">
        @forelse($groupedClasses as $courseIdKey => $classesInGroup)
            @php $course = $courses->find($courseIdKey); @endphp
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gray-50/75 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-orange-100 text-orange-700 font-bold text-xs">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </span>
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">{{ $course->name ?? 'Sem Curso' }}</h3>
                            <p class="text-[10px] text-gray-500">{{ $classesInGroup->count() }} Turma(s)</p>
                        </div>
                    </div>
                    @if($course)
                        <a href="{{ route('courses.show', $courseIdKey) }}" class="text-[11px] font-bold text-orange-700 hover:underline inline-flex items-center gap-1 print:hidden">
                            <span>Ver Curso</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/50 text-[10px] font-black uppercase tracking-wider text-gray-500">
                                @if($canManageClasses)
                                    <th class="px-4 py-2.5 w-8 print:hidden"></th>
                                @endif
                                <th class="px-4 py-2.5">Nome da Turma</th>
                                <th class="px-4 py-2.5">Professores / Responsáveis</th>
                                <th class="px-4 py-2.5 text-center">Inscritos</th>
                                <th class="px-4 py-2.5 text-center">Status</th>
                                <th class="px-4 py-2.5 text-right print:hidden">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                            @foreach($classesInGroup as $class)
                                <tr class="hover:bg-gray-50/60 transition">
                                    @if($canManageClasses)
                                        <td class="px-4 py-3 print:hidden">
                                            <input type="checkbox" value="{{ $class->id }}" x-model="selected"
                                                class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                        </td>
                                    @endif
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-gray-900 leading-tight">{{ $class->name }}</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            {{ $class->start_date ? $class->start_date->format('d/m/Y') : 'Previsão de Início' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-700">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="flex items-center gap-1 font-medium">
                                                <i class="bi bi-person text-gray-400"></i>
                                                {{ $class->teacherMale->name ?? 'N/A' }}
                                            </span>
                                            @if($class->teacherFemale)
                                                <span class="flex items-center gap-1 text-gray-500">
                                                    <i class="bi bi-person text-pink-400"></i>
                                                    {{ $class->teacherFemale->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-900">
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-800">
                                            {{ $class->course_enrollments_count + $class->couple_enrollments_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($class->status === 'em_andamento')
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Em curso</span>
                                        @elseif($class->status === 'concluida')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800">Concluída</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-800">{{ ucfirst($class->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right print:hidden">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('course-classes.show', $class) }}" title="Detalhes"
                                                class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-orange-700 transition">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($canManageClasses)
                                                <a href="{{ route('course-classes.edit', $class) }}" title="Editar"
                                                    class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('course-classes.destroy', $class) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta turma?')">
                                                    @csrf @method('DELETE')
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
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-xs text-gray-500">
                Nenhuma turma cadastrada para os filtros selecionados.
            </div>
        @endforelse
    </div>

    <!-- Grid View -->
    <div x-show="view === 'grid'" class="space-y-5 print:hidden">
        @foreach($groupedClasses as $courseIdKey => $classesInGroup)
            @php $course = $courses->find($courseIdKey); @endphp
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">{{ $course->name ?? 'Sem Curso' }} ({{ $classesInGroup->count() }})</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4">
                    @foreach($classesInGroup as $class)
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                            :class="{'ring-2 ring-orange-500 bg-orange-50/20': selected.includes({{ $class->id }})}">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-orange-700">
                                        <i class="bi bi-mortarboard text-sm"></i>
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        @if($class->status === 'em_andamento')
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-bold text-emerald-800">Em curso</span>
                                        @elseif($class->status === 'concluida')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[9px] font-bold text-blue-800">Concluída</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-bold text-rose-800">{{ ucfirst($class->status) }}</span>
                                        @endif
                                        @if($canManageClasses)
                                            <input type="checkbox" value="{{ $class->id }}" x-model="selected"
                                                class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                        @endif
                                    </div>
                                </div>

                                <h4 class="font-bold text-gray-900 text-sm line-clamp-1 mb-0.5">{{ $class->name }}</h4>
                                <p class="text-[10px] text-gray-400 uppercase font-medium">
                                    {{ $class->start_date ? $class->start_date->format('d/m/Y') : 'Início a definir' }}
                                </p>

                                <div class="mt-3 pt-2 border-t border-gray-100 text-xs text-gray-600 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px]">Professor:</span>
                                        <span class="font-medium text-gray-900 truncate max-w-[140px]">{{ $class->teacherMale->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px]">Inscritos:</span>
                                        <span class="font-bold text-orange-700">{{ $class->course_enrollments_count + $class->couple_enrollments_count }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-end gap-1">
                                <a href="{{ route('course-classes.show', $class) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                @if($canManageClasses)
                                    <a href="{{ route('course-classes.edit', $class) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-blue-700 hover:bg-blue-50 transition">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
