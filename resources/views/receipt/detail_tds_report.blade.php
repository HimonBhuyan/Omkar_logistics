@extends('layouts.app')

@section('title', 'Receipt Detail / TDS Report - Omkaar Logistics')

@section('styles')
<style>
    /* Window Container */
    .receipt-detail-report-window {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        background: #d4d0c8;
        border: 1px solid #808080;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        font-size: 11px;
        color: #000;
        user-select: none;
    }

    /* 1. Top Red Header Bar */
    .report-header-red {
        background: #8b0000;
        color: #ffffff;
        text-align: center;
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 0.5px;
        padding: 3px 0;
        border-bottom: 1px solid #5a0000;
    }

    /* 2. Controls & Filter Bar (Grey ERP background) */
    .report-filter-panel {
        background: #d4d0c8;
        padding: 6px 12px 6px 12px;
        border-bottom: 1px solid #808080;
    }

    .filter-grid-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 5px;
        flex-wrap: wrap;
    }

    .filter-grid-row:last-child {
        margin-bottom: 0;
    }

    .ctrl-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ctrl-item label {
        font-weight: bold;
        font-size: 11px;
        color: #000;
        white-space: nowrap;
        margin: 0;
    }

    .ctrl-item input[type="text"],
    .ctrl-item input[type="date"],
    .ctrl-item select {
        height: 22px;
        border: 1px solid #7f9db9;
        background: #ffffff;
        font-size: 11px;
        padding: 1px 4px;
        color: #000;
        box-sizing: border-box;
    }

    .ctrl-item input[type="text"]:focus,
    .ctrl-item input[type="date"]:focus,
    .ctrl-item select:focus {
        outline: 1px solid #0055ff;
    }

    /* Radio button groups */
    .radio-pill-group {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        margin-left: 10px;
    }

    .radio-pill-group label {
        font-weight: bold;
        font-size: 11px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .radio-pill-group input[type="radio"] {
        margin: 0;
        cursor: pointer;
    }

    /* Square search button */
    .btn-search-box {
        width: 32px;
        height: 32px;
        background: linear-gradient(to bottom, #ffffff 0%, #e6e6e6 100%);
        border: 1px solid #7f9db9;
        border-radius: 2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.8), 0 1px 2px rgba(0,0,0,0.1);
        font-size: 15px;
        margin-left: auto;
    }

    .btn-search-box:hover {
        background: linear-gradient(to bottom, #f0f7ff 0%, #d8e8f8 100%);
        border-color: #3b6c8c;
    }

    /* 3. Main Data Table Container */
    .report-grid-container {
        width: 100%;
        overflow-x: auto;
        overflow-y: auto;
        height: calc(100vh - 290px);
        min-height: 380px;
        background: #d8e6f8; /* Soft blue fill for empty table space */
        border-top: 1px solid #808080;
        border-bottom: 1px solid #808080;
    }

    .report-data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5px;
        background: #ffffff;
    }

    .report-data-table th {
        background: #e4e2de;
        color: #000;
        font-weight: bold;
        padding: 4px 4px;
        border: 1px solid #808080;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 1px 1px rgba(0,0,0,0.08);
    }

    .report-data-table td {
        border: 1px solid #b4b4b4;
        padding: 2.5px 4px;
        white-space: nowrap;
        color: #000;
    }

    .report-data-table tr.data-row {
        cursor: pointer;
        background: #ffffff;
    }

    .report-data-table tr.data-row:hover {
        background-color: #ffffd0 !important;
    }

    .report-data-table tr.data-row.selected-row {
        background-color: #3399ff !important;
        color: #ffffff !important;
    }
    .report-data-table tr.data-row.selected-row td {
        color: #ffffff !important;
    }

    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }

    /* Total Pink Sub-total Row */
    .total-summary-row td {
        background: #fedbdb !important;
        font-weight: bold;
        border-top: 1.5px solid #808080;
        border-bottom: 1.5px solid #808080;
        color: #000 !important;
    }

    /* 4. Bottom Footer Bar */
    .report-footer-bar {
        background: #d4d0c8;
        padding: 6px 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        border-top: 1px solid #808080;
        min-height: 38px;
    }

    .btn-report-action {
        background: linear-gradient(to bottom, #ffffff 0%, #e6e6e6 100%);
        border: 1px solid #7f9db9;
        font-size: 11px;
        font-weight: bold;
        color: #000;
        padding: 3px 20px;
        cursor: pointer;
        min-width: 75px;
        height: 25px;
        border-radius: 2px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.7), 0 1px 2px rgba(0,0,0,0.1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-report-action:hover {
        background: #eef5fc;
        border-color: #3b6c8c;
    }

    @media print {
        .report-header-red,
        .report-filter-panel,
        .report-footer-bar,
        .navbar,
        footer {
            display: none !important;
        }
        .report-grid-container {
            height: auto !important;
            overflow: visible !important;
            background: #fff !important;
            border: none !important;
        }
        .report-data-table {
            border: 1px solid #000 !important;
        }
        .report-data-table th,
        .report-data-table td {
            border: 1px solid #000 !important;
            font-size: 9px !important;
            padding: 2px 3px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid" style="padding: 6px 10px;">

    <div class="receipt-detail-report-window">
        <!-- 1. Top Red Title Bar -->
        <div class="report-header-red">
            RECEIPT DETAIL / TDS REPORT
        </div>

        <!-- 2. Controls & Filters Form -->
        <form action="{{ route('report.receipt_detail_tds') }}" method="GET" id="receiptDetailFilterForm">
            <div class="report-filter-panel">
                <!-- Row 1: Series, Receipt Detail / TDS Report Radio, User, From, To, Search Button -->
                <div class="filter-grid-row">
                    <div class="ctrl-item">
                        <label for="series_input" style="min-width: 42px;">SERIES</label>
                        <input type="text" name="series" id="series_input" value="{{ request('series') }}" placeholder="SERIES" style="width: 70px; text-transform: uppercase;">
                    </div>

                    <div class="radio-pill-group" style="margin-left: 15px;">
                        <label>
                            <input type="radio" name="report_type" value="receipt_detail" {{ request('report_type', 'receipt_detail') === 'receipt_detail' ? 'checked' : '' }} onchange="document.getElementById('receiptDetailFilterForm').submit();">
                            RECEIPT DETAIL
                        </label>
                        <label style="margin-left: 10px;">
                            <input type="radio" name="report_type" value="tds_report" {{ request('report_type') === 'tds_report' ? 'checked' : '' }} onchange="document.getElementById('receiptDetailFilterForm').submit();">
                            TDS REPORT
                        </label>
                    </div>

                    <div class="ctrl-item" style="margin-left: 20px;">
                        <label for="user_select">USER</label>
                        <select name="user_id" id="user_select" style="width: 170px;">
                            <option value="all">ALL USER</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ctrl-item" style="margin-left: auto;">
                        <label for="from_date">FROM</label>
                        <input type="date" name="from_date" id="from_date" value="{{ request('from_date', $fromDate) }}" style="width: 115px;">
                    </div>

                    <div class="ctrl-item">
                        <label for="to_date">TO</label>
                        <input type="date" name="to_date" id="to_date" value="{{ request('to_date', $toDate) }}" style="width: 115px;">
                    </div>

                    <button type="submit" class="btn-search-box" title="Fetch Report">
                        🔍
                    </button>
                </div>

                <!-- Row 2: Customer, Customer Like, Non Cancel/Cancel/All Radio -->
                <div class="filter-grid-row" style="margin-top: 4px;">
                    <div class="ctrl-item">
                        <label for="customer_input" style="min-width: 60px;">CUSTOMER</label>
                        <input type="text" name="customer" id="customer_input" list="customers_datalist" value="{{ request('customer') }}" placeholder="SEARCH CUSTOMER..." style="width: 220px;">
                        <datalist id="customers_datalist">
                            @foreach ($customers as $c)
                                <option value="{{ $c }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="ctrl-item" style="margin-left: 15px;">
                        <label for="customer_like_input">CUSTOMER LIKE</label>
                        <input type="text" name="customer_like" id="customer_like_input" value="{{ request('customer_like') }}" placeholder="CUSTOMER NAME..." style="width: 180px;">
                    </div>

                    <div class="radio-pill-group" style="margin-left: 25px;">
                        <label>
                            <input type="radio" name="cancel_status" value="non_cancel" {{ request('cancel_status', 'non_cancel') == 'non_cancel' ? 'checked' : '' }}>
                            NON CANCEL
                        </label>
                        <label>
                            <input type="radio" name="cancel_status" value="cancel" {{ request('cancel_status') == 'cancel' ? 'checked' : '' }}>
                            CANCEL
                        </label>
                        <label>
                            <input type="radio" name="cancel_status" value="all" {{ request('cancel_status') == 'all' ? 'checked' : '' }}>
                            ALL
                        </label>
                    </div>
                </div>
            </div>
        </form>

        <!-- 3. Main Data Table Grid Container -->
        <div class="report-grid-container">
            <table class="report-data-table" id="receiptDetailReportTable">
                <thead>
                    <tr>
                        <th style="width: 40px;">SRNO.</th>
                        <th style="width: 45px;">SERIES</th>
                        <th style="width: 80px;">RECEIPT NO</th>
                        <th style="width: 80px;">DATE</th>
                        <th style="min-width: 180px;">RECEIPT BY</th>
                        <th style="width: 65px;">INVOICE NO</th>
                        <th style="width: 85px;">UTR NO</th>
                        <th style="width: 75px;">TDS</th>
                        <th style="width: 85px;">AMOUNT</th>
                        <th style="width: 65px;">MOP</th>
                        <th style="min-width: 140px;">BANK/ACCOUNT</th>
                        <th style="width: 80px;">NO.</th>
                        <th style="width: 80px;">CHE.DATE</th>
                        <th style="min-width: 130px;">REMARK</th>
                        <th style="width: 80px;">ENTRY BY</th>
                    </tr>
                </thead>
                <tbody>
                    @php $srNo = 1; @endphp
                    @forelse ($items as $it)
                        @php
                            $r = $it->receipt;
                            $tds = (float)$it->tds;
                            $amt = (float)$it->paid_amount;
                        @endphp
                        <tr class="data-row" onclick="selectReportRow(this)" ondblclick="window.location.href='{{ route('receipt.edit', $it->receipt_id) }}'" title="Double click to edit Receipt #{{ $r ? $r->series . '-' . $r->receipt_no : '' }}">
                            <td class="text-center">{{ $srNo++ }}</td>
                            <td class="text-center">{{ $it->series ?: ($r ? $r->series : 'A') }}</td>
                            <td class="text-center font-bold">
                                @if($r)
                                    <a href="{{ route('receipt.edit', $r->id) }}" style="color: #0044cc; text-decoration: underline; font-weight: bold;" title="Edit Receipt #{{ $r->series }}-{{ $r->receipt_no }}">
                                        {{ $r->voucher_no ?: $r->receipt_no }}
                                    </a>
                                    @if($r->status === 'draft')
                                        <span style="background: #f59e0b; color: #fff; font-size: 9px; padding: 1px 4px; border-radius: 2px; margin-left: 2px; font-weight: normal;">Draft</span>
                                    @elseif($r->status === 'cancelled')
                                        <span style="background: #ef4444; color: #fff; font-size: 9px; padding: 1px 4px; border-radius: 2px; margin-left: 2px; font-weight: normal;">Cancelled</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">{{ ($r && $r->receipt_date) ? $r->receipt_date->format('d-m-Y') : '' }}</td>
                            <td class="text-left font-bold" style="max-width: 220px; overflow: hidden; text-overflow: ellipsis;" title="{{ $r ? $r->account_name : '' }}">
                                {{ $r ? $r->account_name : '' }}
                            </td>
                            <td class="text-center font-bold" style="color: #0044cc;">
                                {{ $it->invoice_no }}
                            </td>
                            <td class="text-left" style="max-width: 100px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $it->utr_no ?: '' }}
                            </td>
                            <td class="text-right font-bold">{{ number_format($tds, 2) }}</td>
                            <td class="text-right font-bold">{{ number_format($amt, 2) }}</td>
                            <td class="text-center font-bold">{{ $r ? $r->pay_mode : '' }}</td>
                            <td class="text-left" style="max-width: 160px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $r ? ($r->bank_name ?: ($r->bank ? $r->bank->ledger_name : '')) : '' }}
                            </td>
                            <td class="text-center">{{ $r ? ($r->cheque_no ?: '') : '' }}</td>
                            <td class="text-center">{{ ($r && $r->cheque_date) ? $r->cheque_date->format('d-m-Y') : '' }}</td>
                            <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;" title="{{ $r ? $r->remark : '' }}">
                                {{ $r ? $r->remark : '' }}
                            </td>
                            <td class="text-center font-bold" style="white-space: nowrap;">
                                {{ ($r && $r->user) ? ($r->user->name ?: $r->user->username) : 'ADMIN' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" style="text-align: center; color: #666; padding: 40px; font-size: 12px; background: #fff;">
                                No records found matching the selected filters.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Total Row Matching Screenshot -->
                    <tr class="total-summary-row">
                        <td colspan="7" class="text-right font-bold" style="letter-spacing: 0.5px;">
                            Total
                        </td>
                        <td class="text-right font-bold">{{ number_format($totalTdsAmt, 2) }}</td>
                        <td class="text-right font-bold">{{ number_format($totalPaidAmt, 2) }}</td>
                        <td class="text-center"></td>
                        <td class="text-left"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-left"></td>
                        <td class="text-center"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 4. Bottom Footer Bar -->
        <div class="report-footer-bar">
            <button type="button" class="btn-report-action" onclick="window.print();">
                Print
            </button>

            <a href="{{ route('report.receipt_detail_tds.export', request()->all()) }}" class="btn-report-action" title="Export to Excel">
                Export
            </a>

            <a href="{{ route('dashboard') }}" class="btn-report-action" title="Close">
                Close
            </a>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function selectReportRow(row) {
        document.querySelectorAll('#receiptDetailReportTable tr.data-row').forEach(r => r.classList.remove('selected-row'));
        row.classList.add('selected-row');
    }
</script>
@endsection
