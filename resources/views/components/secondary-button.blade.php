<button {{ $attributes->merge(['type' => 'button', 'class' => 'cg-btn-secondary cg-motion-lift disabled:cursor-not-allowed disabled:opacity-60']) }}>
    {{ $slot }}
</button>
