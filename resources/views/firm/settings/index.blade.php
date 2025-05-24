<x-firm.app>

    <div class="card">
        <div class="card-body">

            @session('success')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Hurray!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
            @endsession

            <form action="{{ route('firm.settings.store') }}" method="POST">

                @csrf
                <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="calendar-tab" data-bs-toggle="tab" href="#calendar">Calendar</a>
                    </li>
                    {{-- Add more here --}}
                </ul>
                <div class="tab-content mt-3" id="lineTabContent">
                    <div class="tab-pane fade show active" id="calendar" role="tabpanel">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Custom event color</label>
                            <div class="col-sm-9">
                                <div class="color-picker" id="custom-event-color"></div>
                                <input type="hidden" name="calendar[custom_event_color]">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Appointment event color</label>
                            <div class="col-sm-9">
                                <div class="color-picker" id="appointment-event-color"></div>
                                <input type="hidden" name="calendar[appointment_event_color]">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Hearing event color</label>
                            <div class="col-sm-9">
                                <div class="color-picker" id="hearing-event-color"></div>
                                <input type="hidden" name="calendar[hearing_event_color]">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add more here --}}


                <button type="submit" class="btn btn-primary me-2">Save</button>
            </form>
        </div>
    </div>

    @push('style')
        <link rel="stylesheet" href="{{ asset('assets/plugins/color-picker/color-picker.min.css') }}">
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/color-picker/color-picker.min.js') }}"></script>
    @endpush

    @push('style')
        <style>
            .color-picker {
                width: 25px;
                height: 25px;
                border-radius: 5px;
            }
        </style>
    @endpush

    @push('custom-scripts')
        <script>
            $("#custom-event-color").colorPick({
                'initialColor': '{{$settings?->calendar->custom_event_color ?? "#95a5a6"}}',
                'allowRecent': true,
                'recentMax': 5,
                'allowCustomColor': true,
                'palette': ["#1abc9c", "#16a085", "#2ecc71", "#27ae60", "#3498db", "#2980b9", "#9b59b6", "#8e44ad",
                    "#34495e", "#2c3e50", "#f1c40f", "#f39c12", "#e67e22", "#d35400", "#e74c3c", "#c0392b",
                    "#ecf0f1", "#bdc3c7", "#95a5a6", "#7f8c8d"
                ],
                onColorSelected: function() {
                    this.element.css({'backgroundColor':this.color});
                    $("[name='calendar[custom_event_color]']").val(this.color);
                },
            });
            $("#appointment-event-color").colorPick({
                'initialColor': '{{$settings?->calendar->appointment_event_color ?? "#95a5a6"}}',
                'allowRecent': true,
                'recentMax': 5,
                'allowCustomColor': true,
                'palette': ["#1abc9c", "#16a085", "#2ecc71", "#27ae60", "#3498db", "#2980b9", "#9b59b6", "#8e44ad",
                    "#34495e", "#2c3e50", "#f1c40f", "#f39c12", "#e67e22", "#d35400", "#e74c3c", "#c0392b",
                    "#ecf0f1", "#bdc3c7", "#95a5a6", "#7f8c8d"
                ],
                onColorSelected: function() {
                    this.element.css({'backgroundColor':this.color});
                    $("[name='calendar[appointment_event_color]']").val(this.color);
                },
            });
            $("#hearing-event-color").colorPick({
                'initialColor': '{{$settings?->calendar->hearing_event_color ?? "#95a5a6"}}',
                'allowRecent': true,
                'recentMax': 5,
                'allowCustomColor': true,
                'palette': ["#1abc9c", "#16a085", "#2ecc71", "#27ae60", "#3498db", "#2980b9", "#9b59b6", "#8e44ad",
                    "#34495e", "#2c3e50", "#f1c40f", "#f39c12", "#e67e22", "#d35400", "#e74c3c", "#c0392b",
                    "#ecf0f1", "#bdc3c7", "#95a5a6", "#7f8c8d"
                ],
                onColorSelected: function() {
                    this.element.css({'backgroundColor':this.color});
                    $("[name='calendar[hearing_event_color]']").val(this.color);
                },
            });
        </script>
    @endpush
</x-firm.app>
