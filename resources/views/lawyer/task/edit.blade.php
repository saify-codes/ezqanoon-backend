<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.task.index') }}" class="btn btn-dark btn-icon-text mb-3">
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
            <form action="{{ route('lawyer.task.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Describe task <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="name" name="name" placeholder="e.g. My task" value="{{old('name', $task->name)}}" required>
                </div>
                
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start date <span class="text-danger">*</span></label>
                    <input class="form-control" type="date" id="start_date" value="{{old('start_date', $task->start_date)}}" name="start_date" required>
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">Deadline <span class="text-danger">*</span></label>
                    <input class="form-control" type="date" id="end_date" value="{{old('end_date', $task->end_date)}}" name="end_date" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="PENDING"     {{ old('status', $task->status) === 'PENDING'      ? 'selected' : '' }}>PENDING</option>
                        <option value="IN PROGRESS" {{ old('status', $task->status) === 'IN PROGRESS'  ? 'selected' : '' }}>IN PROGRESS</option>
                        <option value="COMPLETED"   {{ old('status', $task->status) === 'COMPLETED'    ? 'selected' : '' }}>COMPLETED</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="assign_to" class="form-label">Assing to</label>
                    <select class="form-select" id="assign_to" name="assign_to">
                        <option value="">select member</option>
                        @foreach ($team as $member)
                            <option value="{{$member->id}}" {{$member->id === $task->assign_to ? 'selected' : ''}}>{{$member->name}} ({{$member->email}})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit button (with an ID for enabling/disabling) -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Edit task
                </button>
            </form>
        </div>
    </div>

    @push('plugin-styles')
        <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet">
    @endpush

    @push('plugin-scripts')
        <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    @endpush

    @push('custom-scripts')
        <script>
            $(document).ready(function() {
                $('#assign_to').select2()
            });
        </script>
    @endpush
</x-lawyer.app>
