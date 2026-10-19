@props([
    'title' => null,
    'subtitle' => null,
    'variant' => 'default',
    'compact' => false
])

@php
    $baseClasses = 'rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 transition-all duration-200';
    
    $variantClasses = match($variant) {
        'default' => 'shadow-sm',
        'flat' => 'shadow-none',
        'bordered' => 'shadow-none border border-gray-300 dark:border-zinc-700',
        'gradient' => 'bg-gradient-to-br from-white to-zinc-50/50 dark:from-zinc-900/40 dark:to-zinc-900/10 shadow-sm',
        default => 'shadow-sm'
    };

    $paddingClasses = $compact ? 'p-3 sm:p-4' : 'p-3.5 sm:p-5';
@endphp

<div {{ $attributes->merge(['class' => "$baseClasses $variantClasses"]) }}>
    <!-- Header -->
    @if (isset($header) || $title || $subtitle)
        <div class="px-3.5 sm:px-5 py-3 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between flex-wrap gap-2.5">
            @if (isset($header))
                {{ $header }}
            @else
                <div>
                    @if ($title)
                        <h3 class="text-xs sm:text-sm font-black tracking-tight text-gray-900 dark:text-zinc-100">{{ $title }}</h3>
                    @endif
                    @if ($subtitle)
                        <p class="text-[11px] sm:text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ $subtitle }}</p>
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
        <div class="px-3.5 sm:px-5 py-2.5 bg-gray-50/60 dark:bg-zinc-950/20 border-t border-gray-200 dark:border-zinc-800 rounded-b-xl">
            {{ $footer }}
        </div>
    @endif
</div>
