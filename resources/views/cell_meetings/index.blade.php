@extends('layouts.app')

@section('title', 'Encontros de Célula - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header & Top Actions -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Encontros de Célula</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Gestão de Reuniões e Atas</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-gray-100 p-1 rounded-xl flex items-center mr-2">
                    <button class="px-4 py-2 rounded-lg text-sm font-bold bg-white text-gray-900 shadow-sm transition-all duration-300">
                        <i class="bi bi-list-ul mr-2"></i> Lista
                    </button>
                    <button class="px-4 py-2 rounded-lg text-sm font-bold text-gray-400 hover:text-gray-900 transition-all duration-300 opacity-50 cursor-not-allowed">
                        <i class="bi bi-grid-fill mr-2"></i> Grid
                    </button>
                </div>
                @can('create', App\Models\CellMeeting::class)
                    <a href="{{ route('cell-meetings.create') }}"
                        class="bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-100">
                        <i class="bi bi-plus-lg mr-2"></i> Novo Encontro
                    </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-[2rem] flex items-center gap-4 animate-fade-in">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- List View -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Data / Unidade</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Liderança / Conteúdo</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Participação</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo de Atividade</th>
                            <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($meetings as $meeting)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">
                                            {{ $meeting->meeting_date->format('d/m/Y') }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                            {{ $meeting->cell->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 leading-tight">{{ $meeting->leader->name ?? 'N/A' }}</span>
                                        <span class="text-[10px] text-gray-400 italic">{{ $meeting->theme ?? 'Sem tema registrado' }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-lg font-black text-gray-900 tracking-tighter">
                                            {{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }}
                                        </span>
                                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Presentes</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    @php
                                        $typeStyles = [
                                            'normal' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'leadership' => 'bg-purple-50 text-purple-600 border-purple-100',
                                            'supervision' => 'bg-orange-50 text-orange-600 border-orange-100',
                                            'zone' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                        ];
                                        $style = $typeStyles[$meeting->meeting_type] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                    @endphp
                                    <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $style }}">
                                        @switch($meeting->meeting_type)
                                            @case('leadership') Liderança @break
                                            @case('supervision') Supervisão @break
                                            @case('zone') Zona @break
                                            @default Célula
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <a href="{{ route('cell-meetings.show', $meeting) }}"
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @can('update', $meeting)
                                            <a href="{{ route('cell-meetings.edit', $meeting) }}"
                                                class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $meeting)
                                            <form action="{{ route('cell-meetings.destroy', $meeting) }}" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este encontro?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm font-black">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4 text-gray-300">
                                        <i class="bi bi-calendar-x text-7xl"></i>
                                        <p class="font-bold text-lg">Nenhum encontro registrado.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($meetings->hasPages())
                <div class="p-8 bg-gray-50/50 border-t border-gray-50">
                    {{ $meetings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection