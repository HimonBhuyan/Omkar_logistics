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
    <div class="title-bar" style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:15px;">
            <span class="app-title">
                {{ strtoupper(session('company_name', 'OMKAAR LOGISTICS')) }}
            </span>
            <form action="{{ route('financial-year.switch') }}" method="POST" style="display:inline-block; margin:0;">
                @csrf
                <span style="background:rgba(255,255,255,0.2); padding:2px 8px; border-radius:4px; font-weight:bold; font-size:11px; color:#fff;">
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
        </div>
        <div>
            <span class="version">Version :10.10.1005</span>
            <span class="user-badge" style="margin-left:10px;">{{ strtoupper(auth()->user()->username ?? 'USER') }}</span>
        </div>
    </div>

    @php
        $user = Auth::user();
        $canTransaction = $user->hasPermission('transaction.cn_book') || $user->hasPermission('transaction.receipt') || $user->hasPermission('transaction.payment') || $user->hasPermission('transaction.party_bill');
        $canAccount = $user->hasPermission('account.group') || $user->hasPermission('account.ledger') || $user->hasPermission('account.payment_expenses') || $user->hasPermission('account.voucher') || $user->hasPermission('account.deposit_bank') || $user->hasPermission('account.reports');
        $canReport = $user->hasPermission('report.bilty_register') || $user->hasPermission('report.party_bill_register') || $user->hasPermission('report.receipt_register') || $user->hasPermission('report.payment_register') || $user->hasPermission('report.tds_report');
        $canMaster = $user->hasPermission('master.item') || $user->hasPermission('master.measurement_unit') || $user->hasPermission('master.series') || $user->hasPermission('master.transport') || $user->hasPermission('master.country') || $user->hasPermission('master.state') || $user->hasPermission('master.city') || $user->hasPermission('master.currency');
        $canTools = $user->hasPermission('tools.backup') || $user->hasPermission('tools.restore') || $user->hasPermission('tools.settings');
        $canSystem = $user->hasPermission('system.change_password') || $user->hasPermission('system.user_management') || $user->hasPermission('system.role_management');
    @endphp

    <!-- Menu Bar -->
    <nav class="menu-bar" style="display: flex; justify-content: space-between; align-items: center;">

        <div style="display: flex; align-items: center;">
            <!-- Transaction -->
            @if($canTransaction)
            <div class="menu-item">
                <a href="#">Transaction</a>
                <div class="dropdown-menu">
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
            </div>
            @endif

            <!-- Account -->
            @if($canAccount)
            <div class="menu-item">
                <a href="#">Account</a>
                <div class="dropdown-menu">
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
            </div>
            @endif

            <!-- Report -->
            @if($canReport)
            <div class="menu-item">
                <a href="#">Report</a>
                <div class="dropdown-menu">
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
            </div>
            @endif

            <!-- Master -->
            @if($canMaster)
            <div class="menu-item">
                <a href="#">Master</a>
                <div class="dropdown-menu">
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
            </div>
            @endif

            <!-- Tools -->
            @if($canTools)
            <div class="menu-item">
                <a href="#">Tools</a>
                <div class="dropdown-menu">
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
            </div>
            @endif

            <!-- System -->
            @if($canSystem)
            <div class="menu-item">
                <a href="#">System</a>
                <div class="dropdown-menu">
                    @if($user->hasPermission('system.user_management'))
                        <a href="{{ route('system.user') }}">User Management</a>
                    @endif
                    @if($user->hasPermission('system.role_management'))
                        <a href="{{ route('system.role') }}">Role Management</a>
                    @endif
                    @if($user->hasPermission('system.change_password'))
                        <a href="{{ route('system.change_password') }}">Change Password</a>
                    @endif
                </div>
            </div>
            @endif
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