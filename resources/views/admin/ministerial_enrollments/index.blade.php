@extends('layouts.app')

@section('title', 'Inscrições Ministeriais Públicas')
@section('page-title', 'Inscrições Ministeriais')
@section('page-subtitle', 'Gestão de novos inscritos individuais via formulário público')

@section('header-actions')
    <a href="{{ route('ministerial-enrollments.export-pdf', request()->all()) }}"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-rose-600 hover:bg-gray-50 transition-colors" title="Exportar PDF">
        <i class="bi bi-file-earmark-pdf text-sm"></i>
    </a>
@endsection

@section('content')
<!-- Print Header -->
<div class="hidden print:block mb-6 text-center border-b pb-4">
    <h1 class="text-xl font-bold uppercase">Comunidade de Vida Cristã - Life Church</h1>
    <p class="text-xs font-semibold text-gray-600 uppercase">Relatório de Inscrições Ministeriais Públicas</p>
    <p class="text-[10px] text-gray-500 mt-1">Gerado em {{ date('d/m/Y H:i') }}</p>
</div>

<div class="space-y-5">
    <!-- Header Actions Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Módulo Ministerial</span>
                <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-800">
                    {{ $enrollments->total() }} inscritos
                </span>
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">Inscrições Individuais / Ministeriais</h1>
            <p class="text-xs text-gray-500 mt-0.5">Gerencie os inscritos recebidos pelos formulários públicos do portal.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('ministerial-enrollments.export-pdf', request()->all()) }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-rose-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-file-earmark-pdf"></i>
                <span>Exportar PDF</span>
            </a>
            <button onclick="window.print()"
                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-black transition">
                <i class="bi bi-printer"></i>
                <span>Imprimir</span>
            </button>
        </div>
    </div>

    <!-- Stats KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 print:hidden">
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Total Inscritos</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 mt-0.5">{{ $stats['total'] ?? $enrollments->total() }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Individuais</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-700">
                <i class="bi bi-person-lines-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Pendentes</p>
                <h3 class="text-xl sm:text-2xl font-black text-amber-700 mt-0.5">{{ $stats['pending'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Sem turma</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-amber-50 text-amber-700">
                <i class="bi bi-hourglass-split text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Alocados</p>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-700 mt-0.5">{{ $stats['assigned'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Em turmas</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700">
                <i class="bi bi-check-circle-fill text-lg"></i>
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-500">Membros Célula</p>
                <h3 class="text-xl sm:text-2xl font-black text-purple-700 mt-0.5">{{ $stats['members'] ?? 0 }}</h3>
                <p class="text-[10px] text-gray-400 font-medium">Em célula</p>
            </div>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-purple-50 text-purple-700">
                <i class="bi bi-house-heart-fill text-lg"></i>
            </span>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm print:hidden">
        <form action="{{ route('ministerial-enrollments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nome, email ou telefone..."
                    class="w-full rounded-lg border-gray-300 pl-8 pr-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div>
                <select name="course_id" onchange="this.form.submit()"
                    class="w-full rounded-lg border-gray-300 px-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    <option value="">Todos os Cursos</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-gray-900 px-4 py-1.5 text-xs font-bold text-white hover:bg-black transition">
                    Filtrar
                </button>
                @if(request()->anyFilled(['search', 'course_id']))
                    <a href="{{ route('ministerial-enrollments.index') }}" class="rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition" title="Limpar">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/75 text-[10px] font-black uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3">Inscrito</th>
                        <th class="px-4 py-3">Curso</th>
                        <th class="px-4 py-3">Contato</th>
                        <th class="px-4 py-3">Turma Atual</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right print:hidden">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900 leading-tight">{{ $enrollment->full_name }}</div>
                                <div class="text-[10px] text-gray-400 font-medium mt-0.5">
                                    {{ $enrollment->is_church_member ? 'Membro' : 'Visitante' }} • {{ $enrollment->cell_name ?: 'Sem Célula' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-bold text-orange-800 border border-orange-100">
                                    {{ $enrollment->course->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <div class="font-medium text-gray-800">{{ $enrollment->phone }}</div>
                                <div class="text-[10px] text-gray-400">{{ $enrollment->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($enrollment->courseClass)
                                    <span class="inline-flex items-center gap-1.5 font-bold text-gray-900 text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $enrollment->courseClass->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                                        Aguardando Turma
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($enrollment->course_class_id)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                        Alocado
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right print:hidden">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('ministerial-enrollments.show', $enrollment) }}"
                                        class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-orange-700 transition" title="Ver Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('ministerial-enrollments.edit', $enrollment) }}"
                                        class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('ministerial-enrollments.destroy', $enrollment) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta inscrição?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-red-600 transition" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-xs text-gray-400">
                                Nenhuma inscrição individual encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
            <div class="p-3 border-t border-gray-100 bg-gray-50/50 print:hidden">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
