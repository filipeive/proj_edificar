@extends('layouts.app')

@section('title', 'Cursos e Formação')
@section('page-title', 'Academia & Cursos')
@section('page-subtitle', 'Gerencie a formação ministerial e cursos da igreja')

@section('content')
    <div class="container-fluid space-y-12">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Academia & Formação</h1>
                <p class="text-gray-500 font-medium">Desenvolvimento ministerial e crescimento espiritual.</p>
            </div>
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                <a href="{{ route('courses.create') }}"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-2xl flex items-center transition shadow-lg shadow-orange-600/20 font-black text-sm uppercase tracking-widest">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Curso
                </a>
            @endif
        </div>

        @if($enrolledCourses->isNotEmpty())
            <!-- Meus Cursos Section -->
            <section class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                        <i class="bi bi-mortarboard-fill text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Meus Cursos</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($enrolledCourses as $course)
                        @include('courses.partials.course-card', ['course' => $course, 'type' => 'enrolled'])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Cursos Disponíveis Section -->
        <section class="space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="bi bi-book-half text-xl"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Cursos Disponíveis</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($availableCourses as $course)
                    @include('courses.partials.course-card', ['course' => $course, 'type' => 'available'])
                @empty
                    <div class="col-span-full bg-white rounded-[2.5rem] p-16 text-center border border-dashed border-gray-200">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="bi bi-stars text-5xl text-gray-200"></i>
                        </div>
                        <h4 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tight">Sem novos cursos no momento
                        </h4>
                        <p class="text-gray-500 font-medium max-w-md mx-auto">Já estás inscrito em todos os cursos disponíveis
                            para o teu nível ou não há novas inscrições abertas.</p>
                    </div>
                @endforelse
            </div>
        </section>

        @if((auth()->user()->role === 'admin' || auth()->user()->role === 'pastor') && $allCourses->isNotEmpty())
            <!-- Admin: Todos os Cursos Monitoramento -->
            <section class="mt-20 pt-10 border-t border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                    <h2 class="text-xl font-black text-gray-400 uppercase tracking-widest">Monitoramento Global (Admin)</h2>
                    @if(auth()->user()->role === 'admin')
                        <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl flex items-center transition shadow-lg shadow-red-600/20 font-black text-xs uppercase tracking-widest hidden">
                            <i class="bi bi-trash-fill mr-2"></i> Excluir Selecionados
                        </button>
                    @endif
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <form id="bulkActionForm" method="POST">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100">
                                        @if(auth()->user()->role === 'admin')
                                            <th class="px-8 py-6 w-10">
                                                <input type="checkbox" id="selectAllCheckbox"
                                                    class="rounded-lg border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                            </th>
                                        @endif
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Curso</th>
                                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Categoria</th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                            Alunos</th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                            Inscrições</th>
                                        <th
                                            class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                            Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($allCourses as $course)
                                        <tr class="hover:bg-gray-50/50 transition-colors group">
                                            @if(auth()->user()->role === 'admin')
                                                <td class="px-8 py-6">
                                                    <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                                        class="course-checkbox rounded-lg border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5">
                                                </td>
                                            @endif
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-black group-hover:scale-110 transition-transform">
                                                        {{ strtoupper(substr($course->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h5 class="font-bold text-gray-900 leading-tight">{{ $course->name }}</h5>
                                                        <span
                                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $course->slug }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                                    {{ $course->category ?? 'ACADEMIA' }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <span
                                                    class="text-sm font-black text-gray-700">{{ $course->enrollments_count }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-center text-xs font-bold">
                                                @if($course->registration_open)
                                                    <span class="text-green-600">ABERTAS</span>
                                                @else
                                                    <span class="text-red-500">FECHADAS</span>
                                                @endif
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <div
                                                    class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <a href="{{ route('courses.show', $course) }}"
                                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-500 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                    <a href="{{ route('courses.edit', $course) }}"
                                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    @if(auth()->user()->role === 'admin')
                                                        <form action="{{ route('courses.destroy', $course) }}" method="POST"
                                                            id="delete-course-{{ $course->id }}" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="button"
                                                                onclick="confirmDelete('delete-course-{{ $course->id }}', 'Deseja excluir este curso permanentemente?')"
                                                                class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </section>
        @endif
    </div>

    @if(auth()->user()->role === 'admin')
        <script>
            const selectAll = document.getElementById('selectAllCheckbox');
            const checkboxes = document.querySelectorAll('.course-checkbox');
            const bulkBtn = document.getElementById('bulkDeleteBtn');

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkBtn();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBtn);
            });

            function updateBulkBtn() {
                const count = document.querySelectorAll('.course-checkbox:checked').length;
                if (count > 0) {
                    bulkBtn.disabled = false;
                    bulkBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'hidden');
                    bulkBtn.innerHTML = `<i class="bi bi-trash-fill mr-2"></i> Excluir ${count} Curso(s)`;
                } else {
                    bulkBtn.disabled = true;
                    bulkBtn.classList.add('opacity-50', 'cursor-not-allowed', 'hidden');
                }
            }

            function bulkDelete() {
                confirmAction(
                    'Confirmação de Exclusão em Massa',
                    'Você tem certeza que deseja excluir os cursos selecionados? Esta ação é irreversível e removerá todas as matrículas associadas.',
                    'warning',
                    'Sim, excluir tudo!',
                    null
                ).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('bulkActionForm');
                        form.action = "{{ route('courses.bulk-delete') }}";
                        form.submit();
                    }
                });
            }
        </script>
    @endif
@endsection