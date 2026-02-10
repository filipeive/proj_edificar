@extends('layouts.app')

@section('title', 'Gestão de Turmas - Portal Life Church')
@section('page-title', 'Turmas dos Cursos')
@section('page-subtitle', 'Organização e acompanhamento de alunos e casais')

@section('content')
    @php
        $canManageClasses = !auth()->user()->isPastorZona();
    @endphp

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
        @resize.window.debounce.500ms="updateView()">

        <!-- Bulk Action Bar -->
        @if($canManageClasses)
            <div x-show="selected.length > 0" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
                <div class="bg-gray-900 text-white rounded-2xl shadow-2xl p-4 flex items-center gap-6 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                    <div class="flex items-center gap-3 pl-2">
                        <span class="bg-blue-600 text-xs font-black px-2.5 py-1 rounded-lg" x-text="selected.length"></span>
                        <span class="text-sm font-medium">selecionados</span>
                    </div>

                    <div class="h-8 w-px bg-gray-700"></div>

                    <div class="flex items-center gap-2">
                        <button @click="selected = []" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors">
                            Cancelar
                        </button>

                        <!-- Bulk Export -->
                        <form method="GET" action="{{ route('course-classes.export-all') }}" target="_blank">
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="class_ids[]" :value="id">
                            </template>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar
                            </button>
                        </form>

                        @if(auth()->user()->role === 'admin')
                            <!-- Bulk Delete -->
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

        @section('header-actions')
            @if($canManageClasses)
                <div class="flex items-center gap-2 md:hidden">
                    <a href="{{ route('course-classes.export-all') }}"
                        class="bg-indigo-50 text-indigo-600 border border-indigo-100 p-2 rounded-lg hover:bg-indigo-100 transition-all flex items-center justify-center shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet text-2xl"></i>
                    </a>
                    <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                        class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="bi bi-plus-circle text-2xl"></i>
                    </a>
                </div>
            @endif
        @endsection
    <div class="container-fluid">
        <!-- Modern Header Section -->
        <div class="bg-white dark:bg-gray-800 p-5 md:p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col xl:flex-row justify-between items-center gap-6 mb-6 transition-colors">
            <div class="flex flex-col md:flex-row items-center gap-5 w-full xl:w-auto">
                <div class="w-full md:w-auto">
                    <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-none">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-orange-600">Gestão de</span>
                        <span class="text-gray-300 dark:text-gray-600">Turmas</span>
                    </h2>
                    <p class="text-[10px] md:text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Organização por curso e categoria</p>
                </div>

                <div class="h-8 w-[1px] bg-gray-100 dark:bg-gray-700 hidden md:block"></div>

                <form action="{{ route('course-classes.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="relative group min-w-[200px]">
                        <select name="course_id" onchange="this.form.submit()"
                            class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-600 dark:text-gray-300 cursor-pointer">
                            <option value="">Todos os Cursos</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative group min-w-[150px]">
                        <select name="status" onchange="this.form.submit()"
                            class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-600 dark:text-gray-300 cursor-pointer">
                            <option value="">Todos os Status</option>
                            <option value="em_andamento" {{ $status == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="concluida" {{ $status == 'concluida' ? 'selected' : '' }}>Concluída</option>
                            <option value="cancelada" {{ $status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3">
                <!-- View Toggle -->
                <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl border border-gray-50 dark:border-gray-600">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white dark:bg-gray-600 text-orange-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
                        <i class="bi bi-list-ul"></i>
                        <span class="hidden sm:inline">Lista</span>
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white dark:bg-gray-600 text-orange-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
                        <i class="bi bi-grid-fill"></i>
                        <span class="hidden sm:inline">Grid</span>
                    </button>
                </div>

                @if($canManageClasses)
                    <div class="hidden md:flex items-center gap-2">
                        <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                            class="bg-orange-600 text-white px-6 py-4 rounded-2xl hover:bg-orange-700 transition-all font-black text-[10px] uppercase tracking-widest flex items-center gap-2 shadow-lg shadow-orange-600/20 group">
                            <i class="bi bi-plus-lg text-base group-hover:rotate-90 transition-transform duration-300"></i>
                            Nova Turma
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
                <i class="bi bi-check-circle-fill mr-3 text-2xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Grouped Listing -->
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-8">
            @forelse($groupedClasses as $courseIdKey => $classesInGroup)
                @php $course = $courses->find($courseIdKey); @endphp
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-8 py-5 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center font-black">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">{{ $course->name ?? 'Sem Curso' }}</h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $classesInGroup->count() }} Turma(s)</p>
                            </div>
                        </div>
                        <a href="{{ route('courses.show', $courseIdKey) }}" class="text-[9px] font-black uppercase tracking-widest text-orange-600 hover:text-orange-700 flex items-center gap-1">
                            Ver Curso <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left table-compact">
                            <thead>
                                <tr class="bg-white/50 dark:bg-gray-800/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                                    @if($canManageClasses)
                                        <th class="px-8 py-4 w-10"></th>
                                    @endif
                                    <th class="px-8 py-4">Turma</th>
                                    <th class="px-8 py-4">Responsáveis</th>
                                    <th class="px-8 py-4 text-center">Inscritos</th>
                                    <th class="px-8 py-4 text-center">Status</th>
                                    <th class="px-8 py-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @foreach($classesInGroup as $class)
                                    <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-700/30 transition-colors group">
                                        @if($canManageClasses)
                                            <td class="px-8 py-5">
                                                <input type="checkbox" value="{{ $class->id }}" x-model="selected"
                                                    class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 bg-white dark:bg-gray-800">
                                            </td>
                                        @endif
                                        <td class="px-8 py-5">
                                            <div class="font-black text-gray-900 dark:text-white text-sm">{{ $class->name }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase">
                                                {{ $class->start_date ? $class->start_date->format('d/m/Y') : 'Previsão' }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-[10px] text-gray-600 dark:text-gray-400 font-bold uppercase">
                                            <div class="flex flex-col gap-1">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="bi bi-person text-orange-500"></i>
                                                    {{ $class->teacherMale->name ?? 'N/A' }}
                                                </span>
                                                @if($class->teacherFemale)
                                                    <span class="flex items-center gap-1.5">
                                                        <i class="bi bi-person text-pink-500"></i>
                                                        {{ $class->teacherFemale->name }}
                                                    </span>
                                                @endif
                                                @if($class->assistantMale || $class->assistantFemale)
                                                    <span class="flex items-center gap-1.5 opacity-50 mt-1">
                                                        <i class="bi bi-person-badge text-gray-400"></i>
                                                        {{ $class->assistantMale->name ?? '' }} {{ $class->assistantMale && $class->assistantFemale ? '&' : '' }} {{ $class->assistantFemale->name ?? '' }} (Aux)
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 dark:bg-gray-900 rounded-full border border-gray-100 dark:border-gray-700">
                                                <span class="text-xs font-black text-gray-900 dark:text-white">{{ $class->course_enrollments_count + $class->couple_enrollments_count }}</span>
                                                <i class="bi bi-people text-[10px] text-gray-400"></i>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @php
                                                $statusStyles = [
                                                    'em_andamento' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800',
                                                    'concluida' => 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
                                                    'cancelada' => 'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
                                                    'default' => 'bg-gray-50 text-gray-500 border-gray-100 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800'
                                                ];
                                                $statusLabels = [
                                                    'em_andamento' => 'Em curso',
                                                    'concluida' => 'Concluída',
                                                    'cancelada' => 'Cancelada',
                                                    'default' => 'Processando'
                                                ];
                                                $style = $statusStyles[$class->status] ?? $statusStyles['default'];
                                                $label = $statusLabels[$class->status] ?? $statusLabels['default'];
                                            @endphp
                                            <span class="px-3 py-1 {{ $style }} text-[9px] font-black uppercase rounded-full border">
                                                {{ $label }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <div class="flex items-center justify-end gap-2 text-gray-400">
                                                <a href="{{ route('course-classes.show', $class) }}" class="p-2 hover:text-orange-600 transition-colors">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($canManageClasses)
                                                    <a href="{{ route('course-classes.edit', $class) }}" class="p-2 hover:text-orange-600 transition-colors">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('course-classes.destroy', $class) }}" method="POST" onsubmit="return confirm('Excluir turma?')">
                                                        @csrf @method('DELETE')
                                                        <button class="p-2 hover:text-red-600 transition-colors">
                                                            <i class="bi bi-trash3"></i>
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
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-dashed border-gray-200 dark:border-gray-700">
                    <i class="bi bi-mortarboard text-6xl text-gray-200"></i>
                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest mt-4">Nenhuma turma encontrada</p>
                </div>
            @endforelse
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groupedClasses as $courseIdKey => $classesInGrid)
                @foreach($classesInGrid as $class)
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
                         <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50/50 dark:bg-orange-900/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-150 duration-700"></div>

                         <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-orange-600 text-white flex items-center justify-center text-2xl font-black shadow-lg shadow-orange-600/20 group-hover:rotate-6 transition-transform">
                                    {{ strtoupper(substr($class->name, 0, 1)) }}
                                </div>
                                @if($canManageClasses)
                                    <input type="checkbox" value="{{ $class->id }}" x-model="selected"
                                        class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 w-5 h-5 bg-white dark:bg-gray-800">
                                @endif
                            </div>

                            <h4 class="text-xl font-black text-gray-900 dark:text-white mb-2 leading-tight">{{ $class->name }}</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 block truncate">
                                <i class="bi bi-journal-text mr-1"></i> {{ $class->course->name ?? 'Sem Curso' }}
                            </p>

                            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-50 dark:border-gray-700">
                                <div>
                                    <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1">Inscritos</p>
                                    <p class="text-sm font-black text-gray-900 dark:text-white">{{ $class->course_enrollments_count + $class->couple_enrollments_count }}</p>
                                </div>
                                <div>
                                    <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1">Status</p>
                                    <span class="text-[9px] font-black uppercase text-orange-600 dark:text-orange-400">
                                        {{ $statusLabels[$class->status] ?? $class->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-8 flex gap-2">
                                <a href="{{ route('course-classes.show', $class) }}" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white text-center py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all">Detalhes</a>
                                @if($canManageClasses)
                                    <a href="{{ route('course-classes.edit', $class) }}" class="w-14 bg-gray-50 dark:bg-gray-900 text-gray-400 dark:text-gray-500 flex items-center justify-center rounded-2xl hover:text-orange-600 transition-colors border border-gray-100 dark:border-gray-700">
                                        <i class="bi bi-pencil-square text-lg"></i>
                                    </a>
                                @endif
                            </div>
                         </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
    </div>
@endsection
