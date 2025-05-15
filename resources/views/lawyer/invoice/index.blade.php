<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.invoice.create') }}" class="btn btn-primary btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="plus"></i>
            Create invoice
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            @session('success')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Hurray!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
            @endsession

            <div class="table-responsive">
                <table class="table table-hover w-100" id="team-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Invoice type</th>
                            <th>Status</th>
                            <th>Case type</th>
                            <th>Total</th>
                            <th>Balance</th>
                            <th>Due date</th>
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
                return badgeClass ?
                    `<span class="badge rounded-pill border border-${badgeClass} text-${badgeClass}">${value}</span>` : '';
            }

            /**
             * Function to handle the AJAX delete request.
             */
            function deleteInvoice(invoiceId, deleteUrl) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to delete this Invoice?",
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
                                $('#team-table').DataTable().ajax.reload(null, false);
                                successMessage('Invoice deleted');
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
            $('#team-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lawyer.invoice.index') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'phone',
                        render: function(phone){
                            return phone ?? 'N/A'
                        }
                    },
                    {
                        data: 'type',
                        render: function(invoiceType) {
                            const typeClasses = {
                                'ONE TIME': 'primary',
                                'MILESTONE': 'info',
                            };
                            return getBadge(invoiceType, typeClasses);
                        }
                    },
                    {
                        data: null,
                        render: function(invoice) {

                            if (invoice.type === 'MILESTONE') {
                                const showUrl = `{{ route('lawyer.invoice.show', ':id') }}`.replace(':id', invoice.id);
                                return `<a href="${showUrl}" class="btn btn-inverse-secondary btn-icon"><i data-feather="eye"></i></a>`
                            }

                            const statusClasses = {
                                'PENDING': 'warning',
                                'PAID': 'success',
                                'OVERDUE': 'danger',
                            };
                            return getBadge(invoice.status, statusClasses);
                        }
                    },
                    {
                        data: 'case_type'
                    },
                    {
                        data: 'total'
                    },
                    {
                        data: 'balance'
                    },
                    {
                        data: null,
                        render: function(invoice){
                            if (invoice.type === 'MILESTONE') {
                                const showUrl = `{{ route('lawyer.invoice.show', ':id') }}`.replace(':id', invoice.id);
                                return `<a href="${showUrl}" class="btn btn-inverse-secondary btn-icon"><i data-feather="eye"></i></a>`
                            }
    
                            return invoice.due_date
                        }
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        sortable: false,
                        data: function(invoice) {
                            // Build URLs
                            const showUrl   = `{{ route('lawyer.invoice.show', ':id') }}`.replace(':id', invoice.id);
                            const editUrl   = `{{ route('lawyer.invoice.edit', ':id') }}`.replace(':id', invoice.id);
                            const deleteUrl = `{{ route('lawyer.invoice.destroy', ':id') }}`.replace(':id',invoice.id);

                            return `
                            <div>
                                    <!-- Show button -->
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
                                            onclick="deleteInvoice(${invoice.id}, '${deleteUrl}')">
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
