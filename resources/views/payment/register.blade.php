@extends('layouts.app')

@section('title', 'Payment Register - Omkaar Logistics')

@section('styles')
<style>
    /* Classic Desktop ERP Payment Register */
    .register-window {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        background: #d4d0c8;
        border: 1px solid #808080;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        font-size: 11px;
        color: #000;
        user-select: none;
    }

    /* 1. Red Header Bar */
    .register-title-bar {
        background: #8b0000;
        color: #ffffff;
        text-align: center;
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 0.5px;
        padding: 4px 0;
        border-bottom: 1px solid #5a0000;
    }

    /* 2. Filter Section */
    .filter-panel {
        background: #d4d0c8;
        padding: 8px 14px 6px 14px;
        border-bottom: 1px solid #999;
    }

    .filter-grid {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .filter-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .filter-label {
        font-weight: bold;
        font-size: 11px;
        color: #000;
        white-space: nowrap;
        margin: 0;
    }

    .filter-input {
        height: 22px;
        border: 1px solid #7f9db9;
        background: #ffffff;
        font-size: 11px;
        padding: 1px 4px;
        color: #000;
        box-sizing: border-box;
    }

    .filter-input:focus {
        outline: 1px solid #0055ff;
    }

    .radio-group {
        display: inline-flex;
        align-items: center;
        gap: 15px;
        margin-left: 5px;
    }

    .radio-group label {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: bold;
        font-size: 11px;
        cursor: pointer;
    }

    .btn-search-icon {
        width: 34px;
        height: 30px;
        background: #e4e2de;
        border: 1px solid #7f9db9;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 2px;
        box-shadow: 1px 1px 2px rgba(0,0,0,0.15);
    }

    .btn-search-icon:hover {
        background: #ffffff;
        border-color: #0055ff;
    }

    /* 3. Data Table Grid */
    .table-container {
        width: 100%;
        overflow-x: auto;
        overflow-y: auto;
        max-height: calc(100vh - 275px);
        min-height: 280px;
        background: #ffffff;
        border-bottom: 1px solid #808080;
    }

    .grid-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5px;
        background: #ffff00; /* Yellow background matching Image 2 */
    }

    .grid-table th {
        background: #e4e2de;
        color: #000;
        font-weight: bold;
        padding: 4px 3px;
        border: 1px solid #808080;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .grid-table td {
        border: 1px solid #7f7f00;
        padding: 3px 4px;
        white-space: nowrap;
        color: #000;
        vertical-align: middle;
        background-color: #ffff00; /* Image 2 Yellow table rows */
    }

    .grid-table tr:hover td {
        background-color: #ffff80;
    }

    .grid-table tr.cancelled-row td {
        background-color: #ffcccc !important;
        color: #990000;
    }

    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }

    /* Totals Summary Row */
    .grid-table tr.total-row td {
        background-color: #e4e2de !important;
        font-weight: bold;
        border-top: 2px solid #000;
        border-bottom: 2px solid #000;
    }

    /* 4. Bottom Toolbar */
    .register-bottom-bar {
        background: #d4d0c8;
        padding: 6px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .bottom-status-text {
        font-size: 11px;
        font-weight: bold;
        color: #333;
    }

    .bottom-action-buttons {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-report-action {
        width: 32px;
        height: 28px;
        background: #e4e2de;
        border: 1px solid #808080;
        border-radius: 2px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 1px 1px 2px rgba(0,0,0,0.15);
        text-decoration: none;
        color: #000;
    }

    .btn-report-action:hover {
        background: #ffffff;
        border-color: #0055ff;
    }

    @media print {
        .fixed-top-nav, header, nav, footer, .filter-panel, .register-bottom-bar, #top-loading-bar {
            display: none !important;
        }
        body {
            padding: 0 !important;
            background: #fff !important;
        }
        .register-window {
            border: none !important;
            box-shadow: none !important;
        }
        .table-container {
            max-height: none !important;
            overflow: visible !important;
        }
        .grid-table td {
            background-color: #ffffff !important;
        }
    }
</style>
@endsection

@section('content')
<div class="register-window">
    <!-- Red Title Bar (Matches Image 2) -->
    <div class="register-title-bar">
        Payment register
    </div>

    <!-- Filter Section (Matches Image 2) -->
    <form id="filterForm" method="GET" action="{{ route('payment.register') }}" class="filter-panel">
        <!-- Row 1: Series, User, From, To, Search button -->
        <div class="filter-grid">
            <div class="filter-item">
                <label class="filter-label">Series</label>
                <input type="text" name="series" id="series" class="filter-input text-center" 
                       value="{{ request('series', '') }}" style="width: 75px;" placeholder="ALL">
            </div>

            <div class="filter-item">
                <label class="filter-label" style="margin-left: 10px;">User</label>
                <select name="user_id" id="user_id" class="filter-input" style="width: 130px;">
                    <option value="all">All User</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name ?: $u->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-item">
                <label class="filter-label" style="margin-left: 15px;">From</label>
                <input type="date" name="from_date" id="from_date" class="filter-input" 
                       value="{{ request('from_date', $fromDate) }}" style="width: 110px;">
            </div>

            <div class="filter-item">
                <label class="filter-label" style="margin-left: 5px;">To</label>
                <input type="date" name="to_date" id="to_date" class="filter-input" 
                       value="{{ request('to_date', $toDate) }}" style="width: 110px;">
            </div>

            <div style="flex: 1; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-search-icon" title="Search / Filter">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.5">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="21" y1="21" x2="16" y2="16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Row 2: Bank Name, Supplier -->
        <div class="filter-grid" style="margin-bottom: 8px;">
            <div class="filter-item">
                <label class="filter-label" style="width: 65px;">Bank Name</label>
                <input type="text" name="bank_name" id="bank_name" class="filter-input" 
                       value="{{ request('bank_name', '') }}" list="bankList" style="width: 250px;" placeholder="SEARCH BANK NAME">
                <datalist id="bankList">
                    @foreach($bankNames as $bn)
                        <option value="{{ $bn }}">
                    @endforeach
                </datalist>
            </div>

            <div class="filter-item">
                <label class="filter-label" style="margin-left: 15px;">Supplier</label>
                <input type="text" name="supplier" id="supplier" class="filter-input" 
                       value="{{ request('supplier', '') }}" list="supplierList" style="width: 250px;" placeholder="SEARCH SUPPLIER / PARTY">
                <datalist id="supplierList">
                    @foreach($suppliers as $s)
                        <option value="{{ $s }}">
                    @endforeach
                </datalist>
            </div>
        </div>

        <!-- Row 3: Status Radios (Non Cancel, Cancel, All) -->
        <div class="filter-grid" style="margin-bottom: 0;">
            @php $cancelStatus = request('cancel_status', 'non_cancel'); @endphp
            <div class="radio-group">
                <label>
                    <input type="radio" name="cancel_status" value="non_cancel" {{ $cancelStatus === 'non_cancel' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit();">
                    Non Cancel
                </label>
                <label>
                    <input type="radio" name="cancel_status" value="cancel" {{ $cancelStatus === 'cancel' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit();">
                    Cancel
                </label>
                <label>
                    <input type="radio" name="cancel_status" value="all" {{ $cancelStatus === 'all' ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit();">
                    All
                </label>
            </div>
        </div>
    </form>

    <!-- 3. Table Grid (Matches Image 2) -->
    <div class="table-container">
        <table class="grid-table">
            <thead>
                <tr>
                    <th style="width: 35px;">Srno.</th>
                    <th style="width: 35px;">Series</th>
                    <th style="width: 60px;">Vouche...</th>
                    <th style="width: 75px;">Date</th>
                    <th style="width: 170px; text-align: left; padding-left: 5px;">Party Name</th>
                    <th style="width: 80px; text-align: right; padding-right: 5px;">Pay Amount</th>
                    <th style="width: 80px; text-align: right; padding-right: 5px;">Deduct Amount</th>
                    <th style="width: 60px; text-align: right; padding-right: 5px;">Discount</th>
                    <th style="width: 80px; text-align: right; padding-right: 5px;">Paid</th>
                    <th style="width: 50px;">MOP</th>
                    <th style="width: 110px; text-align: left; padding-left: 5px;">Bank/Acco...</th>
                    <th style="width: 90px; text-align: left; padding-left: 5px;">No.</th>
                    <th style="width: 75px;">Che.Date</th>
                    <th style="min-width: 140px; text-align: left; padding-left: 5px;">Purpose of payment</th>
                    <th style="width: 75px;">Entry By</th>
                </tr>
            </thead>
            <tbody>
                @php $sr = 1; @endphp
                @forelse($payments as $p)
                    <tr class="{{ $p->status === 'cancelled' ? 'cancelled-row' : '' }}" 
                        ondblclick="window.location='{{ route('payment.edit', $p->id) }}'" 
                        title="Double-click to open Voucher #{{ $p->series }}-{{ $p->payment_no }}"
                        style="cursor: pointer;">
                        <td class="text-center">{{ $sr++ }}</td>
                        <td class="text-center">{{ $p->series }}</td>
                        <td class="text-center font-bold">{{ $p->voucher_no ?: $p->payment_no }}</td>
                        <td class="text-center">{{ $p->payment_date ? $p->payment_date->format('d-m-Y') : '' }}</td>
                        <td class="text-left font-bold" title="{{ $p->account_name }}">
                            {{ \Illuminate\Support\Str::limit($p->account_name, 24) }}
                        </td>
                        <td class="text-right font-bold">{{ number_format($p->payment_amount, 2, '.', '') }}</td>
                        <td class="text-right font-bold">{{ number_format($p->deduct_amount ?: $p->due_amount, 2, '.', '') }}</td>
                        <td class="text-right">{{ number_format($p->discount_amount, 2, '.', '') }}</td>
                        <td class="text-right font-bold">{{ number_format($p->total_amount, 2, '.', '') }}</td>
                        <td class="text-center">{{ $p->pay_mode === 'BANK TRANSFER' ? 'Bank' : ($p->pay_mode ?: 'Bank') }}</td>
                        <td class="text-left" title="{{ $p->bank_name }}">
                            {{ \Illuminate\Support\Str::limit($p->bank_name ?: ($p->bank ? $p->bank->ledger_name : ''), 15) }}
                        </td>
                        <td class="text-left" title="{{ $p->cheque_no }}">
                            {{ \Illuminate\Support\Str::limit($p->cheque_no ?: '', 14) }}
                        </td>
                        <td class="text-center">{{ $p->cheque_date ? $p->cheque_date->format('d-m-Y') : '' }}</td>
                        <td class="text-left" title="{{ $p->remark }}">
                            {{ \Illuminate\Support\Str::limit($p->remark ?: '', 28) }}
                        </td>
                        <td class="text-center font-bold">
                            {{ strtoupper($p->user ? ($p->user->name ?: $p->user->username) : 'SUSOVAN') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" class="text-center" style="padding: 25px; background: #fff; font-size: 12px; color: #666;">
                            No payment records found for the selected filter criteria.
                        </td>
                    </tr>
                @endforelse

                <!-- Totals Row -->
                @if($payments->isNotEmpty())
                    <tr class="total-row">
                        <td colspan="5" class="text-center font-bold">Total :</td>
                        <td class="text-right font-bold">{{ number_format($totalPayAmount, 2, '.', '') }}</td>
                        <td class="text-right font-bold">{{ number_format($totalDeductAmount, 2, '.', '') }}</td>
                        <td class="text-right font-bold">{{ number_format($totalDiscount, 2, '.', '') }}</td>
                        <td class="text-right font-bold" style="color: #000080;">{{ number_format($totalPaid, 2, '.', '') }}</td>
                        <td colspan="6"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- 4. Bottom Toolbar (Matches Image 2) -->
    <div class="register-bottom-bar">
        <div class="bottom-status-text">
            Total Records: {{ $payments->count() }}
        </div>

        <div class="bottom-action-buttons">
            <!-- Print Report -->
            <button type="button" class="btn-report-action" onclick="window.print();" title="Print Payment Register">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6a1b9a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8" fill="#e1bee7"></rect>
                </svg>
            </button>

            <!-- Excel Export -->
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn-report-action" title="Export to Excel (XLSX)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" fill="#e8f5e9"/>
                    <line x1="8" y1="8" x2="16" y2="16" stroke="#2e7d32" stroke-width="2.5"/>
                    <line x1="16" y1="8" x2="8" y2="16" stroke="#2e7d32" stroke-width="2.5"/>
                </svg>
            </a>

            <!-- Exit / Back to Dashboard -->
            <a href="{{ route('dashboard') }}" class="btn-report-action" title="Exit / Close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
