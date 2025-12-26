@extends('layouts.app')

@section('title', 'Detalhes do Curso')
@section('page-title', 'Detalhes do Curso')
@section('page-subtitle', 'Informações e lista de alunos matriculados')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('courses.index') }}" class="text-gray-600 hover:text-orange-600 flex items-center transition font-semibold">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
            <div class="flex space-x-3">
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                    <a href="{{ route('courses.edit', $course) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-xl flex items-center transition shadow-sm font-bold">
                        <i class="bi bi-pencil mr-2"></i> Editar
                    </a>
                @endif
                
                @php
                    $isEnrolled = $course->enrollments->where('user_id', auth()->id())->first();
                @endphp

                @if(!$isEnrolled)
                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-2.5 rounded-xl flex items-center transition shadow-lg shadow-orange-600/20 font-bold">
                            <i class="bi bi-person-plus mr-2"></i> Matricular-me
                        </button>
                    </form>
                @else
                    <span class="bg-green-100 text-green-700 px-6 py-2.5 rounded-xl flex items-center font-bold border border-green-200">
                        <i class="bi bi-check-circle-fill mr-2"></i> Já Matriculado
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Info do Curso -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="h-3 bg-orange-500"></div>
                    <div class="p-8">
                        <span class="px-3 py-1 bg-orange-100 text-orange-600 text-[10px] font-bold uppercase rounded-full tracking-widest mb-4 inline-block">
                            {{ $course->category ?? 'Geral' }}
                        </span>
                        <h3 class="text-3xl font-extrabold text-gray-900 mb-6">{{ $course->name }}</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Duração</h4>
                                <p class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="bi bi-clock mr-2 text-orange-500"></i> {{ $course->duration ?? 'Não informada' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Descrição</h4>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ $course->description ?? 'Sem descrição disponível.' }}
                                </p>
                            </div>
                            <div class="pt-6 border-t border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-gray-900">{{ $course->enrollments->count() }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">Alunos</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-green-600">{{ $course->enrollments->where('status', 'completed')->count() }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">Formados</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-orange-500">{{ $course->enrollments->where('status', 'enrolled')->count() }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">Ativos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Alunos -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                        <h4 class="text-xl font-bold text-gray-800">Alunos Matriculados</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    <th class="px-8 py-4">Aluno</th>
                                    <th class="px-8 py-4">Data Matrícula</th>
                                    <th class="px-8 py-4">Status</th>
                                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor')
                                        <th class="px-8 py-4 text-right">Ações</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($course->enrollments as $enrollment)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold mr-4">
                                                    {{ substr($enrollment->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900">{{ $enrollment->user->name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $enrollment->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-sm text-gray-600">
                                            {{ $enrollment->enrolled_at->format('d/m/Y') }}
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
                                                <form action="{{ route('enrollments.status', $enrollment) }}" method="POST" class="inline-flex space-x-2">
                                                    @csrf
                                                    <select name="status" onchange="this.form.submit()" class="text-xs rounded-lg border-gray-200 focus:ring-orange-500 focus:border-orange-500 py-1">
                                                        <option value="enrolled" {{ $enrollment->status === 'enrolled' ? 'selected' : '' }}>Ativo</option>
                                                        <option value="completed" {{ $enrollment->status === 'completed' ? 'selected' : '' }}>Concluir</option>
                                                        <option value="dropped" {{ $enrollment->status === 'dropped' ? 'selected' : '' }}>Desistir</option>
                                                    </select>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic">
                                            Nenhum aluno matriculado neste curso.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
