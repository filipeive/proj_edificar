@extends('layouts.app')

@section('title', 'Detalhes da Inscrição - ' . $coupleEnrollment->husband_name . ' & ' . $coupleEnrollment->wife_name)
@section('page-title', 'Detalhes da Inscrição')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('couple-enrollments.index') }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <a href="{{ route('couple-enrollments.edit', $coupleEnrollment) }}" class="bg-blue-50 text-blue-600 border border-blue-100 px-3 py-2.5 rounded-xl hover:bg-blue-100 transition-all flex items-center gap-2 text-xs font-black uppercase tracking-widest shadow-sm">
            <i class="bi bi-pencil-fill"></i> <span class="hidden md:inline">Editar</span>
        </a>
    </div>
@endsection
@section('content')
    <div class="w-full space-y-8 animate-fadeIn">
        <!-- Hero Section -->
        <div class="bg-white dark:bg-gray-800 rounded-[3rem] p-10 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/5 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-purple-500/10 transition-all duration-700"></div>
            
            <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
                <!--voltar a turma ou a lista de inscricoes-->
                @if($coupleEnrollment->course_class_id)
                    <a href="{{ route('course-classes.show', $coupleEnrollment->course_class_id) }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition-all">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                @else
                    <a href="{{ route('couple-enrollments.index') }}" class="p-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition-all">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                @endif
                <div class="w-32 h-32 rounded-[2.5rem] bg-purple-100 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center text-5xl shadow-inner group-hover:scale-110 transition-all duration-500">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                
                <div class="flex-1 text-center md:text-left">

                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                        <span class="px-4 py-1.5 bg-purple-600 text-white rounded-full text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-purple-600/20">
                            {{ $coupleEnrollment->course->name }}
                        </span>
                        @php
                            $statusClass = $coupleEnrollment->course_class_id ? 'bg-green-100 text-green-700 border-green-200' : 'bg-orange-100 text-orange-700 border-orange-200';
                            $statusLabel = $coupleEnrollment->course_class_id ? 'Alocado na Turma: ' . $coupleEnrollment->courseClass->name : 'Pendente de Turma';
                        @endphp
                        <span class="px-4 py-1.5 {{ $statusClass }} border rounded-full text-[10px] font-black uppercase tracking-[0.2em]">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">
                        {{ $coupleEnrollment->husband_name }} <span class="text-purple-600">&</span> {{ $coupleEnrollment->wife_name }}
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 font-medium text-lg italic">
                        "{{ ucfirst($coupleEnrollment->relationship_type) }} há {{ $coupleEnrollment->years_together }} anos"
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Personal Info -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Data Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Partner 1 -->
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i class="bi bi-person text-lg text-blue-500"></i> Informação do Parceiro (Masculino)
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nome Completo</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $coupleEnrollment->husband_name }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Telefone / WhatsApp</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $coupleEnrollment->husband_phone ?: 'Não informado' }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Endereço Residencial</label>
                                <p class="text-gray-900 dark:text-white font-bold text-sm leading-relaxed">{{ $coupleEnrollment->address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Partner 2 -->
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i class="bi bi-person-heart text-lg text-pink-500"></i> Informação da Parceira (Feminino)
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nome Completo</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $coupleEnrollment->wife_name }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Telefone / WhatsApp</label>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $coupleEnrollment->wife_phone ?: 'Não informado' }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Endereço Residencial</label>
                                <p class="text-gray-900 dark:text-white font-bold text-sm leading-relaxed">{{ $coupleEnrollment->wife_address ?: $coupleEnrollment->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observations & Church Info -->
                <div class="bg-white dark:bg-gray-800 p-10 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-8">Informações Adicionais</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Localização (Zona/Célula)</label>
                                    <p class="text-gray-900 dark:text-white font-bold">{{ $coupleEnrollment->cell_zone ?: 'Não informado' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                    <i class="bi bi-person-badge-fill"></i>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Líder / Responsável</label>
                                    <p class="text-gray-900 dark:text-white font-bold">{{ $coupleEnrollment->leader_name ?: 'Não informado' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl {{ in_array($coupleEnrollment->is_church_member, ['both', 'one']) ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shadow-sm">
                                    <i class="bi {{ in_array($coupleEnrollment->is_church_member, ['both', 'one']) ? ($coupleEnrollment->is_church_member === 'both' ? 'bi-patch-check-fill' : 'bi-person-check') : 'bi-x-circle' }} text-xl"></i>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Membro da Igreja?</label>
                                    <p class="text-gray-900 dark:text-white font-black uppercase text-xs">
                                        {{ $coupleEnrollment->is_church_member === 'both' ? 'Ambos são membros' : ($coupleEnrollment->is_church_member === 'one' ? '1 de nós é membro' : 'Não, são visitantes') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl {{ $coupleEnrollment->has_pastoral_recommendation ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shadow-sm">
                                    <i class="bi {{ $coupleEnrollment->has_pastoral_recommendation ? 'bi-shield-check' : 'bi-shield-x' }} text-xl"></i>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Recomendação Pastoral?</label>
                                    <p class="text-gray-900 dark:text-white font-black uppercase text-xs">{{ $coupleEnrollment->has_pastoral_recommendation ? 'Sim, possui' : 'Não possui' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($coupleEnrollment->observations)
                        <div class="mt-10 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observações Internas</label>
                            <p class="text-gray-700 dark:text-gray-300 text-sm italic">{{ $coupleEnrollment->observations }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Actions & Meta -->
            <div class="space-y-8">
                <!-- Registration Info -->
                <div class="bg-gray-900 text-white p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 relative z-10">Dados do Sistema</h3>
                    
                    <div class="space-y-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">ID da Inscrição</span>
                            <span class="text-sm font-black">#{{ $coupleEnrollment->id }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Data de Envio</span>
                            <span class="text-sm font-bold">{{ $coupleEnrollment->created_at->format('d M, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Horário</span>
                            <span class="text-sm font-bold">{{ $coupleEnrollment->created_at->format('H:i') }}</span>
                        </div>
                        
                        <div class="pt-6 border-t border-white/10">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Ações Rápidas</label>
                            
                            @if(!$coupleEnrollment->course_class_id)
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="w-full bg-white text-gray-900 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all flex items-center justify-center gap-2 mb-4">
                                        <i class="bi bi-box-arrow-in-right"></i> Alocar Turma
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" class="absolute bottom-full left-0 w-full bg-white rounded-2xl p-4 shadow-2xl mb-2 text-gray-900 z-50">
                                        <form action="{{ route('couple-enrollments.assign-class', $coupleEnrollment) }}" method="POST">
                                            @csrf
                                            <select name="course_class_id" required class="w-full bg-gray-50 border-none rounded-xl text-xs font-bold mb-3">
                                                <option value="">Escolha uma turma...</option>
                                                @foreach(\App\Models\CourseClass::where('course_id', $coupleEnrollment->course_id)->orderBy('name')->get() as $class)
                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest">Confirmar</button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('couple-enrollments.destroy', $coupleEnrollment) }}" method="POST" 
                                  onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir esta inscrição permanentemente?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500/10 text-red-500 border border-red-500/20 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">
                                    Excluir Registo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Help/Info Card -->
                <div class="bg-blue-600 text-white p-8 rounded-[2.5rem] shadow-xl shadow-blue-600/20">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <h4 class="text-sm font-black uppercase tracking-widest">Procedimento</h4>
                    </div>
                    <p class="text-xs text-blue-100 leading-relaxed font-medium">
                        As inscrições públicas servem como uma pré-matrícula. Após validar os detalhes do casal, você deve alocá-los em uma turma ativa do curso correspondente para que possam iniciar as aulas.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
