<x-team.app>
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

            // Initialize DataTable
            $('#task-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('team.task.index') }}',
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
                        data: 'created_at'
                    },
                    {
                        sortable: false,
                        data: function(task) {
                            // Build URLs
                            const showUrl = `{{ route('team.task.show', ':id') }}`.replace(':id', task.id);
                            const editUrl = `{{ route('team.task.edit', ':id') }}`.replace(':id', task.id);

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
</x-team.app>
