@extends('layouts.app')

@section('title', 'Editar Curso')
@section('page-title', 'Editar Curso')
@section('page-subtitle', 'Atualize as informações do curso')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('courses.index') }}"
                class="text-gray-600 hover:text-orange-600 flex items-center transition font-semibold">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
        </div>

        <div class="max-w-3xl mx-auto">
            <form action="{{ route('courses.update', $course) }}" method="POST"
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @csrf
                @method('PUT')
                <div class="p-8 space-y-8">
                    <div class="grid grid-cols-1 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Nome do
                                Curso *</label>
                            <input type="text" name="name" value="{{ old('name', $course->name) }}" required
                                placeholder="Ex: Academia de Vida - Nível 1"
                                class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4 text-lg">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Categoria</label>
                                <select name="category"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4">
                                    <option value="Teologia" {{ old('category', $course->category) == 'Teologia' ? 'selected' : '' }}>Teologia / Academia</option>
                                    <option value="Família" {{ old('category', $course->category) == 'Família' ? 'selected' : '' }}>Família / Casais</option>
                                    <option value="Liderança" {{ old('category', $course->category) == 'Liderança' ? 'selected' : '' }}>Liderança</option>
                                    <option value="Geral" {{ old('category', $course->category) == 'Geral' ? 'selected' : '' }}>Geral</option>
                                </select>
                                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Duração</label>
                                <input type="text" name="duration" value="{{ old('duration', $course->duration) }}"
                                    placeholder="Ex: 3 meses / 12 aulas"
                                    class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4">
                                @error('duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest">Descrição</label>
                            <textarea name="description" rows="5" placeholder="Descreva o objetivo e conteúdo do curso..."
                                class="w-full rounded-xl border-gray-200 focus:ring-orange-500 focus:border-orange-500 p-4">{{ old('description', $course->description) }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center space-x-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $course->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <label for="is_active"
                                class="text-sm font-bold text-gray-700 uppercase tracking-widest cursor-pointer">Curso
                                Ativo</label>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-10 py-4 rounded-xl shadow-lg shadow-orange-600/20 transition-all transform hover:-translate-y-1">
                        <i class="bi bi-check-lg mr-2"></i> ATUALIZAR CURSO
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection