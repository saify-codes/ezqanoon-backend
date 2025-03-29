<x-lawyer.app>
    <div>
        <a href="{{route('lawyer.cases.create')}}" class="btn btn-primary btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="plus"></i>
            Create case
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
                <table class="table table-hover w-100" id="cases-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Case number</th>
                            <th>Case name</th>
                            <th>Case type</th>
                            <th>Urgency</th>
                            <th>Status</th>
                            <th>Payment</th>
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
    @endpush

    @push('plugin-styles')
        <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}">
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
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
            function deleteCase(caseId, deleteUrl) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to delete this case?",
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
                                $('#cases-table').DataTable().ajax.reload(null, false);
                                successMessage('Case deleted');
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
            $('#cases-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lawyer.cases.index') }}',
                columns: [
                    { data: 'id' },
                    { data: 'court_case_number' },
                    { data: 'name' },
                    { data: 'type' },
                    {
                        data: 'urgency',
                        render: function(data) {
                            const urgencyClasses = {
                                'HIGH': 'danger',
                                'MEDIUM': 'warning',
                                'CRITICAL': 'danger',
                            };
                            return getBadge(data, urgencyClasses);
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const statusClasses = {
                                'OPEN': 'success',
                                'IN PROGRESS': 'warning',
                                'CLOSED': 'danger',
                            };
                            return getBadge(data, statusClasses);
                        }
                    },
                    {
                        data: 'payment_status',
                        render: function(data) {
                            const paymentClasses = {
                                'PENDING': 'warning',
                                'PAID': 'success',
                                'OVERDUE': 'danger',
                            };
                            return getBadge(data, paymentClasses);
                        }
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        sortable: false,
                        data: function(row) {
                            // Build URLs
                            const showUrl   = `{{ route('lawyer.cases.show', ':id') }}`.replace(':id', row.id);
                            const editUrl   = `{{ route('lawyer.cases.edit', ':id') }}`.replace(':id', row.id);
                            const deleteUrl = `{{ route('lawyer.cases.destroy', ':id') }}`.replace(':id', row.id);

                            return `
                                <div>
                                    @if(Auth::user()->hasPermission('cases:view'))
                                        <a href="${showUrl}" class="btn btn-inverse-secondary btn-icon">
                                            <i data-feather="eye"></i>
                                        </a>
                                    @endif

                                    @if(Auth::user()->hasPermission('cases:edit'))
                                        <a href="${editUrl}" class="btn btn-inverse-success btn-icon">
                                            <i data-feather="edit-3"></i>
                                        </a>
                                    @endif

                                    @if(Auth::user()->hasPermission('cases:delete'))
                                        <button type="button" class="btn btn-inverse-danger btn-icon" onclick="deleteCase(${row.id}, '${deleteUrl}')">
                                            <i data-feather="trash"></i>
                                        </button>
                                    @endif
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
