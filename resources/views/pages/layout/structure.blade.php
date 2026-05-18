{{-- resources/views/layouts/msit/structure.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>@yield('title','Attendance System')</title>

  <meta name="csrf-token" content="{{ csrf_token() }}"/>

  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/images/web/logo.png') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('/assets/css/main.css') }}">

  @stack('styles')
  @yield('styles')

  <style>
    *, *::before, *::after{
      box-sizing:border-box;
    }

    :root{
      --w3-rail-w: 236px;
      --w3-rail-bg: var(--surface);
      --w3-rail-text: var(--text-color);
      --w3-rail-muted: var(--muted-color);
      --w3-rail-border: var(--line-strong);
      --w3-rail-hover: rgba(15,118,110,.06);
      --w3-rail-active: rgba(15,118,110,.12);
      --w3-appbar-h: 52px;

      --w3-rule-grad-l: linear-gradient(90deg, rgba(2,6,23,0), rgba(2,6,23,.12), rgba(2,6,23,0));
      --w3-rule-grad-d: linear-gradient(90deg, rgba(226,232,240,0), rgba(226,232,240,.18), rgba(226,232,240,0));
    }

    body{
      min-height:100dvh;
      background:var(--bg-body);
      color:var(--text-color);
      overflow-x:hidden;
      max-width:100%;
    }

    .w3-sidebar{
      position:fixed;
      inset:0 auto 0 0;
      width:var(--w3-rail-w);
      background:var(--w3-rail-bg);
      border-right:1px solid var(--w3-rail-border);
      display:flex;
      flex-direction:column;
      z-index:1041;
      transform:translateX(0);
      transition:transform .28s cubic-bezier(.4,0,.2,1);
    }

    .w3-sidebar-head{
      height:var(--w3-appbar-h);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:8px 10px;
      border-bottom:1px solid var(--w3-rail-border);
      flex-shrink:0;
    }

    .w3-brand{
      display:flex;
      align-items:center;
      justify-content:center;
      text-decoration:none;
      width:100%;
    }

    .w3-brand img{
      height:40px;
      width:auto;
      max-width:150px;
      display:block;
    }

    .w3-sidebar-scroll{
      flex:1;
      min-height:0;
      overflow:auto;
      padding:6px 8px;
      scroll-behavior:smooth;
    }

    .w3-sidebar-foot{
      margin-top:auto;
      border-top:1px solid var(--w3-rail-border);
      padding:6px 8px;
      flex-shrink:0;
      background:var(--surface);
      position:relative;
      z-index:2;
    }

    .w3-nav-section{
      padding:8px 4px 4px;
    }

    .w3-section-title{
      display:flex;
      align-items:center;
      gap:7px;
      color:var(--primary-color);
      font-size:var(--fs-12);
      font-weight:700;
      letter-spacing:.08rem;
      text-transform:uppercase;
      padding:0 4px;
      line-height:1.15;
    }

    .w3-section-rule{
      height:8px;
      display:grid;
      align-items:center;
    }

    .w3-section-rule::before{
      content:"";
      height:1px;
      width:100%;
      background:var(--w3-rule-grad-l);
    }

    html.theme-dark .w3-section-rule::before{
      background:var(--w3-rule-grad-d);
    }

    .w3-menu{
      display:grid;
      gap:3px;
      padding:4px 2px;
    }

    .w3-group{
      display:grid;
      gap:3px;
      margin-top:1px;
    }

    .w3-link{
      position:relative;
      display:flex;
      align-items:center;
      gap:8px;
      padding:7px 8px;
      color:var(--w3-rail-text);
      border-radius:var(--radius-2);
      transition:background .16s ease, transform .16s ease, color .16s ease, box-shadow .16s ease;
      text-decoration:none;
      font-size:var(--fs-12);
      line-height:1.2;
    }

    .w3-link i{
      min-width:16px;
      text-align:center;
      opacity:.95;
      font-size:.92em;
    }

    .w3-link:hover{
      background:var(--w3-rail-hover);
      transform:translateX(1px);
      color:var(--ink);
    }

    .w3-link.active{
      background:var(--w3-rail-active);
      color:var(--ink);
      font-weight:600;
    }

    .w3-link.active::before{
      content:"";
      position:absolute;
      left:-4px;
      top:6px;
      bottom:6px;
      width:2px;
      background:var(--accent-color);
      border-radius:2px;
    }

    .w3-link.w3-focus-flash{
      animation:w3FocusFlash 1.05s ease;
    }

    @keyframes w3FocusFlash{
      0%   { box-shadow: 0 0 0 0 rgba(15,118,110,0); }
      20%  { box-shadow: 0 0 0 5px rgba(15,118,110,.14); }
      100% { box-shadow: 0 0 0 0 rgba(15,118,110,0); }
    }

    html.theme-dark .w3-link.w3-focus-flash{
      animation:w3FocusFlashDark 1.05s ease;
    }

    @keyframes w3FocusFlashDark{
      0%   { box-shadow: 0 0 0 0 rgba(15,118,110,0); }
      20%  { box-shadow: 0 0 0 5px rgba(15,118,110,.18); }
      100% { box-shadow: 0 0 0 0 rgba(15,118,110,0); }
    }

    .w3-toggle{
      cursor:pointer;
      user-select:none;
    }

    .w3-toggle .w3-chev{
      margin-left:auto;
      margin-right:1px;
      padding-left:4px;
      opacity:.8;
      font-size:.8em;
      transition:transform .18s ease;
    }

    .w3-toggle.w3-open .w3-chev{
      transform:rotate(180deg);
    }

    .w3-submenu{
      display:none;
      margin-left:6px;
      padding-left:6px;
      border-left:1px dashed var(--w3-rail-border);
    }

    .w3-submenu .w3-link{
      padding:7px 8px 7px 28px;
      margin-bottom:2px;
      font-size:var(--fs-12);
    }

    .w3-appbar{
      position:sticky;
      top:0;
      z-index:1030;
      height:var(--w3-appbar-h);
      background:var(--surface);
      border-bottom:1px solid var(--line-strong);
      display:flex;
      align-items:center;
      transition:box-shadow .22s ease, background .22s ease;
      animation:fadeInDown .38s ease both;
    }

    .w3-appbar.w3-appbar-scrolled{
      box-shadow:0 4px 18px rgba(15,23,42,.08);
    }

    .w3-appbar-inner{
      width:100%;
      display:flex;
      align-items:center;
      gap:8px;
      padding:0 10px;
    }

    .w3-app-logo{
      display:flex;
      align-items:center;
      gap:7px;
      text-decoration:none;
    }

    .w3-app-logo img{
      height:18px;
      width:auto;
    }

    .w3-app-logo span{
      font-family:var(--font-head);
      font-weight:700;
      color:var(--ink);
      font-size:var(--fs-13);
      line-height:1;
    }

    .w3-title{
      font-family:var(--font-head);
      color:var(--ink);
      font-size:var(--fs-14);
      line-height:1.1;
      font-weight:600;
      margin:0;
    }

    .w3-icon-btn{
      width:34px;
      height:34px;
      display:inline-grid;
      place-items:center;
      border:1px solid var(--line-strong);
      background:var(--surface);
      color:var(--secondary-color);
      border-radius:var(--radius-1);
      transition:transform .16s ease, background .16s ease, border-color .16s ease, box-shadow .16s ease;
      padding:0;
      box-shadow:none;
    }

    .w3-icon-btn i{
      font-size:12px;
      line-height:1;
    }

    .w3-icon-btn:hover{
      background:var(--page-hover);
      transform:translateY(-1px);
      box-shadow:0 4px 12px rgba(15,23,42,.08);
    }

    .w3-notify-btn{
      position:relative;
    }

    .w3-notify-badge{
      position:absolute;
      top:-4px;
      right:-3px;
      min-width:17px;
      height:17px;
      padding:0 4px;
      border-radius:999px;
      background:#dc2626;
      color:#fff;
      font-size:9px;
      font-weight:800;
      display:none;
      align-items:center;
      justify-content:center;
      border:2px solid var(--surface);
      line-height:1;
    }

    .w3-notify-badge.show{
      display:inline-flex;
    }

    .w3-profile-link{
      width:36px;
      height:36px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1.5px solid var(--line-strong);
      background:var(--surface);
      border-radius:999px;
      overflow:hidden;
      text-decoration:none;
      transition:transform .16s ease, background .16s ease, border-color .16s ease, box-shadow .16s ease;
      box-shadow:none;
    }

    .w3-profile-link:hover{
      background:var(--page-hover);
      transform:translateY(-1px);
    }

    .w3-profile-avatar{
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
    }

    .w3-profile-initial{
      width:100%;
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:12px;
      font-weight:800;
      color:var(--secondary-color);
      background:rgba(15,118,110,.08);
      text-transform:uppercase;
      line-height:1;
    }

    .w3-hamburger{
      width:36px;
      height:36px;
      border:1px solid var(--line-strong);
      border-radius:var(--radius-1);
      background:var(--surface);
      display:inline-grid;
      place-items:center;
      cursor:pointer;
      padding:0;
      box-shadow:none;
      transition:background .16s ease, transform .16s ease;
    }

    .w3-hamburger:hover{
      background:var(--page-hover);
      transform:scale(1.04);
    }

    .w3-bars{
      position:relative;
      width:14px;
      height:10px;
    }

    .w3-bar{
      position:absolute;
      left:0;
      width:100%;
      height:2px;
      background:#1f2a44;
      border-radius:2px;
      transition:transform .22s ease, opacity .18s ease, top .22s ease;
    }

    .w3-bar:nth-child(1){top:0}
    .w3-bar:nth-child(2){top:4px}
    .w3-bar:nth-child(3){top:8px}

    .w3-hamburger.is-active .w3-bar:nth-child(1){top:4px; transform:rotate(45deg)}
    .w3-hamburger.is-active .w3-bar:nth-child(2){opacity:0}
    .w3-hamburger.is-active .w3-bar:nth-child(3){top:4px; transform:rotate(-45deg)}

    .w3-note{
      border:1px solid var(--line-strong);
      background:var(--surface-2);
      border-radius:var(--radius-2);
      padding:8px 10px;
    }

    .w3-content{
      width:100%;
      max-width:100%;
      min-width:0;
      padding:12px;
      margin-inline:auto;
      overflow-x:clip;
      transition:padding .24s ease;
    }

    .panel{
      width:100%;
      max-width:100%;
      min-width:0;
    }

    .w3-content .row,
    .w3-content [class*="col-"]{
      min-width:0;
    }

    .w3-content.w3-disabled-content{
      pointer-events:none;
      user-select:none;
      opacity:.45;
      filter:grayscale(.08);
    }

    @media (min-width: 992px){
      .w3-content{
        padding-left:calc(12px + var(--w3-rail-w));
      }

      .w3-app-logo{
        display:none;
      }
    }

    .w3-overlay{
      position:fixed;
      top:0;
      bottom:0;
      right:0;
      left:var(--w3-rail-w);
      background:rgba(0,0,0,.42);
      z-index:1040;
      opacity:0;
      visibility:hidden;
      pointer-events:none;
      transition:opacity .18s ease, visibility .18s ease;
    }

    .w3-overlay.w3-on{
      opacity:1;
      visibility:visible;
      pointer-events:auto;
    }

    @media (max-width: 991px){
      .w3-sidebar{
        transform:translateX(-100%);
      }

      .w3-sidebar.w3-on{
        transform:translateX(0);
      }

      .w3-content{
        padding-left:12px;
      }

      .js-theme-btn{
        display:none!important;
      }

      .w3-app-logo{
        display:flex;
      }

      /* On mobile the overlay covers the full viewport (behind the sidebar) */
      .w3-overlay{
        left:0;
      }

      /* Show profile link on mobile */
      .w3-profile-link{
        display:inline-flex!important;
      }
    }

    @media (max-width: 575px){
      .w3-appbar-inner{
        padding:0 8px;
      }
    }

    html.theme-dark .w3-sidebar{
      background:var(--surface);
      border-right-color:var(--line-strong);
    }

    html.theme-dark .w3-sidebar-head{
      border-bottom-color:var(--line-strong);
    }

    html.theme-dark .w3-link:hover{
      background:#0c172d;
    }

    html.theme-dark .w3-link.active{
      background:rgba(15,118,110,.18);
    }

    html.theme-dark .w3-overlay{
      background:rgba(0,0,0,.55);
    }

    html.theme-dark .w3-appbar{
      background:var(--surface);
      border-bottom-color:var(--line-strong);
    }

    html.theme-dark .w3-icon-btn,
    html.theme-dark .w3-hamburger,
    html.theme-dark .w3-profile-link{
      background:var(--surface);
      border-color:var(--line-strong);
      color:var(--text-color);
    }

    html.theme-dark .w3-icon-btn:hover,
    html.theme-dark .w3-hamburger:hover,
    html.theme-dark .w3-profile-link:hover{
      background:#0c172d;
    }

    html.theme-dark .w3-bar{
      background:#e8edf7;
    }

    html.theme-dark .w3-note{
      background:#0b1020;
      border-color:var(--line-strong);
    }

    html.theme-dark .w3-profile-initial{
      background:rgba(15,118,110,.18);
      color:#f7d7dd;
    }

    .w3-notif-drawer{
      border-left:1px solid var(--line-strong);
      background:var(--surface);
    }

    .w3-notif-drawer .offcanvas-header{
      border-bottom:1px solid var(--line-strong);
    }

    .w3-notif-list{
      display:flex;
      flex-direction:column;
      gap:10px;
    }

    .w3-notif-empty{
      text-align:center;
      padding:24px 16px;
      border:1px dashed var(--line-strong);
      border-radius:16px;
      background:var(--surface-2);
      color:var(--muted-color);
      font-size:12px;
    }

    .w3-notif-item{
      display:block;
      text-decoration:none;
      color:inherit;
      border:1px solid var(--line-strong);
      border-radius:16px;
      background:var(--surface);
      padding:14px;
      transition:transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    }

    .w3-notif-item:hover{
      transform:translateY(-1px);
      box-shadow:0 14px 28px rgba(15,23,42,.08);
      color:inherit;
    }

    .w3-notif-item.is-unread{
      border-color:rgba(53,92,125,.26);
      background:linear-gradient(180deg, rgba(53,92,125,.04), rgba(255,255,255,.98));
    }

    .w3-notif-row{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:10px;
    }

    .w3-notif-title{
      font-size:13px;
      font-weight:800;
      color:var(--ink);
      line-height:1.35;
    }

    .w3-notif-time{
      white-space:nowrap;
      color:var(--muted-color);
      font-size:11px;
    }

    .w3-notif-text{
      margin-top:6px;
      color:var(--muted-color);
      font-size:12px;
      line-height:1.55;
    }

    .w3-notif-meta{
      margin-top:8px;
      display:flex;
      gap:6px;
      flex-wrap:wrap;
    }

    .w3-notif-pill{
      display:inline-flex;
      align-items:center;
      gap:5px;
      padding:4px 8px;
      border-radius:999px;
      border:1px solid var(--line-strong);
      background:var(--surface-2);
      color:var(--muted-color);
      font-size:10px;
      font-weight:700;
    }

    .w3-notif-actions{
      margin-top:10px;
      display:flex;
      gap:8px;
      flex-wrap:wrap;
    }

    html.theme-dark .w3-notif-drawer,
    html.theme-dark .w3-notif-item,
    html.theme-dark .w3-notif-pill,
    html.theme-dark .w3-notif-empty{
      background:var(--surface);
      border-color:var(--line-strong);
    }

    html.theme-dark .w3-notif-item.is-unread{
      background:linear-gradient(180deg, rgba(59,130,246,.08), rgba(15,23,42,.96));
    }

    /* ── Page content entrance ── */
    .w3-content{
      animation:fadeInUp .38s .08s ease both;
    }

    @keyframes fadeInDown{from{opacity:0;transform:translateY(-14px)}to{opacity:1;transform:translateY(0)}}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
  </style>

  <style>
    html.theme-dark ::-webkit-scrollbar { width: 8px !important; }
    html.theme-dark ::-webkit-scrollbar-track { background: #1e293b !important; border-radius: 4px !important; }
    html.theme-dark ::-webkit-scrollbar-thumb { background: #475569 !important; border-radius: 4px !important; }
    html.theme-dark ::-webkit-scrollbar-thumb:hover { background: #64748b !important; }
    html.theme-dark .w3-sidebar-scroll::-webkit-scrollbar { width: 6px !important; }
    html.theme-dark .w3-sidebar-scroll::-webkit-scrollbar-track { background: #1e293b !important; }
    html.theme-dark .w3-sidebar-scroll::-webkit-scrollbar-thumb { background: #475569 !important; }
  </style>
</head>
<body>

<aside id="sidebar" class="w3-sidebar" aria-label="Sidebar">
  <div class="w3-sidebar-head">
    <a href="/dashboard" class="w3-brand">
      <img id="logo" src="{{ asset('/assets/media/images/web/logo.png') }}" alt="Attendance System">
    </a>
  </div>

  <div class="w3-sidebar-scroll">
    <div class="w3-nav-section">
      <div class="w3-section-title"><i class="fa-solid fa-chart-simple"></i> OVERVIEW</div>
      <div class="w3-section-rule"></div>
    </div>
    <nav class="w3-menu" aria-label="Overview">
      <a href="/dashboard" class="w3-link"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a>
    </nav>

    <div id="dynamicMenuWrap" style="display:none"></div>

    <div id="adminFullMenu" style="display:none">
      <div class="w3-nav-section">
        <div class="w3-section-title"><i class="fa-solid fa-users-gear"></i> WORKFORCE</div>
        <div class="w3-section-rule"></div>
      </div>
      <nav class="w3-menu" aria-label="Workforce">
        <a href="/user/manage" class="w3-link">
          <i class="fa-solid fa-user-group"></i><span>Manage Users</span>
        </a>
        <a href="/activity-logs" class="w3-link">
          <i class="fa-solid fa-clock-rotate-left"></i><span>Activity Logs</span>
        </a>
      </nav>

      <div class="w3-nav-section">
        <div class="w3-section-title"><i class="fa-solid fa-screwdriver-wrench"></i> ACCESS CONTROL</div>
        <div class="w3-section-rule"></div>
      </div>
      <nav class="w3-menu" aria-label="Access Control">
        <div class="w3-group">
          <a href="#" class="w3-link w3-toggle" data-target="sm-dashboard-menu" aria-expanded="false">
            <i class="fa-solid fa-puzzle-piece"></i><span>Dashboard Menu</span>
            <i class="fa fa-chevron-down w3-chev"></i>
          </a>
          <div id="sm-dashboard-menu" class="w3-submenu" role="group" aria-label="Dashboard Menu submenu">
            <a href="/dashboard-menu/create" class="w3-link"><span>Create Dashboard Menu</span></a>
            <a href="/dashboard-menu/manage" class="w3-link"><span>Manage Dashboard Menu</span></a>
          </div>
        </div>

        <div class="w3-group">
          <a href="#" class="w3-link w3-toggle" data-target="sm-page-privilege" aria-expanded="false">
            <i class="fa-solid fa-shield-halved"></i><span>Page Privileges</span>
            <i class="fa fa-chevron-down w3-chev"></i>
          </a>
          <div id="sm-page-privilege" class="w3-submenu" role="group" aria-label="Page Privileges submenu">
            <a href="/page-privilege/create" class="w3-link"><span>Create Page Privilege</span></a>
            <a href="/page-privilege/manage" class="w3-link"><span>Manage Page Privileges</span></a>
          </div>
        </div>

        <a href="/role-privileges/manage" class="w3-link">
          <i class="fa-solid fa-user-shield"></i><span>Assign Role Privileges</span>
        </a>
        <a href="/user-privileges/manage" class="w3-link">
          <i class="fa-solid fa-user-lock"></i><span>Assign User Privileges</span>
        </a>
      </nav>

      <div class="w3-nav-section d-lg-none">
        <div class="w3-section-title"><i class="fa-solid fa-house"></i> ACCOUNT</div>
        <div class="w3-section-rule"></div>
      </div>
    <nav class="w3-menu d-lg-none" aria-label="Account">
        <a href="/dashboard" class="w3-link"><i class="fa fa-gauge"></i><span>Dashboard</span></a>
      </nav>
    </div>
  </div>

  <div class="w3-sidebar-foot">
    <a href="/dashboard" class="w3-link">
      <i class="fa fa-gauge"></i><span>Dashboard</span>
    </a>
    <a href="/profile" class="w3-link">
      <i class="fa fa-user"></i><span>Profile</span>
    </a>
    <a href="#" id="logoutBtnSidebar" class="w3-link" style="padding:7px 8px">
      <i class="fa fa-right-from-bracket"></i><span>Logout</span>
    </a>
  </div>
</aside>

<header class="w3-appbar" id="w3Appbar">
  <div class="w3-appbar-inner">
    <button id="btnHamburger" class="w3-hamburger d-lg-none" aria-label="Open menu" aria-expanded="false" title="Menu">
      <span class="w3-bars" aria-hidden="true">
        <span class="w3-bar"></span><span class="w3-bar"></span><span class="w3-bar"></span>
      </span>
    </button>

    <a href="/dashboard" class="w3-app-logo d-lg-none">
      <img src="{{ asset('/assets/media/images/web/logo.png') }}" alt="Attendance System">
      <span>Attendance System</span>
    </a>

    <strong class="w3-title ms-1 d-none d-lg-inline">
      @yield('title','Attendance System')
    </strong>

    <div class="ms-auto d-flex align-items-center gap-2">
      <button id="btnFullscreen" class="w3-icon-btn d-none d-lg-inline-grid" aria-label="Toggle fullscreen" title="Fullscreen">
        <i class="fa-solid fa-expand" id="fullscreenIcon"></i>
      </button>

      <button id="btnTheme" class="w3-icon-btn js-theme-btn d-none d-lg-inline-grid" aria-label="Toggle theme" title="Toggle theme">
        <i class="fa-regular fa-moon" id="themeIcon"></i>
      </button>

      <button
        type="button"
        class="w3-icon-btn w3-notify-btn d-none"
        id="alertsMenu"
        data-bs-toggle="offcanvas"
        data-bs-target="#notificationDrawer"
        aria-controls="notificationDrawer"
        aria-label="Notifications"
        title="Notifications">
        <i class="fa-regular fa-bell"></i>
        <span class="w3-notify-badge" id="notificationBadge">0</span>
      </button>

      <a href="/profile" class="w3-profile-link d-none d-lg-inline-flex" id="profileCircleLink" aria-label="Profile" title="Profile">
        <img id="profileCircleImage" class="w3-profile-avatar" alt="Profile" style="display:none;">
        <span id="profileCircleLetter" class="w3-profile-initial">
          <i class="fa-regular fa-user"></i>
        </span>
      </a>
    </div>
  </div>
</header>

<div class="offcanvas offcanvas-end w3-notif-drawer d-none" tabindex="-1" id="notificationDrawer" aria-labelledby="notificationDrawerLabel">
  <div class="offcanvas-header">
    <div>
      <h5 class="offcanvas-title" id="notificationDrawerLabel">Notifications</h5>
      <div class="small text-muted">Updates routed to your account</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
      <a href="/notifications" class="btn btn-light btn-sm">
        <i class="fa fa-list me-1"></i>View All
      </a>
      <button type="button" class="btn btn-primary" id="drawerReadAllBtn">
        <i class="fa fa-check-double me-1"></i>Read All
      </button>
    </div>

    <div class="w3-notif-list" id="notificationDrawerList">
      <div class="w3-notif-empty">
        <i class="fa fa-spinner fa-spin me-2"></i>Loading notifications...
      </div>
    </div>
  </div>
</div>

<div id="sidebarOverlay" class="w3-overlay" aria-hidden="true"></div>

<main class="w3-content mx-auto" id="mainContentWrap">
  <section class="panel mx-auto">@yield('content')</section>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@stack('scripts')
@yield('scripts')

<script>
document.addEventListener('DOMContentLoaded', () => {
  const html = document.documentElement;
  const $doc = $(document);
  const $win = $(window);

  const wrap = document.getElementById('pageLoadingWrap');
  const hideLoading = () => {
    if (wrap) {
      $('#pageLoadingWrap').stop(true, true).fadeOut(180, function () {
        this.style.display = 'none';
      });
    }
  };

  const SIDEBAR_CACHE_PREFIX = 'w3_sidebar_cache_v1:';
  const SIDEBAR_CACHE_TTL_MS = 1000;
  const PROFILE_CACHE_PREFIX = 'w3_profile_cache_v1:';
  const PROFILE_CACHE_TTL_MS = 5 * 60 * 1000;
  const NOTIFICATION_CACHE_PREFIX = 'w3_notification_cache_v1:';
  const NOTIFICATION_CACHE_TTL_MS = 60 * 1000;

  function getScopedCacheKey(prefix, token) {
    return prefix + (token ? token.slice(-24) : 'guest');
  }

  function getSidebarCacheKey(token) {
    return getScopedCacheKey(SIDEBAR_CACHE_PREFIX, token);
  }

  function readSidebarCache(token) {
    try {
      const raw = sessionStorage.getItem(getSidebarCacheKey(token));
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      return null;
    }
  }

  function writeSidebarCache(token, data) {
    try {
      sessionStorage.setItem(getSidebarCacheKey(token), JSON.stringify({
        saved_at: Date.now(),
        payload: data
      }));
    } catch (e) {}
  }

  function getSidebarPayload(cacheEntry) {
    if (!cacheEntry) return null;
    if (Object.prototype.hasOwnProperty.call(cacheEntry, 'payload')) {
      return cacheEntry.payload;
    }
    return cacheEntry;
  }

  function getSidebarCacheAge(cacheEntry) {
    if (!cacheEntry || typeof cacheEntry.saved_at !== 'number') return Number.POSITIVE_INFINITY;
    return Math.max(0, Date.now() - cacheEntry.saved_at);
  }

  function readScopedCache(prefix, token) {
    try {
      const raw = sessionStorage.getItem(getScopedCacheKey(prefix, token));
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      return null;
    }
  }

  function writeScopedCache(prefix, token, data) {
    try {
      sessionStorage.setItem(getScopedCacheKey(prefix, token), JSON.stringify({
        saved_at: Date.now(),
        payload: data
      }));
    } catch (e) {}
  }

  function getCachePayload(cacheEntry) {
    if (!cacheEntry) return null;
    return Object.prototype.hasOwnProperty.call(cacheEntry, 'payload') ? cacheEntry.payload : cacheEntry;
  }

  function getCacheAge(cacheEntry) {
    if (!cacheEntry || typeof cacheEntry.saved_at !== 'number') return Number.POSITIVE_INFINITY;
    return Math.max(0, Date.now() - cacheEntry.saved_at);
  }

  function renderSidebarFromPayload(data) {
    if (data === 'all' || data?.tree === 'all') {
      if (adminFullMenu) adminFullMenu.style.display = '';
      if (dynamicMenuWrap) dynamicMenuWrap.style.display = 'none';
      if (adminFullMenu) {
        bindSubmenuToggles(adminFullMenu);
        restoreOpenMenus(adminFullMenu);
      }
      return true;
    }

    const tree = Array.isArray(data?.tree) ? data.tree : [];
    if (tree.length) {
      if (adminFullMenu) adminFullMenu.style.display = 'none';
      renderDynamicGroupedTree(tree);
    } else {
      if (adminFullMenu) adminFullMenu.style.display = 'none';
      if (dynamicMenuWrap) {
        dynamicMenuWrap.innerHTML = '';
        dynamicMenuWrap.style.display = 'none';
      }
    }

    return true;
  }

  const THEME_KEY = 'theme';
  const btnTheme = document.getElementById('btnTheme');
  const themeIcon = document.getElementById('themeIcon');

  function setTheme(mode){
    const isDark = mode === 'dark';
    html.classList.toggle('theme-dark', isDark);
    localStorage.setItem(THEME_KEY, mode);
    if (themeIcon) themeIcon.className = isDark ? 'fa-regular fa-sun' : 'fa-regular fa-moon';
  }

  setTheme(
    localStorage.getItem(THEME_KEY) ||
    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
  );

  btnTheme?.addEventListener('click', () =>
    setTheme(html.classList.contains('theme-dark') ? 'light' : 'dark')
  );

  const btnFullscreen = document.getElementById('btnFullscreen');
  const fullscreenIcon = document.getElementById('fullscreenIcon');

  function syncFullscreenIcon(){
    const isFs = !!document.fullscreenElement;
    if (fullscreenIcon) {
      fullscreenIcon.className = isFs ? 'fa-solid fa-compress' : 'fa-solid fa-expand';
    }
    if (btnFullscreen) {
      btnFullscreen.setAttribute('title', isFs ? 'Exit Fullscreen' : 'Fullscreen');
      btnFullscreen.setAttribute('aria-label', isFs ? 'Exit fullscreen' : 'Enter fullscreen');
    }
  }

  btnFullscreen?.addEventListener('click', async () => {
    try{
      if (!document.fullscreenElement) {
        await document.documentElement.requestFullscreen();
      } else {
        await document.exitFullscreen();
      }
    }catch(e){}
  });

  document.addEventListener('fullscreenchange', syncFullscreenIcon);
  syncFullscreenIcon();

  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const btnHamburger = document.getElementById('btnHamburger');

  const openSidebar = () => {
    sidebar.classList.add('w3-on');
    overlay.classList.add('w3-on');
    btnHamburger?.classList.add('is-active');
    btnHamburger?.setAttribute('aria-expanded','true');
    btnHamburger?.setAttribute('aria-label','Close menu');
    $('body').css('overflow', 'hidden');
  };

  const closeSidebar = () => {
    sidebar.classList.remove('w3-on');
    overlay.classList.remove('w3-on');
    btnHamburger?.classList.remove('is-active');
    btnHamburger?.setAttribute('aria-expanded','false');
    btnHamburger?.setAttribute('aria-label','Open menu');
    $('body').css('overflow', '');
  };

  btnHamburger?.addEventListener('click', () =>
    sidebar.classList.contains('w3-on') ? closeSidebar() : openSidebar()
  );

  overlay?.addEventListener('click', closeSidebar);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSidebar();
  });

  function syncAppbarShadow(){
    $('#w3Appbar').toggleClass('w3-appbar-scrolled', $win.scrollTop() > 4);
  }
  $win.on('scroll', syncAppbarShadow);
  syncAppbarShadow();

  const profileCircleImage = document.getElementById('profileCircleImage');
  const profileCircleLetter = document.getElementById('profileCircleLetter');

  function setHeaderProfile(imageUrl, letter){
    const img = (imageUrl || '').trim();
    const fallback = (letter || '').trim().charAt(0).toUpperCase();

    if (img) {
      profileCircleImage.src = img;
      profileCircleImage.style.display = 'block';
      profileCircleLetter.style.display = 'none';
      profileCircleImage.onerror = function () {
        profileCircleImage.style.display = 'none';
        profileCircleLetter.style.display = 'flex';
        profileCircleLetter.innerHTML = fallback || '<i class="fa-regular fa-user"></i>';
        profileCircleImage.onerror = null;
      };
      return;
    }

    profileCircleImage.removeAttribute('src');
    profileCircleImage.style.display = 'none';
    profileCircleLetter.style.display = 'flex';
    profileCircleLetter.innerHTML = fallback || '<i class="fa-regular fa-user"></i>';
  }

  function renderProfileMiniFromPayload(payload){
    const block = payload?.user || payload?.data || payload || {};
    setHeaderProfile(block.image || '', block.avatar_text || '');
  }

  const roleFromStorage = sessionStorage.getItem('role') || localStorage.getItem('role');

  const API_SIDEBAR = '/api/my/sidebar-menus';
  const API_LOGOUT = '/api/auth/logout';
  const API_PROFILE_MINI = '/api/auth/profile';
  const API_NOTIFICATION_COUNT = '/api/notifications/unread-count';
  const API_NOTIFICATION_DRAWER = '/api/notifications/drawer';
  const API_NOTIFICATION_READ_ALL = '/api/notifications/read-all';
  const LOGIN_PAGE = '/';
  const OPEN_MENU_KEY = 'msit_open_sidebar_menus';

  const adminFullMenu   = document.getElementById('adminFullMenu');
  const alertsMenu = document.getElementById('alertsMenu');
  const dynamicMenuWrap = document.getElementById('dynamicMenuWrap');
  const mainContentWrap = document.getElementById('mainContentWrap');
  const notificationBadge = document.getElementById('notificationBadge');
  const notificationDrawer = document.getElementById('notificationDrawer');
  const notificationDrawerList = document.getElementById('notificationDrawerList');
  const drawerReadAllBtn = document.getElementById('drawerReadAllBtn');

  let sessionExpiredShown = false;

  function getBearerToken(){
    return sessionStorage.getItem('token') || localStorage.getItem('token') || null;
  }

  function clearAuthStorage(){
    try { sessionStorage.removeItem('token'); } catch(e){}
    try { sessionStorage.removeItem('role'); } catch(e){}
    try { localStorage.removeItem('token'); } catch(e){}
    try { localStorage.removeItem('role'); } catch(e){}
  }

  function disableContentSection(){
    if (mainContentWrap) mainContentWrap.classList.add('w3-disabled-content');
  }

  function notificationHeaders(extra = {}){
    const token = getBearerToken();
    return Object.assign({
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }, extra);
  }

  function escapeHtml(value){
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function formatNotificationTime(value){
    if (!value) return 'Just now';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleString([], {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  function setNotificationBadge(count){
    const safeCount = Math.max(0, Number(count || 0));
    if (!notificationBadge) return;
    notificationBadge.textContent = safeCount > 99 ? '99+' : safeCount;
    notificationBadge.classList.toggle('show', safeCount > 0);
  }

  function setButtonLoadingState(button, loading, loadingLabel){
    if (!button) return;
    if (loading) {
      button.classList.add('btn-loading');
      button.disabled = true;
      if (loadingLabel) button.setAttribute('aria-label', loadingLabel);
      return;
    }

    button.classList.remove('btn-loading');
    button.disabled = false;
  }

  async function markNotificationRead(id){
    try{
      await fetch(`/api/notifications/${id}/read`, {
        method: 'POST',
        headers: notificationHeaders()
      });
    }catch(e){}

    await loadHeaderNotifications({ forceRefresh: true });
  }

  function bindNotificationDrawerActions(){
    notificationDrawerList?.querySelectorAll('.js-drawer-read-notification').forEach(btn => {
      btn.addEventListener('click', function () {
        markNotificationRead(this.dataset.id);
      });
    });
  }

  function renderNotificationDrawer(items){
    if (!notificationDrawerList) return;

    if (!Array.isArray(items) || !items.length) {
      notificationDrawerList.innerHTML = `
        <div class="w3-notif-empty">
          <i class="fa fa-bell-slash me-2"></i>No notifications available.
        </div>
      `;
      return;
    }

    notificationDrawerList.innerHTML = items.map(item => `
      <div class="w3-notif-item ${item.is_read ? '' : 'is-unread'}">
        <div class="w3-notif-row">
          <div class="w3-notif-title">${escapeHtml(item.title || 'Notification')}</div>
          <div class="w3-notif-time">${escapeHtml(formatNotificationTime(item.created_at))}</div>
        </div>
        <div class="w3-notif-text">${escapeHtml(item.message || '')}</div>
        <div class="w3-notif-meta">
          <span class="w3-notif-pill"><i class="fa fa-tag"></i>${escapeHtml(item.type || 'general')}</span>
          <span class="w3-notif-pill"><i class="fa ${item.is_read ? 'fa-circle-check' : 'fa-circle'}"></i>${item.is_read ? 'Read' : 'Unread'}</span>
        </div>
        <div class="w3-notif-actions">
          ${!item.is_read ? `
            <button type="button" class="btn btn-light btn-sm js-drawer-read-notification" data-id="${item.id}">
              <i class="fa fa-check me-1"></i>Read
            </button>
          ` : ''}
        </div>
      </div>
    `).join('');

    bindNotificationDrawerActions();
  }

  function renderNotificationSnapshot(snapshot){
    if (!snapshot) return;
    setNotificationBadge(snapshot.unread_count || 0);
    renderNotificationDrawer(Array.isArray(snapshot.items) ? snapshot.items : []);
  }

  async function loadHeaderNotifications(options = {}){
    if (!getBearerToken()) {
      setNotificationBadge(0);
      renderNotificationDrawer([]);
      return;
    }

    try{
      const [countResponse, drawerResponse] = await Promise.all([
        fetch(API_NOTIFICATION_COUNT, { headers: notificationHeaders() }),
        fetch(API_NOTIFICATION_DRAWER, { headers: notificationHeaders() })
      ]);

      const countResult = await countResponse.json().catch(() => ({}));
      const drawerResult = await drawerResponse.json().catch(() => ({}));

      if (!countResponse.ok || !drawerResponse.ok) {
        throw new Error(drawerResult?.message || countResult?.message || 'Unable to load notifications.');
      }

      renderNotificationSnapshot({
        unread_count: countResult.unread_count ?? drawerResult.unread_count ?? 0,
        items: drawerResult.items || []
      });
    }catch(e){
      setNotificationBadge(0);
      renderNotificationDrawer([]);
    }
  }

  window.loadHeaderNotifications = loadHeaderNotifications;

  drawerReadAllBtn?.addEventListener('click', async function () {
    setButtonLoadingState(drawerReadAllBtn, true, 'Reading all notifications');
    try{
      const response = await fetch(API_NOTIFICATION_READ_ALL, {
        method: 'POST',
        headers: notificationHeaders()
      });
      const result = await response.json().catch(() => ({}));

      if (!response.ok || !result?.success) {
        throw new Error(result?.message || 'Unable to mark all notifications as read.');
      }

      await loadHeaderNotifications({ forceRefresh: true });
    }catch(e){
      if (window.Swal) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: e.message || 'Unable to mark all notifications as read.'
        });
      }
    } finally {
      setButtonLoadingState(drawerReadAllBtn, false);
    }
  });

  notificationDrawer?.addEventListener('show.bs.offcanvas', () => {
    loadHeaderNotifications();
  });

  async function showSessionExpiredPopup(message = 'Your session has expired. Please login again.'){
    if (sessionExpiredShown) return;
    sessionExpiredShown = true;

    hideLoading();

    disableContentSection();
    closeSidebar();
    clearAuthStorage();

    await Swal.fire({
      icon: 'warning',
      title: 'Session expired',
      text: message,
      confirmButtonText: 'Login again',
      allowOutsideClick: false,
      allowEscapeKey: false,
      confirmButtonColor: '#0f766e'
    });

    window.location.replace(LOGIN_PAGE);
  }

  async function loadMiniProfile(options = {}){
    const token = getBearerToken();
    if (!token) {
      setHeaderProfile('', '');
      return;
    }

    const forceRefresh = !!options.forceRefresh;
    const skipCachedRender = !!options.skipCachedRender;
    const cached = readScopedCache(PROFILE_CACHE_PREFIX, token);
    const cachedPayload = getCachePayload(cached);

    if (cachedPayload && !skipCachedRender) {
      renderProfileMiniFromPayload(cachedPayload);
    }

    if (cachedPayload && !forceRefresh && getCacheAge(cached) <= PROFILE_CACHE_TTL_MS) {
      return;
    }

    try{
      const res = await fetch(API_PROFILE_MINI, {
        method: 'GET',
        headers: {
          'Authorization': 'Bearer ' + token,
          'Accept': 'application/json'
        }
      });

      if (res.status === 401 || res.status === 403) {
        setHeaderProfile('', '');
        return;
      }

      const data = await res.json().catch(() => ({}));
      writeScopedCache(PROFILE_CACHE_PREFIX, token, data);
      renderProfileMiniFromPayload(data);
    }catch(e){
      if (!cachedPayload) {
        setHeaderProfile('', '');
      }
    }
  }

  function getOpenMenuIds(){
    try {
      return JSON.parse(localStorage.getItem(OPEN_MENU_KEY) || '[]');
    } catch(e){
      return [];
    }
  }

  function saveOpenMenuIds(ids){
    localStorage.setItem(OPEN_MENU_KEY, JSON.stringify(ids));
  }

  function syncOpenMenuStorage(){
    const openIds = [];
    $('.w3-toggle.w3-open').each(function(){
      const id = $(this).data('target');
      if (id) openIds.push(id);
    });
    saveOpenMenuIds(openIds);
  }

  function openSubmenu($toggle, instant = false){
    const id = $toggle.data('target');
    const $submenu = $('#' + id);

    if (!$submenu.length) return;

    $toggle.addClass('w3-open').attr('aria-expanded', 'true');
    $submenu.addClass('w3-open');

    if (instant) {
      $submenu.show();
    } else {
      $submenu.stop(true, true).slideDown(170);
    }
  }

  function closeSubmenu($toggle, instant = false){
    const id = $toggle.data('target');
    const $submenu = $('#' + id);

    if (!$submenu.length) return;

    $toggle.removeClass('w3-open').attr('aria-expanded', 'false');
    $submenu.removeClass('w3-open');

    if (instant) {
      $submenu.hide();
    } else {
      $submenu.stop(true, true).slideUp(170);
    }
  }

  function restoreOpenMenus(root = document){
    const openIds = getOpenMenuIds();
    openIds.forEach((id) => {
      const $toggle = $(root).find(`.w3-toggle[data-target="${id}"]`).first();
      if ($toggle.length) openSubmenu($toggle, true);
    });
  }

  function bindSubmenuToggles(root = document){
    $(root).find('.w3-toggle').each(function(){
      const $tg = $(this);
      if ($tg.data('bound')) return;
      $tg.data('bound', true);

      $tg.on('click', function(e){
        e.preventDefault();

        if ($tg.hasClass('w3-open')) {
          closeSubmenu($tg);
        } else {
          openSubmenu($tg);
        }

        syncOpenMenuStorage();
      });
    });
  }

  function markActiveLinks(){
    const path = window.location.pathname.replace(/\/+$/, '') || '/';
    let firstVisibleActive = null;

    document.querySelectorAll('.w3-menu a[href]').forEach(a => {
      const href = a.getAttribute('href');
      if (!href || href === '#') return;

      const normalizedHref = href.replace(/\/+$/, '') || '/';
      if (normalizedHref === path){
        a.classList.add('active');

        const sub = a.closest('.w3-submenu');
        if (sub){
          const $sub = $(sub);
          const $toggle = $sub.prev('.w3-toggle');
          if ($toggle.length) {
            openSubmenu($toggle, true);
          }
        }

        const isVisible = a.offsetParent !== null && getComputedStyle(a).visibility !== 'hidden';
        if (!firstVisibleActive && isVisible) firstVisibleActive = a;
      }
    });

    syncOpenMenuStorage();
    return firstVisibleActive;
  }

  function focusActiveNavIntoView(activeEl){
    if (!activeEl) return;

    const scroller = document.querySelector('.w3-sidebar-scroll');
    if (!scroller) return;

    setTimeout(() => {
      try{
        const sRect = scroller.getBoundingClientRect();
        const aRect = activeEl.getBoundingClientRect();
        const fullyVisible = aRect.top >= sRect.top + 8 && aRect.bottom <= sRect.bottom - 8;

        if (!fullyVisible) {
          const currentTop = scroller.scrollTop;
          const deltaTop = aRect.top - sRect.top;
          const targetTop = currentTop + deltaTop - (sRect.height / 2) + (aRect.height / 2);

          $(scroller).stop(true).animate(
            { scrollTop: Math.max(0, targetTop - 8) },
            260
          );
        }

        $(activeEl).addClass('w3-focus-flash');
        setTimeout(() => $(activeEl).removeClass('w3-focus-flash'), 1200);
      }catch(e){}
    }, 180);
  }

  function safeText(v){ return (v ?? '').toString(); }

  function iconHtml(iconClass, fallback='fa-solid fa-circle'){
    const cls = safeText(iconClass).trim();
    return `<i class="${cls || fallback}"></i>`;
  }

  function normalizePath(href){
    const raw = safeText(href).trim();
    if (!raw) return '';
    try{
      const u = new URL(raw, window.location.origin);
      return (u.pathname || '/').replace(/\/+$/, '') || '/';
    }catch(e){
      return ('/' + raw.replace(/^\/+/, '')).replace(/\/+$/, '') || '/';
    }
  }

  const DEFAULT_ROUTE_META = {
    '/user/manage':            { section:'WORKFORCE',      header:'Workforce',      page:'Manage Users',             icon:'fa-solid fa-user-group',       direct:true  },
    '/users/manage':           { section:'WORKFORCE',      header:'Workforce',      page:'Manage Users',             icon:'fa-solid fa-user-group',       direct:true  },
    '/profile':                { section:'SELF SERVICE',   header:'Self Service',   page:'Profile',                  icon:'fa-solid fa-user',             direct:true  },
    '/dashboard-menu/create':  { section:'ACCESS CONTROL', header:'Dashboard Menu', page:'Create Dashboard Menu',    icon:'fa-solid fa-puzzle-piece',     direct:false },
    '/dashboard-menu/manage':  { section:'ACCESS CONTROL', header:'Dashboard Menu', page:'Manage Dashboard Menu',    icon:'fa-solid fa-puzzle-piece',     direct:false },
    '/page-privilege/create':  { section:'ACCESS CONTROL', header:'Page Privilege', page:'Create Page Privilege',    icon:'fa-solid fa-key',              direct:false },
    '/page-privilege/manage':  { section:'ACCESS CONTROL', header:'Page Privilege', page:'Manage Page Privilege',    icon:'fa-solid fa-lock',             direct:false },
    '/role-privileges/manage': { section:'ACCESS CONTROL', header:'Role Privilege', page:'Assign Role Privilege',    icon:'fa-solid fa-user-shield',      direct:true  },
    '/user-privileges/manage': { section:'ACCESS CONTROL', header:'User Privilege', page:'Assign User Privilege',    icon:'fa-solid fa-user-lock',        direct:true  },
    '/activity-logs':          { section:'OPERATIONS',     header:'Operations',     page:'Activity Logs',            icon:'fa-solid fa-clock-rotate-left',direct:true  },
    '/notifications':          { section:'OPERATIONS',     header:'Operations',     page:'Notifications',            icon:'fa-solid fa-bell',             direct:true  },
  };

  const SECTION_ICONS = {
    'WORKFORCE':      'fa-solid fa-users-gear',
    'SELF SERVICE':   'fa-solid fa-user',
    'ACCESS CONTROL': 'fa-solid fa-shield-halved',
    'OPERATIONS':     'fa-solid fa-gear',
    'OTHER':          'fa-solid fa-folder'
  };

  const SECTION_ORDER = ['WORKFORCE','SELF SERVICE','ACCESS CONTROL','OPERATIONS','OTHER'];

  function inferSectionFromName(name){
    const n = safeText(name).toLowerCase();
    if (!n) return 'OTHER';
    if (/(user|employee|staff|workforce|team|people|hr)/.test(n)) return 'WORKFORCE';
    if (/(profile|self service|account)/.test(n)) return 'SELF SERVICE';
    if (/(privilege|dashboard menu|role privilege|user privilege|access)/.test(n)) return 'ACCESS CONTROL';
    if (/(notification|log|setting|operation|config)/.test(n)) return 'OPERATIONS';
    return 'OTHER';
  }

  function getMatchedMeta(page, header){
    const path = normalizePath(page?.href || '');
    const matched = DEFAULT_ROUTE_META[path] || null;

    if (matched) {
      return {
        section: matched.section,
        headerName: matched.header,
        pageName: matched.page,
        icon: safeText(page?.icon_class || header?.icon_class || matched.icon || ''),
        direct: !!matched.direct,
        href: path || safeText(page?.href || ''),
      };
    }

    return {
      section: inferSectionFromName(header?.name || page?.name || ''),
      headerName: safeText(header?.name || 'Menu'),
      pageName: safeText(page?.name || 'Page'),
      icon: safeText(page?.icon_class || header?.icon_class || 'fa-solid fa-folder'),
      direct: false,
      href: safeText(page?.href || '#'),
    };
  }

  function renderDynamicGroupedTree(tree){
    if (!dynamicMenuWrap) return;
    dynamicMenuWrap.innerHTML = '';

    const buckets = {};

    (tree || []).forEach((header) => {
      const pages = Array.isArray(header?.children) ? header.children : [];
      pages.forEach((page) => {
        const meta = getMatchedMeta(page, header);
        const section = meta.section || 'OTHER';
        const headerKey = `${section}__${meta.headerName}`;

        if (!buckets[section]) buckets[section] = {};
        if (!buckets[section][headerKey]) {
          buckets[section][headerKey] = {
            headerName: meta.headerName || 'Menu',
            headerIcon: meta.icon || 'fa-solid fa-folder',
            direct: !!meta.direct,
            children: []
          };
        }

        const pageHref = safeText(meta.href || '#');
        const exists = buckets[section][headerKey].children.some(x =>
          normalizePath(x.href) === normalizePath(pageHref) && safeText(x.name) === safeText(meta.pageName)
        );

        if (!exists) {
          buckets[section][headerKey].children.push({
            href: pageHref || '#',
            name: meta.pageName || 'Page'
          });
        }

        if (!buckets[section][headerKey].headerIcon) {
          buckets[section][headerKey].headerIcon = meta.icon || 'fa-solid fa-folder';
        }
      });
    });

    SECTION_ORDER.forEach((sectionName) => {
      const headersMap = buckets[sectionName];
      if (!headersMap || !Object.keys(headersMap).length) return;

      const section = document.createElement('div');
      section.innerHTML = `
        <div class="w3-nav-section">
          <div class="w3-section-title">
            ${iconHtml(SECTION_ICONS[sectionName] || 'fa-solid fa-folder')}
            <span>${sectionName}</span>
          </div>
          <div class="w3-section-rule"></div>
        </div>
        <nav class="w3-menu" aria-label="${sectionName}"></nav>
      `;

      const nav = section.querySelector('.w3-menu');

      Object.values(headersMap).forEach((item, idx) => {
        const children = Array.isArray(item.children) ? item.children : [];
        if (!children.length) return;

        if (item.direct && children.length === 1) {
          const a = document.createElement('a');
          a.className = 'w3-link';
          a.href = safeText(children[0].href || '#');
          a.innerHTML = `${iconHtml(item.headerIcon || 'fa-solid fa-folder')}<span>${safeText(children[0].name || item.headerName || 'Page')}</span>`;
          nav.appendChild(a);
          return;
        }

        const subId = `dyn-sub-${sectionName.toLowerCase()}-${idx}-${Math.random().toString(36).slice(2,7)}`;
        const group = document.createElement('div');
        group.className = 'w3-group';
        group.innerHTML = `
          <a href="#" class="w3-link w3-toggle" data-target="${subId}" aria-expanded="false">
            ${iconHtml(item.headerIcon || 'fa-solid fa-folder')}
            <span>${safeText(item.headerName || 'Menu')}</span>
            <i class="fa fa-chevron-down w3-chev"></i>
          </a>
          <div id="${subId}" class="w3-submenu" role="group" aria-label="${safeText(item.headerName || 'Menu')} submenu"></div>
        `;

        const sub = group.querySelector(`#${subId}`);
        children.forEach((child) => {
          const a = document.createElement('a');
          a.className = 'w3-link';
          a.href = safeText(child.href || '#');
          a.innerHTML = `<span>${safeText(child.name || 'Page')}</span>`;
          sub.appendChild(a);
        });

        nav.appendChild(group);
      });

      if (nav.children.length) dynamicMenuWrap.appendChild(section);
    });

    dynamicMenuWrap.style.display = dynamicMenuWrap.children.length ? '' : 'none';
    bindSubmenuToggles(dynamicMenuWrap);
    restoreOpenMenus(dynamicMenuWrap);
  }

  async function loadSidebarByToken(options = {}){
    const token = getBearerToken();
    const skipCachedRender = !!options.skipCachedRender;
    const forceRefresh = !!options.forceRefresh;

    if (!token) {
      if (adminFullMenu) adminFullMenu.style.display = 'none';
      if (dynamicMenuWrap) dynamicMenuWrap.style.display = 'none';
      await showSessionExpiredPopup();
      return false;
    }

    const cachedSidebar = readSidebarCache(token);
    const cachedPayload = getSidebarPayload(cachedSidebar);

    if (cachedPayload && !skipCachedRender) {
      renderSidebarFromPayload(cachedPayload);
    }

    if (cachedPayload && !forceRefresh && getSidebarCacheAge(cachedSidebar) <= SIDEBAR_CACHE_TTL_MS) {
      return true;
    }

    try{
      const res = await fetch(API_SIDEBAR, {
        method: 'GET',
        headers: {
          'Authorization': 'Bearer ' + token,
          'Accept': 'application/json'
        }
      });

      let data = null;
      try {
        data = await res.json();
      } catch (e) {
        data = null;
      }

      if (!res.ok || data?.session_expired === true) {
        if (adminFullMenu) adminFullMenu.style.display = 'none';
        if (dynamicMenuWrap) dynamicMenuWrap.style.display = 'none';
        await showSessionExpiredPopup(data?.message || 'Your session has expired. Please login again.');
        return false;
      }

      writeSidebarCache(token, data);
      return renderSidebarFromPayload(data);

    }catch(e){
      if (cachedPayload) {
        return true;
      }
      if (adminFullMenu) adminFullMenu.style.display = 'none';
      if (dynamicMenuWrap) dynamicMenuWrap.style.display = 'none';
      return false;
    }
  }

  async function performLogout(){
    const token = getBearerToken();

    const confirm = await Swal.fire({
      title: 'Log out?',
      text: 'You will be signed out of Attendance System.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, logout',
      cancelButtonText: 'Cancel',
      focusCancel: true,
      confirmButtonColor: '#0f766e'
    });

    if (!confirm.isConfirmed) return;

    let ok = false;
    if (token){
      try{
        const res = await fetch(API_LOGOUT, {
          method: 'POST',
          headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
          },
          body: ''
        });
        ok = res.ok;
      }catch(e){ ok = false; }
    }

    clearAuthStorage();

    await Swal.fire({
      title: ok ? 'Logged out' : 'Signed out locally',
      text: ok ? 'See you soon 👋' : 'Your session was cleared on this device.',
      icon: ok ? 'success' : 'info',
      timer: 1200,
      showConfirmButton: false
    });

    window.location.replace(LOGIN_PAGE);
  }

  document.getElementById('logoutBtnSidebar')?.addEventListener('click', (e) => { e.preventDefault(); performLogout(); });

  $doc.on('click', '.w3-menu a[href]:not(.w3-toggle)', function(){
    if (window.innerWidth < 992) {
      closeSidebar();
    }
  });

  (async () => {
    let activeNavEl = null;

    try{
      if (getBearerToken()) {
        alertsMenu?.classList.remove('d-none');
        notificationDrawer?.classList.remove('d-none');
      }

      bindSubmenuToggles(document);
      restoreOpenMenus(document);

      const bootToken = getBearerToken();
      const bootCachedSidebar = readSidebarCache(bootToken);
      const bootCachedPayload = getSidebarPayload(bootCachedSidebar);

      if (bootCachedPayload) {
        renderSidebarFromPayload(bootCachedPayload);
        activeNavEl = markActiveLinks();
      }

      const [loaded] = await Promise.all([
        loadSidebarByToken({ skipCachedRender: true }),
        loadMiniProfile(),
        loadHeaderNotifications()
      ]);
      if (loaded && !sessionExpiredShown) {
        activeNavEl = markActiveLinks();
      }
    } finally {
      if (!sessionExpiredShown) {
        hideLoading();
      }
    }
  })();

  setInterval(() => {
    if (!sessionExpiredShown && getBearerToken()) {
      loadHeaderNotifications();
    }
  }, 60000);

  // ── Scroll-hint for wide tables ──
  function applyTableScrollHints(root) {
    (root || document).querySelectorAll('.table-responsive').forEach(function(wrap) {
      function check() {
        wrap.classList.toggle('has-scroll-hint', wrap.scrollWidth > wrap.clientWidth && wrap.scrollLeft < (wrap.scrollWidth - wrap.clientWidth - 4));
      }
      check();
      wrap.addEventListener('scroll', check, { passive: true });
      new ResizeObserver(check).observe(wrap);
    });
  }

  applyTableScrollHints(document);

  // Re-run after dynamic content renders (e.g. sidebar menu done → page JS adds table rows)
  setTimeout(function() { applyTableScrollHints(document); }, 800);
});
</script>

</body>
</html>
