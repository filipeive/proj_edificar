@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'type' => 'button',
    'loading' => null
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold tracking-tight rounded-2xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-[0.98] border shadow-sm cursor-pointer disabled:opacity-60 disabled:pointer-events-none relative overflow-hidden';
    
    $variantClasses = match($variant) {
        'primary' => 'bg-orange-500 hover:bg-orange-600 border-orange-600/10 text-white focus:ring-orange-500',
        'secondary' => 'bg-zinc-800 hover:bg-zinc-700 border-zinc-700/50 text-white focus:ring-zinc-600',
        'outline' => 'bg-transparent hover:bg-zinc-100 border-zinc-300 text-zinc-700 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-800 focus:ring-zinc-500',
        'danger' => 'bg-red-500 hover:bg-red-600 border-red-600/10 text-white focus:ring-red-500',
        'success' => 'bg-emerald-500 hover:bg-emerald-600 border-emerald-600/10 text-white focus:ring-emerald-500',
        'warning' => 'bg-amber-500 hover:bg-amber-600 border-amber-600/10 text-white focus:ring-amber-500',
        'info' => 'bg-blue-500 hover:bg-blue-600 border-blue-600/10 text-white focus:ring-blue-500',
        default => 'bg-orange-500 hover:bg-orange-600 border-orange-600/10 text-white focus:ring-orange-500'
    };

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3.5 text-base',
        default => 'px-4 py-2.5 text-sm'
    };
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => "$baseClasses $variantClasses $sizeClasses"]) }}>
        @if ($icon)
            <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-2 @endif text-base"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" 
        @if($loading)
            :disabled="{{ $loading }}"
        @endif
        {{ $attributes->merge(['class' => "$baseClasses $variantClasses $sizeClasses"]) }}>
        
        @if ($loading)
            <span x-show="{{ $loading }}" x-cloak class="absolute inset-0 flex items-center justify-center bg-inherit">
                <svg class="animate-spin h-5 w-5 text-current" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            <span :class="{{ $loading }} ? 'opacity-0' : ''" class="inline-flex items-center justify-center transition-opacity duration-200">
                @if ($icon)
                    <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-2 @endif text-base"></i>
                @endif
                {{ $slot }}
            </span>
        @else
            @if ($icon)
                <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-2 @endif text-base"></i>
            @endif
            {{ $slot }}
        @endif
    </button>
@endif
