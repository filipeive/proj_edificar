@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'type' => 'button',
    'loading' => null
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold tracking-tight rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 active:scale-[0.98] border shadow-sm cursor-pointer disabled:opacity-60 disabled:pointer-events-none relative overflow-hidden';
    
    $variantClasses = match($variant) {
        'primary' => 'bg-orange-700 hover:bg-orange-800 border-orange-800/10 text-white focus:ring-orange-600',
        'secondary' => 'bg-gray-900 hover:bg-gray-800 border-gray-800/50 text-white focus:ring-gray-700',
        'outline' => 'bg-white hover:bg-gray-50 border-gray-300 text-gray-700 dark:bg-transparent dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-800 focus:ring-gray-400',
        'danger' => 'bg-red-600 hover:bg-red-700 border-red-700/10 text-white focus:ring-red-500',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 border-emerald-700/10 text-white focus:ring-emerald-500',
        'warning' => 'bg-amber-600 hover:bg-amber-700 border-amber-700/10 text-white focus:ring-amber-500',
        'info' => 'bg-blue-600 hover:bg-blue-700 border-blue-700/10 text-white focus:ring-blue-500',
        default => 'bg-orange-700 hover:bg-orange-800 border-orange-800/10 text-white focus:ring-orange-600'
    };

    $sizeClasses = match($size) {
        'xs' => 'px-2 py-1 text-[11px]',
        'sm' => 'px-2.5 py-1.5 text-xs',
        'md' => 'px-3 py-1.5 sm:py-2 text-xs sm:text-sm',
        'lg' => 'px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm',
        default => 'px-3 py-1.5 sm:py-2 text-xs sm:text-sm'
    };
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => "$baseClasses $variantClasses $sizeClasses"]) }}>
        @if ($icon)
            <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-1.5 @endif text-sm"></i>
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
                <svg class="animate-spin h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            <span :class="{{ $loading }} ? 'opacity-0' : ''" class="inline-flex items-center justify-center transition-opacity duration-200">
                @if ($icon)
                    <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-1.5 @endif text-sm"></i>
                @endif
                {{ $slot }}
            </span>
        @else
            @if ($icon)
                <i class="{{ $icon }} @if($slot->isNotEmpty()) mr-1.5 @endif text-sm"></i>
            @endif
            {{ $slot }}
        @endif
    </button>
@endif
