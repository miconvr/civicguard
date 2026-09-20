@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-maroon-500 dark:focus:border-maroon-600 focus:ring-maroon-500 dark:focus:ring-maroon-600 rounded-lg shadow-sm px-3 py-2 transition']) }}>