<x-team.app>
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

            <form action="{{ route('team.profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- === IMAGE / DOCUMENT UPLOADERS ==================================== --}}
                @php
                    $team = Auth::guard('team')->user();
                @endphp

                <div class="d-flex gap-2 flex-wrap mb-4">
                    {{-- Avatar --}}
                    <div class="uploader"
                        data-type="avatar"
                        data-label="Avatar"
                        data-url="{{ str_ends_with($team->avatar, '/assets/images/avatar.jpg') ? '' : $team->avatar }}">
                    </div>
                </div>


                {{-- Email (disabled) --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="{{ old('email', $team->email) }}" disabled>
                    @error('email')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Phone (disabled) --}}
                <div class="mb-3">
                    <label for="phone" class="form-label">Contact phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                        value="{{ old('phone', $team->phone) }}" disabled>
                    @error('phone')
                        <small class="d-block text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $team->name) }}" required>
                    @error('name')
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

            <form action="{{ route('team.reset-password') }}" method="POST">
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
            .uploader {
                width: 200px;
                height: 250px;
                border: 1px solid var(--bs-light);
            }

            .upload,
            .preview {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: .5rem;
                cursor: pointer;
                width: 100%;
                height: 100%;
            }

            .upload {
                border: 2px dashed #ccc;
            }

            .preview {
                position: relative;
            }

            .preview:hover::after {
                content: "";
                position: absolute;
                inset: 0;
                background: color-mix(in srgb, var(--bs-danger) 30%, #0000);
            }

            .preview #discard-btn {
                position: absolute;
                inset: 0;
                margin: auto;
                border: none;
                background: none;
                color: #fff;
                top: 50%;
                transition: .4s;
                opacity: 0;
                z-index: 1;
            }

            .preview:hover #discard-btn {
                top: 0;
                opacity: 1;
            }
        </style>
    @endpush

    @push('custom-scripts')
        <script>
            /* -- base path for every request -------------------------------- */
            const baseRoute = '{{ route("team.file.upload", ":type") }}';

            $('.uploader').each(function () {

                /* ----------------------------------------------------------- */
                /*   SET-UP                                                   */
                /* ----------------------------------------------------------- */
                const $box   = $(this);
                const type   = $box.data('type');   // avatar | selfie | licence_front …
                const label  = $box.data('label');  // pretty label
                const url    = $box.data('url');    // current file or ""
                const endpt  = baseRoute.replace(':type', type); // POST & DELETE hit the same URL

                url ? renderPreview(url) : renderUploader();

                /* ----------------------------------------------------------- */
                /*   VIEW HELPERS                                              */
                /* ----------------------------------------------------------- */
                function renderUploader () {
                    $box.html(`
                        <div class="upload text-muted">
                            <i data-feather="camera"></i>
                            <p class="fw-semibold mb-0">Upload ${label}</p>
                            <small>Drag & drop or click</small>
                        </div>
                        <div class="progress mt-1" style="display:none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div>
                        </div>
                    `);
                    feather.replace();
                    bindPickers();
                }

                function renderPreview (src) {
                    $box.html(`
                        <div class="preview">
                            <img src="${src}" style="width:100%;height:100%;object-fit:contain;">
                            <button id="discard-btn" type="button" class="btn btn-danger btn-sm">
                                <i data-feather="trash"></i>
                            </button>
                        </div>
                    `);
                    feather.replace();
                    bindDelete();
                }


                /* ----------------------------------------------------------- */
                /*   PICK FILE – CLICK or DRAG&DROP                            */
                /* ----------------------------------------------------------- */
                function bindPickers () {
                    const $up = $box.find('.upload');

                    // click
                    $up.on('click', () =>
                        $('<input type="file" accept="image/png,image/jpeg,image/webp" hidden>')
                            .on('change', e => handleFile(e.target.files[0]))
                            .trigger('click')
                    );

                    // drag-over feedback
                    $up.on('dragover',  e => { e.preventDefault(); $up.css('background','#f0f0f0'); })
                    .on('dragleave', e => { e.preventDefault(); $up.css('background','');          })
                    .on('drop',      e => {
                            e.preventDefault(); $up.css('background','');
                            handleFile(e.originalEvent.dataTransfer.files[0]);
                    });
                }


                /* ----------------------------------------------------------- */
                /*   DELETE CURRENT FILE                                       */
                /* ----------------------------------------------------------- */
                function bindDelete () {
                    $box.find('#discard-btn').on('click', e => {
                        e.stopPropagation();
                        Swal.fire({
                            title: `Delete ${label}?`,
                            icon:  'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--bs-primary)'
                        }).then(r => r.isConfirmed && deleteFile());
                    });
                }


                /* ----------------------------------------------------------- */
                /*   VALIDATE + SEND FILE                                      */
                /* ----------------------------------------------------------- */
                function handleFile (file) {
                    if (!file) return;

                    const okTypes = ['image/png','image/jpeg','image/webp'];
                    if (!okTypes.includes(file.type)) return errorMessage('Unsupported type');
                    if (file.size > 2*1024*1024)      return errorMessage('Max size 2 MB');

                    const fd = new FormData();
                    fd.append('file', file);
                    fd.append('_token', '{{ csrf_token() }}');

                    $box.find('.progress').show();

                    $.ajax({
                        url:  endpt,
                        type: 'POST',
                        data: fd,
                        contentType: false,
                        processData: false,
                        xhr () {
                            const x = $.ajaxSettings.xhr();
                            x.upload.onprogress = evt => {
                                if (evt.lengthComputable) {
                                    $box.find('.progress-bar')
                                        .css('width', (evt.loaded / evt.total * 100) + '%');
                                }
                            };
                            return x;
                        },
                        success: res => { successMessage(`${label} uploaded`); renderPreview(res.url); },
                        error:   ()  => { errorMessage('Upload error');        renderUploader();       }
                    });
                }


                /* ----------------------------------------------------------- */
                /*   DELETE REQUEST                                            */
                /* ----------------------------------------------------------- */
                function deleteFile () {
                    $.ajax({
                        url:  endpt,     // same endpoint, just a DELETE verb
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: () => { successMessage(`${label} removed`); renderUploader(); },
                        error:   () => { errorMessage('Delete failed');                      }
                    });
                }

            });

        </script>
    @endpush

</x-team.app>
