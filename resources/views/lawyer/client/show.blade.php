<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.client.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h6>Client Details</h6>
        </div>
        <div class="card-body">

            <!-- Client Info -->
            <div class="table-responsive mb-5">
                <table class="table table-hover" style="table-layout: fixed">
                    <tbody>
                        <tr>
                            <th scope="row">Full Name</th>
                            <td>{{ $client->first_name }} {{ $client->last_name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Gender</th>
                            <td>{{ $client->gender }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Client Type</th>
                            <td>{{ $client->type }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Date of Birth</th>
                            <td>{{ $client->dob ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Address</th>
                            <td>{{ $client->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Phone</th>
                            <td>{{ $client->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>{{ $client->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Company Name</th>
                            <td>{{ $client->company_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Company Website</th>
                            <td>{{ $client->company_website ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Communication Method</th>
                            <td>{{ $client->communication_method ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Contact Time</th>
                            <td>{{ $client->contact_time ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Language</th>
                            <td>{{ $client->language ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Billing Address</th>
                            <td>{{ $client->billing_address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Payment Methods</th>
                            <td>
                                @if($client->payment_methods)
                                    {{ implode(', ', $client->payment_methods) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">TIN</th>
                            <td>{{ $client->tin ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Notes -->
            @if ($client->notes)
                <div class="mb-3">
                    <h6 class="mb-3">Notes:</h6>
                    <div class="border rounded p-3">
                        @if(is_array($client->notes))
                            <ul class="mb-0">
                                @foreach($client->notes as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        @else
                            {!! $client->notes !!}
                        @endif
                    </div>
                </div>
            @endif

            <!-- Attachments -->
            @if ($client->attachments && count($client->attachments))
                <div class="mb-3">
                    <h6 class="mb-3">Attachments</h6>
                    <div class="client-attachments">
                        @foreach ($client->attachments as $attachment)
                            @php
                                $fileUrl = asset("/storage/clients/$client->id/$attachment->file");
                                $mimeType = $attachment->mime_type;
                                $isImage = strpos($mimeType, 'image') === 0;
                            @endphp

                            <div class="attachment" id="attachment-{{ $attachment->id }}">
                                <a href="{{ $fileUrl }}" class="{{ $isImage ? 'glightbox' : '' }}"
                                    data-gallery="client-attachments" data-title="{{ $attachment->original_name }}"
                                    target="_blank">
                                    <img src="{{ $isImage ? $fileUrl : asset('assets/images/icons/file.png') }}"
                                        alt="{{ $attachment->original_name }}" class="rounded">
                                </a>
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
        <a href="{{ route('lawyer.client.edit', $client->id) }}" class="btn btn-primary btn-icon-text me-3">
            <i data-feather="edit-2"></i> Edit Client
        </a>
        <form action="{{ route('lawyer.client.destroy', $client->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this client?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-icon-text">
                <i data-feather="trash"></i> Delete Client
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
            .client-attachments {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                place-content: center;
                gap: 1rem;
            }

            .client-attachments .attachment {
                position: relative;
            }

            .client-attachments .attachment img {
                background: var(--bs-light);
                padding: 1rem;
                width: 100%;
                height: 150px;
                object-fit: contain;
                object-position: center;
            }
        </style>
    @endpush

    @push('custom-scripts')
        <script>
            const lightbox = GLightbox({
                selector: '.glightbox',
                fitImagesInViewport: true,
            });
        </script>
    @endpush

</x-lawyer.app>
