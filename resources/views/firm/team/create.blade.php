<x-firm.app>
    <div>
        <a href="{{ route('firm.team.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    {{-- Display validation errors (if any) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Create user</h4>

            {{-- The form --}}
            <form action="{{ route('firm.team.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Personal info --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g. Musaafa"
                            required
                        >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input 
                                type="tel" 
                                class="form-control" 
                                id="phone" 
                                value="{{ old('phone') }}" 
                                required
                            >
                        </div>
                        <input type="hidden" name="phone">
                        <input type="hidden" name="country_code">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="e.g. test@gmail.com"
                            required
                        >
                    </div>
                    <div class="col-md-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="•••••••••••••••"
                                required
                            >
                            <button
                                class="btn btn-xs btn-outline-secondary"
                                type="button"
                                id="toggle-password"
                                tabindex="-1"
                                style="z-index: 0"
                            >
                                <i data-feather="eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="password-confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input
                                type="password"
                                class="form-control"
                                id="password-confirmation"
                                name="password_confirmation"
                                placeholder="•••••••••••••••"
                                required
                            >
                            <button
                                class="btn btn-xs btn-outline-secondary"
                                type="button"
                                id="toggle-password-confirmation"
                                tabindex="-1"
                                style="z-index: 0"
                            >
                                <i data-feather="eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-3" style="display: flow-root">
                    <label for="permissions" class="form-label">Permissions</label>
                    <select
                        class="form-select"
                        id="permissions"
                        name="permissions[]"
                        multiple
                    >
                        @foreach (getPermissionsList() as $key => $permission)
                            @if (is_array($permission))
                                <optgroup label="{{ $key }}">
                                    @foreach ($permission as $subKey => $subPermission)
                                        <option
                                            value="{{ $subKey }}"
                                            {{ in_array($subKey, old('permissions', $user->permissions ?? [])) ? 'selected' : '' }}
                                        >
                                            {{ $subPermission }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @else
                                <option
                                    value="{{ $key }}"
                                    {{ in_array($key, old('permissions', $user->permissions ?? [])) ? 'selected' : '' }}
                                >
                                    {{ $permission }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    <div class="btn-group mt-2 float-end">
                        <button
                            type="button"
                            class="btn btn-xs btn-success"
                            id="select-all-permissions"
                        >
                            Select All Permissions
                        </button>
                        <button
                            type="button"
                            class="btn btn-xs btn-danger"
                            id="revoke-all-permissions"
                        >
                            Revoke All Permissions
                        </button>
                    </div>
                </div>

                {{-- Submit button --}}
                <button
                    type="submit"
                    class="btn btn-primary"
                    id="submit-btn"
                >
                    Add Member
                </button>
            </form>
        </div>
    </div>

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet">
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    @endpush

    @push('custom-scripts')
        <script>
            $(document).ready(function() {

                const iti = intlTelInput(document.querySelector("#phone"), {
                    separateDialCode: true,
                    initialCountry: "pk",
                    loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
                    strictMode: true    
                });

                $('form').on('submit', (eve) => {
                    // Only validate phone if it's not empty
                    if (!iti.isValidNumber()) {
                        eve.preventDefault();
                        alert('Invalid phone');
                        return;
                    }

                    // If valid, set the phone number and country code
                    if ($('#phone').val()) {
                        $('[name="phone"]').val(iti.getNumber());
                        $('[name="country_code"]').val(iti.getSelectedCountryData().iso2);
                    }
                });

                $('#permissions').select2({placeholder: 'Select permission'});

                // Handle select all permissions button
                $('#select-all-permissions').on('click', function() {
                    $('#permissions option').prop('selected', true);
                    $('#permissions').trigger('change');
                });

                // Handle revoke all permissions button
                $('#revoke-all-permissions').on('click', function() {
                    $('#permissions option').prop('selected', false);
                    $('#permissions').trigger('change');
                });

                $('#toggle-password').on('click', function() {
                    const $password = $('#password');
                    const type = $password.attr('type') === 'password' ? 'text' : 'password';

                    $password.attr('type', type);
                    $(this).html(
                        type === 'password'
                            ? '<i data-feather="eye"></i>'
                            : '<i data-feather="eye-off"></i>'
                    );
                    feather.replace();
                });

                $('#toggle-password-confirmation').on('click', function() {
                    const $password = $('#password-confirmation');
                    const type = $password.attr('type') === 'password' ? 'text' : 'password';

                    $password.attr('type', type);
                    $(this).html(
                        type === 'password'
                            ? '<i data-feather="eye"></i>'
                            : '<i data-feather="eye-off"></i>'
                    );
                    feather.replace();
                });
            });
        </script>
    @endpush
</x-firm.app>
