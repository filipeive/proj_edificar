@props([
    'title' => 'Nenhum registo encontrado',
    'subtitle' => 'Não conseguimos localizar nenhuma informação correspondente nesta seção.',
    'icon' => 'bi-search',
    'actionHref' => null,
    'actionLabel' => null,
    'actionIcon' => null
])

<div class="flex flex-col items-center justify-center text-center p-6 sm:p-10 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl shadow-sm relative overflow-hidden group">
    <div class="relative z-10 space-y-4 max-w-md">
        <!-- Modern Icon Container -->
        <div class="mx-auto w-12 h-12 rounded-xl bg-gray-50 dark:bg-zinc-800/40 border border-gray-200 dark:border-zinc-700/30 flex items-center justify-center text-gray-400 dark:text-zinc-500">
            <i class="bi {{ $icon }} text-xl"></i>
        </div>

        <!-- Typography -->
        <div class="space-y-1">
            <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-zinc-200 tracking-tight">
                {{ $title }}
            </h3>
            <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-zinc-400 leading-relaxed">
                {{ $subtitle }}
            </p>
        </div>

        <!-- Action Button -->
        @if ($actionHref && $actionLabel)
            <div class="pt-1">
                <x-button :href="$actionHref" variant="primary" :icon="$actionIcon">
                    {{ $actionLabel }}
                </x-button>
            </div>
        @endif
    </div>
</div>
