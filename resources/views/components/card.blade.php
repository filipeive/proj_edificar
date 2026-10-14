@props([
    'title' => null,
    'subtitle' => null,
    'variant' => 'default',
    'compact' => false
])

@php
    $baseClasses = 'rounded-xl sm:rounded-2xl border border-gray-100 dark:border-zinc-850 bg-white dark:bg-zinc-900/30 backdrop-blur-md transition-all duration-300';
    
    $variantClasses = match($variant) {
        'default' => 'shadow-sm hover:shadow-md',
        'flat' => 'shadow-none',
        'bordered' => 'shadow-none border-2 border-gray-200 dark:border-zinc-700',
        'gradient' => 'bg-gradient-to-br from-white to-zinc-50/50 dark:from-zinc-900/40 dark:to-zinc-900/10 shadow-md',
        default => 'shadow-sm hover:shadow-md'
    };

    $paddingClasses = $compact ? 'p-3 sm:p-4' : 'p-3.5 sm:p-5 md:p-6';
@endphp

<div {{ $attributes->merge(['class' => "$baseClasses $variantClasses"]) }}>
    <!-- Header -->
    @if (isset($header) || $title || $subtitle)
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-50 dark:border-zinc-850 flex items-center justify-between flex-wrap gap-3">
            @if (isset($header))
                {{ $header }}
            @else
                <div>
                    @if ($title)
                        <h3 class="text-sm sm:text-base font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $title }}</h3>
                    @endif
                    @if ($subtitle)
                        <p class="text-[11px] sm:text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 sm:mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Body -->
    <div class="{{ $paddingClasses }}">
        {{ $slot }}
    </div>

    <!-- Footer -->
    @if (isset($footer))
        <div class="px-4 sm:px-6 py-3 bg-zinc-50/30 dark:bg-zinc-950/20 border-t border-gray-50 dark:border-zinc-850 rounded-b-xl sm:rounded-b-2xl">
            {{ $footer }}
        </div>
    @endif
</div>
