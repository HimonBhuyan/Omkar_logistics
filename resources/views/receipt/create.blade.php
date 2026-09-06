@extends('layouts.app')

@section('title', (isset($existingReceipt) ? 'Edit Receipt #' . $existingReceipt->series . '-' . $existingReceipt->receipt_no : 'Receipt Voucher') . ' - Omkaar Logistics')

@section('styles')
<style>
    /* Classic ERP Window styling */
    .receipt-window {
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
    .receipt-title-bar {
        background: #8b0000;
        color: #ffffff;
        text-align: center;
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 0.5px;
        padding: 4px 0;
        border-bottom: 1px solid #5a0000;
        position: relative;
    }

    .receipt-status-badge {
        position: absolute;
        right: 10px;
        top: 3px;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 2px;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* 2. Controls & Form Panels */
    .receipt-panel {
        background: #d4d0c8;
        padding: 8px 14px 6px 14px;
    }

    .receipt-form-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }

    .ctrl-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ctrl-group label {
        font-weight: bold;
        font-size: 11px;
        color: #000;
        white-space: nowrap;
        margin: 0;
    }

    .ctrl-group input[type="text"],
    .ctrl-group input[type="number"],
    .ctrl-group input[type="date"],
    .ctrl-group select {
        height: 22px;
        border: 1px solid #7f9db9;
        background: #ffffff;
        font-size: 11px;
        padding: 1px 4px;
        color: #000;
        box-sizing: border-box;
    }

    .ctrl-group input[type="text"]:focus,
    .ctrl-group input[type="number"]:focus,
    .ctrl-group input[type="date"]:focus,
    .ctrl-group select:focus {
        outline: 1px solid #0055ff;
    }

    .input-lavender {
        background: #dcdcff !important;
        font-weight: bold;
    }

    .input-yellow {
        background: #ffffc0 !important;
        font-weight: bold;
    }

    .input-bright-yellow {
        background: #ffff00 !important;
        font-weight: bold;
    }

    /* 3. Main Data Table */
    .receipt-table-container {
        width: 100%;
        overflow-x: auto;
        overflow-y: auto;
        height: auto;
        max-height: calc(100vh - 320px);
        min-height: 60px;
        background: #ffffff;
        border-top: 1px solid #808080;
        border-bottom: 1px solid #808080;
    }

    .receipt-grid-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5px;
        background: #ffffff;
    }

    .receipt-grid-table th {
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

    .receipt-grid-table td {
        border: 1px solid #b4b4b4;
        padding: 2px 3px;
        white-space: nowrap;
        color: #000;
        vertical-align: middle;
    }

    .receipt-grid-table tr.data-row:hover {
        background-color: #ffffd0;
    }

    .receipt-grid-table tr.data-row.active-row {
        background-color: #e6f0fa;
    }

    .row-pointer {
        width: 14px;
        text-align: center;
        font-size: 9px;
        color: #0044cc;
        cursor: pointer;
    }

    .table-cell-input {
        width: 100%;
        height: 20px;
        border: 1px solid #7f9db9;
        font-size: 10.5px;
        padding: 1px 3px;
        box-sizing: border-box;
        text-align: right;
    }

    .table-cell-input:focus {
        outline: 1px solid #0055ff;
        background: #ffffd8;
    }

    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }

    /* 4. Bottom Payment & Summary Panels */
    .receipt-bottom-panel {
        background: #d4d0c8;
        padding: 6px 14px;
        border-top: 1px solid #808080;
    }

    .summary-box-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }

    .summary-group-left, .summary-group-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .summary-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .summary-item label {
        font-weight: bold;
        font-size: 11px;
        color: #000;
        margin: 0;
    }

    .summary-item input {
        height: 22px;
        border: 1px solid #7f9db9;
        font-size: 11px;
        font-weight: bold;
        padding: 1px 4px;
        text-align: right;
        box-sizing: border-box;
    }

    /* 5. Button Bar */
    .receipt-button-bar {
        background: #d4d0c8;
        padding: 6px 14px 10px 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        border-top: 1px solid #808080;
    }

    .btn-desktop-cyan {
        background: linear-gradient(to bottom, #b3e5fc 0%, #81d4fa 50%, #4fc3f7 100%);
        border: 1px solid #0288d1;
        border-radius: 2px;
        color: #000000;
        font-weight: bold;
        font-size: 11px;
        padding: 3px 20px;
        min-width: 65px;
        height: 25px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.7), 0 1px 2px rgba(0,0,0,0.15);
        text-decoration: none;
    }

    .btn-desktop-cyan:hover {
        background: linear-gradient(to bottom, #e1f5fe 0%, #b3e5fc 50%, #81d4fa 100%);
        border-color: #01579b;
        color: #000;
    }

    .btn-desktop-cyan:active {
        background: #4fc3f7;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.3);
    }
</style>
@endsection

@section('content')
<div class="container-fluid" style="padding: 6px 10px;">

    <!-- Receipt Main Window Container -->
    <div class="receipt-window">
        <!-- 1. Top Red Header Bar -->
        <div class="receipt-title-bar">
            RECEIPT
            @if(isset($existingReceipt))
                @if($existingReceipt->status === 'draft')
                    <span class="receipt-status-badge" style="background: #f59e0b; color: #fff;">DRAFT</span>
                @elseif($existingReceipt->status === 'cancelled')
                    <span class="receipt-status-badge" style="background: #ef4444; color: #fff;">CANCELLED</span>
                @else
                    <span class="receipt-status-badge" style="background: #10b981; color: #fff;">FINALIZED</span>
                @endif
            @endif
        </div>

        <form action="{{ isset($existingReceipt) ? route('receipt.update', $existingReceipt->id) : route('receipt.store') }}" method="POST" id="receiptForm">
            @csrf
            @if(isset($existingReceipt))
                @method('PUT')
            @endif

            <input type="hidden" name="status" id="formStatus" value="{{ isset($existingReceipt) ? $existingReceipt->status : 'final' }}">
            <input type="hidden" name="account_id" id="account_id_val" value="{{ isset($existingReceipt) ? $existingReceipt->account_id : ($selectedAccount ? $selectedAccount->id : '') }}">

            <!-- 2. Controls & Form Panel (Top Rows) -->
            <div class="receipt-panel">
                <!-- Row 1: Series, Receipt No., Date, Time, Voucher No. -->
                <div class="receipt-form-row">
                    <div class="ctrl-group">
                        <label for="series_input" style="min-width: 40px;">Series</label>
                        <input type="text" name="series" id="series_input" value="{{ old('series', isset($existingReceipt) ? $existingReceipt->series : $series) }}" style="width: 70px; text-transform: uppercase;" required>
                    </div>

                    <div class="ctrl-group" style="margin-left: 10px;">
                        <label for="receipt_no_input">Receipt No.</label>
                        <input type="number" name="receipt_no" id="receipt_no_input" value="{{ old('receipt_no', isset($existingReceipt) ? $existingReceipt->receipt_no : $nextReceiptNo) }}" style="width: 75px;" required>
                    </div>

                    <div class="ctrl-group" style="margin-left: 20px;">
                        <label for="receipt_date_input">Date</label>
                        <input type="date" name="receipt_date" id="receipt_date_input" value="{{ old('receipt_date', $currentDate) }}" style="width: 120px;" required>
                    </div>

                    <div class="ctrl-group">
                        <input type="text" name="receipt_time" id="receipt_time_input" value="{{ old('receipt_time', $currentTime) }}" style="width: 80px; text-align: center;">
                    </div>

                    <div class="ctrl-group" style="margin-left: auto;">
                        <label for="voucher_no_input">Voucher No.</label>
                        <input type="text" name="voucher_no" id="voucher_no_input" class="input-lavender" value="{{ old('voucher_no', isset($existingReceipt) ? $existingReceipt->voucher_no : (isset($existingReceipt) ? $existingReceipt->receipt_no : $nextReceiptNo)) }}" style="width: 85px;">
                    </div>
                </div>

                <!-- Row 2: Account (Debtor Party) & Mobile -->
                <div class="receipt-form-row" style="margin-top: 4px;">
                    <div class="ctrl-group" style="flex-grow: 1;">
                        <label for="account_input" style="min-width: 40px;">Account</label>
                        <input type="text" name="account_name" id="account_input" list="accounts_datalist" value="{{ old('account_name', isset($existingReceipt) ? $existingReceipt->account_name : ($selectedAccount ? $selectedAccount->ledger_name : '')) }}" placeholder="Search Debtor Account / Party..." style="flex-grow: 1; max-width: 420px; font-weight: bold; color: #000080;" autocomplete="off" required>
                        <datalist id="accounts_datalist">
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->ledger_name }}" data-id="{{ $acc->id }}" data-mobile="{{ $acc->mobile ?: $acc->phone_o }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="ctrl-group" style="margin-left: auto;">
                        <label for="mobile_input">Mobile</label>
                        <input type="text" name="mobile" id="mobile_input" class="input-yellow" value="{{ old('mobile', isset($existingReceipt) ? $existingReceipt->mobile : ($selectedAccount ? ($selectedAccount->mobile ?: $selectedAccount->phone_o) : '')) }}" style="width: 220px;" placeholder="Party Mobile No.">
                    </div>
                </div>
            </div>

            <!-- 3. Pending Invoices Data Table -->
            <div class="receipt-table-container">
                <table class="receipt-grid-table" id="receiptInvoicesTable">
                    <thead>
                        <tr>
                            <th style="width: 16px;"></th>
                            <th style="width: 32px;">SrNo</th>
                            <th style="width: 42px;">Series</th>
                            <th style="width: 65px;">Invoice No</th>
                            <th style="width: 85px;">Gross Amt</th>
                            <th style="width: 80px;">GST Amt</th>
                            <th style="width: 85px;">Bill Amt</th>
                            <th style="width: 75px;">Old Paid</th>
                            <th style="width: 85px;">Due Amount</th>
                            <th style="width: 75px;">Discount</th>
                            <th style="width: 70px;">TDS</th>
                            <th style="width: 85px;">Paid</th>
                            <th style="width: 90px;">UTR No</th>
                            <th style="width: 50px;">Full Pay</th>
                            <th style="width: 80px;">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTableBody">
                        <!-- Populated dynamically via JS when Account is selected or in Edit Mode -->
                        <tr id="noInvoicesRow">
                            <td colspan="15" style="text-align: center; color: #777; padding: 20px; font-size: 11.5px;">
                                Please select an <b>Account</b> above to load outstanding unsettled invoices.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 4. Summary & Payment Panel -->
            <div class="receipt-bottom-panel">
                <!-- Row 1: Bill Amt, Due Amt, TDS Amt, Rect Amt, Balance -->
                <div class="summary-box-row">
                    <div class="summary-group-left">
                        <div class="summary-item">
                            <label>Bill Amt.</label>
                            <input type="text" name="bill_amount" id="sum_bill_amt" value="{{ old('bill_amount', isset($existingReceipt) ? number_format($existingReceipt->bill_amount, 2, '.', '') : '0.00') }}" style="width: 105px;" readonly>
                        </div>
                        <div class="summary-item" style="margin-left: 10px;">
                            <label>Due Amt.</label>
                            <input type="text" name="due_amount" id="sum_due_amt" value="{{ old('due_amount', isset($existingReceipt) ? number_format($existingReceipt->due_amount, 2, '.', '') : '0.00') }}" style="width: 105px;" readonly>
                        </div>
                    </div>

                    <div class="summary-group-right">
                        <div class="summary-item">
                            <label>TDS Amt</label>
                            <input type="text" name="tds_amount" id="sum_tds_amt" class="input-bright-yellow" value="{{ old('tds_amount', isset($existingReceipt) ? number_format($existingReceipt->tds_amount, 2, '.', '') : '0.00') }}" style="width: 95px;" readonly>
                        </div>
                        <div class="summary-item" style="margin-left: 10px;">
                            <label>Rect Amt</label>
                            <input type="text" name="receipt_amount" id="sum_rect_amt" class="input-bright-yellow" value="{{ old('receipt_amount', isset($existingReceipt) ? number_format($existingReceipt->receipt_amount, 2, '.', '') : '0.00') }}" style="width: 115px; font-size: 12.5px;" readonly>
                        </div>
                        <div class="summary-item" style="margin-left: 10px;">
                            <label>Balance</label>
                            <input type="text" name="balance_amount" id="sum_balance_amt" value="{{ old('balance_amount', isset($existingReceipt) ? number_format($existingReceipt->balance_amount, 2, '.', '') : '0.00') }}" style="width: 95px;" readonly>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Pay Mode, Bank Name, Chq No, Chq Date -->
                <div class="receipt-form-row" style="margin-top: 6px;">
                    <div class="ctrl-group">
                        <label for="pay_mode_select" style="min-width: 60px;">Pay Mode</label>
                        <select name="pay_mode" id="pay_mode_select" style="width: 120px; font-weight: bold;" onchange="handlePayModeChange(this.value)">
                            <option value="CASH" {{ old('pay_mode', isset($existingReceipt) ? $existingReceipt->pay_mode : 'CASH') === 'CASH' ? 'selected' : '' }}>CASH</option>
                            <option value="BANK" {{ old('pay_mode', isset($existingReceipt) ? $existingReceipt->pay_mode : '') === 'BANK' ? 'selected' : '' }}>BANK</option>
                            <option value="CHEQUE" {{ old('pay_mode', isset($existingReceipt) ? $existingReceipt->pay_mode : '') === 'CHEQUE' ? 'selected' : '' }}>CHEQUE</option>
                            <option value="NEFT/RTGS" {{ old('pay_mode', isset($existingReceipt) ? $existingReceipt->pay_mode : '') === 'NEFT/RTGS' ? 'selected' : '' }}>NEFT/RTGS</option>
                            <option value="UPI" {{ old('pay_mode', isset($existingReceipt) ? $existingReceipt->pay_mode : '') === 'UPI' ? 'selected' : '' }}>UPI</option>
                        </select>
                    </div>

                    <div class="ctrl-group" style="margin-left: 12px;">
                        <label for="bank_name_select">Bank Name</label>
                        <select name="bank_name" id="bank_name_select" style="width: 180px;">
                            <option value="">Select Bank</option>
                            @foreach ($bankAccounts as $bank)
                                <option value="{{ $bank->ledger_name }}" {{ old('bank_name', isset($existingReceipt) ? $existingReceipt->bank_name : '') === $bank->ledger_name ? 'selected' : '' }}>{{ $bank->ledger_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ctrl-group" style="margin-left: 12px;">
                        <label for="cheque_no_input">:: Chq No</label>
                        <input type="text" name="cheque_no" id="cheque_no_input" value="{{ old('cheque_no', isset($existingReceipt) ? $existingReceipt->cheque_no : '') }}" placeholder="Chq/Ref No." style="width: 110px;">
                    </div>

                    <div class="ctrl-group" style="margin-left: 12px;">
                        <label for="cheque_date_input">Chq Date</label>
                        <input type="date" name="cheque_date" id="cheque_date_input" value="{{ old('cheque_date', isset($existingReceipt) && $existingReceipt->cheque_date ? $existingReceipt->cheque_date->format('Y-m-d') : $currentDate) }}" style="width: 115px;">
                    </div>
                </div>

                <!-- Row 3: Remark -->
                <div class="receipt-form-row" style="margin-top: 4px;">
                    <div class="ctrl-group" style="width: 100%;">
                        <label for="remark_input" style="min-width: 60px;">Remark</label>
                        <input type="text" name="remark" id="remark_input" value="{{ old('remark', isset($existingReceipt) ? $existingReceipt->remark : '') }}" placeholder="Enter Narration / Notes..." style="flex-grow: 1;">
                    </div>
                </div>
            </div>

            <!-- 5. Bottom Cyan Button Bar -->
            <div class="receipt-button-bar">
                <a href="{{ route('receipt.create') }}" class="btn-desktop-cyan" title="New Receipt Voucher">
                    New
                </a>

                <button type="submit" class="btn-desktop-cyan" id="btnSubmitReceipt" title="Save Receipt">
                    {{ isset($existingReceipt) ? 'Update' : 'Save' }}
                </button>

                @if(isset($existingReceipt))
                    <button type="button" class="btn-desktop-cyan" onclick="confirmCancelReceipt()" title="Cancel Receipt">
                        Cancel
                    </button>

                    <button type="button" class="btn-desktop-cyan" onclick="confirmDeleteReceipt()" title="Delete Receipt">
                        Delete
                    </button>
                @else
                    <button type="button" class="btn-desktop-cyan" disabled style="opacity: 0.6; cursor: not-allowed;">
                        Cancel
                    </button>
                    <button type="button" class="btn-desktop-cyan" disabled style="opacity: 0.6; cursor: not-allowed;">
                        Delete
                    </button>
                @endif

                <a href="{{ route('receipt.register') }}" class="btn-desktop-cyan" title="Exit to Receipt Register">
                    Exit
                </a>
            </div>
        </form>

        @if(isset($existingReceipt))
            <!-- Hidden Cancel Form -->
            <form id="cancelReceiptForm" action="{{ route('receipt.cancel', $existingReceipt->id) }}" method="POST" style="display: none;">
                @csrf
            </form>

            <!-- Hidden Delete Form -->
            <form id="deleteReceiptForm" action="{{ route('receipt.destroy', $existingReceipt->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentInvoices = [];
    const isEditMode = {{ isset($existingReceipt) ? 'true' : 'false' }};
    const currentReceiptId = {{ isset($existingReceipt) ? $existingReceipt->id : 'null' }};

    document.addEventListener('DOMContentLoaded', function() {
        const accountInput = document.getElementById('account_input');
        if (accountInput) {
            function triggerAccountSelection(val) {
                if (!val || val.trim() === '') return;
                const opts = document.querySelectorAll('#accounts_datalist option');
                for (let o of opts) {
                    if (o.value.toLowerCase() === val.trim().toLowerCase()) {
                        const accId = o.dataset.id;
                        const accMob = o.dataset.mobile;
                        if (accId) document.getElementById('account_id_val').value = accId;
                        if (accMob) document.getElementById('mobile_input').value = accMob;
                        break;
                    }
                }
                loadPendingInvoices(val.trim());
            }

            accountInput.addEventListener('change', function() {
                triggerAccountSelection(this.value);
            });

            accountInput.addEventListener('input', function() {
                const val = this.value;
                const opts = document.querySelectorAll('#accounts_datalist option');
                for (let o of opts) {
                    if (o.value.toLowerCase() === val.trim().toLowerCase()) {
                        triggerAccountSelection(val);
                        break;
                    }
                }
            });

            // Initial load if account is present
            if (accountInput.value.trim() !== '') {
                triggerAccountSelection(accountInput.value.trim());
            }
        }
    });

    function loadPendingInvoices(accountName) {
        if (!accountName || accountName.trim() === '') {
            renderInvoicesTable([]);
            return;
        }

        const url = new URL('{{ route('receipt.pending_invoices') }}', window.location.origin);
        url.searchParams.set('account_name', accountName.trim());
        const accId = document.getElementById('account_id_val')?.value;
        if (accId) {
            url.searchParams.set('account_id', accId);
        }
        if (currentReceiptId) {
            url.searchParams.set('receipt_id', currentReceiptId);
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.mobile) {
                        document.getElementById('mobile_input').value = data.mobile;
                    }
                    currentInvoices = data.invoices || [];
                    renderInvoicesTable(currentInvoices);
                }
            })
            .catch(err => {
                console.error('Error fetching pending invoices:', err);
            });
    }

    function renderInvoicesTable(invoices) {
        const tbody = document.getElementById('invoicesTableBody');
        tbody.innerHTML = '';

        if (!invoices || invoices.length === 0) {
            tbody.innerHTML = `
                <tr id="noInvoicesRow">
                    <td colspan="15" style="text-align: center; color: #777; padding: 20px; font-size: 11.5px;">
                        No unsettled invoices found for this account.
                    </td>
                </tr>
            `;
            recalculateSummaryTotals();
            return;
        }

        invoices.forEach((inv, idx) => {
            const tr = document.createElement('tr');
            tr.className = 'data-row ' + (idx === 0 ? 'active-row' : '');
            tr.onclick = function() {
                document.querySelectorAll('#receiptInvoicesTable tr.data-row').forEach(r => r.classList.remove('active-row'));
                this.classList.add('active-row');
            };

            const srNo = idx + 1;
            const gross = parseFloat(inv.gross_amount || 0).toFixed(2);
            const gst = parseFloat(inv.gst_amount || 0).toFixed(2);
            const bill = parseFloat(inv.bill_amount || 0).toFixed(2);
            const oldPaid = parseFloat(inv.old_paid || 0).toFixed(2);
            const due = parseFloat(inv.due_amount || 0).toFixed(2);
            const discount = parseFloat(inv.discount || 0).toFixed(2);
            const tds = parseFloat(inv.tds || 0).toFixed(2);
            const paid = parseFloat(inv.paid !== undefined ? inv.paid : inv.due_amount).toFixed(2);
            const utr = inv.utr_no || '';
            const isFull = inv.is_full_pay !== undefined ? inv.is_full_pay : true;
            const balance = parseFloat(inv.balance !== undefined ? inv.balance : 0).toFixed(2);

            tr.innerHTML = `
                <td class="row-pointer">${idx === 0 ? '▶' : ''}</td>
                <td class="text-center">${srNo}</td>
                <td class="text-center font-bold">${inv.series || 'A'}</td>
                <td class="text-center font-bold" style="color: #0044cc;">${inv.invoice_no}</td>
                <td class="text-right">${gross}</td>
                <td class="text-right">${gst}</td>
                <td class="text-right font-bold row-bill-amt" data-val="${bill}">${bill}</td>
                <td class="text-right">${oldPaid}</td>
                <td class="text-right font-bold row-due-amt" data-val="${due}">${due}</td>
                <td>
                    <input type="number" step="0.01" class="table-cell-input row-discount" name="items[${idx}][discount]" value="${discount}" oninput="handleRowChange(${idx})">
                </td>
                <td>
                    <input type="number" step="0.01" class="table-cell-input row-tds" name="items[${idx}][tds]" value="${tds}" oninput="handleRowChange(${idx})">
                </td>
                <td>
                    <input type="number" step="0.01" class="table-cell-input row-paid font-bold" name="items[${idx}][paid]" value="${paid}" oninput="handleRowPaidChange(${idx})">
                </td>
                <td>
                    <input type="text" class="table-cell-input" style="text-align: left;" name="items[${idx}][utr_no]" value="${utr}" placeholder="UTR No">
                </td>
                <td class="text-center">
                    <input type="hidden" name="items[${idx}][is_full_pay]" value="0">
                    <input type="checkbox" name="items[${idx}][is_full_pay]" value="1" class="row-full-pay" ${isFull ? 'checked' : ''} onchange="handleFullPayToggle(${idx}, this.checked)">
                </td>
                <td class="text-right font-bold row-balance" data-val="${balance}">
                    <input type="hidden" name="items[${idx}][balance]" class="row-balance-input" value="${balance}">
                    <input type="hidden" name="items[${idx}][invoice_id]" value="${inv.invoice_id}">
                    <input type="hidden" name="items[${idx}][series]" value="${inv.series}">
                    <input type="hidden" name="items[${idx}][invoice_no]" value="${inv.invoice_no}">
                    <input type="hidden" name="items[${idx}][gross_amount]" value="${gross}">
                    <input type="hidden" name="items[${idx}][gst_amount]" value="${gst}">
                    <input type="hidden" name="items[${idx}][bill_amount]" value="${bill}">
                    <input type="hidden" name="items[${idx}][old_paid]" value="${oldPaid}">
                    <input type="hidden" name="items[${idx}][due_amount]" value="${due}">
                    <span class="balance-text">${balance}</span>
                </td>
            `;

            tbody.appendChild(tr);
        });

        recalculateSummaryTotals();
    }

    function handleRowChange(idx) {
        const tr = document.querySelectorAll('#receiptInvoicesTable tbody tr.data-row')[idx];
        if (!tr) return;

        const due = parseFloat(tr.querySelector('.row-due-amt')?.dataset.val || 0);
        const discount = parseFloat(tr.querySelector('.row-discount')?.value || 0);
        const tds = parseFloat(tr.querySelector('.row-tds')?.value || 0);
        const fullPayCheck = tr.querySelector('.row-full-pay');

        if (fullPayCheck && fullPayCheck.checked) {
            const netPaid = Math.max(0, due - discount - tds);
            const paidInput = tr.querySelector('.row-paid');
            if (paidInput) paidInput.value = netPaid.toFixed(2);
            updateRowBalance(tr, due, discount, tds, netPaid);
        } else {
            const paid = parseFloat(tr.querySelector('.row-paid')?.value || 0);
            updateRowBalance(tr, due, discount, tds, paid);
        }

        recalculateSummaryTotals();
    }

    function handleRowPaidChange(idx) {
        const tr = document.querySelectorAll('#receiptInvoicesTable tbody tr.data-row')[idx];
        if (!tr) return;

        const due = parseFloat(tr.querySelector('.row-due-amt')?.dataset.val || 0);
        const discount = parseFloat(tr.querySelector('.row-discount')?.value || 0);
        const tds = parseFloat(tr.querySelector('.row-tds')?.value || 0);
        const paid = parseFloat(tr.querySelector('.row-paid')?.value || 0);

        const fullPayCheck = tr.querySelector('.row-full-pay');
        if (fullPayCheck) {
            const expectedPaid = Math.max(0, due - discount - tds);
            fullPayCheck.checked = (Math.abs(paid - expectedPaid) < 0.01 && paid > 0);
        }

        updateRowBalance(tr, due, discount, tds, paid);
        recalculateSummaryTotals();
    }

    function handleFullPayToggle(idx, isChecked) {
        const tr = document.querySelectorAll('#receiptInvoicesTable tbody tr.data-row')[idx];
        if (!tr) return;

        const due = parseFloat(tr.querySelector('.row-due-amt')?.dataset.val || 0);
        const discount = parseFloat(tr.querySelector('.row-discount')?.value || 0);
        const tds = parseFloat(tr.querySelector('.row-tds')?.value || 0);
        const paidInput = tr.querySelector('.row-paid');

        if (isChecked) {
            const netPaid = Math.max(0, due - discount - tds);
            if (paidInput) paidInput.value = netPaid.toFixed(2);
            updateRowBalance(tr, due, discount, tds, netPaid);
        } else {
            if (paidInput) paidInput.value = '0.00';
            updateRowBalance(tr, due, discount, tds, 0);
        }

        recalculateSummaryTotals();
    }

    function updateRowBalance(tr, due, discount, tds, paid) {
        const balance = Math.max(0, due - discount - tds - paid);
        const balSpan = tr.querySelector('.balance-text');
        const balInput = tr.querySelector('.row-balance-input');
        const balCell = tr.querySelector('.row-balance');

        if (balSpan) balSpan.textContent = balance.toFixed(2);
        if (balInput) balInput.value = balance.toFixed(2);
        if (balCell) balCell.dataset.val = balance.toFixed(2);
    }

    function recalculateSummaryTotals() {
        let totalBill = 0;
        let totalDue = 0;
        let totalTds = 0;
        let totalRect = 0;
        let totalBalance = 0;

        const rows = document.querySelectorAll('#receiptInvoicesTable tbody tr.data-row');
        rows.forEach(tr => {
            const bill = parseFloat(tr.querySelector('.row-bill-amt')?.dataset.val || 0);
            const due = parseFloat(tr.querySelector('.row-due-amt')?.dataset.val || 0);
            const tds = parseFloat(tr.querySelector('.row-tds')?.value || 0);
            const paid = parseFloat(tr.querySelector('.row-paid')?.value || 0);
            const balance = parseFloat(tr.querySelector('.row-balance-input')?.value || 0);

            totalBill += bill;
            totalDue += due;
            totalTds += tds;
            totalRect += paid;
            totalBalance += balance;
        });

        document.getElementById('sum_bill_amt').value = totalBill.toFixed(2);
        document.getElementById('sum_due_amt').value = totalDue.toFixed(2);
        document.getElementById('sum_tds_amt').value = totalTds.toFixed(2);
        document.getElementById('sum_rect_amt').value = totalRect.toFixed(2);
        document.getElementById('sum_balance_amt').value = totalBalance.toFixed(2);
    }

    function handlePayModeChange(mode) {
        const bankSelect = document.getElementById('bank_name_select');
        const chqNoInput = document.getElementById('cheque_no_input');
        if (mode === 'CASH') {
            if (bankSelect) bankSelect.value = '';
            if (chqNoInput) chqNoInput.placeholder = 'N/A';
        } else {
            if (chqNoInput) chqNoInput.placeholder = (mode === 'CHEQUE') ? 'Cheque No.' : 'UTR / Ref No.';
        }
    }

    function confirmCancelReceipt() {
        Swal.fire({
            title: 'Cancel Receipt Voucher?',
            text: 'This will mark the receipt as CANCELLED and restore the invoice due amounts.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Cancel Receipt'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancelReceiptForm').submit();
            }
        });
    }

    function confirmDeleteReceipt() {
        Swal.fire({
            title: 'Delete Receipt Voucher?',
            text: 'This will permanently remove the receipt record from the database.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete Permanently'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteReceiptForm').submit();
            }
        });
    }
</script>
@endsection
