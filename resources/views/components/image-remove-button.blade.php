@props([
    'label' => __('Remove selected image'),
    'placement' => 'absolute right-2 top-2',
])

<button type="button" {{ $attributes->merge([
    'aria-label' => $label,
    'title' => $label,
    'class' => $placement.' inline-flex h-6 w-6 items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 shadow-sm transition hover:border-red-300 hover:bg-red-50 hover:text-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-800 dark:hover:bg-red-950/40 dark:hover:text-red-300',
]) }}>
    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <path stroke-linecap="round" d="m5 5 10 10M15 5 5 15" />
    </svg>
</button>
