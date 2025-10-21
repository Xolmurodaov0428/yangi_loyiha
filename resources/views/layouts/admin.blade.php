<!DOCTYPE html>
<html lang="uz">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Admin paneli')</title>
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
      background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important; 
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
    #sidebarExpandBtn {
      position: relative;
      overflow: hidden;
    }
    #sidebarExpandBtn::after {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,.1);
      transform: translateX(-100%);
      transition: transform .3s ease;
    }
    #sidebarExpandBtn:hover::after {
      transform: translateX(0);
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
    .sidebar::-webkit-scrollbar { width: 5px; }
    .sidebar::-webkit-scrollbar-track { background: transparent; }
    .sidebar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; transition: background .2s; }
    .sidebar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

    .sidebar .brand { font-weight: 700; font-size: 1.1rem; color: #4f46e5; display: none; }
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
    .sidebar .nav-section::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 30px;
      height: 2px;
      background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
    }

    .sidebar .nav-link {
      position: relative;
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
      overflow: hidden;
    }
    .sidebar .nav-link::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 0;
      background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
      border-radius: 0 3px 3px 0;
      transition: height .25s ease;
    }
    .sidebar .nav-link:hover::before { height: 60%; }
    .sidebar .nav-link.active::before { height: 70%; }
    .sidebar .nav-link:hover { 
      background: rgba(99, 102, 241, 0.06); 
      color: #4f46e5;
      transform: translateX(2px);
    }
    .sidebar .nav-link.active {
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
      color: #4f46e5;
      transform: translateX(2px);
    }
    .sidebar .nav-link.active .link-ico { 
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .sidebar .nav-link .link-ico {
      width: 44px;
      height: 44px;
      display: grid;
      place-items: center;
      border-radius: 11px;
      background: rgba(99, 102, 241, 0.08);
      color: #6366f1;
      font-size: 1.15rem;
      transition: all .25s cubic-bezier(0.25, 0.8, 0.25, 1);
      flex-shrink: 0;
    }
    .sidebar .nav-link:hover .link-ico { 
      background: rgba(99, 102, 241, 0.12); 
      transform: scale(1.05);
      box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);
    }
    .sidebar .nav-link .link-text { 
      display: none; 
      opacity: 0;
      white-space: nowrap;
      transition: opacity .2s ease;
    }

    .sidebar .submenu { 
      padding: .5rem 0 .25rem 0;
      width: 100%;
      margin-top: 0;
      background: rgba(99, 102, 241, 0.02);
      border-radius: 8px;
      border-left: 2px solid rgba(99, 102, 241, 0.2);
      margin-left: 4px;
    }
    .sidebar .submenu .nav-link { 
      padding: .5rem;
      font-size: .85rem;
      margin: .15rem 0;
      border-radius: 8px;
    }
    .sidebar .submenu .nav-link .link-ico {
      width: 36px;
      height: 36px;
      font-size: 1rem;
    }
    .sidebar .nav-link .fa-chevron-down { 
      display: none;
      font-size: .7rem;
      margin-left: auto;
      transition: transform .3s cubic-bezier(0.25, 0.8, 0.25, 1);
      opacity: .5;
      color: currentColor;
    }
    .sidebar .nav-link[aria-expanded="true"] { 
      background: rgba(99, 102, 241, 0.08);
      color: #4f46e5;
    }
    .sidebar .nav-link[aria-expanded="true"]::before { 
      height: 80%;
    }
    .sidebar .nav-link[aria-expanded="true"] .fa-chevron-down { 
      transform: rotate(180deg);
      opacity: 1;
    }
    .sidebar .nav-link[aria-expanded="true"] .link-ico {
      background: rgba(99, 102, 241, 0.15);
      color: #6366f1;
    }
    
    /* Submenu collapse animation */
    .sidebar .submenu.collapsing {
      transition: height .3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .sidebar .submenu.show {
      animation: slideDown .3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
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

    /* Desktop - expandable */
    @media (min-width: 992px) {
      .sidebar { width: 80px; }
      .content { margin-left: 80px; transition: margin-left .35s cubic-bezier(0.25, 0.8, 0.25, 1); }
      
      /* Mini mode - show chevron as badge */
      .sidebar .nav-link .fa-chevron-down {
        display: block;
        position: absolute;
        right: 4px;
        top: 4px;
        font-size: .6rem;
        background: rgba(99, 102, 241, 0.15);
        width: 16px;
        height: 16px;
        display: grid;
        place-items: center;
        border-radius: 4px;
      }
      
      /* Expanded state */
      body.sidebar-expanded .sidebar { 
        width: 270px; 
        align-items: flex-start;
        padding: 1rem;
        box-shadow: 6px 0 20px rgba(0,0,0,0.06);
      }
      body.sidebar-expanded .sidebar .brand { display: block; }
      body.sidebar-expanded .sidebar .nav-section { 
        display: block; 
        text-align: left;
        padding-left: .5rem;
      }
      body.sidebar-expanded .sidebar .nav-section::after {
        left: 0;
        transform: none;
        width: 40px;
      }
      body.sidebar-expanded .sidebar .nav-link { 
        justify-content: flex-start; 
        padding: .625rem .875rem;
      }
      body.sidebar-expanded .sidebar .nav-link .link-text { 
        display: inline; 
        opacity: 1;
      }
      body.sidebar-expanded .sidebar .fa-chevron-down { 
        display: inline;
        position: static;
        width: auto;
        height: auto;
        background: transparent;
      }
      body.sidebar-expanded .sidebar .sidebar-footer { 
        display: block;
      }
      body.sidebar-expanded .sidebar .submenu {
        margin-left: 0.5rem;
        padding: .5rem;
      }
      body.sidebar-expanded .sidebar .submenu .nav-link {
        padding: .5rem .75rem;
        padding-left: 2.5rem;
        position: relative;
      }
      body.sidebar-expanded .sidebar .submenu .nav-link::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background: currentColor;
        border-radius: 50%;
        opacity: .3;
        transition: all .2s ease;
      }
      body.sidebar-expanded .sidebar .submenu .nav-link:hover::before,
      body.sidebar-expanded .sidebar .submenu .nav-link.active::before {
        opacity: 1;
        transform: translateY(-50%) scale(1.3);
      }
      body.sidebar-expanded .content { margin-left: 270px; }
    }

    /* Mobile */
    @media (max-width: 991.98px) {
      .sidebar {
        position: fixed;
        z-index: 1040;
        left: -80px;
        top: 64px;
        bottom: 0;
        width: 80px;
      }
      .sidebar.show { left: 0; }
      .content { margin-left: 0; }
    }

    /* Content */
    .with-fixed-navbar { padding-top: 64px; }
    .content { 
      transition: margin-left .3s cubic-bezier(0.4, 0, 0.2, 1); 
      background: #f8f9fa; 
      min-height: calc(100vh - 64px);
      margin-left: 80px;
    }
    @media (max-width: 991.98px) {
      .content { margin-left: 0; }
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline-light d-lg-none" id="sidebarToggle"><i class="fa fa-bars"></i></button>
        <button class="btn btn-sm btn-outline-light d-none d-lg-inline-flex" id="sidebarExpandBtn" title="Menyuni kengaytir/siq">
          <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand ms-1" href="{{ route('admin.dashboard') }}">
          <i class="fa fa-shield-halved me-2"></i>Admin Panel
        </a>
      </div>
      <div class="ms-auto d-flex align-items-center gap-2">
        <span class="text-white small d-none d-sm-inline"><i class="fa fa-user-circle me-1"></i>{{ auth()->user()->name ?? 'Admin' }}</span>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
          @csrf
          <button class="btn btn-sm btn-outline-light"><i class="fa fa-right-from-bracket me-1"></i>Chiqish</button>
        </form>
      </div>
    </div>
  </nav>

  <div class="d-flex with-fixed-navbar">
    <aside class="sidebar bg-light border-end d-flex flex-column" id="sidebar">
      <div class="d-flex align-items-center justify-content-end mb-2 d-lg-none px-2 pt-2">
        <button class="btn btn-sm btn-outline-secondary" id="sidebarClose"><i class="fa fa-xmark"></i></button>
      </div>

      <div class="nav-section"><span class="link-text">ASOSIY</span></div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" data-bs-toggle="tooltip" data-bs-title="Dashboard">
            <span class="link-ico"><i class="fa fa-gauge"></i></span><span class="link-text">Dashboard</span>
          </a>
        </li>
      </ul>

      <div class="nav-section"><span class="link-text">MODULLAR</span></div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#usersMenu" role="button" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" aria-controls="usersMenu">
            <span class="link-ico"><i class="fa fa-user-gear"></i></span><span class="link-text">Foydalanuvchilar</span>
            <i class="fa fa-chevron-down ms-auto small"></i>
          </a>
          <div class="collapse submenu {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="usersMenu">
            <ul class="nav flex-column mt-1">
              <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><span class="link-ico"><i class="fa fa-list"></i></span><span class="link-text">Ro'yxat</span></a></li>
              <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}"><span class="link-ico"><i class="fa fa-user-plus"></i></span><span class="link-text">Yangi qo'shish</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#"><span class="link-ico"><i class="fa fa-building"></i></span><span class="link-text">Tashkilotlar</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#studentsMenu" role="button" aria-expanded="{{ request()->routeIs('admin.students.*') ? 'true' : 'false' }}" aria-controls="studentsMenu">
            <span class="link-ico"><i class="fa fa-graduation-cap"></i></span><span class="link-text">Talabalar</span>
            <i class="fa fa-chevron-down ms-auto small"></i>
          </a>
          <div class="collapse submenu {{ request()->routeIs('admin.students.*') ? 'show' : '' }}" id="studentsMenu">
            <ul class="nav flex-column mt-1">
              <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.students.index') ? 'active' : '' }}" href="{{ route('admin.students.index') }}"><span class="link-ico"><i class="fa fa-list"></i></span><span class="link-text">Ro'yxat</span></a></li>
              <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.students.create') ? 'active' : '' }}" href="{{ route('admin.students.create') }}"><span class="link-ico"><i class="fa fa-user-plus"></i></span><span class="link-text">Bitta qo'shish</span></a></li>
              <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.students.import') ? 'active' : '' }}" href="{{ route('admin.students.import') }}"><span class="link-ico"><i class="fa fa-file-import"></i></span><span class="link-text">Guruh qo'shish</span></a></li>
              <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.students.attendance') ? 'active' : '' }}" href="{{ route('admin.students.attendance') }}"><span class="link-ico"><i class="fa fa-clipboard-check"></i></span><span class="link-text">Davomat</span></a></li>
              <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.students.tasks') ? 'active' : '' }}" href="{{ route('admin.students.tasks') }}"><span class="link-ico"><i class="fa fa-tasks"></i></span><span class="link-text">Topshiriq</span></a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}"><span class="link-ico"><i class="fa fa-chart-line"></i></span><span class="link-text">Hisobotlar</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" href="{{ route('admin.messages.index') }}"><span class="link-ico"><i class="fa fa-envelope"></i></span><span class="link-text">Xabarlar</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}" href="{{ route('admin.activity-logs') }}"><span class="link-ico"><i class="fa fa-history"></i></span><span class="link-text">Faoliyat Jurnali</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.api-tokens.*') ? 'active' : '' }}" href="{{ route('admin.api-tokens.index') }}"><span class="link-ico"><i class="fa fa-key"></i></span><span class="link-text">API Tokenlar</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}"><span class="link-ico"><i class="fa fa-gear"></i></span><span class="link-text">Sozlamalar</span></a>
        </li>
      </ul>

      <div class="sidebar-footer mt-auto small text-muted pt-3">
        <div class="d-flex flex-column gap-2">
          <button class="btn btn-sm btn-outline-primary w-100" id="pinToggle" title="Pinni yoqish/o'chirish">
            <i class="fa fa-thumbtack me-2"></i><span class="link-text">Menyuni mahkamlash</span>
          </button>
          <div class="text-center link-text" style="font-size:.75rem;">© {{ date('Y') }} Admin Panel</div>
        </div>
      </div>
    </aside>

    <main class="content p-4">
      @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el=>new bootstrap.Tooltip(el));
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const btnToggle = document.getElementById('sidebarToggle');
    const btnClose = document.getElementById('sidebarClose');
    const btnExpand = document.getElementById('sidebarExpandBtn');
    const btnPinToggle = document.getElementById('pinToggle');

    // Create backdrop for mobile
    const backdrop = document.createElement('div');
    backdrop.id = 'sidebarBackdrop';
    backdrop.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1039;display:none;';
    document.body.appendChild(backdrop);

    function openSidebar(){ sidebar.classList.add('show'); backdrop.style.display='block'; }
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
    btnPinToggle && btnPinToggle.addEventListener('click', toggleExpand);

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
