@props(['inverse' => false])
<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
  <path d="M12 2.25l7.5 3v6.75a11.25 11.25 0 01-7.5 10.5 11.25 11.25 0 01-7.5-10.5V5.25l7.5-3z" fill="{{ $inverse ? '#fffaf5' : '#951f3d' }}" stroke="{{ $inverse ? '#fffaf5' : '#751a33' }}" stroke-width="1.5"/>
  <path d="M9 12.75l2.25 2.25L15 9.75" stroke="{{ $inverse ? '#6d1c32' : '#ffffff' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
