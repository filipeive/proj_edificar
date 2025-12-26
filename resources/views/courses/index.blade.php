@extends('layouts.app')

@section('title', 'Cursos e Formação')
@section('page-title', 'Academia & Cursos')
@section('page-subtitle', 'Gerencie a formação ministerial e cursos da igreja')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">Cursos Disponíveis</h3>
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                <a href="{{ route('courses.create') }}"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-xl flex items-center transition shadow-lg shadow-orange-600/20 font-bold">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Curso
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($courses as $course)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                    <div class="h-3 bg-orange-500"></div>
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-4">
                            <span
                                class="px-3 py-1 bg-orange-100 text-orange-600 text-[10px] font-bold uppercase rounded-full tracking-widest">
                                {{ $course->category ?? 'Geral' }}
                            </span>
                            @if(!$course->is_active)
                                <span
                                    class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold uppercase rounded-full tracking-widest">
                                    Inativo
                                </span>
                            @endif
                        </div>
                        <h4 class="text-2xl font-extrabold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors">
                            {{ $course->name }}
                        </h4>
                        <p class="text-gray-500 text-sm mb-6 line-clamp-2">
                            {{ $course->description ?? 'Sem descrição disponível.' }}
                        </p>

                        <div class="flex items-center justify-between text-sm text-gray-400 mb-8">
                            <div class="flex items-center">
                                <i class="bi bi-clock mr-2"></i> {{ $course->duration ?? 'N/A' }}
                            </div>
                            <div class="flex items-center">
                                <i class="bi bi-people mr-2"></i> {{ $course->enrollments_count }} Alunos
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <a href="{{ route('courses.show', $course) }}"
                                class="flex-1 bg-gray-900 text-white text-center py-3 rounded-xl font-bold hover:bg-orange-600 transition shadow-lg">
                                Ver Detalhes
                            </a>
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                <a href="{{ route('courses.edit', $course) }}"
                                    class="p-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-orange-100 hover:text-orange-600 transition">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-dashed border-gray-300">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="bi bi-mortarboard text-4xl text-gray-300"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Nenhum curso encontrado</h4>
                    <p class="text-gray-500">Comece criando o primeiro curso da academia.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection