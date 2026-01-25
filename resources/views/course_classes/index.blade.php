@extends('layouts.app')

@section('title', 'Gestão de Turmas - Portal Life Church')
@section('page-title', 'Turmas dos Cursos')
@section('page-subtitle', 'Organização e acompanhamento de alunos e casais')

@section('content')
    <div x-data="{ 
                     view: window.innerWidth < 768 ? 'grid' : 'list',
                     selected: [],
                     updateView() {
                         if (window.innerWidth < 768 && this.view === 'list') {
                             this.view = 'grid'; 
                         }
                     },
                     toggleAll() {
                        const allIds = {{ Js::from($classes->pluck('id')) }};
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

        @section('header-actions')
            <div class="flex items-center gap-2">
                <a href="{{ route('course-classes.export-all') }}"
                    class="bg-indigo-50 text-indigo-600 border border-indigo-100 p-2 rounded-lg hover:bg-indigo-100 transition-all flex items-center justify-center shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet text-2xl"></i>
                </a>
                <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                    class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
                    <i class="bi bi-plus-circle text-2xl"></i>
                </a>
            </div>
        @endsection
    <div class="container-fluid">
        <!-- Modern Header Section -->
        <div class="bg-white dark:bg-gray-800 p-5 md:p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col xl:flex-row justify-between items-center gap-6 mb-6 transition-colors">
            <div class="flex flex-col md:flex-row items-center gap-5">
                <div>
                    <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-none">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600 dark:from-indigo-400 dark:to-blue-400">Turmas</span>
                        <span class="text-gray-300 dark:text-gray-600">& Cursos</span>
                    </h2>
                    <p class="text-[10px] md:text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Gestão de turmas e inscritos</p>
                </div>
                
                <div class="h-8 w-[1px] bg-gray-100 dark:bg-gray-700 hidden md:block"></div>

                <form action="{{ route('course-classes.index') }}" method="GET" class="flex items-center">
                    <div class="relative group w-full md:w-auto">
                        <i class="bi bi-funnel absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                        <select name="course_id" onchange="this.form.submit()"
                            class="pl-11 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-blue-500 font-black text-[10px] uppercase tracking-widest appearance-none cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-all text-gray-600 dark:text-gray-300 min-w-[200px] md:min-w-[240px]">
                            <option value="">Todos os Cursos</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>
                </form>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3">
                <!-- View Toggle -->
                <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl border border-gray-50 dark:border-gray-600">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                        class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
                        <i class="bi bi-list-ul"></i>
                        <span class="hidden sm:inline">Lista</span>
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                        class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
                        <i class="bi bi-grid-fill"></i>
                        <span class="hidden sm:inline">Grid</span>
                    </button>
                </div>

                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('course-classes.export-all') }}"
                        class="bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 border border-indigo-50 dark:border-indigo-900/30 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 dark:hover:text-white px-5 py-3 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest flex items-center gap-2 shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet text-base"></i>
                        <span class="hidden lg:inline text-[8px]">Excel</span>
                    </a>
                    
                    <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                        class="bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-[10px] uppercase tracking-widest flex items-center shadow-lg shadow-blue-100 dark:shadow-none gap-2 group">
                        <i class="bi bi-plus-lg text-base group-hover:rotate-90 transition-transform duration-300"></i>
                        <span class="hidden sm:inline">Nova Turma</span>
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
                <i class="bi bi-check-circle-fill mr-3 text-2xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" @click="toggleAll()"
                                        :checked="selected.length === {{ $classes->count() }} && selected.length > 0"
                                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer bg-white dark:bg-gray-700">
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Turma</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Curso</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Professores
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                    Inscritos</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                                    Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($classes as $class)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 opacity-0 transition-opacity" :class="{'opacity-100': selected.includes({{ $class->id }})}"></div>
                                        <input type="checkbox" value="{{ $class->id }}" x-model="selected"
                                            class="class-checkbox rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer bg-white dark:bg-gray-700">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $class->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $class->start_date ? $class->start_date->format('d/m/Y') : 'N/A' }} -
                                            {{ $class->end_date ? $class->end_date->format('d/m/Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $class->course->name ?? 'Curso não definido' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $class->teacherMale->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $class->teacherFemale->name ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full text-xs font-bold">
                                            {{ $class->enrollments_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'em_andamento' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                                'concluida' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                                                'cancelada' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                                                'active' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300', // Backward compatibility
                                                'completed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                                                'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                                            ];
                                            $statusLabels = [
                                                'em_andamento' => 'Em Andamento',
                                                'concluida' => 'Concluída',
                                                'cancelada' => 'Cancelada',
                                                'active' => 'Ativa',
                                                'completed' => 'Concluída',
                                                'cancelled' => 'Cancelada',
                                            ];
                                        @endphp
                                        <span
                                            class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClasses[$class->status] ?? 'bg-gray-100 dark:bg-gray-700 dark:text-gray-300' }}">
                                            {{ $statusLabels[$class->status] ?? $class->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('course-classes.show', $class) }}"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-200 border border-transparent hover:border-blue-200 p-1 rounded transition-all"
                                            title="Ver Detalhes">
                                            <i class="bi bi-eye-fill text-lg"></i>
                                        </a>
                                        <a href="{{ route('course-classes.edit', $class) }}"
                                            class="text-amber-600 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-200 border border-transparent hover:border-amber-200 p-1 rounded transition-all"
                                            title="Editar">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </a>
                                        <form action="{{ route('course-classes.destroy', $class) }}" method="POST"
                                            id="delete-form-{{ $class->id }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-form-{{ $class->id }}', 'Deseja excluir permanentemente esta turma?')"
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200 border border-transparent hover:border-red-200 p-1 rounded transition-all">
                                                <i class="bi bi-trash-fill text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        <i class="bi bi-inbox text-4xl block mb-2"></i>
                                        Nenhuma turma encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @if($classes->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                    {{ $classes->links() }}
                </div>
            @endif
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($classes as $class)
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col group hover:shadow-2xl hover:shadow-blue-900/10 dark:hover:shadow-blue-900/20 transition-all duration-300 relative overflow-hidden"
                     :class="{'ring-2 ring-indigo-500 bg-indigo-50/10 dark:bg-indigo-900/10': selected.includes({{ $class->id }})}">
                    <!-- Status Badge -->
                    <div class="absolute top-6 right-6 z-10">
                        @php
                            $statusClasses = [
                                'em_andamento' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                'concluida' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                'cancelada' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border-red-200 dark:border-red-800',
                                'active' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                'completed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border-red-200 dark:border-red-800',
                            ];
                            $statusLabels = [
                                'em_andamento' => 'Andamento',
                                'concluida' => 'Concluída',
                                'cancelada' => 'Cancelada',
                                'active' => 'Ativa',
                                'completed' => 'Concluída',
                                'cancelled' => 'Cancelada',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClasses[$class->status] ?? 'bg-gray-100 dark:bg-gray-700 border-gray-200 dark:border-gray-600' }}">
                            {{ $statusLabels[$class->status] ?? $class->status }}
                        </span>
                    </div>

                     <!-- Checkbox for Bulk Actions (Grid) -->
                     <div class="absolute top-6 left-6 z-10">
                        <input type="checkbox" value="{{ $class->id }}" x-model="selected"
                            class="class-checkbox rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer w-6 h-6 shadow-sm bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm">
                    </div>

                    <!-- Card Header / Icon -->
                    <div class="p-8 pb-4">
                        <div class="w-16 h-16 rounded-3xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center font-black text-3xl shadow-lg shadow-blue-500/20 mb-6 group-hover:scale-110 transition-transform duration-500">
                            {{ strtoupper(substr($class->name, 0, 1)) }}
                        </div>

                        <div class="mb-2 min-h-[4rem]">
                             <h4 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">{{ $class->name }}</h4>
                             <span class="inline-block px-2 py-1 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-gray-100 dark:border-gray-600">
                                {{ $class->course->name ?? 'Curso não definido' }}
                             </span>
                        </div>
                    </div>

                    <!-- Metrics/Details -->
                    <div class="px-8 pb-8 flex-1">
                        <div class="space-y-4">
                             <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors">
                                <span class="text-[10px] font-black uppercase text-gray-400 dark:text-gray-500">Alunos</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-gray-800 dark:text-gray-200">{{ $class->enrollments_count }}</span>
                                    <i class="bi bi-people-fill text-blue-500 dark:text-blue-400 text-xs"></i>
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <span class="text-[10px] font-black uppercase text-gray-300 dark:text-gray-600 block mb-1">Responsáveis</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-[10px]">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300 truncate">{{ $class->teacherMale->name ?? 'N/A' }}</span>
                                </div>
                                @if($class->teacherFemale)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center text-pink-600 dark:text-pink-400 text-[10px]">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300 truncate">{{ $class->teacherFemale->name }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions Footer -->
                    <div class="px-8 pb-8 pt-0 mt-auto">
                        <div class="flex gap-3">
                            <a href="{{ route('course-classes.show', $class) }}" class="flex-1 bg-gray-900 dark:bg-white/10 text-white text-center py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 dark:hover:bg-blue-600 transition-all shadow-lg hover:shadow-blue-600/30 flex items-center justify-center gap-2">
                                <i class="bi bi-eye-fill"></i> Detalhes
                            </a>
                            <a href="{{ route('course-classes.edit', $class) }}" class="w-12 bg-white dark:bg-gray-700 border-2 border-gray-100 dark:border-gray-600 text-gray-400 dark:text-gray-300 flex items-center justify-center rounded-2xl hover:border-blue-500 hover:text-blue-500 dark:hover:border-blue-400 dark:hover:text-blue-400 transition-all">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 bg-gray-50 dark:bg-gray-800 rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center gap-6 text-center transition-colors">
                    <div class="w-24 h-24 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center shadow-sm">
                        <i class="bi bi-inbox text-4xl text-gray-300 dark:text-gray-500"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Nenhuma turma encontrada</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm max-w-xs mx-auto">Não existem turmas cadastradas para o filtro selecionado.</p>
                    </div>
                </div>
            @endforelse
        </div>
        @if($classes->hasPages())
            <div class="mt-8" x-show="view === 'grid'">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
    </div>
@endsection