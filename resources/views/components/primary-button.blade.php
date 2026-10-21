<button {{ $attributes->merge(['type' => 'submit', 'class' => 'cg-btn cg-motion-lift disabled:cursor-wait disabled:opacity-70']) }}>
    {{ $slot }}
</button>
