<a {{ $attributes->merge(['class' => 'inline-flex min-h-[40px] items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-transparent px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm transition hover:border-maroon-700 hover:text-maroon-700 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-maroon-600']) }}>
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
    </svg>
    <span>{{ __('Export PDF') }}</span>
</a>
