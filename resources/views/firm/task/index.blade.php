<x-firm.app>
    <div>
        <a href="{{ route('firm.task.create') }}" class="btn btn-primary btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="plus"></i>
            Create task
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
                <table class="table table-hover w-100" id="task-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>About</th>
                            <th>Start date</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Assign to</th>
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
                return badgeClass?`<span class="badge rounded-pill border border-${badgeClass} text-${badgeClass}">${value}</span>` : '';
            }
           
            /**
             * Function to handle the AJAX delete request.
             */
            function deleteTask(taskId, deleteUrl) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to delete this task?",
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
                                $('#task-table').DataTable().ajax.reload(null, false);
                                successMessage('Task deleted');
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
            $('#task-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('firm.task.index') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'start_date'
                    },
                    {
                        data: 'end_date'
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const statusClasses = {
                                'PENDING': 'primary',
                                'IN PROGRESS': 'warning',
                                'COMPLETED': 'success',
                            };
                            return getBadge(data, statusClasses);
                        }
                    },
                    {
                        data: 'member.name',
                        render: function(name) {
                            return name ?? 'N/A'
                        }
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        sortable: false,
                        data: function(task) {
                            // Build URLs
                            const showUrl   = `{{ route('firm.task.show', ':id') }}`.replace(':id', task.id);
                            const editUrl   = `{{ route('firm.task.edit', ':id') }}`.replace(':id', task.id);
                            const deleteUrl = `{{ route('firm.task.destroy', ':id') }}`.replace(':id', task.id);

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
                                            onclick="deleteTask(${task.id}, '${deleteUrl}')">
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
</x-firm.app>
