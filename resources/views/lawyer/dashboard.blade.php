<x-lawyer.app>

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome {{ Auth::user()->name }}</h4>
        </div>
    </div>

    <div class="row">

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
                        <h2>{{ $totalClients }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-lawyer.app>
