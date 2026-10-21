@props(['value'])

<label {{ $attributes->merge(['class' => 'cg-form-label text-sm font-medium']) }}>
    {{ $value ?? $slot }}
</label>
