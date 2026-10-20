@extends('layouts.app')

@section('title', 'Detalhes do Membro - Portal Life Church')
@section('page-title', $member->name)
@section('page-subtitle', 'Informações completas do membro')

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('members.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
            <span>Voltar</span>
        </a>
        @if($userRole !== 'secretaria' && $member->id !== auth()->user()->id)
            <a href="{{ route('members.edit', $member) }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
                title="Editar perfil">
                <i class="bi bi-pencil-square text-xs text-orange-700"></i>
                <span class="hidden sm:inline">Editar</span>
            </a>
            <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Tem certeza que deseja excluir este membro? Esta ação não pode ser revertida.')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors"
                    title="Eliminar Membro">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            </form>
        @endif
        <a href="{{ route('contributions.create', ['user_id' => $member->id]) }}"
            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
            <i class="bi bi-plus-lg text-xs"></i>
            <span class="hidden sm:inline">Nova Oferta</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Executive Banner -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 font-black text-orange-700 text-lg border border-orange-200/50">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $member->name }}</h1>
                            @if($member->is_active)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Ativo</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-bold text-red-600">Inativo</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mt-0.5">
                            <span class="inline-flex items-center gap-1"><i class="bi bi-envelope text-gray-400"></i> {{ $member->email }}</span>
                            @if($member->phone)
                                <span class="inline-flex items-center gap-1"><i class="bi bi-telephone text-gray-400"></i> {{ $member->phone }}</span>
                            @endif
                            <span class="inline-flex items-center gap-1"><i class="bi bi-calendar3 text-gray-400"></i> Desde {{ $member->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($member->role === 'lider_celula')
                        <span class="inline-flex items-center rounded-md bg-purple-50 px-2.5 py-1 text-xs font-bold text-purple-700 border border-purple-200">
                            <i class="bi bi-person-badge mr-1"></i> Líder de Célula
                        </span>
                    @elseif($member->role === 'timoteo')
                        <span class="inline-flex items-center rounded-md bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-700 border border-orange-200">
                            <i class="bi bi-star mr-1"></i> Timóteo
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 border border-blue-200">
                            <i class="bi bi-person mr-1"></i> Membro
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Total Contribuído</p>
                    <p class="mt-1 text-2xl font-black text-emerald-700 tracking-tight">
                        {{ number_format($member->contributions->where('status', 'verificada')->sum('amount'), 0, ',', '.') }} <span class="text-xs font-bold text-gray-400">MT</span>
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i class="bi bi-cash-stack text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Doações Verificadas</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 tracking-tight">
                        {{ $member->contributions->where('status', 'verificada')->count() }}
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <i class="bi bi-check2-all text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Compromissos</p>
                    <p class="mt-1 text-2xl font-black text-purple-700 tracking-tight">
                        {{ $member->commitments->whereNull('end_date')->count() }}
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                    <i class="bi bi-award text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Célula</p>
                    <p class="mt-1 text-sm font-black text-gray-900 truncate">
                        {{ $member->cell->name ?? 'Sem célula' }}
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-house-door text-lg"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Coluna Principal (Compromissos & Histórico) -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Compromissos -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 px-4 py-3 bg-gray-50/60">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Compromissos do Reino</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse($member->commitments as $commitment)
                            <div class="flex items-center justify-between p-3 rounded-lg border {{ $commitment->end_date === null ? 'border-orange-200 bg-orange-50/40' : 'border-gray-200 bg-gray-50/50' }}">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-gray-200 text-orange-700">
                                        <i class="bi bi-shield-check text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900">{{ $commitment->package->name ?? 'Compromisso Personalizado' }}</p>
                                        <p class="text-[11px] text-gray-400">Iniciado em {{ $commitment->start_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $commitment->end_date === null ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $commitment->end_date === null ? 'Ativo' : 'Encerrado' }}
                                </span>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-gray-400 italic">Nenhum compromisso registrado.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Histórico de Ofertas Recentes -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 bg-gray-50/60">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Histórico de Ofertas</h3>
                        <a href="{{ route('contributions.index', ['user_id' => $member->id]) }}" class="text-xs font-bold text-orange-700 hover:underline">
                            Ver Todas
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-[10px] uppercase font-black tracking-wider text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2.5">Data</th>
                                    <th class="px-4 py-2.5">Valor</th>
                                    <th class="px-4 py-2.5 text-center">Estado</th>
                                    <th class="px-4 py-2.5 text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($member->contributions as $contribution)
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="px-4 py-2.5 font-bold text-gray-900">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2.5 font-bold text-emerald-700">{{ number_format($contribution->amount, 0, ',', '.') }} MT</td>
                                        <td class="px-4 py-2.5 text-center">
                                            @if($contribution->status === 'verificada')
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Verificada</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">{{ $contribution->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 text-right">
                                            <a href="{{ route('contributions.show', $contribution) }}" class="inline-flex h-6 w-6 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:text-gray-900 hover:bg-gray-50">
                                                <i class="bi bi-chevron-right text-[11px]"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-xs text-gray-400 italic">Nenhuma oferta registrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral (Hierarquia Estrutural) -->
            <div class="space-y-5">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Alocação Estrutural</h3>
                    @if($member->cell)
                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between p-2.5 rounded-lg border border-gray-200 bg-gray-50/50">
                                <div>
                                    <span class="text-[10px] font-bold uppercase text-gray-400 block">Célula</span>
                                    <a href="{{ route('cells.show', $member->cell) }}" class="font-bold text-gray-900 hover:text-orange-700">
                                        {{ $member->cell->name }}
                                    </a>
                                </div>
                                <span class="rounded px-1.5 py-0.5 text-[9px] font-black uppercase {{ $member->cell->type_badge_classes }}">
                                    {{ $member->cell->type_label }}
                                </span>
                            </div>

                            @if($member->cell->supervision)
                                <div class="p-2.5 rounded-lg border border-gray-200 bg-gray-50/50">
                                    <span class="text-[10px] font-bold uppercase text-gray-400 block">Supervisão</span>
                                    <a href="{{ route('supervisions.show', $member->cell->supervision) }}" class="font-bold text-gray-900 hover:text-orange-700">
                                        {{ $member->cell->supervision->name }}
                                    </a>
                                </div>
                            @endif

                            @if($member->cell->supervision && $member->cell->supervision->zone)
                                <div class="p-2.5 rounded-lg border border-gray-200 bg-gray-50/50">
                                    <span class="text-[10px] font-bold uppercase text-gray-400 block">Zona Pastoral</span>
                                    <a href="{{ route('zones.show', $member->cell->supervision->zone) }}" class="font-bold text-gray-900 hover:text-orange-700">
                                        {{ $member->cell->supervision->zone->name }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                            Membro sem alocação de célula definida.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
