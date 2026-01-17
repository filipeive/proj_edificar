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
                <div class="flex gap-4">
                    <a href="{{ route('courses.export-global') }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-2xl flex items-center transition shadow-lg shadow-green-600/20 font-black text-sm uppercase tracking-widest">
                        <i class="bi bi-file-earmark-spreadsheet mr-2"></i> Relatório Geral
                    </a>
                    <a href="{{ route('courses.create') }}"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-2xl flex items-center transition shadow-lg shadow-orange-600/20 font-black text-sm uppercase tracking-widest">
                        <i class="bi bi-plus-lg mr-2"></i> Novo Curso
                    </a>
                </div>
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
                <h2 class="text-xl font-black text-gray-400 uppercase tracking-widest mb-8">Monitoramento Global (Admin)</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @foreach($allCourses as $course)
                        <a href="{{ route('courses.show', $course) }}"
                            class="bg-gray-50 p-4 rounded-2xl border border-gray-100 hover:bg-white hover:shadow-md transition-all group">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                {{ $course->category ?? 'ACADEMIA' }}</p>
                            <h5 class="font-bold text-gray-900 group-hover:text-orange-600 transition-colors">{{ $course->name }}
                            </h5>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $course->enrollments_count }}
                                    Alunos</span>
                                <i class="bi bi-arrow-right-short text-gray-300 group-hover:text-orange-600 transition-colors"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection