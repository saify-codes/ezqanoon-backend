<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.client.index') }}" class="btn btn-dark btn-icon-text mb-3">
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
            <h4 class="card-title mb-4">Create client</h4>

            <!-- The form -->
            <form action="{{ route('lawyer.client.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Personal info -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                            value="{{ old('first_name') }}" placeholder="e.g. Musaafa" required>
                    </div>
                    <div class="col-md-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                            value="{{ old('last_name') }}" placeholder="e.g. Ahmed" required>
                    </div>
                    <div class="col-md-2">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="MALE" {{ old('gender') === 'MALE' ? 'selected' : '' }}>Male</option>
                            <option value="FEMALE" {{ old('gender') === 'FEMALE' ? 'selected' : '' }}>Female</option>
                            <option value="OTHER" {{ old('gender') === 'OTHER' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="type" class="form-label">Client type <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="REGULAR" {{ old('type') === 'REGULAR' ? 'selected' : '' }}>Regular</option>
                            <option value="VIP" {{ old('type') === 'VIP' ? 'selected' : '' }}>VIP</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="dob" class="form-label">DOB</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">address</label>
                    <input type="text" class="form-control" name="address" value="{{ old('address') }}" placeholder="e.g. St#44 sadar, karachi">
                </div>
                
                <!-- Contact details -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="urgency" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. +923487161543">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. test@gmail.com">
                    </div>
                </div>
                
                <!-- Company details -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Company name</label>
                        <input type="tel" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Martin max Co.">
                    </div>
                    <div class="col-md-6">
                        <label for="company_website" class="form-label">Website</label>
                        <input type="text" class="form-control" id="company_website" name="company_website" value="{{ old('company_website') }}" placeholder="e.g. www.martin.co">
                    </div>
                </div>
                
                <!-- Preferences -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="communication_method" class="form-label">Communation method</label>
                        <input type="tel" class="form-control" id="communication_method" name="communication_method" value="{{ old('communication_method') }}" placeholder="e.g. sms">
                    </div>
                    <div class="col-md-4">
                        <label for="contact_time" class="form-label">Contact time</label>
                        <input type="time" class="form-control" id="contact_time" name="contact_time" value="{{ old('contact_time') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="language" class="form-label">Language</label>
                        <input type="text" class="form-control" id="language" name="language" value="{{ old('language') }}" placeholder="e.g. English, Urdu">
                    </div>
                </div>
                
                <!-- Financial details -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="billing_address" class="form-label">Billing address</label>
                        <input type="tel" class="form-control" id="billing_address" name="billing_address" value="{{ old('billing_address') }}" placeholder="e.g. sms">
                    </div>
                    <div class="col-md-4">
                        <label for="payment_methods" class="form-label">Payment methods</label>
                        <select class="form-select" id="payment_methods" name="payment_methods[]" multiple>
                            <option value="credit_card"     {{ in_array('credit_card', old('payment_methods', [])) ? 'selected' : '' }}>Credit Card</option>
                            <option value="bank_transfer"   {{ in_array('bank_transfer', old('payment_methods', [])) ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="paypal"          {{ in_array('paypal', old('payment_methods', [])) ? 'selected' : '' }}>Paypal</option>
                        </select>
                    </div>                    
                    <div class="col-md-4">
                        <label for="tin" class="form-label">Tax identification number (TIN)</label>
                        <input type="text" class="form-control" id="tin" name="tin" value="{{ old('tin') }}" placeholder="e.g. XXXXX-XXXXXX-X">
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <div id="notes-container">
                        <div class="note-row d-flex align-items-center gap-4 mb-3">
                            <textarea class="form-control" rows="5" placeholder="Add a note"></textarea>
                            <button type="button" class="btn btn-sm btn-success add-note"><i data-feather="plus"></i></button>
                        </div>
                    </div>
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

                <!-- Submit button (with an ID for enabling/disabling) -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Add Client
                </button>
            </form>
        </div>
    </div>

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/aerodrop/aerodrop.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet">
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/aerodrop/aerodrop.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/ckeditor/ckeditor.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    @endpush

    @push('custom-scripts')
        <script>
            $(document).ready(function() {

                $(document).on('click', '.add-note', function() {
                    // Determine the index based on rows that already have a name attribute.
                    const index = $('#notes-container .note-row').length - 1;
                    const $row  = $(this).closest('.note-row');

                    // Mark inputs as required and set name attributes with the current index
                    $row.find('textarea')
                        .attr('name', 'notes[]')
                        .prop('required', true);

                    // Change button from add (plus) to remove (minus)
                    $(this)
                        .removeClass('btn-success add-note')
                        .addClass('btn-danger remove-note')
                        .html('<i data-feather="trash"></i>');

                    // Append a new blank row (without name attributes)
                    $('#notes-container').append(`
                        <div class="note-row d-flex align-items-center gap-4 mb-3">
                            <textarea class="form-control" rows="5" placeholder="Add a note"></textarea>
                            <button type="button" class="btn btn-sm btn-success add-note"><i data-feather="plus"></i></button>
                        </div>
                    `);
                });

                // Remove the row when the minus button is clicked
                $(document).on('click', '.remove-note', function() {
                    $(this).closest('.note-row').remove();
                });

                // AeroDrop initialization with upload state management.
                const aerodrop = new AeroDrop(document.querySelector('#aerodrop'), {
                    name: 'attachments',
                    uploadURL: '/upload',
                    enableCamera: true,
                    maxFiles: 10,
                    allowedFileTypes: ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
                    fileSizeRules: [
                        { types: ['image/jpeg', 'image/png', 'image/webp'], maxSize: 2 * 1024 * 1024, error: "Image file too big" },
                        { types: ['application/pdf'], maxSize: 10 * 1024 * 1024, error: "PDF file too big" }
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
            });

            // Re-render icons on DOM change
            new MutationObserver(() => feather.replace()).observe(document.getElementById('notes-container'), {
                childList: true
            });

            // Disable form untill all pending upload processed
            new MutationObserver(function(mutationsList, observer) {
                $('#submitBtn').prop('disabled', $('#aerodrop').attr('data-loading') === 'true')
            }).observe($('#aerodrop')[0], { attributes: true, attributeFilter: ['data-loading'] });

            // select 2
            $('#payment_methods').select2({
                placeholder: 'select payment method'
            })
        </script>
    @endpush
</x-lawyer.app>
