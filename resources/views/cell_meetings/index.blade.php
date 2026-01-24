@extends('layouts.app')

@section('title', 'Encontros de Célula - Portal Life Church')

@section('content')
@section('header-actions')
    @can('create', App\Models\CellMeeting::class)
        <a href="{{ route('cell-meetings.create') }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
            <i class="bi bi-calendar-plus-fill text-2xl"></i>
        </a>
    @endcan
@endsection

@section('content')
    <div class="space-y-8" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : 'list',
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid';
                }
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('cell_meetings_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('cell_meetings_view') || 'list')"
        @resize.window.debounce.500ms="updateView()">
        <!-- Header & Top Actions -->
        <div class="bg-white p-4 md:p-8 rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Encontros de Célula</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Gestão de Reuniões e Atas</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex bg-gray-100 p-1 rounded-xl items-center">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-list-ul mr-2"></i> Lista
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-grid-fill mr-2"></i> Grid
                    </button>
                </div>
                @can('create', App\Models\CellMeeting::class)
                    <a href="{{ route('cell-meetings.create') }}"
                        class="hidden md:flex bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest items-center shadow-lg shadow-blue-100">
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
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
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
                            <tr class="group hover:bg-gray-50/50 transition-colors duration-200">
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
                                                id="list-delete-form-{{ $meeting->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('{{ route('cell-meetings.destroy', $meeting) }}', 'list-delete-form-{{ $meeting->id }}')"
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
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($meetings as $meeting)
                <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300 relative">
                    <div class="absolute top-6 right-6">
                        @php
                            $style = $typeStyles[$meeting->meeting_type] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $style }}">
                            @switch($meeting->meeting_type)
                                @case('leadership') Lider @break
                                @case('supervision') Sup @break
                                @case('zone') Zona @break
                                @default Célula
                            @endswitch
                        </span>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 mb-6">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-lg font-black text-gray-900 leading-tight mb-1 group-hover:text-blue-600 transition-colors">
                            {{ $meeting->meeting_date->format('d/m/Y') }}
                        </h4>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $meeting->cell->name }}</p>
                    </div>

                    <div class="space-y-3 mb-6 flex-1 bg-gray-50 p-4 rounded-2xl">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase text-gray-400">Presença</span>
                            <span class="text-lg font-black text-gray-900">{{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }}</span>
                        </div>
                        <div class="flex flex-col border-t border-gray-100 pt-2">
                            <span class="text-[9px] font-black uppercase text-gray-400">Responsável</span>
                            <span class="text-xs font-bold text-gray-700 truncate">{{ $meeting->leader->name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-4 border-t border-gray-50">
                        <a href="{{ route('cell-meetings.show', $meeting) }}"
                            class="flex-1 bg-gray-900 text-white text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-eye"></i> Detalhes
                        </a>
                        @can('update', $meeting)
                            <a href="{{ route('cell-meetings.edit', $meeting) }}"
                                class="w-10 h-10 bg-gray-50 text-gray-400 flex items-center justify-center rounded-xl hover:bg-orange-600 hover:text-white transition-all">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('delete', $meeting)
                            <form action="{{ route('cell-meetings.destroy', $meeting) }}" method="POST"
                                id="grid-delete-form-{{ $meeting->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ route('cell-meetings.destroy', $meeting) }}', 'grid-delete-form-{{ $meeting->id }}')"
                                    class="w-10 h-10 bg-gray-50 text-gray-400 flex items-center justify-center rounded-xl hover:bg-red-600 hover:text-white transition-all">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        @if($meetings->hasPages())
            <div class="pt-4">
                {{ $meetings->links() }}
            </div>
        @endif
    </div>
@endsection