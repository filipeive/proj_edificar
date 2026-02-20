@extends('layouts.app')

@section('title', 'Detalhes do Curso')
@section('page-title', 'Detalhes do Curso')
@section('page-subtitle', 'Informações e lista de alunos matriculados')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        @if(auth()->user()->isAdmin() || in_array(auth()->user()->role, ['pastor', 'pastor_senior'], true))
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
        $canManageCourse = auth()->user()->isAdmin() || in_array(auth()->user()->role, ['pastor', 'pastor_senior'], true);
        $isSupervisorView = auth()->user()->isSupervisor();
        $showCoupleSections = !$isSupervisorView;
        $manageTotalCount = $courseEnrollments->count() + ($showCoupleSections ? $coupleEnrollments->count() : 0);

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
        activeTab: 'classes',
        view: localStorage.getItem('course_details_view') || 'list',
        showMoveModal: false,
        movingClass: null,
        targetCourseId: '',
        manageSearch: '',
        manageClassFilter: '',
        manageStatusFilter: ''
    }" 
    x-init="console.log('Course Details Alpine initialized'); $watch('view', val => localStorage.setItem('course_details_view', val))"
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
                @if($canManageCourse)
                    <a href="{{ route('courses.edit', $course) }}"
                        class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl font-bold text-xs uppercase tracking-widest hover:border-orange-500 hover:text-orange-600 transition-all flex items-center gap-2 shadow-sm">
                        <i class="bi bi-pencil-square"></i> Editar Curso
                    </a>
                @endif

                @php
                    $isEnrolled = $course->enrollments->where('user_id', auth()->id())->first();
                    $isRegistrationOpen = $course->isRegistrationOpen();
                @endphp

                @if(!$isEnrolled && $isRegistrationOpen)
                    <form action="{{ route('courses.enroll', $course) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-6 py-2 bg-orange-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20 flex items-center gap-2">
                            <i class="bi bi-person-plus-fill"></i> Matricular-me
                        </button>
                    </form>
                @elseif(!$isEnrolled && !$isRegistrationOpen)
                    <span class="px-4 py-2 bg-gray-100 dark:bg-gray-700/50 text-gray-400 rounded-xl font-bold text-xs uppercase tracking-widest border border-gray-200 dark:border-gray-700 opacity-60">
                        Inscrições Encerradas
                    </span>
                @endif
            </div>
        </div>

        <!-- Main Course Hero / Info -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Course Summary Card -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-50/50 dark:bg-orange-900/10 rounded-full -mr-32 -mt-32 transition-transform group-hover:scale-110 duration-700"></div>

                <div class="relative z-10 space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase rounded-full tracking-widest inline-block">
                            {{ $course->category ?? 'Geral' }}
                        </span>
                        @if($course->registration_deadline)
                            <span class="px-3 py-1 {{ $course->isRegistrationOpen() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-[10px] font-black uppercase rounded-full tracking-widest inline-block">
                                <i class="bi bi-calendar-event mr-1"></i> Data Limite: {{ \Carbon\Carbon::parse($course->registration_deadline)->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                    <div>
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

            @if(!$isSupervisorView)
            <!-- Stats Card 1: Total Alunos -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-[2.5rem] p-8 text-white shadow-xl shadow-blue-200 dark:shadow-none relative overflow-hidden group">
                <i class="bi bi-person-video3 absolute -right-4 -bottom-4 text-8xl text-white/10 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-100/70">Total Matriculados</p>
                        <p class="text-5xl font-black mt-2">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="pt-4 border-t border-white/10 mt-auto">
                        <div class="flex items-center gap-2 text-[10px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-blue-300 animate-pulse"></span>
                            Alunos no Sistema
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card 2: Turmas & Pendentes -->
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Turmas Ativas</p>
                            <p class="text-2xl font-black text-green-600 dark:text-green-400">{{ $stats['active_classes'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center text-green-600">
                            <i class="bi bi-mortarboard-fill text-xl"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Inscrições Públicas</p>
                            <p class="text-2xl font-black text-orange-500">{{ $stats['pending_public'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center text-orange-500">
                            <i class="bi bi-inbox-fill text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-50 dark:border-gray-700 mt-4">
                    <div class="flex justify-between items-center">
                         <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Matrículas Públicas</p>
                         <span class="text-xs font-bold text-gray-900 dark:text-white">
                            {{ $stats['pending_public'] }} pendentes
                         </span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Tabs Navigation -->
        <div class="flex items-center gap-8 border-b border-gray-100 dark:border-gray-700">
            <button @click="activeTab = 'classes'" 
                :class="activeTab === 'classes' ? 'text-orange-600 border-orange-600' : 'text-gray-400 border-transparent hover:text-gray-600 dark:hover:text-gray-200'"
                class="pb-4 border-b-2 font-black text-xs uppercase tracking-[0.2em] transition-all">
                Turmas
            </button>
            @if($showCoupleSections && isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
                <button @click="activeTab = 'public'" 
                    :class="activeTab === 'public' ? 'text-purple-600 border-purple-600' : 'text-gray-400 border-transparent hover:text-gray-600 dark:hover:text-gray-200'"
                    class="pb-4 border-b-2 font-black text-xs uppercase tracking-[0.2em] transition-all flex items-center gap-2">
                    Inscrições Públicas
                    <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 px-2 py-0.5 rounded-full text-[8px]">
                        {{ $publicCoupleEnrollments->count() }}
                    </span>
                </button>
            @endif
            @if(!$isSupervisorView)
                <button @click="activeTab = 'manage'" 
                    :class="activeTab === 'manage' ? 'text-blue-600 border-blue-600' : 'text-gray-400 border-transparent hover:text-gray-600 dark:hover:text-gray-200'"
                    class="pb-4 border-b-2 font-black text-xs uppercase tracking-[0.2em] transition-all flex items-center gap-2">
                    Matrículas
                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded-full text-[8px]">
                        {{ $manageTotalCount }}
                    </span>
                </button>
            @endif
        </div>

        <div class="w-full">
            <!-- TAB: CLASSES -->
            <div x-show="activeTab === 'classes'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white">Lista de Turmas</h3>
                    @if($canManageCourse)
                        <a href="{{ route('course-classes.create', ['course_id' => $course->id]) }}" 
                           class="px-4 py-2 bg-zinc-900 dark:bg-zinc-700 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-zinc-800 transition-all flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i> Nova Turma
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($course->classes as $class)
                        <div class="bg-white dark:bg-gray-800 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:border-orange-500/30 transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[9px] font-black uppercase rounded-full border border-gray-100 dark:border-gray-600">
                                    {{ $class->type === 'pre_nupcial' ? 'Curso Noivos' : 'Casais Vivendo' }}
                                </span>
                                <span class="px-3 py-1 {{ $class->status === 'concluida' ? 'bg-green-100 text-green-700 border-green-200' : ($class->status === 'cancelada' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-blue-100 text-blue-700 border-blue-200') }} text-[9px] font-black uppercase rounded-full border">
                                    {{ ucfirst(str_replace('_', ' ', $class->status)) }}
                                </span>
                            </div>

                            <h4 class="text-lg font-black text-gray-900 dark:text-white mb-2 group-hover:text-orange-600 transition-colors">{{ $class->name }}</h4>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                                    <i class="bi bi-person-badge text-xs"></i>
                                    <span class="text-xs font-bold">{{ $class->teacherMale->name ?? 'Sem Professor' }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1">
                                        <i class="bi bi-people text-xs text-blue-500"></i>
                                        <span class="text-xs font-black text-gray-700 dark:text-gray-300">{{ $class->course_enrollments_count + $class->couple_enrollments_count }} Alunos</span>
                                    </div>
                                    @if($class->start_date)
                                        <div class="flex items-center gap-1">
                                            <i class="bi bi-calendar-check text-xs text-orange-500"></i>
                                            <span class="text-xs font-black text-gray-700 dark:text-gray-300">{{ $class->start_date->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-6 border-t border-gray-100 dark:border-gray-700">
                                <a href="{{ route('course-classes.show', $class) }}" 
                                   class="flex-1 h-11 flex items-center justify-center bg-zinc-900 dark:bg-zinc-700 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-md">
                                    Detalhes
                                </a>
                                @if($canManageCourse)
                                    <div class="flex items-center gap-1 h-11">
                                        <button @click="showMoveModal = true; movingClass = {{ \Illuminate\Support\Js::from(['id' => $class->id, 'name' => $class->name]) }}"
                                                class="w-10 h-11 flex items-center justify-center text-gray-400 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-all"
                                                title="Mover Turma">
                                            <i class="bi bi-arrow-left-right text-sm"></i>
                                        </button>
                                        <form action="{{ route('course-classes.destroy', $class) }}" method="POST" onsubmit="return confirm('Excluir esta turma?');" class="inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-10 h-11 flex items-center justify-center text-gray-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all" title="Excluir">
                                                <i class="bi bi-trash3 text-sm"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('course-classes.edit', $class) }}" 
                                           class="w-10 h-11 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all" title="Editar">
                                            <i class="bi bi-pencil-square text-sm"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center bg-gray-50 dark:bg-gray-900/50 rounded-[2rem] border border-dashed border-gray-200 dark:border-gray-700">
                            <i class="bi bi-mortarboard text-4xl text-gray-200"></i>
                            <p class="text-sm font-black text-gray-400 uppercase tracking-widest mt-4">
                                {{ $isSupervisorView ? 'Você ainda não foi alocado a uma turma neste curso' : 'Nenhuma turma cadastrada' }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB: PUBLIC INBOX -->
            @if($showCoupleSections)
            <div x-show="activeTab === 'public'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                @if($showCoupleSections && isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/10 dark:to-indigo-900/10 border border-purple-100/80 dark:border-purple-800/40 rounded-3xl p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-white/80 dark:bg-gray-800/70 text-purple-600 flex items-center justify-center">
                                    <i class="bi bi-inboxes-fill text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-purple-500">Caixa de Entrada</p>
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Inscrições Públicas Pendentes</h3>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-white/80 dark:bg-gray-800/70 border border-purple-100 dark:border-purple-700 text-purple-700 dark:text-purple-300 text-[10px] font-black uppercase tracking-widest">
                                {{ $publicCoupleEnrollments->count() }} registos
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        @foreach($publicCoupleEnrollments as $enrollment)
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-8 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg hover:border-purple-200 dark:hover:border-purple-700/40 transition-all group">
                                <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">
                                    <div class="flex items-start gap-5 w-full">
                                    <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <h4 class="text-xl font-black text-gray-900 dark:text-white leading-tight">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</h4>
                                                <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[8px] font-black uppercase rounded-full">Novo</span>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500 mb-4 leading-relaxed">
                                                {{ $enrollment->contacts }} <span class="text-gray-300 px-1">•</span> {{ $enrollment->address }}
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-[9px] font-black uppercase rounded-full border border-gray-100 dark:border-gray-600">
                                                    <i class="bi bi-link-45deg mr-1"></i> {{ $enrollment->relationship_type }}
                                                </span>
                                                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-[9px] font-black uppercase rounded-full border border-gray-100 dark:border-gray-600">
                                                    <i class="bi bi-geo-alt mr-1"></i> {{ $enrollment->is_church_member ? 'Membro' : 'Visitante' }}
                                                </span>
                                                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-[9px] font-black uppercase rounded-full border border-gray-100 dark:border-gray-600">
                                                    <i class="bi bi-calendar3 mr-1"></i> {{ $enrollment->created_at?->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="xl:w-[28rem] w-full space-y-4">
                                        @if($canManageCourse)
                                            <form action="{{ route('courses.assign-public-enrollment', $course) }}" method="POST" class="flex flex-col sm:flex-row items-stretch gap-3">
                                                @csrf
                                                <input type="hidden" name="couple_enrollment_id" value="{{ $enrollment->id }}">
                                                <select name="course_class_id" required
                                                    class="w-full sm:flex-1 px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-4 focus:ring-purple-100 dark:focus:ring-purple-900/20 transition-all text-sm font-bold text-gray-900 dark:text-white">
                                                    <option value="">Selecionar turma...</option>
                                                    @foreach($course->classes as $class)
                                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit"
                                                    class="w-full sm:w-auto px-6 py-3.5 bg-purple-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-purple-700 transition-all shadow-lg shadow-purple-600/20">
                                                    Atribuir
                                                </button>
                                            </form>
                                        @endif

                                        <div class="flex items-center justify-between gap-3 pt-1">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ações do Registo</p>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('couple-enrollments.show', $enrollment) }}"
                                                    class="w-11 h-11 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-300 hover:text-purple-600 hover:border-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all flex items-center justify-center"
                                                    title="Ver detalhes" aria-label="Ver detalhes">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                @if($canManageCourse)
                                                    <a href="{{ route('couple-enrollments.edit', $enrollment) }}"
                                                        class="w-11 h-11 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-300 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all flex items-center justify-center"
                                                        title="Editar inscrição" aria-label="Editar inscrição">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('couple-enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Excluir esta inscrição?')" class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-11 h-11 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-300 hover:text-red-600 hover:border-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all flex items-center justify-center"
                                                            title="Excluir inscrição" aria-label="Excluir inscrição">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
            @endif

            <!-- TAB: MANAGE ENROLLMENTS -->
            @if(!$isSupervisorView)
            <div x-show="activeTab === 'manage'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                
                <!-- Filters & Search -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="relative">
                            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="manageSearch" placeholder="Pesquisar por nome..." 
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-bold text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <select x-model="manageClassFilter" 
                                class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-bold text-gray-900 dark:text-white">
                                <option value="">Todas as Turmas</option>
                                @foreach($course->classes as $c)
                                    <option value="{{ $c->name }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select x-model="manageStatusFilter" 
                                class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all text-sm font-bold text-gray-900 dark:text-white">
                                <option value="">Todos os Status</option>
                                @foreach($statusLabels as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Unified Enrollment List -->
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Estudante(s) / Tipo</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Turma</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700 text-sm">
                                @foreach($courseEnrollments as $enrollment)
                                    <tr x-show="(manageSearch === '' || '{{ strtolower($enrollment->user->name ?? '') }}'.includes(manageSearch.toLowerCase())) && 
                                               (manageClassFilter === '' || '{{ $enrollment->courseClass->name ?? '' }}' === manageClassFilter) &&
                                               (manageStatusFilter === '' || '{{ $enrollment->status }}' === manageStatusFilter)"
                                        class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center font-bold text-xs uppercase">
                                                    @if($enrollment->user)
                                                        {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                                    @elseif($enrollment->malePartner)
                                                        <i class="bi bi-people-fill"></i>
                                                    @else
                                                        ?
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-white">
                                                        @if($enrollment->user)
                                                            {{ $enrollment->user->name }}
                                                        @elseif($enrollment->malePartner && $enrollment->femalePartner)
                                                            {{ $enrollment->malePartner->name }} & {{ $enrollment->femalePartner->name }}
                                                        @else
                                                            {{ $enrollment->user->name ?? 'Usuário Removido' }}
                                                        @endif
                                                    </p>
                                                    <span class="text-[9px] font-black uppercase text-blue-500 tracking-wider">
                                                        {{ ($enrollment->malePartner) ? 'Casal' : 'Individual' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="px-3 py-1 bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-[10px] font-bold rounded-lg border border-gray-100 dark:border-gray-700">
                                                {{ $enrollment->courseClass->name ?? 'Sem Turma' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="px-3 py-1 {{ $statusStyles[$enrollment->status] ?? $statusStyles['default'] }} text-[9px] font-black uppercase rounded-full border">
                                                {{ $statusLabels[$enrollment->status] ?? $statusLabels['default'] }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            @if($canManageCourse)
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('course-enrollments.edit', $enrollment) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Editar Matrícula">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('course-enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Remover matrícula?')" class="m-0 flex">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Remover">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @if($showCoupleSections)
                                @foreach($coupleEnrollments as $enrollment)
                                    <tr x-show="(manageSearch === '' || '{{ strtolower($enrollment->husband_name . ' ' . $enrollment->wife_name) }}'.includes(manageSearch.toLowerCase())) && 
                                               (manageClassFilter === '' || '{{ $enrollment->courseClass->name ?? '' }}' === manageClassFilter) &&
                                               (manageStatusFilter === '' || '{{ $enrollment->status }}' === manageStatusFilter)"
                                        class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center font-bold text-xs">
                                                    <i class="bi bi-heart-fill"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-white">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</p>
                                                    <span class="text-[9px] font-black uppercase text-purple-500 tracking-wider">Casal</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="px-3 py-1 bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-[10px] font-bold rounded-lg border border-gray-100 dark:border-gray-700">
                                                {{ $enrollment->courseClass->name ?? 'Sem Turma' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="px-3 py-1 {{ $statusStyles[$enrollment->status] ?? $statusStyles['default'] }} text-[9px] font-black uppercase rounded-full border">
                                                {{ $statusLabels[$enrollment->status] ?? $statusLabels['default'] }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            @if($canManageCourse)
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('couple-enrollments.edit', $enrollment) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Editar Matrícula">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('couple-enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Remover matrícula?')" class="m-0 flex">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Remover">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>


    <!-- Move Class Modal -->
    <div x-show="showMoveModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showMoveModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" 
                 @click="showMoveModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showMoveModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8">
                
                <div class="absolute top-0 right-0 pt-6 pr-6">
                    <button @click="showMoveModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>

                <div class="sm:flex sm:items-start">
                    <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white leading-tight mb-2">
                            Mover Turma
                        </h3>
                        <p class="text-sm font-medium text-gray-500 mb-8">
                            Selecione o curso de destino para a turma <span class="text-orange-600 font-bold" x-text="movingClass?.name"></span>.
                        </p>

                        <form :action="movingClass ? '/course-classes/' + movingClass.id + '/move' : ''" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Curso de Destino</label>
                                <select name="target_course_id" x-model="targetCourseId" required
                                    class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900 border-transparent focus:bg-white focus:ring-4 focus:ring-orange-100 dark:focus:ring-orange-900/20 transition-all text-sm font-bold text-gray-900 dark:text-white">
                                    <option value="">Selecione um curso...</option>
                                    @foreach($allCourses as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-4 flex flex-col sm:flex-row gap-3">
                                <button type="submit" :disabled="!targetCourseId"
                                    class="flex-1 px-8 py-4 bg-orange-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all shadow-xl shadow-orange-600/20 disabled:opacity-50 disabled:shadow-none">
                                    Confirmar Mudança
                                </button>
                                <button type="button" @click="showMoveModal = false"
                                    class="flex-1 px-8 py-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if(auth()->user()->isAdmin() || in_array(auth()->user()->role, ['pastor', 'pastor_senior'], true))
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
