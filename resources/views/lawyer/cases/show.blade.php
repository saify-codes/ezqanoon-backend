<x-lawyer.app>
    <div>
        <a href="{{ route('lawyer.cases.index') }}" class="btn btn-dark btn-icon-text mb-3">
            <i class="btn-icon-prepend" data-feather="list"></i>
            List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">Case Details</h4>
        </div>
        <div class="card-body">
            <!-- Basic Information -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Case Name:</strong>
                    <p>{{ $case->name }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Case Type:</strong>
                    <p>{{ $case->type }}</p>
                </div>
            </div>

            <!-- Urgency & Court Information -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Urgency:</strong>
                    <p>{{ $case->urgency }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Court Name:</strong>
                    <p>{{ $case->court_name }}</p>
                </div>
            </div>

            <!-- Court Case Number & Judge -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Court Case Number:</strong>
                    <p>{{ $case->court_case_number }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Judge Name:</strong>
                    <p>{{ $case->judge_name ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Under Acts & Under Sections -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Under Acts:</strong>
                    <p>{{ $case->under_acts ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Under Sections:</strong>
                    <p>{{ $case->under_sections ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- FIR & Police Station Details -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>FIR Number:</strong>
                    <p>{{ $case->fir_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>FIR Year:</strong>
                    <p>{{ $case->fir_year ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Police Station:</strong>
                    <p>{{ $case->police_station ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Party Details -->
            <div class="mb-3">
                <strong>Your Party Details:</strong>
                <div class="border rounded p-3">
                    {!! nl2br(e($case->your_party_details)) !!}
                </div>
            </div>
            <div class="mb-3">
                <strong>Opposite Party Details:</strong>
                <div class="border rounded p-3">
                    {!! $case->opposite_party_details !!}
                </div>
            </div>
            <div class="mb-3">
                <strong>Opposite Party Advocate Details:</strong>
                <div class="border rounded p-3">
                    {!! $case->opposite_party_advocate_details !!}
                </div>
            </div>

            <!-- Case Information -->
            <div class="mb-3">
                <strong>Case Information:</strong>
                <div class="border rounded p-3">
                    {!! nl2br(e($case->case_information)) !!}
                </div>
            </div>

            <!-- Deadlines & Payment Status -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Deadlines:</strong>
                    <p>
                        {{ $case->deadlines ? \Carbon\Carbon::parse($case->deadlines)->format('F d, Y') : 'N/A' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <strong>Payment Status:</strong>
                    <p>{{ $case->payment_status }}</p>
                </div>
            </div>

            <!-- Attachments -->
            @if($case->attachments && count($case->attachments))
                <div class="mb-3">
                    <strong>Attachments:</strong>
                    <ul class="list-unstyled">
                        @foreach($case->attachments as $attachment)
                            <li>
                                <a href="{{ asset('storage/' . $attachment) }}" target="_blank">
                                    <i data-feather="file"></i> {{ basename($attachment) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex">
        <a href="{{ route('lawyer.cases.edit', $case->id) }}" class="btn btn-primary me-2">
            <i data-feather="edit"></i> Edit Case
        </a>
        <form action="{{ route('lawyer.cases.destroy', $case->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this case?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i data-feather="trash-2"></i> Delete Case
            </button>
        </form>
    </div>
</x-lawyer.app>
