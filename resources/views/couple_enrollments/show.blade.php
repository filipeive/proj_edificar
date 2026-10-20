@extends('layouts.app')

@section('title', 'Detalhes da Inscrição - ' . $coupleEnrollment->husband_name . ' & ' . $coupleEnrollment->wife_name)
@section('page-title', 'Detalhes da Inscrição')
@section('page-subtitle', 'Dados completos do casal inscrito')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('couple-enrollments.edit', $coupleEnrollment) }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Editar">
            <i class="bi bi-pencil-square"></i>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        @if($coupleEnrollment->course_class_id)
            <a href="{{ route('course-classes.show', $coupleEnrollment->course_class_id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Turma</span>
            </a>
        @else
            <a href="{{ route('couple-enrollments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Inscrições</span>
            </a>
        @endif

        <div class="flex items-center gap-2">
            <a href="{{ route('couple-enrollments.edit', $coupleEnrollment) }}"
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
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">{{ $coupleEnrollment->course->name }}</span>
                    @if($coupleEnrollment->course_class_id)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                            Turma: {{ $coupleEnrollment->courseClass->name }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                            Pendente de Turma
                        </span>
                    @endif
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">
                    {{ $coupleEnrollment->husband_name }} & {{ $coupleEnrollment->wife_name }}
                </h1>
                <p class="text-xs text-gray-500 mt-1">
                    {{ ucfirst(str_replace('_', ' ', $coupleEnrollment->relationship_type)) }} há {{ $coupleEnrollment->years_together }} anos
                </p>
            </div>

            <div class="text-xs text-gray-400 sm:text-right">
                <p class="text-[10px] font-black uppercase tracking-wider text-gray-400">Inscrito em</p>
                <p class="font-bold text-gray-700">{{ $coupleEnrollment->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Dados do Esposo -->
            <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-blue-700 mb-3 flex items-center gap-1.5">
                    <i class="bi bi-person"></i> Parceiro Masculino
                </p>
                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Nome Completo</span>
                        <span class="font-bold text-gray-900">{{ $coupleEnrollment->husband_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Telefone / WhatsApp</span>
                        <span class="font-bold text-gray-800">{{ $coupleEnrollment->husband_phone ?: 'Não informado' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Endereço</span>
                        <span class="font-medium text-gray-800">{{ $coupleEnrollment->address }}</span>
                    </div>
                </div>
            </div>

            <!-- Dados da Esposa -->
            <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-pink-700 mb-3 flex items-center gap-1.5">
                    <i class="bi bi-person-heart"></i> Parceira Feminina
                </p>
                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Nome Completo</span>
                        <span class="font-bold text-gray-900">{{ $coupleEnrollment->wife_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Telefone / WhatsApp</span>
                        <span class="font-bold text-gray-800">{{ $coupleEnrollment->wife_phone ?: 'Não informado' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Endereço da Esposa</span>
                        <span class="font-medium text-gray-800">{{ $coupleEnrollment->wife_address ?: $coupleEnrollment->address }}</span>
                    </div>
                </div>
            </div>

            <!-- Dados de Integração Eclesiástica -->
            <div class="md:col-span-2 rounded-lg border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-orange-700 mb-3 flex items-center gap-1.5">
                    <i class="bi bi-diagram-3"></i> Integração Eclesiástica & Igreja
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Membro da Igreja?</span>
                        <span class="font-bold text-gray-900">{{ $coupleEnrollment->is_church_member ? 'Sim' : 'Não' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Zona de Célula</span>
                        <span class="font-bold text-gray-900">{{ $coupleEnrollment->zone->name ?? $coupleEnrollment->cell_zone ?? 'Não informada' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Líder</span>
                        <span class="font-bold text-gray-900">{{ $coupleEnrollment->leader_name ?: 'Não informado' }}</span>
                    </div>
                </div>
                @if($coupleEnrollment->observations)
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Observações</span>
                        <p class="text-xs text-gray-700 mt-0.5 whitespace-pre-line">{{ $coupleEnrollment->observations }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
