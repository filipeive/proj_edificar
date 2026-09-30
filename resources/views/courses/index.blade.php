@extends('layouts.app')

@section('title', 'Cursos e Formação - Portal Life Church')
@section('page-title', 'Academia & Cursos')
@section('page-subtitle', 'Gerencie a formação ministerial e cursos da igreja')

@section('header-actions')
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor' || auth()->user()->role === 'pastor_senior')
        <div class="md:hidden">
            <a href="{{ route('courses.create') }}"
                class="text-gray-600 hover:text-orange-600 p-2.5 hover:bg-orange-50 rounded-xl transition-all duration-300 flex items-center justify-center">
                <i class="bi bi-journal-plus text-2xl"></i>
            </a>
        </div>
    @endif
@endsection

@section('content')
    <!-- Print Header -->
    <div class="hidden print:block mb-8 text-center border-b pb-4">
        <h1 class="text-2xl font-bold uppercase tracking-wide">Comunidade de Vida Cristã - Life Church</h1>
        <p class="text-sm font-semibold text-gray-600 uppercase">Relatório Geral de Cursos e Formação Ministerial</p>
        <p class="text-xs text-gray-500 mt-1">Gerado em {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="w-full space-y-8">
        <!-- Desktop Header Action Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-orange-500">Módulo de Ensino</p>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Academia & Formação</h2>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">Gerencie os módulos de capacitação e acompanhe inscrições.</p>
            </div>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor' || auth()->user()->role === 'pastor_senior')
                <div class="hidden md:flex flex-wrap items-center gap-2">
                    <a href="{{ route('courses.create') }}"
                        class="bg-orange-600 text-white px-4 py-2.5 rounded-xl hover:bg-orange-700 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-md shadow-orange-600/20">
                        <i class="bi bi-plus-lg text-sm"></i>
                        <span>Novo Curso</span>
                    </a>

                    <a href="{{ route('courses.export-global') }}"
                        class="bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 px-4 py-2.5 rounded-xl hover:bg-emerald-100 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-sm"></i>
                        <span>Excel</span>
                    </a>

                    <a href="{{ route('courses.export-pdf') }}"
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
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total de Cursos</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['total'] ?? $allCourses->count() }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Cadastrados na academia</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Inscrições Abertas</p>
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['open'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Cursos disponíveis</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-door-open-fill"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-500">Total de Alunos</p>
                    <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $stats['students'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Membros & casais matriculados</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Turmas Ativas</p>
                    <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['classes'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Turmas em andamento</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>
        </div>

        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-orange-600 via-amber-600 to-orange-500 dark:from-orange-700 dark:to-amber-800 rounded-[2rem] p-6 text-white shadow-lg shadow-orange-500/10 print:hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-1">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest text-white">
                        Formação Ministerial
                    </span>
                    <h3 class="text-xl font-black tracking-tight">Academia & Capacitação de Líderes</h3>
                    <p class="text-xs font-medium text-orange-100">Explore os cursos disponíveis e acompanhe o crescimento espiritual da congregação.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('course-classes.index') }}"
                        class="px-4 py-2.5 bg-white text-orange-600 hover:bg-orange-50 rounded-xl transition-all text-xs font-black uppercase tracking-wider shadow-sm flex items-center gap-2">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span>Gerir Turmas</span>
                    </a>
                    <a href="{{ route('couple-enrollments.index') }}"
                        class="px-4 py-2.5 bg-black/20 hover:bg-black/30 backdrop-blur-md text-white border border-white/20 rounded-xl transition-all text-xs font-black uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-heart-fill"></i>
                        <span>Inscrições Casais</span>
                    </a>
                </div>
            </div>
        </div>

        @if($enrolledCourses->isNotEmpty())
            <!-- Meus Cursos Section -->
            <section class="space-y-4 print:hidden">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black">
                        <i class="bi bi-mortarboard-fill text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Meus Cursos</h2>
                        <p class="text-xs text-gray-400">Cursos onde você está atualmente matriculado(a)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($enrolledCourses as $course)
                        @include('courses.partials.course-card', ['course' => $course, 'type' => 'enrolled'])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Cursos Disponíveis Section -->
        <section class="space-y-4 print:hidden">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black">
                    <i class="bi bi-book-half text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Cursos Disponíveis</h2>
                    <p class="text-xs text-gray-400">Inscreva-se nos módulos abertos para seu perfil</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($availableCourses as $course)
                    @include('courses.partials.course-card', ['course' => $course, 'type' => 'available'])
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 rounded-[2.5rem] p-12 text-center border border-dashed border-gray-200 dark:border-gray-700">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                            <i class="bi bi-stars text-4xl"></i>
                        </div>
                        <h4 class="text-base font-black text-gray-900 dark:text-white mb-1 uppercase tracking-tight">Sem novos cursos disponíveis</h4>
                        <p class="text-xs text-gray-400 max-w-md mx-auto">Você já está matriculado nos módulos ativos ou não há novas inscrições abertas.</p>
                    </div>
                @endforelse
            </div>
        </section>

        @if((auth()->user()->role === 'admin' || auth()->user()->role === 'pastor' || auth()->user()->role === 'pastor_senior') && $allCourses->isNotEmpty())
            <!-- Admin: Todos os Cursos Monitoramento -->
            <section class="space-y-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-t border-gray-100 dark:border-gray-700 pt-8 print:hidden">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Painel Administrativo</p>
                        <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Monitorização Global de Cursos</h2>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" 
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
                    <div x-show="selected.length > 0" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                        class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none print:hidden">
                        <div class="bg-gray-900 text-white rounded-2xl shadow-2xl p-4 flex items-center gap-6 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                            <div class="flex items-center gap-3 pl-2">
                                <span class="bg-orange-600 text-xs font-black px-2.5 py-1 rounded-lg" x-text="selected.length"></span>
                                <span class="text-sm font-medium">selecionados</span>
                            </div>

                            <div class="h-8 w-px bg-gray-700"></div>

                            <div class="flex items-center gap-2">
                                <button @click="selected = []"
                                    class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors">
                                    Cancelar
                                </button>
                                @if(auth()->user()->role === 'admin')
                                    <form method="POST" action="{{ route('courses.bulk-delete') }}" @submit.prevent="
                                        Swal.fire({
                                            title: 'Confirmação de Exclusão',
                                            text: 'Tem certeza que deseja excluir ' + selected.length + ' curso(s)? Esta ação é irreversível.',
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
                                            <input type="hidden" name="course_ids[]" :value="id">
                                        </template>
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
                                            <i class="bi bi-trash-fill"></i> Excluir
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/20 print:hidden">
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Lista Geral de Cursos</h3>
                        <div class="hidden md:flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
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

                    <!-- List View -->
                    <div x-show="view === 'list'" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                    @if(auth()->user()->role === 'admin')
                                        <th class="px-6 py-4 w-10 print:hidden">
                                            <input type="checkbox" @click="toggleAll()"
                                                :checked="selected.length === {{ $allCourses->count() }} && selected.length > 0"
                                                class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 w-4 h-4">
                                        </th>
                                    @endif
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Curso</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Categoria</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Alunos</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status Inscrições</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right print:hidden">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @foreach($allCourses as $course)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                                        @if(auth()->user()->role === 'admin')
                                            <td class="px-6 py-4 print:hidden">
                                                <input type="checkbox" value="{{ $course->id }}" x-model="selected"
                                                    class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 w-4 h-4">
                                            </td>
                                        @endif
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black">
                                                    {{ strtoupper(substr($course->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h5 class="font-black text-gray-900 dark:text-white text-sm leading-snug">{{ $course->name }}</h5>
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $course->slug }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                                {{ $course->category ?? 'ACADEMIA' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-sm font-black text-gray-900 dark:text-white">{{ $course->enrollments_count + $course->couple_enrollments_count }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($course->registration_open)
                                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 rounded-full text-[9px] font-black uppercase tracking-wider border border-emerald-100 dark:border-emerald-800">Abertas</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 rounded-full text-[9px] font-black uppercase tracking-wider border border-rose-100 dark:border-rose-800">Fechadas</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right print:hidden">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('courses.show', $course) }}" title="Detalhes"
                                                    class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-purple-600 hover:border-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all flex items-center justify-center">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                <a href="{{ route('courses.edit', $course) }}" title="Editar"
                                                    class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all flex items-center justify-center">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                @if(auth()->user()->role === 'admin')
                                                    <form id="list-delete-course-{{ $course->id }}" action="{{ route('courses.destroy', $course) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmDelete('list-delete-course-{{ $course->id }}')"
                                                            class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all flex items-center justify-center" title="Excluir">
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

                    <!-- Grid View -->
                    <div x-show="view === 'grid'" class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 print:hidden">
                        @foreach($allCourses as $course)
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 flex flex-col justify-between group hover:shadow-md transition-all">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black text-xl">
                                        {{ strtoupper(substr($course->name, 0, 1)) }}
                                    </div>
                                    @if($course->registration_open)
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 rounded-full text-[9px] font-black uppercase tracking-wider">Abertas</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 rounded-full text-[9px] font-black uppercase tracking-wider">Fechadas</span>
                                    @endif
                                </div>

                                <div>
                                    <h5 class="font-black text-gray-900 dark:text-white text-base leading-tight mb-1">{{ $course->name }}</h5>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">{{ $course->category ?? 'ACADEMIA' }}</p>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between text-xs font-bold text-gray-500">
                                    <span>Alunos: {{ $course->enrollments_count + $course->couple_enrollments_count }}</span>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('courses.show', $course) }}" class="p-1.5 text-gray-400 hover:text-purple-600"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('courses.edit', $course) }}" class="p-1.5 text-gray-400 hover:text-blue-600"><i class="bi bi-pencil-fill"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>

    @if(auth()->user()->role === 'admin')
        <form id="singleDeleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endsection
