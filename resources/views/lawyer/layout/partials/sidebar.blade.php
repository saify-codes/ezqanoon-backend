<nav class="sidebar">
  <div class="sidebar-header">
    <a href="#" class="sidebar-brand">
      Logo
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

      <li class="nav-item nav-category">Client & Case Management</li>
      <li class="nav-item {{ activeClass(['manage/client*']) }}">
        <a href="{{ url('/manage/client') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Client management</span>
        </a>
      </li>
      <li class="nav-item {{ activeClass(['manage/cases*']) }}">
        <a href="{{ url('/manage/cases') }}" class="nav-link">
          <i class="link-icon" data-feather="book-open"></i>
          <span class="link-title">Case management</span>
        </a>
      </li>

      <li class="nav-item nav-category">Task & Schedule</li>
      <li class="nav-item {{ activeClass(['manage/appointments']) }}">
        <a href="{{ url('/manage/appointments') }}" class="nav-link">
          <i class="link-icon" data-feather="clipboard"></i>
          <span class="link-title">Appointments</span>
        </a>
      </li>
      <li class="nav-item {{ activeClass(['manage/task']) }}">
        <a href="{{ url('/manage/task') }}" class="nav-link">
          <i class="link-icon" data-feather="check-square"></i>
          <span class="link-title">Task management</span>
        </a>
      </li>
      <li class="nav-item {{ activeClass(['calendar']) }}">
        <a href="{{ url('/calendar') }}" class="nav-link">
          <i class="link-icon" data-feather="calendar"></i>
          <span class="link-title">Calendar & scheduling</span>
        </a>
      </li>

      <li class="nav-item nav-category">Finance</li>
      <li class="nav-item {{ activeClass(['billing']) }}">
        <a href="{{ url('/manage/client') }}" class="nav-link">
          <i class="link-icon" data-feather="dollar-sign"></i>
          <span class="link-title">Billing & invoicing</span>
        </a>
      </li>

      <li class="nav-item nav-category">Reports & Analytics</li>
      <li class="nav-item {{ activeClass(['report']) }}">
        <a href="{{ url('/manage/client') }}" class="nav-link">
          <i class="link-icon" data-feather="trending-up"></i>
          <span class="link-title">Report</span>
        </a>
      </li>
    </ul>
  </div>
</nav>