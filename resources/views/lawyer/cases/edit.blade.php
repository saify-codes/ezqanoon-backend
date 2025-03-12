<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.cases.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Edit Case</h4>

            <!-- The form -->
            <form action="{{ route('lawyer.cases.update', $case->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') <!-- Required for a PUT request -->

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

                <!-- Case Name & Type -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Case Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $case->name) }}" placeholder="Enter case name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Case Type</label>
                        <input type="text" class="form-control" id="type" name="type"
                            value="{{ old('type', $case->type) }}" placeholder="e.g. Criminal, Civil" required>
                    </div>
                </div>

                <!-- Urgency & Court Name -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="urgency" class="form-label">Urgency</label>
                        <select class="form-select" id="urgency" name="urgency">
                            <option value="">-- Select Urgency --</option>
                            <option value="HIGH" {{ old('urgency', $case->urgency) === 'HIGH' ? 'selected' : '' }}>HIGH</option>
                            <option value="MEDIUM" {{ old('urgency', $case->urgency) === 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                            <option value="CRITICAL" {{ old('urgency', $case->urgency) === 'CRITICAL' ? 'selected' : '' }}>CRITICAL</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="court_name" class="form-label">Court Name</label>
                        <input type="text" class="form-control" id="court_name" name="court_name"
                            value="{{ old('court_name', $case->court_name) }}" placeholder="e.g. Supreme Court, District Court" required>
                    </div>
                </div>

                <!-- Court Case Number & Judge Name -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="court_case_number" class="form-label">Court Case Number</label>
                        <input type="text" class="form-control" id="court_case_number" name="court_case_number"
                            value="{{ old('court_case_number', $case->court_case_number) }}" placeholder="Enter court case number" required>
                    </div>
                    <div class="col-md-6">
                        <label for="judge_name" class="form-label">Judge Name (optional)</label>
                        <input type="text" class="form-control" id="judge_name" name="judge_name"
                            value="{{ old('judge_name', $case->judge_name) }}" placeholder="Enter judge name">
                    </div>
                </div>

                <!-- Under Acts & Under Sections -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="under_acts" class="form-label">Under Acts (optional)</label>
                        <input type="text" class="form-control" id="under_acts" name="under_acts"
                            value="{{ old('under_acts', $case->under_acts) }}" placeholder="e.g. CPC, IPC">
                    </div>
                    <div class="col-md-6">
                        <label for="under_sections" class="form-label">Under Sections (optional)</label>
                        <input type="text" class="form-control" id="under_sections" name="under_sections"
                            value="{{ old('under_sections', $case->under_sections) }}" placeholder="e.g. Section 302">
                    </div>
                </div>

                <!-- FIR Number, FIR Year, Police Station -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fir_number" class="form-label">FIR Number (optional)</label>
                        <input type="text" class="form-control" id="fir_number" name="fir_number"
                            value="{{ old('fir_number', $case->fir_number) }}" placeholder="Enter FIR number">
                    </div>
                    <div class="col-md-4">
                        <label for="fir_year" class="form-label">FIR Year (optional)</label>
                        <input type="text" class="form-control" id="fir_year" name="fir_year"
                            value="{{ old('fir_year', $case->fir_year) }}" placeholder="e.g. 2023">
                    </div>
                    <div class="col-md-4">
                        <label for="police_station" class="form-label">Police Station (optional)</label>
                        <input type="text" class="form-control" id="police_station" name="police_station"
                            value="{{ old('police_station', $case->police_station) }}" placeholder="Enter the police station">
                    </div>
                </div>

                <!-- Your Party Details (using CKEditor) -->
                <div class="mb-3">
                    <label for="your_party_details_editor" class="form-label">Your Party Details (optional)</label>
                    <textarea class="form-control" id="your_party_details_editor" name="your_party_details" rows="3"
                        placeholder="Enter your client's details">{{ old('your_party_details', $case->your_party_details) }}</textarea>
                </div>

                <!-- Opposite Party Details (using CKEditor) -->
                <div class="mb-3">
                    <label for="opposite_party_details_editor" class="form-label">Opposite Party Details (optional)</label>
                    <textarea class="form-control" id="opposite_party_details_editor" name="opposite_party_details" rows="3"
                        placeholder="Enter opposite party details">{{ old('opposite_party_details', $case->opposite_party_details) }}</textarea>
                </div>

                <!-- Opposite Party Advocate Details (using CKEditor) -->
                <div class="mb-3">
                    <label for="opposite_party_advocate_details_editor" class="form-label">
                        Opposite Party Advocate Details (optional)
                    </label>
                    <textarea class="form-control" id="opposite_party_advocate_details_editor" name="opposite_party_advocate_details" rows="3"
                        placeholder="Enter opposite party advocate details">{{ old('opposite_party_advocate_details', $case->opposite_party_advocate_details) }}</textarea>
                </div>

                <!-- Case Information -->
                <div class="mb-3">
                    <label for="case_information" class="form-label">Case Information (optional)</label>
                    <textarea class="form-control" id="case_information" name="case_information" rows="3"
                        placeholder="Describe the case in detail">{{ old('case_information', $case->case_information) }}</textarea>
                </div>

                <!-- Deadlines & Payment Status -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="deadlines" class="form-label">Deadlines (optional)</label>
                        <input type="date" class="form-control" id="deadlines" name="deadlines"
                            value="{{ old('deadlines', $case->deadlines) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Case Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="OPEN" {{ old('status', $case->status) === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                            <option value="IN PROGRESS" {{ old('status', $case->status) === 'IN PROGRESS' ? 'selected' : '' }}>IN PROGRESS</option>
                            <option value="CLOSED" {{ old('status', $case->status) === 'CLOSED' ? 'selected' : '' }}>CLOSED</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="PENDING" {{ old('payment_status', $case->payment_status) === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                            <option value="PAID" {{ old('payment_status', $case->payment_status) === 'PAID' ? 'selected' : '' }}>PAID</option>
                            <option value="OVERDUE" {{ old('payment_status', $case->payment_status) === 'OVERDUE' ? 'selected' : '' }}>OVERDUE</option>
                        </select>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="mb-3">
                    <label for="attachments" class="form-label">Add New Attachments (optional)</label>
                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                    <small class="text-muted d-block mt-1">
                        - Maximum <strong>10 files</strong> allowed.<br>
                        - Allowed file types: <strong>PNG, JPG, WEBP, PDF, DOC, DOCX</strong>.<br>
                        - <strong>Images</strong> must not exceed <strong>2MB</strong> each.<br>
                        - <strong>Documents/PDFs</strong> must not exceed <strong>10MB</strong> each.<br>
                        <em>Existing files will remain stored unless you remove them elsewhere.</em>
                    </small>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary">
                    Update Case
                </button>
            </form>
        </div>
    </div>

    @push('plugin-scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/35.3.0/classic/ckeditor.js"></script>
    @endpush

    @push('custom-scripts')
        <script>
            $(document).ready(function() {
                // Validate file attachments
                $('#attachments').on('change', function(e) {
                    const files = e.target.files;
                    if (files.length > 10) {
                        alert('You can only upload a maximum of 10 files.');
                        $(this).val('');
                        return;
                    }
                    const allowedImages = ['image/png', 'image/jpeg', 'image/webp'];
                    const allowedDocs = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ];
                    for (const file of files) {
                        if (allowedImages.includes(file.type)) {
                            if (file.size > 2 * 1024 * 1024) {
                                alert(`Image "${file.name}" exceeds 2 MB limit.`);
                                $(this).val('');
                                return;
                            }
                        } else if (allowedDocs.includes(file.type)) {
                            if (file.size > 10 * 1024 * 1024) {
                                alert(`File "${file.name}" exceeds 10 MB limit for PDFs/docs.`);
                                $(this).val('');
                                return;
                            }
                        } else {
                            alert(`File "${file.name}" is not an allowed format.`);
                            $(this).val('');
                            return;
                        }
                    }
                });

                // Common configuration for allowed toolbar tools
                const editorConfig = {
                    toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'link']
                };

                // Initialize CKEditor for Your Party Details
                ClassicEditor
                    .create(document.querySelector('#your_party_details_editor'), editorConfig)
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '300px';
                    })
                    .catch(error => {
                        console.error(error);
                    });

                // Initialize CKEditor for Opposite Party Details
                ClassicEditor
                    .create(document.querySelector('#opposite_party_details_editor'), editorConfig)
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '300px';
                    })
                    .catch(error => {
                        console.error(error);
                    });

                // Initialize CKEditor for Opposite Party Advocate Details
                ClassicEditor
                    .create(document.querySelector('#opposite_party_advocate_details_editor'), editorConfig)
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '300px';
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
        </script>
    @endpush
</x-lawyer.app>
