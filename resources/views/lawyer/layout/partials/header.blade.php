<nav class="navbar">
    <a href="#" class="sidebar-toggler">
        <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
        <div class="search-form position-relative">
            <div class="input-group">
                <div class="input-group-text">
                    <i data-feather="search"></i>
                </div>
                <input type="text" class="form-control" id="navbarSearch" placeholder="Search features...">
            </div>
            <div id="searchResults" class="position-absolute bg-white shadow-sm rounded w-100 d-none" style="top: 100%; left: 0; z-index: 1000; max-height: 300px; overflow-y: auto;"></div>
        </div>
        <ul class="navbar-nav">

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="notificationDropdown" role="button">
                    <i data-feather="bell"></i>
                    <div class="indicator d-none">
                        <div class="circle"></div>
                    </div>
                </a>
                <div class="dropdown-menu p-0" data-bs-popper="static">
                    {{-- <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                        <p>6 New Notifications</p>
                        <a href="javascript:;" class="text-muted">Clear all</a>
                    </div> --}}
                    <div class="p-1" id="notifications_container"
                        style="width: 300px; max-height: 500px; overflow:auto;">
                        {{-- Notifications will go here --}}
                        <div id="unreadMessages"></div>
                        <div id="readMessages"></div>
                    </div>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="wd-30 ht-30 rounded-circle" src="{{ Auth::user()->avatar }}" alt="profile">
                </a>
                <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                    <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                        <div class="mb-3">
                            <img class="wd-80 ht-80 rounded-circle" src="{{ Auth::user()->avatar }}" alt="">
                        </div>
                        <div class="text-center">
                            <p class="tx-16 fw-bolder">{{Auth::user()->name}}</p>
                            <p class="tx-12 text-muted">{{Auth::user()->email}}</p>
                        </div>
                    </div>
                    <ul class="list-unstyled p-1">
                        <li class="dropdown-item py-2">
                            <a href="{{ route('lawyer.profile') }}" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="javascript:;" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="edit"></i>
                                <span>Edit Profile</span>
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="javascript:;" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="repeat"></i>
                                <span>Switch User</span>
                            </a>
                        </li>
                        <li class="dropdown-item py-2">
                            <a href="{{ route('lawyer.signout') }}" class="text-body ms-0">
                                <i class="me-2 icon-md" data-feather="log-out"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>

@push('custom-scripts')
    <script>
        $(document).ready(function() {
            const searchInput   = $('#navbarSearch');
            const searchResults = $('#searchResults');
            
            // Define searchable items with their routes
            const searchableItems = [
                { text: 'Dashboard', keywords: ['home', 'main', 'dashboard'], url: '{{ route("lawyer.dashboard") }}' },
                { text: 'Client List', keywords: ['clients', 'view clients', 'all clients', 'client list', 'manage clients'], url: '{{ route("lawyer.client.index") }}' },
                { text: 'Add Client', keywords: ['new client', 'create client', 'add client'], url: '{{ route("lawyer.client.create") }}' },
                { text: 'Cases', keywords: ['cases', 'view cases', 'all cases', 'case list', 'manage cases'], url: '{{ route("lawyer.cases.index") }}' },
                { text: 'Add Case', keywords: ['new case', 'create case', 'add case'], url: '{{ route("lawyer.cases.create") }}' },
                { text: 'Appointments', keywords: ['appointments', 'view appointments', 'schedule'], url: '{{ route("lawyer.appointment.index") }}' },
                { text: 'Add Appointment', keywords: ['new appointment', 'create appointment', 'schedule appointment'], url: '{{ route("lawyer.appointment.create") }}' },
                { text: 'Profile', keywords: ['my profile', 'account', 'profile settings'], url: '{{ route("lawyer.profile") }}' },
            ];
            
            function showSearchResults() {
                const query = searchInput.val().toLowerCase().trim();
                searchResults.empty();
                
                if (query.length < 2) {
                    searchResults.addClass('d-none');
                    return;
                }
                
                const filteredItems = searchableItems.filter(item => {
                    return item.text.toLowerCase().includes(query) || 
                        item.keywords.some(keyword => keyword.includes(query));
                });
                
                if (filteredItems.length > 0) {
                    filteredItems.forEach(item => {
                        $('<div>')
                            .addClass('p-2 border-bottom search-item')
                            .text(item.text)
                            .css('cursor', 'pointer')
                            .on('click', function() {
                                // window.location.href = item.url;
                            })
                            .hover(
                                function() { $(this).addClass('bg-light'); },
                                function() { $(this).removeClass('bg-light'); }
                            )
                            .appendTo(searchResults);
                    });
                    searchResults.removeClass('d-none');
                } else {
                    $('<div>')
                        .addClass('p-2 text-muted')
                        .text('No results found')
                        .appendTo(searchResults);
                    searchResults.removeClass('d-none');
                }
            }
            
            searchInput.on('input', showSearchResults);
            
            // Show results when clicking on search input if there's text
            searchInput.on('click', function() {
                if ($(this).val().length >= 2) {
                    showSearchResults();
                }
            });
            
            // Close search results when clicking outside
            $(document).on('click', function(e) {                
                if (!searchInput.is(e.target) && !searchResults.is(e.target) && searchResults.has(e.target).length === 0) {
                    searchResults.addClass('d-none');
                }
            });
            
            // Handle keyboard navigation
            searchInput.on('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchResults.addClass('d-none');
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    searchResults.find('.search-item:first').focus();
                }
            });
        });
    </script>
@endpush