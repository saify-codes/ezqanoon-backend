<nav class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            <img src="{{ asset('/logo.png') }}" alt="logo" style="height: 50px">
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">Dashboard</li>
            <li class="nav-item {{ activeClass(['/']) }}">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item nav-category">Management</li>

            <li class="nav-item {{ activeClass(['team/manage/task*']) }}">
                <a href="{{ url('/team/manage/task') }}" class="nav-link">
                    <i class="link-icon" data-feather="clipboard"></i>
                    <span class="link-title">My task</span>
                </a>
            </li>

            @if (Auth::guard('team')->user()->hasPermission('manage:client'))
                <li class="nav-item {{ activeClass(['team/manage/client*']) }}">
                    <a href="{{ url('/team/manage/client') }}" class="nav-link">
                        <i class="link-icon" data-feather="users"></i>
                        <span class="link-title">Client management</span>
                    </a>
                </li>
            @endif
            @if (Auth::guard('team')->user()->hasPermission('manage:case'))
                <li class="nav-item {{ activeClass(['team/manage/cases*']) }}">
                    <a href="{{ url('/team/manage/cases') }}" class="nav-link">
                        <i class="link-icon" data-feather="book-open"></i>
                        <span class="link-title">Case management</span>
                    </a>
                </li>
            @endif
            @if (Auth::guard('team')->user()->hasPermission('manage:appointment'))
                <li class="nav-item {{ activeClass(['team/manage/appointments*']) }}">
                    <a href="{{ url('/team/manage/appointments') }}" class="nav-link">
                        <i class="link-icon" data-feather="calendar"></i>
                        <span class="link-title">Appointments</span>
                    </a>
                </li>
            @endif

            {{-- <li class="nav-item nav-category">Task & Schedule</li>

            <li class="nav-item {{ activeClass(['team/calendar*']) }}">
                <a href="{{ url('/team/calendar') }}" class="nav-link">
                    <i class="link-icon" data-feather="calendar"></i>
                    <span class="link-title">Calendar & scheduling</span>
                </a>
            </li> --}}


            <li class="nav-item nav-category">Finance</li>

            @if (Auth::guard('team')->user()->hasPermission('manage:billing'))
                <li class="nav-item {{ activeClass(['team/invoice*']) }}">
                    <a href="{{ url('/team/invoice') }}" class="nav-link">
                        <i class="link-icon" data-feather="dollar-sign"></i>
                        <span class="link-title">Billing & invoicing</span>
                    </a>
                </li>
            @endif

            <li class="nav-item nav-category">Reports & Analytics</li>

            @if (Auth::guard('team')->user()->hasPermission('manage:report'))
                <li class="nav-item {{ activeClass(['team/report*']) }}">
                    <a href="{{ url('/team/manage/client') }}" class="nav-link">
                        <i class="link-icon" data-feather="trending-up"></i>
                        <span class="link-title">Report</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
