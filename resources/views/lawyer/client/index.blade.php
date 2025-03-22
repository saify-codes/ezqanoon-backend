<x-lawyer.app>
    <div>
        <a href="{{route('lawyer.client.create')}}" class="btn btn-primary btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="plus"></i>
            Create client
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            @session('success')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Hurray!</strong> {{session('success')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
            @endsession

            <div class="table-responsive">
                <table class="table table-hover" id="client-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Dob</th>
                            <th>Type</th>
                            <th>Date created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}">
        <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @endpush

    @push('custom-scripts')
        <script>
            /**
             * Helper function to create a Bootstrap badge snippet.
             */
            function getBadge(value, classMapping) {
                const badgeClass = classMapping[value];
                return badgeClass?`<span class="badge rounded-pill border border-${badgeClass} text-${badgeClass}">${value}</span>` : '';
            }

            /**
             * Function to handle the AJAX delete request.
             */
            function deleteClient(clientId, deleteUrl) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to delete this client?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--bs-primary)',
                    cancelButtonColor: 'var(--bs-danger)',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                // Reload DataTable on success
                                $('#client-table').DataTable().ajax.reload(null, false);
                                successMessage('Client deleted');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'An error occurred while deleting the record.', 'error');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            }

            // Initialize DataTable
            $('#client-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lawyer.client.index') }}',
                columns: [
                    { data: 'id' },
                    { data: 'first_name' },
                    { data: 'last_name' },
                    { data: 'gender' },
                    { data: 'phone' },
                    { data: 'email' },
                    { data: 'dob' },
                    {
                        data: 'type',
                        render: function(data) {
                            const urgencyClasses = {
                                'REGULAR': 'secondary',
                                'VIP': 'primary',
                            };
                            return getBadge(data, urgencyClasses);
                        }
                    },
                    { data: 'created_at' },
                    {
                        sortable: false,
                        data: function(row) {
                            // Build URLs
                            const showUrl   = `{{ route('lawyer.client.show', ':id') }}`.replace(':id', row.id);
                            const editUrl   = `{{ route('lawyer.client.edit', ':id') }}`.replace(':id', row.id);
                            const deleteUrl = `{{ route('lawyer.client.destroy', ':id') }}`.replace(':id', row.id);

                            return `
                                <div>
                                    <!-- View button -->
                                    <a href="${showUrl}" class="btn btn-inverse-secondary btn-icon">
                                        <i data-feather="eye"></i>
                                    </a>

                                    <!-- Edit button -->
                                    <a href="${editUrl}" class="btn btn-inverse-success btn-icon">
                                        <i data-feather="edit-3"></i>
                                    </a>

                                    <!-- AJAX Delete button -->
                                    <button type="button"
                                            class="btn btn-inverse-danger btn-icon"
                                            onclick="deleteClient(${row.id}, '${deleteUrl}')">
                                        <i data-feather="trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    },
                ],
                drawCallback: function(settings) {
                    feather.replace();
                }
            });
        </script>
    @endpush
</x-lawyer.app>
