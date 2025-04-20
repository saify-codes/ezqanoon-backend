<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.appointment.index') }}" class="btn btn-dark btn-icon-text mb-3">
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
            <h4 class="card-title mb-4">Edit Appointment</h4>

            <!-- The form -->
            <form action="{{ route('lawyer.appointment.update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Summary -->
                <div class="mb-3">
                    <label for="summary" class="form-label">Summary</label>
                    <textarea class="form-control" id="summary" name="summary" rows="3" placeholder="Enter opposite party details">{{ old('summary', $appointment->summary) }}</textarea>
                </div>

                <!-- Attachments -->
                <div class="mb-3">
                    <label for="attachments" class="form-label">Attachments (optional)</label>
                    {{-- <input type="file" class="form-control" id="attachments" name="attachments[]" multiple> --}}
                    <div id="aerodrop" class="aerodrop mb-3"></div>
                    <!-- Note for users -->
                    <small class="text-muted d-block mt-1">
                        - Maximum <strong>10 files</strong> allowed.<br>
                        - Allowed file types: <strong>PNG, JPG, WEBP, PDF, DOC, DOCX</strong>.<br>
                        - <strong>Images</strong> must not exceed <strong>2MB</strong> each.<br>
                        - <strong>Documents/PDFs</strong> must not exceed <strong>10MB</strong> each.
                    </small>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Update Appointment
                </button>
            </form>
        </div>
    </div>

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/aerodrop/aerodrop.min.css') }}" rel="stylesheet">
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/aerodrop/aerodrop.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/ckeditor/ckeditor.min.js') }}"></script>
    @endpush

    @push('custom-scripts')
        <script>
            $(document).ready(function() {

                // AeroDrop initialization with upload state management.
                const aerodrop = new AeroDrop(document.querySelector('#aerodrop'), {
                    name: 'attachments',
                    uploadURL: '/upload',
                    enableCamera: true,
                    maxFiles: 10,
                    allowedFileTypes: ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
                    fileSizeRules: [{
                            types: ['image/jpeg', 'image/png', 'image/webp'],
                            maxSize: 2 * 1024 * 1024,
                            error: "Image file too big"
                        },
                        {
                            types: ['application/pdf'],
                            maxSize: 10 * 1024 * 1024,
                            error: "PDF file too big"
                        }
                    ],
                    fallbackError: "File too big",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    },
                });

                aerodrop.onupload = function(res) {
                    console.log("Upload successful:", res);
                };

                aerodrop.onerror = function(error) {
                    errorMessage(error)
                    console.error("Upload error:", error);
                };

                const editorConfig = {
                    toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'link']
                };

                ClassicEditor
                    .create(document.querySelector('#summary'), editorConfig)
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '100px';
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });

            // Disable form until all pending uploads are processed
            new MutationObserver(function(mutationsList, observer) {
                $('#submitBtn').prop('disabled', $('#aerodrop').attr('data-loading') === 'true')
            }).observe($('#aerodrop')[0], {
                attributes: true,
                attributeFilter: ['data-loading']
            });
        </script>
    @endpush
</x-lawyer.app>
