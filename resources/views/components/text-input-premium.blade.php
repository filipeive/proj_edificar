@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'icon' => null,
    'required' => false
])

@php
    $hasError = $name && $errors->has($name);
    
    $inputClasses = 'w-full px-3 py-2 rounded-lg border text-xs sm:text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2';
    
    $themeClasses = $hasError 
        ? 'border-red-300 dark:border-red-900 bg-red-50/10 dark:bg-red-950/5 text-red-900 dark:text-red-300 placeholder-red-300 focus:border-red-500 focus:ring-red-500/10'
        : 'border-gray-300 dark:border-zinc-800 bg-white dark:bg-zinc-900/20 text-gray-900 dark:text-zinc-100 placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500/10 dark:focus:border-orange-500/50';

    $paddingLeft = $icon ? 'pl-9' : 'px-3';
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="block mb-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <div class="relative rounded-lg shadow-sm">
        @if ($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                <i class="{{ $icon }} text-sm"></i>
            </div>
        @endif

        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => "$inputClasses $themeClasses $paddingLeft"]) }}
        />
    </div>

    @if ($hasError)
        <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1.5 animate-pulse">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
