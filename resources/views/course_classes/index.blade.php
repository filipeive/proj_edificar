@extends('layouts.app')

@section('title', 'Gestão de Turmas - Portal Life Church')
@section('page-title', 'Turmas dos Cursos')
@section('page-subtitle', 'Organização e acompanhamento de alunos e casais')

@section('header-actions')
    @php
        $canManageClasses = !auth()->user()->isPastorZona();
    @endphp
    @if($canManageClasses)
        <div class="md:hidden">
            <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                class="text-gray-600 hover:text-orange-600 p-2.5 hover:bg-orange-50 rounded-xl transition-all duration-300 flex items-center justify-center">
                <i class="bi bi-plus-circle text-2xl"></i>
            </a>
        </div>
    @endif
@endsection

@section('content')
    <!-- Print Header -->
    <div class="hidden print:block mb-8 text-center border-b pb-4">
        <h1 class="text-2xl font-bold uppercase tracking-wide">Comunidade de Vida Cristã - Life Church</h1>
        <p class="text-sm font-semibold text-gray-600 uppercase">Relatório Geral de Turmas e Módulos</p>
        <p class="text-xs text-gray-500 mt-1">Gerado em {{ date('d/m/Y H:i') }}</p>
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
        class="w-full space-y-6">

        <!-- Desktop Header Action Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-orange-500">Módulo de Ensino</p>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Gestão de Turmas</h2>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">Organização de turmas ativas, líderes e acompanhamento de alunos.</p>
            </div>

            @if($canManageClasses)
                <div class="hidden md:flex flex-wrap items-center gap-2">
                    <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                        class="bg-orange-600 text-white px-4 py-2.5 rounded-xl hover:bg-orange-700 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-md shadow-orange-600/20">
                        <i class="bi bi-plus-lg text-sm"></i>
                        <span>Nova Turma</span>
                    </a>

                    <a href="{{ route('course-classes.export-all') }}"
                        class="bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/60 px-4 py-2.5 rounded-xl hover:bg-indigo-100 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-sm"></i>
                        <span>Excel</span>
                    </a>

                    <a href="{{ route('course-classes.export-all-pdf', request()->all()) }}"
                        class="bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 px-4 py-2.5 rounded-xl hover:bg-rose-100 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm">
                        <i class="bi bi-file-earmark-pdf-fill text-sm"></i>
                        <span>PDF</span>
                    </a>

                    <button onclick="window.print()"
                        class="bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 px-4 py-2.5 rounded-xl hover:bg-black dark:hover:bg-white transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm">
                        <i class="bi bi-printer-fill text-sm"></i>
                        <span>Imprimir</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Top Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:hidden">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total de Turmas</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Todas as turmas</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Em Andamento</p>
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['active'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Turmas ativas</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-play-circle-fill"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-500">Alunos Alocados</p>
                    <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $stats['students'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Membros em turmas</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Cursos com Turma</p>
                    <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['courses'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Módulos em oferta</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-journal-check"></i>
                </div>
            </div>
        </div>

        <!-- Bulk Action Bar -->
        @if($canManageClasses)
            <div x-show="selected.length > 0" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none print:hidden">
                <div class="bg-gray-900 text-white rounded-2xl shadow-2xl p-4 flex items-center gap-6 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                    <div class="flex items-center gap-3 pl-2">
                        <span class="bg-orange-600 text-xs font-black px-2.5 py-1 rounded-lg" x-text="selected.length"></span>
                        <span class="text-sm font-medium">selecionados</span>
                    </div>

                    <div class="h-8 w-px bg-gray-700"></div>

                    <div class="flex items-center gap-2">
                        <button @click="selected = []" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors">
                            Cancelar
                        </button>

                        <form method="GET" action="{{ route('course-classes.export-all') }}" target="_blank">
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="class_ids[]" :value="id">
                            </template>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar Excel
                            </button>
                        </form>

                        @if(auth()->user()->role === 'admin')
                            <form method="POST" action="{{ route('course-classes.bulk-delete') }}" 
                                  @submit.prevent="
                                    Swal.fire({
                                        title: 'Confirmação de Exclusão',
                                        text: 'Tem certeza que deseja excluir ' + selected.length + ' turma(s)? Esta ação é irreversível.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'Sim, excluir!',
                                        cancelButtonText: 'Cancelar'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $el.submit();
                                        }
                                    })
                                  ">
                                @csrf
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="class_ids[]" :value="id">
                                </template>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
                                    <i class="bi bi-trash-fill"></i> Excluir
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Header Controls Bar -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col xl:flex-row justify-between items-center gap-4 print:hidden">
            <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
                <form action="{{ route('course-classes.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="relative min-w-[200px]">
                        <select name="course_id" onchange="this.form.submit()"
                            class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-700 dark:text-gray-200">
                            <option value="">Todos os Cursos</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative min-w-[150px]">
                        <select name="status" onchange="this.form.submit()"
                            class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-700 dark:text-gray-200">
                            <option value="">Todos os Status</option>
                            <option value="em_andamento" {{ $status == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="concluida" {{ $status == 'concluida' ? 'selected' : '' }}>Concluída</option>
                            <option value="cancelada" {{ $status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white dark:bg-gray-600 text-orange-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 text-[10px] font-black uppercase">
                        <i class="bi bi-list-ul"></i>
                        <span>Lista</span>
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white dark:bg-gray-600 text-orange-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 text-[10px] font-black uppercase">
                        <i class="bi bi-grid-fill"></i>
                        <span>Grid</span>
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 p-4 rounded-2xl flex items-center gap-3 text-xs font-bold">
                <i class="bi bi-check-circle-fill text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Grouped Listing -->
        <div x-show="view === 'list'" class="space-y-6">
            @forelse($groupedClasses as $courseIdKey => $classesInGroup)
                @php $course = $courses->find($courseIdKey); @endphp
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-8 py-5 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center font-black">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">{{ $course->name ?? 'Sem Curso' }}</h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $classesInGroup->count() }} Turma(s)</p>
                            </div>
                        </div>
                        <a href="{{ route('courses.show', $courseIdKey) }}" class="text-[10px] font-black uppercase tracking-widest text-orange-600 hover:text-orange-700 flex items-center gap-1 print:hidden">
                            Ver Curso <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white/50 dark:bg-gray-800/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                                    @if($canManageClasses)
                                        <th class="px-6 py-4 w-10 print:hidden"></th>
                                    @endif
                                    <th class="px-6 py-4">Nome da Turma</th>
                                    <th class="px-6 py-4">Professores / Responsáveis</th>
                                    <th class="px-6 py-4 text-center">Inscritos</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right print:hidden">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @foreach($classesInGroup as $class)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                                        @if($canManageClasses)
                                            <td class="px-6 py-5 print:hidden">
                                                <input type="checkbox" value="{{ $class->id }}" x-model="selected"
                                                    class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 w-4 h-4">
                                            </td>
                                        @endif
                                        <td class="px-6 py-5">
                                            <div class="font-black text-gray-900 dark:text-white text-sm leading-snug">{{ $class->name }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase">
                                                {{ $class->start_date ? $class->start_date->format('d/m/Y') : 'Previsão de Início' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-xs text-gray-700 dark:text-gray-300 font-bold uppercase">
                                            <div class="flex flex-col gap-0.5">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="bi bi-person text-orange-500"></i>
                                                    {{ $class->teacherMale->name ?? 'N/A' }}
                                                </span>
                                                @if($class->teacherFemale)
                                                    <span class="flex items-center gap-1.5">
                                                        <i class="bi bi-person-heart text-pink-500"></i>
                                                        {{ $class->teacherFemale->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-full text-xs font-black">
                                                {{ $class->course_enrollments_count + $class->couple_enrollments_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            @if($class->status === 'em_andamento')
                                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[9px] font-black uppercase rounded-full border border-emerald-200">Em curso</span>
                                            @elseif($class->status === 'concluida')
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 text-[9px] font-black uppercase rounded-full border border-blue-200">Concluída</span>
                                            @else
                                                <span class="px-3 py-1 bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 text-[9px] font-black uppercase rounded-full border border-rose-200">{{ ucfirst($class->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5 text-right print:hidden">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('course-classes.show', $class) }}" title="Detalhes"
                                                    class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-purple-600 hover:border-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all flex items-center justify-center">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                @if($canManageClasses)
                                                    <a href="{{ route('course-classes.edit', $class) }}" title="Editar"
                                                        class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all flex items-center justify-center">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    <form action="{{ route('course-classes.destroy', $class) }}" method="POST" onsubmit="return confirm('Excluir turma?')">
                                                        @csrf @method('DELETE')
                                                        <button class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all flex items-center justify-center" title="Excluir">
                                                            <i class="bi bi-trash-fill"></i>
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
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-16 text-center border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700 rounded-3xl flex items-center justify-center text-gray-300 mx-auto mb-4">
                        <i class="bi bi-mortarboard text-3xl"></i>
                    </div>
                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Nenhuma turma encontrada</p>
                </div>
            @endforelse
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 print:hidden">
            @foreach($groupedClasses as $courseIdKey => $classesInGrid)
                @foreach($classesInGrid as $class)
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-orange-600 text-white flex items-center justify-center text-xl font-black shadow-md shadow-orange-600/20">
                                    {{ strtoupper(substr($class->name, 0, 1)) }}
                                </div>
                                <span class="px-2.5 py-1 bg-orange-50 text-orange-600 dark:bg-orange-950/40 dark:text-orange-400 rounded-full text-[9px] font-black uppercase">
                                    {{ $class->status }}
                                </span>
                            </div>

                            <h4 class="text-base font-black text-gray-900 dark:text-white mb-1 leading-snug">{{ $class->name }}</h4>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-4 truncate">
                                {{ $class->course->name ?? 'Sem Curso' }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500">Inscritos: {{ $class->course_enrollments_count + $class->couple_enrollments_count }}</span>
                            <a href="{{ route('course-classes.show', $class) }}" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase">Detalhes</a>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@endsection
