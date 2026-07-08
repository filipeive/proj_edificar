@props([
    'title' => 'Nenhum registo encontrado',
    'subtitle' => 'Não conseguimos localizar nenhuma informação correspondente nesta seção.',
    'icon' => 'bi-search',
    'actionHref' => null,
    'actionLabel' => null,
    'actionIcon' => null
])

<div class="flex flex-col items-center justify-center text-center p-8 md:p-16 bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-[2.5rem] shadow-sm relative overflow-hidden group">
    <!-- Background subtle gradient circle -->
    <div class="absolute -top-12 -right-12 w-48 h-48 bg-orange-500/5 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>
    <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-amber-500/5 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>

    <div class="relative z-10 space-y-6 max-w-md">
        <!-- Modern Icon Container -->
        <div class="mx-auto w-20 h-20 rounded-[2rem] bg-zinc-50 dark:bg-zinc-800/40 border border-gray-100 dark:border-zinc-700/30 flex items-center justify-center text-zinc-400 dark:text-zinc-500 shadow-inner group-hover:scale-110 transition-all duration-500">
            <i class="bi {{ $icon }} text-3xl"></i>
        </div>

        <!-- Typography -->
        <div class="space-y-2">
            <h3 class="text-xl font-black text-zinc-800 dark:text-zinc-200 tracking-tight">
                {{ $title }}
            </h3>
            <p class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 leading-relaxed">
                {{ $subtitle }}
            </p>
        </div>

        <!-- Action Button -->
        @if ($actionHref && $actionLabel)
            <div class="pt-2">
                <x-button :href="$actionHref" variant="primary" :icon="$actionIcon">
                    {{ $actionLabel }}
                </x-button>
            </div>
        @endif
    </div>
</div>
