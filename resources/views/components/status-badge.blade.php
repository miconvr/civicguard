@props(['status', 'label' => null])

@php
    $key = \Illuminate\Support\Str::snake(strtolower(trim($status)));

    $tone = match ($key) {
        'pending', 'submitted', 'report_submitted' => 'amber',
        'in_progress'                              => 'blue',
        'resolved', 'confirmed_fixed'              => 'green',
        'verified'                                 => 'green',
        'declined', 'rejected'                     => 'red',
        'low'                                      => 'green',
        'moderate', 'medium'                       => 'yellow',
        'high'                                     => 'orange',
        'critical'                                 => 'red',
        default                                    => 'neutral',
    };
@endphp

<span {{ $attributes->class(['tone', "tone-$tone"]) }}>
    {{ $label ?? \Illuminate\Support\Str::headline($status) }}
</span>
