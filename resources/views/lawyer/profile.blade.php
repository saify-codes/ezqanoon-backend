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

    {{-- PROFILE UPDATE FORM --}}
    <div class="card mb-5">
        <div class="card-body">
            <h6 class="card-title">Profile Form</h6>

            <form action="{{ route('lawyer.profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- === IMAGE / DOCUMENT UPLOADERS ==================================== --}}
                @php
                    $lawyer = Auth::guard('lawyer')->user();
                @endphp

                <div class="d-flex gap-2 flex-wrap mb-4">
                    {{-- Avatar --}}
                    <div class="uploader"
                        data-type="avatar"
                        data-label="Avatar"
                        data-url="{{ str_ends_with($lawyer->avatar, '/assets/images/avatar.jpg') ? '' : $lawyer->avatar }}">
                    </div>

                    {{-- Selfie --}}
                    <div class="uploader"
                        data-type="selfie"
                        data-label="Selfie"
                        data-url="{{ $lawyer->selfie ?? '' }}">
                    </div>

                    {{-- Licence – front --}}
                    <div class="uploader"
                        data-type="licence_front"
                        data-label="Front licence"
                        data-url="{{ $lawyer->licence_front ?? '' }}">
                    </div>

                    {{-- Licence – back --}}
                    <div class="uploader"
                        data-type="licence_back"
                        data-label="Back licence"
                        data-url="{{ $lawyer->licence_back ?? '' }}">
                    </div>
                </div>


                {{-- Email (disabled) --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="{{ old('email', $lawyer->email) }}" disabled>
                </div>

                {{-- Phone (disabled) --}}
                <div class="mb-3">
                    <label for="phone" class="form-label">Contact phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                        value="{{ old('phone', $lawyer->phone) }}" disabled>
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $lawyer->name) }}" required>
                </div>

                {{-- Licence --}}
                <div class="mb-3">
                    <label for="licence_number" class="form-label">Licence number</label>
                    <input type="text" class="form-control" id="licence_number" name="licence_number"
                        value="{{ old('licence_number', $lawyer->licence_number) }}" required>
                </div>
                
                {{-- CNIC --}}
                <div class="mb-3">
                    <label for="cnic" class="form-label">CNIC</label>
                    <input type="text" class="form-control" id="cnic" name="cnic"
                        value="{{ old('cnic', $lawyer->cnic) }}" required>
                </div>

                {{-- City --}}
                <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city"
                        value="{{ old('city', $lawyer->city) }}" required>
                </div>

                {{-- Country --}}
                <div class="mb-3">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="country" name="country"
                        value="{{ old('country', $lawyer->country) }}">
                </div>

                {{-- Location --}}
                <div class="mb-3">
                    <label for="location" class="form-label">Office location</label>
                    <input type="text" class="form-control" id="location" name="location"
                        value="{{ old('location', $lawyer->location) }}" required>
                </div>

                {{-- Specialization --}}
                <div class="mb-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <select type="text" class="form-select" id="specialization" name="specialization[]" multiple required>
                        @foreach (getSpecializationList() as $specialization)
                            <option value="{{$specialization}}" {{in_array($specialization, $lawyer->specialization ?? []) ? 'selected' : ''}}>{{$specialization}}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Qualification --}}
                <div class="mb-3">
                    <label for="qualification" class="form-label">Qualification</label>
                    <input type="text" class="form-control" id="qualification" name="qualification"
                        value="{{ old('qualification', $lawyer->qualification) }}" required>
                </div>

                {{-- Experience --}}
                <div class="mb-3">
                    <label for="experience" class="form-label">Experience in years</label>
                    <input type="number" min="0" class="form-control" id="experience" name="experience"
                        value="{{ old('experience', $lawyer->experience) }}" required>
                </div>

                {{-- Consulting Price --}}
                <div class="mb-3">
                    <label for="price" class="form-label">Consulting price</label>
                    <input type="number" min="0" class="form-control" id="price" name="price"
                        value="{{ old('price', $lawyer->price) }}" required>
                </div>

                {{-- Availability --}}
                <div class="mb-3">
                    <h6 class="card-title">Availability</h6>
                    @php
                        $availabilities = old('availability', $lawyer->availabilities->groupBy('day')->map(fn($availability) => $availability->pluck('time')));
                        $days           = [
                                            'mon' => 'Monday',
                                            'tue' => 'Tuesday',
                                            'wed' => 'Wednesday',
                                            'thu' => 'Thursday',
                                            'fri' => 'Friday',
                                            'sat' => 'Saturday',
                                            'sun' => 'Sunday',
                                        ];
                        
                    @endphp

                    @foreach ($days as $key => $value)
                    <div class="mb-3">
                        <label class="form-label">{{ $value }}</label>
                        <div class="availability-day" data-day="{{ $key }}">

                            @foreach ($availabilities[$key] ?? [] as $time)
                                <div class="input-group mb-2 time-slot">
                                    <input type="time" name="availability[{{ $key }}][]" class="form-control" value="{{ $time }}" required>
                                    <button type="button" class="btn btn-outline-danger remove-time">Remove</button>
                                </div>
                            @endforeach

                            <button type="button" class="btn btn-sm btn-outline-primary add-time">Add time</button>
                        </div>
                    </div>
                    @endforeach
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

                {{-- Conlawyer Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Conlawyer password</label>
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

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet">
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    @endpush

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

            .preview .discard-btn {
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

            .preview:hover .discard-btn {
                top: 0;
                opacity: 1;
            }
        </style>
    @endpush

    @push('custom-scripts')
        <script>
            /* -- base path for every request -------------------------------- */
            const baseRoute = '{{ route("lawyer.file.upload", ":type") }}';

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
                            <button type="button" class="discard-btn btn btn-danger btn-sm">
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
                    $box.find('.discard-btn').on('click', e => {
                        e.stopPropagation();
                        Swal.fire({
                            title: `Delete ${label}?`,
                            icon:  'warning',
                            showCancelButton: true,
                            conlawyerButtonColor: 'var(--bs-primary)'
                        }).then(r => r.isConfirmed && deleteFile(e.target));
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
                        success: () => { successMessage(`${label} removed`); renderUploader() },
                        error:   () => { errorMessage('Delete failed')}
                    });
                }

            });

            $('.availability-day').on('click', '.add-time', function() {
                const $container    = $(this).closest('.availability-day');
                const day           = $container.data('day');
                const newInput      = $(`
                    <div class="input-group mb-2 time-slot">
                        <input type="time" name="availability[${day}][]" class="form-control" required>
                        <button type="button" class="btn btn-outline-danger remove-time">Remove</button>
                    </div>
                `);

                if ($container.children().length <= 10) {
                    newInput.insertBefore($(this));
                } else {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Max 10 slots can be used',
                        icon: 'error'
                    });
                }
            });

            // Remove time slot input
            $('.availability-day').on('click', '.remove-time', function() {
                const $container = $(this).closest('.availability-day');
                $(this).closest('.time-slot').remove();
            });

            $('#specialization').select2({placeholder: 'select specialization'})

        </script>
    @endpush

</x-lawyer.app>
