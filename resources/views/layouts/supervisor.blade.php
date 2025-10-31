<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Rahbar Paneli')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8f9fa;
        }
        /* Navbar */
        .navbar {
            height: 64px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }
        .navbar-brand { font-weight: 700; font-size: 1.25rem; letter-spacing: -.02em; }
        .navbar .btn {
            border-radius: .5rem;
            font-weight: 500;
            transition: all .2s ease;
            border: 1px solid rgba(255,255,255,.2);
        }
        .navbar .btn:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,.15);
            border-color: rgba(255,255,255,.3);
        }
        /* Sidebar */
        .sidebar {
            width: 80px;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            transition: all .35s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 4px 0 12px rgba(0,0,0,0.03);
            position: fixed;
            top: 64px;
            bottom: 0;
            left: 0;
            z-index: 1020;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem 0.5rem;
        }
        .sidebar .nav-section {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #9ca3af;
            padding: 1rem 0 .5rem 0;
            margin-top: .75rem;
            width: 100%;
            text-align: center;
            display: none;
            position: relative;
        }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .875rem;
            padding: .625rem;
            border-radius: 12px;
            color: #6b7280;
            font-weight: 500;
            font-size: .9rem;
            transition: all .25s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin: .25rem 0;
            text-decoration: none;
        }
        .sidebar .nav-link .link-text {
            display: none;
            opacity: 0;
            white-space: nowrap;
            transition: opacity .2s ease;
        }
        .sidebar .nav-text {
            display: none;
            opacity: 0;
            white-space: nowrap;
            transition: opacity .2s ease;
        }
        .sidebar .nav-sublist {
            width: 100%;
            list-style: none;
            padding-left: 0;
            margin: 0.25rem 0 0 0;
        }
        .sidebar .nav-link-sub {
            display: flex;
            align-items: center;
            gap: .875rem;
            padding: .5rem;
            border-radius: 10px;
            color: #6b7280;
            font-weight: 500;
            font-size: .8rem;
            transition: all .25s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin: .15rem 0 .15rem .5rem;
            text-decoration: none;
        }
        .sidebar .nav-link-sub:hover {
            background: rgba(5, 150, 105, 0.06);
            color: #059669;
        }
        .sidebar .nav-link-sub.active {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(4, 120, 87, 0.1) 100%);
            color: #059669;
        }
        .sidebar .nav-sublist .nav-sublist {
            margin: .15rem 0 .15rem 2.25rem;
            padding-left: 0;
        }
        .sidebar .nav-link-tertiary {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .45rem;
            border-radius: 8px;
            color: #6b7280;
            font-weight: 500;
            font-size: .78rem;
            transition: all .25s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin: .1rem 0;
            text-decoration: none;
        }
        .sidebar .nav-link-tertiary:hover {
            background: rgba(5, 150, 105, 0.06);
            color: #059669;
        }
        .sidebar .nav-link-tertiary.active {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.12) 0%, rgba(4, 120, 87, 0.12) 100%);
            color: #059669;
        }
        .sidebar .nav-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #d1d5db;
        }
        .sidebar .nav-link-tertiary.active .nav-dot {
            background: #059669;
        }
        .sidebar .nav-link:hover {
            background: rgba(5, 150, 105, 0.06);
            color: #059669;
        }
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(4, 120, 87, 0.1) 100%);
            color: #059669;
        }
        .sidebar .nav-link .link-ico {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            background: rgba(5, 150, 105, 0.08);
            color: #059669;
            font-size: 1.15rem;
        }
        .sidebar-footer {
            border-top: 1px solid #e2e8f0;
            padding: 1rem .5rem;
            margin-top: auto;
            background: rgba(255,255,255,.5);
            width: 100%;
            display: none;
        }
        .sidebar-footer .btn { border-radius: .5rem; }
        .with-fixed-navbar { padding-top: 64px; }
        /* Notification Dropdown */
        .notification-dropdown {
            min-width: 360px;
            max-height: 480px;
            overflow-y: auto;
        }
        .notification-item {
            transition: background .2s ease;
            border-left: 3px solid transparent;
        }
        .notification-item:hover {
            background: #f8f9fa;
        }
        .notification-item.unread {
            background: rgba(59, 130, 246, 0.05);
            border-left-color: #3b82f6;
        }
 

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            font-size: 0.7rem;
            font-weight: 700;
            line-height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 1;
        }
        /* Content */
        .content {
            transition: margin-left .3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8f9fa;
            min-height: calc(100vh - 64px);
            margin-left: 80px;
            padding: 1.5rem;
        }
        @media (min-width: 992px) {
            body.sidebar-expanded .sidebar {
                width: 220px;
                align-items: flex-start;
                padding: 1rem;
            }
            body.sidebar-expanded .sidebar .nav-section {
                display: block;
                text-align: left;
            }
            body.sidebar-expanded .sidebar .nav-link {
                justify-content: flex-start;
                padding: .625rem .875rem;
            }
            body.sidebar-expanded .sidebar .nav-link .link-text {
                display: inline;
                opacity: 1;
            }
            body.sidebar-expanded .sidebar .nav-link-sub .link-text {
                display: inline;
                opacity: 1;
            }
            body.sidebar-expanded .sidebar .nav-link-tertiary .link-text {
                display: inline;
                opacity: 1;
            }
            body.sidebar-expanded .sidebar .sidebar-footer {
                display: block;
            }
            body.sidebar-expanded .content {
                margin-left: 200px;
            }
        }
        @media (max-width: 991.98px) {
            .sidebar {
                width: 90px;
                transform: translateX(-100%);
                box-shadow: 6px 0 16px rgba(0,0,0,0.12);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-light d-lg-none" id="sidebarToggle"><i class="fa fa-bars"></i></button>
                <button class="btn btn-sm btn-outline-light d-none d-lg-inline-flex" id="sidebarExpandBtn" title="Menyuni kengaytir">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand ms-1" href="{{ route('supervisor.dashboard') }}">
                    <i class="fa fa-user-tie me-2"></i>Rahbar Paneli
                </a>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->unread()->count();
                        @endphp
                        <span class="notification-badge bg-danger rounded-pill" id="notificationBadge" style="display:{{ $unreadCount > 0 ? 'flex' : 'none' }};">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown shadow-lg" aria-labelledby="notificationDropdown">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <h6 class="mb-0 fw-bold">Xabarlar</h6>
                            <button class="btn btn-sm btn-link text-decoration-none p-0" id="markAllReadDropdown">
                                <small>Barchasini o'qilgan</small>
                            </button>
                        </div>
                        <div id="notificationList" class="py-2">
                            <div class="text-center py-4 text-muted">
                                <i class="fa fa-spinner fa-spin"></i>
                                <p class="mb-0 small">Yuklanmoqda...</p>
                            </div>
                        </div>
                        <div class="border-top p-2">
                            <a href="{{ route('supervisor.messages.index') }}" class="btn btn-sm btn-outline-primary w-100">
                                Xabarlar sahifasiga o'tish
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-user-circle me-1"></i><span class="d-none d-sm-inline">{{ auth()->user()->name ?? 'Rahbar' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="{{ route('supervisor.profile.index') }}"><i class="fa fa-user me-2"></i>Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit"><i class="fa fa-right-from-bracket me-2"></i>Chiqish</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="d-flex with-fixed-navbar">
        <aside class="sidebar bg-light border-end" id="sidebar">
            <!-- <div class="d-flex align-items-center justify-content-end mb-2 d-lg-none px-2 pt-2">
                <button class="btn btn-sm btn-outline-secondary" id="sidebarClose"><i class="fa fa-xmark"></i></button>
            </div> -->

            @php
                $supervisorGroups = collect();
                try {
                    $supervisorGroups = auth()->user()?->groups()
                        ->with(['students' => function ($query) {
                            $query->where('supervisor_id', auth()->id())
                                  ->select('id', 'full_name', 'group_id')
                                  ->orderBy('full_name')
                                  ->limit(3); // Har guruh uchun 3 ta talaba
                        }])
                        ->select('groups.id', 'groups.name')
                        ->orderBy('name')
                        ->get();
                } catch (\Exception $e) {
                }
            @endphp

            <div class="nav-section"><span class="link-text">ASOSIY</span></div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}" data-bs-toggle="tooltip" data-bs-title="Dashboard">
                        <span class="link-ico"><i class="fa fa-gauge"></i></span><span class="link-text">Dashboard</span>
                    </a>
                </li>
            </ul>

            <div class="nav-section"><span class="link-text">MODULLAR</span></div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.students') || request()->routeIs('supervisor.students.group') ? 'active' : '' }}" href="{{ route('supervisor.students') }}">
                        <span class="link-ico"><i class="fa fa-users"></i></span><span class="link-text">Talabalar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.logbooks') ? 'active' : '' }}" href="{{ route('supervisor.logbooks') }}">
                        <span class="link-ico"><i class="fa fa-book"></i></span><span class="link-text">Kundaliklar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.attendance') ? 'active' : '' }}" href="{{ route('supervisor.attendance') }}">
                        <span class="link-ico"><i class="fa fa-clipboard-check"></i></span><span class="link-text">Davomat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.evaluations') ? 'active' : '' }}" href="{{ route('supervisor.evaluations') }}">
                        <span class="link-ico"><i class="fa fa-star"></i></span><span class="link-text">Baholash</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.tasks*') ? 'active' : '' }}" href="{{ route('supervisor.tasks.index') }}">
                        <span class="link-ico"><i class="fa fa-tasks"></i></span><span class="link-text">Topshiriqlar</span>
                    </a>
                </li>
            </ul>

            <div class="nav-section"><span class="link-text">MULOQOT</span></div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.messages.*') ? 'active' : '' }}" href="{{ route('supervisor.messages.index') }}">
                        <span class="link-ico position-relative">
                            <i class="fa fa-comments"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" id="messagesBadge" style="display:none;">
                                <span class="visually-hidden">Yangi xabarlar</span>
                            </span>
                        </span>
                        <span class="link-text">Xabarlar</span>
                    </a>
                </li>
            </ul>

            <div class="nav-section"><span class="link-text">SOZLAMALAR</span></div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.profile.*') ? 'active' : '' }}" href="{{ route('supervisor.profile.index') }}">
                        <span class="link-ico"><i class="fa fa-user-circle"></i></span><span class="link-text">Profil</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer mt-auto small text-muted pt-3">
                <div class="d-flex flex-column gap-2">
                    <!-- <button class="btn btn-sm btn-outline-primary w-100" id="pinToggle" title="Pinni yoqish/o'chirish">
                        <i class="fa fa-thumbtack me-2"></i><span class="link-text">Menyuni mahkamlash</span>
                    </button> -->
                    <div class="text-center link-text" style="font-size:.75rem;">© {{ date('Y') }} Rahbar Panel</div>
                </div>
            </div>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el=>new bootstrap.Tooltip(el));

        // Notification System
        function loadNotifications() {
            fetch('{{ route("supervisor.notifications.recent") }}')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.unread_count);
                    renderNotifications(data.notifications);
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    document.getElementById('notificationList').innerHTML = 
                        '<div class="text-center py-4 text-muted"><p class="mb-0 small">Xatolik yuz berdi</p></div>';
                });
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        function renderNotifications(notifications) {
            const list = document.getElementById('notificationList');
            if (notifications.length === 0) {
                list.innerHTML = '<div class="text-center py-4 text-muted"><p class="mb-0 small">Xabarlar yo\'q</p></div>';
                return;
            }

            list.innerHTML = notifications.map(n => {
                // Extract student_id from notification data if it's a message notification
                let clickHandler = `markNotificationRead(${n.id})`;
                if (n.type === 'message_received' && n.data && n.data.student_id) {
                    clickHandler = `handleNotificationClick(${n.id}, ${n.data.student_id})`;
                }
                
                return `
                    <div class="notification-item px-3 py-2 ${n.is_read ? '' : 'unread'}" style="cursor:pointer;" onclick="${clickHandler}">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-2">
                                <i class="fa ${n.icon} text-${n.color}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small fw-bold">${n.title}</h6>
                                <p class="mb-1 small text-muted">${n.message}</p>
                                <small class="text-muted" style="font-size:0.7rem;">
                                    <i class="fa fa-clock me-1"></i>${n.created_at}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function handleNotificationClick(notificationId, studentId) {
            // Mark as read first
            fetch(`/supervisor/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Redirect to chat with student
                    window.location.href = `{{ url('supervisor/messages') }}/${studentId}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Still redirect even if marking read fails
                window.location.href = `{{ url('supervisor/messages') }}/${studentId}`;
            });
        }

        function markNotificationRead(id) {
            fetch(`/supervisor/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Mark all as read from dropdown
        document.getElementById('markAllReadDropdown')?.addEventListener('click', function() {
            fetch('{{ route("supervisor.notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', loadNotifications);

        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);

        // Load unread messages count
        function loadUnreadMessages() {
            fetch('{{ route("supervisor.messages.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('messagesBadge');
                    if (data.count > 0) {
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        // Load on page load
        document.addEventListener('DOMContentLoaded', loadUnreadMessages);
        
        // Refresh every 30 seconds
        setInterval(loadUnreadMessages, 30000);
        const body = document.body;
        const sidebar = document.getElementById('sidebar');
        const btnToggle = document.getElementById('sidebarToggle');
        const btnExpand = document.getElementById('sidebarExpandBtn');
        const btnClose = document.getElementById('sidebarClose');

        // Create backdrop for mobile
        const backdrop = document.createElement('div');
        backdrop.id = 'sidebarBackdrop';
        backdrop.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1010;display:none;';
        document.body.appendChild(backdrop);

        function openSidebar(){
            const isShown = sidebar.classList.contains('show');
            if(isShown){
                sidebar.classList.remove('show');
                backdrop.style.display='none';
            } else {
                sidebar.classList.add('show');
                backdrop.style.display='block';
            }
        }
        function closeSidebar(){ sidebar.classList.remove('show'); backdrop.style.display='none'; }

        // Mobile show/hide
        btnToggle && btnToggle.addEventListener('click', openSidebar);
        btnClose && btnClose.addEventListener('click', closeSidebar);
        backdrop.addEventListener('click', closeSidebar);

        // Desktop expand/collapse with persistence
        const LS_KEY = 'sidebar-expanded';
        function toggleExpand(){
            if(window.matchMedia('(min-width: 992px)').matches){
                const isExpanded = body.classList.toggle('sidebar-expanded');
                if(isExpanded){
                    localStorage.setItem(LS_KEY, '1');
                } else {
                    localStorage.removeItem(LS_KEY);
                }
            }
        }

        // Restore state on load
        if(localStorage.getItem(LS_KEY) === '1'){
            body.classList.add('sidebar-expanded');
        }

        // Expand/collapse buttons
        btnExpand && btnExpand.addEventListener('click', toggleExpand);
        const pinToggle = document.getElementById('pinToggle');
        pinToggle && pinToggle.addEventListener('click', toggleExpand);
        // btnPinToggle && btnPinToggle.addEventListener('click', toggleExpand);

        // Ensure proper state on resize
        window.addEventListener('resize', ()=>{
            if(!window.matchMedia('(min-width: 992px)').matches){
                body.classList.remove('sidebar-expanded');
                closeSidebar();
            } else {
                backdrop.style.display='none';
            }
        });
    </script>
</body>
</html>
