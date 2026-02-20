@extends('layouts.app')

@section('title', 'Lista de Membros')
@section('page-title', 'Gestão de Membros')

@section('page-subtitle')
    @if($userRole === 'lider_celula')
        Membros da sua célula
    @elseif($userRole === 'supervisor')
        Membros da sua supervisão
    @elseif($userRole === 'pastor_zona')
        Membros da sua zona
    @else
        Todos os membros da igreja
    @endif
@endsection

@section('header-actions')
    @if($userRole !== 'secretaria')
        <a href="{{ route('members.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-white transition hover:bg-blue-700">
            <i class="bi bi-person-plus-fill"></i>
            <span>Novo Membro</span>
        </a>
    @endif
@endsection

@section('content')
    <div class="space-y-6"
        x-data="{
            view: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('members_view') || 'list'),
            selected: [],
            toggleAll() {
                const allIds = {{ Js::from($members->pluck('id')->values()) }};
                this.selected = this.selected.length === allIds.length ? [] : allIds;
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('members_view', value))">

        <div x-show="selected.length > 0" x-transition class="sticky top-20 z-20">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-lg">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm font-bold text-gray-700">
                        <span class="rounded-lg bg-blue-100 px-2 py-1 text-blue-700" x-text="selected.length"></span>
                        selecionado(s)
                    </p>

                    <div class="flex items-center gap-2">
                        <button @click="selected = []" type="button"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-bold uppercase tracking-wider text-gray-600 transition hover:bg-gray-50">
                            Cancelar
                        </button>

                        @if($userRole !== 'secretaria')
                            <form method="POST" action="{{ route('members.bulk-destroy') }}"
                                onsubmit="return confirm('Tem certeza que deseja excluir os membros selecionados?')">
                                @csrf
                                <template x-for="id in selected" :key="id">
                                    <input type="hidden" name="selected_ids[]" :value="id">
                                </template>
                                <button type="submit"
                                    class="rounded-lg bg-red-600 px-3 py-2 text-xs font-black uppercase tracking-wider text-white transition hover:bg-red-700">
                                    Excluir
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('members.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="relative lg:col-span-5">
                    <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, email ou telefone"
                        class="w-full rounded-xl border border-gray-300 py-2.5 pl-10 pr-3 text-sm text-gray-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                @if($userRole !== 'lider_celula' && $availableCells->count() > 1)
                    <div class="lg:col-span-3">
                        <select name="cell_id"
                            class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <option value="">Todas as Células</option>
                            @foreach($availableCells as $cell)
                                <option value="{{ $cell->id }}" {{ request('cell_id') == $cell->id ? 'selected' : '' }}>
                                    {{ $cell->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-center gap-2 lg:col-span-4 lg:justify-end">
                    <button type="submit"
                        class="rounded-xl bg-gray-900 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-white transition hover:bg-gray-800">
                        Filtrar
                    </button>

                    @if(request('search') || request('cell_id'))
                        <a href="{{ route('members.index') }}"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-gray-700 transition hover:bg-gray-50">
                            Limpar
                        </a>
                    @endif

                    <div class="hidden rounded-xl border border-gray-300 p-1 md:flex">
                        <button @click.prevent="view = 'list'" type="button"
                            :class="view === 'list' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-700'"
                            class="rounded-lg px-3 py-2 text-xs font-bold">
                            <i class="bi bi-list-ul"></i>
                        </button>
                        <button @click.prevent="view = 'grid'" type="button"
                            :class="view === 'grid' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-700'"
                            class="rounded-lg px-3 py-2 text-xs font-bold">
                            <i class="bi bi-grid-fill"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <p class="text-xs font-black uppercase tracking-wider text-blue-700">Total</p>
                <p class="mt-2 text-3xl font-black text-blue-900">{{ $members->total() }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
                <p class="text-xs font-black uppercase tracking-wider text-emerald-700">Ativos (página)</p>
                <p class="mt-2 text-3xl font-black text-emerald-900">{{ $members->getCollection()->where('is_active', true)->count() }}</p>
            </div>
            <div class="rounded-2xl border border-purple-100 bg-purple-50 p-5">
                <p class="text-xs font-black uppercase tracking-wider text-purple-700">Células</p>
                <p class="mt-2 text-3xl font-black text-purple-900">{{ $availableCells->count() }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5">
                <p class="text-xs font-black uppercase tracking-wider text-amber-700">Líderes (página)</p>
                <p class="mt-2 text-3xl font-black text-amber-900">{{ $members->getCollection()->where('role', 'lider_celula')->count() }}</p>
            </div>
        </div>

        <div x-show="view === 'list'" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" @click="toggleAll" class="h-4 w-4 rounded border-gray-300 text-blue-600">
                            </th>
                            <th class="px-4 py-3 text-left">Nome</th>
                            <th class="px-4 py-3 text-left">Papel</th>
                            <th class="px-4 py-3 text-left">Contacto</th>
                            <th class="px-4 py-3 text-left">Célula/Zona</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($members as $member)
                            <tr class="text-sm text-gray-700 hover:bg-gray-50"
                                :class="selected.includes({{ $member->id }}) ? 'bg-blue-50' : ''">
                                <td class="px-4 py-3">
                                    <input type="checkbox" value="{{ $member->id }}" x-model="selected"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $member->name }}</div>
                                    <div class="text-xs text-gray-400">#{{ $member->id }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($member->role === 'lider_celula')
                                        <span class="rounded-full bg-purple-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-purple-700">Líder</span>
                                    @else
                                        <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-blue-700">Membro</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs text-gray-700">{{ $member->email }}</div>
                                    <div class="text-xs text-gray-400">{{ $member->phone ?: 'Sem telefone' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs font-semibold text-gray-700">{{ $member->cell->name ?? 'Sem célula' }}</div>
                                    <div class="text-xs text-gray-400">{{ $member->cell->supervision->zone->name ?? 'Sem zona' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($member->is_active)
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-700">Ativo</span>
                                    @else
                                        <span class="rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-red-700">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('members.show', $member) }}" title="Detalhes"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-blue-600 hover:bg-blue-600 hover:text-white">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($userRole !== 'secretaria')
                                            <a href="{{ route('members.edit', $member) }}" title="Editar"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-amber-500 hover:bg-amber-500 hover:text-white">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center text-gray-400">
                                    Nenhum membro encontrado para os filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="view === 'grid'" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @forelse($members as $member)
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                    :class="selected.includes({{ $member->id }}) ? 'ring-2 ring-blue-500 border-blue-300' : ''">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-900 text-sm font-black text-white">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="line-clamp-1 text-sm font-black text-gray-900">{{ $member->name }}</h3>
                                <p class="text-[11px] text-gray-400">#{{ $member->id }}</p>
                            </div>
                        </div>
                        <input type="checkbox" value="{{ $member->id }}" x-model="selected"
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600">
                    </div>

                    <div class="space-y-2 text-xs text-gray-600">
                        <p><strong class="text-gray-800">Email:</strong> {{ $member->email }}</p>
                        <p><strong class="text-gray-800">Telefone:</strong> {{ $member->phone ?: 'Sem telefone' }}</p>
                        <p><strong class="text-gray-800">Célula:</strong> {{ $member->cell->name ?? 'Sem célula' }}</p>
                        <p><strong class="text-gray-800">Zona:</strong> {{ $member->cell->supervision->zone->name ?? 'Sem zona' }}</p>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        @if($member->is_active)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-700">Ativo</span>
                        @else
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-red-700">Inativo</span>
                        @endif

                        <span class="rounded-full {{ $member->role === 'lider_celula' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-2.5 py-1 text-[10px] font-black uppercase tracking-wider">
                            {{ $member->role === 'lider_celula' ? 'Líder' : 'Membro' }}
                        </span>
                    </div>

                    <div class="mt-4 grid {{ $userRole !== 'secretaria' ? 'grid-cols-2' : 'grid-cols-1' }} gap-2 border-t border-gray-100 pt-4">
                        <a href="{{ route('members.show', $member) }}"
                            class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-white transition hover:bg-blue-600">
                            Ver
                        </a>
                        @if($userRole !== 'secretaria')
                            <a href="{{ route('members.edit', $member) }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-gray-700 transition hover:border-amber-500 hover:text-amber-600">
                                Editar
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-gray-50 py-16 text-center text-gray-500">
                    Nenhum membro encontrado.
                </div>
            @endforelse
        </div>

        @if($members->hasPages())
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                {{ $members->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
