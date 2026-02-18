@extends('layouts.app')

@section('title', 'Inscrições Públicas')
@section('page-title', 'Inscrições Públicas')
@section('page-subtitle', 'Gestão de novos inscritos via formulário público')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('couple-enrollments.export', request()->all()) }}"
            class="bg-green-50 text-green-600 border border-green-100 px-4 py-2.5 rounded-xl hover:bg-green-100 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-widest shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet-fill text-lg"></i>
            Exportar CSV
        </a>
    </div>
@endsection

@section('content')
    <div class="w-full space-y-6">
        <!-- Filters Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700">
            <form action="{{ route('couple-enrollments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nome ou contato..."
                        class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-bold text-xs text-gray-600 dark:text-gray-300">
                </div>

                <div class="relative group">
                    <select name="course_id" onchange="this.form.submit()"
                        class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-600 dark:text-gray-300">
                        <option value="">Todos os Cursos</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative group">
                    <select name="status" onchange="this.form.submit()"
                        class="w-full pl-4 pr-10 py-3 bg-gray-50 dark:bg-gray-700 border-none rounded-xl focus:ring-2 focus:ring-orange-500 font-black text-[10px] uppercase tracking-widest text-gray-600 dark:text-gray-300">
                        <option value="">Todos os Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovado (Com Turma)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all">
                        Filtrar
                    </button>
                    <a href="{{ route('couple-enrollments.index') }}" class="px-4 py-3 bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition-all">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Inscrito / Casal</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Curso</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Contato / Zona</th>
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
                                        <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center font-black">
                                            {{ substr($enrollment->husband_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 dark:text-white leading-tight">
                                                {{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}
                                            </p>
                                            <div class="flex flex-col mt-0.5">
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">
                                                    {{ ucfirst($enrollment->relationship_type) }} • {{ $enrollment->years_together }} anos
                                                </p>
                                                <div class="mt-1.5 space-y-0.5">
                                                    <p class="text-[9px] font-medium text-gray-500 dark:text-gray-400 italic flex items-center gap-1">
                                                        <i class="bi bi-geo-alt-fill text-[8px]"></i>
                                                        {{ $enrollment->address }}
                                                        @if($enrollment->wife_address)
                                                            <span class="text-[8px] text-gray-400 not-italic ml-1">(Marido)</span>
                                                        @endif
                                                    </p>
                                                    @if($enrollment->wife_address)
                                                        <p class="text-[9px] font-medium text-gray-500 dark:text-gray-400 italic flex items-center gap-1">
                                                            <i class="bi bi-geo-alt-fill text-[8px] text-pink-400"></i>
                                                            {{ $enrollment->wife_address }}
                                                            <span class="text-[8px] text-gray-400 not-italic ml-1">(Esposa)</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $enrollment->course->name }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    @if($enrollment->husband_phone)
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300"><i class="bi bi-person text-blue-500 mr-1"></i>{{ $enrollment->husband_phone }}</p>
                                    @endif
                                    @if($enrollment->wife_phone)
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300"><i class="bi bi-person-heart text-pink-500 mr-1"></i>{{ $enrollment->wife_phone }}</p>
                                    @endif
                                    @if(!$enrollment->husband_phone && !$enrollment->wife_phone && $enrollment->contacts)
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $enrollment->contacts }}</p>
                                    @endif
                                    <p class="text-[9px] font-medium text-gray-400 uppercase mt-0.5">{{ $enrollment->cell_zone ?? 'Zona não inf.' }}</p>
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
                                        @if(!$enrollment->course_class_id)
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <button @click="open = !open" type="button" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-md shadow-blue-600/20">
                                                    Alocar Turma
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" 
                                                     class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 p-4 text-left">
                                                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Selecionar Turma</h5>
                                                    <form action="{{ route('couple-enrollments.assign-class', $enrollment) }}" method="POST">
                                                        @csrf
                                                        <select name="course_class_id" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl text-xs font-bold mb-3 focus:ring-orange-500">
                                                            <option value="">Escolha uma turma...</option>
                                                            @if(is_object($classes) && method_exists($classes, 'where'))
                                                                @foreach($classes->where('course_id', $enrollment->course_id) as $class)
                                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700">Confirmar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif

                                        <a href="{{ route('couple-enrollments.show', $enrollment) }}" class="p-2 text-gray-400 hover:text-purple-500 transition-colors" title="Ver Detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="{{ route('couple-enrollments.edit', $enrollment) }}" class="p-2 text-gray-400 hover:text-blue-500 transition-colors">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <form action="{{ route('couple-enrollments.destroy', $enrollment) }}" method="POST" 
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
                                        <i class="bi bi-person-plus text-3xl"></i>
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
