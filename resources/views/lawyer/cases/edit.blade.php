<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.cases.index') }}" class="btn btn-dark btn-icon-text mb-3">
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
            <h4 class="card-title mb-4">Edit Case</h4>

            <!-- The form -->
            <form action="{{ route('lawyer.cases.update', $case->id) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Case Name & Type -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Case Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $case->name) }}" placeholder="Enter case name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Case Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">-- Select Case type --</option>
                            <option value="CRIMINAL" {{ old('type', $case->type) === 'CRIMINAL' ? 'selected' : '' }}>CRIMINAL</option>
                            <option value="CIVIL"    {{ old('type', $case->type) === 'CIVIL'    ? 'selected' : '' }}>CIVIL</option>
                            <option value="OTHERs"   {{ old('type', $case->type) === 'OTHERS'   ? 'selected' : '' }}>OTHERS</option>
                        </select>
                    </div>
                </div>

                <!-- Urgency & Court Name -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="urgency" class="form-label">Urgency</label>
                        <select class="form-select" id="urgency" name="urgency">
                            <option value="">-- Select Urgency --</option>
                            <option value="HIGH"        {{ old('urgency', $case->urgency) === 'HIGH' ? 'selected' : '' }}>HIGH</option>
                            <option value="MEDIUM"      {{ old('urgency', $case->urgency) === 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                            <option value="LOW"         {{ old('urgency', $case->urgency) === 'LOW' ? 'selected' : '' }}>LOW</option>
                            <option value="URGENT"      {{ old('urgency', $case->urgency) === 'URGENT' ? 'selected' : '' }}>URGENT</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="court_name" class="form-label">Court Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="court_name" name="court_name"
                            value="{{ old('court_name', $case->court_name) }}"
                            placeholder="e.g. Supreme Court, District Court" required>
                    </div>
                </div>

                <!-- Court Case Number & Judge Name -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="court_case_number" class="form-label">Court Case Number <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="court_case_number" name="court_case_number"
                            value="{{ old('court_case_number', $case->court_case_number) }}"
                            placeholder="Enter court case number" required>
                    </div>
                    <div class="col-md-6">
                        <label for="judge_name" class="form-label">Judge Name</label>
                        <input type="text" class="form-control" id="judge_name" name="judge_name"
                            value="{{ old('judge_name', $case->judge_name) }}" placeholder="Enter judge name">
                    </div>
                </div>

                <!-- Under Acts & Under Sections -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="under_acts" class="form-label">Under Acts</label>
                        <input type="text" class="form-control" id="under_acts" name="under_acts"
                            value="{{ old('under_acts', $case->under_acts) }}" placeholder="e.g. PPC, CRPC">
                    </div>
                    <div class="col-md-6">
                        <label for="under_sections" class="form-label">Under Sections</label>
                        <input type="text" class="form-control" id="under_sections" name="under_sections"
                            value="{{ old('under_sections', $case->under_sections) }}" placeholder="e.g. Section 302">
                    </div>
                </div>

                <!-- FIR Number, FIR Year, Police Station -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fir_number" class="form-label">FIR Number</label>
                        <input type="text" class="form-control" id="fir_number" name="fir_number"
                            value="{{ old('fir_number', $case->fir_number) }}" placeholder="Enter FIR number">
                    </div>
                    <div class="col-md-4">
                        <label for="fir_year" class="form-label">FIR Year</label>
                        <input type="number" class="form-control" id="fir_year" name="fir_year"
                            value="{{ old('fir_year', $case->fir_year) }}" placeholder="e.g. 2025">
                    </div>
                    <div class="col-md-4">
                        <label for="police_station" class="form-label">Police Station</label>
                        <input type="text" class="form-control" id="police_station" name="police_station"
                            value="{{ old('police_station', $case->police_station) }}"
                            placeholder="Enter the police station">
                    </div>
                </div>

                <!-- Your Party Details -->
                <div class="mb-3">
                    <label for="your-party-details-editor" class="form-label">Your Party Details</label>
                    <textarea class="form-control" id="your-party-details-editor" name="your_party_details" rows="3"
                        placeholder="Enter your client's details">{{ old('your_party_details', $case->your_party_details) }}</textarea>
                </div>

                <!-- Opposite Party Details -->
                <div class="mb-3">
                    <label for="opposite-party-details-editor" class="form-label">Opposite Party Details</label>
                    <textarea class="form-control" id="opposite-party-details-editor" name="opposite_party_details" rows="3"
                        placeholder="Enter opposite party details">{{ old('opposite_party_details', $case->opposite_party_details) }}</textarea>
                </div>

                <!-- Opposite Party Advocate Details -->
                <div class="mb-3">
                    <label for="opposite-party-advocate-details-editor" class="form-label">Opposite Party Advocate
                        Details</label>
                    <textarea class="form-control" id="opposite-party-advocate-details-editor" name="opposite_party_advocate_details"
                        rows="3" placeholder="Enter opposite party advocate details">{{ old('opposite_party_advocate_details', $case->opposite_party_advocate_details) }}</textarea>
                </div>

                <!-- Case Information -->
                <div class="mb-3">
                    <label for="case-information-editor" class="form-label">Case Information</label>
                    <textarea class="form-control" id="case-information-editor" name="case_information" rows="3"
                        placeholder="Describe the case in detail">{{ old('case_information', $case->case_information) }}</textarea>
                </div>

                <!-- Filing -->
                <div class="mb-3">
                    <label for="filing" class="form-label">Filing date</label>
                    <div id="filing-container">
                        @php
                            $fillings = old('filling', $case->fillings) ?? [];
                        @endphp

                        @foreach ($fillings as $index => $filling)
                            <div class="filling-row d-flex gap-4 mb-3">
                                <input type="text" name="fillings[{{ $index }}][description]" class="form-control filling-description" maxlength="255" placeholder="Filling description" value="{{ old("fillings.$index.description", $filling['description'] ?? '') }}" required>
                                <input type="date" name="fillings[{{$index}}][date]" class="form-control filling-date" value="{{ old("fillings.$index.date", $filling['date'] ?? '') }}" required>
                                <button type="button" class="btn btn-sm btn-danger remove-filling"><i data-feather="minus"></i></button>
                            </div>
                        @endforeach
                        <div class="filling-row d-flex gap-4 mb-3">
                            <input type="text" class="form-control filling-description" maxlength="255" placeholder="Filling description">
                            <input type="date" class="form-control filling-date">
                            <button type="button" class="btn btn-sm btn-success add-filling"><i data-feather="plus"></i></button>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Hearings -->
                <div class="mb-3">
                    <label for="hearing" class="form-label">Hearing date</label>
                    <div id="hearing-container">
                        @php
                            $hearings = old('filling', $case->hearings) ?? [];
                        @endphp

                        @foreach ($hearings as $index => $hearing)
                            <div class="filling-row d-flex gap-4 mb-3">
                                <input type="text" name="hearings[{{ $index }}][description]" class="form-control filling-description" maxlength="255" placeholder="Filling description" value="{{ old("hearings.$index.description", $hearing['description'] ?? '') }}" required>
                                <input type="date" name="hearings[{{$index}}][date]" class="form-control filling-date" value="{{ old("hearings.$index.date", $hearing['date'] ?? '') }}" required>
                                <button type="button" class="btn btn-sm btn-danger remove-hearing"><i data-feather="minus"></i></button>
                            </div>
                        @endforeach
                        <div class="hearing-row d-flex gap-4 mb-3">
                            <input type="text" class="form-control hearing-description" maxlength="255" placeholder="Hearing description">
                            <input type="date" class="form-control hearing-date">
                            <button type="button" class="btn btn-sm btn-success add-hearing"><i data-feather="plus"></i></button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Payment Status -->
                    <div class="col-md-6">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="PENDING"
                                {{ old('payment_status', $case->payment_status) === 'PENDING' ? 'selected' : '' }}>PENDING
                            </option>
                            <option value="PAID"
                                {{ old('payment_status', $case->payment_status) === 'PAID' ? 'selected' : '' }}>PAID
                            </option>
                            <option value="OVERDUE"
                                {{ old('payment_status', $case->payment_status) === 'OVERDUE' ? 'selected' : '' }}>OVERDUE
                            </option>
                        </select>
                    </div>
                   
                    <!-- Case Status -->
                    <div class="col-md-6">
                        <label for="status" class="form-label">Case Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="OPEN"
                                {{ old('status', $case->status) === 'OPEN' ? 'selected' : '' }}>OPEN
                            </option>
                            <option value="IN PROGRESS"
                                {{ old('status', $case->status) === 'IN PROGRESS' ? 'selected' : '' }}>IN PROGRESS
                            </option>
                            <option value="CLOSED"
                                {{ old('status', $case->status) === 'CLOSED' ? 'selected' : '' }}>CLOSED
                            </option>
                        </select>
                    </div>

                </div>
                

                <!-- Attachments -->
                <div class="mb-3">
                    <label for="attachments" class="form-label">Attachments (optional)</label>
                    {{-- <input type="file" class="form-control" id="attachments" name="attachments[]" multiple> --}}
                    <div id="aerodrop" class="aerodrop mb-3"></div>
                    <!-- Note for users -->
                    <small class="text-muted d-block mt-1">
                        - Maximum <strong>20 files</strong> allowed.<br>
                        - Allowed file types: <strong>PNG, JPG, WEBP, PDF, DOC, DOCX</strong>.<br>
                        - <strong>Images</strong> must not exceed <strong>2MB</strong> each.<br>
                        - <strong>Documents/PDFs</strong> must not exceed <strong>10MB</strong> each.
                    </small>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Update Case
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

                // Dynamic Filing rows
                $(document).on('click', '.add-filling', function() {
                    // compute the index for the new “filled” row
                    const index = $('#filing-container .filling-date').length - 1;
                    const $row = $(this).closest('.filling-row');

                    // mark current inputs required and give them names
                    $row.find('.filling-description, .filling-date')
                        .prop('required', true);
                    $row.find('.filling-description')
                        .attr('name', `fillings[${index}][description]`);
                    $row.find('.filling-date')
                        .attr('name', `fillings[${index}][date]`);

                    // switch the “add” button into a “remove” button
                    $(this)
                    .removeClass('btn-success add-filling')
                    .addClass('btn-danger remove-filling')
                    .html('<i data-feather="trash"></i>');

                    // append a fresh blank row
                    $('#filing-container').append(`
                    <div class="filling-row d-flex gap-4 mb-3">
                        <input type="text" class="form-control filling-description" placeholder="Filing description">
                        <input type="date" class="form-control filling-date">
                        <button type="button" class="btn btn-sm btn-success add-filling">
                        <i data-feather="plus"></i>
                        </button>
                    </div>
                    `);

                    // re-render Feather icons
                    feather.replace();
                });

                $(document).on('click', '.remove-filling', function() {
                    $(this).closest('.filling-row').remove();
                });


                // Dynamic Hearing rows
                $(document).on('click', '.add-hearing', function() {
                    const index = $('#hearing-container .hearing-date').length - 1;
                    const $row = $(this).closest('.hearing-row');

                    $row.find('.hearing-description, .hearing-date')
                        .prop('required', true);
                    $row.find('.hearing-description')
                        .attr('name', `hearings[${index}][description]`);
                    $row.find('.hearing-date')
                        .attr('name', `hearings[${index}][date]`);

                    $(this)
                    .removeClass('btn-success add-hearing')
                    .addClass('btn-danger remove-hearing')
                    .html('<i data-feather="trash"></i>');

                    $('#hearing-container').append(`
                    <div class="hearing-row d-flex gap-4 mb-3">
                        <input type="text" class="form-control hearing-description" placeholder="Hearing description">
                        <input type="date" class="form-control hearing-date">
                        <button type="button" class="btn btn-sm btn-success add-hearing">
                        <i data-feather="plus"></i>
                        </button>
                    </div>
                    `);

                    feather.replace();
                });

                $(document).on('click', '.remove-hearing', function() {
                    $(this).closest('.hearing-row').remove();
                });

                const editorConfig = {
                    toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'link']
                };

                // Initialize CKEditor for Your Party Details
                ClassicEditor
                    .create(document.querySelector('#your-party-details-editor'), editorConfig)
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '100px';
                    })
                    .catch(error => {
                        console.error(error);
                    });

                // Initialize CKEditor for Opposite Party Details
                ClassicEditor
                    .create(document.querySelector('#opposite-party-details-editor'), editorConfig)
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '100px';
                    })
                    .catch(error => {
                        console.error(error);
                    });

                // Initialize CKEditor for Opposite Party Advocate Details
                ClassicEditor
                    .create(document.querySelector('#opposite-party-advocate-details-editor'), editorConfig)
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '100px';
                    })
                    .catch(error => {
                        console.error(error);
                    });

                // Initialize CKEditor for Case Information
                ClassicEditor
                    .create(document.querySelector('#case-information-editor'), editorConfig)
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '100px';
                    })
                    .catch(error => {
                        console.error(error);
                    });

                // AeroDrop initialization with upload state management.
                const aerodrop = new AeroDrop(document.querySelector('#aerodrop'), {
                    name: 'attachments',
                    uploadURL: '/upload',
                    enableCamera: true,
                    maxFiles: 20,
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

            });

            // Disable form untill all pending upload processed
            new MutationObserver(function(mutationsList, observer) {
                $('#submitBtn').prop('disabled', $('#aerodrop').attr('data-loading') === 'true')
            }).observe($('#aerodrop')[0], { attributes: true, attributeFilter: ['data-loading'] });
 
        </script>
    @endpush
</x-lawyer.app>
