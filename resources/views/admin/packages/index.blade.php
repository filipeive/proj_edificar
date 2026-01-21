@extends('layouts.app')

@section('title', 'Gestão de Pacotes - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header & Top Actions -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Pacotes de Compromisso</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Gestão de Planos de Contribuição
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('packages.create') }}"
                    class="bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-100">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Pacote
                </a>
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-[2rem] flex items-center gap-4 animate-fade-in">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Packages List -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Nome do Pacote</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Intervalo de Valores</th>
                            <th
                                class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Membros Ativos</th>
                            <th
                                class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Responsável</th>
                            <th
                                class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Estado</th>
                            <th
                                class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($packages as $package)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">
                                            {{ $package->name }}
                                        </span>
                                        @if($package->whatsapp_link)
                                            <a href="{{ $package->whatsapp_link }}" target="_blank"
                                                class="flex items-center gap-1 text-[10px] text-green-600 font-black uppercase mt-1 hover:text-green-700">
                                                <i class="bi bi-whatsapp"></i> Grupo WhatsApp
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black text-green-600">
                                            {{ number_format($package->min_amount, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[10px] text-gray-300 font-black">→</span>
                                        <span class="text-sm font-black text-green-600">
                                            @if($package->max_amount)
                                                {{ number_format($package->max_amount, 0, ',', '.') }}
                                            @else
                                                <i class="bi bi-infinity text-lg"></i>
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-black uppercase ml-1">MT</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-lg font-black text-gray-900 tracking-tighter">
                                            {{ $package->getActiveMembersCount() }}
                                        </span>
                                        <span
                                            class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Inscritos</span>
                                    </div>
                                </td>
                                <!-- responsavel -->
                                <td class="px-10 py-6 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-lg font-black text-gray-900 tracking-tighter">
                                            {{ $package->getResponsavelName() }}
                                        </span>
                                        <span
                                            class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Responsável</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <span
                                        class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border 
                                                                        {{ $package->is_active ? 'bg-green-50 text-green-600 border-green-100' : 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                        {{ $package->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <a href="{{ route('packages.show', $package) }}"
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('packages.edit', $package) }}"
                                            class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="{{ route('contributions.create') }}?package_id={{ $package->id }}"
                                            class="w-10 h-10 rounded-xl bg-green-50 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center transition-all shadow-sm"
                                            title="Nova Contribuição">
                                            <i class="bi bi-plus-lg"></i>
                                        </a>
                                        <form action="{{ route('packages.destroy', $package) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('Tem certeza que deseja excluir o pacote {{ $package->name }}?').then(result => { if(result.isConfirmed) this.closest('form').submit(); })"
                                                class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm font-black">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection