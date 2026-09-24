@extends('layouts.app')

@section('title', (isset($existingPayment) ? 'Edit Payment #' . $existingPayment->series . '-' . $existingPayment->payment_no : 'Payment Voucher') . ' - Omkaar Logistics')

@section('styles')
<style>
    /* Classic Windows ERP Window styling */
    .payment-window-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 15px 0 30px 0;
        width: 100%;
    }

    .payment-window {
        width: 820px;
        max-width: 98%;
        background: #d4d0c8;
        border: 2px solid #808080;
        border-right-color: #404040;
        border-bottom-color: #404040;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        font-size: 13px;
        color: #000;
        user-select: none;
    }

    /* Top window title bar (small gray bar) */
    .payment-system-bar {
        background: #d4d0c8;
        color: #333;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #b0b0b0;
    }

    .payment-system-bar .win-btn {
        background: #d4d0c8;
        border: 1px solid #808080;
        width: 16px;
        height: 16px;
        line-height: 14px;
        text-align: center;
        font-size: 10px;
        cursor: pointer;
    }

    /* 1. Red Header Bar with Yellow Text */
    .payment-title-bar {
        background: #8b0000;
        color: #ffff00;
        text-align: center;
        font-size: 15px;
        font-weight: bold;
        letter-spacing: 1px;
        padding: 6px 0;
        border-bottom: 2px solid #5a0000;
        position: relative;
    }

    .payment-status-badge {
        position: absolute;
        right: 8px;
        top: 5px;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 2px;
        font-weight: bold;
        text-transform: uppercase;
        background: #ffff00;
        color: #8b0000;
    }

    /* 2. Form Panel */
    .payment-form-panel {
        background: #d4d0c8;
        padding: 14px 20px 12px 20px;
    }

    .form-row {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        gap: 8px;
        flex-wrap: nowrap;
    }

    .form-label {
        font-weight: bold;
        font-size: 13px !important;
        color: #000 !important;
        white-space: nowrap;
        flex-shrink: 0;
        text-align: left;
    }

    .form-input {
        height: 28px;
        border: 1.5px solid #7f9db9;
        background: #ffffff;
        font-size: 13px;
        font-weight: 600;
        padding: 2px 6px;
        color: #000;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: 1.5px solid #0055ff;
        background-color: #ffffff;
    }

    .input-yellow {
        background-color: #ffff00 !important;
        font-weight: bold;
        color: #000;
    }

    .input-lavender {
        background-color: #dcdcff !important;
        font-weight: bold;
        color: #000080;
        text-align: center;
    }

    .input-readonly {
        background-color: #ece9d8;
        color: #222;
        font-weight: 600;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .btn-lookup {
        height: 28px;
        width: 28px;
        background: #e4e2de;
        border: 1.5px solid #7f9db9;
        font-weight: bold;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        flex-shrink: 0;
    }

    .btn-lookup:hover {
        background: #d0ceca;
    }

    /* 3. Action Toolbar (Bottom Right) */
    .payment-action-bar {
        background: #d4d0c8;
        padding: 8px 20px 14px 20px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }

    .action-icon-btn {
        width: 36px;
        height: 34px;
        background: #e4e2de;
        border: 1.5px solid #808080;
        border-radius: 3px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.1s ease;
        box-shadow: 1px 1px 2px rgba(0,0,0,0.15);
        text-decoration: none;
        color: #000;
        padding: 0;
    }

    .action-icon-btn:hover {
        background: #ffffff;
        border-color: #0055ff;
        transform: translateY(-1px);
    }

    .action-icon-btn:active {
        background: #c0c0c0;
        transform: translateY(1px);
        box-shadow: inset 1px 1px 2px rgba(0,0,0,0.3);
    }

    .btn-add { color: #0288d1; font-weight: bold; font-size: 20px; }
    .btn-print { color: #6a1b9a; }
    .btn-save { color: #0066cc; }
    .btn-cancel-doc { color: #d32f2f; }
    .btn-delete { color: #c62828; font-weight: bold; }
    .btn-exit { color: #2e7d32; font-weight: bold; font-size: 18px; }

    /* Autocomplete Dropdown */
    .autocomplete-list {
        position: absolute;
        top: calc(100% + 2px);
        left: 0;
        right: 0;
        width: 100%;
        min-width: 280px;
        background: #ffffff;
        border: 1.5px solid #0055ff;
        border-radius: 2px;
        max-height: 220px;
        overflow-y: auto;
        z-index: 99999;
        box-shadow: 0 6px 16px rgba(0,0,0,0.3);
        display: none;
    }

    .autocomplete-item {
        padding: 5px 8px;
        font-size: 11px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.1s ease;
    }

    .autocomplete-item:hover, .autocomplete-item.active, .autocomplete-item.selected {
        background: #0055ff !important;
        color: #ffffff !important;
    }
    .autocomplete-item:hover span, .autocomplete-item.active span, .autocomplete-item.selected span,
    .autocomplete-item:hover .match-text, .autocomplete-item.active .match-text, .autocomplete-item.selected .match-text {
        color: #ffffff !important;
    }

    .autocomplete-item .match-text {
        font-weight: 800;
        color: #c92a2a;
        text-decoration: underline;
    }

    .autocomplete-no-match {
        padding: 6px 10px;
        color: #64748b;
        font-style: italic;
        font-size: 11px;
    }
</style>
@endsection

@section('content')
<div class="payment-window-wrapper">
    <div class="payment-window">
        <!-- Top Small Window Bar -->
        <div class="payment-system-bar">
            <span>Payment</span>
            <div class="win-btn" onclick="window.location='{{ route('dashboard') }}'" title="Close">&times;</div>
        </div>

        <!-- Red Title Bar -->
        <div class="payment-title-bar">
            PAYMENT
            <span id="paymentStatusBadge" class="payment-status-badge" style="{{ isset($existingPayment) ? '' : 'display:none;' }}">
                {{ isset($existingPayment) ? strtoupper($existingPayment->status ?? 'FINAL') . ' (#' . $existingPayment->series . '-' . $existingPayment->payment_no . ')' : '' }}
            </span>
        </div>

        <!-- Main Form -->
        <form id="paymentForm" action="{{ isset($existingPayment) ? route('payment.update', $existingPayment->id) : route('payment.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="{{ isset($existingPayment) ? 'PUT' : 'POST' }}">

            <div class="payment-form-panel">
                <!-- Row 1: Series, Payment No, Date, Time, Voucher No. -->
                <div class="form-row">
                    <label class="form-label" style="min-width: 55px;">SERIES</label>
                    <select name="series" id="series" class="form-input text-center font-bold" style="width: 75px; height: 28px;">
                        @if(isset($seriesList) && count($seriesList) > 0)
                            @foreach($seriesList as $s)
                                @if($s->name !== 'A')
                                    <option value="{{ $s->name }}" {{ old('series', isset($existingPayment) ? $existingPayment->series : ($series ?? ($defaultSeries ?? '26-27'))) == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="{{ $defaultSeries ?? '26-27' }}" selected>{{ $defaultSeries ?? '26-27' }}</option>
                        @endif
                    </select>

                    <label class="form-label" style="margin-left: 12px;">PAYMENT NO</label>
                    <input type="number" name="payment_no" id="payment_no" class="form-input text-center font-bold" 
                           value="{{ old('payment_no', isset($existingPayment) ? $existingPayment->payment_no : ($nextPaymentNo ?? 1)) }}" 
                           style="width: 75px;" required>

                    <label class="form-label" style="margin-left: 10px;">DATE</label>
                    <input type="date" name="payment_date" id="payment_date" class="form-input font-bold" 
                           value="{{ old('payment_date', isset($existingPayment) && $existingPayment->payment_date ? $existingPayment->payment_date->format('Y-m-d') : ($currentDate ?? date('Y-m-d'))) }}" 
                           style="width: 138px; min-width: 135px; padding: 2px 4px;" required>

                    <input type="text" name="payment_time" id="payment_time" class="form-input text-center" 
                           value="{{ old('payment_time', isset($existingPayment) ? $existingPayment->payment_time : ($currentTime ?? date('H:i:s'))) }}" 
                           style="width: 75px;" placeholder="HH:MM:SS">

                    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 6px;">
                        <label class="form-label">VOUCHER NO.</label>
                        <input type="text" name="voucher_no" id="voucher_no" class="form-input input-lavender" 
                               value="{{ old('voucher_no', isset($existingPayment) ? ($existingPayment->voucher_no ?: $existingPayment->payment_no) : ($nextPaymentNo ?? 1)) }}" 
                               style="width: 65px;" readonly>
                    </div>
                </div>

                <!-- Row 2: Account, Account Alias / Details -->
                <div class="form-row" style="position: relative;">
                    <label class="form-label" style="min-width: 80px;">ACCOUNT</label>
                    <div style="flex: 1; position: relative;">
                        <input type="text" name="account_name" id="account_name" class="form-input font-bold" 
                               value="{{ old('account_name', isset($existingPayment) ? $existingPayment->account_name : ($selectedAccount ? $selectedAccount->ledger_name : '')) }}" 
                               style="width: 100%;" placeholder="SELECT OR TYPE ACCOUNT / SUPPLIER NAME" autocomplete="off" required>
                        <input type="hidden" name="account_id" id="account_id" value="{{ old('account_id', isset($existingPayment) ? $existingPayment->account_id : ($selectedAccount ? $selectedAccount->id : '')) }}">
                        <div id="accountAutocompleteList" class="autocomplete-list" style="width: 100%;"></div>
                    </div>

                    <input type="text" name="account_alias" id="account_alias" class="form-input input-readonly" 
                           value="{{ old('account_alias', isset($existingPayment) ? $existingPayment->account_alias : ($selectedAccount ? $selectedAccount->code : '')) }}" 
                           style="width: 160px;" placeholder="ALIAS / CODE" readonly>
                </div>

                <!-- Row 3: Customer Invoice Number, Customer Bill Amt. -->
                <div class="form-row">
                    <label class="form-label" style="min-width: 180px;">CUSTOMER INVOICE NO.</label>
                    <input type="text" name="customer_invoice_no" id="customer_invoice_no" class="form-input" 
                           value="{{ old('customer_invoice_no', isset($existingPayment) ? $existingPayment->customer_invoice_no : '') }}" 
                           style="width: 220px;" placeholder="ENTER CUSTOMER INVOICE NO">

                    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 6px;">
                        <label class="form-label">CUSTOMER BILL AMT.</label>
                        <input type="number" step="0.01" name="customer_bill_amt" id="customer_bill_amt" class="form-input text-right font-bold" 
                               value="{{ old('customer_bill_amt', isset($existingPayment) && $existingPayment->customer_bill_amt ? number_format($existingPayment->customer_bill_amt, 2, '.', '') : '') }}" 
                               style="width: 120px;" placeholder="0">
                    </div>
                </div>

                <!-- Row 4: Payment (Yellow), Deduct Amount (Readonly), Discount (Yellow) -->
                <div class="form-row">
                    <label class="form-label" style="min-width: 80px;">PAYMENT</label>
                    <input type="number" step="0.01" name="payment_amount" id="payment_amount" class="form-input input-yellow text-right" 
                           value="{{ old('payment_amount', isset($existingPayment) ? number_format($existingPayment->payment_amount, 2, '.', '') : '') }}" 
                           style="width: 110px;" placeholder="0" required>

                    <label class="form-label" style="margin-left: 10px;">DEDUCT AMOUNT :</label>
                    <input type="number" step="0.01" name="deduct_amount" id="deduct_amount" class="form-input input-readonly text-right font-bold" 
                           value="{{ old('deduct_amount', isset($existingPayment) ? number_format($existingPayment->deduct_amount ?: $existingPayment->due_amount, 2, '.', '') : '0.00') }}" 
                           style="width: 95px;" readonly>

                    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 6px;">
                        <label class="form-label">DISCOUNT</label>
                        <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-input input-yellow text-right" 
                               value="{{ old('discount_amount', isset($existingPayment) ? number_format($existingPayment->discount_amount, 2, '.', '') : '0.00') }}" 
                               style="width: 110px;">
                    </div>
                </div>

                <!-- Row 5: Pay Mode, Bank Name, Lookup :: -->
                <div class="form-row">
                    <label class="form-label" style="min-width: 80px;">PAY MODE</label>
                    <select name="pay_mode" id="pay_mode" class="form-input font-bold" style="width: 165px;">
                        @php
                            $currentPayMode = old('pay_mode', isset($existingPayment) ? $existingPayment->pay_mode : 'BANK TRANSFER');
                        @endphp
                        <option value="BANK TRANSFER" {{ $currentPayMode === 'BANK TRANSFER' ? 'selected' : '' }}>BANK TRANSFER</option>
                        <option value="CASH" {{ $currentPayMode === 'CASH' ? 'selected' : '' }}>CASH</option>
                        <option value="CHEQUE" {{ $currentPayMode === 'CHEQUE' ? 'selected' : '' }}>CHEQUE</option>
                        <option value="NEFT/RTGS" {{ $currentPayMode === 'NEFT/RTGS' ? 'selected' : '' }}>NEFT/RTGS</option>
                        <option value="UPI" {{ $currentPayMode === 'UPI' ? 'selected' : '' }}>UPI</option>
                        <option value="DIRECT" {{ $currentPayMode === 'DIRECT' ? 'selected' : '' }}>DIRECT</option>
                    </select>

                    <label class="form-label" style="margin-left: 10px;">BANK NAME</label>
                    <select name="bank_name" id="bank_name" class="form-input font-bold" style="flex: 1;">
                        <option value="">-- SELECT BANK --</option>
                        @php
                            $selectedBank = old('bank_name', isset($existingPayment) ? $existingPayment->bank_name : 'OMKAAR CBI CD');
                        @endphp
                        @foreach($bankAccounts as $b)
                            <option value="{{ $b->ledger_name }}" {{ $selectedBank === $b->ledger_name ? 'selected' : '' }}>
                                {{ $b->ledger_name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="btn-lookup" title="Select Bank" onclick="document.getElementById('bank_name').focus();">::</button>
                </div>

                <!-- Row 7: Chq No., Chq Date -->
                <div class="form-row">
                    <label class="form-label" style="min-width: 80px;">CHQ NO.</label>
                    <input type="text" name="cheque_no" id="cheque_no" class="form-input" 
                           value="{{ old('cheque_no', isset($existingPayment) ? $existingPayment->cheque_no : '') }}" 
                           style="width: 250px;" placeholder="CHEQUE / UTR / REF NO">

                    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 6px;">
                        <label class="form-label">CHQ DATE</label>
                        <input type="date" name="cheque_date" id="cheque_date" class="form-input font-bold" 
                               value="{{ old('cheque_date', isset($existingPayment) && $existingPayment->cheque_date ? $existingPayment->cheque_date->format('Y-m-d') : ($currentDate ?? date('Y-m-d'))) }}" 
                               style="width: 138px; min-width: 135px; padding: 2px 4px;">
                    </div>
                </div>

                <!-- Row 8: Purpose of payment -->
                <div class="form-row">
                    <label class="form-label" style="min-width: 180px;">PURPOSE OF PAYMENT</label>
                    <input type="text" name="remark" id="remark" class="form-input" 
                           value="{{ old('remark', isset($existingPayment) ? $existingPayment->remark : '') }}" 
                           style="flex: 1;" placeholder="ENTER PURPOSE OF PAYMENT / BILL DETAILS">
                </div>
            </div>

            <!-- Action Toolbar (Bottom Right Icons) -->
            <div class="payment-action-bar">
                <!-- Add / New -->
                <a href="{{ route('payment.create') }}" class="action-icon-btn btn-add" title="New Payment Voucher">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#007acc" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" stroke="#007acc" stroke-width="2" fill="#e6f2ff"/>
                        <line x1="12" y1="8" x2="12" y2="16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                </a>

                <!-- Print (Can be printed before or after save) -->
                <button type="button" id="btnPrint" onclick="printPaymentVoucher();" 
                   class="action-icon-btn btn-print" 
                   title="Print Payment Voucher (A5)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6a1b9a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8" fill="#d1c4e9"></rect>
                    </svg>
                </button>

                <!-- Save / Floppy Disk -->
                <button type="submit" class="action-icon-btn btn-save" title="Save Payment Voucher (Ctrl+S)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#0066cc" stroke="#004c99" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21" fill="#ffffff"/>
                        <polyline points="7 3 7 8 15 8" fill="#cce6ff"/>
                    </svg>
                </button>

                <!-- Cancel Status -->
                <button type="button" id="btnCancel" class="action-icon-btn btn-cancel-doc" title="Cancel Voucher" 
                        style="{{ isset($existingPayment) ? '' : 'display:none;' }}" 
                        onclick="{{ isset($existingPayment) ? 'cancelPayment(' . $existingPayment->id . ');' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d32f2f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                    </svg>
                </button>

                <!-- Delete -->
                <button type="button" id="btnDelete" class="action-icon-btn btn-delete" title="Delete Voucher" 
                        style="{{ isset($existingPayment) ? '' : 'display:none;' }}" 
                        onclick="{{ isset($existingPayment) ? 'deletePayment(' . $existingPayment->id . ');' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c62828" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                <!-- Exit / Return to Dashboard -->
                <a href="{{ route('dashboard') }}" class="action-icon-btn btn-exit" title="Exit / Close">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Accounts list for autocomplete (filtered by Group Ledgers)
    const accountsData = {!! json_encode($accounts->map(function($a) {
        return [
            'id' => $a->id,
            'name' => (string)($a->ledger_name ?? ''),
            'code' => (string)($a->code ?? ''),
            'group' => (string)($a->under_group ?? ''),
            'opening' => (float)($a->opening ?? 0),
            'bank_name' => (string)($a->bank_name ?? ''),
        ];
    })->values()) !!};

    const accountInput = document.getElementById('account_name');
    const accountIdInput = document.getElementById('account_id');
    const accountAliasInput = document.getElementById('account_alias');
    const accountList = document.getElementById('accountAutocompleteList');
    
    const custBillAmtInput = document.getElementById('customer_bill_amt');
    const paymentAmtInput = document.getElementById('payment_amount');
    const deductAmtInput = document.getElementById('deduct_amount');
    const discountAmtInput = document.getElementById('discount_amount');
    const paymentNoInput = document.getElementById('payment_no');
    const voucherNoInput = document.getElementById('voucher_no');

    let lookupTimeout = null;
    let isLookingUp = false;

    // Perform live AJAX lookup when typing/changing Payment No
    function performPaymentLookup(paymentNo) {
        if (lookupTimeout) {
            clearTimeout(lookupTimeout);
        }

        const cleanNo = (paymentNo || '').toString().trim();
        if (!cleanNo || parseInt(cleanNo) <= 0) {
            resetPaymentForm(cleanNo);
            return;
        }

        const seriesEl = document.getElementById('series');
        const seriesVal = seriesEl ? seriesEl.value : '26-27';

        lookupTimeout = setTimeout(() => {
            isLookingUp = true;
            fetch(`{{ url('/payment/lookup') }}/${encodeURIComponent(cleanNo)}?series=${encodeURIComponent(seriesVal)}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Not found');
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.found && data.payment) {
                        populatePaymentData(data.payment);
                    } else {
                        resetPaymentForm(cleanNo);
                    }
                })
                .catch(err => {
                    resetPaymentForm(cleanNo);
                })
                .finally(() => {
                    isLookingUp = false;
                });
        }, 250);
    }

    function populatePaymentData(p) {
        const form = document.getElementById('paymentForm');
        if (form) form.action = p.update_url;
        const methodInput = document.getElementById('formMethod');
        if (methodInput) methodInput.value = 'PUT';

        // Keep current selected series intact as requested
        if (paymentNoInput) paymentNoInput.value = p.payment_no;
        if (voucherNoInput) voucherNoInput.value = p.voucher_no || p.payment_no;
        if (p.payment_date) document.getElementById('payment_date').value = p.payment_date;
        if (p.payment_time) document.getElementById('payment_time').value = p.payment_time;

        if (accountInput) accountInput.value = p.account_name || '';
        if (accountIdInput) accountIdInput.value = p.account_id || '';
        if (accountAliasInput) accountAliasInput.value = p.account_alias || '';

        const custInv = document.getElementById('customer_invoice_no');
        if (custInv) custInv.value = p.customer_invoice_no || '';

        if (custBillAmtInput) custBillAmtInput.value = p.customer_bill_amt > 0 ? p.customer_bill_amt.toFixed(2) : '';
        if (paymentAmtInput) paymentAmtInput.value = p.payment_amount > 0 ? p.payment_amount.toFixed(2) : '';
        if (deductAmtInput) deductAmtInput.value = p.deduct_amount ? p.deduct_amount.toFixed(2) : '0.00';
        if (discountAmtInput) discountAmtInput.value = p.discount_amount ? p.discount_amount.toFixed(2) : '0.00';

        const payModeSelect = document.getElementById('pay_mode');
        if (payModeSelect && p.pay_mode) payModeSelect.value = p.pay_mode;

        const bankSelect = document.getElementById('bank_name');
        if (bankSelect && p.bank_name) bankSelect.value = p.bank_name;

        const chqNo = document.getElementById('cheque_no');
        if (chqNo) chqNo.value = p.cheque_no || '';

        const chqDate = document.getElementById('cheque_date');
        if (chqDate && p.cheque_date) chqDate.value = p.cheque_date;

        const remarkEl = document.getElementById('remark');
        if (remarkEl) remarkEl.value = p.remark || '';

        // Status badge
        const badge = document.getElementById('paymentStatusBadge');
        if (badge) {
            badge.textContent = `${(p.status || 'FINAL').toUpperCase()} (#${p.series}-${p.payment_no})`;
            badge.style.display = 'inline-block';
        }

        // Print button is always active and prints current live form data
        const btnPrint = document.getElementById('btnPrint');
        if (btnPrint) {
            btnPrint.title = 'Print Payment Voucher (A5)';
        }

        // Cancel & Delete buttons
        const btnCancel = document.getElementById('btnCancel');
        if (btnCancel) {
            btnCancel.style.display = 'inline-flex';
            btnCancel.onclick = () => cancelPayment(p.id);
        }

        const btnDelete = document.getElementById('btnDelete');
        if (btnDelete) {
            btnDelete.style.display = 'inline-flex';
            btnDelete.onclick = () => deletePayment(p.id);
        }
    }

    function resetPaymentForm(paymentNo) {
        const form = document.getElementById('paymentForm');
        if (form) form.action = "{{ route('payment.store') }}";
        const methodInput = document.getElementById('formMethod');
        if (methodInput) methodInput.value = 'POST';

        if (voucherNoInput) voucherNoInput.value = paymentNo || '';

        if (accountInput) accountInput.value = '';
        if (accountIdInput) accountIdInput.value = '';
        if (accountAliasInput) accountAliasInput.value = '';

        const custInv = document.getElementById('customer_invoice_no');
        if (custInv) custInv.value = '';

        if (custBillAmtInput) custBillAmtInput.value = '';
        if (paymentAmtInput) paymentAmtInput.value = '';
        if (deductAmtInput) deductAmtInput.value = '0.00';
        if (discountAmtInput) discountAmtInput.value = '0.00';

        const chqNo = document.getElementById('cheque_no');
        if (chqNo) chqNo.value = '';

        const remarkEl = document.getElementById('remark');
        if (remarkEl) remarkEl.value = '';

        const badge = document.getElementById('paymentStatusBadge');
        if (badge) badge.style.display = 'none';

        const btnPrint = document.getElementById('btnPrint');
        if (btnPrint) {
            btnPrint.title = 'Print Payment Voucher (A5)';
        }

        const btnCancel = document.getElementById('btnCancel');
        if (btnCancel) btnCancel.style.display = 'none';

        const btnDelete = document.getElementById('btnDelete');
        if (btnDelete) btnDelete.style.display = 'none';
    }

    // Print Payment Voucher directly (works before save or after save)
    function printPaymentVoucher() {
        const form = document.getElementById('paymentForm');
        if (!form) return;

        // Create temporary form to POST to preview endpoint in new window
        const previewForm = document.createElement('form');
        previewForm.method = 'POST';
        previewForm.action = '{{ route('payment.preview') }}';
        previewForm.target = '_blank';
        previewForm.style.display = 'none';

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        previewForm.appendChild(csrfInput);

        // Copy all input and select fields from current paymentForm
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            if (key === '_method') continue; // don't send PUT to preview
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            previewForm.appendChild(input);
        }

        document.body.appendChild(previewForm);
        previewForm.submit();
        document.body.removeChild(previewForm);
    }

    // Attach listeners for Payment No & Series changes
    if (paymentNoInput) {
        paymentNoInput.addEventListener('input', function() {
            if (voucherNoInput) voucherNoInput.value = this.value;
            performPaymentLookup(this.value.trim());
        });

        paymentNoInput.addEventListener('change', function() {
            performPaymentLookup(this.value.trim());
        });

        paymentNoInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performPaymentLookup(this.value.trim());
                if (accountInput) accountInput.focus();
            }
        });
    }

    const seriesSelect = document.getElementById('series');
    if (seriesSelect) {
        seriesSelect.addEventListener('change', function() {
            const seriesVal = this.value;
            fetch(`{{ url('/payment/next-no') }}?series=${encodeURIComponent(seriesVal)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.next_payment_no) {
                        if (paymentNoInput) paymentNoInput.value = data.next_payment_no;
                        if (voucherNoInput) voucherNoInput.value = data.next_payment_no;
                        resetPaymentForm(data.next_payment_no);
                    }
                })
                .catch(() => {
                    if (paymentNoInput && paymentNoInput.value) {
                        performPaymentLookup(paymentNoInput.value.trim());
                    }
                });
        });
    }

    // Autocomplete for Account (prefix match first, exact highlight like Bilty page)
    let currentFocus = -1;

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, function(m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
        });
    }

    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const idx = text.toLowerCase().indexOf(query.toLowerCase());
        if (idx === -1) return escapeHtml(text);
        const before = text.substring(0, idx);
        const match = text.substring(idx, idx + query.length);
        const after = text.substring(idx + query.length);
        return `${escapeHtml(before)}<span class="match-text">${escapeHtml(match)}</span>${escapeHtml(after)}`;
    }

    function renderAccountItems(items, query) {
        if (!accountList) return;
        accountList.innerHTML = '';
        currentFocus = -1;

        if (!items || items.length === 0) {
            accountList.innerHTML = `<div class="autocomplete-no-match">No accounts matching "${escapeHtml(query)}"</div>`;
            accountList.style.display = 'block';
            return;
        }

        items.slice(0, 35).forEach((m, index) => {
            const div = document.createElement('div');
            div.className = 'autocomplete-item';
            div.setAttribute('data-index', index);
            
            const highlightedName = query ? highlightMatch(m.name || '', query) : escapeHtml(m.name || '');
            const codeHtml = m.code ? `<span style="color:#666; font-size:10px; margin-left:4px;">(${escapeHtml(m.code)})</span>` : '';
            const groupHtml = m.group ? `<span style="color:#0044cc; background:#e6f0ff; padding:1px 5px; border-radius:2px; font-size:10px; font-weight:bold; margin-left:6px; white-space:nowrap;">${escapeHtml(m.group)}</span>` : '';

            div.innerHTML = `<div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                <span><strong>${highlightedName}</strong>${codeHtml}</span>
                ${groupHtml}
            </div>`;
            
            div.addEventListener('mousedown', function(e) {
                e.preventDefault();
                selectAccount(m);
            });
            div.addEventListener('click', function(e) {
                selectAccount(m);
            });
            accountList.appendChild(div);
        });

        accountList.style.display = 'block';
    }

    function filterAccounts(val) {
        if (!accountList) return;

        const query = (val || '').toString().trim();
        if (query.length === 0) {
            accountList.innerHTML = '';
            accountList.style.display = 'none';
            currentFocus = -1;
            return;
        }

        const q = query.toLowerCase();

        // 1. Filter items that contain the query
        let matches = (accountsData || []).filter(a => {
            if (!a) return false;
            const name = (a.name || '').toLowerCase();
            const code = (a.code || '').toLowerCase();
            const group = (a.group || '').toLowerCase();
            return name.includes(q) || code.includes(q) || group.includes(q);
        });

        // 2. Sort prefix match first (items starting with the typed letters come first, exactly like Bilty location matching)
        matches.sort((a, b) => {
            const nameA = (a.name || '').toLowerCase();
            const nameB = (b.name || '').toLowerCase();
            const aStarts = nameA.startsWith(q);
            const bStarts = nameB.startsWith(q);
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            return nameA.localeCompare(nameB);
        });

        renderAccountItems(matches, query);
    }

    if (accountInput) {
        accountInput.addEventListener('focus', function() {
            const val = (this.value || '').trim();
            if (val.length > 0) {
                filterAccounts(val);
            } else {
                if (accountList) accountList.style.display = 'none';
            }
        });

        accountInput.addEventListener('input', function() {
            filterAccounts(this.value);
        });

        accountInput.addEventListener('keyup', function(e) {
            if (!['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) {
                filterAccounts(this.value);
            }
        });

        accountInput.addEventListener('paste', function() {
            setTimeout(() => filterAccounts(this.value), 20);
        });

        accountInput.addEventListener('keydown', function(e) {
            if (!accountList || accountList.style.display !== 'block') return;
            const items = accountList.querySelectorAll('.autocomplete-item');
            if (!items || items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentFocus++;
                if (currentFocus >= items.length) currentFocus = 0;
                setActive(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentFocus--;
                if (currentFocus < 0) currentFocus = items.length - 1;
                setActive(items);
            } else if (e.key === 'Enter') {
                if (currentFocus > -1 && items[currentFocus]) {
                    e.preventDefault();
                    items[currentFocus].click();
                }
            } else if (e.key === 'Escape') {
                accountList.style.display = 'none';
            }
        });

        document.addEventListener('click', function(e) {
            if (accountList && e.target !== accountInput && !accountList.contains(e.target)) {
                accountList.style.display = 'none';
            }
        });
    }

    function setActive(items) {
        if (!items) return;
        items.forEach(item => {
            item.classList.remove('selected');
            item.classList.remove('active');
        });
        if (currentFocus >= 0 && currentFocus < items.length) {
            items[currentFocus].classList.add('active');
            items[currentFocus].classList.add('selected');
            items[currentFocus].scrollIntoView({ block: 'nearest' });
        }
    }

    function selectAccount(acc) {
        if (!acc) return;
        accountInput.value = acc.name;
        accountIdInput.value = acc.id;
        accountAliasInput.value = acc.code || '';
        accountList.style.display = 'none';

        // Set customer bill amount if available
        if (acc.opening > 0) {
            if (custBillAmtInput && (!custBillAmtInput.value || parseFloat(custBillAmtInput.value) === 0)) {
                custBillAmtInput.value = acc.opening.toFixed(2);
            }
            calculateDeduct();
        }

        // Move to next input field
        const custInv = document.getElementById('customer_invoice_no');
        if (custInv) {
            custInv.focus();
            if (typeof custInv.select === 'function') custInv.select();
        }
    }

    // Calculation: Customer Bill Amt - Payment - Discount = Deduct Amount
    function calculateDeduct() {
        const billAmt = parseFloat(custBillAmtInput && custBillAmtInput.value ? custBillAmtInput.value : 0) || 0;
        const payment = parseFloat(paymentAmtInput && paymentAmtInput.value ? paymentAmtInput.value : 0) || 0;
        const discount = parseFloat(discountAmtInput && discountAmtInput.value ? discountAmtInput.value : 0) || 0;

        if (billAmt > 0 || payment > 0) {
            const deduct = billAmt - (payment + discount);
            if (deductAmtInput) {
                deductAmtInput.value = deduct.toFixed(2);
            }
        } else {
            if (deductAmtInput) {
                deductAmtInput.value = '0.00';
            }
        }
    }

    if (custBillAmtInput) custBillAmtInput.addEventListener('input', calculateDeduct);
    if (paymentAmtInput) paymentAmtInput.addEventListener('input', calculateDeduct);
    if (discountAmtInput) discountAmtInput.addEventListener('input', calculateDeduct);

    // Auto-select text on click/focus for quick input
    [custBillAmtInput, paymentAmtInput, discountAmtInput].forEach(function(el) {
        if (el) {
            el.addEventListener('focus', function() {
                this.select();
            });
        }
    });

    // Keyboard Shortcuts (Ctrl+S to save)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
            document.getElementById('paymentForm').submit();
        }
    });

    // Cancel Payment Voucher
    function cancelPayment(id) {
        Swal.fire({
            title: 'Cancel Payment?',
            text: 'Are you sure you want to mark this Payment Voucher as CANCELLED?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d32f2f',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/payment/cancel/${id}`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Delete Payment Voucher
    function deletePayment(id) {
        Swal.fire({
            title: 'Delete Payment?',
            text: 'This will permanently delete this Payment Voucher record!',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#c62828',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete permanently'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/payment/destroy/${id}`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
