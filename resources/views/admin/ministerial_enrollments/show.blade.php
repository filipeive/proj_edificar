@extends('layouts.app')

@section('title', 'Detalhes da Inscrição - ' . $ministerialEnrollment->full_name)
@section('page-title', 'Detalhes da Inscrição Ministerial')
@section('page-subtitle', 'Dados completos do inscrito individual')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('ministerial-enrollments.edit', $ministerialEnrollment) }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Editar">
            <i class="bi bi-pencil-square"></i>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <a href="{{ route('ministerial-enrollments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Inscrições</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('ministerial-enrollments.edit', $ministerialEnrollment) }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-pencil"></i>
                <span>Editar Inscrição</span>
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50/60 p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">{{ $ministerialEnrollment->course->name }}</span>
                    @if($ministerialEnrollment->course_class_id)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                            Turma: {{ $ministerialEnrollment->courseClass->name }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                            Pendente de Turma
                        </span>
                    @endif
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">{{ $ministerialEnrollment->full_name }}</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Inscrição recebida em {{ $ministerialEnrollment->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="text-xs text-gray-400 sm:text-right">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Status</p>
                <p class="font-bold text-gray-700 uppercase">{{ $ministerialEnrollment->status }}</p>
            </div>
        </div>

        <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Dados Pessoais -->
            <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-700 mb-3 flex items-center gap-1.5">
                    <i class="bi bi-person-lines-fill text-blue-600"></i> Informações Pessoais
                </p>
                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Nome Completo</span>
                        <span class="font-bold text-gray-900">{{ $ministerialEnrollment->full_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">E-mail</span>
                        <span class="font-medium text-gray-800">{{ $ministerialEnrollment->email }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Telefone / WhatsApp</span>
                        <span class="font-bold text-gray-800">{{ $ministerialEnrollment->phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Dados Eclesiásticos -->
            <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-700 mb-3 flex items-center gap-1.5">
                    <i class="bi bi-diagram-3 text-orange-600"></i> Integração Eclesiástica
                </p>
                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Membro da Igreja?</span>
                        <span class="font-bold text-gray-900">{{ $ministerialEnrollment->is_church_member ? 'Sim, é membro' : 'Não, é visitante' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Célula</span>
                        <span class="font-medium text-gray-800">{{ $ministerialEnrollment->cell_name ?: 'Sem célula informada' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Turma Atual</span>
                        <span class="font-medium text-gray-800">{{ $ministerialEnrollment->courseClass->name ?? 'Aguardando alocação' }}</span>
                    </div>
                </div>
            </div>

            @if($ministerialEnrollment->observations)
                <div class="md:col-span-2 rounded-lg border border-gray-100 bg-gray-50/50 p-4">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold mb-1">Observações</span>
                    <p class="text-xs text-gray-700 whitespace-pre-line">{{ $ministerialEnrollment->observations }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
