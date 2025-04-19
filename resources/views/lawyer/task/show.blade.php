<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.task.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h6>Task Details</h6>
        </div>
        <div class="card-body">

            <!-- Client Info -->
            <div class="table-responsive mb-5">
                <table class="table table-hover" style="table-layout: fixed">
                    <tbody>
                        <tr>
                            <th scope="row">Name</th>
                            <td>{{ $task->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Start date</th>
                            <td>{{ $task->start_date }}</td>
                        </tr>
                        <tr>
                            <th scope="row">End date</th>
                            <td>{{ $task->end_date }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Status</th>
                            <td>{{ $task->status}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Assign to</th>
                            <td>{{ $task->member->name ?? 'N\A' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Date created</th>
                            <td>{{ $task->created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex">
        <a href="{{ route('lawyer.task.edit', $task->id) }}" class="btn btn-primary btn-icon-text me-3">
            <i data-feather="edit-2"></i> Edit Task
        </a>
        <form action="{{ route('lawyer.task.destroy', $task->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this task?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-icon-text">
                <i data-feather="trash"></i> Delete Task
            </button>
        </form>
    </div>
   
</x-lawyer.app>
