@props(['status'])

@php
    $badgeClass = match($status) {
        'MEDIUM' => 'bg-warning',
        'HIGH' => 'bg-primary',
        'CRITICAL' => 'bg-danger',
        default => 'bg-secondary',
    };

@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $badgeClass]) }}>
    {{ $status ?? 'None' }}
</span>
