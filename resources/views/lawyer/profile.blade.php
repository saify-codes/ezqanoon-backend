<x-lawyer.app>
    {{-- Display success message if available --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle">
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

            <form action="{{ route('lawyer.profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Avatar --}}
                <div class="mb-3">
                    @php
                        $avatar = Auth::user()->avatar;
                    @endphp
                    <div class="avatar" data-url="{{ $avatar == asset('/storage/avatar.jpg') ? '' : $avatar }}"></div>
                </div>


                {{-- Email (disabled) --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="{{ old('email', $lawyer->email) }}" disabled>
                    @error('email')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Phone (disabled) --}}
                <div class="mb-3">
                    <label for="phone" class="form-label">Contact phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                        value="{{ old('phone', $lawyer->phone) }}" disabled>
                    @error('phone')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $lawyer->name) }}" required>
                    @error('name')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Location --}}
                <div class="mb-3">
                    <label for="location" class="form-label">Office location</label>
                    <input type="text" class="form-control" id="location" name="location"
                        value="{{ old('location', $lawyer->location) }}" required>
                    @error('location')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Specialization --}}
                <div class="mb-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <input type="text" class="form-control" id="specialization" name="specialization"
                        value="{{ old('specialization', $lawyer->specialization) }}" required>
                    @error('specialization')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Qualification --}}
                <div class="mb-3">
                    <label for="qualification" class="form-label">Qualification</label>
                    <input type="text" class="form-control" id="qualification" name="qualification"
                        value="{{ old('qualification', $lawyer->qualification) }}" required>
                    @error('qualification')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Experience --}}
                <div class="mb-3">
                    <label for="experience" class="form-label">Experience in years</label>
                    <input type="number" min="0" class="form-control" id="experience" name="experience"
                        value="{{ old('experience', $lawyer->experience) }}" required>
                    @error('experience')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Consulting Price --}}
                <div class="mb-3">
                    <label for="price" class="form-label">Consulting price</label>
                    <input type="number" min="0" class="form-control" id="price" name="price"
                        value="{{ old('price', $lawyer->price) }}" required>
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
                            <input type="time" name="availability_from" class="form-control"
                                value="{{ old('availability_from', $lawyer->availability_from) }}" required>
                            @error('availability_from')
                                <small class="d-block text-danger mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label for="availability_to" class="form-label">To</label>
                            <input type="time" name="availability_to" class="form-control"
                                value="{{ old('availability_to', $lawyer->availability_to) }}" required>
                            @error('availability_to')
                                <small class="d-block text-danger mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label">About</label>
                    <textarea id="description" class="form-control" rows="5" name="description" required>{{ old('description', $lawyer->description) }}</textarea>
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
                    <input type="password" class="form-control" id="password" name="password" required>
                    @error('password')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm password</label>
                    <input type="password" class="form-control" id="password_confirmation"
                        name="password_confirmation" required>
                    @error('password_confirmation')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-danger me-2">Change password</button>
            </form>
        </div>
    </div>

    @push('style')
        <style>
            .avatar {
                width: 200px;
                height: 250px;
                border: 1px solid var(--bs-light);
            }

            .avatar .upload {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                width: 100%;
                height: 100%;
            }

            .avatar .preview {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                width: 100%;
                height: 100%;
                position: relative;
            }

            .avatar .preview:hover::after {
                content: "";
                position: absolute;
                inset: 0;
                background: color-mix(in srgb, var(--bs-danger) 30%, #0000)
            }

            .avatar .preview #discard-btn {
                position: absolute;
                inset: 0;
                margin: auto;
                border: none;
                background: none;
                color: #FFF;
                z-index: 999;
                top: 50%;
                transition: 0.5s ease;
                opacity: 0;
            }

            .avatar .preview:hover #discard-btn {
                top: 0;
                opacity: 1;
            }
        </style>
    @endpush

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @endpush

    @push('custom-scripts')
        <script>
            $(document).ready(function() {
                const imageUrl = $('.avatar').data('url');

                // On load, show preview if an avatar URL exists; otherwise, show the uploader.
                if (imageUrl) {
                    renderPreview(imageUrl);
                } else {
                    renderUploader();
                }

                // Renders uploader UI with a drag and drop area and click-to-select.
                function renderUploader() {
                    const uploaderHTML = `
                        <div class="upload text-muted" style="cursor: pointer; border: 2px dashed #ccc; padding: 20px; text-align: center;">
                            <i data-feather="camera"></i>
                            <p>Drag and drop your avatar here</p>
                        </div>
                        <div class="progress mt-1" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                        </div>
                    `;
                    $('.avatar').html(uploaderHTML);
                    feather.replace();

                    // Bind click to create a temporary file input element
                    $('.upload').on('click', function() {
                        $('<input type="file" accept="image/png, image/jpeg, image/webp" style="display: none;" />')
                            .on('change', function() {
                                const file = this.files[0];
                                if (file) {
                                    handleFile(file);
                                }
                            })
                            .trigger('click');
                    });

                    // Bind drag and drop events to the uploader area.
                    $('.upload').on('dragover', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).css('background-color', '#f0f0f0');
                    });

                    $('.upload').on('dragleave', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).css('background-color', '');
                    });

                    $('.upload').on('drop', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).css('background-color', '');
                        const file = e.originalEvent.dataTransfer.files[0];
                        if (file) {
                            handleFile(file);
                        }
                    });
                }

                // Renders preview UI with the image and a discard button.
                function renderPreview(src) {
                    const previewHTML = `
                        <div class="preview position-relative">
                            <img src="${src}" class="img-fluid">
                            <button type="button" id="discard-btn" class="btn btn-danger btn-sm" style="position: absolute; top: 5px; right: 5px;">
                                <i data-feather="trash"></i>
                            </button>
                        </div>
                    `;
                    $('.avatar').html(previewHTML);
                    feather.replace();
                }

                // Validates the file type and uploads the file.
                function handleFile(file) {
                    const validTypes = ['image/png', 'image/jpeg', 'image/webp'];
                    if ($.inArray(file.type, validTypes) === -1) {
                        alert('Invalid file type');
                        return;
                    }
                    uploadAvatar(file);
                }

                // Uploads the avatar via AJAX while showing upload progress.
                function uploadAvatar(file) {
                    const formData = new FormData();
                    formData.append('avatar', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    $('.progress').show();
                    $('.upload').off('click');

                    $.ajax({
                        url: '{{ route('lawyer.avatar.upload') }}',
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        xhr: function() {
                            const xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function(evt) {
                                if (evt.lengthComputable) {
                                    const percentComplete = Math.round((evt.loaded / evt.total) *
                                        100);
                                    $('.progress-bar').css('width', percentComplete + '%');
                                }
                            }, false);
                            return xhr;
                        },
                        success: function(response) {
                            successMessage('Avatar updated');
                            renderPreview(response.url)
                        },
                        error: function(xhr) {
                            errorMessage('Error uploading');
                            renderUploader();
                        },
                    });
                }

                // When the discard button is clicked, confirm and call the delete API.
                $('.avatar').on('click', '#discard-btn', function(e) {
                    e.stopPropagation();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Are you sure you want to delete this avatar?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--bs-primary)',
                        cancelButtonColor: 'var(--bs-danger)',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('lawyer.avatar.delete') }}',
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    successMessage('Avatar deleted');
                                    renderUploader();
                                },
                                error: function(xhr) {
                                    errorMessage('Something went wrong');
                                }
                            });
                        }
                    })
                  
                });
            });
        </script>
    @endpush

</x-lawyer.app>
