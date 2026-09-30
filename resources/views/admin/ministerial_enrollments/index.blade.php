@extends('layouts.app')

@section('title', 'Inscrições Ministeriais Públicas')
@section('page-title', 'Inscrições Ministeriais')
@section('page-subtitle', 'Gestão de novos inscritos individuais via formulário público')

@section('header-actions')
    <div class="flex flex-wrap items-center gap-1.5 print:hidden">
        <a href="{{ route('ministerial-enrollments.export-pdf', request()->all()) }}"
            class="bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 px-3 py-2 md:px-4 md:py-2.5 rounded-xl hover:bg-rose-100 transition-all flex items-center gap-1.5 text-xs font-black uppercase tracking-wider shadow-sm">
            <i class="bi bi-file-earmark-pdf-fill text-sm"></i>
            <span class="hidden md:inline">Exportar PDF</span>
        </a>

        <button onclick="window.print()"
            class="bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 px-3 py-2 md:px-4 md:py-2.5 rounded-xl hover:bg-black dark:hover:bg-white transition-all flex items-center gap-1.5 text-xs font-black uppercase tracking-wider shadow-sm">
            <i class="bi bi-printer-fill text-sm"></i>
            <span class="hidden md:inline">Imprimir Lista</span>
        </button>
    </div>
@endsection

@section('content')
    <!-- Print Header -->
    <div class="hidden print:block mb-8 text-center border-b pb-4">
        <h1 class="text-2xl font-bold uppercase tracking-wide">Comunidade de Vida Cristã - Life Church</h1>
        <p class="text-sm font-semibold text-gray-600 uppercase">Relatório de Inscrições Ministeriais Públicas</p>
        <p class="text-xs text-gray-500 mt-1">Gerado em {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="w-full space-y-6">
        <!-- Top Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:hidden">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Inscritos</p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['total'] ?? $enrollments->total() }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Inscrições individuais</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Pendentes</p>
                    <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $stats['pending'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Aguardando turma</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

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

            <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500">Membros Célula</p>
                    <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['members'] ?? 0 }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-0.5">Vinculados à célula</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-black">
                    <i class="bi bi-house-heart-fill"></i>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 print:hidden">
            <form action="{{ route('ministerial-enrollments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nome, email ou telefone..."
                        class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-bold text-xs text-gray-700 dark:text-gray-200">
                </div>

                <div class="relative">
                    <select name="course_id" onchange="this.form.submit()"
                        class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-700 dark:text-gray-200">
                        <option value="">Todos os Cursos</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black dark:hover:bg-white transition-all shadow-sm">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['search', 'course_id']))
                        <a href="{{ route('ministerial-enrollments.index') }}" class="px-4 py-3 bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 rounded-xl hover:bg-red-100 transition-all font-bold text-xs" title="Limpar">
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
                    <h4 class="text-sm font-black text-gray-900 dark:text-white">Lista de Inscritos Individuais</h4>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                    Página {{ $enrollments->currentPage() }} de {{ $enrollments->lastPage() }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Inscrito</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Curso</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Contato</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Turma Atual</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right print:hidden">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center font-black">
                                            {{ substr($enrollment->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 dark:text-white leading-tight">
                                                {{ $enrollment->full_name }}
                                            </p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                                {{ $enrollment->is_church_member ? 'Membro' : 'Visitante' }} • {{ $enrollment->cell_name ?: 'Sem Célula' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $enrollment->course->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $enrollment->phone }}</p>
                                    <p class="text-[9px] font-medium text-gray-400 lowercase">{{ $enrollment->email }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    @if($enrollment->courseClass)
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase">{{ $enrollment->courseClass->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-1 rounded-md border border-amber-200 uppercase tracking-wider">Aguardando Turma</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @if($enrollment->course_class_id)
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[9px] font-black uppercase rounded-full border border-emerald-200 tracking-widest shadow-sm">Alocado</span>
                                    @else
                                        <span class="px-3 py-1 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 text-[9px] font-black uppercase rounded-full border border-amber-200 tracking-widest shadow-sm">Pendente</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right print:hidden">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('ministerial-enrollments.show', $enrollment) }}"
                                            class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-purple-600 hover:border-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all flex items-center justify-center" title="Ver Detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('ministerial-enrollments.edit', $enrollment) }}"
                                            class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all flex items-center justify-center" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('ministerial-enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta inscrição?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all flex items-center justify-center" title="Excluir">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700 rounded-3xl flex items-center justify-center text-gray-300 mx-auto mb-4">
                                        <i class="bi bi-person-x text-3xl"></i>
                                    </div>
                                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Nenhuma inscrição encontrada</p>
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
