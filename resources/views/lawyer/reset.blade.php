<x-lawyer.guest>
    <div class="page-content d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-6 mx-auto">
                <div class="card">
                    <div class="row">
                        {{-- <div class="col-md-4 pe-md-0">
                            <div class="auth-side-wrapper"
                                style="background-image: url({{ asset('assets/images/auth-bg.jpg') }})">
                            </div>
                        </div> --}}
                        <div class="col-12">
                            <div class="auth-form-wrapper px-4 py-5">
                                <a href="{{ url('/') }}" class="noble-ui-logo d-block mb-2">EzQanoon</a>
                                <h5 class="text-muted fw-normal mb-4">Recover your account</h5>

                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {!! session('error') !!}
                                    </div>
                                @endif

                                <form class="forms-sample" action="{{ route('lawyer.reset') }}" method="POST">
                                    @csrf
                                    
                                    <input type="hidden" value="{{request()->token}}" class="form-control" name="token"/>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Enter your new password" required>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm password</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                            placeholder="Confirm password" required>
                                        @error('password_confirmation')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div>
                                        <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Reset password</button>
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
