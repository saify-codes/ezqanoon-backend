<x-team.app>
    <div>
        <a href="{{ route('team.appointment.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h6>Appointment Details</h6>
        </div>
        <div class="card-body">

            <!-- Client Info -->
            <div class="table-responsive mb-5">
                <table class="table table-hover" style="table-layout: fixed">
                    <tbody>
                        <tr>
                            <th scope="row">Name</th>
                            <td>{{ $appointment->user->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>{{ $appointment->user->email }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Phone</th>
                            <td>{{ $appointment->user->phone }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Country</th>
                            <td>{{ $appointment->country }}</td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>

            <!-- Details -->
            <div class="mb-3">
                <h6 class="mb-3">Client details</h6>
                <div class="border rounded p-3">
                    {{ $appointment->details }}
                </div>
            </div>

            <!-- Summary -->
            <div class="mb-3">
                <h6 class="mb-3">Summary</h6>
                <div class="border rounded p-3">
                    {!! $appointment->summary ?? 'N\A' !!}
                </div>
            </div>

            <!-- Client Attachments -->
            @if ($appointment->attachments && count($appointment->attachments))
                <div class="mb-3">
                    <h6 class="mb-3">Client Attachments</h6>
                    <div class="appointment-attachments">
                        @foreach ($appointment->attachments as $attachment)
                            @php
                                $fileUrl  = $attachment->file;
                                $mimeType = $attachment->mime_type;
                                $isImage  = strpos($mimeType, 'image') === 0;
                            @endphp

                            <div class="attachment" id="attachment-{{ $attachment->id }}">
                                <a  href="{{ $fileUrl }}" 
                                    class="{{ $isImage ? 'glightbox' : '' }}"
                                    data-gallery="appointment-attachments" data-title="{{ $attachment->original_name }}"
                                    target="_blank">
                                    <img src="{{ $isImage ? $fileUrl : asset('assets/images/icons/file.png') }}" alt="{{ $attachment->original_name }}" class="rounded">
                                </a>
                                <!-- Delete Icon Button using AJAX -->
                                {{-- <button type="button" class="delete-attachment-button"
                                    data-url="{{ route('team.client.attachments.destroy', [$appointment->id, $attachment->id]) }}">
                                    <i data-feather="trash" style="width: 15px"></i>
                                </button> --}}
                                <p class="text-sm text-muted text-center mt-1">{{ $attachment->original_name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            
            <!-- Summary Attachments -->
            @if ($appointment->summaryAttachments && count($appointment->summaryAttachments))
                <div class="mb-3">
                    <h6 class="mb-3">Summary Attachments</h6>
                    <div class="appointment-attachments">
                        @foreach ($appointment->summaryAttachments as $attachment)

                            @php
                                $fileUrl  = $attachment->file;
                                $mimeType = $attachment->mime_type;
                                $isImage  = strpos($mimeType, 'image') === 0;
                            @endphp

                            <div class="attachment" id="attachment-{{ $attachment->id }}">
                                <a  href="{{ $fileUrl }}" 
                                    class="{{ $isImage ? 'glightbox' : '' }}"
                                    data-gallery="appointment-attachments" data-title="{{ $attachment->original_name }}"
                                    target="_blank">
                                    <img src="{{ $isImage ? $fileUrl : asset('assets/images/icons/file.png') }}" alt="{{ $attachment->original_name }}" class="rounded">
                                </a>
                                <!-- Delete Icon Button using AJAX -->
                                {{-- <button type="button" class="delete-attachment-button"
                                    data-url="{{ route('team.client.attachments.destroy', [$appointment->id, $attachment->id]) }}">
                                    <i data-feather="trash" style="width: 15px"></i>
                                </button> --}}
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
        <a href="{{ route('team.appointment.edit', $appointment->id) }}" class="btn btn-primary btn-icon-text me-3">
            <i data-feather="edit-2"></i> Edit appointment
        </a>
        {{-- <form action="{{ route('team.appointment.destroy', $appointment->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this appointment?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-icon-text">
                <i data-feather="trash"></i> Delete appointment
            </button>
        </form> --}}
    </div>

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/glightbox/glightbox.min.css') }}" rel="stylesheet" />
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/glightbox/glightbox.min.js') }}"></script>
    @endpush

    @push('style')
        <style>
            .appointment-attachments {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                place-content: center;
                gap: 1rem;
            }

            .appointment-attachments .attachment {
                position: relative;
            }

            .appointment-attachments .attachment img {
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
                                    successMessage('AppointmentAttachment deleted successfully.');
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
        <script>
            const lightbox = GLightbox({
                selector: '.glightbox',
                fitImagesInViewport: true,
            });
        </script>
    @endpush

</x-team.app>
