@extends('layouts.app')

@section('title', 'Detalhes da Inscrição - ' . $ministerialEnrollment->full_name)
@section('page-title', 'Detalhes da Inscrição Ministerial')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('ministerial-enrollments.index') }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <a href="{{ route('ministerial-enrollments.edit', $ministerialEnrollment) }}" class="bg-blue-50 text-blue-600 border border-blue-100 px-4 py-2.5 rounded-xl hover:bg-blue-100 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-widest shadow-sm">
            <i class="bi bi-pencil-fill"></i> Editar
        </a>
    </div>
@endsection

@section('content')
    <div class="w-full space-y-8 animate-fadeIn">
        <!-- Hero Section -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-blue-500/10 transition-all duration-700"></div>
            
            <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
                <div class="w-32 h-32 rounded-[2.5rem] bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-5xl shadow-inner group-hover:scale-110 transition-all duration-500">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                
                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                        <span class="px-4 py-1.5 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-600/20">
                            {{ $ministerialEnrollment->course->name }}
                        </span>
                        @php
                            $statusClass = $ministerialEnrollment->course_class_id ? 'bg-green-100 text-green-700 border-green-200' : 'bg-orange-100 text-orange-700 border-orange-200';
                            $statusLabel = $ministerialEnrollment->course_class_id ? 'Alocado na Turma: ' . $ministerialEnrollment->courseClass->name : 'Pendente de Turma';
                        @endphp
                        <span class="px-4 py-1.5 {{ $statusClass }} border rounded-full text-[10px] font-black uppercase tracking-[0.2em]">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">
                        {{ $ministerialEnrollment->full_name }}
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 font-medium text-lg italic">
                        Inscrição recebida em {{ $ministerialEnrollment->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Personal Info -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Personal Data Card -->
                <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-10 flex items-center gap-2">
                        <i class="bi bi-person-lines-fill text-lg text-blue-600"></i> Informações Pessoais
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nome Completo</label>
                                <p class="text-gray-900 dark:text-white font-bold text-lg">{{ $ministerialEnrollment->full_name }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">E-mail</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $ministerialEnrollment->email }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Telefone / WhatsApp</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $ministerialEnrollment->phone }}</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl {{ $ministerialEnrollment->is_church_member ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shadow-sm">
                                    <i class="bi {{ $ministerialEnrollment->is_church_member ? 'bi-patch-check-fill' : 'bi-x-circle' }} text-xl"></i>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Membro da Igreja?</label>
                                    <p class="text-gray-900 dark:text-white font-black uppercase text-xs">{{ $ministerialEnrollment->is_church_member ? 'Sim, é membro' : 'Não, é visitante' }}</p>
                                </div>
                            </div>

                            @if($ministerialEnrollment->cell_name)
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center shadow-sm">
                                        <i class="bi bi-house-door-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Célula</label>
                                        <p class="text-gray-900 dark:text-white font-black uppercase text-xs">{{ $ministerialEnrollment->cell_name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($ministerialEnrollment->observations)
                        <div class="mt-10 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observações Adicionais</label>
                            <p class="text-gray-700 dark:text-gray-300 text-sm italic">{{ $ministerialEnrollment->observations }}</p>
                        </div>
                    @endif
                </div>

                <!-- System Integration Section -->
                <div class="bg-blue-600 text-white p-10 rounded-[3rem] shadow-xl shadow-blue-600/20 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-xl">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <h3 class="text-sm font-black uppercase tracking-widest">Acesso ao Sistema</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                            <div>
                                <p class="text-blue-100 text-sm font-medium leading-relaxed mb-4">
                                    Converta esta inscrição em um usuário do sistema para permitir que o aluno acompanhe seu progresso, materiais e certificados.
                                </p>
                                <p class="text-xs text-blue-200 uppercase tracking-widest font-black">
                                    Um usuário será criado com o e-mail: <br>
                                    <span class="text-white normal-case text-sm">{{ $ministerialEnrollment->email }}</span>
                                </p>
                            </div>
                            
                            <div class="flex flex-col gap-3">
                                <button onclick="confirmAction('Gerar Usuário', 'Deseja criar uma conta de acesso para este aluno?', 'question', 'Sim, criar!', 'convert-form-main')" 
                                    class="w-full bg-white text-blue-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl">
                                    Gerar Usuário Agora
                                </button>
                                <p class="text-[10px] text-blue-200 text-center font-bold">Uma senha temporária será gerada automaticamente.</p>
                            </div>
                        </div>
                    </div>

                    <form id="convert-form-main" action="{{ route('ministerial-enrollments.convert', $ministerialEnrollment) }}" method="POST" class="hidden">
                        @csrf
                        @if($ministerialEnrollment->course_class_id)
                            <input type="hidden" name="course_class_id" value="{{ $ministerialEnrollment->course_class_id }}">
                        @endif
                    </form>
                </div>
            </div>

            <!-- Right Column: Actions -->
            <div class="space-y-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-50 pb-4">Gestão da Turma</h3>
                    
                    @if(!$ministerialEnrollment->course_class_id)
                        <div x-data="{ open: false }" class="space-y-4">
                            <button @click="open = !open" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-600/20 flex items-center justify-center gap-2">
                                <i class="bi bi-box-arrow-in-right text-lg"></i> Alocar Turma
                            </button>
                            
                            <div x-show="open" @click.away="open = false" class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 animate-slideIn">
                                <form action="{{ route('course-classes.assign-ministerial-enrollment', \App\Models\CourseClass::where('course_id', $ministerialEnrollment->course_id)->first() ?? 0) }}" method="POST" id="assign-form">
                                    @csrf
                                    <input type="hidden" name="ministerial_enrollment_id" value="{{ $ministerialEnrollment->id }}">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Selecionar Turma</label>
                                    <select name="selected_class_id" onchange="document.getElementById('assign-form').action = '/course-classes/' + this.value + '/assign-ministerial-enrollment'" required class="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-xs font-bold mb-4 focus:ring-blue-500">
                                        <option value="">Escolha...</option>
                                        @foreach(\App\Models\CourseClass::where('course_id', $ministerialEnrollment->course_id)->orderBy('name')->get() as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black">Confirmar</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="p-6 bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-100 dark:border-green-800/30 text-center">
                            <i class="bi bi-check-circle-fill text-green-500 text-3xl mb-3 block"></i>
                            <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">Status: Matriculado</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $ministerialEnrollment->courseClass->name }}</p>
                        </div>
                    @endif

                    <div class="mt-8 pt-8 border-t border-gray-50 dark:border-gray-700 space-y-3">
                        <form action="{{ route('ministerial-enrollments.destroy', $ministerialEnrollment) }}" method="POST" 
                              onsubmit="return confirm('Deseja realmente excluir esta inscrição permanentemente?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-red-500 border border-red-100 dark:border-red-900/30 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-50 transition-all">
                                Excluir Inscrição
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Audit info -->
                <div class="px-6 space-y-2">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex justify-between">
                        <span>Data de Inscrição</span>
                        <span>{{ $ministerialEnrollment->created_at->format('d/m/Y H:i') }}</span>
                    </p>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest flex justify-between">
                        <span>Última Atualização</span>
                        <span>{{ $ministerialEnrollment->updated_at->format('d/m/Y H:i') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
