@extends('layouts.app')

@section('title', $courseClass->name . ' - Portal Life Church')

@section('content')
@php
    $isPastorZona = auth()->user()->isPastorZona();
    $statusClasses = [
        'em_andamento' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'concluida' => 'bg-blue-50 text-blue-700 border-blue-200',
        'cancelada' => 'bg-red-50 text-red-700 border-red-200',
        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
        'cursando' => 'bg-blue-50 text-blue-700 border-blue-200',
        'aprovado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'reprovado' => 'bg-red-50 text-red-700 border-red-200',
        'desistente' => 'bg-gray-100 text-gray-700 border-gray-200',
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
}" x-init="$watch('view', val => localStorage.setItem('class_details_view', val))"
    class="space-y-4">

    <!-- Header Executivo -->
    <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Turma Ministerial</span>
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-black uppercase tracking-wider border {{ $statusClasses[$courseClass->status] ?? $statusClasses['default'] }}">
                        {{ $statusLabels[$courseClass->status] ?? $courseClass->status }}
                    </span>
                    <span class="text-xs text-gray-400 font-medium">| {{ $courseClass->course->name }}</span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight mt-0.5">{{ $courseClass->name }}</h1>
                <p class="text-xs text-gray-500 font-medium">
                    Período: {{ $courseClass->start_date ? $courseClass->start_date->format('d/m/Y') : 'N/A' }} a {{ $courseClass->end_date ? $courseClass->end_date->format('d/m/Y') : 'N/A' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('course-classes.index', ['course_id' => $courseClass->course_id]) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <a href="{{ route('course-classes.report', $courseClass) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="bi bi-bar-chart-fill text-orange-600"></i> Relatório
                </a>
                <a href="{{ route('course-classes.export-pdf', $courseClass) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100 transition-colors">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
                @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                    <a href="{{ route('course-classes.edit', $courseClass) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                        <i class="bi bi-pencil-square"></i> Editar
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Cards de Resumo & Equipe Docente -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <!-- Card 1: Alunos e Aulas -->
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm space-y-2">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Visão Geral da Turma</span>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">Total de Alunos</span>
                <span class="font-bold text-gray-900">{{ $allStudents->count() }} inscritos</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">Encontros Realizados</span>
                <span class="font-bold text-gray-900">{{ $courseClass->meetings->count() }} aulas</span>
            </div>
            <div class="pt-2 border-t border-gray-100 flex justify-end">
                <button onclick="document.getElementById('meetingModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-1 text-[11px] font-bold text-orange-600 hover:text-orange-700">
                    <i class="bi bi-plus-circle"></i> Agendar Novo Encontro
                </button>
            </div>
        </div>

        <!-- Card 2: Docentes Responsáveis -->
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm space-y-2 md:col-span-2">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Equipe de Ensino</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50/60 border border-gray-100">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ substr($courseClass->teacherMale->name ?? 'N', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-black">Professor</p>
                        <p class="font-bold text-gray-800 leading-tight">{{ $courseClass->teacherMale->name ?? 'Não atribuído' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 p-2 rounded-lg bg-gray-50/60 border border-gray-100">
                    <div class="w-7 h-7 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ substr($courseClass->teacherFemale->name ?? 'N', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-black">Professora</p>
                        <p class="font-bold text-gray-800 leading-tight">{{ $courseClass->teacherFemale->name ?? 'Não atribuída' }}</p>
                    </div>
                </div>
            </div>

            @if($courseClass->assistantMale || $courseClass->assistantFemale)
                <div class="flex items-center gap-4 text-[11px] text-gray-500 pt-1">
                    <span class="font-bold text-gray-400 uppercase text-[9px]">Auxiliares:</span>
                    @if($courseClass->assistantMale)
                        <span>{{ $courseClass->assistantMale->name }} (Masc.)</span>
                    @endif
                    @if($courseClass->assistantFemale)
                        <span>{{ $courseClass->assistantFemale->name }} (Fem.)</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Barra de Abas Compacta -->
    <div class="rounded-xl border border-gray-200 bg-white p-1.5 shadow-sm">
        <div class="flex gap-1.5 overflow-x-auto whitespace-nowrap scrollbar-thin">
            <button @click="activeTab = 'students'" type="button"
                :class="activeTab === 'students' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-colors shrink-0 flex items-center gap-1.5">
                <i class="bi bi-people"></i>
                Alunos da Turma ({{ $allStudents->count() }})
            </button>

            @if((isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0) || (isset($publicMinisterialEnrollments) && $publicMinisterialEnrollments->count() > 0))
                <button @click="activeTab = 'public'" type="button"
                    :class="activeTab === 'public' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                    class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-colors shrink-0 flex items-center gap-1.5">
                    <i class="bi bi-inbox"></i>
                    Inscrições Públicas ({{ $publicCoupleEnrollments->count() + $publicMinisterialEnrollments->count() }})
                </button>
            @endif

            <button @click="activeTab = 'meetings'" type="button"
                :class="activeTab === 'meetings' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-colors shrink-0 flex items-center gap-1.5">
                <i class="bi bi-calendar-check"></i>
                Cronograma ({{ $courseClass->meetings->count() }})
            </button>
        </div>
    </div>

    <!-- CONTEÚDO DAS ABAS -->

    <!-- TAB: ALUNOS DA TURMA -->
    <div x-show="activeTab === 'students'" x-cloak class="space-y-3">
        @if(!auth()->user()->isSupervisor() || auth()->user()->isEnrolledInClass($courseClass->id))
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/75 px-3.5 py-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-700">Listagem de Alunos Inscritos</span>
                    </div>

                    @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                        <button onclick="document.getElementById('enrollmentModal').classList.remove('hidden')"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                            <i class="bi bi-person-plus-fill"></i> Matricular Aluno
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/75 border-b border-gray-200 text-[10px] font-black uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="py-2.5 px-3">Aluno / Casal</th>
                                <th class="py-2.5 px-3">Origem</th>
                                <th class="py-2.5 px-3 text-center">Faltas</th>
                                <th class="py-2.5 px-3 text-center">Status</th>
                                <th class="py-2.5 px-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                            @forelse($allStudents as $student)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-2.5 px-3 font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg {{ $student instanceof \App\Models\CoupleEnrollment ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }} flex items-center justify-center font-bold text-xs shrink-0">
                                                @if($student instanceof \App\Models\CoupleEnrollment)
                                                    <i class="bi bi-heart-fill text-[11px]"></i>
                                                @elseif($student instanceof \App\Models\MinisterialEnrollment)
                                                    <i class="bi bi-mortarboard-fill text-[11px]"></i>
                                                @else
                                                    {{ $student->user_id ? substr($student->user->name, 0, 1) : substr($student->malePartner->name ?? 'N', 0, 1) }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 leading-tight">
                                                    @if($student instanceof \App\Models\CoupleEnrollment)
                                                        {{ $student->husband_name }} & {{ $student->wife_name }}
                                                    @elseif($student instanceof \App\Models\MinisterialEnrollment)
                                                        {{ $student->full_name }}
                                                    @else
                                                        {{ $student->user_id ? $student->user->name : ($student->malePartner->name ?? 'N/A') . ' & ' . ($student->femalePartner->name ?? 'N/A') }}
                                                    @endif
                                                </p>
                                                <p class="text-[10px] text-gray-400">
                                                    @if($student instanceof \App\Models\CoupleEnrollment)
                                                        {{ $student->contacts }}
                                                    @elseif($student instanceof \App\Models\MinisterialEnrollment)
                                                        {{ $student->email }} · {{ $student->phone }}
                                                    @else
                                                        {{ $student->user_id ? $student->user->email : 'Matrícula Manual' }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-500 text-xs">
                                        {{ $student instanceof \App\Models\CoupleEnrollment || $student instanceof \App\Models\MinisterialEnrollment ? 'Inscrição Pública' : 'Interna' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @if($student instanceof \App\Models\CourseEnrollment)
                                            <span class="rounded px-1.5 py-0.5 text-xs font-bold {{ $student->absence_count > 2 ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $student->absence_count }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="rounded px-2 py-0.5 text-[10px] font-black uppercase border {{ $statusClasses[$student->status] ?? $statusClasses['default'] }}">
                                            {{ $statusLabels[$student->status] ?? $statusLabels['default'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                                                @if($student instanceof \App\Models\CourseEnrollment)
                                                    <a href="{{ route('course-enrollments.edit', $student) }}" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors" title="Editar">
                                                        <i class="bi bi-pencil text-xs"></i>
                                                    </a>
                                                    <form action="{{ route('course-classes.remove-enrollment', $courseClass) }}" method="POST" class="inline m-0">
                                                        @csrf
                                                        <input type="hidden" name="enrollment_id" value="{{ $student->id }}">
                                                        <button type="button" onclick="if(confirm('Remover aluno desta turma?')) this.form.submit()" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-red-600 hover:bg-gray-50 transition-colors" title="Remover">
                                                            <i class="bi bi-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                @elseif($student instanceof \App\Models\CoupleEnrollment)
                                                    <a href="{{ route('couple-enrollments.edit', $student) }}" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors" title="Editar">
                                                        <i class="bi bi-pencil text-xs"></i>
                                                    </a>
                                                @elseif($student instanceof \App\Models\MinisterialEnrollment)
                                                    <a href="{{ route('ministerial-enrollments.edit', $student) }}" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors" title="Editar">
                                                        <i class="bi bi-pencil text-xs"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if($student instanceof \App\Models\CourseEnrollment)
                                                <a href="{{ route('course-enrollments.show', $student) }}" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-orange-600 hover:bg-gray-50 transition-colors" title="Visualizar">
                                                    <i class="bi bi-eye text-xs"></i>
                                                </a>
                                            @elseif($student instanceof \App\Models\CoupleEnrollment)
                                                <a href="{{ route('couple-enrollments.show', $student) }}" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-purple-600 hover:bg-gray-50 transition-colors" title="Visualizar">
                                                    <i class="bi bi-eye text-xs"></i>
                                                </a>
                                            @elseif($student instanceof \App\Models\MinisterialEnrollment)
                                                <a href="{{ route('ministerial-enrollments.show', $student) }}" class="rounded border border-gray-200 bg-white p-1 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors" title="Visualizar">
                                                    <i class="bi bi-eye text-xs"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-xs text-gray-500">
                                        Nenhum aluno matriculado nesta turma.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- TAB: INSCRIÇÕES PÚBLICAS -->
    <div x-show="activeTab === 'public'" x-cloak class="space-y-3">
        @if(isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
            <div class="space-y-2">
                <span class="text-xs font-bold text-gray-700 block">Inscrições de Casais Pendentes</span>
                @foreach($publicCoupleEnrollments as $enrollment)
                    <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:border-orange-300 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-gray-900">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $enrollment->contacts }} · {{ $enrollment->address }}</p>
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                <span class="rounded bg-purple-50 border border-purple-200 px-1.5 py-0.2 text-[9px] font-black uppercase text-purple-700">{{ $enrollment->relationship_type }}</span>
                                <span class="rounded bg-gray-50 border border-gray-200 px-1.5 py-0.2 text-[9px] text-gray-600 font-medium">
                                    {{ $enrollment->is_church_member === 'both' ? 'Ambos Membros' : ($enrollment->is_church_member === 'one' ? '1 Membro' : 'Visitantes') }}
                                </span>
                            </div>
                        </div>

                        @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                            <div class="flex items-center gap-2">
                                <a href="{{ route('couple-enrollments.show', $enrollment) }}" class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-600 hover:text-purple-600 hover:bg-gray-50" title="Ver Detalhes">
                                    <i class="bi bi-eye text-xs"></i>
                                </a>
                                <form action="{{ route('course-classes.assign-couple-enrollment', $courseClass) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="couple_enrollment_id" value="{{ $enrollment->id }}">
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-purple-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-purple-700 transition-colors">
                                        <i class="bi bi-plus-lg"></i> Adicionar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if(isset($publicMinisterialEnrollments) && $publicMinisterialEnrollments->count() > 0)
            <div class="space-y-2 pt-2">
                <span class="text-xs font-bold text-gray-700 block">Inscrições Ministeriais Pendentes</span>
                @foreach($publicMinisterialEnrollments as $enrollment)
                    <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:border-orange-300 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-gray-900">{{ $enrollment->full_name }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $enrollment->email }} · {{ $enrollment->phone }}</p>
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                <span class="rounded bg-blue-50 border border-blue-200 px-1.5 py-0.2 text-[9px] font-black uppercase text-blue-700">{{ $enrollment->is_church_member ? 'Membro' : 'Visitante' }}</span>
                                @if($enrollment->cell_name)
                                    <span class="rounded bg-gray-50 border border-gray-200 px-1.5 py-0.2 text-[9px] text-gray-600">Célula: {{ $enrollment->cell_name }}</span>
                                @endif
                            </div>
                        </div>

                        @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ministerial-enrollments.show', $enrollment) }}" class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-600 hover:text-blue-600 hover:bg-gray-50" title="Ver Detalhes">
                                    <i class="bi bi-eye text-xs"></i>
                                </a>
                                <form action="{{ route('course-classes.assign-ministerial-enrollment', $courseClass) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="ministerial_enrollment_id" value="{{ $enrollment->id }}">
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                                        <i class="bi bi-plus-lg"></i> Matricular
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB: CRONOGRAMA DE ENCONTROS -->
    <div x-show="activeTab === 'meetings'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-gray-700">Cronograma de Aulas e Chamadas</span>
            <button onclick="document.getElementById('meetingModal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                <i class="bi bi-plus-lg"></i> Novo Encontro
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($courseClass->meetings as $meeting)
                <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:border-orange-300 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="rounded bg-orange-50 border border-orange-200 px-2 py-0.5 text-[10px] font-black uppercase text-orange-700">
                                {{ $meeting->meeting_number }}º Encontro
                            </span>
                            <span class="text-xs font-medium text-gray-500">
                                {{ $meeting->date->format('d/m/Y') }}
                            </span>
                        </div>

                        <h3 class="text-xs sm:text-sm font-bold text-gray-900 leading-tight">{{ $meeting->topic ?? 'Encontro de Classe' }}</h3>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <i class="bi bi-people text-gray-400"></i>
                            <span class="font-bold text-gray-800">{{ $meeting->attendances->count() }}</span> presentes
                        </p>
                    </div>

                    @if(!$isPastorZona)
                        <div class="pt-3 mt-3 border-t border-gray-100 flex justify-end">
                            <a href="{{ route('course-classes.attendance', [$courseClass, $meeting]) }}"
                                class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-orange-700 hover:bg-orange-50 shadow-sm transition-colors">
                                <i class="bi bi-check2-square"></i> Aula & Chamada
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-200 p-8 text-center bg-white">
                    <i class="bi bi-calendar-x text-2xl text-gray-300"></i>
                    <p class="text-xs text-gray-500 mt-2 font-medium">Nenhum encontro agendado ainda.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal: Adicionar Inscrito -->
    <div id="enrollmentModal" class="hidden fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('enrollmentModal').classList.add('hidden')"></div>

            <div class="relative w-full max-w-lg rounded-xl bg-white p-5 text-left shadow-xl border border-gray-200 z-10">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-sm font-bold text-gray-900">Matricular Aluno na Turma</h3>
                    <button type="button" onclick="document.getElementById('enrollmentModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>

                <form action="{{ route('course-classes.add-enrollment', $courseClass) }}" method="POST" class="space-y-3.5">
                    @csrf
                    @php
                        $isCoupleClass = $courseClass->coupleEnrollments->count() > 0
                            || (isset($publicCoupleEnrollments) && $publicCoupleEnrollments->count() > 0)
                            || $courseClass->courseEnrollments->whereNotNull('male_partner_id')->whereNotNull('female_partner_id')->count() > 0;
                    @endphp

                    @if($isCoupleClass)
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Parceiro Masculino</label>
                            <select name="male_partner_id" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Selecione...</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Parceira Feminina</label>
                            <select name="female_partner_id" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Selecione...</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Data do Casamento</label>
                                <input type="date" name="wedding_date" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Membro da Igreja?</label>
                                <select name="is_church_member" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>
                    @else
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Aluno</label>
                            <select name="user_id" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Selecione o aluno...</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Membro da Igreja?</label>
                            <select name="is_church_member" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" onclick="document.getElementById('enrollmentModal').classList.add('hidden')"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-orange-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700">
                            Matricular
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Novo Encontro -->
    <div id="meetingModal" class="hidden fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('meetingModal').classList.add('hidden')"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-5 text-left shadow-xl border border-gray-200 z-10">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-sm font-bold text-gray-900">Agendar Novo Encontro</h3>
                    <button type="button" onclick="document.getElementById('meetingModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>

                <form action="{{ route('course-classes.meetings.store', $courseClass) }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nº do Encontro</label>
                            <input type="number" name="meeting_number" required value="{{ $courseClass->meetings->count() + 1 }}"
                                class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Data</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tema / Tópico</label>
                        <input type="text" name="topic" placeholder="Ex: Finanças no Casamento"
                            class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" onclick="document.getElementById('meetingModal').classList.add('hidden')"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-orange-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700">
                            Agendar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
