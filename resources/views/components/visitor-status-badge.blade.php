@props(['status' => 'pendente'])

@php
    $variant = match ($status) {
        'pendente' => 'warning',
        'contatado' => 'info',
        'integrado' => 'success',
        'sem_interesse' => 'neutral',
        default => 'neutral',
    };

    $label = match ($status) {
        'pendente' => 'Pendente',
        'contatado' => 'Contatado',
        'integrado' => 'Integrado',
        'sem_interesse' => 'Sem Interesse',
        default => 'Desconhecido',
    };
@endphp

<x-badge :variant="$variant" {{ $attributes }}>
    {{ $label }}
</x-badge>
