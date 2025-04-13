<x-lawyer.guest>
    <div class="page-content d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-4 mx-auto">
                <div class="card">
                    <div class="auth-form-wrapper px-4 py-5">
                        <a href="{{ url('/') }}" class="noble-ui-logo d-block mb-2">EzQanoon</a>
                        <h5 class="text-muted fw-normal mb-4">Welcome back! Log in to your account.</h5>

                        <!-- Display Error Messages -->
                        <div id="ajax-errors" class="alert alert-danger d-none"></div>

                        <ul class="nav nav-pills nav-fill mb-4">
                            <li class="nav-item bg-light">
                                <a class="nav-link" href="{{ route('lawyer.signin') }}">Signin</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Signup</a>
                            </li>
                        </ul>

                        <!-- Signup Form -->
                        <form id="signup-form">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="name" class="form-control" id="name" name="name"
                                    placeholder="name" value="{{ old('name') }}" required>
                                <small id="name-error" class="text-danger"></small>
                            </div>

                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" name="gender" id="gender" required>
                                    <option value="male" {{ old('gender') == 'MALE' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'FEMALE' ? 'selected' : '' }}>Female
                                    </option>
                                    <option value="other" {{ old('gender') == 'OTHER' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                <small id="gender-error" class="text-danger"></small>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="email" value="{{ old('email') }}" required>
                                <small id="email-error" class="text-danger"></small>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <div class="input-group">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder="Phone" value="{{ old('phone') }}" required
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <button type="button" class="btn btn-primary" id="send-otp-btn">send otp</button>
                                </div>
                                <small id="phone-error" class="text-danger"></small>
                            </div>

                            <div class="mb-3 d-none" id="otp-section">
                                <label for="otp" class="form-label">Verify otp</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="otp" placeholder="enter otp"
                                        maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <button type="button" class="btn btn-primary" id="verify-otp-btn">verify otp</button>
                                </div>
                                <small id="otp-message" class="text-danger"></small>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    autocomplete="current-password" placeholder="Password" required>
                                <small id="password-error" class="text-danger"></small>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    placeholder="Confirm password" name="password_confirmation" required>
                                <small id="password_confirmation-error" class="text-danger"></small>
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

    @push('style')
        <style>
            .iti{
                flex-grow: 1;
            }
        </style>
    @endpush

    @push('plugin-styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
        <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    @endpush

    @push('plugin-scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
        <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @endpush

    @push('custom-scripts')
    <script>
        $(document).ready(function() {
            let isPhoneVerified = false;
            let canResendOtp = false;
            let countdownInterval;

            // Initialize phone input
            const iti = intlTelInput(document.querySelector("#phone"), {
                onlyCountries: ["pk", "us", "gb"],
                separateDialCode: true,
                initialCountry: "pk",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            });

            function startCountdown() {
                let timeLeft = 60;
                canResendOtp = false;
                
                $('#send-otp-btn').prop('disabled', true).html(`Resend OTP in ${timeLeft}s`);
                clearInterval(countdownInterval);
                
                countdownInterval = setInterval(() => {
                    timeLeft--;
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        canResendOtp = true;
                        $('#send-otp-btn').prop('disabled', false).html('Resend OTP');
                    } else {
                        $('#send-otp-btn').html(`Resend OTP in ${timeLeft}s`);
                    }
                }, 1000);
            }

            async function handleOtpOperation(operation) {
                if (operation === 'resend' && !canResendOtp) return;

                const phone = iti.getNumber();
                if (!iti.isValidNumber()) {
                    Swal.fire('Error', 'Please enter a valid phone number', 'error');
                    return;
                }

                const $btn = operation === 'verify' ? $('#verify-otp-btn') : $('#send-otp-btn');
                const otp  = operation === 'verify' ? $('#otp').val() : null;
                
                if (operation === 'verify' && !otp) {
                    Swal.fire('Error', 'Please enter OTP', 'error');
                    return;
                }

                try {

                    const verifyURL = "{{route('lawyer.otp.verify')}}"
                    const sendURL   = "{{route('lawyer.otp.send')}}"
                    
                    await $.ajax({
                        url: operation === 'verify' ?  verifyURL : sendURL,
                        type: 'POST',
                        data: {
                            phone,
                            country_code: iti.getSelectedCountryData().dialCode,
                            otp,
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: ()=>{
                            $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);
                        },
                        complete: () => {
                            $btn.prop('disabled', false).html(operation === 'verify' ? 'Verify OTP' : 'Send OTP');
                        }
                    });

                    if (operation === 'verify') {
                        isPhoneVerified = true;
                        Swal.fire({
                            title: 'Success',
                            text: 'Phone number verified successfully',
                            icon: 'success',
                            timer: 1500
                        });
                        $('#otp-section').addClass('d-none');
                        $('#phone').prop('readonly', true);
                        $('#send-otp-btn').prop('disabled', true).html('Verified ✓');
                        clearInterval(countdownInterval);
                    } else {
                        $('#phone').prop('readonly', true);
                        $('#otp-section').removeClass('d-none').find('#otp').prop('required', true);
                        startCountdown();
                    }
                } catch (error) {
                    Swal.fire('Error', error.responseJSON?.message || 'Operation failed', 'error');
                }
            }

            // Event Handlers
            $('#send-otp-btn').click(() => 
                handleOtpOperation($('#send-otp-btn').html().includes('Resend') ? 'resend' : 'send')
            );
            
            $('#verify-otp-btn').click(() => handleOtpOperation('verify'));

            $('#signup-form').submit(function(e) {
                e.preventDefault();
                $('.text-danger').text('');
                $('#ajax-errors').addClass('d-none').text('');

                if (!isPhoneVerified) {
                    $('#ajax-errors').removeClass('d-none').text('Please verify your phone number first');
                    return;
                }

                const formData = new FormData(this);
                formData.set('phone', iti.getNumber());
                formData.set('country_code', iti.getSelectedCountryData().dialCode);
                
                const $submitBtn = $(this).find('button[type="submit"]');
                
                $.ajax({
                    url: '{{ route('lawyer.signup') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: () => {
                        $submitBtn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);
                    },
                    success: () => window.location.href = '{{ route("lawyer.signin") }}',
                    error: (xhr) => {
                        Object.entries(xhr.responseJSON.errors || {}).forEach(([field, message]) => {
                            $(`#${field}-error`).text(message);
                        });
                    },
                    complete: () => {
                        $submitBtn.html('Create account').prop('disabled', false);
                        clearInterval(countdownInterval);
                    }
                });
            });
        });
    </script>
    @endpush
</x-lawyer.guest>
