@extends('layouts.app')

@section('title', $courseClass->name . ' - Portal Life Church')
@section('page-title', $courseClass->name)
@section('page-subtitle', $courseClass->course->name)

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('course-classes.report', $courseClass) }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100"
            title="Relatório Final">
            <i class="bi bi-bar-chart-fill text-2xl"></i>
        </a>
        <a href="{{ route('course-classes.export-pdf', $courseClass) }}"
            class="text-gray-600 hover:text-red-600 p-2.5 hover:bg-red-50 rounded-xl transition-all duration-300 border border-transparent hover:border-red-100"
            title="Relatório PDF">
            <i class="bi bi-file-earmark-pdf text-2xl"></i>
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
            <a href="{{ route('course-classes.edit', $courseClass) }}"
                class="text-gray-600 hover:text-orange-600 p-2.5 hover:bg-orange-50 rounded-xl transition-all duration-300 border border-transparent hover:border-orange-100"
                title="Editar Turma">
                <i class="bi bi-pencil-square text-2xl"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="mb-8">
            <a href="{{ route('course-classes.index', ['course_id' => $courseClass->course_id]) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-500 hover:text-blue-600 rounded-xl border border-gray-100 shadow-sm transition-all group">
                <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                <span class="font-bold text-xs uppercase tracking-widest">Painel de Turmas</span>
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
                <i class="bi bi-check-circle-fill mr-3 text-2xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Esquerda: Detalhes e Líderes -->
            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="text-lg font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-info-circle text-blue-600 mr-2"></i> Detalhes da Turma
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status</p>
                            @php
                                $statusClasses = [
                                    'em_andamento' => 'bg-green-100 text-green-800',
                                    'concluida' => 'bg-blue-100 text-blue-800',
                                    'cancelada' => 'bg-red-100 text-red-800',
                                    'active' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
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
                                class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClasses[$courseClass->status] ?? 'bg-gray-100' }}">
                                {{ $statusLabels[$courseClass->status] ?? $courseClass->status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Período</p>
                            <p class="text-sm font-bold text-gray-700">
                                {{ $courseClass->start_date ? $courseClass->start_date->format('d/m/Y') : 'N/A' }} -
                                {{ $courseClass->end_date ? $courseClass->end_date->format('d/m/Y') : 'N/A' }}
                            </p>
                        </div>
                        <hr class="border-gray-100">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Professores</p>
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="text-sm font-bold text-gray-700">{{ $courseClass->teacherMale->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-pink-50 rounded-full flex items-center justify-center text-pink-600">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="text-sm font-bold text-gray-700">{{ $courseClass->teacherFemale->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @if($courseClass->assistantMale || $courseClass->assistantFemale)
                        <div class="mt-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Auxiliares</p>
                            @if($courseClass->assistantMale)
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-8 h-8 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 text-xs">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-600">{{ $courseClass->assistantMale->name }}</span>
                            </div>
                            @endif
                            @if($courseClass->assistantFemale)
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 text-xs">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-600">{{ $courseClass->assistantFemale->name }}</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="mt-8 space-y-3">
                        <a href="{{ route('course-classes.report', $courseClass) }}"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition block text-center shadow-lg shadow-blue-600/20">
                            <i class="bi bi-bar-chart-fill mr-2"></i> Ver Relatório Final
                        </a>
                        <a href="{{ route('course-classes.export', $courseClass) }}"
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition block text-center shadow-lg shadow-green-600/20">
                            <i class="bi bi-file-earmark-excel mr-2"></i> Exportar para Excel
                        </a>
                        <a href="{{ route('course-classes.export-pdf', $courseClass) }}"
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition block text-center shadow-lg shadow-red-600/20">
                            <i class="bi bi-file-earmark-pdf mr-2"></i> Exportar Relatório PDF
                        </a>
                        @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                        <a href="{{ route('course-classes.edit', $courseClass) }}"
                            class="w-full bg-gray-50 text-gray-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-100 transition block text-center border border-gray-100">
                            Editar Turma
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Próximos Encontros -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-black text-gray-900 flex items-center">
                            <i class="bi bi-calendar-check text-orange-600 mr-2"></i> Encontros
                        </h4>
                        @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                        <button type="button" onclick="document.getElementById('meetingModal').classList.remove('hidden')"
                            class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">
                            + Novo
                        </button>
                        @endif
                    </div>
                    <div class="space-y-4">
                        @forelse($courseClass->meetings as $meeting)
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-transparent hover:border-orange-100 transition group">
                                <div class="flex items-center space-x-3">
                                    <div class="text-center">
                                        <span
                                            class="block text-xs font-black text-gray-400">{{ $meeting->meeting_number }}º</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $meeting->topic ?? 'Encontro' }}</p>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                            {{ $meeting->date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('course-classes.attendance', [$courseClass, $meeting]) }}"
                                    class="bg-white text-orange-600 p-2 rounded-lg shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-orange-600 hover:text-white">
                                    <i class="bi bi-card-checklist"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-4 text-sm italic">Nenhum encontro agendado.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: Lista de Inscritos -->
            <div class="lg:col-span-2 space-y-8">
                @if(!auth()->user()->isSupervisor() || auth()->user()->isEnrolledInClass($courseClass->id))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h4 class="text-lg font-black text-gray-900 flex items-center">
                            <i class="bi bi-people-fill text-blue-600 mr-2"></i> Alunos e Casais Inscritos
                        </h4>
                        <div class="flex items-center gap-3">
                            <span class="hidden md:inline text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $courseClass->courseEnrollments->count() }} Inscritos</span>
                            @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                            <button type="button"
                                onclick="document.getElementById('enrollmentModal').classList.remove('hidden')"
                                class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 flex items-center gap-2">
                                <i class="bi bi-plus-lg"></i>
                                <span class="hidden sm:inline">Adicionar</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Mobile List (Hidden on MD up) -->
                    <div class="md:hidden divide-y divide-gray-50">
                        @forelse($courseClass->courseEnrollments as $enrollment)
                            <div class="p-6 space-y-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        @if($enrollment->malePartner && $enrollment->femalePartner)
                                            <div class="font-black text-gray-900 leading-tight">
                                                {{ $enrollment->malePartner->name }} & {{ $enrollment->femalePartner->name }}
                                            </div>
                                            <div class="text-[9px] text-blue-600 font-black uppercase tracking-widest mt-1">CASAL</div>
                                        @else
                                            <div class="font-black text-gray-900 leading-tight">{{ $enrollment->user->name ?? 'N/A' }}</div>
                                            <div class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-1">INDIVIDUAL</div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest {{ $enrollStatusClasses[$enrollment->status] ?? 'bg-gray-100' }}">
                                            {{ $enrollment->status }}
                                        </span>
                                        <div class="flex items-center gap-1 text-[10px] text-gray-400 font-black">
                                            <i class="bi bi-x-circle"></i>
                                            <span>{{ $enrollment->absence_count }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('course-enrollments.show', $enrollment) }}" 
                                        class="flex-1 bg-gray-50 text-gray-500 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest text-center border border-gray-100">
                                        Detalhes
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                                    <a href="{{ route('course-enrollments.edit', $enrollment) }}" 
                                        class="w-11 h-11 bg-orange-50 text-orange-600 flex items-center justify-center rounded-xl border border-orange-100">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center text-gray-400 italic text-xs">
                                Nenhum inscrito nesta turma.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Nome / Casal</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Faltas</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                        Ações</th>
                                </tr>
                            </thead>
                             <tbody class="divide-y divide-gray-100">
                                @forelse($courseClass->courseEnrollments as $enrollment)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            @if($enrollment->malePartner && $enrollment->femalePartner)
                                                <div class="font-bold text-gray-900">
                                                    {{ $enrollment->malePartner->name }} & {{ $enrollment->femalePartner->name }}
                                                </div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">CASAL</div>
                                            @else
                                                <div class="font-bold text-gray-900">{{ $enrollment->user->name ?? 'N/A' }}</div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">INDIVIDUAL</div>
                                            @endif
                                            
                                            @if($courseClass->type == 'pre_nupcial' && $enrollment->wedding_date)
                                                <div class="text-[10px] text-blue-600 font-bold">
                                                    Casamento: {{ $enrollment->wedding_date->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-bold {{ $enrollment->absence_count > 2 ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $enrollment->absence_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $enrollStatusClasses = [
                                                    'cursando' => 'bg-blue-100 text-blue-800',
                                                    'aprovado' => 'bg-green-100 text-green-800',
                                                    'reprovado' => 'bg-red-100 text-red-800',
                                                    'desistente' => 'bg-gray-100 text-gray-800',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-[8px] font-black uppercase tracking-widest {{ $enrollStatusClasses[$enrollment->status] ?? 'bg-gray-100' }}">
                                                {{ $enrollment->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                                                <!-- Status Dropdown -->
                                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                                    <button @click="open = !open" type="button" 
                                                        class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                                        <i class="bi bi-arrow-repeat text-lg"></i>
                                                    </button>
                                                    
                                                    <div x-show="open" @click.away="open = false" 
                                                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-20 overflow-hidden border border-gray-100">
                                                        <div class="py-1">
                                                            @foreach(['cursando', 'aprovado', 'reprovado', 'desistente'] as $status)
                                                                <form action="{{ route('enrollments.status', $enrollment) }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="{{ $status }}">
                                                                    <button type="submit" 
                                                                        class="w-full text-left px-4 py-3 text-xs font-bold uppercase tracking-widest hover:bg-gray-50 transition-colors {{ $enrollment->status == $status ? 'text-blue-600 bg-blue-50/50' : 'text-gray-600' }}">
                                                                        {{ ucfirst($status) }}
                                                                    </button>
                                                                </form>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <a href="{{ route('course-enrollments.show', $enrollment) }}" 
                                                    class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Ver Detalhes">
                                                    <i class="bi bi-eye-fill text-lg"></i>
                                                </a>
                                                
                                                @if(auth()->user()->isAdmin() || auth()->user()->isPastorSenior())
                                                <a href="{{ route('course-enrollments.edit', $enrollment) }}" 
                                                    class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Editar Matrícula">
                                                    <i class="bi bi-pencil-fill text-lg"></i>
                                                </a>

                                                <form action="{{ route('course-classes.remove-enrollment', $courseClass) }}"
                                                    method="POST" id="remove-enrollment-{{ $enrollment->id }}" class="inline-block">
                                                    @csrf
                                                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                                                    <button type="button" 
                                                        onclick="confirmAction('Remover da Turma?', 'Esta ação irá excluir a matrícula deste aluno/casal desta turma.', 'warning', 'Sim, remover!', 'remove-enrollment-{{ $enrollment->id }}')"
                                                        class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                                        title="Remover da Turma">
                                                        <i class="bi bi-trash-fill text-lg"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                                            Nenhum inscrito nesta turma.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
                @endif
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