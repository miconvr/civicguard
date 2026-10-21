@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'cg-input min-h-11 rounded-lg px-3 py-2']) }}>
