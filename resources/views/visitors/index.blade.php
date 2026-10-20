@extends('layouts.app')

@section('title', 'Visitantes - Portal Life Church')
@section('page-title', 'Gestão de Visitantes')
@section('page-subtitle', 'Acompanhamento e integração de novos visitantes')

@section('header-actions')
    <a href="https://chat.whatsapp.com/DxAf8sMvMDYDDhrIV1wRxC" target="_blank"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition-colors"
        title="Grupo WhatsApp">
        <i class="bi bi-whatsapp text-sm"></i>
    </a>
    <a href="{{ route('visitors.export', request()->all()) }}"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 transition-colors"
        title="Exportar">
        <i class="bi bi-file-earmark-excel text-emerald-600 text-sm"></i>
    </a>
    <a href="{{ route('visitors.create') }}"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-orange-700 text-white hover:bg-orange-800 shadow-sm transition-colors"
        title="Novo Visitante">
        <i class="bi bi-person-plus-fill text-sm"></i>
    </a>
@endsection

@section('content')
    <div x-data="{ 
        view: window.innerWidth < 768 ? 'grid' : 'list',
        selected: [],
        updateView() {
            if (window.innerWidth < 768 && this.view === 'list') {
                this.view = 'grid'; 
            }
        },
        toggleAll() {
            const allIds = {{ Js::from($visitors->pluck('id')) }};
            if (this.selected.length === allIds.length) {
                this.selected = [];
            } else {
                this.selected = allIds;
            }
        }
    }"
    x-init="$watch('view', value => localStorage.setItem('visitors_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('visitors_view') || 'list')"
    @resize.window.debounce.500ms="updateView()" class="space-y-4">

        <!-- Header da Página (Desktop) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Comunhão & Integração</span>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white tracking-tight">Gestão de Visitantes</h1>
                <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium">Acompanhamento e integração de novos visitantes da igreja.</p>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <a href="https://chat.whatsapp.com/DxAf8sMvMDYDDhrIV1wRxC" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-300 shadow-sm hover:bg-emerald-100 transition-colors">
                    <i class="bi bi-whatsapp text-xs"></i>
                    <span>Grupo Supervisores</span>
                </a>
                <a href="{{ route('visitors.export', request()->all()) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                    <i class="bi bi-file-earmark-excel text-emerald-600 text-xs"></i>
                    <span>Exportar Excel</span>
                </a>
                <a href="{{ route('visitors.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                    <i class="bi bi-person-plus-fill text-xs"></i>
                    <span>Novo Visitante</span>
                </a>
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Total Visitantes</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 tracking-tight">{{ $stats['total'] }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-people text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Pendentes</p>
                    <p class="mt-1 text-2xl font-black text-amber-700 tracking-tight">{{ $stats['pending'] }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <i class="bi bi-clock-history text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Contatados</p>
                    <p class="mt-1 text-2xl font-black text-blue-700 tracking-tight">{{ $stats['contacted'] }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <i class="bi bi-telephone text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Integrados</p>
                    <p class="mt-1 text-2xl font-black text-emerald-700 tracking-tight">{{ $stats['integrated'] }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i class="bi bi-check2-circle text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('visitors.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 items-end">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Buscar</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full rounded-lg border-gray-300 pl-7 pr-3 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Nome, telefone, bairro...">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Todos</option>
                            <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="contatado" {{ request('status') == 'contatado' ? 'selected' : '' }}>Contatado</option>
                            <option value="integrado" {{ request('status') == 'integrado' ? 'selected' : '' }}>Integrado</option>
                            <option value="sem_interesse" {{ request('status') == 'sem_interesse' ? 'selected' : '' }}>Sem Interesse</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Zona</label>
                        <select name="zone_id" class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Todas</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Data Início</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-orange-800 transition-colors shadow-sm">
                            <i class="bi bi-filter text-xs"></i> Filtrar
                        </button>
                        <a href="{{ route('visitors.index') }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                            <i class="bi bi-x-lg text-xs"></i>
                        </a>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <span class="text-xs text-gray-500 font-semibold">{{ $visitors->total() }} visitantes encontrados</span>
                    <div class="flex items-center rounded-lg border border-gray-200 p-0.5 bg-gray-50">
                        <button type="button" @click="view = 'list'"
                            :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-900'"
                            class="rounded-md px-2 py-1 text-xs transition-colors">
                            <i class="bi bi-list-ul"></i>
                        </button>
                        <button type="button" @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-900'"
                            class="rounded-md px-2 py-1 text-xs transition-colors">
                            <i class="bi bi-grid-fill"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-[10px] uppercase font-black tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            @if(auth()->user()->role === 'admin')
                                <th class="px-4 py-2.5 w-8">
                                    <input type="checkbox" @click="toggleAll()"
                                        :checked="selected.length === {{ $visitors->count() }} && selected.length > 0"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                </th>
                            @endif
                            <th class="px-4 py-2.5">Visitante</th>
                            <th class="px-4 py-2.5">Contato</th>
                            <th class="px-4 py-2.5">Data Visita</th>
                            <th class="px-4 py-2.5">Culto</th>
                            <th class="px-4 py-2.5">Zona</th>
                            <th class="px-4 py-2.5 text-center">Status</th>
                            <th class="px-4 py-2.5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($visitors as $visitor)
                            <tr class="hover:bg-gray-50/70 transition-colors"
                                :class="{'bg-orange-50/40': selected.includes({{ $visitor->id }})}">
                                @if(auth()->user()->role === 'admin')
                                    <td class="px-4 py-2.5">
                                        <input type="checkbox" value="{{ $visitor->id }}" x-model="selected"
                                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                    </td>
                                @endif
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-50 font-bold text-orange-700 text-xs shrink-0">
                                            {{ strtoupper(substr($visitor->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('visitors.show', $visitor) }}" class="font-bold text-gray-900 hover:text-orange-700 transition-colors">
                                                {{ $visitor->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-400 block">
                                                @if($visitor->age) {{ $visitor->age }} anos @endif
                                                @if($visitor->gender) • {{ ucfirst($visitor->gender) }} @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-gray-900 block truncate">{{ $visitor->phone ?: 'Sem telefone' }}</span>
                                    @if($visitor->neighborhood)
                                        <span class="text-[10px] text-gray-400 block truncate">{{ $visitor->neighborhood }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900 block">{{ $visitor->visit_date->format('d/m/Y') }}</span>
                                    <span class="text-[10px] text-gray-400 block">{{ $visitor->visit_date->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($visitor->service)
                                        <span class="text-gray-900 block truncate">{{ $visitor->service->service_type }}</span>
                                        <span class="text-[10px] text-gray-400 block">{{ $visitor->service->date->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Não informado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($visitor->zone)
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">
                                            {{ $visitor->zone->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">Pendente</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <x-visitor-status-badge :status="$visitor->contact_status" />
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('visitors.show', $visitor) }}"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                                            title="Ver detalhes">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                        @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'secretaria', 'pastor_senior', 'pastor', 'pastor_zona']))
                                            <a href="{{ route('visitors.edit', $visitor) }}"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-orange-300 hover:text-orange-700 hover:bg-orange-50 transition-colors"
                                                title="Editar">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                        @endif
                                        @if(auth()->user()->role === 'admin')
                                            <form id="list-delete-visitor-{{ $visitor->id }}"
                                                action="{{ route('visitors.destroy', $visitor) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="confirmDelete('list-delete-visitor-{{ $visitor->id }}')"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                    title="Eliminar">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-xs text-gray-400 italic">
                                    Nenhum visitante encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" style="display: none;" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($visitors as $visitor)
                <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:border-orange-300 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <x-visitor-status-badge :status="$visitor->contact_status" />
                            <span class="text-[10px] font-bold text-gray-400">{{ $visitor->visit_date->format('d/m/Y') }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 truncate">{{ $visitor->name }}</h4>
                        <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $visitor->phone ?: 'Sem telefone' }}</p>
                        @if($visitor->zone)
                            <p class="text-[10px] text-blue-700 font-bold truncate mt-0.5">{{ $visitor->zone->name }}</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5 pt-3 border-t border-gray-100 mt-3">
                        <a href="{{ route('visitors.show', $visitor) }}"
                            class="flex-1 text-center py-1 rounded-md bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs font-bold border border-gray-200">
                            Ver
                        </a>
                        @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'secretaria', 'pastor_senior', 'pastor', 'pastor_zona']))
                            <a href="{{ route('visitors.edit', $visitor) }}"
                                class="inline-flex h-6 w-6 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:text-orange-700">
                                <i class="bi bi-pencil text-[11px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-200 p-8 text-center text-xs text-gray-400 italic">
                    Nenhum visitante encontrado.
                </div>
            @endforelse
        </div>

        @if($visitors->hasPages())
            <div class="border-t border-gray-200 pt-3">
                {{ $visitors->links() }}
            </div>
        @endif

        <!-- Floating Bulk Bar -->
        <div x-show="selected.length > 0" x-cloak
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 rounded-xl bg-gray-900 text-white px-4 py-2.5 shadow-xl flex items-center gap-3 border border-gray-700 text-xs">
            <span class="font-bold"><span x-text="selected.length"></span> selecionado(s)</span>
            <div class="h-4 w-px bg-gray-700"></div>
            @if(auth()->user()->role === 'admin')
                <form method="POST" action="{{ route('visitors.bulk-delete') }}" onsubmit="return confirm('Tem certeza que deseja excluir os visitantes selecionados?')" class="inline">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="visitor_ids[]" :value="id">
                    </template>
                    <button type="submit" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-red-700">
                        <i class="bi bi-trash text-xs"></i> Excluir
                    </button>
                </form>
            @endif
            <button @click="selected = []" class="text-gray-400 hover:text-white ml-1">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
@endsection
