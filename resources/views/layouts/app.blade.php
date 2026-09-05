<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Omkaar Logistics - ERP')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0f3460;   /* Deep Navy */
            --secondary-color: #e94560; /* Crimson Red */
            --bg-color: #f4f6f9;
            --card-bg: #ffffff;
            --text-color: #333333;
            --border-color: #d1d5db;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        input[type="text"], input[type="search"], textarea {
            text-transform: uppercase;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 135px; /* Offset for fixed floating header */
            padding-bottom: 75px; /* Offset for fixed floating footer */
            overflow-x: hidden;
        }

        .fixed-top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            margin: 12px 20px 0 20px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        /* Top Header Bar */
        header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #162447 100%);
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-logo .truck-icon {
            font-size: 32px;
            position: relative;
            animation: drive-uturn 8s linear infinite;
            display: inline-block;
            z-index: -1;
        }

        @keyframes drive-uturn {
            0% {
                transform: translateX(45vw) scaleX(1); /* Starting at middle, facing left */
            }
            40% {
                transform: translateX(0vw) scaleX(1);  /* Arrives at left, still facing left */
            }
            45% {
                transform: translateX(0vw) scaleX(-1); /* U-turn: flips to face right */
            }
            85% {
                transform: translateX(45vw) scaleX(-1); /* Drives back to middle, facing right */
            }
            90% {
                transform: translateX(45vw) scaleX(1);  /* U-turn: flips back to face left */
            }
            100% {
                transform: translateX(45vw) scaleX(1);
            }
        }

        .header-logo h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            z-index: 1;
        }

        .header-logo h1 .subtitle {
            font-size: 10px;
            font-weight: 400;
            color: var(--secondary-color);
            letter-spacing: 2px;
            margin-top: 2px;
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 13px;
        }

        .badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #e5e7eb;
        }

        .badge-active {
            border-color: var(--secondary-color);
            color: #fff;
            background: rgba(233, 69, 96, 0.2);
            font-weight: 500;
        }

        .user-dropdown {
            background: var(--secondary-color);
            color: white;
            padding: 6px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(233, 69, 96, 0.3);
            border: none;
            cursor: pointer;
        }

        .user-dropdown:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Desktop legacy Menu bar simulation */
        nav {
            background: #ffffff;
            padding: 0 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .menu-list {
            list-style: none;
            display: flex;
            gap: 0;
        }

        .menu-item {
            position: relative;
        }

        .menu-link {
            display: block;
            padding: 10px 16px;
            color: #222;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .menu-link:hover,
        .menu-item:hover .menu-link,
        .menu-item.active .menu-link {
            background: #003087;
            color: #fff;
        }

        /* Dropdown - Windows legacy style */
        .dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 1px solid #7da9d4;
            border-top: none;
            min-width: 180px;
            z-index: 9999;
            padding: 2px 0;
        }

        .menu-item:hover .dropdown {
            display: block;
        }

        .dropdown a {
            display: block;
            padding: 6px 18px;
            font-size: 13px;
            color: #222;
            text-decoration: none;
            background: #c5ddf4;
            border-bottom: 1px solid #a8c8e8;
            position: relative;
        }

        .dropdown a:last-child {
            border-bottom: none;
        }

        .dropdown a:hover {
            background: #4a90d4;
            color: #fff;
        }

        /* Sub-dropdown styling (flyout to the right) */
        .dropdown .has-sub > a {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdown .sub-menu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            background: #fff;
            border: 1px solid #7da9d4;
            min-width: 180px;
            z-index: 10000;
            padding: 2px 0;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.15);
        }

        .dropdown .has-sub, .dropdown-menu .has-sub {
            position: relative;
        }

        .dropdown .has-sub:hover .sub-menu, .dropdown-menu .has-sub:hover .sub-menu {
            display: block;
        }

        .dropdown .sub-menu a, .dropdown-menu .sub-menu a {
            background: #c5ddf4;
            border-bottom: 1px solid #a8c8e8;
            color: #222;
        }

        .dropdown .sub-menu a:hover, .dropdown-menu .sub-menu a:hover {
            background: #4a90d4;
            color: #fff;
        }

        .dropdown a.highlighted {
            background: #c5ddf4;
            color: #222;
        }

        .dropdown a.highlighted:hover {
            background: #4a90d4;
            color: #fff;
        }

        /* Main Content Grid */
        main {
            flex: 1;
            padding: 20px;
            width: 100%;
            max-width: 100%;
            margin: 0;
            box-sizing: border-box;
        }

        /* Floating Auto-dismiss Toast Notifications */
        #toast-container {
            position: fixed;
            top: 75px;
            right: 20px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .toast-badge {
            pointer-events: auto;
            min-width: 280px;
            max-width: 420px;
            padding: 12px 18px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            animation: toastSlideIn 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        .toast-badge.toast-exit {
            animation: toastSlideOut 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        @keyframes toastSlideIn {
            from { transform: translateX(120%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes toastSlideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(120%); opacity: 0; }
        }

        .toast-success {
            background: #065f46;
            color: #ffffff;
            border-left: 5px solid #34d399;
        }

        .toast-error {
            background: #991b1b;
            color: #ffffff;
            border-left: 5px solid #f87171;
        }

        .toast-info {
            background: #0f3460;
            color: #ffffff;
            border-left: 5px solid #60a5fa;
        }

        .toast-close {
            background: transparent;
            border: none;
            color: currentColor;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            opacity: 0.8;
            padding: 0 4px;
        }

        .toast-close:hover {
            opacity: 1;
        }

        /* Footer */
        footer {
            position: fixed;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            text-align: center;
            padding: 8px 30px;
            font-size: 11px;
            color: #4b5563;
            border-radius: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            font-weight: 500;
            white-space: nowrap;
        }

        /* Top Loading Progress Bar */
        #top-loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff7e5f, #feb47b, #2ebf91, #8360c3, var(--secondary-color));
            background-size: 400% 400%;
            z-index: 9999999;
            width: 0%;
            transition: width 0.4s ease-out, opacity 0.3s ease;
            opacity: 0;
            animation: gradient-animation 2s ease infinite;
        }
        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 10px;
            }
            .menu-list {
                flex-wrap: wrap;
            }
        }

        /* SysDialog Global Popup Modal Styles */
        .sys-dialog-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 9999999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            transition: opacity 0.18s ease-in-out;
        }
        .sys-dialog-overlay.sys-dialog-active {
            opacity: 1;
        }
        .sys-dialog-box {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.45),
                0 10px 20px -5px rgba(15, 52, 96, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(15, 52, 96, 0.2);
            overflow: hidden;
            transform: translateY(-24px) scale(0.93);
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
            font-family: 'Segoe UI', Tahoma, 'Poppins', sans-serif;
            position: relative;
        }
        .sys-dialog-overlay.sys-dialog-active .sys-dialog-box {
            transform: translateY(0) scale(1);
        }
        .sys-dialog-header {
            background: linear-gradient(135deg, #0f3460 0%, #162447 100%);
            color: #ffffff;
            padding: 11px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: grab;
            user-select: none;
            -webkit-user-select: none;
        }
        .sys-dialog-header:active {
            cursor: grabbing;
        }
        .sys-dialog-close {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            opacity: 0.8;
            transition: opacity 0.15s;
        }
        .sys-dialog-close:hover {
            opacity: 1;
        }
        .sys-dialog-body {
            padding: 20px 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        .sys-dialog-icon-wrapper {
            font-size: 28px;
            line-height: 1;
            flex-shrink: 0;
        }
        .sys-dialog-message {
            font-size: 13px;
            color: #334155;
            line-height: 1.5;
            font-weight: 500;
            word-break: break-word;
        }
        .sys-dialog-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
        }
        .sys-btn {
            height: 34px;
            padding: 0 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .sys-btn:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
        .sys-btn-confirm {
            background: #0f3460;
            color: #ffffff;
            border-color: #0f3460;
        }
        .sys-btn-confirm:hover {
            background: #162447;
            border-color: #162447;
            box-shadow: 0 2px 4px rgba(15, 52, 96, 0.3);
        }
        .sys-btn-danger {
            background: linear-gradient(180deg, #dc2626, #b91c1c) !important;
            color: #ffffff !important;
            border-color: #991b1b !important;
        }
        .sys-btn-danger:hover {
            background: linear-gradient(180deg, #ef4444, #dc2626) !important;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3);
        }
    </style>
    @yield('styles')
    <script>
        // Start loading bar execution
        function startLoadingBar() {
            const bar = document.getElementById('top-loading-bar');
            if (bar) {
                bar.style.opacity = '1';
                bar.style.width = '30%';
                
                // Slowly progress the bar to simulate activity
                let progress = 30;
                const interval = setInterval(() => {
                    if (progress < 90) {
                        progress += Math.random() * 5;
                        bar.style.width = progress + '%';
                    } else {
                        clearInterval(interval);
                    }
                }, 150);
                
                // Store interval key to clear on complete
                window.loadingBarInterval = interval;
            }
        }

        // Finish loading bar execution
        function finishLoadingBar() {
            const bar = document.getElementById('top-loading-bar');
            if (bar) {
                if (window.loadingBarInterval) {
                    clearInterval(window.loadingBarInterval);
                }
                bar.style.width = '100%';
                setTimeout(() => {
                    bar.style.opacity = '0';
                    setTimeout(() => {
                        bar.style.width = '0%';
                    }, 300);
                }, 200);
            }
        }

        document.addEventListener('input', function(e) {
            const el = e.target;
            if (!el || !el.tagName) return;

            const name = (el.name || '').toLowerCase();
            const id = (el.id || '').toLowerCase();

            // 1. Mobile & Phone fields: strictly digits 0-9 and max 10 digits
            if (name.includes('mobile') || id.includes('mobile') || name.includes('phone') || id.includes('phone')) {
                const clean = el.value.replace(/\D/g, '').slice(0, 10);
                if (el.value !== clean) {
                    el.value = clean;
                }
                return;
            }

            // 2. Strict Integer fields (no decimals allowed: e.g. no_of_pkgs, bilty_no, eway_bill_input)
            if (el.classList.contains('input-no_of_pkgs') || id === 'bilty_no' || id === 'eway_bill_input' || name === 'bilty_no') {
                const clean = el.value.replace(/\D/g, '');
                if (el.value !== clean) {
                    el.value = clean;
                }
                return;
            }

            // 3. Uppercase text fields
            if (el.tagName === 'TEXTAREA' || (el.tagName === 'INPUT' && (el.type === 'text' || el.type === 'search' || !el.type))) {
                const start = el.selectionStart;
                const end = el.selectionEnd;
                const upper = el.value.toUpperCase();
                if (el.value !== upper) {
                    el.value = upper;
                    if (start !== null && end !== null) {
                        el.setSelectionRange(start, end);
                    }
                }
            }
        });

        // Prevent invalid characters (e, E, +, -) on type="number" inputs
        document.addEventListener('keydown', function(e) {
            const el = e.target;
            if (el && el.tagName === 'INPUT' && el.type === 'number') {
                if (['e', 'E', '+', '-'].includes(e.key)) {
                    e.preventDefault();
                }
            }
        });

        function dismissToast(el) {
            if (!el || el.classList.contains('toast-exit')) return;
            el.classList.add('toast-exit');
            setTimeout(() => {
                el.remove();
            }, 800);
        }

        // Trigger on load
        document.addEventListener('DOMContentLoaded', () => {
            finishLoadingBar();

            document.querySelectorAll('.toast-badge').forEach(toast => {
                setTimeout(() => {
                    dismissToast(toast);
                }, 4500);
            });

            // Intercept form submissions and link clicks to show loading bar
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', () => {
                    startLoadingBar();
                });
            });

            document.querySelectorAll('a').forEach(link => {
                // Ignore target="_blank", javascript voids, or hash anchors
                const href = link.getAttribute('href');
                const target = link.getAttribute('target');
                if (href && !href.startsWith('#') && !href.startsWith('javascript:') && target !== '_blank') {
                    link.addEventListener('click', () => {
                        startLoadingBar();
                    });
                }
            });
        });

        // Trigger when window starts unloading/refreshing
        window.addEventListener('beforeunload', () => {
            startLoadingBar();
        });
    </script>
</head>
<body>
    <div id="top-loading-bar"></div>

    <div class="fixed-top-nav">
        <header>
            <div class="header-logo" style="cursor: pointer;" onclick="window.location='{{ route('dashboard') }}'">
                <span class="truck-icon">🚚</span>
                <h1>
                    OMKAAR LOGISTICS
                    <span class="subtitle">FAST • SAFE • RELIABLE</span>
                </h1>
            </div>
            <div class="header-info">
                <span class="badge">Company: <strong>{{ session('company_name', 'OMKAAR LOGISTICS') }}</strong></span>
                
                @auth
                    <form action="{{ route('financial-year.switch') }}" method="POST" style="display:inline-block; margin:0;">
                        @csrf
                        <span class="badge badge-active" style="display:inline-flex; align-items:center; gap:4px;">
                            FY:
                            <select name="financial_year" onchange="this.form.submit()" style="background:transparent; color:#fff; border:none; font-weight:bold; font-size:11px; cursor:pointer; outline:none;">
                                @php
                                    $allFinYears = \App\Models\FinancialYear::all();
                                    $currentFy = session('financial_year', '2026-2027');
                                @endphp
                                <option value="ALL" {{ $currentFy === 'ALL' ? 'selected' : '' }} style="color:#000;">ALL (All Years)</option>
                                @foreach($allFinYears as $fy)
                                    <option value="{{ $fy->year_string }}" {{ $currentFy === $fy->year_string ? 'selected' : '' }} style="color:#000;">{{ $fy->year_string }}</option>
                                @endforeach
                            </select>
                        </span>
                    </form>

                    <span class="badge">User: <strong>{{ Auth::user()->name }}</strong></span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="user-dropdown">Log Out</button>
                    </form>
                @endauth
            </div>
        </header>

        @auth
        @php
            $user = Auth::user();
            $canTransaction = $user->hasPermission('transaction.cn_book') || $user->hasPermission('transaction.receipt') || $user->hasPermission('transaction.payment') || $user->hasPermission('transaction.party_bill');
            $canAccount = $user->hasPermission('account.group') || $user->hasPermission('account.ledger') || $user->hasPermission('account.payment_expenses') || $user->hasPermission('account.voucher') || $user->hasPermission('account.deposit_bank') || $user->hasPermission('account.reports');
            $canReport = $user->hasPermission('report.bilty_register') || $user->hasPermission('report.party_bill_register') || $user->hasPermission('report.receipt_register') || $user->hasPermission('report.payment_register') || $user->hasPermission('report.tds_report');
            $canMaster = $user->hasPermission('master.item') || $user->hasPermission('master.measurement_unit') || $user->hasPermission('master.shipping_status') || $user->hasPermission('master.series') || $user->hasPermission('master.transport') || $user->hasPermission('master.country') || $user->hasPermission('master.state') || $user->hasPermission('master.city') || $user->hasPermission('master.currency');
            $canTools = $user->hasPermission('tools.backup') || $user->hasPermission('tools.restore') || $user->hasPermission('tools.settings');
            $canSystem = $user->hasPermission('system.change_password') || $user->hasPermission('system.user_management') || $user->hasPermission('system.role_management');
        @endphp

        <nav>
            <ul class="menu-list">
                <!-- Transaction -->
                @if($canTransaction)
                <li class="menu-item active">
                    <a href="#" class="menu-link">Transaction</a>
                    <div class="dropdown">
                        @if($user->hasPermission('transaction.cn_book'))
                            <a href="{{ route('bilty.create') }}" target="_blank" class="highlighted">C.N Book</a>
                        @endif
                        @if($user->hasPermission('transaction.receipt'))
                            <a href="#">Receipt</a>
                        @endif
                        @if($user->hasPermission('transaction.payment'))
                            <a href="#">Payment</a>
                        @endif
                        @if($user->hasPermission('transaction.party_bill'))
                            <a href="#">Party Bill</a>
                        @endif
                    </div>
                </li>
                @endif

                <!-- Account -->
                @if($canAccount)
                <li class="menu-item">
                    <a href="#" class="menu-link">Account</a>
                    <div class="dropdown">
                        @if($user->hasPermission('account.group'))
                            <a href="#">Group</a>
                        @endif
                        @if($user->hasPermission('account.ledger'))
                            <a href="{{ route('account.ledger') }}">Account Ledger</a>
                        @endif
                        @if($user->hasPermission('account.payment_expenses'))
                            <a href="#">Payment &amp; Expenses</a>
                        @endif
                        @if($user->hasPermission('account.voucher'))
                            <a href="#">Voucher</a>
                        @endif
                        @if($user->hasPermission('account.deposit_bank'))
                            <a href="#">Deposit in Bank</a>
                        @endif
                        @if($user->hasPermission('account.reports'))
                            <a href="#">Reports &nbsp;&#9658;</a>
                        @endif
                    </div>
                </li>
                @endif

                <!-- Report -->
                @if($canReport)
                <li class="menu-item">
                    <a href="#" class="menu-link">Report</a>
                    <div class="dropdown">
                        @if($user->hasPermission('report.bilty_register'))
                            <div class="has-sub">
                                <a href="{{ route('report.bilty_register') }}">C.N &nbsp;&#9658;</a>
                                <div class="sub-menu">
                                    <a href="{{ route('report.bilty_register') }}">C.N Register</a>
                                    @if($user->hasPermission('report.party_bill_register'))
                                        <a href="#">Party Bill Register</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($user->hasPermission('report.receipt_register'))
                            <a href="#">Receipt Register</a>
                        @endif
                        @if($user->hasPermission('report.payment_register'))
                            <a href="#">Payment Register</a>
                        @endif
                        @if($user->hasPermission('report.tds_report'))
                            <a href="#">Receipt Detail/TDS Report</a>
                        @endif
                    </div>
                </li>
                @endif

                <!-- Master -->
                @if($canMaster)
                <li class="menu-item">
                    <a href="#" class="menu-link">Master</a>
                    <div class="dropdown">
                        @if($user->hasPermission('master.item'))
                            <div class="has-sub">
                                <a href="#">Item &nbsp;&#9658;</a>
                                <div class="sub-menu">
                                    <a href="#">Create Item</a>
                                    <a href="#">Item List</a>
                                </div>
                            </div>
                        @endif
                        <div class="has-sub">
                            <a href="#">General &nbsp;&#9658;</a>
                            <div class="sub-menu">
                                @if($user->hasPermission('master.series'))
                                    <a href="{{ route('master.series') }}">Series</a>
                                @endif
                                @if($user->hasPermission('master.measurement_unit'))
                                    <a href="{{ route('master.measurement-unit') }}">Measurement Unit</a>
                                @endif
                                @if($user->hasPermission('master.shipping_status'))
                                    <a href="{{ route('master.shipping-status') }}">Shipping Status</a>
                                @endif
                                @if($user->hasPermission('master.transport'))
                                    <a href="#">Transport</a>
                                @endif
                                @if($user->hasPermission('master.country'))
                                    <a href="{{ route('master.country') }}">Country</a>
                                @endif
                                @if($user->hasPermission('master.state'))
                                    <a href="{{ route('master.state') }}">State</a>
                                @endif
                                @if($user->hasPermission('master.city'))
                                    <a href="{{ route('master.city') }}">City</a>
                                @endif
                                @if($user->hasPermission('master.currency'))
                                    <a href="#">Currency</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                <!-- Tools -->
                @if($canTools)
                <li class="menu-item">
                    <a href="#" class="menu-link">Tools</a>
                    <div class="dropdown">
                        @if($user->hasPermission('tools.backup'))
                            <a href="#">Backup</a>
                        @endif
                        @if($user->hasPermission('tools.restore'))
                            <a href="#">Restore</a>
                        @endif
                        @if($user->hasPermission('tools.settings'))
                            <a href="#">Settings</a>
                        @endif
                    </div>
                </li>
                @endif

                <!-- System -->
                @if($canSystem)
                <li class="menu-item">
                    <a href="#" class="menu-link">System</a>
                    <div class="dropdown">
                        @if($user->hasPermission('system.user_management'))
                            <a href="{{ route('system.user') }}">User Management</a>
                        @endif
                        @if($user->hasPermission('system.role_management'))
                            <a href="{{ route('system.role') }}">Role Management</a>
                        @endif
                        @if($user->hasPermission('system.change_password'))
                            <a href="{{ route('system.change_password') }}">Change Password</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;padding:7px 16px;font-size:13px;color:#222;background:none;border:none;border-top:1px solid #eee;text-align:left;cursor:pointer;">
                                Logout
                            </button>
                        </form>
                    </div>
                </li>
                @endif
            </ul>
        </nav>
        @endauth
    </div>
    </div>

    <!-- Floating Toast Notification Overlay -->
    <div id="toast-container">
        @if (session('success'))
            <div class="toast-badge toast-success">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:16px;">✅</span>
                    <span>{{ session('success') }}</span>
                    @if (session('print_id'))
                        <a href="{{ route('bilty.print', session('print_id')) }}" target="_blank" style="margin-left:10px; color:#fff; font-weight:700; text-decoration:underline;">🖨 Print Bilty</a>
                    @endif
                </div>
                <button class="toast-close" onclick="dismissToast(this.closest('.toast-badge'))">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-badge toast-error">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:16px;">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button class="toast-close" onclick="dismissToast(this.closest('.toast-badge'))">&times;</button>
            </div>
        @endif

        @if (session('info'))
            <div class="toast-badge toast-info">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:16px;">ℹ️</span>
                    <span>{{ session('info') }}</span>
                </div>
                <button class="toast-close" onclick="dismissToast(this.closest('.toast-badge'))">&times;</button>
            </div>
        @endif
    </div>

    <main>
        @yield('content')
    </main>

    <footer>
        OMKAAR LOGISTICS ERP System &copy; 2026. Version 10.10.1005 (Web Version)
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error Occurred',
                    text: {!! json_encode(session('error')) !!},
                    confirmButtonColor: '#0f3460'
                });
            @endif

            @if ($errors->any())
                const serverErrors = {!! json_encode($errors->all()) !!};
                let errHtml = '<div style="text-align:left; font-size:13px; max-height:240px; overflow-y:auto; padding-right:5px;">' +
                    '<p style="margin-top:0; margin-bottom:8px; font-weight:600; color:#b91c1c;">Please correct the following errors:</p>' +
                    '<ul style="margin:0; padding-left:18px; color:#374151; line-height:1.6;">' +
                    serverErrors.map(function(e) { return '<li style="margin-bottom:4px;">' + e + '</li>'; }).join('') +
                    '</ul></div>';
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: errHtml,
                    confirmButtonColor: '#0f3460',
                    confirmButtonText: 'Fix Errors'
                });
            @endif
        });
    </script>
    <!-- SysDialog Global Popup Modal Overlay -->
    <div id="sys-dialog-overlay" class="sys-dialog-overlay" style="display:none;">
        <div class="sys-dialog-box" role="dialog" aria-modal="true">
            <div class="sys-dialog-header" id="sys-dialog-header">
                <span class="sys-dialog-title" id="sys-dialog-title">Confirmation Required</span>
                <button type="button" class="sys-dialog-close" id="sys-dialog-close-btn">&times;</button>
            </div>
            <div class="sys-dialog-body">
                <div class="sys-dialog-icon-wrapper" id="sys-dialog-icon-wrapper">
                    <span class="sys-dialog-icon" id="sys-dialog-icon">⚠️</span>
                </div>
                <div class="sys-dialog-content">
                    <div class="sys-dialog-message" id="sys-dialog-message">Are you sure you want to proceed?</div>
                </div>
            </div>
            <div class="sys-dialog-footer" id="sys-dialog-footer">
                <button type="button" class="sys-btn sys-btn-cancel" id="sys-dialog-cancel-btn">Cancel</button>
                <button type="button" class="sys-btn sys-btn-confirm" id="sys-dialog-confirm-btn">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        window.SysDialog = {
            _resolve: null,

            show: function(options) {
                return new Promise((resolve) => {
                    this._resolve = resolve;
                    const overlay = document.getElementById('sys-dialog-overlay');
                    const titleEl = document.getElementById('sys-dialog-title');
                    const iconEl = document.getElementById('sys-dialog-icon');
                    const msgEl = document.getElementById('sys-dialog-message');
                    const cancelBtn = document.getElementById('sys-dialog-cancel-btn');
                    const confirmBtn = document.getElementById('sys-dialog-confirm-btn');

                    titleEl.textContent = options.title || 'Confirmation Required';
                    msgEl.innerHTML = options.message || 'Are you sure you want to proceed?';

                    let type = options.type || 'warning';
                    if (type === 'danger') {
                        iconEl.textContent = '🗑️';
                    } else if (type === 'warning') {
                        iconEl.textContent = '⚠️';
                    } else if (type === 'success') {
                        iconEl.textContent = '✅';
                    } else {
                        iconEl.textContent = 'ℹ️';
                    }

                    cancelBtn.textContent = options.cancelText || 'Cancel';
                    confirmBtn.textContent = options.confirmText || 'Confirm';

                    confirmBtn.className = 'sys-btn ' + (options.type === 'danger' ? 'sys-btn-danger' : 'sys-btn-confirm');
                    
                    if (options.showCancel === false) {
                        cancelBtn.style.display = 'none';
                    } else {
                        cancelBtn.style.display = 'inline-flex';
                    }

                    const box = document.querySelector('.sys-dialog-box');
                    if (box) {
                        box.style.position = 'relative';
                        box.style.left = 'auto';
                        box.style.top = 'auto';
                    }

                    overlay.style.display = 'flex';
                    requestAnimationFrame(() => {
                        overlay.classList.add('sys-dialog-active');
                        confirmBtn.focus();
                    });
                });
            },

            confirm: function(msgOrOpts, title = 'Confirmation Required') {
                const opts = typeof msgOrOpts === 'string'
                    ? { message: msgOrOpts, title: title, type: 'warning', confirmText: 'Yes, Proceed', cancelText: 'Cancel' }
                    : msgOrOpts;
                return this.show(opts);
            },

            delete: function(msgOrOpts, title = 'Delete Confirmation') {
                const opts = typeof msgOrOpts === 'string'
                    ? { message: msgOrOpts, title: title, type: 'danger', confirmText: 'Yes, Delete', cancelText: 'Cancel' }
                    : Object.assign({ type: 'danger', confirmText: 'Yes, Delete', cancelText: 'Cancel' }, msgOrOpts);
                return this.show(opts);
            },

            alert: function(msgOrOpts, title = 'Notice') {
                const opts = typeof msgOrOpts === 'string'
                    ? { message: msgOrOpts, title: title, type: 'info', showCancel: false, confirmText: 'OK' }
                    : Object.assign({ showCancel: false, confirmText: 'OK' }, msgOrOpts);
                return this.show(opts);
            },

            close: function(result) {
                const overlay = document.getElementById('sys-dialog-overlay');
                if (!overlay) return;
                overlay.classList.remove('sys-dialog-active');
                setTimeout(() => {
                    overlay.style.display = 'none';
                    if (this._resolve) {
                        this._resolve(result);
                        this._resolve = null;
                    }
                }, 150);
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const cancelBtn = document.getElementById('sys-dialog-cancel-btn');
            const confirmBtn = document.getElementById('sys-dialog-confirm-btn');
            const closeBtn = document.getElementById('sys-dialog-close-btn');

            cancelBtn?.addEventListener('click', () => SysDialog.close(false));
            closeBtn?.addEventListener('click', () => SysDialog.close(false));
            confirmBtn?.addEventListener('click', () => SysDialog.close(true));

            // Make floating dialog draggable by title bar
            const header = document.getElementById('sys-dialog-header');
            const dialogBox = document.querySelector('.sys-dialog-box');
            let isDragging = false, startX = 0, startY = 0, initialLeft = 0, initialTop = 0;

            header?.addEventListener('mousedown', function(e) {
                if (e.target.closest('#sys-dialog-close-btn')) return;
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                const rect = dialogBox.getBoundingClientRect();
                initialLeft = rect.left;
                initialTop = rect.top;
                dialogBox.style.position = 'fixed';
                dialogBox.style.left = initialLeft + 'px';
                dialogBox.style.top = initialTop + 'px';
                dialogBox.style.margin = '0';

                const onMouseMove = (evt) => {
                    if (!isDragging) return;
                    const dx = evt.clientX - startX;
                    const dy = evt.clientY - startY;
                    dialogBox.style.left = (initialLeft + dx) + 'px';
                    dialogBox.style.top = (initialTop + dy) + 'px';
                };

                const onMouseUp = () => {
                    isDragging = false;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                };

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });

            document.addEventListener('keydown', function(e) {
                const overlay = document.getElementById('sys-dialog-overlay');
                if (overlay && overlay.style.display !== 'none' && overlay.classList.contains('sys-dialog-active')) {
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        SysDialog.close(false);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        SysDialog.close(true);
                    }
                }
            });

            // Global Interceptor for forms with data-confirm
            document.addEventListener('submit', function(e) {
                const form = e.target;
                const confirmMsg = form.getAttribute('data-confirm');
                if (confirmMsg && !form.dataset.confirmed) {
                    e.preventDefault();
                    const isDelete = confirmMsg.toLowerCase().includes('delete') || confirmMsg.toLowerCase().includes('remove');
                    const dialogPromise = isDelete ? SysDialog.delete(confirmMsg) : SysDialog.confirm(confirmMsg);
                    dialogPromise.then(confirmed => {
                        if (confirmed) {
                            form.dataset.confirmed = "true";
                            form.submit();
                        }
                    });
                }
            }, true);
        });
    </script>
    @yield('scripts')
</body>
</html>
