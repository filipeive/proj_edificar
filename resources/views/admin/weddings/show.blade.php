@extends('layouts.app')

@section('title', 'Detalhes do Casamento')
@section('page-title', 'Detalhes do Casamento')
@section('page-subtitle', 'Informações completas sobre o agendamento matrimonial')

@section('content')
    <div class="w-full">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('weddings.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
            <div class="flex space-x-2">
                <a href="{{ route('weddings.pdf', ['id' => $wedding->id]) }}"
                    class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                    <i class="bi bi-file-earmark-pdf mr-2"></i> Exportar PDF
                </a>
                @if(in_array(auth()->user()->role, ['admin', 'secretaria']))
                    <a href="{{ route('weddings.edit', $wedding) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                        <i class="bi bi-pencil mr-2"></i> Editar
                    </a>
                @endif
            </div>
        </div>

        <div class="w-full">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-pink-600 px-8 py-10 text-white relative">
                    <div class="relative z-10">
                        <p class="text-pink-200 text-sm font-bold uppercase tracking-widest mb-2">
                            Casamento Matrimonial</p>
                        <h3 class="text-4xl font-black mb-4">{{ $wedding->groom_name }} & {{ $wedding->bride_name }}</h3>
                        <div class="flex flex-wrap gap-6 text-sm">
                            <span class="flex items-center"><i class="bi bi-calendar3 mr-2"></i>
                                {{ $wedding->date->format('d/m/Y') }}</span>
                            <span class="flex items-center"><i class="bi bi-clock mr-2"></i>
                                {{ $wedding->time ? \Carbon\Carbon::parse($wedding->time)->format('H:i') : 'Hora não definida' }}</span>
                            <span class="flex items-center"><i class="bi bi-geo-alt mr-2"></i>
                                {{ $wedding->location ?? 'Templo Sede' }}</span>
                            <span class="flex items-center"><i class="bi bi-info-circle mr-2"></i>
                                <span class="capitalize">{{ $wedding->status }}</span></span>
                        </div>
                    </div>
                    <i class="bi bi-heart-fill absolute right-8 bottom-4 text-8xl text-white opacity-10"></i>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div class="space-y-8">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Padrinhos /
                                Testemunhas</h4>
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <p class="text-gray-800 leading-relaxed font-medium">
                                    {{ $wedding->godparents ?? 'Nenhum padrinho/testemunha informado.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Observações
                                Adicionais</h4>
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <p class="text-gray-800 leading-relaxed">
                                    {{ $wedding->observations ?? 'Sem observações para este agendamento.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-gray-50 bg-gray-50/30">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                            Registrado em: {{ $wedding->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="flex items-center gap-2">
                            @if($wedding->status === 'pendente')
                                <span
                                    class="px-4 py-1.5 bg-yellow-100 text-yellow-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-yellow-200">
                                    Pendente
                                </span>
                            @elseif($wedding->status === 'confirmado')
                                <span
                                    class="px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-200">
                                    Confirmado
                                </span>
                            @else
                                <span
                                    class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-200">
                                    {{ $wedding->status }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection