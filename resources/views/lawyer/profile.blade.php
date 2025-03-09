<x-lawyer.app>
    {{-- Display success message if available --}}
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round"
                 class="feather feather-alert-circle">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- PROFILE UPDATE FORM --}}
    <div class="card mb-5">
        <div class="card-body">
            <h6 class="card-title">Profile Form</h6>

            <form action="{{ route('lawyer.profile') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Email (disabled) --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="{{ old('email', $lawyer->email) }}"
                        disabled
                    >
                    @error('email')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Phone (disabled) --}}
                <div class="mb-3">
                    <label for="phone" class="form-label">Contact phone</label>
                    <input
                        type="tel"
                        class="form-control"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $lawyer->phone) }}"
                        disabled
                    >
                    @error('phone')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        name="name"
                        value="{{ old('name', $lawyer->name) }}"
                        required
                    >
                    @error('name')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Location --}}
                <div class="mb-3">
                    <label for="location" class="form-label">Office location</label>
                    <input
                        type="text"
                        class="form-control"
                        id="location"
                        name="location"
                        value="{{ old('location', $lawyer->location) }}"
                        required
                    >
                    @error('location')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Specialization --}}
                <div class="mb-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <input
                        type="text"
                        class="form-control"
                        id="specialization"
                        name="specialization"
                        value="{{ old('specialization', $lawyer->specialization) }}"
                        required
                    >
                    @error('specialization')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Qualification --}}
                <div class="mb-3">
                    <label for="qualification" class="form-label">Qualification</label>
                    <input
                        type="text"
                        class="form-control"
                        id="qualification"
                        name="qualification"
                        value="{{ old('qualification', $lawyer->qualification) }}"
                        required
                    >
                    @error('qualification')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Experience --}}
                <div class="mb-3">
                    <label for="experience" class="form-label">Experience in years</label>
                    <input
                        type="number"
                        min="0"
                        class="form-control"
                        id="experience"
                        name="experience"
                        value="{{ old('experience', $lawyer->experience) }}"
                        required
                    >
                    @error('experience')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Consulting Price --}}
                <div class="mb-3">
                    <label for="price" class="form-label">Consulting price</label>
                    <input
                        type="number"
                        min="0"
                        class="form-control"
                        id="price"
                        name="price"
                        value="{{ old('price', $lawyer->price) }}"
                        required
                    >
                    @error('price')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Availability --}}
                <div class="row">
                    <h6 class="card-title">Availability</h6>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="availability_from" class="form-label">From</label>
                            <input
                                type="time"
                                name="availability_from"
                                class="form-control"
                                value="{{ old('availability_from', $lawyer->availability_from) }}"
                                required
                            >
                            @error('availability_from')
                                <small class="d-block text-danger mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="availability_to" class="form-label">To</label>
                            <input
                                type="time"
                                name="availability_to"
                                class="form-control"
                                value="{{ old('availability_to', $lawyer->availability_to) }}"
                                required
                            >
                            @error('availability_to')
                                <small class="d-block text-danger mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label">About</label>
                    <textarea
                        id="description"
                        class="form-control"
                        rows="5"
                        name="description"
                        required
                    >{{ old('description', $lawyer->description) }}</textarea>
                    @error('description')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary me-2">Save</button>
            </form>
        </div>
    </div>

    {{-- RESET PASSWORD FORM --}}
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Password reset Form</h6>

            <form action="{{ route('lawyer.reset-password') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- New Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">New password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        required
                    >
                    @error('password')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                    >
                    @error('password_confirmation')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-danger me-2">Change password</button>
            </form>
        </div>
    </div>
</x-lawyer.app>
