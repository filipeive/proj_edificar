@extends('layouts.app')

@section('title', "Editar Zona: $zone->name - Portal Life Church")
@section('page-title', 'Editar Zona')
@section('page-subtitle', 'Atualizar informações da área pastoral ' . $zone->name)

@section('content')
    <div class="w-full space-y-8">
        <!-- Header Card -->
        <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-blue-50/50 rounded-full -mr-32 -mt-32 transition-transform group-hover:scale-110 duration-700">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                    <div
                        class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-4xl shadow-2xl shadow-blue-100">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div>
                        <div
                            class="flex items-center justify-center md:justify-start gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">
                            <a href="{{ route('zones.index') }}" class="hover:underline">Zonas</a>
                            <i class="bi bi-chevron-right text-[10px]"></i>
                            <span>Editar Zona</span>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Editar Zona</h1>
                        <p class="text-gray-500 font-medium tracking-tight">Atualizar informações da área pastoral <span
                                class="text-blue-600 font-black italic">{{ $zone->name }}</span></p>
                    </div>
                </div>

                <a href="{{ route('zones.index') }}"
                    class="group flex items-center bg-gray-50 text-gray-500 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-bold text-xs uppercase tracking-widest">
                    <i class="bi bi-arrow-left text-lg mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    Cancelar
                </a>
            </div>
        </div>
        <!-- Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('zones.update', $zone) }}" method="POST" class="p-8 md:p-12 space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Nome da Zona -->
                    <div class="space-y-2">
                        <label for="name" class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Nome da
                            Zona</label>
                        <div class="relative">
                            <i class="bi bi-map absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="name" id="name"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-gray-700 placeholder-gray-400 @error('name') border-red-500 @enderror"
                                value="{{ old('name', $zone->name) }}" required>
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seleção do Pastor de Zona -->
                    <div class="space-y-2">
                        <label for="pastor_id"
                            class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Pastor de Zona</label>
                        <div class="relative">
                            <i class="bi bi-person-badge absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <select name="pastor_id" id="pastor_id"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-gray-700 appearance-none custom-select @error('pastor_id') border-red-500 @enderror">
                                <option value="">A definir futuramente</option>
                                @foreach ($pastors as $pastor)
                                    <option value="{{ $pastor->id }}" @selected(old('pastor_id', $zone->pastor_id) == $pastor->id)>
                                        {{ $pastor->name }}
                                    </option>
                                @endforeach
                            </select>
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
                        class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-medium text-gray-700 placeholder-gray-400 resize-none">{{ old('description', $zone->description) }}</textarea>
                </div>

                <!-- Zone Visibility in Teaching Services -->
                <div
                    class="px-8 py-6 bg-blue-50/30 rounded-3xl border border-blue-100/50 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-black text-gray-900">Visível em Cultos de Ensino</p>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Habilita esta zona na
                            ficha de presença de quarta-feira</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="show_in_teaching_services" value="0">
                        <input type="checkbox" name="show_in_teaching_services" value="1" class="sr-only peer"
                            @checked(old('show_in_teaching_services', $zone->show_in_teaching_services ?? true))>
                        <div
                            class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600">
                        </div>
                    </label>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all duration-300 font-black shadow-lg shadow-blue-200">
                        <i class="bi bi-save-fill mr-2"></i>Salvar Alterações
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