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

                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden"
                        x-data="{ view: 'list' }">
                        <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/10">
                            <div class="flex items-center gap-4">
                                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Lista de Cursos</h3>
                                <div class="flex bg-gray-50 p-1 rounded-xl border border-gray-100">
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
                            </div>
                        </div>

                        <form id="bulkActionForm" method="POST" x-show="view === 'list'"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0">
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
                                                        <button type="button"
                                                            onclick="deleteCourse({{ $course->id }})"
                                                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
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
                    </form>

                    <!-- Grid View for Admin -->
                    <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-gray-50/30">
                        @foreach($allCourses as $course)
                            <div
                                class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-md transition-all relative h-full">
                                <div class="absolute top-6 right-6 flex gap-2">
                                    @if($course->registration_open)
                                        <span
                                            class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-green-100">Abertas</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-red-100">Fechadas</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-4 mb-6">
                                    <div
                                        class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-black text-2xl group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                                        {{ strtoupper(substr($course->name, 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <h5 class="font-black text-gray-900 leading-tight mb-1">{{ $course->name }}</h5>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $course->category ?? 'ACADEMIA' }}</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-2xl mb-6 space-y-3">
                                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                                        <span class="text-gray-400">Total Alunos</span>
                                        <span class="text-gray-900 text-sm">{{ $course->enrollments_count }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
                                        <span class="text-gray-400">Slug</span>
                                        <span class="text-gray-500 lowercase font-mono">{{ $course->slug }}</span>
                                    </div>
                                </div>

                                <div class="mt-auto grid grid-cols-3 gap-2">
                                    <a href="{{ route('courses.show', $course) }}"
                                        class="col-span-1 bg-gray-900 text-white text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center justify-center">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('courses.edit', $course) }}"
                                        class="col-span-1 bg-gray-50 text-gray-400 text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all flex items-center justify-center">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @if(auth()->user()->role === 'admin')
                                            <button type="button"
                                                onclick="deleteCourse({{ $course->id }})"
                                                class="col-span-1 bg-red-50 text-red-400 text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
            </div>
            </section>
        @endif
    </div>

    @if(auth()->user()->role === 'admin')
        <!-- Shared Delete Form -->
        <form id="singleDeleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <script>
            function deleteCourse(id) {
                confirmAction(
                    'Deseja excluir este curso?',
                    'Esta ação é irreversível e removerá todas as matrículas associadas.',
                    'warning',
                    'Sim, excluir'
                ).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('singleDeleteForm');
                        form.action = `/courses/${id}`; 
                        form.submit();
                    }
                });
            }

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