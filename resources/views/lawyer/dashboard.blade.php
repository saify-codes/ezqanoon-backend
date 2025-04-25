<x-lawyer.app>

    {{-- Generic modal --}}
    <div class="modal fade" id="generic-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- spinner while loading --}}
                    <div class="d-flex justify-content-center py-5">
                        <div class="spinner-border" role="status"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="generic-save-btn" style="display: none;">Save
                        changes</button>
                </div>

            </div>
        </div>
    </div>



    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome {{ Auth::user()->name }}</h4>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title mb-0">Total Clients</h6>
                    <div class="card-body">
                        <h2>{{ $totalClients }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body bg-info text-white">
                    <h6 class="card-title mb-0">Total Appointments</h6>
                    <div class="card-body">
                        <h2>{{ $totalAppointments }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body bg-success text-white">
                    <h6 class="card-title mb-0">Todays Cases</h6>
                    <div class="card-body">
                        <h2>{{ $totalCasesToday }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body bg-danger text-white">
                    <h6 class="card-title mb-0">High Priority Cases</h6>
                    <div class="card-body">
                        <h2>{{ $totalHighPriorityCases }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body bg-warning text-white">
                    <h6 class="card-title mb-0">Decided Cases</h6>
                    <div class="card-body">
                        <h2>{{ $totalDecidedCases }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0">Inbox</h6>
                    </div>
                    <div class="d-flex flex-column" id="inbox">
                        <div id="messages">
                            <div class="d-flex justify-content-center">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

    </div>


    @push('style')
        <style>
            #inbox {
                max-height: 500px;
                overflow: auto;
                scrollbar-width: thin;
                /* For Firefox */
                scrollbar-color: transparent transparent;
                /* For Firefox */
                transition: scrollbar-color 0.3s ease, opacity 0.3s ease;
            }

            #inbox:hover {
                scrollbar-color: rgba(0, 0, 0, 0.5) rgba(0, 0, 0, 0);
                /* Show scrollbar on hover */
            }

            #inbox::-webkit-scrollbar {
                width: 8px;
                /* Width of the scrollbar */
            }

            #inbox::-webkit-scrollbar-thumb {
                background-color: transparent;
                /* Hide scrollbar thumb initially */
                border-radius: 10px;
                transition: background-color 0.3s ease;
            }

            #inbox:hover::-webkit-scrollbar-thumb {
                background-color: rgba(0, 0, 0, 0.5);
                /* Show scrollbar thumb on hover */
            }

            #inbox .unread {
                /* background: var(--bs-light); */
            }
        </style>
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/fullcalendar/index.global.min.js') }}"></script>
        <script src="{{ asset('assets/js/lawyer-inbox.js') }}"></script>
    @endpush

    @push('custom-scripts')
        <script>
            class HearingModal {
                constructor(node, caseId, hearingId, description, date) {
                    this.onDateChange = null;
                    this.modalBody = node;
                    this.caseId = caseId;
                    this.hearingId = hearingId;
                    this.description = description
                    this.date = date
                    this.url = `{{ route('lawyer.cases.hearing', ['caseId' => ':caseId', 'hearingId' => ':hearingId']) }}`
                        .replace(':caseId', caseId)
                        .replace(':hearingId', hearingId);
                    this.init();
                }


                init() {
                    this.render()
                    this.bindEvents()
                }
                bindEvents() {
                    $(this.modalBody).find('form').on('submit', (event) => {
                        const formData = {
                            caseId: this.caseId,
                            hearingId: this.hearingId,
                            date: $(this.modalBody).find('input[type="date"]').val()
                        };
                        event.preventDefault();
                        this.changeDate(formData)
                    });
                }
                render() {
                    $(this.modalBody).html(`
                        <form>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" rows="3" disabled>${this.description}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Date</label>
                                        <input type="date" class="form-control" value="${this.date}" required/>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary w-100">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    `)
                }
                changeDate(formData) {

                    $.ajax({
                        url: this.url,
                        method: 'PATCH',
                        data: formData,
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        beforeSend: () => {
                            $(this.modalBody).find('button[type="submit"]')
                                .html('<span class="spinner-border spinner-border-sm" role="status"></span>')
                                .prop('disabled', true);
                        },
                        complete: () =>{
                            $(this.modalBody).find('button[type="submit"]')
                                .html('Save')
                                .prop('disabled', false);

                        },
                        success: (response) => {
                            if (typeof this.onDateChange === 'function') {
                                this.onDateChange(response)
                            }
                            successMessage('Date changed')
                        },
                        error: (xhr, status, error) => {
                            alert('something went wrong!')
                        }
                    });
                }

            }

            $(function() {

                // Cache jQuery selectors
                const $modal = $('#generic-modal');
                const $title = $modal.find('.modal-title');
                const $body = $modal.find('.modal-body');
                const $saveBtn = $('#generic-save-btn');
                const bsModal = new bootstrap.Modal($modal[0]);


                // 2) Initialize FullCalendar
                const calendar = new FullCalendar.Calendar($('#calendar')[0], {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'tidatetle',
                        right: 'dayGridMonth'
                    },
                    events: "{{ route('lawyer.calendar.events') }}",
                    eventClick: function(info) {
                        openModal(info.event);
                    }
                });
                calendar.render();

                // 3) The generic openModal
                function openModal(event) {

                    const type = event.extendedProps.type
                    bsModal.show();

                    switch (type) {
                        case 'HEARING':
                            const {
                                caseId, hearingId, description, date
                            } = event.extendedProps
                            new HearingModal($body[0], caseId, hearingId, description, date)
                                .onDateChange = () =>{
                                    console.log('changed');
                                    calendar.refetchEvents();
                                }   
                            break;

                        default:
                            break;
                    }

                }


            });
        </script>
    @endpush

</x-lawyer.app>
