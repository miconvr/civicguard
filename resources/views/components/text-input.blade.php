@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'cg-input min-h-11 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-maroon-500 dark:focus:border-[#c27872] focus:ring-maroon-500 dark:focus:ring-[#c27872] rounded-lg px-3 py-2 transition']) }}>
