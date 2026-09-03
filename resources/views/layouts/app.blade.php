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

        /* Alerts and notifications */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert .btn-close {
            background: none;
            border: none;
            color: currentColor;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
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
            .menu-link {
                padding: 8px 12px;
            }
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

        // Trigger on load
        document.addEventListener('DOMContentLoaded', () => {
            finishLoadingBar();

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
                <span class="badge badge-active">FY: <strong>{{ session('financial_year', '2026-2027') }}</strong></span>
                @auth
                    <span class="badge">User: <strong>{{ Auth::user()->name }}</strong></span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="user-dropdown">Log Out</button>
                    </form>
                @endauth
            </div>
        </header>

        <nav>
            <ul class="menu-list">
                <!-- Transaction -->
                <li class="menu-item active">
                    <a href="#" class="menu-link">Transaction</a>
                    <div class="dropdown">
                        <a href="{{ route('bilty.create') }}" target="_blank" class="highlighted">C.N Book</a>
                        <a href="#">Receipt</a>
                        <a href="#">Payment</a>
                        <a href="#">Party Bill</a>
                    </div>
                </li>
                <!-- Account -->
                <li class="menu-item">
                    <a href="#" class="menu-link">Account</a>
                    <div class="dropdown">
                        <a href="#">Group</a>
                        <a href="{{ route('account.ledger') }}">Account Ledger</a>
                        <a href="#">Payment &amp; Expenses</a>
                        <a href="#">Voucher</a>
                        <a href="#">Deposit in Bank</a>
                        <a href="#">Reports &nbsp;&#9658;</a>
                    </div>
                </li>
                <!-- Report -->
                <li class="menu-item">
                    <a href="#" class="menu-link">Report</a>
                    <div class="dropdown">
                        <div class="has-sub">
                            <a href="{{ route('report.bilty_register') }}">C.N &nbsp;&#9658;</a>
                            <div class="sub-menu">
                                <a href="{{ route('report.bilty_register') }}">C.N Register</a>
                                <a href="#">Party Bill Register</a>
                            </div>
                        </div>
                        <a href="#">Receipt Register</a>
                        <a href="#">Payment Register</a>
                        <a href="#">Receipt Detail/TDS Report</a>
                    </div>
                </li>
                <!-- Master -->
                <li class="menu-item">
                    <a href="#" class="menu-link">Master</a>
                    <div class="dropdown">
                        <div class="has-sub">
                            <a href="#">Item &nbsp;&#9658;</a>
                            <div class="sub-menu">
                                <a href="#">Create Item</a>
                                <a href="#">Item List</a>
                            </div>
                        </div>
                        <div class="has-sub">
                            <a href="#">General &nbsp;&#9658;</a>
                            <div class="sub-menu">
                                <a href="#">Series</a>
                                <a href="#">Measurement Unit</a>
                                <a href="#">Transport</a>
                                <a href="{{ route('master.country') }}">Country</a>
                                <a href="{{ route('master.state') }}">State</a>
                                <a href="{{ route('master.city') }}">City</a>
                                <a href="#">Currency</a>
                            </div>
                        </div>
                    </div>
                </li>
                <!-- Tools -->
                <li class="menu-item">
                    <a href="#" class="menu-link">Tools</a>
                    <div class="dropdown">
                        <a href="#">Backup</a>
                        <a href="#">Restore</a>
                        <a href="#">Settings</a>
                    </div>
                </li>
                <!-- System -->
                <li class="menu-item">
                    <a href="#" class="menu-link">System</a>
                    <div class="dropdown">
                        <a href="#">Change Password</a>
                        <a href="#">User Management</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;padding:7px 16px;font-size:13px;color:#222;background:none;border:none;border-top:1px solid #eee;text-align:left;cursor:pointer;">
                                Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
    </div>

    <main>
        <!-- Success Alert -->
        @if (session('success'))
            <div class="alert alert-success">
                <div>
                    <strong>Success!</strong> {{ session('success') }}
                    @if (session('print_id'))
                        <a href="{{ route('bilty.print', session('print_id')) }}" target="_blank" style="margin-left:15px; color:var(--primary-color); font-weight:600; text-decoration:underline;">Print Lorry Receipt (Bilty)</a>
                    @endif
                </div>
                <button class="btn-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        <!-- Error Alert -->
        @if (session('error'))
            <div class="alert alert-danger">
                <div><strong>Error!</strong> {{ session('error') }}</div>
                <button class="btn-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

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
    @yield('scripts')
</body>
</html>
