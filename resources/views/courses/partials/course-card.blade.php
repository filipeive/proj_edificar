@php
    $cardColor = $type === 'enrolled' ? 'orange' : 'blue';
@endphp

<div
    class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-gray-900/50 transition-all duration-500 group relative">
    <div class="h-3 bg-{{ $cardColor }}-500"></div>

    <div class="p-8">
        <div class="flex justify-between items-start mb-6">
            <span
                class="px-4 py-1.5 bg-{{ $cardColor }}-50 dark:bg-{{ $cardColor }}-900/20 text-{{ $cardColor }}-600 dark:text-{{ $cardColor }}-400 text-[10px] font-black uppercase rounded-xl tracking-widest border border-{{ $cardColor }}-100/50 dark:border-{{ $cardColor }}-800/50">
                {{ $course->category ?? 'Geral' }}
            </span>
            @if($type === 'enrolled')
                <span
                    class="px-3 py-1 bg-green-500/10 text-green-600 dark:text-green-400 text-[10px] font-black uppercase rounded-full tracking-widest border border-green-500/20">
                    <i class="bi bi-check-circle-fill mr-1"></i> Matriculado
                </span>
            @endif
            @if(!$course->is_active)
                <span
                    class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-black uppercase rounded-full tracking-widest">
                    Inativo
                </span>
            @endif
        </div>

        <h4
            class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-{{ $cardColor }}-600 dark:group-hover:text-{{ $cardColor }}-400 transition-colors duration-300 leading-tight">
            {{ $course->name }}
        </h4>

        <p class="text-gray-500 dark:text-gray-400 text-sm mb-8 line-clamp-2 font-medium">
            {{ $course->description ?? 'Sem descrição disponível.' }}
        </p>

        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="flex items-center text-gray-400 dark:text-gray-500 text-xs font-bold">
                <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center mr-3">
                    <i class="bi bi-clock text-{{ $cardColor }}-500 dark:text-{{ $cardColor }}-400"></i>
                </div>
                {{ $course->duration ?? 'N/A' }}
            </div>
            <div class="flex items-center text-gray-400 dark:text-gray-500 text-xs font-bold">
                <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center mr-3">
                    <i class="bi bi-people text-{{ $cardColor }}-500 dark:text-{{ $cardColor }}-400"></i>
                </div>
                {{ $course->enrollments_count + $course->couple_enrollments_count }} Alunos
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('courses.show', $course) }}"
                class="flex-1 bg-gray-900 dark:bg-black/50 text-white text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-{{ $cardColor }}-600 hover:text-white dark:hover:bg-{{ $cardColor }}-600 transition-all shadow-lg hover:shadow-{{ $cardColor }}-200 dark:hover:shadow-{{ $cardColor }}-900/30 active:scale-95">
                Detalhes
            </a>

            @if($type === 'available' && $course->registration_open)
                <form action="{{ route('courses.enroll', $course) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full bg-{{ $cardColor }}-600 text-white text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-900 transition-all shadow-lg shadow-{{ $cardColor }}-200 active:scale-95">
                        <i class="bi bi-plus-circle mr-2"></i> Inscrever
                    </button>
                </form>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor' || auth()->user()->role === 'secretaria')
                <div class="flex gap-2">
                    <a href="{{ route('courses.edit', $course) }}"
                        class="p-4 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-300 rounded-2xl hover:bg-{{ $cardColor }}-50 dark:hover:bg-{{ $cardColor }}-900/30 hover:text-{{ $cardColor }}-600 dark:hover:text-{{ $cardColor }}-400 transition-all"
                        title="Editar">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>