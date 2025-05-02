<x-lawyer.app>

    <div>
        <a href="{{ route('lawyer.calendar.create') }}" class="btn btn-primary btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="plus"></i>
            Create event
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
                <table class="table" id="calendar-table">
                    <thead>
                        <tr>
                            <th>Event#</th>
                            <th>Description</th>
                            <th>Deadline</th>
                            <th>Created at</th>
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

            function deleteEvent(eventId, deleteUrl) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to delete this event?",
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
                                $('#calendar-table').DataTable().ajax.reload(null, false);
                                successMessage('Event deleted');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'An error occurred while deleting the record.', 'error');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            }


            $('#calendar-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lawyer.calendar.index') }}',
                columns: [{
                        data: 'id',
                    },
                    {
                        data: 'description',
                        orderable: false,
                    },
                    {
                        data: 'deadline',
                        orderable: false,
                    },
                    {
                        data: 'created_at',
                        orderable: false,
                    },

                    {
                        sortable: false,
                        data: (calendar) => {
                            const showUrl = `{{ route('lawyer.calendar.show', ':id') }}`.replace(':id', calendar
                                .id);
                            const editUrl = `{{ route('lawyer.calendar.edit', ':id') }}`.replace(':id', calendar
                                .id);
                            const deleteUrl = `{{ route('lawyer.calendar.destroy', ':id') }}`.replace(':id',
                                calendar.id);
                            return `
                                <div>
                                    <a href="${showUrl}" class="btn btn-inverse-secondary btn-icon"><i data-feather="eye"></i></a>
                                    <a href="${editUrl}" class="btn btn-inverse-success btn-icon"><i data-feather="edit"></i></a>
                                    <a class="btn btn-inverse-danger btn-icon" onclick="deleteEvent(${calendar.id}, '${deleteUrl}')"><i data-feather="trash"></i></a>
                                </div>
                            `;
                        },
                    },

                ],
                drawCallback: function(settings) {
                    feather.replace();
                }
            });
        </script>
    @endpush
</x-lawyer.app>
