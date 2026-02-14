<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Restaurant POS</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0f3460">
    <!-- 👇 TAMBAHKAN 3 META TAG INI -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sweetalert/sweetalert2.min.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding-left: env(safe-area-inset-left);
            padding-right: env(safe-area-inset-right);
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Mobile portrait */
        @media (max-width: 768px) {

            body {
                align-items: stretch;
                padding-top: env(safe-area-inset-top);
            }

            .login-wrapper {
                width: 100%;
                min-height: auto;
                border-radius: 0;
                flex-direction: column;
            }

            /* BRAND → jadi header */
            .login-brand {
                flex: none;
                padding: 32px 20px 28px;
                border-radius: 0 0 28px 28px;
            }

            .login-brand h1 {
                font-size: 22px;
                margin-bottom: 4px;
            }

            .login-brand p {
                font-size: 13px;
                opacity: 0.9;
            }

            /* Stats → swipeable */
            .stats-grid {
                grid-template-columns: repeat(3, minmax(90px, 1fr));
                overflow-x: auto;
                padding-bottom: 8px;
                scrollbar-width: none;
            }

            .stats-grid::-webkit-scrollbar {
                display: none;
            }

            .stat-item {
                min-width: 90px;
            }

            /* FORM */
            .login-form-container {
                padding: 28px 20px 32px;
                background: rgba(15, 15, 30, 0.85);
            }

            .login-form-header h2 {
                font-size: 22px;
            }

            .login-form-header p {
                font-size: 13px;
            }

            .form-control {
                font-size: 16px;
                /* anti zoom iOS */
                padding: 14px 44px 14px 14px;
            }

            .btn-login {
                padding: 15px;
                font-size: 15px;
                border-radius: 14px;
            }

            /* Bottom spacing for iOS home indicator */
            .register-link {
                margin-bottom: 16px;
            }
        }

        /* Extra small (iPhone SE / mini) */
        @media (max-width: 375px) {

            .login-brand {
                padding: 24px 16px;
            }

            .brand-icon {
                width: 52px;
                height: 52px;
                border-radius: 14px;
            }

            .brand-icon svg {
                width: 28px;
                height: 28px;
            }

            .login-form-container {
                padding: 24px 16px 28px;
            }
        }

        /* Animated background pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255, 107, 53, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(76, 175, 80, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(33, 150, 243, 0.05) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-30px) scale(1.1);
            }
        }

        .login-wrapper {
            display: flex;
            max-width: 1000px;
            width: 90%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.12);
            position: relative;
            z-index: 1;
        }


        @media (max-width: 992px) {
            .login-wrapper {
                flex-direction: column;
                width: 95%;
                max-width: 500px;
            }
        }

        @media (max-width: 576px) {
            .login-wrapper {
                width: 95%;
                margin: 10px;
                border-radius: 20px;
            }
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 992px) {
            .login-brand {
                padding: 40px 30px;
            }
        }

        @media (max-width: 576px) {
            .login-brand {
                padding: 30px 20px;
            }
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        @media (max-width: 576px) {
            .brand-icon {
                width: 60px;
                height: 60px;
                margin-bottom: 15px;
            }
        }

        .brand-icon svg {
            width: 45px;
            height: 45px;
            fill: white;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        @media (max-width: 576px) {
            .brand-icon svg {
                width: 32px;
                height: 32px;
            }
        }

        .login-brand h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        @media (max-width: 992px) {
            .login-brand h1 {
                font-size: 28px;
            }
        }

        @media (max-width: 576px) {
            .login-brand h1 {
                font-size: 24px;
                margin-bottom: 6px;
            }
        }

        @media (max-width: 576px) {
            .login-brand p {
                font-size: 14px;
            }
        }

        /* Right side - Login form */
        .login-form-container {
            flex: 1;
            padding: 60px 50px;
            background: rgba(26, 26, 46, 0.6);
            backdrop-filter: blur(10px);
        }

        @media (max-width: 992px) {
            .login-form-container {
                padding: 40px 35px;
            }
        }

        @media (max-width: 576px) {
            .login-form-container {
                padding: 30px 25px;
            }
        }

        .login-form-header {
            margin-bottom: 40px;
        }

        @media (max-width: 576px) {
            .login-form-header {
                margin-bottom: 30px;
            }
        }

        .login-form-header h2 {
            color: white;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        @media (max-width: 992px) {
            .login-form-header h2 {
                font-size: 24px;
            }
        }

        @media (max-width: 576px) {
            .login-form-header h2 {
                font-size: 22px;
                margin-bottom: 6px;
            }
        }

        .login-form-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .login-form-header p {
                font-size: 13px;
            }
        }

        .form-group {
            margin-bottom: 24px;
        }

        @media (max-width: 576px) {
            .form-group {
                margin-bottom: 20px;
            }
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .form-label {
                font-size: 13px;
                margin-bottom: 6px;
            }
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 14px 45px 14px 16px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        @media (max-width: 576px) {
            .form-control {
                padding: 12px 40px 12px 14px;
                font-size: 14px;
                border-radius: 10px;
            }
        }

        .form-control::placeholder {
            color: rgba(164, 155, 155, 0.4);
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b35;
            color: white !important;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            stroke: rgba(255, 255, 255, 0.4);
        }

        @media (max-width: 576px) {
            .input-icon {
                right: 12px;
                width: 18px;
                height: 18px;
            }
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 576px) {
            .btn-login {
                padding: 14px;
                font-size: 15px;
                border-radius: 10px;
                margin-top: 8px;
            }
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(255, 107, 53, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 15px;
        }

        @media (max-width: 576px) {
            .forgot-password {
                margin-top: -12px;
                margin-bottom: 12px;
            }
        }

        .forgot-password a {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s;
        }

        @media (max-width: 576px) {
            .forgot-password a {
                font-size: 12px;
            }
        }

        .forgot-password a:hover {
            color: #ff6b35;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: rgba(255, 255, 255, 0.4);
            font-size: 13px;
        }

        @media (max-width: 576px) {
            .divider {
                margin: 25px 0;
                font-size: 12px;
            }
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider span {
            padding: 0 15px;
        }

        .register-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .register-link {
                font-size: 13px;
            }
        }

        .register-link a {
            color: #ff6b35;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #ff8c42;
        }

        /* Stats section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 992px) {
            .stats-grid {
                margin-top: 30px;
                gap: 12px;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                margin-top: 25px;
                gap: 10px;
            }
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        @media (max-width: 992px) {
            .stat-item {
                padding: 12px;
                border-radius: 10px;
            }
        }

        @media (max-width: 576px) {
            .stat-item {
                padding: 10px 8px;
                border-radius: 8px;
            }
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }

        @media (max-width: 992px) {
            .stat-number {
                font-size: 20px;
            }
        }

        @media (max-width: 576px) {
            .stat-number {
                font-size: 18px;
                margin-bottom: 3px;
            }
        }

        .stat-label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.8);
        }

        @media (max-width: 576px) {
            .stat-label {
                font-size: 10px;
            }
        }

        /* ===== MOBILE DARK (CONSISTENT WITH DESKTOP) ===== */
        @media (max-width: 576px) {

            body {
                background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
                padding: 20px;
            }

            .login-wrapper {
                background: rgba(26, 26, 46, 0.85);
                border-radius: 24px;
                margin: 40px auto;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
                max-width: 420px;
            }

            /* MATIKAN HEADER BIRU MOBILE */
            .mobile-header {
                display: none;
            }

            .login-form-container {
                background: transparent;
                padding: 32px 22px 36px;
            }

            .login-form-header h2 {
                color: #ffffff;
                font-weight: 600;
                text-align: left;
            }

            .login-form-header p {
                color: rgba(255, 255, 255, 0.6);
                text-align: left;
            }

            .form-label {
                color: rgba(255, 255, 255, 0.85);
            }

            .form-control {
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.12);
                color: #fff;
                font-size: 15px;
            }

            .form-control::placeholder {
                color: rgba(255, 255, 255, 0.4);
            }

            .input-icon {
                stroke: rgba(255, 255, 255, 0.45);
            }

            .btn-login {
                background: linear-gradient(135deg, #ff6b35, #ff8c42);
                border-radius: 14px;
                box-shadow: 0 8px 20px rgba(255, 107, 53, 0.35);
            }

            .forgot-password a {
                color: rgba(255, 255, 255, 0.6);
            }

            .register-link {
                color: rgba(255, 255, 255, 0.6);
            }

            .register-link a {
                color: #ff6b35;
            }

            .divider {
                color: rgba(255, 255, 255, 0.35);
            }

            .divider::before,
            .divider::after {
                background: rgba(255, 255, 255, 0.15);
            }
        }

        /* ===== TABLET FIX (iPad / Landscape Phone) ===== */
        @media (min-width: 577px) and (max-width: 1024px) {

            body {
                align-items: flex-start;
                padding: 60px 0;
                /* JARAK ATAS & BAWAH */
            }

            .login-wrapper {
                margin: 0 auto;
                max-width: 420px;
                border-radius: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">

        <!-- Right side - Login form -->
        <div class="login-form-container">
            <div class="login-form-header">
                <h2>Selamat Datang</h2>
                <p>Silakan login untuk melanjutkan ke sistem</p>
            </div>

            <form method="POST" action="/login">
                @csrf

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-wrapper">
                        <input type="text"
                            name="username"
                            class="form-control"
                            placeholder="Masukkan username"
                            value="{{ old('username') }}"
                            autocomplete="off">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            autocomplete="off">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                </div>

                <!-- <div class="forgot-password">
                    <a href="#">Lupa Password?</a>
                </div> -->
                <div class="mt-4">
                    <button type="submit" class="btn-login">Login</button>
                </div>

                <div class="divider">
                </div>
            </form>
        </div>
    </div>
</body>

<script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
<!-- 👇 TAMBAHKAN SCRIPT INI -->
<script>
    // Prevent back button after login
    (function() {
        if (typeof window.history.pushState === 'function') {
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, null, window.location.href);
            };
        }
    })();
</script>

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Login Gagal',
        text: @json(session('error')),
        confirmButtonColor: '#ff6b35',
        background: 'rgba(26, 26, 46, 0.95)',
        color: '#fff'
    });
</script>
@endif

@if (session('logout'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Logout Berhasil',
        text: 'Sampai jumpa 👋',
        confirmButtonColor: '#ff6b35',
        background: 'rgba(26, 26, 46, 0.95)',
        color: '#fff'
    });
</script>
@endif

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        html: `
            <div style="text-align: center;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach ($errors->all() as $error)
                        <li style="margin-bottom: 6px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        `,
        confirmButtonColor: '#ff6b35',
        background: 'rgba(26, 26, 46, 0.95)',
        color: '#fff'
    });
</script>
@endif

</html>