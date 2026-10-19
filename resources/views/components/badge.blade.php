@props([
    'variant' => 'neutral',
    'icon' => null
])

@php
    $baseClasses = 'inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-black tracking-wider uppercase rounded-full border';
    
    $variantClasses = match($variant) {
        'primary' => 'bg-orange-100 dark:bg-orange-500/10 border-orange-200 dark:border-orange-500/20 text-orange-800 dark:text-orange-400',
        'secondary' => 'bg-zinc-100 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300',
        'success' => 'bg-green-100 dark:bg-emerald-500/10 border-green-200 dark:border-emerald-500/20 text-green-800 dark:text-emerald-400',
        'danger' => 'bg-red-100 dark:bg-red-500/10 border-red-200 dark:border-red-500/20 text-red-800 dark:text-red-400',
        'warning' => 'bg-amber-100 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-400',
        'info' => 'bg-sky-100 dark:bg-blue-500/10 border-sky-200 dark:border-blue-500/20 text-sky-800 dark:text-blue-400',
        'neutral' => 'bg-gray-100 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-gray-400',
        default => 'bg-gray-100 dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-gray-400'
    };
@endphp

<span {{ $attributes->merge(['class' => "$baseClasses $variantClasses"]) }}>
    @if ($icon)
        <i class="{{ $icon }} text-[10px]"></i>
    @endif
    {{ $slot }}
</span>
