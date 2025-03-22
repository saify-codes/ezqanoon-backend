<x-lawyer.app>
    
    <div class="card">
        <div class="card-body">

            @session('success')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Hurray!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
            @endsession

            <div id="calendar"></div>
        </div>
    </div>

    @push('style')
        <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/fullcalendar/index.global.min.js') }}"></script>
    @endpush

    @push('custom-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: "{{ route('lawyer.calendar.events') }}",
                    eventClick: function(info) {
                        Swal.fire({
                            title: info.event.title,
                            text: `Case: ${info.event.extendedProps.caseName}`,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'View Case',
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = `{{ route('lawyer.cases.show', '') }}/${info.event.extendedProps.caseId}`;
                            }
                        });
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
</x-lawyer.app>
