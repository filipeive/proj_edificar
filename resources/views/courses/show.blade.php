@extends('layouts.app')

@section('title', 'Detalhes do Curso')
@section('page-title', 'Detalhes do Curso')
@section('page-subtitle', 'Informações e lista de alunos matriculados')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
            <a href="{{ route('courses.edit', $course) }}"
                class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
                title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endif

        @php
            $isEnrolled = $course->enrollments->where('user_id', auth()->id())->first();
        @endphp

        @if(!$isEnrolled)
            <form action="{{ route('courses.enroll', $course) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50"
                    title="Matricular-me">
                    <i class="bi bi-person-plus"></i>
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
    @php
        $statusStyles = [
            'completed' => 'bg-green-100 text-green-700 border-green-200',
            'approved' => 'bg-green-100 text-green-700 border-green-200',
            'aprovado' => 'bg-green-100 text-green-700 border-green-200',
            'dropped' => 'bg-red-100 text-red-700 border-red-200',
            'desistente' => 'bg-red-100 text-red-700 border-red-200',
            'enrolled' => 'bg-blue-100 text-blue-700 border-blue-200',
            'cursando' => 'bg-blue-100 text-blue-700 border-blue-200',
            'em_andamento' => 'bg-blue-100 text-blue-700 border-blue-200',
            'default' => 'bg-gray-100 text-gray-700 border-gray-200'
        ];
        $statusLabels = [
            'completed' => 'Concluído',
            'approved' => 'Aprovado',
            'aprovado' => 'Aprovado',
            'dropped' => 'Desistiu',
            'desistente' => 'Desistente',
            'enrolled' => 'Inscrito',
            'cursando' => 'Cursando',
            'em_andamento' => 'Cursando',
            'default' => 'Processando'
        ];
    @endphp
    <div x-data="{ 
        activeTab: 'students',
        view: localStorage.getItem('course_details_view') || 'list'
    }" 
    x-init="$watch('view', val => localStorage.setItem('course_details_view', val))"
    class="w-full space-y-8">
        <!-- Breadcrumbs & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-orange-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                            <i class="bi bi-journal-bookmark-fill mr-2"></i>
                            Cursos
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="bi bi-chevron-right text-gray-400 text-xs mx-2"></i>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $course->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex items-center gap-3">
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                    <a href="{{ route('courses.edit', $course) }}"
                        class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl font-bold text-xs uppercase tracking-widest hover:border-orange-500 hover:text-orange-600 transition-all flex items-center gap-2 shadow-sm">
                        <i class="bi bi-pencil-square"></i> Editar Curso
                    </a>
                @endif

                @php
                    $isEnrolled = $course->enrollments->where('user_id', auth()->id())->first();
                @endphp

                @if(!$isEnrolled)
                    <form action="{{ route('courses.enroll', $course) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-6 py-2 bg-orange-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20 flex items-center gap-2">
                            <i class="bi bi-person-plus-fill"></i> Matricular-me
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Main Course Hero / Info -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Course Summary Card -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-50/50 dark:bg-orange-900/10 rounded-full -mr-32 -mt-32 transition-transform group-hover:scale-110 duration-700"></div>

                <div class="relative z-10 space-y-6">
                    <div>
                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase rounded-full tracking-widest mb-4 inline-block">
                            {{ $course->category ?? 'Geral' }}
                        </span>
                        <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-none mb-4">{{ $course->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                            {{ $course->description ?? 'Sem descrição disponível.' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-6 pt-4">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-orange-500">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Duração</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $course->duration ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-blue-500">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Alvo</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $course->target_role ?? 'Geral' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card 1: Total Alunos -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-[2.5rem] p-8 text-white shadow-xl shadow-blue-200 dark:shadow-none relative overflow-hidden group">
                <i class="bi bi-person-video3 absolute -right-4 -bottom-4 text-8xl text-white/10 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-100/70">Total Matriculados</p>
                        <p class="text-5xl font-black mt-2">{{ $stats['total'] }}</p>
                    </div>
                    <div class="pt-4 border-t border-white/10 mt-auto">
                        <div class="flex items-center gap-2 text-[10px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-blue-300 animate-pulse"></span>
                            Alunos no Sistema
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card 2: Formados & Ativos -->
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Formados</p>
                            <p class="text-2xl font-black text-green-600 dark:text-green-400">{{ $stats['completed'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center text-green-600">
                            <i class="bi bi-mortarboard-fill text-xl"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Em Curso</p>
                            <p class="text-2xl font-black text-orange-500">{{ $stats['enrolled'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center text-orange-500">
                            <i class="bi bi-person-walking text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-50 dark:border-gray-700 mt-4">
                    <div class="flex justify-between items-center">
                         <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Taxa de Sucesso</p>
                         <span class="text-xs font-bold text-gray-900 dark:text-white">
                            {{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}%
                         </span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mt-2 overflow-hidden">
                        <div class="h-full bg-green-500" style="width: {{ $stats['total'] > 0 ? ($stats['completed'] / $stats['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex items-center gap-8 border-b border-gray-100 dark:border-gray-700">
            <button @click="activeTab = 'students'" 
                :class="activeTab === 'students' ? 'text-orange-600 border-orange-600' : 'text-gray-400 border-transparent hover:text-gray-600 dark:hover:text-gray-200'"
                class="pb-4 border-b-2 font-black text-xs uppercase tracking-[0.2em] transition-all">
                Alunos Matriculados
            </button>
            @if(isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
                <button @click="activeTab = 'public'" 
                    :class="activeTab === 'public' ? 'text-purple-600 border-purple-600' : 'text-gray-400 border-transparent hover:text-gray-600 dark:hover:text-gray-200'"
                    class="pb-4 border-b-2 font-black text-xs uppercase tracking-[0.2em] transition-all flex items-center gap-2">
                    Inscrições Públicas
                    <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 px-2 py-0.5 rounded-full text-[8px]">
                        {{ $publicCoupleEnrollments->count() }}
                    </span>
                </button>
            @endif
        </div>

        <div class="w-full">
            <!-- TAB: STUDENTS -->
            <div x-show="activeTab === 'students'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">

                @if(!auth()->user()->isSupervisor() || auth()->user()->isEnrolledInCourse($course->id))
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <!-- List Header -->
                        <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center bg-gray-50/10 dark:bg-gray-700/10 gap-4">
                            <div class="flex items-center gap-4">
                                <h4 class="text-xl font-black text-gray-900 dark:text-white">Alunos</h4>
                                <div class="flex bg-gray-50 dark:bg-gray-900 p-1 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <button @click="view = 'list'" 
                                        :class="view === 'list' ? 'bg-white dark:bg-gray-800 text-orange-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                        class="p-2 rounded-lg transition-all">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                    <button @click="view = 'grid'" 
                                        :class="view === 'grid' ? 'bg-white dark:bg-gray-800 text-orange-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                        class="p-2 rounded-lg transition-all">
                                        <i class="bi bi-grid-fill"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                    <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                                        class="bg-red-50 text-red-600 border border-red-100 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all hidden">
                                        Remover Selecionados
                                    </button>
                                @endif
                                
                                <form action="{{ route('courses.show', $course) }}" method="GET" class="flex flex-wrap items-center gap-2">
                                    <!-- Class Filter -->
                                    <select name="course_class_id" onchange="this.form.submit()"
                                        class="pl-4 pr-8 py-2 rounded-xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-2 focus:ring-orange-500 transition-all text-[10px] font-black uppercase tracking-widest text-gray-500">
                                        <option value="">Todas as Turmas</option>
                                        @foreach($courseClasses as $class)
                                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Status Filter -->
                                    <select name="status" onchange="this.form.submit()"
                                        class="pl-4 pr-8 py-2 rounded-xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-2 focus:ring-orange-500 transition-all text-[10px] font-black uppercase tracking-widest text-gray-500">
                                        <option value="">Todos os Status</option>
                                        @foreach(['enrolled' => 'Em Curso', 'completed' => 'Concluído', 'dropped' => 'Desistiu'] as $s => $l)
                                            <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>

                                    <div class="relative">
                                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                                            placeholder="Pesquisar..." 
                                            class="w-48 pl-10 pr-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-2 focus:ring-orange-500 transition-all text-xs font-bold">
                                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>

                                    @if($search || $classId || $status)
                                        <a href="{{ route('courses.show', $course) }}" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Limpar filtros">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <form id="bulkActionForm" action="{{ route('course-enrollments.bulk-destroy') }}" method="POST">@csrf</form>

                        <!-- LIST VIEW -->
                        <div x-show="view === 'list'" class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-gray-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                            <th class="px-8 py-5 w-10 text-center"><input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 text-orange-600"></th>
                                        @endif
                                        <th class="px-8 py-5">Aluno / Casal</th>
                                        <th class="px-8 py-5">Tipo</th>
                                        <th class="px-8 py-5">Turma</th>
                                        <th class="px-8 py-5">Status</th>
                                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                            <th class="px-8 py-5 text-right">Ações</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 font-medium">
                                    @forelse($allStudents as $student)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                                <td class="px-8 py-6 text-center">
                                                    @if($student instanceof \App\Models\CourseEnrollment)
                                                        <input type="checkbox" name="enrollment_ids[]" value="{{ $student->id }}" form="bulkActionForm" class="enrollment-checkbox rounded border-gray-300 text-orange-600">
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-full {{ $student instanceof \App\Models\CoupleEnrollment ? 'bg-purple-100 text-purple-600' : 'bg-orange-100 text-orange-600' }} flex items-center justify-center font-black">
                                                        @if($student instanceof \App\Models\CoupleEnrollment)
                                                            <i class="bi bi-heart-fill"></i>
                                                        @else
                                                            {{ $student->user_id ? substr($student->user->name, 0, 1) : substr($student->malePartner->name ?? 'N', 0, 1) }}
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-black text-gray-900 dark:text-white leading-none">
                                                            @if($student instanceof \App\Models\CoupleEnrollment)
                                                                {{ $student->husband_name }} & {{ $student->wife_name }}
                                                            @else
                                                                {{ $student->user_id ? $student->user->name : ($student->malePartner->name ?? 'N/A') . ' & ' . ($student->femalePartner->name ?? 'N/A') }}
                                                            @endif
                                                        </p>
                                                        <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tighter">
                                                            @if($student instanceof \App\Models\CoupleEnrollment)
                                                                {{ $student->contacts }}
                                                            @else
                                                                {{ $student->user_id ? $student->user->email : 'Matrícula Casal' }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                                    {{ $student instanceof \App\Models\CoupleEnrollment ? 'Inscrição Pública' : 'Matrícula Interna' }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span class="text-xs font-black text-gray-700 dark:text-gray-300">
                                                    {{ $student->courseClass->name ?? 'Sem Turma' }}
                                                </span>
                                            </td>
                                             <td class="px-8 py-6">
                                                @php
                                                    $currentStatus = $student->status;
                                                    $style = $statusStyles[$currentStatus] ?? $statusStyles['default'];
                                                    $label = $statusLabels[$currentStatus] ?? $statusLabels['default'];
                                                @endphp
                                                <span class="px-3 py-1 {{ $style }} text-[9px] font-black uppercase rounded-full border">
                                                    {{ $label }}
                                                </span>
                                            </td>
                                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                                <td class="px-8 py-6 text-right">
                                                    <div class="flex items-center justify-end gap-2 text-gray-400">
                                                        @if($student instanceof \App\Models\CourseEnrollment)
                                                            <a href="{{ route('course-enrollments.show', $student) }}" class="p-2 hover:text-orange-600 transition-colors">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        @endif
                                                        <div x-data="{ open: false }" class="relative">
                                                            <button @click="open = !open" class="p-2 hover:text-orange-600 transition-colors">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                                <div x-show="open" @click.away="open = false" 
                                                                    class="absolute right-0 bottom-full mb-2 w-48 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 z-50">
                                                                    <p class="px-4 py-2 text-[8px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-gray-700 mb-1">Mudar Status</p>
                                                                    @php
                                                                        $availableStatuses = ['cursando' => 'Cursando', 'aprovado' => 'Aprovado', 'reprovado' => 'Reprovado', 'desistente' => 'Desistiu'];
                                                                    @endphp
                                                                    @foreach($availableStatuses as $s => $l)
                                                                        <form action="{{ $student instanceof \App\Models\CoupleEnrollment ? route('couple-enrollments.status', $student) : route('enrollments.status', $student) }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="status" value="{{ $s }}">
                                                                            <button type="submit"
                                                                                class="w-full text-left px-4 py-2 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 {{ ($student->status == $s || ($s == 'cursando' && $student->status == 'enrolled') || ($s == 'aprovado' && $student->status == 'completed')) ? 'text-orange-600 bg-orange-50/50' : 'text-gray-500' }}">
                                                                                {{ $l }}
                                                                            </button>
                                                                        </form>
                                                                    @endforeach
                                                                </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-8 py-20 text-center">
                                                <div class="flex flex-col items-center gap-4 text-gray-300 dark:text-gray-600">
                                                    <i class="bi bi-people-fill text-6xl"></i>
                                                    <p class="text-sm font-black uppercase tracking-widest">Nenhum aluno encontrado com esses filtros</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- GRID VIEW -->
                        <div x-show="view === 'grid'" class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                             @foreach($allStudents as $student)
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-3xl p-6 border border-gray-100 dark:border-gray-700/50 hover:border-orange-200 transition-all group">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-orange-600 font-black text-xl">
                                            @if($student instanceof \App\Models\CoupleEnrollment)
                                                <i class="bi bi-heart-fill text-purple-600"></i>
                                            @else
                                                {{ substr($student->user->name ?? $student->malePartner->name ?? 'N', 0, 1) }}
                                            @endif
                                        </div>
                                        <span class="px-2 py-1 {{ $statusStyles[$student->status] ?? $statusStyles['default'] }} text-[8px] font-black uppercase rounded-lg border">
                                            {{ $statusLabels[$student->status] ?? $statusLabels['default'] }}
                                        </span>
                                    </div>
                                    <h5 class="text-sm font-black text-gray-900 dark:text-white mb-1 leading-tight line-clamp-1">
                                        @if($student instanceof \App\Models\CoupleEnrollment)
                                            {{ $student->husband_name }} & {{ $student->wife_name }}
                                        @else
                                            {{ $student->user_id ? $student->user->name : ($student->malePartner->name ?? 'N/A') . ' & ' . ($student->femalePartner->name ?? 'N/A') }}
                                        @endif
                                    </h5>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter mb-6 truncate">
                                        {{ $student instanceof \App\Models\CoupleEnrollment ? $student->contacts : ($student->user->email ?? 'Matrícula Casal') }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                                        <div>
                                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Turma</p>
                                            <p class="text-xs font-black text-gray-800 dark:text-gray-200 mt-1">{{ $student->courseClass->name ?? 'Sem Turma' }}</p>
                                        </div>
                                        @if($student instanceof \App\Models\CourseEnrollment)
                                            <a href="{{ route('course-enrollments.show', $student) }}" class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 hover:text-orange-600 shadow-sm transition-colors">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                             @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- TAB: PUBLIC INBOX -->
            <div x-show="activeTab === 'public'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                @if(isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($publicCoupleEnrollments as $enrollment)
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all flex flex-col lg:flex-row items-center justify-between gap-8 group">
                                <div class="flex items-start gap-6 w-full md:w-auto">
                                    <div class="w-16 h-16 rounded-[1.5rem] bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            <h4 class="text-xl font-black text-gray-900 dark:text-white leading-none">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</h4>
                                            <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[8px] font-black uppercase rounded-full">Novo</span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 mb-4">{{ $enrollment->contacts }} · {{ $enrollment->address }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500 text-[9px] font-black uppercase rounded-full border border-gray-100 dark:border-gray-600">
                                                <i class="bi bi-link-45deg mr-1"></i> {{ $enrollment->relationship_type }}
                                            </span>
                                            <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500 text-[9px] font-black uppercase rounded-full border border-gray-100 dark:border-gray-600">
                                                <i class="bi bi-geo-alt mr-1"></i> {{ $enrollment->is_church_member ? 'Membro' : 'Visitante' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                    <form action="{{ route('courses.assign-public-enrollment', $course) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                                        @csrf
                                        <input type="hidden" name="couple_enrollment_id" value="{{ $enrollment->id }}">
                                        <select name="course_class_id" required
                                            class="w-full sm:w-56 px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-4 focus:ring-purple-100 dark:focus:ring-purple-900/20 transition-all text-sm font-bold text-gray-900 dark:text-white">
                                            <option value="">Selecionar turma...</option>
                                            @foreach($courseClasses as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                            class="w-full sm:w-auto px-8 py-4 bg-purple-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-purple-700 transition-all shadow-xl shadow-purple-600/20">
                                            Atribuir à Turma
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-dashed border-gray-200 dark:border-gray-700">
                        <i class="bi bi-inbox text-6xl text-gray-200"></i>
                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest mt-4">Nenhuma inscrição pública pendente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('.enrollment-checkbox');
            const bulkBtn = document.getElementById('bulkDeleteBtn');

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkBtn();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBtn);
            });

            function updateBulkBtn() {
                const count = document.querySelectorAll('.enrollment-checkbox:checked').length;
                if (count > 0) {
                    bulkBtn.disabled = false;
                    bulkBtn.classList.remove('hidden');
                    bulkBtn.innerHTML = `<i class="bi bi-trash3-fill mr-2"></i> Remover ${count}`;
                } else {
                    bulkBtn.disabled = true;
                    bulkBtn.classList.add('hidden');
                }
            }
        });

        function bulkDelete() {
            if (typeof confirmAction === 'function') {
                confirmAction(
                    'Remover em Massa',
                    'Deseja remover as matrículas selecionadas?',
                    'warning',
                    'Sim, remover',
                    '#ef4444'
                ).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('bulkActionForm').submit();
                    }
                });
            } else if (confirm('Remover matrículas selecionadas?')) {
                document.getElementById('bulkActionForm').submit();
            }
        }
    </script>
@endif
