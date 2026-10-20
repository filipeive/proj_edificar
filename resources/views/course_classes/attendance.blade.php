@extends('layouts.app')

@section('title', 'Lista de Presença - ' . $courseClass->name)
@section('page-title', 'Lista de Presença')
@section('page-subtitle', $courseClass->name . ' - ' . ($meeting->topic ?? 'Encontro ' . $meeting->meeting_number))

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('course-classes.show', $courseClass) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para a Turma</span>
        </a>
    </div>

    <form action="{{ route('course-classes.attendance.store', [$courseClass, $meeting]) }}" method="POST"
        class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf

        <div class="border-b border-gray-100 bg-gray-50/60 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Chamada do Encontro</p>
                <h2 class="text-sm sm:text-base font-bold text-gray-900 mt-0.5">{{ $meeting->topic ?? 'Encontro ' . $meeting->meeting_number }}</h2>
                <p class="text-xs text-gray-500">{{ $meeting->date->format('d/m/Y') }}</p>
            </div>
            <div>
                <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-1 text-xs font-bold text-orange-800">
                    {{ $courseClass->enrollments_count }} Inscritos
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/75 text-[10px] font-black uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3">Inscrito</th>
                        <th class="px-4 py-3 text-center">Presença</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                    @php
                        $hasEnrollments = $courseClass->courseEnrollments->count() || $courseClass->coupleEnrollments->count();
                    @endphp
                    @if($hasEnrollments)
                        @foreach($courseClass->courseEnrollments as $enrollment)
                            @php
                                $attendance = $meeting->attendances->where('enrollable_type', \App\Models\CourseEnrollment::class)->where('enrollable_id', $enrollment->id)->first();
                                $currentStatus = $attendance ? $attendance->status : 'absent';
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-4 py-3">
                                    @if($enrollment->malePartner && $enrollment->femalePartner)
                                        <div class="font-bold text-gray-900">{{ $enrollment->malePartner->name }} & {{ $enrollment->femalePartner->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase">Casal</div>
                                    @else
                                        <div class="font-bold text-gray-900">{{ $enrollment->user->name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase">Individual</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-3">
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendance[{{ $enrollment->id }}]" value="present"
                                                {{ $currentStatus == 'present' ? 'checked' : '' }}
                                                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                            <span class="text-xs font-bold text-emerald-700">Presente</span>
                                        </label>

                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendance[{{ $enrollment->id }}]" value="absent"
                                                {{ $currentStatus == 'absent' ? 'checked' : '' }}
                                                class="h-4 w-4 text-rose-600 focus:ring-rose-500 border-gray-300">
                                            <span class="text-xs font-bold text-rose-700">Falta</span>
                                        </label>

                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendance[{{ $enrollment->id }}]" value="justified"
                                                {{ $currentStatus == 'justified' ? 'checked' : '' }}
                                                class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                            <span class="text-xs font-bold text-amber-700">Justificada</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @foreach($courseClass->coupleEnrollments as $enrollment)
                            @php
                                $attendance = $meeting->attendances->where('enrollable_type', \App\Models\CoupleEnrollment::class)->where('enrollable_id', $enrollment->id)->first();
                                $currentStatus = $attendance ? $attendance->status : 'absent';
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">Casal (Formulário)</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-3">
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendance_couple[{{ $enrollment->id }}]" value="present"
                                                {{ $currentStatus == 'present' ? 'checked' : '' }}
                                                class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                            <span class="text-xs font-bold text-emerald-700">Presente</span>
                                        </label>

                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendance_couple[{{ $enrollment->id }}]" value="absent"
                                                {{ $currentStatus == 'absent' ? 'checked' : '' }}
                                                class="h-4 w-4 text-rose-600 focus:ring-rose-500 border-gray-300">
                                            <span class="text-xs font-bold text-rose-700">Falta</span>
                                        </label>

                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendance_couple[{{ $enrollment->id }}]" value="justified"
                                                {{ $currentStatus == 'justified' ? 'checked' : '' }}
                                                class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                            <span class="text-xs font-bold text-amber-700">Justificada</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-xs text-gray-400">
                                Nenhum aluno matriculado nesta turma.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('course-classes.show', $courseClass) }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Salvar Presenças</span>
            </button>
        </div>
    </form>
</div>
@endsection
