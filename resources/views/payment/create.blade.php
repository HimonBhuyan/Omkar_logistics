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
        width: 780px;
        max-width: 100%;
        background: #d4d0c8;
        border: 2px solid #808080;
        border-right-color: #404040;
        border-bottom-color: #404040;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        font-size: 11.5px;
        color: #000;
        user-select: none;
    }

    /* Top window title bar (small gray bar) */
    .payment-system-bar {
        background: #d4d0c8;
        color: #555;
        padding: 2px 6px;
        font-size: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #b0b0b0;
    }

    .payment-system-bar .win-btn {
        background: #d4d0c8;
        border: 1px solid #808080;
        width: 14px;
        height: 14px;
        line-height: 12px;
        text-align: center;
        font-size: 9px;
        cursor: pointer;
    }

    /* 1. Red Header Bar with Yellow Text */
    .payment-title-bar {
        background: #8b0000;
        color: #ffff00;
        text-align: center;
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 1px;
        padding: 4px 0;
        border-bottom: 1px solid #5a0000;
        position: relative;
    }

    .payment-status-badge {
        position: absolute;
        right: 8px;
        top: 3px;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 2px;
        font-weight: bold;
        text-transform: uppercase;
        background: #ffff00;
        color: #8b0000;
    }

    /* 2. Form Panel */
    .payment-form-panel {
        background: #d4d0c8;
        padding: 12px 18px 10px 18px;
    }

    .form-row {
        display: flex;
        align-items: center;
        margin-bottom: 7px;
        gap: 8px;
        flex-wrap: nowrap;
    }

    .form-label {
        font-weight: bold;
        font-size: 11.5px;
        color: #000;
        white-space: nowrap;
        text-align: right;
    }

    .form-input {
        height: 22px;
        border: 1px solid #7f9db9;
        background: #ffffff;
        font-size: 11.5px;
        padding: 1px 4px;
        color: #000;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: 1px solid #0055ff;
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
        color: #333;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .btn-lookup {
        height: 22px;
        width: 24px;
        background: #e4e2de;
        border: 1px solid #7f9db9;
        font-weight: bold;
        font-size: 11px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .btn-lookup:hover {
        background: #d0ceca;
    }

    /* 3. Action Toolbar (Bottom Right) */
    .payment-action-bar {
        background: #d4d0c8;
        padding: 6px 18px 12px 18px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 6px;
    }

    .action-icon-btn {
        width: 32px;
        height: 30px;
        background: #e4e2de;
        border: 1px solid #808080;
        border-radius: 3px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
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

    .btn-add { color: #0288d1; font-weight: bold; font-size: 18px; }
    .btn-print { color: #6a1b9a; }
    .btn-save { color: #0066cc; }
    .btn-cancel-doc { color: #d32f2f; }
    .btn-delete { color: #c62828; font-weight: bold; }
    .btn-exit { color: #2e7d32; font-weight: bold; font-size: 18px; }

    /* Autocomplete Dropdown */
    .autocomplete-list {
        position: absolute;
        background: #ffffff;
        border: 1px solid #7f9db9;
        max-height: 180px;
        overflow-y: auto;
        z-index: 10000;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        display: none;
    }

    .autocomplete-item {
        padding: 4px 8px;
        font-size: 11px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .autocomplete-item:hover, .autocomplete-item.selected {
        background: #0055ff;
        color: #ffffff;
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
            @if(isset($existingPayment))
                <span class="payment-status-badge">
                    {{ strtoupper($existingPayment->status ?? 'FINAL') }} (#{{ $existingPayment->series }}-{{ $existingPayment->payment_no }})
                </span>
            @endif
        </div>

        <!-- Main Form -->
        <form id="paymentForm" action="{{ isset($existingPayment) ? route('payment.update', $existingPayment->id) : route('payment.store') }}" method="POST">
            @csrf
            @if(isset($existingPayment))
                @method('PUT')
            @endif

            <div class="payment-form-panel">
                <!-- Row 1: Series, Payment No, Date, Time, Voucher No. -->
                <div class="form-row">
                    <label class="form-label" style="width: 50px;">Series</label>
                    <input type="text" name="series" id="series" class="form-input text-center font-bold" 
                           value="{{ old('series', isset($existingPayment) ? $existingPayment->series : ($series ?? 'A')) }}" 
                           style="width: 45px;" maxlength="5" required>

                    <label class="form-label" style="margin-left: 15px;">Payment No</label>
                    <input type="number" name="payment_no" id="payment_no" class="form-input text-center font-bold" 
                           value="{{ old('payment_no', isset($existingPayment) ? $existingPayment->payment_no : ($nextPaymentNo ?? 1)) }}" 
                           style="width: 75px;" required>

                    <label class="form-label" style="margin-left: 15px;">Date</label>
                    <input type="date" name="payment_date" id="payment_date" class="form-input" 
                           value="{{ old('payment_date', isset($existingPayment) && $existingPayment->payment_date ? $existingPayment->payment_date->format('Y-m-d') : ($currentDate ?? date('Y-m-d'))) }}" 
                           style="width: 110px;" required>

                    <input type="text" name="payment_time" id="payment_time" class="form-input text-center" 
                           value="{{ old('payment_time', isset($existingPayment) ? $existingPayment->payment_time : ($currentTime ?? date('H:i:s'))) }}" 
                           style="width: 75px;" placeholder="HH:MM:SS">

                    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 6px;">
                        <label class="form-label">Voucher No.</label>
                        <input type="text" name="voucher_no" id="voucher_no" class="form-input input-lavender" 
                               value="{{ old('voucher_no', isset($existingPayment) ? ($existingPayment->voucher_no ?: $existingPayment->payment_no) : ($nextPaymentNo ?? 1)) }}" 
                               style="width: 65px;" readonly>
                    </div>
                </div>

                <!-- Row 2: Account, Account Alias / Details -->
                <div class="form-row" style="position: relative;">
                    <label class="form-label" style="width: 50px;">Account</label>
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
                    <label class="form-label" style="width: 155px; text-align: left;">Customer Invoice Number</label>
                    <input type="text" name="customer_invoice_no" id="customer_invoice_no" class="form-input" 
                           value="{{ old('customer_invoice_no', isset($existingPayment) ? $existingPayment->customer_invoice_no : '') }}" 
                           style="width: 220px;" placeholder="ENTER CUSTOMER INVOICE NO">

                    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 6px;">
                        <label class="form-label">Customer Bill Amt.</label>
                        <input type="number" step="0.01" name="customer_bill_amt" id="customer_bill_amt" class="form-input text-right font-bold" 
                               value="{{ old('customer_bill_amt', isset($existingPayment) && $existingPayment->customer_bill_amt ? number_format($existingPayment->customer_bill_amt, 2, '.', '') : '') }}" 
                               style="width: 130px;" placeholder="0.00">
                    </div>
                </div>

                <!-- Row 4: Payment (Yellow), Deduct Amount (Readonly), Discount (Yellow) -->
                <div class="form-row">
                    <label class="form-label" style="width: 50px;">Payment</label>
                    <input type="number" step="0.01" name="payment_amount" id="payment_amount" class="form-input input-yellow text-right" 
                           value="{{ old('payment_amount', isset($existingPayment) ? number_format($existingPayment->payment_amount, 2, '.', '') : '') }}" 
                           style="width: 110px;" placeholder="0.00" required>

                    <label class="form-label" style="margin-left: 10px;">Deduct Amount :</label>
                    <input type="number" step="0.01" name="deduct_amount" id="deduct_amount" class="form-input input-readonly text-right font-bold" 
                           value="{{ old('deduct_amount', isset($existingPayment) ? number_format($existingPayment->deduct_amount ?: $existingPayment->due_amount, 2, '.', '') : '0.00') }}" 
                           style="width: 100px;" readonly>

                    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 6px;">
                        <label class="form-label">Discount</label>
                        <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-input input-yellow text-right" 
                               value="{{ old('discount_amount', isset($existingPayment) ? number_format($existingPayment->discount_amount, 2, '.', '') : '0.00') }}" 
                               style="width: 110px;">
                    </div>
                </div>

                <!-- Row 5: Pay Mode, Bank Name, Lookup :: -->
                <div class="form-row">
                    <label class="form-label" style="width: 50px;">Pay Mode</label>
                    <select name="pay_mode" id="pay_mode" class="form-input font-bold" style="width: 170px;">
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

                    <label class="form-label" style="margin-left: 10px;">Bank Name</label>
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
                    <label class="form-label" style="width: 50px;">Chq No.</label>
                    <input type="text" name="cheque_no" id="cheque_no" class="form-input" 
                           value="{{ old('cheque_no', isset($existingPayment) ? $existingPayment->cheque_no : '') }}" 
                           style="width: 250px;" placeholder="CHEQUE / UTR / REF NO">

                    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 6px;">
                        <label class="form-label">Chq Date</label>
                        <input type="date" name="cheque_date" id="cheque_date" class="form-input" 
                               value="{{ old('cheque_date', isset($existingPayment) && $existingPayment->cheque_date ? $existingPayment->cheque_date->format('Y-m-d') : ($currentDate ?? date('Y-m-d'))) }}" 
                               style="width: 110px;">
                    </div>
                </div>

                <!-- Row 8: Purpose of payment -->
                <div class="form-row">
                    <label class="form-label" style="width: 120px; text-align: left;">Purpose of payment</label>
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

                <!-- Print -->
                @if(isset($existingPayment))
                    <a href="{{ route('payment.print', $existingPayment->id) }}" target="_blank" class="action-icon-btn btn-print" title="Print Payment Voucher">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6a1b9a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8" fill="#d1c4e9"></rect>
                        </svg>
                    </a>
                @else
                    <button type="button" class="action-icon-btn btn-print" title="Print (Save First)" onclick="alert('Please save the payment voucher first to print.');">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                    </button>
                @endif

                <!-- Save / Floppy Disk -->
                <button type="submit" class="action-icon-btn btn-save" title="Save Payment Voucher (Ctrl+S)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#0066cc" stroke="#004c99" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21" fill="#ffffff"/>
                        <polyline points="7 3 7 8 15 8" fill="#cce6ff"/>
                    </svg>
                </button>

                <!-- Cancel Status -->
                @if(isset($existingPayment))
                    <button type="button" class="action-icon-btn btn-cancel-doc" title="Cancel Voucher" onclick="cancelPayment({{ $existingPayment->id }});">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d32f2f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                        </svg>
                    </button>
                @endif

                <!-- Delete -->
                @if(isset($existingPayment))
                    <button type="button" class="action-icon-btn btn-delete" title="Delete Voucher" onclick="deletePayment({{ $existingPayment->id }});">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c62828" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                @endif

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
            'name' => $a->ledger_name,
            'code' => $a->code ?? '',
            'group' => $a->under_group ?? '',
            'opening' => (float)($a->opening ?? 0),
            'bank_name' => $a->bank_name ?? '',
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

    // Auto-update Voucher No when Payment No changes
    if (paymentNoInput && voucherNoInput) {
        paymentNoInput.addEventListener('input', function() {
            voucherNoInput.value = this.value;
        });
    }

    // Autocomplete for Account
    let currentFocus = -1;

    function renderAccountItems(items) {
        accountList.innerHTML = '';
        currentFocus = -1;

        if (!items || items.length === 0) {
            accountList.style.display = 'none';
            return;
        }

        items.slice(0, 30).forEach((m, index) => {
            const div = document.createElement('div');
            div.className = 'autocomplete-item';
            div.setAttribute('data-index', index);
            div.innerHTML = `<div style="display:flex; justify-content:space-between; align-items:center;">
                <span><strong>${m.name}</strong> ${m.code ? '<span style="color:#666; font-size:10px;">(' + m.code + ')</span>' : ''}</span>
                ${m.group ? '<span style="color:#0044cc; background:#e6f0ff; padding:1px 5px; border-radius:2px; font-size:10px; font-weight:bold; margin-left:6px;">' + m.group + '</span>' : ''}
            </div>`;
            div.addEventListener('click', function() {
                selectAccount(m);
            });
            accountList.appendChild(div);
        });

        accountList.style.display = 'block';
    }

    function filterAccounts(val) {
        if (!val) {
            renderAccountItems(accountsData);
            return;
        }

        const upperVal = val.toUpperCase();
        const matches = accountsData.filter(a => 
            a.name.toUpperCase().includes(upperVal) || 
            (a.code && a.code.toUpperCase().includes(upperVal)) ||
            (a.group && a.group.toUpperCase().includes(upperVal))
        );

        renderAccountItems(matches);
    }

    if (accountInput) {
        accountInput.addEventListener('focus', function() {
            filterAccounts(this.value.trim());
        });

        accountInput.addEventListener('input', function() {
            filterAccounts(this.value.trim());
        });

        accountInput.addEventListener('keydown', function(e) {
            const items = accountList.querySelectorAll('.autocomplete-item');
            if (accountList.style.display === 'block' && items.length > 0) {
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
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target !== accountInput && !accountList.contains(e.target)) {
                accountList.style.display = 'none';
            }
        });
    }

    function setActive(items) {
        if (!items) return;
        items.forEach(item => item.classList.remove('selected'));
        if (currentFocus >= 0 && currentFocus < items.length) {
            items[currentFocus].classList.add('selected');
            items[currentFocus].scrollIntoView({ block: 'nearest' });
        }
    }

    function selectAccount(acc) {
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
