@extends('layouts.app')

@section('title', 'Detalhes da Matrícula')
@section('page-title', 'Detalhes da Matrícula')
@section('page-subtitle', $enrollment->course->name)

@section('header-actions')
    <div class="flex items-center gap-2">
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
            <a href="{{ route('course-enrollments.edit', $enrollment) }}"
                class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="w-full space-y-8">
        <!-- Breadcrumbs & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('courses.index') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-orange-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                            <i class="bi bi-journal-bookmark-fill mr-2"></i>
                            Cursos
                        </a>
                    </li>
                    @if($enrollment->course_class_id)
                        <li>
                            <div class="flex items-center">
                                <i class="bi bi-chevron-right text-gray-400 text-xs mx-2"></i>
                                <a href="{{ route('course-classes.show', $enrollment->course_class_id) }}"
                                    class="text-sm font-medium text-gray-500 hover:text-orange-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                                    {{ $enrollment->courseClass->name }}
                                </a>
                            </div>
                        </li>
                    @endif
                    <li>
                        <div class="flex items-center">
                            <i class="bi bi-chevron-right text-gray-400 text-xs mx-2"></i>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Matrícula
                                #{{ $enrollment->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Primary Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Enrollment Hero Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        @php
                            $statusStyles = [
                                'cursando' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'aprovado' => 'bg-green-100 text-green-700 border-green-200',
                                'reprovado' => 'bg-red-100 text-red-700 border-red-200',
                                'desistente' => 'bg-gray-100 text-gray-700 border-gray-200',
                                'default' => 'bg-gray-100 text-gray-700 border-gray-200'
                            ];
                            $style = $statusStyles[$enrollment->status] ?? $statusStyles['default'];
                        @endphp
                        <span
                            class="px-4 py-1.5 {{ $style }} text-xs font-black uppercase rounded-full border tracking-widest shadow-sm">
                            {{ $enrollment->status === 'cursando' ? 'Em Curso' : ucfirst($enrollment->status) }}
                        </span>
                    </div>

                    <div class="flex flex-col md:flex-row gap-8 items-start relative z-10">
                        <div
                            class="w-24 h-24 rounded-3xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white text-4xl shadow-xl shadow-orange-100 dark:shadow-none font-black shrink-0">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-none mb-2">
                                {{ $enrollment->malePartner->name ?? $enrollment->user->name ?? 'N/A' }}
                                <span class="text-gray-300 dark:text-gray-600 font-light mx-2">&</span>
                                {{ $enrollment->femalePartner->name ?? 'N/A' }}
                            </h1>
                            <p class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-xs mb-6">
                                Matrícula do Casal • {{ $enrollment->course->name }}</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div
                                    class="flex items-center gap-3 p-3 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100/50 dark:border-blue-800/20">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-300 text-xs font-black">
                                        M</div>
                                    <div class="truncate">
                                        <p
                                            class="text-[8px] font-black text-blue-400 uppercase tracking-widest leading-none">
                                            Cônjuge Masc.</p>
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate">
                                            {{ $enrollment->malePartner->name ?? $enrollment->user->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center gap-3 p-3 bg-pink-50/50 dark:bg-pink-900/10 rounded-2xl border border-pink-100/50 dark:border-pink-800/20">
                                    <div
                                        class="w-8 h-8 rounded-full bg-pink-100 dark:bg-pink-800 flex items-center justify-center text-pink-600 dark:text-pink-300 text-xs font-black">
                                        F</div>
                                    <div class="truncate">
                                        <p
                                            class="text-[8px] font-black text-pink-400 uppercase tracking-widest leading-none">
                                            Cônjuge Fem.</p>
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate">
                                            {{ $enrollment->femalePartner->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div x-data="{ activeTab: 'details' }" class="space-y-6">
                    <div class="flex items-center gap-2 p-1.5 bg-gray-100/50 dark:bg-gray-800/50 rounded-2xl w-fit">
                        <button @click="activeTab = 'details'"
                            :class="activeTab === 'details' ? 'bg-white dark:bg-gray-700 text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200">
                            Detalhes
                        </button>
                        <button @click="activeTab = 'attendance'"
                            :class="activeTab === 'attendance' ? 'bg-white dark:bg-gray-700 text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200">
                            Frequência
                        </button>
                    </div>

                    <!-- Details Tab -->
                    <div x-show="activeTab === 'details'" x-transition class="space-y-8">
                        <!-- Marriage Info Card -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h4
                                class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-gray-700 pb-4 mb-6 flex items-center justify-between">
                                Informações de Casamento
                                <i class="bi bi-calendar-heart text-lg text-orange-500 opacity-30"></i>
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Data do
                                        Casamento</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white">
                                        {{ $enrollment->wedding_date ? $enrollment->wedding_date->format('d/m/Y') : 'Não informada' }}
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Data do
                                        Noivado</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white">
                                        {{ $enrollment->engagement_date ? $enrollment->engagement_date->format('d/m/Y') : 'Não informada' }}
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">São Membros?
                                    </p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white">
                                        <span class="inline-flex items-center gap-2">
                                            <i
                                                class="bi {{ $enrollment->is_church_member ? 'bi-check-circle-fill text-green-500' : 'bi-x-circle-fill text-gray-300' }}"></i>
                                            {{ $enrollment->is_church_member ? 'Sim' : 'Não' }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div
                                class="mt-8 pt-8 border-t border-gray-50 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Padrinhos
                                        (Ele)</p>
                                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ $enrollment->godparents_male ?? 'Não informado' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Padrinhos
                                        (Ela)</p>
                                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ $enrollment->godparents_female ?? 'Não informado' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Evaluations & Notes Card -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                            <h4
                                class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-gray-700 pb-4 mb-6 flex items-center justify-between">
                                Avaliação e Notas Internas
                                <i class="bi bi-clipboard-check text-lg text-blue-500 opacity-30"></i>
                            </h4>
                            <div class="space-y-8">
                                <div class="space-y-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Recomendação
                                        Final</p>
                                    <div
                                        class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-gray-100 dark:border-gray-700">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed italic">
                                            {{ $enrollment->recommendation ?? 'Nenhuma recomendação registrada.' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Observações
                                        Detalhadas</p>
                                    <div
                                        class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-gray-100 dark:border-gray-700">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                            {{ $enrollment->notes ?? 'Sem observações adicionais.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Tab -->
                    <div x-show="activeTab === 'attendance'" x-transition
                        class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">Registros de Frequência
                            </h4>
                            <div class="flex gap-4">
                                <div class="text-center">
                                    <p class="text-lg font-black text-green-600">{{ $enrollment->attendance_count }}</p>
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Presenças</p>
                                </div>
                                <div class="text-center border-l border-gray-100 dark:border-gray-700 pl-4">
                                    <p class="text-lg font-black text-red-600">{{ $enrollment->absence_count }}</p>
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Faltas</p>
                                </div>
                            </div>
                        </div>

                        @if($enrollment->attendances->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                                            <th
                                                class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Encontro</th>
                                            <th
                                                class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Data</th>
                                            <th
                                                class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                        @foreach($enrollment->attendances as $attendance)
                                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                                <td class="px-8 py-6">
                                                    <p class="text-sm font-black text-gray-900 dark:text-white">
                                                        {{ $attendance->meeting->subject ?? 'Reunião' }}</p>
                                                </td>
                                                <td class="px-8 py-6 text-sm text-gray-500 font-bold">
                                                    {{ $attendance->meeting->date ? $attendance->meeting->date->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="px-8 py-6 text-center">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $attendance->status === 'present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                        <i
                                                            class="bi {{ $attendance->status === 'present' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                        {{ $attendance->status === 'present' ? 'Presente' : 'Ausente' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-50 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-gray-300 mx-auto mb-4">
                                    <i class="bi bi-calendar-x text-3xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-400">Nenhum registro de frequência encontrado.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Stats -->
            <div class="space-y-8">
                <!-- Class Info Card -->
                @if($enrollment->courseClass)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Turma Vinculada</h4>
                        <div
                            class="p-6 bg-gradient-to-br from-blue-600 to-blue-700 rounded-3xl text-white shadow-lg shadow-blue-100 dark:shadow-none mb-6 group relative overflow-hidden">
                            <i
                                class="bi bi-door-open absolute -right-4 -bottom-4 text-7xl text-white/10 group-hover:scale-110 transition-transform duration-500"></i>
                            <div class="relative z-10">
                                <h3 class="text-lg font-black leading-tight mb-1">{{ $enrollment->courseClass->name }}</h3>
                                <p class="text-blue-100 text-[10px] font-bold uppercase tracking-widest">
                                    {{ $enrollment->courseClass->course->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('course-classes.show', $enrollment->courseClass->id) }}"
                            class="flex items-center justify-between p-4 px-6 bg-gray-50 dark:bg-gray-700 hover:bg-orange-600 hover:text-white rounded-2xl transition-all duration-300 group">
                            <span class="text-xs font-black uppercase tracking-widest">Ver Turma</span>
                            <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                @else
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 rounded-[2.5rem] p-8 border border-orange-100 dark:border-orange-900/50 text-center">
                        <div
                            class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-orange-500 shadow-sm mx-auto mb-4">
                            <i class="bi bi-exclamation-triangle text-3xl"></i>
                        </div>
                        <h4 class="text-sm font-black text-orange-900 dark:text-orange-200 uppercase tracking-widest mb-2">Sem
                            Turma</h4>
                        <p class="text-xs text-orange-700 dark:text-orange-400">Este casal ainda não foi atribuído a uma turma
                            específica.</p>
                    </div>
                @endif

                <!-- Contacts Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Contato do Casal</h4>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">
                                    E-mail Principal</p>
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate">
                                    {{ $enrollment->user->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($enrollment->malePartner && $enrollment->malePartner->phone)
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                    <i class="bi bi-whatsapp"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">
                                        WhatsApp (Ele)</p>
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $enrollment->malePartner->phone }}</p>
                                </div>
                            </div>
                        @endif
                        @if($enrollment->femalePartner && $enrollment->femalePartner->phone)
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                    <i class="bi bi-whatsapp"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">
                                        WhatsApp (Ela)</p>
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $enrollment->femalePartner->phone }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection