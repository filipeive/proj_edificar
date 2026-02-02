@extends('layouts.app')

@section('title', 'Lista de Presença - ' . $courseClass->name)
@section('page-title', 'Lista de Presença')
@section('page-subtitle', $courseClass->name . ' - ' . ($meeting->topic ?? 'Encontro ' . $meeting->meeting_number))

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('course-classes.show', $courseClass) }}"
                class="text-gray-500 hover:text-gray-700 flex items-center">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para a turma
            </a>
        </div>

        <div class="w-full">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-xl font-black text-gray-900">
                                {{ $meeting->topic ?? 'Encontro ' . $meeting->meeting_number }}</h4>
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">
                                {{ $meeting->date->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span
                                class="bg-blue-100 text-blue-800 px-4 py-1 rounded-full text-xs font-black uppercase tracking-widest">
                                {{ $courseClass->enrollments_count }} Inscritos
                            </span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('course-classes.attendance.store', [$courseClass, $meeting]) }}" method="POST">
                    @csrf
                    <div class="p-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white border-b border-gray-100">
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Inscrito</th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Presença</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @php
                                    $hasEnrollments = $courseClass->courseEnrollments->count() || $courseClass->coupleEnrollments->count();
                                @endphp
                                @if($hasEnrollments)
                                    @foreach($courseClass->courseEnrollments as $enrollment)
                                        @php
                                            $attendance = $meeting->attendances->where('enrollable_type', \App\Models\CourseEnrollment::class)->where('enrollable_id', $enrollment->id)->first();
                                            $currentStatus = $attendance ? $attendance->status : 'absent';
                                        @endphp
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-8 py-6">
                                                @if($enrollment->malePartner && $enrollment->femalePartner)
                                                    <div class="font-bold text-gray-900">{{ $enrollment->malePartner->name }} & {{ $enrollment->femalePartner->name }}</div>
                                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">CASAL</div>
                                                @else
                                                    <div class="font-bold text-gray-900">{{ $enrollment->user->name ?? 'N/A' }}</div>
                                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">INDIVIDUAL</div>
                                                @endif
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="flex justify-center space-x-4">
                                                    <label class="flex flex-col items-center cursor-pointer group">
                                                        <input type="radio" name="attendance[{{ $enrollment->id }}]"
                                                            value="present" {{ $currentStatus == 'present' ? 'checked' : '' }}
                                                            class="hidden peer">
                                                        <div
                                                            class="w-10 h-10 rounded-full border-2 border-gray-200 flex items-center justify-center text-gray-400 peer-checked:border-green-500 peer-checked:bg-green-500 peer-checked:text-white transition group-hover:border-green-200">
                                                            <i class="bi bi-check-lg text-2xl"></i>
                                                        </div>
                                                        <span
                                                            class="text-[8px] font-black uppercase tracking-widest mt-1 text-gray-400 peer-checked:text-green-600">Presente</span>
                                                    </label>

                                                    <label class="flex flex-col items-center cursor-pointer group">
                                                        <input type="radio" name="attendance[{{ $enrollment->id }}]"
                                                            value="absent" {{ $currentStatus == 'absent' ? 'checked' : '' }}
                                                            class="hidden peer">
                                                        <div
                                                            class="w-10 h-10 rounded-full border-2 border-gray-200 flex items-center justify-center text-gray-400 peer-checked:border-red-500 peer-checked:bg-red-500 peer-checked:text-white transition group-hover:border-red-200">
                                                            <i class="bi bi-x-lg text-2xl"></i>
                                                        </div>
                                                        <span
                                                            class="text-[8px] font-black uppercase tracking-widest mt-1 text-gray-400 peer-checked:text-red-600">Falta</span>
                                                    </label>

                                                    <label class="flex flex-col items-center cursor-pointer group">
                                                        <input type="radio" name="attendance[{{ $enrollment->id }}]"
                                                            value="justified" {{ $currentStatus == 'justified' ? 'checked' : '' }}
                                                            class="hidden peer">
                                                        <div
                                                            class="w-10 h-10 rounded-full border-2 border-gray-200 flex items-center justify-center text-gray-400 peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white transition group-hover:border-amber-200">
                                                            <i class="bi bi-info-lg text-2xl"></i>
                                                        </div>
                                                        <span
                                                            class="text-[8px] font-black uppercase tracking-widest mt-1 text-gray-400 peer-checked:text-amber-600">Justif.</span>
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
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-8 py-6">
                                                <div class="font-bold text-gray-900">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">CASAL (FORMULÁRIO)</div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="flex justify-center space-x-4">
                                                    <label class="flex flex-col items-center cursor-pointer group">
                                                        <input type="radio" name="attendance_couple[{{ $enrollment->id }}]"
                                                            value="present" {{ $currentStatus == 'present' ? 'checked' : '' }}
                                                            class="hidden peer">
                                                        <div
                                                            class="w-10 h-10 rounded-full border-2 border-gray-200 flex items-center justify-center text-gray-400 peer-checked:border-green-500 peer-checked:bg-green-500 peer-checked:text-white transition group-hover:border-green-200">
                                                            <i class="bi bi-check-lg text-2xl"></i>
                                                        </div>
                                                        <span
                                                            class="text-[8px] font-black uppercase tracking-widest mt-1 text-gray-400 peer-checked:text-green-600">Presente</span>
                                                    </label>

                                                    <label class="flex flex-col items-center cursor-pointer group">
                                                        <input type="radio" name="attendance_couple[{{ $enrollment->id }}]"
                                                            value="absent" {{ $currentStatus == 'absent' ? 'checked' : '' }}
                                                            class="hidden peer">
                                                        <div
                                                            class="w-10 h-10 rounded-full border-2 border-gray-200 flex items-center justify-center text-gray-400 peer-checked:border-red-500 peer-checked:bg-red-500 peer-checked:text-white transition group-hover:border-red-200">
                                                            <i class="bi bi-x-lg text-2xl"></i>
                                                        </div>
                                                        <span
                                                            class="text-[8px] font-black uppercase tracking-widest mt-1 text-gray-400 peer-checked:text-red-600">Falta</span>
                                                    </label>

                                                    <label class="flex flex-col items-center cursor-pointer group">
                                                        <input type="radio" name="attendance_couple[{{ $enrollment->id }}]"
                                                            value="justified" {{ $currentStatus == 'justified' ? 'checked' : '' }}
                                                            class="hidden peer">
                                                        <div
                                                            class="w-10 h-10 rounded-full border-2 border-gray-200 flex items-center justify-center text-gray-400 peer-checked:border-amber-500 peer-checked:bg-amber-500 peer-checked:text-white transition group-hover:border-amber-200">
                                                            <i class="bi bi-info-lg text-2xl"></i>
                                                        </div>
                                                        <span
                                                            class="text-[8px] font-black uppercase tracking-widest mt-1 text-gray-400 peer-checked:text-amber-600">Justif.</span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2" class="px-8 py-10 text-center text-gray-400 italic">
                                            Nenhum inscrito nesta turma.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-10 py-3 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-blue-600/20 hover:bg-blue-700 transition transform hover:-translate-y-1">
                            Salvar Presenças
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
