<x-lawyer.guest>
    <div class="page-content d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-4 mx-auto">
                <div class="card">
                    <div class="auth-form-wrapper px-4 py-5">
                        <a href="{{ url('/') }}" class="noble-ui-logo d-block mb-2">EzQanoon</a>
                        <h5 class="text-muted fw-normal mb-4">Welcome back! Log in to your account.</h5>

                        <!-- Display Error Messages -->
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {!! session('error') !!}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {!! session('success') !!}
                            </div>
                        @endif

                        <ul class="nav nav-pills nav-fill mb-4">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Signin</a>
                            </li>
                            <li class="nav-item bg-light">
                                <a class="nav-link" href="{{ route('lawyer.signup') }}">Signup</a>
                            </li>
                        </ul>

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
                            
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="authCheck" name="remember">
                                    <label class="form-check-label" for="authCheck">
                                        Remember me
                                    </label>
                                </div>
                                <a href="{{route('lawyer.forgot')}}" class="d-block mt-2 text-muted">
                                    Forgot password?
                                </a>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-primary w-100 me-2 mb-2 mb-md-0">Login</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </x-lawyer.app>
