<x-admin.app>

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome {{ Auth::guard('admin')->user()->name }}</h4>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title mb-0">Total Firms</h6>
                    <div class="card-body">
                        <h2>{{ $totalFirms }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title mb-0">Total Lawyers</h6>
                    <div class="card-body">
                        <h2>{{ $totalLawyers }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body bg-info text-white">
                    <h6 class="card-title mb-0">Total Users</h6>
                    <div class="card-body">
                        <h2>{{ $totalUsers }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
