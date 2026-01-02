@extends('layouts.app')

@section('title', $courseClass->name . ' - Portal Life Church')
@section('page-title', $courseClass->name)
@section('page-subtitle', $courseClass->course->name)

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('course-classes.index', ['course_id' => $courseClass->course_id]) }}"
                class="text-gray-500 hover:text-gray-700 flex items-center">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para a lista
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
                <i class="bi bi-check-circle-fill mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Coluna Esquerda: Detalhes e Líderes -->
            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="text-lg font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-info-circle text-blue-600 mr-2"></i> Detalhes da Turma
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status</p>
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
                            <span
                                class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClasses[$courseClass->status] }}">
                                {{ $statusLabels[$courseClass->status] }}
                            </span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Período</p>
                            <p class="text-sm font-bold text-gray-700">
                                {{ $courseClass->start_date ? $courseClass->start_date->format('d/m/Y') : 'N/A' }} -
                                {{ $courseClass->end_date ? $courseClass->end_date->format('d/m/Y') : 'N/A' }}
                            </p>
                        </div>
                        <hr class="border-gray-100">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Líderes
                                Acompanhantes</p>
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span
                                    class="text-sm font-bold text-gray-700">{{ $courseClass->leaderHusband->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-pink-50 rounded-full flex items-center justify-center text-pink-600">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span
                                    class="text-sm font-bold text-gray-700">{{ $courseClass->leaderWife->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 space-y-3">
                        <a href="{{ route('course-classes.report', $courseClass) }}"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition block text-center shadow-lg shadow-blue-600/20">
                            <i class="bi bi-bar-chart-fill mr-2"></i> Ver Relatório Final
                        </a>
                        <a href="{{ route('course-classes.edit', $courseClass) }}"
                            class="w-full bg-gray-50 text-gray-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-100 transition block text-center border border-gray-100">
                            Editar Turma
                        </a>
                    </div>
                </div>

                <!-- Próximos Encontros -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-black text-gray-900 flex items-center">
                            <i class="bi bi-calendar-check text-orange-600 mr-2"></i> Encontros
                        </h4>
                        <button type="button" onclick="document.getElementById('meetingModal').classList.remove('hidden')"
                            class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">
                            + Novo
                        </button>
                    </div>
                    <div class="space-y-4">
                        @forelse($courseClass->meetings as $meeting)
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-transparent hover:border-orange-100 transition group">
                                <div class="flex items-center space-x-3">
                                    <div class="text-center">
                                        <span
                                            class="block text-xs font-black text-gray-400">{{ $meeting->meeting_number }}º</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $meeting->topic ?? 'Encontro' }}</p>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                            {{ $meeting->date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('course-classes.attendance', [$courseClass, $meeting]) }}"
                                    class="bg-white text-orange-600 p-2 rounded-lg shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-orange-600 hover:text-white">
                                    <i class="bi bi-card-checklist"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-4 text-sm italic">Nenhum encontro agendado.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: Lista de Inscritos -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h4 class="text-lg font-black text-gray-900 flex items-center">
                            <i class="bi bi-people-fill text-blue-600 mr-2"></i> Alunos e Casais Inscritos
                        </h4>
                        <button type="button"
                            onclick="document.getElementById('enrollmentModal').classList.remove('hidden')"
                            class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                            Adicionar Inscrito
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Nome / Casal</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Faltas</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                {{-- Casais --}}
                                @foreach($courseClass->coupleEnrollments as $couple)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $couple->husband_name }} &
                                                {{ $couple->wife_name }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Casal
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php $absences = $couple->attendances()->where('status', 'absent')->count(); @endphp
                                            <span class="font-bold {{ $absences > 2 ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $absences }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($couple->status == 'failed')
                                                <span
                                                    class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-[8px] font-black uppercase tracking-widest">Reprovado</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-[8px] font-black uppercase tracking-widest">Ativo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('course-classes.remove-enrollment', $courseClass) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="enrollment_type" value="couple">
                                                <input type="hidden" name="enrollment_id" value="{{ $couple->id }}">
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition"
                                                    title="Remover da Turma">
                                                    <i class="bi bi-x-circle text-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Alunos Individuais --}}
                                @foreach($courseClass->courseEnrollments as $enrollment)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $enrollment->user->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                                Individual</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php $absences = $enrollment->attendances()->where('status', 'absent')->count(); @endphp
                                            <span class="font-bold {{ $absences > 2 ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $absences }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($enrollment->status == 'failed')
                                                <span
                                                    class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-[8px] font-black uppercase tracking-widest">Reprovado</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-[8px] font-black uppercase tracking-widest">Ativo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('course-classes.remove-enrollment', $courseClass) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="enrollment_type" value="course">
                                                <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition"
                                                    title="Remover da Turma">
                                                    <i class="bi bi-x-circle text-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($courseClass->courseEnrollments->isEmpty() && $courseClass->coupleEnrollments->isEmpty())
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                                            Nenhum inscrito nesta turma.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Adicionar Inscrito -->
    <div id="enrollmentModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                <h4 class="text-xl font-black text-gray-900">Adicionar Inscrito</h4>
                <button type="button" onclick="document.getElementById('enrollmentModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-8">
                <form action="{{ route('course-classes.add-enrollment', $courseClass) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Selecione o Inscrito</label>
                            <select name="enrollment_data" required
                                class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione...</option>
                                <optgroup label="Casais">
                                    @foreach($availableCoupleEnrollments as $couple)
                                        <option value="couple:{{ $couple->id }}">{{ $couple->husband_name }} &
                                            {{ $couple->wife_name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Individuais">
                                    @foreach($availableCourseEnrollments as $enrollment)
                                        <option value="course:{{ $enrollment->id }}">{{ $enrollment->user->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <input type="hidden" name="enrollment_type" id="modal_type">
                        <input type="hidden" name="enrollment_id" id="modal_id">
                    </div>
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" onclick="document.getElementById('enrollmentModal').classList.add('hidden')"
                            class="px-6 py-2 text-gray-500 font-bold">Cancelar</button>
                        <button type="submit" onclick="prepareEnrollmentData()"
                            class="px-8 py-2 bg-blue-600 text-white rounded-xl font-black uppercase tracking-widest shadow-lg shadow-blue-600/20">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Novo Encontro -->
    <div id="meetingModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                <h4 class="text-xl font-black text-gray-900">Agendar Encontro</h4>
                <button type="button" onclick="document.getElementById('meetingModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-8">
                <form action="{{ route('course-classes.meetings.store', $courseClass) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nº do Encontro</label>
                                <input type="number" name="meeting_number" required
                                    value="{{ $courseClass->meetings->count() + 1 }}"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Data</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tema / Tópico</label>
                            <input type="text" name="topic" placeholder="Ex: Finanças no Casamento"
                                class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" onclick="document.getElementById('meetingModal').classList.add('hidden')"
                            class="px-6 py-2 text-gray-500 font-bold">Cancelar</button>
                        <button type="submit"
                            class="px-8 py-2 bg-orange-600 text-white rounded-xl font-black uppercase tracking-widest shadow-lg shadow-orange-600/20">Agendar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function prepareEnrollmentData() {
            const select = document.querySelector('select[name="enrollment_data"]');
            const [type, id] = select.value.split(':');
            document.getElementById('modal_type').value = type;
            document.getElementById('modal_id').value = id;
        }
    </script>
@endsection