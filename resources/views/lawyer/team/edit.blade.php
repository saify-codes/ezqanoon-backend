<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.team.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>
    <!-- Display validation errors (if any) -->
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
            <h4 class="card-title mb-4">Edit user</h4>

            <!-- The form -->
            <form action="{{ route('lawyer.team.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') <!-- This is important for the update action -->

                <!-- Personal info -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $user->name) }}" placeholder="e.g. Musaafa" required>
                    </div>
                    <div class="col-md-4">
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="{{ old('phone', $user->phone) }}" placeholder="e.g. +923487161543" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="email" name="email"
                            value="{{ old('email', $user->email) }}" disabled>
                    </div>
                </div>

                <div class="mb-3" style="display: flow-root">
                    <label for="permissions" class="form-label">Permissions</label>
                    <select class="form-select" id="permissions" name="permissions[]" multiple>
                        @foreach (getPermissionsList() as $key => $permission)
                            @if (is_array($permission)) 
                                <optgroup label="{{ $key }}">
                                    @foreach ($permission as $key => $permission)
                                        <option value="{{ $key }}" {{ in_array($key, old('permissions', $user->permissions ?? [])) ? 'selected' : '' }}>
                                            {{ $permission }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @else
                                <option value="{{ $key }}"{{ in_array($key, old('permissions', $user->permissions ?? [])) ? 'selected' : '' }}>
                                    {{ $permission }}
                                </option>  
                            @endif
                        @endforeach
                    </select>
                    <div class="btn-group mt-2 float-end">
                        <button type="button" class="btn btn-xs btn-success" id="selectAllPermissions">Select All Permissions</button>
                        <button type="button" class="btn btn-xs btn-danger" id="revokeAllPermissions">Revoke All Permissions</button>
                    </div>
                </div>

                <!-- Submit button (with an ID for enabling/disabling) -->
                <button type="submit" class="btn btn-primary" id="submitBtn">Update Member</button>

            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title mb-4">Change Password</h4>
            <form action="{{ route('lawyer.team.change-password', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="•••••••••••••••" required>
                            <button class="btn btn-xs btn-outline-secondary" type="button" id="togglePassword"
                                tabindex="-1"><i data-feather="eye"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="•••••••••••••••" required>
                            <button class="btn btn-xs btn-outline-secondary" type="button" id="togglePasswordConfirmation" tabindex="-1"><i data-feather="eye"></i></button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-danger">Change Password</button>
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
                $('#permissions').select2();

                // Handle select all permissions button
                $('#selectAllPermissions').on('click', function() {
                    $('#permissions option').prop('selected', true);
                    $('#permissions').trigger('change');
                });

                // Handle revoke all permissions button
                $('#revokeAllPermissions').on('click', function() {
                    $('#permissions option').prop('selected', false);
                    $('#permissions').trigger('change');
                });

                $('#togglePassword').on('click', function() {
                    const $password = $('#password');
                    const type = $password.attr('type') === 'password' ? 'text' : 'password';

                    $password.attr('type', type);
                    $(this).html(type === 'password' ? '<i data-feather="eye"></i>' :
                        '<i data-feather="eye-off"></i>');
                    feather.replace();
                });

                $('#togglePasswordConfirmation').on('click', function() {
                    const $password = $('#password_confirmation');
                    const type      = $password.attr('type') === 'password' ? 'text' : 'password';
                    
                    $password.attr('type', type);
                    $(this).html(type === 'password' ? '<i data-feather="eye"></i>' : '<i data-feather="eye-off"></i>');
                    feather.replace();
                });
            });
        </script>
    @endpush
</x-lawyer.app>
