<nav class="sidebar">
  <div class="sidebar-header">
    <a href="#" class="sidebar-brand">
      <img src="{{asset('/logo.png')}}" alt="logo"  style="height: 50px">
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
      <li class="nav-item {{ activeClass(['admin']) }}">
        <a href="{{ url('/admin') }}" class="nav-link">
          <i class="link-icon" data-feather="box"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>

      <li class="nav-item nav-category">Management</li>
      <li class="nav-item {{ activeClass(['admin/manage/firm*']) }}">
        <a href="{{ url('/admin/manage/firm') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Firms</span>
        </a>
      </li>

      <li class="nav-item {{ activeClass(['admin/manage/lawyer*']) }}">
        <a href="{{ url('/admin/manage/lawyer') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Lawyers</span>
        </a>
      </li>

      <li class="nav-item {{ activeClass(['admin/manage/users*']) }}">
        <a href="{{ url('/admin/manage/users') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
        <span class="link-title">Users</span>
        </a>
      </li>

      <li class="nav-item {{ activeClass(['admin/setting*']) }}">
        <a href="{{ url('/admin/manage/setting') }}" class="nav-link">
          <i class="link-icon" data-feather="settings"></i>
        <span class="link-title">Settings</span>
        </a>
      </li>

    </ul>
  </div>
</nav>