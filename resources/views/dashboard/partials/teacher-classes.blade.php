@php
    $taughtClasses = auth()->user()->getTaughtClasses();
@endphp

@if($taughtClasses->isNotEmpty())
    <section class="rounded-xl border border-orange-200 dark:border-zinc-800 bg-gradient-to-br from-orange-50/40 via-white to-white dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-900 p-4 sm:p-5 shadow-sm space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-orange-100 dark:border-zinc-800 pb-3.5">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700 dark:text-orange-400">
                        <i class="bi bi-mortarboard-fill"></i> Docência & Formação Ministerial
                    </span>
                    <span class="inline-flex items-center rounded-full bg-orange-100 dark:bg-orange-950/60 px-2 py-0.5 text-[10px] font-black text-orange-800 dark:text-orange-300">
                        {{ $taughtClasses->count() }} {{ $taughtClasses->count() === 1 ? 'Turma' : 'Turmas' }}
                    </span>
                </div>
                <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white mt-1">
                    Minhas Turmas como Professor / Formador
                </h2>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">
                    Você foi designado como instrutor responsável. Aceda às turmas para fazer chamadas, acompanhar frequências e gerir participantes.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('course-classes.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-700 shadow-sm transition">
                    <i class="bi bi-collection"></i>
                    <span>Ver Todas as Turmas</span>
                </a>
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
            @foreach($taughtClasses as $class)
                @php
                    $isPrimaryTeacher = ($class->teacher_male_id === auth()->id() || $class->teacher_female_id === auth()->id());
                    $totalStudents = $class->course_enrollments_count + $class->couple_enrollments_count;
                    
                    $statusStyles = [
                        'em_andamento' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/40',
                        'concluida' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800/40',
                        'cancelada' => 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800/40',
                    ];
                    $statusNames = [
                        'em_andamento' => 'Em Andamento',
                        'concluida' => 'Concluída',
                        'cancelada' => 'Cancelada',
                    ];
                @endphp
                <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 shadow-sm hover:border-orange-400 dark:hover:border-orange-500/50 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Badges Topo -->
                        <div class="flex items-center justify-between gap-2 mb-2.5">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-black uppercase border {{ $statusStyles[$class->status] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $class->status === 'em_andamento' ? 'bg-emerald-500 animate-pulse' : 'bg-current' }}"></span>
                                {{ $statusNames[$class->status] ?? ucfirst($class->status) }}
                            </span>

                            @if($isPrimaryTeacher)
                                <span class="rounded bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/40 px-2 py-0.5 text-[9px] font-black uppercase text-blue-700 dark:text-blue-300">
                                    Professor
                                </span>
                            @else
                                <span class="rounded bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800/40 px-2 py-0.5 text-[9px] font-black uppercase text-purple-700 dark:text-purple-300">
                                    Assistente
                                </span>
                            @endif
                        </div>

                        <!-- Curso & Nome da Turma -->
                        <span class="text-[10px] font-black uppercase tracking-wider text-orange-700 dark:text-orange-400 block truncate">
                            {{ $class->course->name ?? 'Curso Geral' }}
                        </span>
                        <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-white mt-0.5 leading-snug group-hover:text-orange-700 dark:group-hover:text-orange-400 transition-colors">
                            {{ $class->name }}
                        </h3>

                        <!-- Metadados / Resumo -->
                        <div class="grid grid-cols-2 gap-2 mt-3 p-2.5 rounded-lg bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-800 text-xs">
                            <div>
                                <span class="text-[10px] text-gray-400 dark:text-zinc-500 block uppercase font-bold">Alunos / Casais</span>
                                <span class="font-black text-gray-900 dark:text-zinc-200 flex items-center gap-1 mt-0.5">
                                    <i class="bi bi-people text-orange-600 dark:text-orange-400 text-xs"></i>
                                    {{ $totalStudents }} inscritos
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 dark:text-zinc-500 block uppercase font-bold">Encontros</span>
                                <span class="font-black text-gray-900 dark:text-zinc-200 flex items-center gap-1 mt-0.5">
                                    <i class="bi bi-calendar2-check text-blue-600 dark:text-blue-400 text-xs"></i>
                                    {{ $class->meetings_count }} aulas
                                </span>
                            </div>
                        </div>

                        @if($class->start_date)
                            <p class="text-[11px] text-gray-500 dark:text-zinc-400 mt-2 flex items-center gap-1">
                                <i class="bi bi-clock-history text-gray-400 text-xs"></i>
                                Início: <span class="font-bold text-gray-700 dark:text-zinc-300">{{ $class->start_date->format('d/m/Y') }}</span>
                                @if($class->end_date)
                                    · Término: <span class="font-bold text-gray-700 dark:text-zinc-300">{{ $class->end_date->format('d/m/Y') }}</span>
                                @endif
                            </p>
                        @endif
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center gap-2 pt-3 mt-3 border-t border-gray-100 dark:border-zinc-800">
                        <a href="{{ route('course-classes.show', $class) }}"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-orange-700 hover:bg-orange-800 text-white px-3 py-1.5 text-xs font-bold shadow-sm transition">
                            <i class="bi bi-gear-fill text-xs"></i>
                            <span>Gerir Turma</span>
                        </a>
                        <a href="{{ route('course-classes.report', $class) }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition"
                            title="Relatório da Turma">
                            <i class="bi bi-bar-chart-fill text-orange-600"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
