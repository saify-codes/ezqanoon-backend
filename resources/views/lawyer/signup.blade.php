<x-lawyer.guest>
    <div class="page-content d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-4 mx-auto">
                <div class="card">
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

                        <ul class="nav nav-pills nav-fill mb-4">
                            <li class="nav-item bg-light">
                                <a class="nav-link" href="{{ route('lawyer.signin') }}">Signin</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Signup</a>
                            </li>
                        </ul>

                        <!-- Login Form -->
                        <form action="{{ route('lawyer.signup') }}" method="POST">
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
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" name="gender" id="gender" required>
                                    <option value="male"   {{ old('gender') == 'MALE' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'FEMALE' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender') == 'OTHER' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
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
                                <div class="input-group">
                                    <input type="phone" class="form-control" id="phone" name="phone"
                                        placeholder="phone" value="{{ old('phone') }}" required>
                                    <button type="button" class="btn btn-primary" id="send-otp-btn">send otp</button>
                                </div>
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror 
                            </div>
                            
                            <div class="mb-3 d-none" id="otp-section">
                                <label for="otp" class="form-label">Verify otp</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="otp" placeholder="enter otp" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <button type="button" class="btn btn-primary" id="verify-otp-btn">verify otp</button>
                                </div>
                                <small id="otp-message" class="text-danger"></small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" placeholder="Password" required>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm password" name="password_confirmation" required>
                                @error('password_confirmation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div>
                                <button type="submit" class="btn btn-primary w-100 me-2 mb-2 mb-md-0">Create account</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('custom-scripts')
        <script>
        $(document).ready(function() {
            let isPhoneVerified = false;
            
            $('#send-otp-btn').click(function() {
                const phone = $('#phone').val();
                
                if (!phone) {
                    alert('Please enter phone number');
                    return;
                }
                
                $.ajax({
                    url: '{{ route("lawyer.otp.send") }}',
                    type: 'POST',
                    data: {
                        phone: phone,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: () => {
                        $('#send-otp-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
                    },
                    complete: () => {
                        $('#send-otp-btn').html('send otp');
                    },
                    success: (response) => {
                        $('#send-otp-btn').prop('disabled', true);
                        $('#phone').prop('readonly', true);
                        $('#otp-section').removeClass('d-none').find('#otp').prop('required', true);
                    },
                    error: (xhr, status, error) => {
                        alert(xhr.responseJSO03122030440N.message || 'Something went wrong!');
                    }
                });
            });
    
            // Verify OTP button click handler
            $('#verify-otp-btn').click(function() {
                const otp = $('#otp').val();
                const phone = $('#phone').val();
                
                if (!otp) {
                    alert('Please enter otp');
                    return;
                }
    
                $.ajax({
                    url: '{{ route("lawyer.otp.verify") }}',
                    type: 'POST',
                    datatype: 'json',
                    data: {
                        phone: phone,
                        otp: otp,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {                        
                        isPhoneVerified = true;
                        $('#otp-message').removeClass('text-danger').addClass('text-success').text('otp verified');
                        $('#phone').prop('readonly', true);
                    },
                    error: function(xhr, status, error) {
                        $('#otp-message').text(xhr.responseJSON.message || 'Something went wrong!');
                    }
                });
            });
    
            $('form').submit(function(e) {
                if (!isPhoneVerified) {
                    e.preventDefault();
                    alert('Please verify your phone number first');
                }
            });
        });
        </script>
    @endpush
</x-lawyer.guest>

