@extends('layouts.app')

@section('title', 'Gestão de Pacotes - Portal Life Church')
@section('page-title','Gestão de Pacotes')

@section('content')
    <div x-data="{ 
                        view: window.innerWidth < 768 ? 'grid' : 'list',
                        updateView() {
                            if (window.innerWidth < 768 && this.view === 'list') {
                                this.view = 'grid';
                            }
                        }
                    }"
        x-init="$watch('view', value => localStorage.setItem('packages_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('packages_view') || 'list')"
        @resize.window.debounce.500ms="updateView()">
        @section('header-actions')
            @if(!auth()->user()->isResponsavelPacote())
                <div class="md:hidden">
                    <a href="{{ route('packages.create') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-orange-700 p-2 text-white shadow-sm hover:bg-orange-800 transition">
                        <i class="bi bi-plus-lg text-base"></i>
                    </a>
                </div>
            @endif
        @endsection
        <div class="space-y-5">
            <!-- Page Header -->
            <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
                <div>
                    <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Projeto Edificar</p>
                    <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Pacotes de Compromisso</h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Gestão de planos e faixas de contribuição</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                        <button @click="view = 'list'"
                            :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                            class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 text-xs font-bold">
                            <i class="bi bi-list-ul"></i> <span>Lista</span>
                        </button>
                        <button @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'"
                            class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 text-xs font-bold">
                            <i class="bi bi-grid-fill"></i> <span>Grid</span>
                        </button>
                    </div>
                    @if(!auth()->user()->isResponsavelPacote())
                        <a href="{{ route('packages.create') }}"
                            class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                            <i class="bi bi-plus-lg"></i> <span>Novo Pacote</span>
                        </a>
                    @endif
                </div>
            </header>

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Packages List -->
            <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-compact">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left">Nome do Pacote</th>
                                <th class="px-4 py-3 text-left">Intervalo de Valores</th>
                                <th class="px-4 py-3 text-center">Membros Ativos</th>
                                <th class="px-4 py-3 text-center">Responsável</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($packages as $package)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-xs sm:text-sm font-black text-gray-900 leading-tight group-hover:text-orange-700 transition-colors">
                                                {{ $package->name }}
                                            </span>
                                            @if($package->whatsapp_link)
                                                <a href="{{ $package->whatsapp_link }}" target="_blank"
                                                    class="flex items-center gap-1 text-[10px] text-green-600 font-bold uppercase mt-0.5 hover:text-green-700">
                                                    <i class="bi bi-whatsapp"></i> Grupo WhatsApp
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs sm:text-sm font-black text-emerald-700">
                                                {{ number_format($package->min_amount, 0, ',', '.') }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-black">→</span>
                                            <span class="text-xs sm:text-sm font-black text-emerald-700">
                                                @if($package->max_amount)
                                                    {{ number_format($package->max_amount, 0, ',', '.') }}
                                                @else
                                                    <i class="bi bi-infinity text-base"></i>
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase ml-0.5">MT</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-black text-gray-900">
                                            {{ $package->getActiveMembersCount() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-xs font-bold text-gray-700">
                                            {{ $package->getResponsavelName() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border 
                                                {{ $package->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                            {{ $package->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div
                                            class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('packages.show', $package) }}" title="Ver detalhes"
                                                class="action-icon bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white shadow-sm">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            @if(!auth()->user()->isResponsavelPacote())
                                                <a href="{{ route('packages.edit', $package) }}" title="Editar"
                                                    class="action-icon bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white shadow-sm">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('contributions.create') }}?package_id={{ $package->id }}"
                                                class="action-icon bg-green-50 text-green-600 hover:bg-green-600 hover:text-white shadow-sm"
                                                title="Nova Contribuição">
                                                <i class="bi bi-plus-lg"></i>
                                            </a>
                                            @if(!auth()->user()->isResponsavelPacote())
                                                <form action="{{ route('packages.destroy', $package) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="confirmDelete('Tem certeza que deseja excluir o pacote {{ $package->name }}?').then(result => { if(result.isConfirmed) this.closest('form').submit(); })"
                                                        class="action-icon bg-red-50 text-red-600 hover:bg-red-600 hover:text-white shadow-sm font-black"
                                                        title="Excluir">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
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
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($packages as $package)
                    <div
                        class="bg-white p-4 sm:p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col group hover:border-gray-300 transition-all relative compact-card">
                        <div class="absolute top-4 right-4">
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border 
                                    {{ $package->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                {{ $package->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>

                        <div
                            class="w-10 h-10 rounded-lg bg-orange-50 text-orange-700 border border-orange-200 flex items-center justify-center font-black text-base mb-3">
                            {{ strtoupper(substr($package->name, 0, 1)) }}
                        </div>

                        <div class="mb-3">
                            <h4
                                class="text-sm sm:text-base font-black text-gray-900 leading-tight mb-1 group-hover:text-orange-700 transition-colors line-clamp-1">
                                {{ $package->name }}</h4>
                            <div class="flex items-center gap-1.5 text-emerald-700 font-black text-xs">
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

                        <div class="space-y-2 mb-4 flex-1 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase text-gray-500">Inscritos</span>
                                <span class="text-sm font-black text-gray-900">{{ $package->getActiveMembersCount() }}</span>
                            </div>
                            <div class="flex flex-col border-t border-gray-200 pt-1.5">
                                <span class="text-[9px] font-bold uppercase text-gray-400">Responsável</span>
                                <span
                                    class="text-xs font-bold text-gray-700 line-clamp-1">{{ $package->getResponsavelName() }}</span>
                            </div>
                            @if($package->whatsapp_link)
                                <a href="{{ $package->whatsapp_link }}" target="_blank"
                                    class="hidden sm:flex items-center gap-1.5 text-[10px] text-green-600 font-bold uppercase mt-1 hover:text-green-700">
                                    <i class="bi bi-whatsapp"></i> Grupo WhatsApp
                                </a>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('packages.show', $package) }}"
                                class="flex-1 bg-gray-900 text-white text-center py-2 rounded-lg font-bold text-xs hover:bg-orange-700 transition-all flex items-center justify-center gap-1.5">
                                <i class="bi bi-eye"></i> Detalhes
                            </a>
                            @if(!auth()->user()->isResponsavelPacote())
                                <a href="{{ route('packages.edit', $package) }}"
                                    class="w-8 h-8 bg-gray-100 text-gray-600 flex items-center justify-center rounded-lg hover:bg-orange-600 hover:text-white transition-all">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($packages->hasPages())
            <div class="mt-8">
                {{ $packages->links() }}
            </div>
        @endif
    </div>
@endsection
