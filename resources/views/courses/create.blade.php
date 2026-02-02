@extends('layouts.app')

@section('title', 'Novo Curso')
@section('page-title', 'Criar Curso')
@section('page-subtitle', 'Adicione um novo curso à academia')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('courses.index') }}"
                class="text-gray-600 hover:text-orange-600 flex items-center transition font-semibold">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
        </div>

        <div class="max-w-3xl mx-auto">
            <form action="{{ route('courses.store') }}" method="POST"
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @csrf
                <div class="p-8 space-y-8">
                    <div class="grid grid-cols-1 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Nome do
                                Curso *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Ex: Academia de Vida - Nível 1"
                                class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4 text-lg">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Categoria</label>
                                <select name="category"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4 appearance-none custom-select">
                                    <option value="Teologia" {{ old('category') == 'Teologia' ? 'selected' : '' }}>Teologia /
                                        Academia</option>
                                    <option value="Família" {{ old('category') == 'Família' ? 'selected' : '' }}>Família /
                                        Casais</option>
                                    <option value="Liderança" {{ old('category') == 'Liderança' ? 'selected' : '' }}>Liderança
                                    </option>
                                    <option value="Geral" {{ old('category') == 'Geral' ? 'selected' : '' }}>Geral</option>
                                </select>
                                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Duração</label>
                                <input type="text" name="duration" value="{{ old('duration') }}"
                                    placeholder="Ex: 3 meses / 12 aulas"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4">
                                @error('duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Nível /
                                    Cargo Alvo</label>
                                <select name="target_role"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4 appearance-none custom-select">
                                    <option value="" {{ old('target_role') == '' ? 'selected' : '' }}>Todos (Geral)</option>
                                    <option value="membro" {{ old('target_role') == 'membro' ? 'selected' : '' }}>Apenas
                                        Membros</option>
                                    <option value="lider_celula" {{ old('target_role') == 'lider_celula' ? 'selected' : '' }}>
                                        Apenas Líderes</option>
                                    <option value="supervisor" {{ old('target_role') == 'supervisor' ? 'selected' : '' }}>
                                        Apenas Supervisores</option>
                                    <option value="pastor_zona" {{ old('target_role') == 'pastor_zona' ? 'selected' : '' }}>
                                        Apenas Pastores de Zona</option>
                                </select>
                                <p class="text-[10px] text-gray-400 mt-2 font-bold uppercase tracking-tighter">Define quem
                                    será elegível para se inscrever neste curso.</p>
                                @error('target_role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center space-x-3 pt-8">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}
                                    class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <label for="is_active"
                                    class="text-sm font-bold text-gray-700 uppercase tracking-widest cursor-pointer">Curso
                                    Ativo</label>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Descrição</label>
                            <textarea name="description" rows="5" placeholder="Descreva o objetivo e conteúdo do curso..."
                                class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-10 py-4 rounded-xl shadow-lg shadow-orange-600/20 transition-all transform hover:-translate-y-1">
                        <i class="bi bi-check-lg mr-2"></i> CRIAR CURSO
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection