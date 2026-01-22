@extends('layouts.app')

@section('title', 'Gestão de Pacotes - Portal Life Church')

@section('content')
    <div x-data="{ view: 'list' }">
        @section('header-actions')
            <a href="{{ route('packages.create') }}"
                class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
                <i class="bi bi-plus-circle text-xl"></i>
            </a>
        @endsection
        <div class="space-y-8">
            <!-- Header & Top Actions -->
            <div
                class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Pacotes de Compromisso</h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Gestão de Planos de
                        Contribuição
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex bg-gray-50 p-1 rounded-xl border border-gray-100 mr-2">
                        <button @click="view = 'list'"
                            :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                            <i class="bi bi-list-ul"></i>
                        </button>
                        <button @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                            <i class="bi bi-grid-fill"></i>
                        </button>
                    </div>
                    <a href="{{ route('packages.create') }}"
                        class="hidden md:flex bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest items-center shadow-lg shadow-blue-100">
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
            <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th
                                    class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Nome do Pacote</th>
                                <th
                                    class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
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

            <!-- Grid View -->
            <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($packages as $package)
                    <div
                        class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300 relative">
                        <div class="absolute top-6 right-6">
                            <span
                                class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border 
                                    {{ $package->is_active ? 'bg-green-50 text-green-600 border-green-100' : 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                {{ $package->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>

                        <div
                            class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 mb-6">
                            {{ strtoupper(substr($package->name, 0, 1)) }}
                        </div>

                        <div class="mb-4">
                            <h4
                                class="text-lg font-black text-gray-900 leading-tight mb-1 group-hover:text-blue-600 transition-colors uppercase tracking-widest">
                                {{ $package->name }}</h4>
                            <div class="flex items-center gap-2 text-green-600 font-black text-xs">
                                <span>{{ number_format($package->min_amount, 0, ',', '.') }} MT</span>
                                <span class="text-gray-300">→</span>
                                <span>
                                    @if($package->max_amount)
                                        {{ number_format($package->max_amount, 0, ',', '.') }} MT
                                    @else
                                        <i class="bi bi-infinity"></i>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6 flex-1 bg-gray-50 p-4 rounded-2xl">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase text-gray-400">Inscritos</span>
                                <span class="text-lg font-black text-gray-900">{{ $package->getActiveMembersCount() }}</span>
                            </div>
                            <div class="flex flex-col border-t border-gray-100 pt-2">
                                <span class="text-[9px] font-black uppercase text-gray-400">Responsável</span>
                                <span
                                    class="text-xs font-bold text-gray-700 truncate">{{ $package->getResponsavelName() }}</span>
                            </div>
                            @if($package->whatsapp_link)
                                <a href="{{ $package->whatsapp_link }}" target="_blank"
                                    class="flex items-center gap-2 text-[10px] text-green-600 font-black uppercase mt-2 hover:text-green-700">
                                    <i class="bi bi-whatsapp"></i> Grupo WhatsApp
                                </a>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-4 border-t border-gray-50">
                            <a href="{{ route('packages.show', $package) }}"
                                class="flex-1 bg-gray-900 text-white text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-eye"></i> Detalhes
                            </a>
                            <a href="{{ route('packages.edit', $package) }}"
                                class="w-10 h-10 bg-gray-50 text-gray-400 flex items-center justify-center rounded-xl hover:bg-orange-600 hover:text-white transition-all">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection