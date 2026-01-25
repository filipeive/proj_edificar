@extends('layouts.app')

@section('title', 'Nova Zona Pastoral - Portal Life Church')

@section('header-actions')
    <div class="w-full">
        <!-- Header -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Criar Nova Zona</h1>
                <p class="text-gray-500 text-sm">Preencha os dados abaixo para registar uma nova divisão.</p>
            </div>
            <a href="{{ route('zones.index') }}"
                class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="w-full">
        <!-- Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('zones.store') }}" method="POST" class="p-8 md:p-12 space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Nome da Zona -->
                    <div class="space-y-2">
                        <label for="name" class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Nome da
                            Zona <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="bi bi-map absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="name" id="name"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-gray-700 placeholder-gray-400 @error('name') border-red-500 @enderror"
                                value="{{ old('name') }}" placeholder="Ex: Zona Centro" required>
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seleção do Pastor de Zona -->
                    <div class="space-y-2">
                        <label for="pastor_id"
                            class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Pastor de Zona
                            (Opcional)</label>
                        <div class="relative">
                            <i class="bi bi-person-badge absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="pastor_id" id="pastor_id"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-gray-700 appearance-none @error('pastor_id') border-red-500 @enderror">
                                <option value="">A definir futuramente</option>
                                @foreach ($pastors as $pastor)
                                    <option value="{{ $pastor->id }}" @selected(old('pastor_id') == $pastor->id)>
                                        {{ $pastor->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i
                                class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                        @error('pastor_id')
                            <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="description"
                        class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Descrição Detalhada</label>
                    <textarea name="description" id="description" rows="4"
                        placeholder="Detalhes sobre a cobertura geográfica ou características da zona..."
                        class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-gray-700 placeholder-gray-400 resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all duration-300 font-black shadow-lg shadow-blue-200">
                        <i class="bi bi-check-circle-fill mr-2"></i>Registrar Zona
                    </button>
                    <a href="{{ route('zones.index') }}"
                        class="flex-1 bg-gray-50 text-gray-500 px-8 py-4 rounded-2xl hover:bg-gray-100 transition-all duration-300 font-bold text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection