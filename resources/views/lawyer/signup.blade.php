<x-lawyer.guest>
    <div class="page-content d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-6 mx-auto">
                <div class="card">
                    <div class="row">
                        <div class="col-md-4 pe-md-0">
                            <div class="auth-side-wrapper"
                                style="background-image: url({{ asset('assets/images/auth-bg.jpg') }})">
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
                                        {!! session('error') !!}
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <!-- Login Form -->
                                <form class="forms-sample" action="{{ route('lawyer.signup') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="name" class="form-control" id="name" name="name"
                                            placeholder="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="phone" class="form-control" id="phone" name="phone"
                                            placeholder="phone" value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" placeholder="Password" required>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password_confirmation</label>
                                        <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm password" name="password_confirmation" required>
                                        @error('password_confirmation')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div>
                                        <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Create account</button>
                                    </div>

                                    <a href="{{ route('lawyer.signin') }}" class="d-block mt-3 text-muted">
                                        Already a user? Sign in
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
