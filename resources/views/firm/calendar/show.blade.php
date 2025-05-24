<x-firm.app>
    <div>
        <a href="{{ route('firm.calendar.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title mb-4">Calendar event</h4>

            <!-- Client Info -->
            <div class="table-responsive mb-5">
                <table class="table table-hover" style="table-layout: fixed">
                    <tbody>
                        <tr>
                            <th scope="row">Description</th>
                            <td>{{ $calendarEvent->description }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Deadline</th>
                            <td>{{ $calendarEvent->deadline }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Created at</th>
                            <td>{{ $calendarEvent->created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex">
        <a href="{{ route('firm.calendar.edit', $calendarEvent->id) }}" class="btn btn-primary btn-icon-text me-3">
            <i data-feather="edit-2"></i> Edit Event
        </a>
        <form action="{{ route('firm.calendar.destroy', $calendarEvent->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this event?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-icon-text">
                <i data-feather="trash"></i> Delete Event
            </button>
        </form>
    </div>
</x-firm.app>
