@extends('layouts.app')

@section('title', 'Editar Casamento')
@section('page-title', 'Editar Casamento')
@section('page-subtitle', 'Atualizar detalhes do evento')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('weddings.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para o Calendário</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('weddings.show', $wedding) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
                <i class="bi bi-eye"></i>
                <span>Ver Detalhes</span>
            </a>
            <form id="delete-wedding-form" action="{{ route('weddings.destroy', $wedding) }}" method="POST" class="inline"
                onsubmit="return confirm('Tem certeza que deseja excluir este casamento?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-2.5 py-1 text-xs font-bold text-red-600 hover:bg-red-50 transition">
                    <i class="bi bi-trash"></i>
                    <span>Excluir</span>
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('weddings.update', $wedding) }}" method="POST" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Edição de Casamento</p>
            <h2 class="text-sm font-bold text-gray-900">{{ $wedding->groom_name }} & {{ $wedding->bride_name }}</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <!-- Os Noivos -->
            <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-3 sm:p-4">
                <p class="text-[10px] font-black uppercase tracking-wider text-orange-700 mb-3">Os Noivos</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome do Noivo <span class="text-red-500">*</span></label>
                        <input type="text" name="groom_name" value="{{ old('groom_name', $wedding->groom_name) }}" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        @error('groom_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome da Noiva <span class="text-red-500">*</span></label>
                        <input type="text" name="bride_name" value="{{ old('bride_name', $wedding->bride_name) }}" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        @error('bride_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Dados da Cerimônia -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Data da Cerimônia <span class="text-red-500">*</span></label>
                    <input type="date" name="date" required value="{{ old('date', $wedding->date->format('Y-m-d')) }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Horário</label>
                    <input type="time" name="time" value="{{ old('time', $wedding->time ? $wedding->time->format('H:i') : '') }}"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    @error('time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                        <option value="scheduled" {{ old('status', $wedding->status) == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                        <option value="completed" {{ old('status', $wedding->status) == 'completed' ? 'selected' : '' }}>Realizado</option>
                        <option value="cancelled" {{ old('status', $wedding->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Local da Cerimônia</label>
                    <input type="text" name="location" value="{{ old('location', $wedding->location) }}" placeholder="Ex: Templo Sede"
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Padrinhos / Testemunhas</label>
                <textarea name="godparents" rows="2" placeholder="Nome dos padrinhos..."
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">{{ old('godparents', $wedding->godparents) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Observações Adicionais</label>
                <textarea name="observations" rows="2" placeholder="Notas sobre a cerimônia..."
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">{{ old('observations', $wedding->observations) }}</textarea>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('weddings.index') }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Atualizar Casamento</span>
            </button>
        </div>
    </form>
</div>
@endsection
