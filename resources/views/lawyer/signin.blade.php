<x-lawyer.guest>
    <div class="page-content d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-6 mx-auto">
                <div class="card">
                    <div class="row">
                        <div class="col-md-4 pe-md-0">
                            <div class="auth-side-wrapper"
                                style="background-image: url({{ asset('images/auth-bg.jpg') }})">
                            </div>
                        </div>
                        <div class="col-md-8 ps-md-0">
                            <div class="auth-form-wrapper px-4 py-5">
                                <a href="{{ url('/') }}"
                                    class="noble-ui-logo d-block mb-2">EzQanoon</a>
                                <h5 class="text-muted fw-normal mb-4">Welcome back! Log in to your account.</h5>

                                <!-- Display Error Messages -->
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <!-- Login Form -->
                                <form class="forms-sample" action="{{ route('lawyer.signin') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email address</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            autocomplete="current-password" placeholder="Password" required>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="authCheck" name="remember">
                                        <label class="form-check-label" for="authCheck">
                                            Remember me
                                        </label>
                                    </div>

                                    <div>
                                        <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Login</button>
                                        {{-- <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                                            <i class="btn-icon-prepend" data-feather="twitter"></i>
                                            Login with Twitter
                                        </button> --}}
                                    </div>

                                    <a href="{}" class="d-block mt-2 text-muted">
                                        Forgot password?
                                    </a>

                                    <a href="{{ route('lawyer.signup') }}" class="d-block mt-3 text-muted">
                                        Not a user? Sign up
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </x-lawyer.app>
