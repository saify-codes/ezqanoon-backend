@props(['status'])

@php
    $badgeClass = match($status) {
        'OPEN' => 'bg-success',
        'IN PROGESS' => 'bg-warning',
        'CLOSED' => 'bg-danger',
        default => 'bg-secondary',
    };

@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $badgeClass]) }}>
    {{ $status }}
</span>
