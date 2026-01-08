@php
    $cardColor = $type === 'enrolled' ? 'orange' : 'blue';
@endphp

<div
    class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-500 group relative">
    <div class="h-3 bg-{{ $cardColor }}-500"></div>

    <div class="p-8">
        <div class="flex justify-between items-start mb-6">
            <span
                class="px-4 py-1.5 bg-{{ $cardColor }}-50 text-{{ $cardColor }}-600 text-[10px] font-black uppercase rounded-xl tracking-widest border border-{{ $cardColor }}-100/50">
                {{ $course->category ?? 'Geral' }}
            </span>
            @if(!$course->is_active)
                <span
                    class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-black uppercase rounded-full tracking-widest">
                    Inativo
                </span>
            @endif
        </div>

        <h4
            class="text-2xl font-black text-gray-900 mb-3 group-hover:text-{{ $cardColor }}-600 transition-colors leading-tight">
            {{ $course->name }}
        </h4>

        <p class="text-gray-500 text-sm mb-8 line-clamp-2 font-medium">
            {{ $course->description ?? 'Sem descrição disponível.' }}
        </p>

        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="flex items-center text-gray-400 text-xs font-bold">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center mr-3">
                    <i class="bi bi-clock text-{{ $cardColor }}-500"></i>
                </div>
                {{ $course->duration ?? 'N/A' }}
            </div>
            <div class="flex items-center text-gray-400 text-xs font-bold">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center mr-3">
                    <i class="bi bi-people text-{{ $cardColor }}-500"></i>
                </div>
                {{ $course->enrollments_count }} Alunos
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('courses.show', $course) }}"
                class="flex-1 bg-gray-900 text-white text-center py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-{{ $cardColor }}-600 transition-all shadow-lg hover:shadow-{{ $cardColor }}-200 active:scale-95">
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
                        class="p-4 bg-gray-50 text-gray-400 rounded-2xl hover:bg-{{ $cardColor }}-50 hover:text-{{ $cardColor }}-600 transition-all"
                        title="Editar">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Decorative status tag -->
    @if($type === 'enrolled')
        <div class="absolute top-0 right-0 mt-4 mr-4">
            <div class="bg-green-500 text-white p-1 rounded-full px-2 text-[8px] font-black uppercase tracking-tighter">
                Matriculado
            </div>
        </div>
    @endif
</div>