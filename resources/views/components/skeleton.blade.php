@props([
    'type' => 'text', // 'text', 'avatar', 'rect', 'card', 'table'
    'lines' => 1,
    'class' => ''
])

@php
    $baseClasses = 'animate-pulse bg-zinc-200 dark:bg-zinc-800 rounded';
    
    $typeClasses = match($type) {
        'avatar' => 'w-12 h-12 rounded-full shrink-0',
        'circle' => 'rounded-full shrink-0',
        'rect' => 'w-full h-32 rounded-2xl',
        'card' => 'w-full p-6 bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-[2.5rem] shadow-sm space-y-4',
        'table' => 'w-full space-y-4',
        default => 'h-4 w-full rounded-lg' // text
    };
@endphp

@if ($type === 'card')
    <div class="{{ $typeClasses }} {{ $class }}">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-zinc-200 dark:bg-zinc-800 animate-pulse"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-1/3"></div>
                <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-1/2"></div>
            </div>
        </div>
        <div class="space-y-2 pt-2">
            <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse"></div>
            <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-5/6"></div>
        </div>
    </div>
@elseif ($type === 'table')
    <div class="{{ $typeClasses }} {{ $class }}">
        <div class="h-10 bg-zinc-200 dark:bg-zinc-800 rounded-xl animate-pulse w-full"></div>
        @for ($i = 0; $i < $lines; $i++)
            <div class="flex items-center space-x-4 py-2 border-b border-gray-100 dark:border-zinc-800/40">
                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-8"></div>
                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse flex-1"></div>
                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-24"></div>
                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-20"></div>
                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded animate-pulse w-12"></div>
            </div>
        @endfor
    </div>
@else
    @if ($lines > 1 && $type === 'text')
        <div class="space-y-3 {{ $class }}">
            @for ($i = 0; $i < $lines; $i++)
                <div class="{{ $baseClasses }} {{ $typeClasses }} @if($i === $lines - 1) w-2/3 @endif"></div>
            @endfor
        </div>
    @else
        <div class="{{ $baseClasses }} {{ $typeClasses }} {{ $class }}"></div>
    @endif
@endif
