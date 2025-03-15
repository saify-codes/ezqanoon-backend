@props(['status'])

@php
    $badgeClass = match($status) {
        'PAID' => 'bg-success',
        'PENDING' => 'bg-warning',
        'OVERDUE' => 'bg-danger',
        default => 'bg-secondary',
    };

@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $badgeClass]) }}>
    {{ $status }}
</span>
