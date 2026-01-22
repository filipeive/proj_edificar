@extends('layouts.app')

@section('title', 'Gestão de Turmas - Portal Life Church')
@section('page-title', 'Turmas dos Cursos')
@section('page-subtitle', 'Organização e acompanhamento de alunos e casais')

@section('content')
    <div x-data="{ view: 'list' }">
        @section('header-actions')
            <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
                <i class="bi bi-plus-circle text-xl"></i>
            </a>
        @endsection
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h3 class="text-2xl font-bold text-gray-800">Lista de Turmas</h3>
                <form action="{{ route('course-classes.index') }}" method="GET" class="flex items-center">
                    <select name="course_id" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos os Cursos</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="flex items-center space-x-3">
                <div class="hidden md:flex bg-gray-50 p-1 rounded-xl border border-gray-100 mr-2">
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                        <i class="bi bi-list-ul"></i>
                    </button>
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                        <i class="bi bi-grid-fill"></i>
                    </button>
                </div>
                
                <a href="{{ route('course-classes.export-all') }}"
                    class="bg-indigo-50 text-indigo-600 border border-indigo-100 hover:bg-indigo-100 px-4 py-2 rounded-lg flex items-center transition shadow-sm font-bold text-sm">
                    <i class="bi bi-file-earmark-spreadsheet mr-2"></i> <span class="hidden md:inline">Relatório Geral (Excel)</span><span class="md:hidden">Exportar</span>
                </a>
                <button type="button" id="exportBtn" onclick="exportSelected()" disabled
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md font-bold text-sm opacity-50 cursor-not-allowed hidden md:flex">
                    <i class="bi bi-check-all mr-2"></i> Exportar Selecionadas
                </button>
                @if(auth()->user()->role === 'admin')
                    <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md font-bold text-sm opacity-50 cursor-not-allowed hidden">
                        <i class="bi bi-trash-fill mr-2"></i> Excluir Selecionadas
                    </button>
                @endif
                <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}"
                    class="hidden md:flex bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md font-bold text-sm">
                    <i class="bi bi-plus-lg mr-2"></i> Nova Turma
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
                <i class="bi bi-check-circle-fill mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form id="exportForm" action="{{ route('course-classes.export-all') }}" method="GET">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" id="selectAllCheckbox"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Turma</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Curso</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Professores
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                                    Inscritos</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">
                                    Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($classes as $class)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="class_ids[]" value="{{ $class->id }}"
                                            class="class-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $class->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $class->start_date ? $class->start_date->format('d/m/Y') : 'N/A' }} -
                                            {{ $class->end_date ? $class->end_date->format('d/m/Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $class->course->name ?? 'Curso não definido' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $class->teacherMale->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-900">
                                            {{ $class->teacherFemale->name ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                                            {{ $class->enrollments_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'em_andamento' => 'bg-green-100 text-green-800',
                                                'concluida' => 'bg-blue-100 text-blue-800',
                                                'cancelada' => 'bg-red-100 text-red-800',
                                                'active' => 'bg-green-100 text-green-800', // Backward compatibility
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
                                            class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClasses[$class->status] ?? 'bg-gray-100' }}">
                                            {{ $statusLabels[$class->status] ?? $class->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('course-classes.show', $class) }}"
                                            class="text-blue-600 hover:text-blue-900 border border-transparent hover:border-blue-200 p-1 rounded transition-all"
                                            title="Ver Detalhes">
                                            <i class="bi bi-eye-fill text-lg"></i>
                                        </a>
                                        <a href="{{ route('course-classes.edit', $class) }}"
                                            class="text-amber-600 hover:text-amber-900 border border-transparent hover:border-amber-200 p-1 rounded transition-all"
                                            title="Editar">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </a>
                                        <form action="{{ route('course-classes.destroy', $class) }}" method="POST"
                                            id="delete-form-{{ $class->id }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-form-{{ $class->id }}', 'Deseja excluir permanentemente esta turma?')"
                                                class="text-red-600 hover:text-red-900 border border-transparent hover:border-red-200 p-1 rounded transition-all">
                                                <i class="bi bi-trash-fill text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                        <i class="bi bi-inbox text-4xl block mb-2"></i>
                                        Nenhuma turma encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            @if($classes->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $classes->links() }}
                </div>
            @endif
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($classes as $class)
                <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300 relative">
                    <div class="absolute top-6 right-6">
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
                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClasses[$class->status] ?? 'bg-gray-100' }}">
                            {{ $statusLabels[$class->status] ?? $class->status }}
                        </span>
                    </div>
                    
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-black text-2xl group-hover:bg-orange-600 group-hover:text-white transition-all duration-500 mb-6">
                        {{ strtoupper(substr($class->name, 0, 1)) }}
                    </div>

                    <div class="mb-4">
                        <h4 class="text-lg font-black text-gray-900 leading-tight mb-1 group-hover:text-orange-600 transition-colors uppercase tracking-widest">{{ $class->name }}</h4>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $class->course->name ?? 'Curso não definido' }}</span>
                    </div>

                    <div class="space-y-3 mb-6 flex-1 text-sm bg-gray-50 p-4 rounded-2xl">
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="bi bi-people-fill text-blue-500"></i>
                            <span class="font-bold">{{ $class->enrollments_count }} Alunos</span>
                        </div>
                        <div class="flex flex-col gap-1 mt-2">
                            <span class="text-[10px] font-black uppercase text-gray-400">Professores:</span>
                            <span class="text-xs font-bold text-gray-700">{{ $class->teacherMale->name ?? 'N/A' }}</span>
                            @if($class->teacherFemale)
                                <span class="text-xs font-bold text-gray-700">{{ $class->teacherFemale->name }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-4 border-t border-gray-50">
                        <a href="{{ route('course-classes.show', $class) }}" class="flex-1 bg-gray-900 text-white text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-eye"></i> Detalhes
                        </a>
                        <a href="{{ route('course-classes.edit', $class) }}" class="w-10 h-10 bg-gray-50 text-gray-400 flex items-center justify-center rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white rounded-[2rem] border border-dashed border-gray-200 flex flex-col items-center gap-4 text-gray-300">
                    <i class="bi bi-inbox text-7xl opacity-20"></i>
                    <p class="font-bold text-lg">Nenhuma turma encontrada</p>
                </div>
            @endforelse
        </div>
        @if($classes->hasPages())
            <div class="mt-8">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
    </div>

    <script>
        document.getElementById('selectAllCheckbox').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.class-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateActionButtons();
        });

        document.querySelectorAll('.class-checkbox').forEach(cb => {
            cb.addEventListener('change', updateActionButtons);
        });

        function updateActionButtons() {
            const selectedCount = document.querySelectorAll('.class-checkbox:checked').length;
            
            // Update Export Button
            const exportBtn = document.getElementById('exportBtn');
            if (selectedCount > 0) {
                exportBtn.disabled = false;
                exportBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                exportBtn.innerText = `Exportar ${selectedCount} Turma(s)`;
            } else {
                exportBtn.disabled = true;
                exportBtn.classList.add('opacity-50', 'cursor-not-allowed');
                exportBtn.innerText = 'Exportar Selecionadas';
            }

            // Update Bulk Delete Button (Admin Only)
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            if (bulkDeleteBtn) {
                if (selectedCount > 0) {
                    bulkDeleteBtn.disabled = false;
                    bulkDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'hidden');
                    bulkDeleteBtn.innerText = `Excluir ${selectedCount} Turma(s)`;
                } else {
                    bulkDeleteBtn.disabled = true;
                    bulkDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed', 'hidden');
                    bulkDeleteBtn.innerText = 'Excluir Selecionadas';
                }
            }
        }

        function exportSelected() {
            document.getElementById('exportForm').action = "{{ route('course-classes.export-all') }}";
            document.getElementById('exportForm').method = "GET";
            document.getElementById('exportForm').submit();
        }

        function bulkDelete() {
            confirmAction(
                'Confirmação de Exclusão em Massa',
                'Você tem certeza que deseja excluir as turmas selecionadas? Esta ação é irreversível.',
                'warning',
                'Sim, excluir tudo!',
                null
            ).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('exportForm');
                    form.action = "{{ route('course-classes.bulk-delete') }}";
                    form.method = "POST";
                    
                    // Add CSRF token if not present in the form (highly likely it is)
                    if (!form.querySelector('input[name="_token"]')) {
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = "{{ csrf_token() }}";
                        form.appendChild(csrf);
                    }
                    
                    form.submit();
                }
            });
        }
    </script>
@endsection