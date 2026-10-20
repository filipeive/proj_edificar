@extends('layouts.app')

@section('title', $course->name . ' - Detalhes do Curso')

@section('content')
@php
    $canManageCourse = auth()->user()->isAdmin() || in_array(auth()->user()->role, ['pastor', 'pastor_senior'], true);
    $isSupervisorView = auth()->user()->isSupervisor();
    $showCoupleSections = !$isSupervisorView;
    $manageTotalCount = $courseEnrollments->count() + ($showCoupleSections ? $coupleEnrollments->count() : 0);

    $statusStyles = [
        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'aprovado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'dropped' => 'bg-red-50 text-red-700 border-red-200',
        'desistente' => 'bg-red-50 text-red-700 border-red-200',
        'enrolled' => 'bg-blue-50 text-blue-700 border-blue-200',
        'cursando' => 'bg-blue-50 text-blue-700 border-blue-200',
        'em_andamento' => 'bg-blue-50 text-blue-700 border-blue-200',
        'default' => 'bg-gray-50 text-gray-700 border-gray-200'
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
    showMoveModal: false,
    movingClass: null,
    targetCourseId: '',
    manageSearch: '',
    manageClassFilter: '',
    manageStatusFilter: ''
}" class="space-y-4">

    <!-- Header Executivo -->
    <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Escola Ministerial & Cursos</span>
                    <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-orange-700 border border-orange-200">
                        {{ $course->category ?? 'Geral' }}
                    </span>
                    @if($course->registration_deadline)
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-black uppercase tracking-wider {{ $course->isRegistrationOpen() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                            Inscrições até {{ \Carbon\Carbon::parse($course->registration_deadline)->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight mt-0.5">{{ $course->name }}</h1>
                <p class="text-xs text-gray-500 font-medium line-clamp-1">{{ $course->description ?? 'Sem descrição cadastrada.' }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('courses.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>

                @if($canManageCourse)
                    <a href="{{ route('courses.edit', $course) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        <i class="bi bi-pencil-square"></i> Editar Curso
                    </a>
                @endif

                @php
                    $isEnrolled = $course->enrollments->where('user_id', auth()->id())->first();
                    $isRegistrationOpen = $course->isRegistrationOpen();
                @endphp

                @if(!$isEnrolled && $isRegistrationOpen)
                    <form action="{{ route('courses.enroll', $course) }}" method="POST" class="inline m-0">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                            <i class="bi bi-person-plus-fill"></i> Matricular-me
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- KPIs Compactos -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Duração</span>
            <span class="text-sm font-bold text-gray-800 mt-0.5 block">{{ $course->duration ?? 'N/A' }}</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Público-Alvo</span>
            <span class="text-sm font-bold text-gray-800 mt-0.5 block">{{ $course->target_role ?? 'Geral' }}</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Turmas Cadastradas</span>
            <span class="text-base sm:text-lg font-bold text-gray-900 mt-0.5 block">{{ $course->classes->count() }}</span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Total Matriculados</span>
            <span class="text-base sm:text-lg font-bold text-orange-600 mt-0.5 block">{{ $stats['total_students'] ?? $manageTotalCount }}</span>
        </div>
    </div>

    <!-- Barra de Abas Compacta -->
    <div class="rounded-xl border border-gray-200 bg-white p-1.5 shadow-sm">
        <div class="flex gap-1.5 overflow-x-auto whitespace-nowrap scrollbar-thin">
            <button @click="activeTab = 'classes'" type="button"
                :class="activeTab === 'classes' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-colors shrink-0 flex items-center gap-1.5">
                <i class="bi bi-mortarboard"></i>
                Turmas ({{ $course->classes->count() }})
            </button>

            @if($showCoupleSections && isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
                <button @click="activeTab = 'public'" type="button"
                    :class="activeTab === 'public' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                    class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-colors shrink-0 flex items-center gap-1.5">
                    <i class="bi bi-inbox"></i>
                    Inscrições Públicas ({{ $publicCoupleEnrollments->count() }})
                </button>
            @endif

            @if(!$isSupervisorView)
                <button @click="activeTab = 'manage'" type="button"
                    :class="activeTab === 'manage' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                    class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-colors shrink-0 flex items-center gap-1.5">
                    <i class="bi bi-people"></i>
                    Matrículas ({{ $manageTotalCount }})
                </button>
            @endif
        </div>
    </div>

    <!-- CONTEÚDO DAS ABAS -->

    <!-- TAB: TURMAS -->
    <div x-show="activeTab === 'classes'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-gray-700">Turmas vinculadas a este curso</span>
            @if($canManageCourse)
                <a href="{{ route('course-classes.create', ['course_id' => $course->id]) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                    <i class="bi bi-plus-lg"></i> Nova Turma
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($course->classes as $class)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-orange-300 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="rounded bg-gray-50 border border-gray-200 px-2 py-0.5 text-[10px] font-black uppercase text-gray-600">
                                {{ $class->type === 'pre_nupcial' ? 'Curso Noivos' : 'Casais Vivendo' }}
                            </span>
                            <span class="rounded px-2 py-0.5 text-[10px] font-black uppercase border {{ $class->status === 'concluida' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($class->status === 'cancelada' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                {{ ucfirst(str_replace('_', ' ', $class->status)) }}
                            </span>
                        </div>

                        <h3 class="text-sm font-bold text-gray-900">{{ $class->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1.5">
                            <i class="bi bi-person-badge text-gray-400"></i>
                            {{ $class->teacherMale->name ?? 'Sem Professor Designado' }}
                        </p>

                        <div class="flex items-center gap-3 mt-3 text-xs text-gray-500 font-medium">
                            <span class="inline-flex items-center gap-1">
                                <i class="bi bi-people text-blue-500"></i>
                                {{ $class->course_enrollments_count + $class->couple_enrollments_count }} Alunos
                            </span>
                            @if($class->start_date)
                                <span class="inline-flex items-center gap-1">
                                    <i class="bi bi-calendar3 text-orange-500"></i>
                                    {{ $class->start_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 pt-3 mt-3 border-t border-gray-100">
                        <a href="{{ route('course-classes.show', $class) }}"
                            class="flex-1 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                            Detalhes
                        </a>

                        @if($canManageCourse)
                            <button type="button"
                                @click="showMoveModal = true; movingClass = {{ \Illuminate\Support\Js::from(['id' => $class->id, 'name' => $class->name]) }}"
                                class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-500 hover:text-orange-600 hover:bg-orange-50 transition-colors"
                                title="Mover Turma">
                                <i class="bi bi-arrow-left-right text-xs"></i>
                            </button>
                            <a href="{{ route('course-classes.edit', $class) }}"
                                class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                title="Editar">
                                <i class="bi bi-pencil-square text-xs"></i>
                            </a>
                            <form action="{{ route('course-classes.destroy', $class) }}" method="POST" onsubmit="return confirm('Excluir esta turma?');" class="inline m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors" title="Excluir">
                                    <i class="bi bi-trash3 text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-200 p-8 text-center bg-white">
                    <i class="bi bi-mortarboard text-2xl text-gray-300"></i>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Nenhuma turma cadastrada neste curso.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB: INSCRIÇÕES PÚBLICAS -->
    @if($showCoupleSections)
    <div x-show="activeTab === 'public'" x-cloak class="space-y-3">
        @if(isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
            <div class="space-y-2.5">
                @foreach($publicCoupleEnrollments as $enrollment)
                    <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:border-orange-300 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs sm:text-sm font-bold text-gray-900">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</h4>
                                    <span class="rounded bg-purple-50 border border-purple-200 px-1.5 py-0.2 text-[9px] font-black uppercase text-purple-700">Pendente</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $enrollment->contacts }} • {{ $enrollment->address }}
                                </p>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="rounded bg-gray-50 border border-gray-200 px-2 py-0.5 text-[10px] text-gray-600 font-medium">
                                        {{ $enrollment->relationship_type }}
                                    </span>
                                    <span class="rounded bg-gray-50 border border-gray-200 px-2 py-0.5 text-[10px] text-gray-600 font-medium">
                                        {{ $enrollment->is_church_member ? 'Membro' : 'Visitante' }}
                                    </span>
                                    <span class="rounded bg-gray-50 border border-gray-200 px-2 py-0.5 text-[10px] text-gray-600 font-medium">
                                        {{ $enrollment->created_at?->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                @if($canManageCourse)
                                    <form action="{{ route('courses.assign-public-enrollment', $course) }}" method="POST" class="flex items-center gap-1.5 m-0">
                                        @csrf
                                        <input type="hidden" name="couple_enrollment_id" value="{{ $enrollment->id }}">
                                        <select name="course_class_id" required
                                            class="rounded-lg border-gray-300 text-xs py-1.5 focus:border-orange-500 focus:ring-orange-500 font-medium">
                                            <option value="">Atribuir a turma...</option>
                                            @foreach($course->classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                            class="inline-flex items-center rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                                            Atribuir
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('couple-enrollments.show', $enrollment) }}"
                                    class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-600 hover:text-orange-600 hover:bg-gray-50 transition-colors"
                                    title="Visualizar">
                                    <i class="bi bi-eye text-xs"></i>
                                </a>
                                @if($canManageCourse)
                                    <a href="{{ route('couple-enrollments.edit', $enrollment) }}"
                                        class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors"
                                        title="Editar">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('couple-enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Excluir esta inscrição?')" class="inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-600 hover:text-red-600 hover:bg-gray-50 transition-colors" title="Excluir">
                                            <i class="bi bi-trash3 text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-200 p-8 text-center bg-white">
                <i class="bi bi-inbox text-2xl text-gray-300"></i>
                <p class="text-xs text-gray-500 mt-2 font-medium">Nenhuma inscrição pública pendente para este curso.</p>
            </div>
        @endif
    </div>
    @endif

    <!-- TAB: MATRÍCULAS GERAIS -->
    @if(!$isSupervisorView)
    <div x-show="activeTab === 'manage'" x-cloak class="space-y-3">
        <!-- Filtros -->
        <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" x-model="manageSearch" placeholder="Pesquisar aluno..."
                        class="w-full rounded-lg border-gray-300 pl-8 pr-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <select x-model="manageClassFilter"
                        class="w-full rounded-lg border-gray-300 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Todas as Turmas</option>
                        @foreach($course->classes as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select x-model="manageStatusFilter"
                        class="w-full rounded-lg border-gray-300 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Todos os Status</option>
                        @foreach($statusLabels as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabela Compacta de Alunos -->
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/75 border-b border-gray-200 text-[10px] font-black uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="py-2.5 px-3">Aluno(s) / Modalidade</th>
                            <th class="py-2.5 px-3">Turma</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 px-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                        @foreach($courseEnrollments as $enrollment)
                            <tr x-show="(manageSearch === '' || '{{ strtolower($enrollment->user->name ?? '') }}'.includes(manageSearch.toLowerCase())) && 
                                       (manageClassFilter === '' || '{{ $enrollment->courseClass->name ?? '' }}' === manageClassFilter) &&
                                       (manageStatusFilter === '' || '{{ $enrollment->status }}' === manageStatusFilter)"
                                class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-2.5 px-3 font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                            @if($enrollment->user)
                                                {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                            @else
                                                ?
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 leading-tight">{{ $enrollment->user->name ?? 'Usuário Removido' }}</p>
                                            <span class="text-[10px] text-gray-400 font-medium">Individual</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-gray-600 text-xs">
                                    {{ $enrollment->courseClass->name ?? 'Sem Turma' }}
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="rounded px-2 py-0.5 text-[10px] font-black uppercase border {{ $statusStyles[$enrollment->status] ?? $statusStyles['default'] }}">
                                        {{ $statusLabels[$enrollment->status] ?? $statusLabels['default'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    @if($canManageCourse)
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('course-enrollments.edit', $enrollment) }}"
                                                class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors"
                                                title="Editar">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                            <form action="{{ route('course-enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Remover matrícula?')" class="inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-red-600 hover:bg-gray-50 transition-colors" title="Remover">
                                                    <i class="bi bi-trash3 text-xs"></i>
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
                                    class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-2.5 px-3 font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                <i class="bi bi-heart-fill text-[11px]"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 leading-tight">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</p>
                                                <span class="text-[10px] text-purple-600 font-medium">Casal</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-600 text-xs">
                                        {{ $enrollment->courseClass->name ?? 'Sem Turma' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="rounded px-2 py-0.5 text-[10px] font-black uppercase border {{ $statusStyles[$enrollment->status] ?? $statusStyles['default'] }}">
                                            {{ $statusLabels[$enrollment->status] ?? $statusLabels['default'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-right">
                                        @if($canManageCourse)
                                            <div class="inline-flex items-center gap-1">
                                                <a href="{{ route('couple-enrollments.edit', $enrollment) }}"
                                                    class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors"
                                                    title="Editar">
                                                    <i class="bi bi-pencil-square text-xs"></i>
                                                </a>
                                                <form action="{{ route('couple-enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Remover matrícula?')" class="inline m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-red-600 hover:bg-gray-50 transition-colors" title="Remover">
                                                        <i class="bi bi-trash3 text-xs"></i>
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

    <!-- Move Class Modal -->
    <div x-show="showMoveModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="showMoveModal = false"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-5 text-left shadow-xl border border-gray-200 z-10">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-sm font-bold text-gray-900">Mover Turma de Curso</h3>
                    <button type="button" @click="showMoveModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>

                <p class="text-xs text-gray-500 mb-4">
                    Selecione o curso de destino para a turma <span class="font-bold text-gray-800" x-text="movingClass?.name"></span>:
                </p>

                <form :action="movingClass ? '/course-classes/' + movingClass.id + '/move' : ''" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Curso de Destino</label>
                        <select name="target_course_id" x-model="targetCourseId" required
                            class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Selecione o novo curso...</option>
                            @foreach($allCourses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="showMoveModal = false"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="!targetCourseId"
                            class="rounded-lg bg-orange-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 disabled:opacity-50">
                            Confirmar Mudança
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
