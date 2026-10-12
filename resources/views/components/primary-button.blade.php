<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-11 items-center justify-center px-5 py-2.5 bg-maroon-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-maroon-800 focus:bg-maroon-800 focus:outline-none focus:ring-2 focus:ring-maroon-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-70 transition-colors duration-150']) }}>
    {{ $slot }}
</button>
