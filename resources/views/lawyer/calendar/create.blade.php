<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.calendar.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <!-- Display validation errors (if any) -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Create calendar event</h4>

            <!-- The form -->
            <form action="{{ route('lawyer.calendar.store') }}" method="POST">
                @csrf

                <!-- description -->
                <div class="mb-3">
                    <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="deadline" name="deadline" value="{{ old('deadline') }}" required/>
                </div>

                <!-- description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter opposite party details" required>{{ old('details') }}</textarea>
                </div>
               

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Create event
                </button>
            </form>
        </div>
    </div>
</x-lawyer.app>
