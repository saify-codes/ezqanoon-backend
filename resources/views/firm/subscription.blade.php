<x-firm.guest>
    <div class="d-flex flex-column justify-content-center min-vh-100 p-2">
        <div class="container">
            <a href="{{route('firm.signout')}}" class="btn btn-danger me-auto">Signout out</a>
            <h2 class="text-center mb-3 mt-4">Choose a plan</h2>
            <p class="text-secondary text-center mb-4 pb-2">Choose the features and functionality your team need today.Easily upgrade as your company grows.</p>
            <div class="row">
                @if ($subscriptions->isEmpty())
                    <div class="col-md-12">
                        <div class="alert alert-warning">No subscriptions found</div>
                    </div>
                @endif
                @foreach ($subscriptions as $subscription)
                    <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="text-center mt-3 mb-4">{{ $subscription->name }}</h4>
                                <div class="icon text-center my-3">
                                    <i data-feather="gift" class="text-success icon-xxl"></i>
                                </div>
                                <h1 class="text-center">{{ round($subscription->price) }} PKR</h1>
                                <p class="text-secondary text-center mb-4 fw-light">per {{ $subscription->duration }} days</p>
                                <h5 class="text-success text-center mb-4">Up to 75 units</h5>
                                <table class="mx-auto">
                                    <tbody>
                                        @foreach ($subscription->features ?? [] as $feature)
                                            <tr>
                                                <td>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-check icon-md text-primary me-2">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </td>
                                                <td>
                                                    <p>{{ $feature }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-grid">
                                    <form action="{{ route('firm.subscription.select', $subscription->id) }}" method="post">
                                        @csrf
                                        <button class="btn btn-success w-100 mt-4">Select Plan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-firm.guest>
