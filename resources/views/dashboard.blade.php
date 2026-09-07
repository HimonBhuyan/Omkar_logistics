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
            background: #c5ddf4;
            border: 1px solid #7da9d4;
            min-width: 245px;
            z-index: 10000;
            padding: 0;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .dropdown-menu .has-sub:hover > .sub-menu {
            display: block;
        }

        .dropdown-menu .sub-menu a {
            background: #c5ddf4;
            border-bottom: 1px solid #a8c8e8;
            color: #000080;
            font-weight: 600;
            font-size: 12px;
            padding: 5px 14px;
            white-space: nowrap;
        }

        .dropdown-menu .sub-menu a:hover {
            background: #003087;
            color: #ffffff;
        }

        .sub-divider {
            height: 1px;
            background: #ffffff;
            border-bottom: 1px solid #7da9d4;
            margin: 0;
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
                <span style="background:#003087; color:#ffffff; padding:2px 8px; border-radius:3px; font-weight:600; font-size:11px; display:inline-flex; align-items:center; gap:4px; box-shadow: 0 1px 3px rgba(0,0,0,0.15);">
                    <span style="color:#ffffff; font-weight:700;">FY:</span>
                    <select name="financial_year" onchange="this.form.submit()" style="background:#003087; color:#ffffff; border:none; font-weight:700; font-size:11px; cursor:pointer; outline:none; padding:1px 2px;">
                        @php
                            $allFinYears = \App\Models\FinancialYear::all();
                            $currentFy = session('financial_year', '2026-2027');
                        @endphp
                        <option value="ALL" {{ $currentFy === 'ALL' ? 'selected' : '' }} style="background:#ffffff; color:#000000;">ALL (All Years)</option>
                        @foreach($allFinYears as $fy)
                            <option value="{{ $fy->year_string }}" {{ $currentFy === $fy->year_string ? 'selected' : '' }} style="background:#ffffff; color:#000000;">{{ $fy->year_string }}</option>
                        @endforeach
                    </select>
                </span>
            </form>
        </div>
        <div>
            <span class="version">Version :10.10.1005</span>
            <span class="user-badge" style="margin-left:10px;">{{ strtoupper(auth()->user()->name ?? 'USER') }}</span>
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

        // 1. Transaction Sub-Tabs Active States
        $isCnBookActive = request()->routeIs('bilty.create', 'bilty.store', 'bilty.edit') || (request()->is('bilty*') && !request()->routeIs('report.bilty_register*'));
        $isReceiptActive = request()->routeIs('receipt.create', 'receipt.edit', 'receipt.store') || (request()->is('receipt*') && !request()->routeIs('receipt.register*') && !request()->routeIs('report.receipt_detail_tds*'));
        $isPaymentActive = request()->routeIs('payment.create', 'payment.edit', 'payment.store') || (request()->is('payment*') && !request()->routeIs('payment.register*'));
        $isInvoiceActive = request()->routeIs('invoice.create', 'invoice.edit', 'invoice.store') || (request()->is('invoice*') && !request()->routeIs('invoice.register*'));

        $isTransactionActive = $isCnBookActive || $isReceiptActive || $isPaymentActive || $isInvoiceActive;

        // 2. Account Sub-Tabs Active States
        $isAccountLedgerActive = request()->routeIs('account.ledger*') || request()->is('account/ledger*');
        $isSundryCreditorsActive = request()->routeIs('report.sundry_creditors*') || request()->is('account/reports/sundry-creditors*');
        $isSundryDebtorsActive = request()->routeIs('report.sundry_debtors*') || request()->is('account/reports/sundry-debtors*');
        $isAccountReportFlyoutActive = $isSundryCreditorsActive || $isSundryDebtorsActive;

        $isAccountActive = $isAccountLedgerActive || $isAccountReportFlyoutActive || (request()->is('account*') && !request()->is('account/reports/sundry*'));

        // 3. Report Sub-Tabs Active States
        $isBiltyRegisterActive = request()->routeIs('report.bilty_register*') || request()->is('report/bilty-register*');
        $isInvoiceRegisterActive = request()->routeIs('invoice.register*') || request()->is('invoice/register*');
        $isCnReportFlyoutActive = $isBiltyRegisterActive || $isInvoiceRegisterActive;

        $isReceiptRegisterActive = request()->routeIs('receipt.register*') || request()->is('receipt/register*');
        $isPaymentRegisterActive = request()->routeIs('payment.register*') || request()->is('payment/register*');
        $isReceiptTdsActive = request()->routeIs('report.receipt_detail_tds*') || request()->is('report/receipt-detail-tds*');

        $isReportActive = $isCnReportFlyoutActive || $isReceiptRegisterActive || $isPaymentRegisterActive || $isReceiptTdsActive || request()->is('report*');

        // 4. Master Sub-Tabs Active States
        $isSeriesActive = request()->routeIs('master.series*') || request()->is('master/series*');
        $isUnitActive = request()->routeIs('master.measurement-unit*') || request()->is('master/measurement-unit*');
        $isShippingActive = request()->routeIs('master.shipping-status*') || request()->is('master/shipping-status*');
        $isCountryActive = request()->routeIs('master.country*') || request()->is('master/country*');
        $isStateActive = request()->routeIs('master.state*') || request()->is('master/state*');
        $isCityActive = request()->routeIs('master.city*') || request()->is('master/city*');

        $isGeneralMasterFlyoutActive = $isSeriesActive || $isUnitActive || $isShippingActive || $isCountryActive || $isStateActive || $isCityActive;

        $isMasterActive = $isGeneralMasterFlyoutActive || request()->routeIs('master.*') || request()->is('master*');

        // 5. Tools Sub-Tabs Active States
        $isToolsActive = request()->routeIs('tools.*') || request()->is('tools*');

        // 6. System Sub-Tabs Active States
        $isUserMgmtActive = request()->routeIs('system.user*') || request()->is('system/users*');
        $isRoleMgmtActive = request()->routeIs('system.role*') || request()->is('system/roles*');
        $isChangePasswordActive = request()->routeIs('system.change_password*') || request()->is('system/change-password*');

        $isSystemActive = $isUserMgmtActive || $isRoleMgmtActive || $isChangePasswordActive || request()->routeIs('system.*') || request()->is('system*');
    @endphp

    <!-- Menu Bar -->
    <nav class="menu-bar" style="display: flex; justify-content: space-between; align-items: center;">

        <div style="display: flex; align-items: center;">
            <!-- Transaction -->
            @if($canTransaction)
            <div class="menu-item {{ $isTransactionActive ? 'active' : '' }}">
                <a href="#">Transaction</a>
                    <div class="dropdown-menu">
                        <a href="{{ Route::has('bilty.create') ? route('bilty.create') : '#' }}" target="_blank" class="highlighted {{ $isCnBookActive ? 'active' : '' }}">C.N Book</a>
                        <a href="{{ Route::has('receipt.create') ? route('receipt.create') : '#' }}" target="_blank" class="{{ $isReceiptActive ? 'active' : '' }}">Receipt</a>
                        <a href="{{ Route::has('payment.create') ? route('payment.create') : '#' }}" target="_blank" class="{{ $isPaymentActive ? 'active' : '' }}">Payment</a>
                        <a href="{{ Route::has('invoice.create') ? route('invoice.create') : '#' }}" target="_blank" class="{{ $isInvoiceActive ? 'active' : '' }}">Invoice</a>
                    </div>
            </div>
            @endif

            <!-- Account -->
            @if($canAccount)
            <div class="menu-item {{ $isAccountActive ? 'active' : '' }}">
                <a href="#">Account</a>
                <div class="dropdown-menu">
                    <a href="#">Group</a>
                    <a href="{{ Route::has('account.ledger') ? route('account.ledger') : '#' }}" class="{{ $isAccountLedgerActive ? 'active' : '' }}">Account Ledger</a>
                    <a href="#">Payment &amp; Expenses</a>
                    <a href="#">Voucher</a>
                    <a href="#">Deposit in Bank</a>
                    <div class="has-sub">
                        <a href="#" class="{{ $isAccountReportFlyoutActive ? 'active' : '' }}"><span>Reports</span> <span>&#9658;</span></a>
                        <div class="sub-menu">
                            <a href="#">Day Book</a>
                            <a href="#">Cash Book</a>
                            <a href="#">Bank Book</a>
                            <div class="sub-divider"></div>
                            <a href="#">Ledger Book</a>
                            <a href="#">Ledger Book Summary</a>
                            <div class="sub-divider"></div>
                            <a href="{{ Route::has('report.sundry_creditors') ? route('report.sundry_creditors') : '#' }}" class="{{ $isSundryCreditorsActive ? 'active' : '' }}">Sundry Creditors Ledger Summary</a>
                            <a href="{{ Route::has('report.sundry_debtors') ? route('report.sundry_debtors') : '#' }}" class="{{ $isSundryDebtorsActive ? 'active' : '' }}">Sundry Debtor Ledger Summary</a>
                            <div class="sub-divider"></div>
                            <a href="#">Trial Balance</a>
                            <a href="#">Trading Account</a>
                            <a href="#">Profit &amp; Loss A/C</a>
                            <a href="#">Balance Sheet</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Report -->
            @if($canReport)
            <div class="menu-item {{ $isReportActive ? 'active' : '' }}">
                <a href="#">Report</a>
                <div class="dropdown-menu">
                    <div class="has-sub">
                        <a href="{{ Route::has('report.bilty_register') ? route('report.bilty_register') : '#' }}" class="{{ $isCnReportFlyoutActive ? 'active' : '' }}">C.N &nbsp;&#9658;</a>
                        <div class="sub-menu">
                            <a href="{{ Route::has('report.bilty_register') ? route('report.bilty_register') : '#' }}" class="{{ $isBiltyRegisterActive ? 'active' : '' }}">C.N Register</a>
                            <a href="{{ Route::has('invoice.register') ? route('invoice.register') : '#' }}" class="{{ $isInvoiceRegisterActive ? 'active' : '' }}">Invoice Register</a>
                        </div>
                    </div>
                    <a href="{{ Route::has('receipt.register') ? route('receipt.register') : '#' }}" class="{{ $isReceiptRegisterActive ? 'active' : '' }}">Receipt Register</a>
                    <a href="{{ Route::has('payment.register') ? route('payment.register') : '#' }}" class="{{ $isPaymentRegisterActive ? 'active' : '' }}">Payment Register</a>
                    <a href="{{ Route::has('report.receipt_detail_tds') ? route('report.receipt_detail_tds') : '#' }}" class="{{ $isReceiptTdsActive ? 'active' : '' }}">Receipt Detail/TDS Report</a>
                </div>
            </div>
            @endif

            <!-- Master -->
            @if($canMaster)
            <div class="menu-item {{ $isMasterActive ? 'active' : '' }}">
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
                        <a href="#" class="{{ $isGeneralMasterFlyoutActive ? 'active' : '' }}">General &nbsp;&#9658;</a>
                        <div class="sub-menu">
                            @if($user->hasPermission('master.series'))
                                <a href="{{ route('master.series') }}" class="{{ $isSeriesActive ? 'active' : '' }}">Series</a>
                            @endif
                            @if($user->hasPermission('master.measurement_unit'))
                                <a href="{{ route('master.measurement-unit') }}" class="{{ $isUnitActive ? 'active' : '' }}">Measurement Unit</a>
                            @endif
                            @if($user->hasPermission('master.shipping_status'))
                                <a href="{{ route('master.shipping-status') }}" class="{{ $isShippingActive ? 'active' : '' }}">Shipping Status</a>
                            @endif
                            @if($user->hasPermission('master.transport'))
                                <a href="#">Transport</a>
                            @endif
                            @if($user->hasPermission('master.country'))
                                <a href="{{ route('master.country') }}" class="{{ $isCountryActive ? 'active' : '' }}">Country</a>
                            @endif
                            @if($user->hasPermission('master.state'))
                                <a href="{{ route('master.state') }}" class="{{ $isStateActive ? 'active' : '' }}">State</a>
                            @endif
                            @if($user->hasPermission('master.city'))
                                <a href="{{ route('master.city') }}" class="{{ $isCityActive ? 'active' : '' }}">City</a>
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
            <div class="menu-item {{ $isToolsActive ? 'active' : '' }}">
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
            <div class="menu-item {{ $isSystemActive ? 'active' : '' }}">
                <a href="#">System</a>
                <div class="dropdown-menu">
                    @if($user->hasPermission('system.user_management'))
                        <a href="{{ route('system.user') }}" class="{{ $isUserMgmtActive ? 'active' : '' }}">User Management</a>
                    @endif
                    @if($user->hasPermission('system.role_management'))
                        <a href="{{ route('system.role') }}" class="{{ $isRoleMgmtActive ? 'active' : '' }}">Role Management</a>
                    @endif
                    @if($user->hasPermission('system.change_password'))
                        <a href="{{ route('system.change_password') }}" class="{{ $isChangePasswordActive ? 'active' : '' }}">Change Password</a>
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