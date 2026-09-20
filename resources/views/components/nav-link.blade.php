@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center border-b-2 border-maroon-700 px-2 py-2 text-sm font-semibold leading-5 text-maroon-800 focus:outline-none transition duration-150 ease-in-out dark:border-maroon-300 dark:text-maroon-200'
    : 'inline-flex items-center border-b-2 border-transparent px-2 py-2 text-sm font-medium leading-5 text-gray-500 hover:border-gray-300 hover:text-gray-800 focus:outline-none transition duration-150 ease-in-out dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
