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
            <div id="searchResults" class="position-absolute bg-white rounded-3 w-100 d-none shadow" style="top: 100%; left: 0; z-index: 1000; max-height: 350px; overflow-y: auto; border: 1px solid rgba(0,0,0,.08);"></div>
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
                        {{-- <li class="dropdown-item py-2">
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
                        </li> --}}
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
            
            // Define searchable items with their routes and icons
            const searchableItems = [
                { text: 'Dashboard',        keywords: ['home', 'main', 'dashboard'], url: '{{ route("lawyer.dashboard") }}', icon: 'home' },
                { text: 'Clients',          keywords: ['clients', 'view clients', 'all clients', 'client list', 'manage clients'], url: '{{ route("lawyer.client.index") }}', icon: 'users' },
                { text: 'Add Client',       keywords: ['new client', 'create client', 'add client'], url: '{{ route("lawyer.client.create") }}', icon: 'user-plus' },
                { text: 'Cases',            keywords: ['cases', 'view cases', 'all cases', 'case list', 'manage cases'], url: '{{ route("lawyer.cases.index") }}', icon: 'briefcase' },
                { text: 'Add Case',         keywords: ['new case', 'create case', 'add case'], url: '{{ route("lawyer.cases.create") }}', icon: 'file-plus' },
                { text: 'Appointments',     keywords: ['appointments', 'view appointments', 'schedule'], url: '{{ route("lawyer.appointment.index") }}', icon: 'calendar' },
                { text: 'Add Appointment',  keywords: ['new appointment', 'create appointment', 'schedule appointment'], url: '{{ route("lawyer.appointment.create") }}', icon: 'calendar-plus' },
                { text: 'Profile',          keywords: ['my profile', 'account', 'profile settings'], url: '{{ route("lawyer.profile") }}', icon: 'user' },
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
                    // Add header
                    $('<div>')
                        .addClass('p-3 border-bottom search-header')
                        .html('<strong>Search Results</strong>')
                        .appendTo(searchResults);
                    
                    filteredItems.forEach(item => {
                        // Create result item with icon
                        const resultItem = $('<a>')
                            .attr('href', item.url)
                            .addClass('search-item d-flex align-items-center p-3 border-bottom text-decoration-none text-dark')
                            .css('transition', 'background-color 0.2s ease');
                            
                        // Add icon container
                        const iconContainer = $('<div>')
                            .addClass('me-3 d-flex align-items-center justify-content-center rounded-circle bg-light')
                            .css({
                                'width': '40px',
                                'height': '40px'
                            });
                            
                        // Create icon element
                        const icon = document.createElement('i');
                        $(icon).attr('data-feather', item.icon);
                        iconContainer.append(icon);
                        
                        // Add content
                        const content = $('<div>')
                            .addClass('d-flex flex-column')
                            
                        const title = $('<div>')
                            .addClass('fw-medium')
                            .text(item.text);
                            
                        const description = $('<div>')
                            .addClass('small text-muted')
                            .text(item.keywords[0]);
                            
                        content.append(title, description);
                        resultItem.append(iconContainer, content);
                        
                        // Add hover effect
                        resultItem.hover(
                            function() { $(this).addClass('bg-light'); },
                            function() { $(this).removeClass('bg-light'); }
                        );
                        
                        searchResults.append(resultItem);
                    });
                    
                    // Initialize feather icons for the new elements
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                    
                    searchResults.removeClass('d-none');
                } else {
                    $('<div>')
                        .addClass('p-4 text-center text-muted')
                        .html('<i data-feather="alert-circle" class="mb-2"></i><p class="mb-0">No results found</p>')
                        .appendTo(searchResults);
                        
                    // Initialize feather icons for the new elements
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                    
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