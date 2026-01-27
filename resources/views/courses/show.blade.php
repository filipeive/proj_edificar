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
        <div x-data="{ view: 'list' }" class="container-fluid">
            <div class="mb-6 flex justify-between items-center hidden md:flex">
                <a href="{{ route('courses.index') }}" class="text-gray-600 hover:text-orange-600 flex items-center transition font-semibold">
                    <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
                </a>
            </div>
        
        <div class="md:hidden mb-6">
            <a href="{{ route('courses.index') }}" class="text-gray-600 hover:text-orange-600 flex items-center transition font-semibold">
                <i class="bi bi-arrow-left mr-2"></i> Voltar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Info do Curso -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
                    <div class="h-3 bg-orange-500"></div>
                    <div class="p-8">
                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-[10px] font-bold uppercase rounded-full tracking-widest mb-4 inline-block">
                            {{ $course->category ?? 'Geral' }}
                        </span>
                        <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6">{{ $course->name }}</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Duração</h4>
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center">
                                    <i class="bi bi-clock mr-2 text-orange-500"></i> {{ $course->duration ?? 'Não informada' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Descrição</h4>
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                    {{ $course->description ?? 'Sem descrição disponível.' }}
                                </p>
                            </div>
                            <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $course->enrollments->count() }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase">Alunos</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-green-600 dark:text-green-400">{{ $course->enrollments->where('status', 'completed')->count() }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase">Formados</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-orange-500 dark:text-orange-400">{{ $course->enrollments->where('status', 'enrolled')->count() }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase">Ativos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Alunos -->
            @if(!auth()->user()->isSupervisor() || auth()->user()->isEnrolledInCourse($course->id))
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
                    <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/10 dark:bg-gray-700/10">
                        <h4 class="text-xl font-bold text-gray-800 dark:text-white">Alunos Matriculados</h4>
                        <div class="flex items-center gap-4">
                            <div class="flex bg-white dark:bg-gray-700 p-1 rounded-xl border border-gray-100 dark:border-gray-600">
                                <button @click="view = 'list'" 
                                    :class="view === 'list' ? 'bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                    class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                                <button @click="view = 'grid'" 
                                    :class="view === 'grid' ? 'bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200'"
                                    class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                                    <i class="bi bi-grid-fill"></i>
                                </button>
                            </div>
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                <div class="mt-8 space-y-3 hidden md:block">
                        <a href="{{ route('course-classes.report', $course) }}"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition block text-center shadow-lg shadow-blue-600/20">
                            <i class="bi bi-bar-chart-fill mr-2"></i> Ver Relatório Final
                        </a>
                        <a href="{{ route('course-classes.export', $course) }}"
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition block text-center shadow-lg shadow-green-600/20">
                            <i class="bi bi-file-earmark-excel mr-2"></i> Exportar para Excel
                        </a>
                        <a href="{{ route('course-classes.export-pdf', $course) }}"
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition block text-center shadow-lg shadow-red-600/20">
                            <i class="bi bi-file-earmark-pdf mr-2"></i> Exportar Relatório PDF
                        </a>
                    </div>
                                <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl flex items-center transition shadow-lg shadow-red-600/20 font-bold text-xs uppercase tracking-widest hidden">
                                    <i class="bi bi-trash-fill mr-2"></i> Remover Selecionados
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="px-8 py-4 bg-gray-50/50 dark:bg-gray-700/30 border-b border-gray-100 dark:border-gray-700">
                        <form action="{{ route('courses.show', $course) }}" method="GET" class="relative">
                            <input type="text" name="search" value="{{ $search ?? '' }}" 
                                placeholder="Pesquisar por nome ou e-mail..." 
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500 transition-all text-sm">
                            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                            @if($search)
                                <a href="{{ route('courses.show', $course) }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    <i class="bi bi-x-circle-fill"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                    <form id="bulkActionForm" action="{{ route('course-enrollments.bulk-destroy') }}" method="POST">
                        @csrf
                    </form>

                    <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                        <th class="px-8 py-4 w-10 text-center flex items-center justify-center pt-5">
                                            <input type="checkbox" id="selectAllCheckbox" 
                                                class="rounded-lg border-gray-300 dark:border-gray-600 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5 bg-white dark:bg-gray-700">
                                        </th>
                                    @endif
                                        <th class="px-8 py-4">Aluno</th>
                                        <th class="px-8 py-4">Data Matrícula</th>
                                        <th class="px-8 py-4">Status</th>
                                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                            <th class="px-8 py-4 text-right">Ações</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($enrollments as $enrollment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                                <td class="px-8 py-6">
                                                    <input type="checkbox" name="enrollment_ids[]" value="{{ $enrollment->id }}" form="bulkActionForm"
                                                        class="enrollment-checkbox rounded-lg border-gray-300 dark:border-gray-600 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5 bg-white dark:bg-gray-700">
                                                </td>
                                            @endif
                                            <td class="px-8 py-6">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-orange-600 dark:text-orange-400 font-bold mr-4">
                                                        @if($enrollment->user_id)
                                                            {{ substr($enrollment->user->name, 0, 1) }}
                                                        @else
                                                            <i class="bi bi-heart-fill text-red-500"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        @if($enrollment->user_id)
                                                            <p class="font-bold text-gray-900 dark:text-white">{{ $enrollment->user->name }}</p>
                                                            <p class="text-xs text-gray-400">{{ $enrollment->user->email }}</p>
                                                        @else
                                                            <p class="font-bold text-gray-900 dark:text-white">
                                                                {{ $enrollment->malePartner->name ?? 'N/A' }} & {{ $enrollment->femalePartner->name ?? 'N/A' }}
                                                            </p>
                                                            <p class="text-xs text-gray-400">Casal (Pré-Nupcial)</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-sm text-gray-600 dark:text-gray-400">
                                                {{ optional($enrollment->enrolled_at)->format('d/m/Y') ?? $enrollment->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-8 py-6">
                                                @if($enrollment->status === 'completed')
                                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-bold uppercase rounded-full">Concluído</span>
                                                @elseif($enrollment->status === 'dropped')
                                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-bold uppercase rounded-full">Desistiu</span>
                                                @else
                                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold uppercase rounded-full">Em Curso</span>
                                                @endif
                                            </td>
                                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                                <td class="px-8 py-6 text-right">
                                                    <div class="flex items-center justify-end space-x-2">
                                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                                            <button @click="open = !open" type="button" 
                                                                class="p-2 text-gray-400 hover:text-orange-600 transition-colors">
                                                                <i class="bi bi-arrow-repeat text-lg"></i>
                                                            </button>
                                                            
                                                            <div x-show="open" @click.away="open = false" 
                                                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20 overflow-hidden border border-gray-100 dark:border-gray-700">
                                                                <div class="py-1 text-left">
                                                                    @foreach(['enrolled' => 'Ativo', 'completed' => 'Concluído', 'dropped' => 'Desistir'] as $s => $label)
                                                                        <form action="{{ route('enrollments.status', $enrollment) }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="status" value="{{ $s }}">
                                                                            <button type="submit" 
                                                                                class="w-full text-left px-4 py-3 text-xs font-bold uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $enrollment->status == $s ? 'text-orange-600 dark:text-orange-400 bg-orange-50/50 dark:bg-orange-900/20' : 'text-gray-600 dark:text-gray-300' }}">
                                                                                {{ $label }}
                                                                            </button>
                                                                        </form>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <form action="{{ route('course-enrollments.destroy', $enrollment) }}" method="POST" id="delete-enrollment-{{ $enrollment->id }}" class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" 
                                                                onclick="confirmDelete('delete-enrollment-{{ $enrollment->id }}')"
                                                                class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                                                title="Excluir Matrícula">
                                                                <i class="bi bi-trash-fill text-lg"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-8 py-12 text-center text-gray-400 italic">
                                                Nenhum aluno matriculado neste curso.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/30 dark:bg-gray-700/30 transition-colors">
                        @forelse($enrollments as $enrollment)
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col group hover:shadow-md transition-all relative">
                                <div class="absolute top-6 right-6">
                                    @if($enrollment->status === 'completed')
                                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] font-bold uppercase rounded-full tracking-widest">Concluído</span>
                                    @elseif($enrollment->status === 'dropped')
                                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-[10px] font-bold uppercase rounded-full tracking-widest">Desistiu</span>
                                    @else
                                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] font-bold uppercase rounded-full tracking-widest">Em Curso</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center text-orange-600 dark:text-orange-400 font-bold group-hover:bg-orange-600 group-hover:text-white transition-all text-2xl">
                                        @if($enrollment->user_id)
                                            {{ substr($enrollment->user->name, 0, 1) }}
                                        @else
                                            <i class="bi bi-heart-fill"></i>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        @if($enrollment->user_id)
                                            <h4 class="text-sm font-black text-gray-900 dark:text-white">{{ $enrollment->user->name }}</h4>
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-tighter">{{ $enrollment->user->email }}</span>
                                        @else
                                            <h4 class="text-sm font-black text-gray-900 dark:text-white truncate max-w-[150px]">
                                                {{ $enrollment->malePartner->name ?? 'N/A' }} & {{ $enrollment->femalePartner->name ?? 'N/A' }}
                                            </h4>
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-tighter">Casal (Pré-Nupcial)</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-2xl mb-6 flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                                    <span class="text-gray-400 dark:text-gray-500">Matrícula</span>
                                    <span class="text-gray-900 dark:text-white">
                                        {{ optional($enrollment->enrolled_at)->format('d/m/Y') ?? $enrollment->created_at->format('d/m/Y') }}
                                    </span>
                                </div>

                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                    <div class="flex items-center gap-2 mt-auto">
                                        <div x-data="{ open: false }" class="flex-1 relative">
                                            <button @click="open = !open" type="button" 
                                                class="w-full bg-gray-900 dark:bg-gray-700 text-white text-center py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-orange-200">
                                                <i class="bi bi-arrow-repeat"></i> Alterar Status
                                            </button>
                                            
                                            <div x-show="open" @click.away="open = false" 
                                                class="origin-bottom-left absolute left-0 bottom-full mb-2 w-48 rounded-2xl shadow-2xl bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20 overflow-hidden border border-gray-100 dark:border-gray-700">
                                                <div class="py-1">
                                                    @foreach(['enrolled' => 'Ativo', 'completed' => 'Concluído', 'dropped' => 'Desistir'] as $s => $label)
                                                        <form action="{{ route('enrollments.status', $enrollment) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="{{ $s }}">
                                                            <button type="submit" 
                                                                class="w-full text-left px-4 py-3 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $enrollment->status == $s ? 'text-orange-600 dark:text-orange-400 bg-orange-50/50 dark:bg-orange-900/20' : 'text-gray-600 dark:text-gray-300' }}">
                                                                {{ $label }}
                                                            </button>
                                                        </form>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <form action="{{ route('course-enrollments.destroy', $enrollment) }}" method="POST" id="delete-enrollment-grid-{{ $enrollment->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                onclick="confirmDelete('delete-enrollment-grid-{{ $enrollment->id }}')"
                                                class="w-12 h-12 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center rounded-2xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-gray-400">
                                <p class="text-xs font-bold uppercase tracking-widest italic">Nenhum aluno matriculado.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
    <script>
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
                bulkBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'hidden');
                bulkBtn.innerHTML = `<i class="bi bi-trash-fill mr-2"></i> Remover ${count} Matrícula(s)`;
            } else {
                bulkBtn.disabled = true;
                bulkBtn.classList.add('opacity-50', 'cursor-not-allowed', 'hidden');
            }
        }

        function bulkDelete() {
            confirmAction(
                'Confirmação de Remoção em Massa',
                'Você tem certeza que deseja remover as matrículas selecionadas? Esta ação não pode ser desfeita.',
                'warning',
                'Sim, remover!',
                null
            ).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('bulkActionForm');
                    form.action = "{{ route('course-enrollments.bulk-destroy') }}";
                    form.submit();
                }
            });
        }
    </script>
    @endif
@endsection
