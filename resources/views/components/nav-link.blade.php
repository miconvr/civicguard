@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center rounded-lg bg-maroon-50 px-3 py-2 text-sm font-semibold leading-5 text-maroon-800 ring-1 ring-inset ring-maroon-100 focus:outline-none transition duration-150 ease-in-out dark:bg-maroon-900/30 dark:text-maroon-200 dark:ring-maroon-800'
    : 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium leading-5 text-gray-500 hover:bg-gray-100 hover:text-maroon-700 focus:outline-none transition duration-150 ease-in-out dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
