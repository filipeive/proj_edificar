@extends('layouts.app')

@section('title', 'Editar Membro')
@section('page-title', 'Editar Membro')
@section('page-subtitle', $member->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <form action="{{ route('members.update', $member) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Similar ao create, mas com value="{{ $member->name }}" etc -->
            <!-- SEM o campo de senha (não deve alterar senha aqui) -->
            
            <!-- Botão -->
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg">
                <i class="bi bi-save mr-2"></i>Salvar Alterações
            </button>
        </form>
    </div>
</div>
@endsection