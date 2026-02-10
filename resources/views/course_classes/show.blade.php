@extends('layouts.app')

@section('title', $courseClass->name . ' - Portal Life Church')
@section('page-title', $courseClass->name)
@section('page-subtitle', $courseClass->course->name)

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('course-classes.index', ['course_id' => $courseClass->course_id]) }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Voltar à lista">
            <i class="bi bi-arrow-left"></i>
        </a>
        <a href="{{ route('course-classes.report', $courseClass) }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Relatório final">
            <i class="bi bi-bar-chart-fill"></i>
        </a>
        <a href="{{ route('course-classes.export-pdf', $courseClass) }}"
            class="action-icon text-gray-600 hover:text-red-600 hover:bg-red-50"
            title="Relatório PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
            <a href="{{ route('course-classes.edit', $courseClass) }}"
                class="action-icon text-gray-600 hover:text-orange-600 hover:bg-orange-50"
                title="Editar turma">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    @php
        $isPastorZona = auth()->user()->isPastorZona();
        $statusClasses = [
            'em_andamento' => 'bg-green-100 text-green-800 border-green-200',
            'concluida' => 'bg-blue-100 text-blue-800 border-blue-200',
            'cancelada' => 'bg-red-100 text-red-800 border-red-200',
            'active' => 'bg-green-100 text-green-800 border-green-200',
            'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
            'cursando' => 'bg-blue-100 text-blue-800 border-blue-200',
            'aprovado' => 'bg-green-100 text-green-800 border-green-200',
            'reprovado' => 'bg-red-100 text-red-800 border-red-200',
            'desistente' => 'bg-gray-100 text-gray-800 border-gray-200',
            'default' => 'bg-gray-100 text-gray-700 border-gray-200'
        ];
        $statusLabels = [
            'em_andamento' => 'Em Andamento',
            'concluida' => 'Concluída',
            'cancelada' => 'Cancelada',
            'active' => 'Ativa',
            'completed' => 'Concluída',
            'cancelled' => 'Cancelada',
            'cursando' => 'Cursando',
            'aprovado' => 'Aprovado',
            'reprovado' => 'Reprovado',
            'desistente' => 'Desistente',
            'default' => 'Processando'
        ];
    @endphp

    <div x-data="{ 
        activeTab: 'students',
        view: localStorage.getItem('class_details_view') || 'list'
    }" 
    x-init="$watch('view', val => localStorage.setItem('class_details_view', val))"
    class="w-full space-y-8">
        
        <!-- Breadcrumbs & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('course-classes.index', ['course_id' => $courseClass->course_id]) }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                            <i class="bi bi-journal-check mr-2"></i>
                            Turmas
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="bi bi-chevron-right text-gray-400 text-xs mx-2"></i>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $courseClass->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('course-classes.report', $courseClass) }}"
                    class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-xl font-bold text-xs uppercase tracking-widest hover:border-blue-500 hover:text-blue-600 transition-all flex items-center gap-2 shadow-sm">
                    <i class="bi bi-bar-chart-fill"></i> Relatório
                </a>
                <a href="{{ route('course-classes.export-pdf', $courseClass) }}"
                    class="px-4 py-2 bg-white dark:bg-gray-800 text-red-600 border border-gray-200 dark:border-gray-700 rounded-xl font-bold text-xs uppercase tracking-widest hover:border-red-500 transition-all flex items-center gap-2 shadow-sm">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
                @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                    <a href="{{ route('course-classes.edit', $courseClass) }}"
                        class="px-4 py-2 bg-orange-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20 flex items-center gap-2">
                        <i class="bi bi-pencil-square"></i> Editar
                    </a>
                @endif
            </div>
        </div>

        <!-- Class Hero Info -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Class Info Card -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50/50 dark:bg-blue-900/10 rounded-full -mr-32 -mt-32 transition-transform group-hover:scale-110 duration-700"></div>

                <div class="relative z-10 space-y-6">
                    <div>
                        <span class="px-3 py-1 {{ $statusClasses[$courseClass->status] ?? $statusClasses['default'] }} text-[10px] font-black uppercase rounded-full tracking-widest mb-4 inline-block border">
                            {{ $statusLabels[$courseClass->status] ?? $courseClass->status }}
                        </span>
                        <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-none mb-2">{{ $courseClass->name }}</h1>
                        <p class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-xs">{{ $courseClass->course->name }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 pt-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                                <i class="bi bi-calendar-range"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Período</p>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">
                                    {{ $courseClass->start_date ? $courseClass->start_date->format('d/m/Y') : 'N/A' }} - {{ $courseClass->end_date ? $courseClass->end_date->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Alunos</p>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $allStudents->count() }} Total</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers Card -->
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-gray-700 pb-2">Equipe Docente</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 font-black">
                            {{ substr($courseClass->teacherMale->name ?? 'N', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Professor Masc.</p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $courseClass->teacherMale->name ?? 'Não atribuído' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-pink-50 dark:bg-pink-900/20 flex items-center justify-center text-pink-600 font-black">
                            {{ substr($courseClass->teacherFemale->name ?? 'N', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Professora Fem.</p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $courseClass->teacherFemale->name ?? 'Não atribuída' }}</p>
                        </div>
                    </div>
                </div>

                @if($courseClass->assistantMale || $courseClass->assistantFemale)
                    <div class="pt-4 border-t border-gray-50 dark:border-gray-700">
                        <p class="text-[8px] font-black text-gray-300 uppercase tracking-[0.2em] mb-4">Auxiliares</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($courseClass->assistantMale)
                                <div class="flex items-center gap-3 opacity-80">
                                    <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 font-black text-[10px]">
                                        {{ substr($courseClass->assistantMale->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Auxiliar Masc.</p>
                                        <p class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $courseClass->assistantMale->name }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($courseClass->assistantFemale)
                                <div class="flex items-center gap-3 opacity-80">
                                    <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 font-black text-[10px]">
                                        {{ substr($courseClass->assistantFemale->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Auxiliar Fem.</p>
                                        <p class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $courseClass->assistantFemale->name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Meetings Quick Stats -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-orange-100 dark:shadow-none relative overflow-hidden group">
                <i class="bi bi-calendar-check absolute -right-4 -bottom-4 text-8xl text-white/10 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-orange-100/70">Encontros</p>
                        <p class="text-5xl font-black mt-2">{{ $courseClass->meetings->count() }}</p>
                    </div>
                    <div class="pt-4 border-t border-white/10 mt-auto">
                        <button onclick="document.getElementById('meetingModal').classList.remove('hidden')" class="text-[10px] font-bold flex items-center gap-2 hover:bg-white/10 px-3 py-1 rounded-full transition-all">
                            <i class="bi bi-plus-circle"></i> Agendar Novo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex items-center gap-8 border-b border-gray-100 dark:border-gray-700">
            <button @click="activeTab = 'students'" 
                :class="activeTab === 'students' ? 'text-blue-600 border-blue-600' : 'text-gray-400 border-transparent hover:text-gray-600 dark:hover:text-gray-200'"
                class="pb-4 border-b-2 font-black text-xs uppercase tracking-[0.2em] transition-all">
                Alunos da Turma
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
            <button @click="activeTab = 'meetings'" 
                :class="activeTab === 'meetings' ? 'text-orange-600 border-orange-600' : 'text-gray-400 border-transparent hover:text-gray-600 dark:hover:text-gray-200'"
                class="pb-4 border-b-2 font-black text-xs uppercase tracking-[0.2em] transition-all">
                Cronograma
            </button>
        </div>

        <!-- TAB CONTENT -->
        <div class="w-full">
            <!-- TAB: STUDENTS -->
            <div x-show="activeTab === 'students'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                
                @if(!auth()->user()->isSupervisor() || auth()->user()->isEnrolledInClass($courseClass->id))
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <!-- List Header -->
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center bg-gray-50/10 dark:bg-gray-700/10 gap-4">
                        <div class="flex items-center gap-4">
                            <h4 class="text-xl font-black text-gray-900 dark:text-white">Lista de Alunos</h4>
                            <div class="flex bg-gray-50 dark:bg-gray-900 p-1 rounded-xl border border-gray-100 dark:border-gray-700">
                                <button @click="view = 'list'" 
                                    :class="view === 'list' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                    class="p-2 rounded-lg transition-all">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                                <button @click="view = 'grid'" 
                                    :class="view === 'grid' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                    class="p-2 rounded-lg transition-all">
                                    <i class="bi bi-grid-fill"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                                <button onclick="document.getElementById('enrollmentModal').classList.remove('hidden')" 
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 flex items-center gap-2">
                                    <i class="bi bi-person-plus-fill text-sm"></i> Matricular Aluno
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- LIST VIEW -->
                    <div x-show="view === 'list'" class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-8 py-5">Aluno / Casal</th>
                                    <th class="px-8 py-5">Tipo</th>
                                    <th class="px-8 py-5 text-center">Faltas</th>
                                    <th class="px-8 py-5 text-center">Status</th>
                                    <th class="px-8 py-5 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 font-medium">
                                @forelse($allStudents as $student)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full {{ $student instanceof \App\Models\CoupleEnrollment ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center font-black">
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
                                                            {{ $student->user_id ? $student->user->email : 'Matrícula Manual' }}
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
                                        <td class="px-8 py-6 text-center">
                                            @if($student instanceof \App\Models\CourseEnrollment)
                                                <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 text-xs font-black {{ $student->absence_count > 2 ? 'text-red-500' : 'text-gray-500' }}">
                                                    {{ $student->absence_count }}
                                                </span>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="px-3 py-1 {{ $statusClasses[$student->status] ?? $statusClasses['default'] }} text-[9px] font-black uppercase rounded-full border">
                                                {{ $statusLabels[$student->status] ?? $statusLabels['default'] }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                                                    <div x-data="{ open: false }" class="relative">
                                                        <button @click="open = !open" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Mudar Status">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                        <div x-show="open" @click.away="open = false" 
                                                            class="absolute right-0 bottom-full mb-2 w-48 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 z-50">
                                                            <p class="px-4 py-2 text-[8px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-gray-700 mb-1 text-left">Andamento</p>
                                                            @php
                                                                $availableStatuses = ['cursando' => 'Cursando', 'aprovado' => 'Aprovado', 'reprovado' => 'Reprovado', 'desistente' => 'Desistiu'];
                                                            @endphp
                                                            @foreach($availableStatuses as $s => $l)
                                                                <form action="{{ $student instanceof \App\Models\CoupleEnrollment ? route('couple-enrollments.status', $student) : route('enrollments.status', $student) }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="{{ $s }}">
                                                                    <button type="submit"
                                                                        class="w-full text-left px-4 py-2 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 {{ $student->status == $s ? 'text-blue-600 bg-blue-50/50' : 'text-gray-500' }}">
                                                                        {{ $l }}
                                                                    </button>
                                                                </form>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    @if($student instanceof \App\Models\CourseEnrollment)
                                                        <a href="{{ route('course-enrollments.edit', $student) }}" class="p-2 text-gray-400 hover:text-orange-600 transition-colors">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                        <form action="{{ route('course-classes.remove-enrollment', $courseClass) }}" method="POST" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="enrollment_id" value="{{ $student->id }}">
                                                            <button type="button" onclick="if(confirm('Remover aluno desta turma?')) this.form.submit()" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                                @if($student instanceof \App\Models\CourseEnrollment)
                                                    <a href="{{ route('course-enrollments.show', $student) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Ver Perfil">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-20 text-center">
                                            <div class="flex flex-col items-center gap-4 text-gray-300 dark:text-gray-600">
                                                <i class="bi bi-people-fill text-6xl"></i>
                                                <p class="text-sm font-black uppercase tracking-widest">Nenhum aluno matriculado nesta turma</p>
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
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-3xl p-6 border border-gray-100 dark:border-gray-700/50 hover:border-blue-200 transition-all group">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-blue-600 font-black text-xl">
                                        @if($student instanceof \App\Models\CoupleEnrollment)
                                            <i class="bi bi-heart-fill text-purple-600"></i>
                                        @else
                                            {{ substr($student->user->name ?? $student->malePartner->name ?? 'N', 0, 1) }}
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 {{ $statusClasses[$student->status] ?? $statusClasses['default'] }} text-[8px] font-black uppercase rounded-lg border">
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
                                    {{ $student instanceof \App\Models\CoupleEnrollment ? $student->contacts : ($student->user->email ?? 'Matrícula Manual') }}
                                </p>
                                
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                                    <div>
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none">Matrícula</p>
                                        <p class="text-[10px] font-black text-gray-800 dark:text-gray-200 mt-1 uppercase">
                                            {{ $student instanceof \App\Models\CoupleEnrollment ? 'Inscrição Pública' : 'Interna' }}
                                        </p>
                                    </div>
                                    @if($student instanceof \App\Models\CourseEnrollment)
                                        <a href="{{ route('course-enrollments.show', $student) }}" class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 hover:text-blue-600 shadow-sm transition-colors">
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

            <!-- TAB: PUBLIC INBOX (Assigned specifically to this session's context if needed) -->
            <div x-show="activeTab === 'public'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                @if(isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($publicCoupleEnrollments as $enrollment)
                            <div class="bg-purple-50/30 dark:bg-purple-900/10 rounded-3xl p-8 border border-purple-100 dark:border-purple-800/20 flex flex-col lg:flex-row items-center justify-between gap-8 group">
                                <div class="flex items-start gap-6">
                                    <div class="w-16 h-16 rounded-3xl bg-white dark:bg-gray-800 text-purple-600 flex items-center justify-center text-2xl shadow-sm group-hover:scale-110 transition-all">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-black text-gray-900 dark:text-white leading-none mb-2">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</h4>
                                        <p class="text-sm font-medium text-gray-500 mb-4">{{ $enrollment->contacts }} · {{ $enrollment->address }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="px-2 py-0.5 bg-white/50 dark:bg-gray-800 rounded-lg text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400 border border-purple-100">{{ $enrollment->relationship_type }}</span>
                                            <span class="px-2 py-0.5 bg-white/50 dark:bg-gray-800 rounded-lg text-[9px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400 border border-purple-100">{{ $enrollment->is_church_member ? 'Membro' : 'Visitante' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                                    <form action="{{ route('course-classes.assign-couple-enrollment', $courseClass) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="couple_enrollment_id" value="{{ $enrollment->id }}">
                                        <button type="submit" class="w-full lg:w-auto px-8 py-4 bg-purple-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-purple-700 transition-all shadow-xl shadow-purple-200">
                                            Adicionar à Turma
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- TAB: MEETINGS -->
            <div x-show="activeTab === 'meetings'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/10 dark:bg-gray-700/10">
                        <h4 class="text-xl font-black text-gray-900 dark:text-white">Cronograma de Encontros</h4>
                        <button onclick="document.getElementById('meetingModal').classList.remove('hidden')" class="text-orange-600 font-black text-xs uppercase tracking-widest hover:text-orange-700">
                            + Novo Encontro
                        </button>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($courseClass->meetings as $meeting)
                            <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-700 relative group overflow-hidden">
                                <div class="absolute -right-4 -top-4 w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-orange-600 font-black">
                                    {{ $meeting->meeting_number }}º
                                </div>
                                <div class="relative z-10 pt-4">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">{{ $meeting->date->format('d/m/Y') }}</p>
                                    <h5 class="text-lg font-black text-gray-900 dark:text-white mb-6">{{ $meeting->topic ?? 'Encontro' }}</h5>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-orange-500 shadow-sm">
                                                <i class="bi bi-people-fill"></i>
                                            </span>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $meeting->attendances->count() }} Presentes</span>
                                        </div>
                                        @if(!$isPastorZona)
                                            <a href="{{ route('course-classes.attendance', [$courseClass, $meeting]) }}" class="px-4 py-2 bg-white dark:bg-gray-800 text-orange-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm border border-gray-100 dark:border-gray-700 hover:bg-orange-600 hover:text-white transition-all">
                                                Aula & Chamada
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center">
                                <p class="text-gray-400 italic">Nenhum encontro agendado ainda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Adicionar Inscrito -->
    <div id="enrollmentModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-2xl w-full overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                <h4 class="text-xl font-black text-gray-900">Novo Casal / Inscrito</h4>
                <button type="button" onclick="document.getElementById('enrollmentModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg text-2xl"></i>
                </button>
            </div>
            <div class="p-8">
                <form action="{{ route('course-classes.add-enrollment', $courseClass) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Parceiro Masculino</label>
                            <select name="male_partner_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 select2">
                                <option value="">Selecione...</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Parceiro Feminino</label>
                            <select name="female_partner_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 select2">
                                <option value="">Selecione...</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                             <div class="p-4 bg-blue-50 rounded-xl text-blue-700 text-xs">
                                <i class="bi bi-info-circle mr-2"></i> Se for um curso individual, preencha apenas um dos parceiros ou use o sistema de matrículas individuais.
                             </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Data do Casamento</label>
                            <input type="date" name="wedding_date" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Membro da Igreja?</label>
                            <select name="is_church_member" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" onclick="document.getElementById('enrollmentModal').classList.add('hidden')"
                            class="px-6 py-2 text-gray-500 font-bold">Cancelar</button>
                        <button type="submit"
                            class="px-8 py-2 bg-blue-600 text-white rounded-xl font-black uppercase tracking-widest shadow-lg shadow-blue-600/20">Matricular</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Novo Encontro -->
    <div id="meetingModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                <h4 class="text-xl font-black text-gray-900">Agendar Encontro</h4>
                <button type="button" onclick="document.getElementById('meetingModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg text-2xl"></i>
                </button>
            </div>
            <div class="p-8">
                <form action="{{ route('course-classes.meetings.store', $courseClass) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nº do Encontro</label>
                                <input type="number" name="meeting_number" required
                                    value="{{ $courseClass->meetings->count() + 1 }}"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Data</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tema / Tópico</label>
                            <input type="text" name="topic" placeholder="Ex: Finanças no Casamento"
                                class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" onclick="document.getElementById('meetingModal').classList.add('hidden')"
                            class="px-6 py-2 text-gray-500 font-bold">Cancelar</button>
                        <button type="submit"
                            class="px-8 py-2 bg-orange-600 text-white rounded-xl font-black uppercase tracking-widest shadow-lg shadow-orange-600/20">Agendar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function prepareEnrollmentData() {
            const select = document.querySelector('select[name="enrollment_data"]');
            const [type, id] = select.value.split(':');
            document.getElementById('modal_type').value = type;
            document.getElementById('modal_id').value = id;
        }
    </script>
@endsection
