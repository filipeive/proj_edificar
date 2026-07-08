@props([
    'links' => []
])

<nav class="flex text-zinc-500 dark:text-zinc-400 text-xs font-semibold uppercase tracking-wider mb-2" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <li class="inline-flex items-center">
            <a href="/" class="inline-flex items-center hover:text-orange-500 transition-colors duration-200">
                <i class="bi bi-house-door-fill mr-1.5 text-sm"></i>
                Início
            </a>
        </li>
        @foreach ($links as $label => $url)
            <li class="flex items-center">
                <i class="bi bi-chevron-right mx-1.5 text-[10px] text-zinc-400"></i>
                @if ($url)
                    <a href="{{ $url }}" class="hover:text-orange-500 transition-colors duration-200">
                        {{ $label }}
                    </a>
                @else
                    <span class="text-zinc-400 dark:text-zinc-600 font-bold" aria-current="page">
                        {{ $label }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
