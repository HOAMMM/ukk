<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Kasir - {{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/icon/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sweetalert/sweetalert2.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

    <style>
        :root {
            --primary: #ea580c;
            --primary-dark: #c2410c;
            --primary-light: #fb923c;
            --sidebar-width: 280px;
            --header-height: 70px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            box-shadow: var(--shadow-lg);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            opacity: 0.1;
            pointer-events: none;
        }

        /* Sidebar Header */
        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
            position: relative;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #fff;
            text-decoration: none;
            transition: var(--transition);
        }

        .brand-logo:hover {
            transform: translateX(4px);
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        }

        .brand-text h5 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .brand-text small {
            font-size: 11px;
            color: var(--primary-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .btn-close-sidebar {
            position: absolute;
            top: 24px;
            right: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-close-sidebar:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 16px 12px 24px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .sidebar-section-header {
            font-size: 11px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 20px 12px 8px;
            margin-top: 8px;
        }

        .sidebar-section-header:first-child {
            margin-top: 0;
            padding-top: 8px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 16px;
            margin: 2px 0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary);
            transform: scaleY(0);
            transition: var(--transition);
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transform: translateX(4px);
            text-decoration: none;
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(234, 88, 12, 0.2), rgba(234, 88, 12, 0.05));
            color: #fff;
            font-weight: 600;
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i {
            width: 20px;
            font-size: 16px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link.active i {
            color: var(--primary-light);
        }

        /* User Box */
        .user-box {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            transition: var(--transition);
        }

        .user-info:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 2px solid var(--primary);
            flex-shrink: 0;
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 12px;
            color: var(--primary-light);
            margin: 0;
        }

        .btn-logout {
            background: rgba(220, 38, 38, 0.15);
            color: #fca5a5;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .btn-logout:hover {
            background: rgba(220, 38, 38, 0.25);
            color: #ef4444;
        }

        /* ========== HEADER ========== */
        .header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: #fff;
            box-shadow: var(--shadow-sm);
            z-index: 1040;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            transition: var(--transition);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-menu {
            display: none;
            background: #f1f5f9;
            border: none;
            border-radius: 10px;
            color: #334155;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-menu:hover {
            background: #e2e8f0;
        }

        .header-title h4 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-title small {
            font-size: 13px;
            color: #64748b;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .date-time {
            text-align: right;
            padding: 8px 16px;
            background: #f8fafc;
            border-radius: 10px;
        }

        .date-time-date {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .date-time-clock {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            font-variant-numeric: tabular-nums;
        }

        .btn-notification {
            position: relative;
            background: #f1f5f9;
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            color: #334155;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-notification:hover {
            background: #e2e8f0;
        }

        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid #fff;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* ========== CONTENT ========== */
        .content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            transition: var(--transition);
            min-height: calc(100vh - var(--header-height));
        }

        .main-content {
            padding: 28px;
        }

        /* ========== OVERLAY ========== */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1045;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* ========== RESPONSIVE ========== */
        /* Tablet */
        @media (max-width: 991.98px) {
            :root {
                --sidebar-width: 260px;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .btn-close-sidebar {
                display: flex;
            }

            .header {
                left: 0;
            }

            .btn-menu {
                display: flex;
            }

            .content {
                margin-left: 0;
            }

            .main-content {
                padding: 20px;
            }
        }

        /* Mobile */
        @media (max-width: 767.98px) {
            :root {
                --header-height: 64px;
            }

            .header {
                padding: 0 16px;
            }

            .header-title h4 {
                font-size: 18px;
            }

            .header-title small {
                font-size: 11px;
            }

            .date-time {
                display: none;
            }

            .main-content {
                padding: 16px;
            }

            .user-box {
                padding: 16px;
            }
        }

        /* Small Mobile */
        @media (max-width: 575.98px) {
            :root {
                --sidebar-width: 100%;
            }

            .sidebar-section-header {
                font-size: 10px;
            }

            .nav-link {
                font-size: 13px;
            }
        }

        /* Landscape Mobile */
        @media (max-height: 600px) and (orientation: landscape) {
            .sidebar-header {
                padding: 16px 20px;
            }

            .brand-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }

            .brand-text h5 {
                font-size: 16px;
            }

            .user-box {
                padding: 12px 20px;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">

        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <a href="/dashboard" class="brand-logo">
                <div class="brand-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="brand-text">
                    <h5>Restaurant POS</h5>
                    <small>Point of Sale</small>
                </div>
            </a>
            <button class="btn-close-sidebar" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        @php
        $user = Auth::user();
        $level = $user->id_level ?? 1;

        if ($level == 1) {
        $roleName = 'Administrator';
        $roleColor = 'text-danger';
        $avatarBg = 'dc2626';
        $roleIcon = 'fa-user-shield';
        } elseif ($level == 2) {
        $roleName = 'Waiter';
        $roleColor = 'text-warning';
        $avatarBg = 'f59e0b';
        $roleIcon = 'fa-concierge-bell';
        } elseif ($level == 3) {
        $roleName = 'Kasir';
        $roleColor = 'text-success';
        $avatarBg = '16a34a';
        $roleIcon = 'fa-cash-register';
        } else {
        $roleName = 'Owner';
        $roleColor = 'text-secondary';
        $avatarBg = '6b7280';
        $roleIcon = 'fa-user';
        }
        @endphp

        <!-- Navigation Menu -->
        <nav class="sidebar-nav">

            {{-- DASHBOARD --}}
            <div class="sidebar-section-header">Dashboard</div>
            <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            {{-- ADMIN MENU --}}
            @if ($level == 1)
            <div class="sidebar-section-header">Data</div>
            <a href="/dashboard/staff-kasir" class="nav-link {{ request()->is('dashboard/staff-kasir*') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i>
                <span>Staff Kasir</span>
            </a>
            <a href="/dashboard/staff-waiter" class="nav-link {{ request()->is('dashboard/staff-waiter*') ? 'active' : '' }}">
                <i class="fas fa-concierge-bell"></i>
                <span>Staff Waiter</span>
            </a>
            <a href="/dashboard/meja" class="nav-link {{ request()->is('dashboard/meja*') ? 'active' : '' }}">
                <i class="fas fa-table"></i>
                <span>Meja</span>
            </a>

            <div class="sidebar-section-header">Laporan</div>
            <a href="/dashboard/laporan" class="nav-link {{ request()->is('dashboard/laporan*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>Laporan</span>
            </a>
            <a href="/dashboard/transaksi" class="nav-link {{ request()->is('dashboard/transaksi*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i>
                <span>Transaksi</span>
            </a>

            <div class="sidebar-section-header">Produk</div>
            <a href="/dashboard/menu" class="nav-link {{ request()->is('dashboard/menu*') ? 'active' : '' }}">
                <i class="fas fa-hamburger"></i>
                <span>Menu</span>
            </a>
            <a href="/dashboard/kategori" class="nav-link {{ request()->is('dashboard/kategori-produk*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>
            <div class="sidebar-section-header">Setting</div>
            <a href="/dashboard/pengaturan" class="nav-link {{ request()->is('dashboard/pengaturan*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            @endif

            {{-- Waiter MENU --}}
            @if (in_array($level, [2]))

            <div class="sidebar-section-header">Data</div>
            <a href="/dashboard/meja" class="nav-link {{ request()->is('dashboard/meja*') ? 'active' : '' }}">
                <i class="fas fa-table"></i>
                <span>Meja</span>
            </a>

            <div class="sidebar-section-header">Laporan</div>
            <a href="/dashboard/laporan" class="nav-link {{ request()->is('dashboard/laporan*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>Laporan</span>
            </a>
            <div class="sidebar-section-header">Setting</div>
            <a href="/dashboard/pengaturan" class="nav-link {{ request()->is('dashboard/pengaturan*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            @endif

            {{-- Kasir MENU --}}
            @if ($level == 3)
            <div class="sidebar-section-header">Menu Utama</div>
            <a href="/dashboard/order" class="nav-link {{ request()->is('dashboard/order*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i>
                <span>Order</span>
            </a>
            <a href="/dashboard/transaksi" class="nav-link {{ request()->is('dashboard/transaksi*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i>
                <span>Transaksi</span>
            </a>
            <div class="sidebar-section-header">Laporan</div>
            <a href="/dashboard/laporan" class="nav-link {{ request()->is('dashboard/laporan*') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>Laporan</span>
            </a>

            <div class="sidebar-section-header">Setting</div>
            <a href="/dashboard/pengaturan" class="nav-link {{ request()->is('dashboard/pengaturan*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            @endif

        </nav>

        <!-- User Info -->
        <div class="user-box">
            <div class="user-info justify-content-between w-100 align-items-center">

                <div class="user-details">
                    <div class="user-name" style="font-size:16px;font-weight:700;">
                        {{ $user->user_namleng ?? $user->name }}
                    </div>

                    <div class="user-role {{ $roleColor }}" style="font-size:14px;">
                        <i class="fas {{ $roleIcon }} me-2" style="font-size:18px;"></i>
                        {{ $roleName }}
                    </div>
                </div>

                <button class="btn-logout" id="btnLogout" title="Logout">
                    <i class="fas fa-sign-out-alt" style="font-size:18px;"></i>
                </button>

            </div>
        </div>


        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

    </div>

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <button class="btn-menu" id="menuBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-title">
                <h4>Dashboard</h4>
                <small>Selamat datang di sistem kasir restoran</small>
            </div>
        </div>

        <div class="header-right">
            <div class="date-time">
                <div class="date-time-date">{{ date('l, d F Y') }}</div>
                <div class="date-time-clock" id="clock"></div>
            </div>

            <button class="btn-notification" title="Notifikasi">
                <i class="fas fa-bell"></i>
                <span class="notification-badge"></span>
            </button>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bootstrap/icon/bootstrap-icons.json') }}"></script>
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>

    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('
            success ') }}',
            confirmButtonColor: '#ea580c'
        });
    </script>
    @endif

    @if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: `
            <div style="text-align:center">
                @foreach ($errors->all() as $error)
                    <p style="margin:6px 0;">• {{ $error }}</p>
                @endforeach
            </div>
        `,
            confirmButtonColor: '#ea580c'
        }).then(() => {

            @if(session('modal') === 'edit')
            const modal = document.getElementById('editModal');
            @else
            const modal = document.getElementById('exampleModal');
            @endif

            if (modal) {
                new bootstrap.Modal(modal).show();
            }
        });
    </script>
    @endif


    <script>
        // ========== LOGOUT ==========
        document.getElementById('btnLogout').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Logout',
                text: 'Apakah Anda yakin ingin keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        });

        // ========== CLOCK ==========
        function updateClock() {
            const now = new Date();
            const clockElement = document.getElementById('clock');
            if (clockElement) {
                clockElement.innerText = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                });
            }
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ========== SIDEBAR TOGGLE ==========
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('menuBtn');
        const closeSidebar = document.getElementById('closeSidebar');

        // Open sidebar
        if (menuBtn) {
            menuBtn.addEventListener('click', function() {
                sidebar.classList.add('show');
                sidebarOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }

        // Close sidebar function
        function closeSidebarFunc() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close button
        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarFunc);
        }

        // Overlay click
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebarFunc);
        }

        // Close on nav link click (mobile)
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    closeSidebarFunc();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                closeSidebarFunc();
            }
        });

        // Prevent body scroll when sidebar is open on mobile
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const isOpen = sidebar.classList.contains('show');
                    document.body.style.overflow = isOpen && window.innerWidth < 992 ? 'hidden' : '';
                }
            });
        });
        observer.observe(sidebar, {
            attributes: true
        });
    </script>

    @stack('scripts')

</body>

</html>