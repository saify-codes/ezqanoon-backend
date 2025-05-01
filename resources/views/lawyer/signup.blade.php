<x-lawyer.guest>
    <div class="page-content d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
            <div class="col-md-8 col-xl-4 mx-auto">
                <div class="card">
                    <div class="auth-form-wrapper px-4 py-5">
                        <a href="{{ url('/') }}" class="noble-ui-logo d-block mb-5 text-center">
                            <img src="{{asset('logo.png')}}" alt="logo" style="height: 100px">
                        </a>
                        <!-- Display Error Messages -->
                        <div id="otp-error" class="alert alert-danger d-none"></div>

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
                                    <input type="tel" class="form-control" id="phone"placeholder="Phone" value="{{ old('phone') }}" required>
                                    <input type="hidden" name="phone">
                                    <input type="hidden" name="country_code">
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

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @endpush

    @push('style')
        <style>
            .iti{
                width: auto;
                flex: 1;
            }
        </style>
    @endpush

    @push('custom-scripts')
    <script>
        $(document).ready(function() {
            let countdownInterval;
            let isPhoneVerified = false;
            let canResendOtp    = false;
            const iti           = intlTelInput(document.querySelector("#phone"), {
                                    separateDialCode: true,
                                    initialCountry: "pk",
                                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                                    strictMode: true});

            function startCountdown() {
                let timeLeft = 60;
                $('#send-otp-btn').prop('disabled', true).html(`resend otp in ${timeLeft}s`);
                clearInterval(countdownInterval);
                
                countdownInterval = setInterval(() => {
                    timeLeft--
                    $('#send-otp-btn').html(`resend otp in ${timeLeft}s`);
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        $('#send-otp-btn')
                            .html('resend otp')
                            .prop('disabled', false);
                    }
                }, 1000);
            }

            async function sendOtp(){

                try {

                    await $.ajax({
                        url: "{{route('lawyer.otp.send')}}",
                        type: 'POST',
                        data: {phone, country_code: countryCode, otp, _token: '{{ csrf_token() }}'},
                        beforeSend: ()  =>  $(this).html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true),
                        complete: ()    =>  $(this).prop('disabled', false).html('send otp'),
                        success: ()     => {
                            $(this).attr('onclick', "verifyOtp()")
                        }
                    });

                    
                } catch (error) {
                    Swal.fire('Error', error.responseJSON?.message || 'Operation failed', 'error');
                }
            }

            async function handleOtpOperation(operation) {
                
                if (operation === 'resend' && !canResendOtp){
                    return
                }

                if (!iti.isValidNumber()) {
                    Swal.fire('Error', 'Please enter a valid phone number', 'error');
                    return;
                }

                const phone         = iti.getNumber()
                const countryCode   = iti.getSelectedCountryData().iso2
                const $btn          = operation === 'verify' ? $('#verify-otp-btn') : $('#send-otp-btn');
                const otp           = operation === 'verify' ? $('#otp').val() : null;
                
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
                            country_code: countryCode,
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
            $('#send-otp-btn').click(() => handleOtpOperation($('#send-otp-btn').html().includes('Resend') ? 'resend' : 'send'));
            $('#verify-otp-btn').click(() => handleOtpOperation('verify'));
            $('form').submit(function(eve) {

                eve.preventDefault();

                if (!isPhoneVerified) {
                    $('#otp-error').removeClass('d-none').text('Please verify your phone number first');
                    return;
                }

                if ($('#phone').val() && !iti.isValidNumber()) {
                    alert('Invalid phone');
                    return;
                }

                $('[name="phone"]').val(iti.getNumber());
                $('[name="country_code"]').val(iti.getSelectedCountryData().iso2);
                $('.text-danger').text('');
                $('#otp-error').addClass('d-none').text('');

                $.ajax({
                    url: '{{ route('lawyer.signup') }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    processData: false,
                    beforeSend: ()      => $('button[type="submit"]').html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true),
                    success:    ()      => window.location.href = '{{ route("lawyer.signin") }}',
                    error:      (xhr)   => Object.entries(xhr.responseJSON.errors || {}).forEach(([field, message]) => $(`#${field}-error`).text(message)),
                    complete:   ()      => $('button[type="submit"]').html('Create account').prop('disabled', false)
                });
            });
        });
    </script>
    @endpush
</x-lawyer.guest>
