@extends('layouts.app')

@section('title', 'Atividades - ' . $user->name)
@section('page-title', 'Histórico de Atividades')
@section('page-subtitle', 'Registo completo de ações realizadas por ' . $user->name)

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('users.show', $user) }}"
                    class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center hover:bg-gray-50 transition-all">
                    <i class="bi bi-arrow-left text-gray-600 text-lg"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $user->name }}</h1>
                    <p class="text-sm font-bold text-gray-400 mt-1">{{ $user->email }}</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
                <form method="GET" class="w-full sm:w-72">
                    <div class="relative">
                        <input type="text" name="q" value="{{ $search ?? '' }}"
                            placeholder="Pesquisar atividades..."
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </form>
                <span
                    class="px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-200">
                    {{ str_replace('_', ' ', $user->role) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Sidebar Stats --}}
            <div class="space-y-6">
                {{-- Activity Summary Card --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-5 flex items-center gap-2">
                        <i class="bi bi-graph-up text-blue-500"></i>
                        Resumo
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-medium">Total de Atividades</span>
                            <span class="text-lg font-black text-gray-900">{{ $user->activities()->count() }}</span>
                        </div>
                        <div class="h-px bg-gray-100"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-medium">Última Atividade</span>
                            <span class="text-sm font-bold text-gray-700">
                                {{ $user->activities()->first() ? $user->activities()->first()->created_at->diffForHumans() : 'Nenhuma' }}
                            </span>
                        </div>
                        <div class="h-px bg-gray-100"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-medium">Último Login</span>
                            <span class="text-sm font-bold text-gray-700">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-5 flex items-center gap-2">
                        <i class="bi bi-lightning-fill text-amber-500"></i>
                        Ações Rápidas
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('users.show', $user) }}"
                            class="flex items-center gap-3 px-4 py-3 bg-gray-50 hover:bg-blue-50 rounded-xl transition-all group">
                            <i class="bi bi-person text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                            <span class="text-sm font-bold text-gray-600 group-hover:text-blue-600 transition-colors">Ver
                                Perfil</span>
                        </a>
                        <a href="{{ route('users.edit', $user) }}"
                            class="flex items-center gap-3 px-4 py-3 bg-gray-50 hover:bg-orange-50 rounded-xl transition-all group">
                            <i class="bi bi-pencil text-gray-400 group-hover:text-orange-500 transition-colors"></i>
                            <span
                                class="text-sm font-bold text-gray-600 group-hover:text-orange-600 transition-colors">Editar
                                Utilizador</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Activity Timeline --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="bi bi-clock-history"></i>
                            Histórico de Atividades
                        </h2>
                    </div>

                    @if($activities->count() > 0)
                        <div class="divide-y divide-gray-50">
                            @foreach($activities as $activity)
                                <div class="p-6 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-start gap-4">
                                        {{-- Icon --}}
                                        <div
                                            class="w-10 h-10 rounded-xl bg-{{ $activity->badge_color }}-50 flex items-center justify-center flex-shrink-0">
                                            <i class="bi {{ $activity->icon }} text-{{ $activity->badge_color }}-500"></i>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-3 mb-1">
                                                <span
                                                    class="px-2.5 py-0.5 bg-{{ $activity->badge_color }}-50 text-{{ $activity->badge_color }}-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-{{ $activity->badge_color }}-100">
                                                    {{ $activity->action }}
                                                </span>
                                                <span class="text-xs text-gray-400 font-medium">
                                                    {{ $activity->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-700 font-medium">
                                                {{ $activity->description ?? 'Sem descrição' }}
                                            </p>
                                            @if($activity->model_type && $activity->model_id)
                                                <p class="text-xs text-gray-400 mt-1">
                                                    <i class="bi bi-database me-1"></i>
                                                    {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}
                                                </p>
                                            @endif
                                            @if($activity->ip_address)
                                                <p class="text-xs text-gray-400 mt-1">
                                                    <i class="bi bi-globe me-1"></i>
                                                    IP: {{ $activity->ip_address }}
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Timestamp --}}
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-xs text-gray-400 font-bold">{{ $activity->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($activities->hasPages())
                            <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs text-gray-500 font-medium">
                                        Mostrando {{ $activities->firstItem() }} a {{ $activities->lastItem() }}
                                        de {{ $activities->total() }} atividades
                                    </p>
                                    {{ $activities->links() }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="p-16 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-clock-history text-3xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-400 mb-2">Nenhuma atividade registada</h3>
                            <p class="text-sm text-gray-400">Este utilizador ainda não realizou nenhuma atividade no sistema.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
