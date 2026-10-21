<button {{ $attributes->merge(['type' => 'submit', 'class' => 'cg-btn-danger cg-motion-lift disabled:cursor-not-allowed disabled:opacity-60']) }}>
    {{ $slot }}
</button>
