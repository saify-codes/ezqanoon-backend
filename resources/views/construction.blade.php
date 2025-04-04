<x-lawyer.guest>
    <div class="page-content d-flex align-items-center justify-content-center">

        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-6 mx-auto d-flex flex-column align-items-center">
                <img src="{{ asset('assets/images/others/construction.svg') }}" class="img-fluid mb-2" alt="500">
                <h1 class="fw-bolder mb-22 mt-2 fs-80px text-secondary">503</h1>
                <h4 class="mb-2">Under construction</h4>
                <h6 class="text-secondary mb-3 text-center">This page is under construction.</h6>
                
                @php
                    $currentUrl  = url()->current();
                    $previousUrl = url()->previous();
                @endphp

                @if ($currentUrl == $previousUrl)
                    <a href="{{ route('lawyer.dashboard') }}">Go to dashboard</a>
                @else
                    <a href="{{ $previousUrl }}">Go back</a>
                @endif
            </div>
        </div>

    </div>
</x-lawyer.guest>
