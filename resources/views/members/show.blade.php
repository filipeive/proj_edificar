@extends('layouts.app')

@section('title', 'Detalhes do Membro')
@section('page-title', $member->name)
@section('page-subtitle', 'Informações completas do membro')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        @if($userRole !== 'secretaria' && $member->id !== auth()->user()->id)
            <a href="{{ route('members.edit', $member) }}"
                class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
                title="Editar perfil">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endif
        <a href="{{ route('contributions.create', ['user_id' => $member->id]) }}"
            class="action-icon text-gray-600 hover:text-green-600 hover:bg-green-50"
            title="Nova oferta">
            <i class="bi bi-plus-circle"></i>
        </a>
        <a href="{{ route('members.index') }}"
            class="action-icon text-gray-600 hover:text-gray-600 hover:bg-gray-50"
            title="Lista de Membros">
            <i class="bi bi-list"></i>
        </a>
        {{-- delete --}}
        <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Tem certeza que deseja excluir este membro? Esta ação não pode ser revertida.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="action-icon text-red-600 hover:text-red-600 hover:bg-red-50"
                title="Eliminar Membro">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="space-y-8">
        <!-- Desktop Actions -->
        <div class="hidden md:flex items-center justify-end gap-3">
            @if($userRole !== 'secretaria' && $member->id !== auth()->user()->id)
                <a href="{{ route('members.edit', $member) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs font-black uppercase tracking-wider text-gray-700 transition hover:border-amber-500 hover:text-amber-600">
                    <i class="bi bi-pencil-square"></i> Editar Perfil
                </a>
                <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Tem certeza que deseja excluir este membro? Esta ação não pode ser revertida.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-red-600 transition hover:border-red-600 hover:bg-red-600 hover:text-white">
                        <i class="bi bi-trash"></i> Eliminar Membro
                    </button>
                </form>
            @endif
            <a href="{{ route('contributions.create', ['user_id' => $member->id]) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-white transition hover:bg-blue-700 shadow-lg shadow-blue-100">
                <i class="bi bi-plus-lg"></i> Nova Oferta
            </a>
        </div>

        <!-- Header & Profile Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Profile Info -->
            <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-10 flex flex-col md:flex-row items-center gap-6 md:gap-8">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-[2rem] md:rounded-[2.5rem] bg-blue-50 text-blue-600 flex items-center justify-center font-black text-4xl md:text-5xl shadow-lg shadow-blue-50">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
                <div class="space-y-3 text-center md:text-left flex-1">
                    <div class="flex flex-col md:flex-row items-center gap-2 md:gap-3">
                        <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tighter">{{ $member->name }}</h1>
                        @if($member->is_active)
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">Ativo</span>
                        @else
                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest">Inativo</span>
                        @endif
                        
                        @if($member->role === 'lider_celula')
                            <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100">Líder</span>
                        @elseif($member->role === 'timoteo')
                            <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-100">Timóteo</span>
                        @else
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Membro</span>
                        @endif
                    </div>
                    <p class="text-gray-400 font-medium flex items-center justify-center md:justify-start gap-2 text-sm">
                        <i class="bi bi-envelope-fill"></i> {{ $member->email }}
                    </p>
                    <div class="flex flex-col md:flex-row justify-center md:justify-start gap-4 md:gap-6 pt-2">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Membro desde</span>
                            <span class="text-sm font-bold text-gray-700">{{ $member->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="hidden md:block w-px bg-gray-100"></div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Contacto</span>
                            <span class="text-sm font-bold text-gray-700">{{ $member->phone ?? 'Indisponível' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="lg:col-span-2 grid grid-cols-4 gap-4">
                <!-- Total Contribuído -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 flex flex-col justify-center text-center group hover:bg-green-50 transition-colors">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.15em] mb-2 group-hover:text-green-400">Total Contribuído</p>
                    <p class="text-3xl md:text-4xl font-black text-green-600 tracking-tighter">
                        {{ number_format($member->contributions->where('status', 'verificada')->sum('amount'), 0, ',', '.') }}<span class="text-xs ml-1 uppercase">MT</span>
                    </p>
                    <p class="text-[10px] font-bold text-gray-400 mt-2 uppercase tracking-widest">{{ $member->contributions->where('status', 'verificada')->count() }} Doações</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Compromissos Card -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="text-xl font-black text-gray-900 flex items-center gap-3">
                            <i class="bi bi-shield-check text-blue-600"></i>
                            Compromissos do Reino
                        </h2>
                    </div>
                    <div class="p-8 space-y-4">
                        @forelse($member->commitments as $commitment)
                            <div class="flex items-center justify-between p-6 rounded-[2rem] {{ $commitment->end_date === null ? 'bg-blue-50 border border-blue-100' : 'bg-gray-50 opacity-60' }}">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-white text-blue-600 flex items-center justify-center shadow-sm">
                                        <i class="bi bi-star-fill text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg font-black text-gray-900 tracking-tight">{{ $commitment->package->name ?? 'Compromisso Personalizado' }}</p>
                                        <p class="text-xs font-medium text-gray-400">Iniciado em {{ $commitment->start_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <span class="px-4 py-2 {{ $commitment->end_date === null ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }} rounded-xl text-[10px] font-black uppercase tracking-widest">
                                    {{ $commitment->end_date === null ? 'Ativo' : 'Encerrado' }}
                                </span>
                            </div>
                        @empty
                            <div class="py-10 text-center text-gray-400 italic font-medium">Nenhum compromisso histórico.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Contributions Table -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                        <h2 class="text-xl font-black text-gray-900 flex items-center gap-3">
                            <i class="bi bi-clock-history text-green-600"></i>
                            Histórico de Ofertas
                        </h2>
                        <a href="{{ route('contributions.index', ['user_id' => $member->id]) }}" class="text-xs font-black text-blue-600 uppercase tracking-widest hover:underline">Ver Todas</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                                    <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor</th>
                                    <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                                    <th class="px-8 py-4 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($member->contributions as $contribution)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5 text-sm font-bold text-gray-900">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                                        <td class="px-8 py-5 text-sm font-black text-green-600">{{ number_format($contribution->amount, 0, ',', '.') }} MT</td>
                                        <td class="px-8 py-5 text-center">
                                            @if($contribution->status === 'verificada')
                                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-tighter">Ok</span>
                                            @else
                                                <span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-[9px] font-black uppercase tracking-tighter">{{ $contribution->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <a href="{{ route('contributions.show', $contribution) }}" class="text-gray-300 hover:text-blue-600 transition-colors">
                                                <i class="bi bi-chevron-right text-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Hierarchy Column -->
            <div class="space-y-6">
                <!-- Location Info -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Alocação Estrutural</h3>
                    @if($member->cell)
                        <div class="space-y-4">
                            <!-- Célula -->
                            <div class="flex items-center gap-4 group cursor-pointer" onclick="window.location='{{ route('cells.show', $member->cell) }}'">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Célula</p>
                                    <p class="text-sm font-black text-gray-900">{{ $member->cell->name }}</p>
                                </div>
                            </div>
                            <!-- Supervisão -->
                            @if($member->cell->supervision)
                                <div class="flex items-center gap-4 group cursor-pointer" onclick="window.location='{{ route('supervisions.show', $member->cell->supervision) }}'">
                                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-black group-hover:bg-purple-600 group-hover:text-white transition-all">
                                        <i class="bi bi-diagram-3-fill"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Supervisão</p>
                                        <p class="text-sm font-black text-gray-900">{{ $member->cell->supervision->name }}</p>
                                    </div>
                                </div>
                            @endif
                            <!-- Zona -->
                            @if($member->cell->supervision && $member->cell->supervision->zone)
                                <div class="flex items-center gap-4 group cursor-pointer" onclick="window.location='{{ route('zones.show', $member->cell->supervision->zone) }}'">
                                    <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center font-black group-hover:bg-green-600 group-hover:text-white transition-all">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Zona Pastoral</p>
                                        <p class="text-sm font-black text-gray-900">{{ $member->cell->supervision->zone->name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-6 text-center text-red-400 italic text-sm font-medium">Sem alocação definida.</div>
                    @endif
                </div>

                <!-- Support Card -->
                <div class="bg-gradient-to-br from-blue-900 to-indigo-900 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden">
                    <div class="relative z-10 space-y-5">
                        <p class="text-[10px] font-black text-blue-300 uppercase tracking-[0.2em]">Assistance</p>
                        <p class="text-sm font-medium leading-relaxed">Este membro faz parte do corpo de cristo. Ajude-o a permanecer firme em sua caminhada.</p>
                        <button class="w-full py-4 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all">
                            Enviar Mensagem
                        </button>
                    </div>
                    <i class="bi bi-chat-heart-fill absolute -right-4 -bottom-4 text-9xl text-white opacity-5"></i>
                </div>
            </div>
        </div>
    </div>
@endsection
