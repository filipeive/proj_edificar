@php
    $cardColor = $type === 'enrolled' ? 'orange' : 'blue';
@endphp

<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
    <div>
        <div class="flex items-center justify-between gap-2 mb-2">
            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-700">
                {{ $course->category ?? 'Geral' }}
            </span>
            <div class="flex items-center gap-1.5">
                @if($type === 'enrolled')
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                        <i class="bi bi-check-circle-fill mr-1"></i> Matriculado
                    </span>
                @endif
                @if(!$course->is_active)
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">
                        Inativo
                    </span>
                @endif
            </div>
        </div>

        <h4 class="text-sm font-bold text-gray-900 line-clamp-1 mb-1">
            {{ $course->name }}
        </h4>

        <p class="text-xs text-gray-500 line-clamp-2 mb-3">
            {{ $course->description ?? 'Sem descrição disponível.' }}
        </p>

        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3 pt-2 border-t border-gray-100">
            <div class="flex items-center gap-1.5">
                <i class="bi bi-clock text-gray-400"></i>
                <span>{{ $course->duration ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <i class="bi bi-people text-gray-400"></i>
                <span>{{ $course->enrollments_count + $course->couple_enrollments_count }} Alunos</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
        <a href="{{ route('courses.show', $course) }}"
            class="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-center text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
            Detalhes
        </a>

        @if($type === 'available' && $course->registration_open)
            <form action="{{ route('courses.enroll', $course) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-1 rounded-lg bg-orange-700 px-3 py-1.5 text-center text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                    <i class="bi bi-plus-circle"></i>
                    <span>Inscrever</span>
                </button>
            </form>
        @endif

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'pastor' || auth()->user()->role === 'secretaria')
            <a href="{{ route('courses.edit', $course) }}"
                class="rounded-lg border border-gray-200 bg-white p-1.5 text-gray-500 hover:bg-gray-50 hover:text-blue-600 transition"
                title="Editar">
                <i class="bi bi-pencil"></i>
            </a>
        @endif
    </div>
</div>
