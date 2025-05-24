@props(['deadline'])

@php
    $deadlineDate   = \Carbon\Carbon::parse($deadline)->endOfDay();
    $now            = \Carbon\Carbon::now();
    
    if ($now < $deadlineDate) {
        $diff = $now->diff($deadlineDate);
        $remaining = match (true) {
            $diff->y >   0  => $diff->y . ' year'  . ($diff->y > 1 ? 's' : '') . ' remaining',
            $diff->m >   0  => $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' remaining',
            $diff->d === 0  => 'Today',
            default         => $diff->d . ' day'   . ($diff->d > 1 ? 's' : '') . ' remaining',
        };
        $badgeClass = ($diff->d === 0 ? 'blinking ' : '') . 'badge rounded-pill border border-success text-success';
    } else {
        $remaining = 'Deadline passed';
        $badgeClass = 'badge rounded-pill border border-danger text-danger';
    }
@endphp

<span {{ $attributes->merge(['class' => $badgeClass]) }}>{{ $remaining }}</span>
