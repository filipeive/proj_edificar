@extends('layouts.app')

@section('title', 'Cursos e Formação')
@section('page-title', 'Academia & Cursos')
@section('page-subtitle', 'Gerencie a formação ministerial e cursos da igreja')

@section('header-actions')
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
        <div class="md:hidden">
            <a href="{{ route('courses.create') }}"
                class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-orange-600/20">
                <i class="bi bi-journal-plus text-2xl"></i>
            </a>
        </div>
    @endif
@endsection

@section('content')
    <div class="container-fluid space-y-12">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white dark:bg-gray-800 p-4 md:p-8 rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 mb-8 transition-colors">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Academia & Formação</h1>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Desenvolvimento ministerial e crescimento espiritual.</p>
            </div>
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                <a href="{{ route('courses.create') }}"
                    class="hidden md:flex bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-2xl flex items-center transition shadow-lg shadow-orange-600/20 font-black text-sm uppercase tracking-widest">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Curso
                </a>
            @endif
        </div>

        @if($enrolledCourses->isNotEmpty())
            <!-- Meus Cursos Section -->
            <section class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                        <i class="bi bi-mortarboard-fill text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Meus Cursos</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($enrolledCourses as $course)
                        @include('courses.partials.course-card', ['course' => $course, 'type' => 'enrolled'])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Cursos Disponíveis Section -->
        <section class="space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <i class="bi bi-book-half text-2xl"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Cursos Disponíveis</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($availableCourses as $course)
                    @include('courses.partials.course-card', ['course' => $course, 'type' => 'available'])
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 rounded-[2.5rem] p-16 text-center border border-dashed border-gray-200 dark:border-gray-700 transition-colors">
                        <div class="w-24 h-24 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="bi bi-stars text-5xl text-gray-200 dark:text-gray-600"></i>
                        </div>
                        <h4 class="text-xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight">Sem novos cursos no momento
                        </h4>
                        <p class="text-gray-500 dark:text-gray-400 font-medium max-w-md mx-auto">Já estás inscrito em todos os cursos disponíveis
                            para o teu nível ou não há novas inscrições abertas.</p>
                    </div>
                @endforelse
            </div>
        </section>

        @if((auth()->user()->role === 'admin' || auth()->user()->role === 'pastor') && $allCourses->isNotEmpty())
            <!-- Admin: Todos os Cursos Monitoramento -->
            <section class="mt-20 pt-10 border-t border-gray-100 dark:border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                    <h2 class="text-xl font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Monitoramento Global (Admin)</h2>
                    @if(auth()->user()->role === 'admin')
                        <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl flex items-center transition shadow-lg shadow-red-600/20 font-black text-xs uppercase tracking-widest hidden">
                            <i class="bi bi-trash-fill mr-2"></i> Excluir Selecionados
                        </button>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 transition-colors" x-data="{  
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
                        class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
                        <div
                            class="bg-gray-900 text-white rounded-2xl shadow-2xl p-4 flex items-center gap-6 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                            <div class="flex items-center gap-3 pl-2">
                                <span class="bg-blue-600 text-xs font-black px-2.5 py-1 rounded-lg"
                                    x-text="selected.length"></span>
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

                    <div class="p-8 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gray-50/10 dark:bg-gray-700/10">
                        <div class="flex items-center gap-4">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Lista de Cursos</h3>
                            <div class="hidden md:flex bg-gray-50 dark:bg-gray-700 p-1 rounded-xl border border-gray-100 dark:border-gray-600">
                                <button @click="view = 'list'"
                                    :class="view === 'list' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                    class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                                <button @click="view = 'grid'"
                                    :class="view === 'grid' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                    class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                                    <i class="bi bi-grid-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse table-compact">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                        @if(auth()->user()->role === 'admin')
                                            <th class="px-8 py-6 w-10">
                                                <input type="checkbox" @click="toggleAll()"
                                                    :checked="selected.length === {{ $allCourses->count() }} && selected.length > 0"
                                                    class="rounded-lg border-gray-300 dark:border-gray-600 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5 bg-white dark:bg-gray-700">
                                            </th>
                                        @endif
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                            Curso</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                            Categoria</th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center">
                                            Alunos</th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center">
                                            Inscrições</th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest text-right">
                                            Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    @foreach($allCourses as $course)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                                            @if(auth()->user()->role === 'admin')
                                                <td class="px-8 py-6 relative">
                                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-orange-500 opacity-0 transition-opacity"
                                                        :class="{'opacity-100': selected.includes({{ $course->id }})}"></div>
                                                    <input type="checkbox" value="{{ $course->id }}" x-model="selected"
                                                        class="course-checkbox rounded-lg border-gray-300 dark:border-gray-600 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5 bg-white dark:bg-gray-700">
                                                </td>
                                            @endif
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black group-hover:scale-110 transition-transform">
                                                        {{ strtoupper(substr($course->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h5 class="font-bold text-gray-900 dark:text-white leading-tight">{{ $course->name }}</h5>
                                                        <span
                                                            class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">{{ $course->slug }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full text-[10px] font-black uppercase tracking-widest">
                                                    {{ $course->category ?? 'ACADEMIA' }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <span
                                                    class="text-sm font-black text-gray-700 dark:text-gray-300">{{ $course->enrollments_count }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-center text-xs font-bold">
                                                @if($course->registration_open)
                                                    <span class="text-green-600">ABERTAS</span>
                                                @else
                                                    <span class="text-red-500">FECHADAS</span>
                                                @endif
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <div
                                                    class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <a href="{{ route('courses.show', $course) }}" title="Detalhes"
                                                        class="action-icon bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 hover:bg-orange-500 hover:text-white dark:hover:bg-orange-500 dark:hover:text-white shadow-sm">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                    <a href="{{ route('courses.edit', $course) }}" title="Editar"
                                                        class="action-icon bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white shadow-sm">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    @if(auth()->user()->role === 'admin')
                                                        <form id="list-delete-course-{{ $course->id }}"
                                                            action="{{ route('courses.destroy', $course) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                onclick="confirmDelete('list-delete-course-{{ $course->id }}')"
                                                                class="action-icon bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white shadow-sm"
                                                                title="Excluir">
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

                    <!-- Grid View for Admin -->
                    <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="p-6 grid grid-compact bg-gray-50/30 dark:bg-gray-900/10">
                        @foreach($allCourses as $course)
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col group hover:shadow-md hover:shadow-gray-300/50 dark:hover:shadow-gray-950/50 transition-all relative h-full compact-card"
                                :class="{'ring-2 ring-orange-500 bg-orange-50/10 dark:bg-orange-900/10': selected.includes({{ $course->id }})}">
                                <div class="absolute top-6 right-6 flex gap-2">
                                    @if($course->registration_open)
                                        <span
                                            class="px-3 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-green-100 dark:border-green-800">Abertas</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-red-100 dark:border-red-800">Fechadas</span>
                                    @endif
                                    @if(auth()->user()->role === 'admin')
                                        <input type="checkbox" value="{{ $course->id }}" x-model="selected"
                                            class="course-checkbox rounded-lg border-gray-300 dark:border-gray-600 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5 bg-white dark:bg-gray-700">
                                    @endif
                                </div>

                                <div class="flex items-center gap-4 mb-4">
                                    <div
                                        class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black text-2xl group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                                        {{ strtoupper(substr($course->name, 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <h5 class="font-black text-gray-900 dark:text-white leading-tight mb-1 line-clamp-1">{{ $course->name }}</h5>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">{{ $course->category ?? 'ACADEMIA' }}</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl mb-6 space-y-3 p-4">
                                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                                        <span class="text-gray-400 dark:text-gray-500">Total Alunos</span>
                                        <span class="text-gray-900 dark:text-white text-sm">{{ $course->enrollments_count }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                                        <span class="text-gray-400 dark:text-gray-500">Slug</span>
                                        <span class="text-gray-500 dark:text-gray-400 lowercase font-mono">{{ $course->slug }}</span>
                                    </div>
                                </div>

                                <div class="mt-auto grid grid-cols-3 gap-2">
                                    <a href="{{ route('courses.show', $course) }}"
                                        class="col-span-1 bg-gray-900 dark:bg-black text-white text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center justify-center">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('courses.edit', $course) }}"
                                        class="col-span-1 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-300 text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all flex items-center justify-center">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @if(auth()->user()->role === 'admin')
                                        <form id="grid-delete-course-{{ $course->id }}" action="{{ route('courses.destroy', $course) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('grid-delete-course-{{ $course->id }}')"
                                                class="w-full h-full bg-red-50 dark:bg-red-900/30 text-red-400 text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>

    @if(auth()->user()->role === 'admin')
        <!-- Shared Delete Form -->
        <form id="singleDeleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endsection
