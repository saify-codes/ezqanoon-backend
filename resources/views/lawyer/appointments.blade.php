<x-lawyer.app>
    <!-- Appointment Details Modal -->
    <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalLabel">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label>Details</label>
                            <textarea id="appointmentDetailsText" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="mb-3" id="attachmentSection">
                            <label>Attachments</label>
                            <ul id="appointmentAttachmentsList" class="list-group">
                                <!-- Attachments will be populated here -->
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="appointments-table">
                    <thead>
                        <tr>
                            <th>Appointment#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Country</th>
                            <th>Date</th>
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
            $('#appointments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('lawyer.appointment.index') }}',
                columns: [
                    {
                        data: 'id',
                    },
                    {
                        data: 'user.name',
                        orderable:false,
                    },
                    {
                        data: 'user.email',
                        orderable:false,
                    },
                    {
                        data: 'user.phone',
                        orderable:false,
                    },
                    {
                        data: 'country',
                        orderable:false,
                    },
                    {
                        data: 'created_at',
                    },
                    {
                        sortable: false,
                        data: (data) => {
                            return `
                                <div>
                                    <button class="btn btn-inverse-secondary btn-icon" onclick='viewCase(${JSON.stringify(data)})'><i data-feather="eye"></i></button>
                                    <button class="btn btn-inverse-success btn-icon" onclick='viewCase(${data})'><i data-feather="edit"></i></button>
                                </div>
                            `;
                        },
                    },

                ],
                drawCallback: function(settings) {
                    feather.replace();
                }
            });

            function viewCase(data) {
                var modalInstance = new bootstrap.Modal($('#appointmentModal')[0]);

                // Set the details text in the textarea
                $('#appointmentModal #appointmentDetailsText').val(data.details);

                const $attachmentsList = $('#appointmentAttachmentsList');
                $attachmentsList.empty(); // Clear any existing attachments

                if (data.attachments && data.attachments.length > 0) {
                    $('#attachmentSection').show();
                    $.each(data.attachments, function(index, {original_name, file_path}) {
                        $attachmentsList.append(`<li class="list-group-item"> <a href="${file_path}" target="_blank">${original_name}</a></li>`);
                    });
                } else {
                    $('#attachmentSection').hide();
                }

                modalInstance.show();
            }
        </script>
    @endpush
</x-lawyer.app>
