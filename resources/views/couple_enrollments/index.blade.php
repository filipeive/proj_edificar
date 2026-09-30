@extends('layouts.app')

@section('title', 'Inscrições Públicas (Casais)')
@section('page-title', 'Inscrições Públicas (Casais)')
@section('page-subtitle', 'Gestão, alocação e relatórios de inscritos via formulário público')

@section('header-actions')
    <div class="md:hidden flex items-center gap-1">
        <a href="{{ route('couple-enrollments.export-pdf', request()->all()) }}"
            class="text-rose-600 hover:bg-rose-50 p-2 rounded-xl transition-all" title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf-fill text-2xl"></i>
        </a>
        <a href="{{ route('couple-enrollments.export', request()->all()) }}"
            class="text-emerald-600 hover:bg-emerald-50 p-2 rounded-xl transition-all" title="Exportar CSV">
            <i class="bi bi-file-earmark-spreadsheet-fill text-2xl"></i>
        </a>
    </div>
@endsection

@section('content')
    <!-- Print Header (Visible only when printing) -->
    <div class="hidden print:block mb-8 text-center border-b pb-4">
        <h1 class="text-2xl font-bold uppercase tracking-wide">Comunidade de Vida Cristã - Life Church</h1>
        <p class="text-sm font-semibold text-gray-600 uppercase">Relatório de Inscrições Públicas de Casais</p>
        <p class="text-xs text-gray-500 mt-1">Gerado em {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="w-full space-y-6">
        <!-- Header Actions & Title Card for Desktop -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-orange-500">Módulo de Casais</p>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Gestão de Inscrições de Casais</h2>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">Acompanhe novos inscritos, faça alocações em turmas e gere relatórios.</p>
            </div>
            
            <div class="hidden md:flex flex-wrap items-center gap-2">
                <a href="{{ route('couple-enrollments.export', request()->all()) }}"
                    class="bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 px-4 py-2.5 rounded-xl hover:bg-emerald-100 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-base"></i>
                    <span>Exportar CSV</span>
                </a>

                <a href="{{ route('couple-enrollments.export-pdf', request()->all()) }}"
                    class="bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 px-4 py-2.5 rounded-xl hover:bg-rose-100 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm">
                    <i class="bi bi-file-earmark-pdf-fill text-base"></i>
                    <span>Exportar PDF</span>
                </a>

                <button onclick="window.print()"
                    class="bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 px-4 py-2.5 rounded-xl hover:bg-black dark:hover:bg-white transition-all flex items-center gap-2 text-xs font-black uppercase tracking-wider shadow-sm">
                    <i class="bi bi-printer-fill text-base"></i>
                    <span>Imprimir Lista</span>
                </button>
            </div>
        </div>

        <!-- Top Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:hidden">
            <!-- Total -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Registados</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['total'] ?? $enrollments->total() }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Todas as inscrições</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>

            <!-- Pendentes -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Pendentes</p>
                    <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $stats['pending'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Aguardando alocação</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

            <!-- Alocados -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Alocados</p>
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['assigned'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Em turmas ativas</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>

            <!-- Cursos -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-500">Cursos Ativos</p>
                    <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $stats['courses'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Com inscrições</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
            </div>
        </div>

        <!-- Quick Access Banner -->
        <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600 dark:from-orange-600 dark:to-amber-700 rounded-[2rem] p-6 text-white shadow-lg shadow-orange-500/10 print:hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-1">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest text-white">
                        Gestão de Ensino
                    </span>
                    <h3 class="text-xl font-black tracking-tight">Atalhos de Cursos e Turmas</h3>
                    <p class="text-xs font-medium text-orange-100">Gerencie a estrutura de cursos e organize as turmas para alocação de casais.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('courses.index') }}"
                        class="px-4 py-2.5 bg-white text-orange-600 hover:bg-orange-50 rounded-xl transition-all text-xs font-black uppercase tracking-wider shadow-sm flex items-center gap-2">
                        <i class="bi bi-journal-check"></i>
                        <span>Cursos</span>
                    </a>
                    <a href="{{ route('course-classes.index') }}"
                        class="px-4 py-2.5 bg-black/20 hover:bg-black/30 backdrop-blur-md text-white border border-white/20 rounded-xl transition-all text-xs font-black uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span>Turmas</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 print:hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Filtros Avançados</p>
                    <h4 class="text-sm font-black text-gray-900 dark:text-white">Filtrar inscrições por curso, status ou termo</h4>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 text-[10px] font-black uppercase tracking-widest border border-orange-100 dark:border-orange-800/40">
                    {{ $enrollments->total() }} registos encontrados
                </span>
            </div>
            <form action="{{ route('couple-enrollments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nome, telefone ou contacto..."
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-bold text-xs text-gray-700 dark:text-gray-200 placeholder-gray-400">
                </div>

                <div class="relative">
                    <select name="course_id" onchange="this.form.submit()" data-searchable="false"
                        class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-700 dark:text-gray-200">
                        <option value="">Todos os Cursos</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <select name="status" onchange="this.form.submit()" data-searchable="false"
                        class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-700 dark:text-gray-200">
                        <option value="">Todos os Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente (Sem Turma)</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Alocado (Com Turma)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black dark:hover:bg-white transition-all shadow-sm">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['search', 'course_id', 'status']))
                        <a href="{{ route('couple-enrollments.index') }}" class="px-4 py-3 bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 rounded-xl hover:bg-red-100 transition-all font-bold text-xs" title="Limpar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 print:hidden">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inscrições</p>
                    <h4 class="text-sm font-black text-gray-900 dark:text-white">Lista de Casais Inscritos</h4>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                    Página {{ $enrollments->currentPage() }} de {{ $enrollments->lastPage() }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Casal / Nomes</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Curso Pretendido</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Relação & Tempo</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Contatos / Endereço</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Turma Alocada</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right print:hidden">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 text-white flex items-center justify-center font-black shadow-md shadow-orange-500/20 flex-shrink-0">
                                            {{ substr($enrollment->husband_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 dark:text-white leading-snug">
                                                {{ $enrollment->husband_name }}
                                            </p>
                                            <p class="text-xs font-bold text-gray-600 dark:text-gray-300">
                                                & {{ $enrollment->wife_name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-xs font-bold border border-orange-100 dark:border-orange-800/40">
                                        {{ $enrollment->course->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase">
                                        {{ ucfirst($enrollment->relationship_type) }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        {{ $enrollment->years_together }} anos juntos
                                    </p>
                                </td>
                                <td class="px-6 py-5 space-y-1">
                                    @if($enrollment->husband_phone)
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                            <i class="bi bi-person text-blue-500"></i>
                                            <span>{{ $enrollment->husband_phone }}</span>
                                        </p>
                                    @endif
                                    @if($enrollment->wife_phone)
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                            <i class="bi bi-person-heart text-pink-500"></i>
                                            <span>{{ $enrollment->wife_phone }}</span>
                                        </p>
                                    @endif
                                    @if(!$enrollment->husband_phone && !$enrollment->wife_phone && $enrollment->contacts)
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                            <i class="bi bi-telephone text-gray-400"></i>
                                            <span>{{ $enrollment->contacts }}</span>
                                        </p>
                                    @endif
                                    <p class="text-[10px] font-medium text-gray-400 flex items-center gap-1 truncate max-w-[200px]" title="{{ $enrollment->address }}">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>{{ $enrollment->address }}</span>
                                    </p>
                                </td>
                                <td class="px-6 py-5">
                                    @if($enrollment->courseClass)
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase">{{ $enrollment->courseClass->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-1 rounded-md border border-amber-200 dark:border-amber-800/40 uppercase tracking-wider">
                                            Aguardando Turma
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($enrollment->course_class_id)
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[9px] font-black uppercase rounded-full border border-emerald-200 dark:border-emerald-800 tracking-widest shadow-sm">
                                            Alocado
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 text-[9px] font-black uppercase rounded-full border border-amber-200 dark:border-amber-800 tracking-widest shadow-sm">
                                            Pendente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right print:hidden">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$enrollment->course_class_id)
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <button @click="open = !open" type="button" class="h-9 px-3 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-orange-700 transition-all shadow-md shadow-orange-600/20 flex items-center gap-1">
                                                    <i class="bi bi-person-plus-fill"></i>
                                                    <span>Alocar</span>
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" 
                                                     class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 p-4 text-left">
                                                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Selecionar Turma</h5>
                                                    <form action="{{ route('couple-enrollments.assign-class', $enrollment) }}" method="POST">
                                                        @csrf
                                                        <select name="course_class_id" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold mb-3 focus:ring-orange-500">
                                                            <option value="">Escolha uma turma...</option>
                                                            @if(is_object($classes) && method_exists($classes, 'where'))
                                                                @foreach($classes->where('course_id', $enrollment->course_id) as $class)
                                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700">Confirmar Alocação</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif

                                        <a href="{{ route('couple-enrollments.show', $enrollment) }}"
                                            class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-purple-600 hover:border-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all flex items-center justify-center"
                                            title="Ver Detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="{{ route('couple-enrollments.edit', $enrollment) }}"
                                            class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all flex items-center justify-center"
                                            title="Editar inscrição">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <form action="{{ route('couple-enrollments.destroy', $enrollment) }}" method="POST" 
                                              onsubmit="return confirm('Tem certeza que deseja remover esta inscrição?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all flex items-center justify-center"
                                                title="Remover inscrição">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700/50 rounded-3xl flex items-center justify-center text-gray-300 dark:text-gray-600 mx-auto mb-4">
                                        <i class="bi bi-people text-3xl"></i>
                                    </div>
                                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Nenhuma inscrição encontrada</p>
                                    <p class="text-xs text-gray-400 mt-1">Tente ajustar os filtros de busca no topo.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($enrollments->hasPages())
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-50 dark:border-gray-700 print:hidden">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
