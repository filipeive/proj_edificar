@extends('layouts.app')

@section('title', 'Gestão de Turmas - Portal Life Church')
@section('page-title', 'Turmas dos Cursos')
@section('page-subtitle', 'Organização e acompanhamento de alunos e casais')

@section('content')
<div class="container-fluid">
    <div class="mb-6 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <h3 class="text-2xl font-bold text-gray-800">Lista de Turmas</h3>
            <form action="{{ route('course-classes.index') }}" method="GET" class="flex items-center">
                <select name="course_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos os Cursos</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <a href="{{ route('course-classes.create', ['course_id' => request('course_id')]) }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md">
            <i class="bi bi-plus-lg mr-2"></i> Nova Turma
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
            <i class="bi bi-check-circle-fill mr-3 text-xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Turma</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Curso</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Líderes</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Inscritos</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($classes as $class)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $class->name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $class->start_date ? $class->start_date->format('d/m/Y') : 'N/A' }} - 
                                    {{ $class->end_date ? $class->end_date->format('d/m/Y') : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $class->course->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $class->leaderHusband->name ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-900">
                                    {{ $class->leaderWife->name ?? '' }}
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
                                        'active' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusLabels = [
                                        'active' => 'Ativa',
                                        'completed' => 'Concluída',
                                        'cancelled' => 'Cancelada',
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClasses[$class->status] }}">
                                    {{ $statusLabels[$class->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('course-classes.show', $class) }}" class="text-blue-600 hover:text-blue-900" title="Ver Detalhes">
                                    <i class="bi bi-eye-fill text-lg"></i>
                                </a>
                                <a href="{{ route('course-classes.edit', $class) }}" class="text-amber-600 hover:text-amber-900" title="Editar">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                                <form action="{{ route('course-classes.destroy', $class) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta turma?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="bi bi-trash-fill text-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <i class="bi bi-inbox text-4xl block mb-2"></i>
                                Nenhuma turma encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($classes->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
