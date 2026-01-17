@extends('layouts.app')

@section('title', 'Detalhes do Pacote - ' . $package->name)

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('packages.index') }}" class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 transition-all">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $package->name }}</h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Gestão de Membros e Contribuições</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($package->whatsapp_link)
                    <a href="{{ $package->whatsapp_link }}" target="_blank"
                        class="bg-green-600 text-white px-8 py-4 rounded-2xl hover:bg-green-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-green-100">
                        <i class="bi bi-whatsapp mr-2 text-lg"></i> Grupo do WhatsApp
                    </a>
                @endif
                <a href="{{ route('packages.edit', $package) }}"
                    class="bg-orange-600 text-white px-8 py-4 rounded-2xl hover:bg-orange-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-orange-100">
                    <i class="bi bi-pencil-square mr-2"></i> Editar Pacote
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Stats Column -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Informações do Pacote</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Valor Mínimo</span>
                            <span class="text-sm font-black text-gray-900">{{ number_format($package->min_amount, 2, ',', '.') }} MT</span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Valor Máximo</span>
                            <span class="text-sm font-black text-gray-900">
                                @if($package->max_amount)
                                    {{ number_format($package->max_amount, 2, ',', '.') }} MT
                                @else
                                    Sem Limite
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Membros Ativos</span>
                            <span class="text-sm font-black text-blue-600">{{ $package->getActiveMembersCount() }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase block mb-2">Descrição</span>
                            <p class="text-sm text-gray-600 leading-relaxed font-medium">
                                {{ $package->description ?? 'Nenhuma descrição fornecida.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- SMS/Newsletter Mockup -->
                <div class="bg-blue-600 p-8 rounded-[2.5rem] shadow-lg shadow-blue-100 text-white">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-4">Lembrete Automático</h3>
                    <p class="text-xs text-blue-100 font-medium mb-6">Envie um lembrete para todos os membros deste pacote via SMS ou WhatsApp.</p>
                    <button class="w-full bg-white text-blue-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl">
                        Disparar Notificações
                    </button>
                </div>
            </div>

            <!-- Members List Column -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Membros Comprometidos</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Membro</th>
                                    <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor</th>
                                    <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Data Início</th>
                                    <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($package->userCommitments as $commitment)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-400 text-xs">
                                                    {{ strtoupper(substr($commitment->user->name, 0, 1)) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-black text-gray-900 leading-tight">{{ $commitment->user->name }}</span>
                                                    <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $commitment->user->cell->name ?? 'Sem Célula' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="text-sm font-black text-gray-900">{{ number_format($commitment->committed_amount, 2, ',', '.') }} MT</span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="text-xs font-bold text-gray-500">{{ $commitment->start_date ? $commitment->start_date->format('d/m/Y') : 'N/A' }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                                {{ $commitment->isActive() ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                                {{ $commitment->isActive() ? 'Ativo' : 'Encerrado' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-10 text-center">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nenhum membro encontrado neste pacote.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
