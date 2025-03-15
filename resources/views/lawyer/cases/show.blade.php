<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.cases.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6>Case Details</h6>
            <x-lawyer.payment-badge :status="$case->payment_status" style="font-size: 1rem" />
        </div>
        <div class="card-body">

            <!-- Case info -->
            <div class="table-responsive mb-5">
                <table class="table table-hover" style="table-layout: fixed">
                    <tbody>
                        <tr>
                            <th scope="row">Case name</th>
                            <td>{{ $case->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Case type</th>
                            <td>{{ $case->type }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Case status</th>
                            <td><x-lawyer.status-badge :status="$case->status" /></td>
                        </tr>
                        <tr>
                            <th scope="row">Urgency</th>
                            <td><x-lawyer.urgency-badge :status="$case->urgency" /></td>
                        </tr>
                        <tr>
                            <th scope="row">Court name</th>
                            <td>{{ $case->court_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Court case#</th>
                            <td>{{ $case->court_case_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Judge name</th>
                            <td>{{ $case->judge_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Under acts</th>
                            <td>{{ $case->under_acts ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Under sections</th>
                            <td>{{ $case->under_sections ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">FIR#</th>
                            <td>{{ $case->fir_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">FIR years</th>
                            <td>{{ $case->fir_year ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Police station</th>
                            <td>{{ $case->police_station ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Deadlines -->
            @if ($case->deadlines)
                <div class="mb-3">
                    <h6 class="mb-3">Deadlines:</h6>
                    <div class="table-responsive mb-5">
                        <table class="table table-hover" style="table-layout: fixed">
                            <tbody>
                                @foreach ($case->deadlines as $deadline)
                                    <tr>
                                        <td scope="row">{{ $deadline['description'] }}</td>
                                        <td>{{ $deadline['date'] }} <x-lawyer.deadline-badge
                                                deadline="{{ $deadline['date'] }}" class="ms-2" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Party Details -->
            <div class="mb-3">
                <h6 class="mb-3">Your Party Details:</h6>
                <div class="border rounded p-3">
                    {!! $case->your_party_details !!}
                </div>
            </div>

            <!-- Opposite Party Details -->
            <div class="mb-3">
                <h6 class="mb-3">Opposite Party Details:</h6>
                <div class="border rounded p-3">
                    {!! $case->opposite_party_details !!}
                </div>
            </div>

            <!-- Opposite Party Advocate Details -->
            <div class="mb-3">
                <h6 class="mb-3">Opposite Party Advocate Details:</h6>
                <div class="border rounded p-3">
                    {!! $case->opposite_party_advocate_details !!}
                </div>
            </div>

            <!-- Case Information -->
            <div class="mb-3">
                <h6 class="mb-3">Case Information:</h6>
                <div class="border rounded p-3">
                    {!! $case->case_information !!}
                </div>
            </div>

            <!-- Attachments -->
            @if ($case->attachments && count($case->attachments))
                <div class="mb-3">
                    <h6 class="mb-3">Attachments</h6>
                    <div class="case-attachments">
                        @foreach ($case->attachments as $attachment)
                            @php
                                $fileUrl = asset("/storage/cases/$case->id/$attachment->file");
                                $mimeType = $attachment->mime_type;
                                $isImage = strpos($mimeType, 'image') === 0;
                            @endphp

                            <div class="attachment" id="attachment-{{ $attachment->id }}">
                                <a href="{{ $fileUrl }}" class="{{ $isImage ? 'glightbox' : '' }}"
                                    data-gallery="case-attachments" data-title="{{ $attachment->original_name }}"
                                    target="_blank">
                                    <img src="{{ $isImage ? $fileUrl : asset('assets/images/icons/file.png') }}"
                                        alt="{{ $attachment->original_name }}" class="rounded">
                                </a>
                                <!-- Delete Icon Button using AJAX -->
                                <button type="button" class="delete-attachment-button"
                                    data-url="{{ route('lawyer.cases.attachments.destroy', [$case->id, $attachment->id]) }}">
                                    <i data-feather="trash" style="width: 15px"></i>
                                </button>
                                <p class="text-sm text-muted text-center mt-1">{{ $attachment->original_name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex">
        <a href="{{ route('lawyer.cases.edit', $case->id) }}" class="btn btn-primary btn-icon-text me-3">
            <i data-feather="edit-2"></i> Edit Case
        </a>
        <form action="{{ route('lawyer.cases.destroy', $case->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this case?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-icon-text">
                <i data-feather="trash"></i> Delete Case
            </button>
        </form>
    </div>

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/glightbox/glightbox.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/glightbox/glightbox.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @endpush

    @push('style')
        <style>
            .case-attachments {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                place-content: center;
                gap: 1rem;
            }

            .case-attachments .attachment {
                position: relative;
            }

            .case-attachments .attachment img {
                background: var(--bs-light);
                padding: 1rem;
                width: 100%;
                height: 150px;
                object-fit: contain;
                object-position: center;
            }

            .delete-attachment-button {
                position: absolute;
                top: 5px;
                right: 5px;
                width: 25px;
                height: 25px;
                background: transparent;
                border: none;
                border-radius: 50%;
                padding: 0;
                cursor: pointer;
                color: #FFF;
                background: var(--bs-danger)
            }
        </style>
    @endpush


    @push('custom-scripts')
        <script>
            $(document).ready(function() {

                $('.delete-attachment-button').on('click', function() {
                    const button = $(this);
                    const deleteUrl = button.data('url');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Are you sure you want to delete this attachment?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--bs-primary)',
                        cancelButtonColor: 'var(--bs-danger)',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: deleteUrl,
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    button.closest('.attachment').remove();
                                    successMessage('Attachment deleted successfully.');
                                },
                                error: function(xhr) {
                                    Swal.fire('Error',
                                        'An error occurred while deleting the attachment.',
                                        'error');
                                }
                            });
                        }
                    });
                });
            });
        </script>
        <!-- GLightbox JS from CDN -->
        <script>
            const lightbox = GLightbox({
                selector: '.glightbox',
                fitImagesInViewport: true,
            });
        </script>
    @endpush



</x-lawyer.app>
