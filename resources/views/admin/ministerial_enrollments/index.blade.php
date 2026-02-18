@extends('layouts.app')

@section('title', 'Inscrições Ministeriais Públicas')
@section('page-title', 'Inscrições Ministeriais')
@section('page-subtitle', 'Gestão de novos inscritos individuais via formulário público')

@section('content')
    <div class="w-full space-y-6">
        <!-- Table Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Inscrito</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Curso</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Contato</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Turma Atual</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
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
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $enrollment->course->name }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $enrollment->phone }}</p>
                                    <p class="text-[9px] font-medium text-gray-400 lowercase">{{ $enrollment->email }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    @if($enrollment->courseClass)
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                            <span class="text-xs font-black text-gray-900 dark:text-white uppercase">{{ $enrollment->courseClass->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest italic">Aguardando Turma</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @php
                                        $statusClass = $enrollment->course_class_id ? 'bg-green-100 text-green-700 border-green-200' : 'bg-orange-100 text-orange-700 border-orange-200';
                                        $statusLabel = $enrollment->course_class_id ? 'Alocado' : 'Pendente';
                                    @endphp
                                    <span class="px-3 py-1 {{ $statusClass }} text-[9px] font-black uppercase rounded-full border tracking-widest shadow-sm">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('ministerial-enrollments.show', $enrollment) }}" class="p-2 text-gray-400 hover:text-blue-500 transition-colors">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="{{ route('ministerial-enrollments.edit', $enrollment) }}" class="p-2 text-gray-400 hover:text-blue-500 transition-colors">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <form action="{{ route('ministerial-enrollments.destroy', $enrollment) }}" method="POST" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta inscrição?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
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
                                        <i class="bi bi-mortarboard text-3xl"></i>
                                    </div>
                                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest">Nenhuma inscrição encontrada</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($enrollments->hasPages())
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-50 dark:border-gray-700">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
