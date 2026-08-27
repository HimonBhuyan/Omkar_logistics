<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('company_name', 'OMKAAR LOGISTICS') }} - {{ session('financial_year', '2026-2027') }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600&family=Poppins:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html,
        body {
            height: 100%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── Title Bar ── */
        .title-bar {
            background: #f0f0f0;
            border-bottom: 1px solid #ccc;
            padding: 4px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #333;
            user-select: none;
        }

        .title-bar .app-title {
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .title-bar .version {
            color: #666;
        }

        .title-bar .user-badge {
            background: #003087;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 2px;
            letter-spacing: 1px;
        }

        /* ── Menu Bar ── */
        .menu-bar {
            background: #f5f5f5;
            border-bottom: 2px solid #ddd;
            display: flex;
            align-items: stretch;
            padding: 0;
            position: relative;
            z-index: 1000;
        }

        .menu-item {
            position: relative;
        }

        .menu-item>a {
            display: block;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #222;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            border: 1px solid transparent;
            transition: background 0.1s;
        }

        .menu-item>a:hover,
        .menu-item.active>a {
            background: #003087;
            color: #fff;
            border-color: #002070;
        }

        /* Dropdown - Windows legacy style */
        .dropdown-menu {
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

        .menu-item:hover .dropdown-menu,
        .menu-item.active .dropdown-menu {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 6px 18px;
            font-size: 13px;
            color: #222;
            text-decoration: none;
            background: #c5ddf4;
            border-bottom: 1px solid #a8c8e8;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }

        .dropdown-menu a:hover,
        .dropdown-menu a.active-item {
            background: #4a90d4;
            color: #fff;
        }

        /* Sub-dropdown styling (flyout to the right) */
        .dropdown-menu .has-sub {
            position: relative;
        }

        .dropdown-menu .has-sub>a {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdown-menu .sub-menu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            background: #fff;
            border: 1px solid #7da9d4;
            min-width: 180px;
            z-index: 10000;
            padding: 2px 0;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.15);
        }

        .dropdown-menu .has-sub:hover .sub-menu {
            display: block;
        }

        .dropdown-menu .sub-menu a {
            background: #c5ddf4;
            border-bottom: 1px solid #a8c8e8;
            color: #222;
        }

        .dropdown-menu .sub-menu a:hover {
            background: #4a90d4;
            color: #fff;
        }

        /* Active highlighted items */
        .dropdown-menu a.highlighted {
            background: #c5ddf4;
            color: #222;
        }

        /* ── Main Content ── */
        .main-content {
            flex: 1;
            display: flex;
            overflow: hidden;
            background: #ffffff;
        }

        .center-logo {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .center-logo img {
            width: 70%;
            height: 70%;
            object-fit: contain;
            display: block;
        }

        /* ── Status Bar ── */
        .status-bar {
            background: #f0f0f0;
            border-top: 1px solid #ccc;
            padding: 3px 10px;
            font-size: 11px;
            color: #555;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>

    <!-- Title Bar -->
    <div class="title-bar">
        <span class="app-title">
            {{ strtoupper(session('company_name', 'OMKAAR LOGISTICS')) }}-{{ session('financial_year', '2026-2027') }}
        </span>
        <span class="version">Version :10.10.1005</span>
        <span class="user-badge">{{ strtoupper(auth()->user()->username ?? 'USER') }}</span>
    </div>

    <!-- Menu Bar -->
    <nav class="menu-bar" style="display: flex; justify-content: space-between; align-items: center;">

        <div style="display: flex; align-items: center;">
            <!-- Transaction -->
            <div class="menu-item">
                <a href="#">Transaction</a>
                <div class="dropdown-menu">
                    <a href="{{ route('bilty.create') }}" target="_blank" class="highlighted">C.N Book</a>
                    <a href="#">Receipt</a>
                    <a href="#">Payment</a>
                    <a href="#">Party Bill</a>
                </div>
            </div>

            <!-- Account -->
            <div class="menu-item">
                <a href="#">Account</a>
                <div class="dropdown-menu">
                    <a href="#">Group</a>
                    <a href="{{ route('account.ledger') }}">Account Ledger</a>
                    <a href="#">Payment &amp; Expenses</a>
                    <a href="#">Voucher</a>
                    <a href="#">Deposit in Bank</a>
                    <a href="#">Reports &nbsp;&#9658;</a>
                </div>
            </div>

            <!-- Report -->
            <div class="menu-item">
                <a href="#">Report</a>
                <div class="dropdown-menu">
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
            </div>

            <!-- Master -->
            <div class="menu-item">
                <a href="#">Master</a>
                <div class="dropdown-menu">
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
            </div>

            <!-- Tools -->
            <div class="menu-item">
                <a href="#">Tools</a>
                <div class="dropdown-menu">
                    <a href="#">Backup</a>
                    <a href="#">Restore</a>
                    <a href="#">Settings</a>
                </div>
            </div>

            <!-- System -->
            <div class="menu-item">
                <a href="#">System</a>
                <div class="dropdown-menu">
                    <a href="#">Change Password</a>
                    <a href="#">User Management</a>
                </div>
            </div>
        </div>

        <!-- Right Side: Navbar Log Out Button -->
        <div style="padding-right: 15px;">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                @csrf
                <button type="submit" style="background: #e3001b; color: #fff; border: none; padding: 4px 14px; font-size: 12px; font-weight: bold; border-radius: 4px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 5px rgba(227, 0, 27, 0.2); transition: all 0.2s;">
                    Log Out
                </button>
            </form>
        </div>

    </nav>

    <!-- Main Content: Centered Animated Truck Video -->
    <div class="main-content" style="position: relative;">
        <div class="center-logo" style="width: 100%; height: 100%; margin-top: 10px;">
            <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; border-radius: 0; box-shadow: none; outline: none; display: block;">
                <source src="{{ asset('assets/Semi-truck_driving_forward_anima…_202608191115.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="status-bar">
        <span>{{ strtoupper(session('company_name', 'OMKAAR LOGISTICS')) }} &copy; {{ date('Y') }} | Version
            10.10.1005</span>
        <span>{{ now()->format('d-m-Y') }}</span>
    </div>

</body>

</html>