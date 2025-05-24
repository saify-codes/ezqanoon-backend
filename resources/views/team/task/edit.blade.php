<x-team.app>
    <div>
        <a href="{{ route('team.task.index') }}" class="btn btn-dark btn-icon-text mb-3">
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
            <h4 class="card-title mb-4">Edit task</h4>

            <!-- The form -->
            <form action="{{ route('team.task.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Describe task</label>
                    <input class="form-control" type="text" id="name" name="name" placeholder="e.g. My task" value="{{old('name', $task->name)}}" disabled>
                </div>
                
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start date</label>
                    <input class="form-control" type="date" id="start_date" value="{{old('start_date', $task->start_date)}}" name="start_date" disabled>
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">Deadline</label>
                    <input class="form-control" type="date" id="end_date" value="{{old('end_date', $task->end_date)}}" name="end_date" disabled>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="PENDING"     {{ old('status', $task->status) === 'PENDING'      ? 'selected' : '' }}>PENDING</option>
                        <option value="IN PROGRESS" {{ old('status', $task->status) === 'IN PROGRESS'  ? 'selected' : '' }}>IN PROGRESS</option>
                        <option value="COMPLETED"   {{ old('status', $task->status) === 'COMPLETED'    ? 'selected' : '' }}>COMPLETED</option>
                    </select>
                </div>

                <!-- Submit button (with an ID for enabling/disabling) -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Change status
                </button>
            </form>
        </div>
    </div>
</x-team.app>
