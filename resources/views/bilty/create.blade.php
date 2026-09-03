@extends('layouts.app')

@section('title', 'Lorry Receipt (Bilty) Entry - Omkaar Logistics')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
<style>
    /* Autocomplete Input & Dropdown Styling */
    .autocomplete-wrapper {
        position: relative;
        width: 100%;
    }
    .autocomplete-input {
        width: 100%;
        padding: 6px 10px;
        font-size: 12px;
        border: 1px solid var(--border-color, #d1d5db);
        border-radius: 6px;
        background: #ffffff;
        color: #333;
        outline: none;
        transition: all 0.2s ease;
        height: 32px;
        box-sizing: border-box;
    }
    .autocomplete-input:focus {
        border-color: var(--primary-color, #0f3460);
        box-shadow: 0 0 6px rgba(15, 52, 96, 0.15);
    }
    .autocomplete-dropdown {
        position: absolute;
        top: calc(100% + 2px);
        left: 0;
        right: 0;
        max-height: 220px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        z-index: 99999;
        display: none;
    }
    .autocomplete-item {
        padding: 7px 12px;
        font-size: 12px;
        color: #1e293b;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.1s ease, color 0.1s ease;
    }
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    .autocomplete-item:hover,
    .autocomplete-item.active {
        background-color: #f1f5f9;
        color: var(--primary-color, #0f3460);
        font-weight: 600;
    }
    .autocomplete-item .match-text {
        font-weight: 700;
        color: var(--secondary-color, #c92a2a);
        text-decoration: underline;
    }
    .autocomplete-item .item-meta {
        font-size: 11px;
        color: #64748b;
        margin-left: 8px;
        font-weight: normal;
    }
    .autocomplete-no-match {
        padding: 8px 12px;
        font-size: 12px;
        color: #94a3b8;
        font-style: italic;
    }
    .bilty-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(15, 52, 96, 0.1);
        overflow: hidden;
    }

    /* Red Bilty Title Bar */
    .bilty-header-bar {
        background: var(--secondary-color);
        color: white;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 600;
        font-size: 15px;
        border-bottom: 2px solid #b32e44;
    }

    .bilty-header-inputs {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .bilty-header-inputs div {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bilty-header-inputs input, .bilty-header-inputs select {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 13px;
        color: #333;
        font-weight: 500;
        outline: none;
    }

    .bilty-body {
        padding: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 20px;
    }

    /* Column Section Headers */
    .section-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        position: relative;
    }

    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--primary-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 6px;
        display: flex;
        justify-content: space-between;
    }

    .grid-fields-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 10px;
    }

    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group-custom label {
        font-size: 11px;
        font-weight: 600;
        color: #4b5563;
    }

    .form-group-custom input, .form-group-custom select, .form-group-custom textarea {
        padding: 6px 10px;
        font-size: 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: #ffffff;
        color: #333;
        outline: none;
        transition: all 0.2s ease;
    }

    .form-group-custom input:focus, .form-group-custom select:focus, .form-group-custom textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 6px rgba(15, 52, 96, 0.1);
    }

    .billing-party-group {
        grid-column: span 2;
    }

    /* Paid/To Pay/TBB Segmented Control */
    .billing-options {
        display: flex;
        gap: 15px;
        align-items: center;
        background: #edf2f7;
        padding: 8px 15px;
        border-radius: 6px;
        margin-bottom: 12px;
    }

    .billing-options label {
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }

    .billing-options input[type="radio"] {
        accent-color: var(--primary-color);
    }

    /* Consignment Grid Styles */
    .grid-container {
        margin: 25px 0;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow-x: auto;
        background: white;
    }

    .bilty-grid {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        text-align: left;
    }

    .bilty-grid th {
        background: #f1f5f9;
        color: #334155;
        font-weight: 600;
        padding: 10px;
        border-bottom: 2px solid #cbd5e1;
        white-space: nowrap;
    }

    .bilty-grid td {
        padding: 8px 10px;
        border-bottom: 1px solid #e2e8f0;
    }

    .bilty-grid input, .bilty-grid select {
        width: 100%;
        padding: 4px 6px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 12px;
        outline: none;
    }

    .input-st, .input-rc, .input-sc {
        min-width: 80px;
        text-align: right;
    }
    .input-dd {
        min-width: 85px;
        text-align: right;
    }

    .bilty-grid input:focus, .bilty-grid select:focus {
        border-color: var(--primary-color);
    }

    .btn-delete-row {
        background: #ef4444;
        color: white;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        transition: background 0.2s ease;
    }

    .btn-delete-row:hover {
        background: #dc2626;
    }

    .grid-actions {
        padding: 10px;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
    }

    .btn-add-row {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-add-row:hover {
        opacity: 0.9;
    }

    /* Summary & Settlement Grid */
    .totals-and-settlement {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 25px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
    }

    .totals-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }

    .net-amount-card {
        background: #fef08a; /* Yellow highlight box matching Net */
        border: 2px solid #eab308;
        padding: 12px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-weight: bold;
    }

    .net-amount-card label {
        font-size: 12px;
        color: #854d0e;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .net-amount-card input {
        background: transparent;
        border: none;
        font-size: 24px;
        text-align: center;
        width: 100%;
        color: #854d0e;
        font-weight: 800;
        outline: none;
    }

    .settlement-panel {
        border-left: 1px solid #cbd5e1;
        padding-left: 25px;
    }

    .balance-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-radius: 6px;
        font-weight: 700;
        margin-top: 15px;
        font-size: 14px;
    }

    .balance-unpaid {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .balance-paid {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    /* Floating bottom action buttons */
    .bilty-actions-footer {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        padding: 20px;
        border-top: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .btn-footer {
        padding: 10px 24px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-save {
        background: var(--primary-color);
        color: white;
    }

    .btn-save:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn-reset {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-reset:hover {
        background: #cbd5e1;
        transform: translateY(-1px);
    }

    /* Print styling */
    .print-actions {
        display: flex;
        gap: 10px;
    }

    /* Loader styling overlay */
    .bilty-loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(255, 255, 255, 0.7);
        z-index: 99999;
        display: none;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(3px);
        transition: all 0.3s ease;
    }

    .bilty-spinner {
        width: 45px;
        height: 45px;
        border: 4px solid rgba(15, 52, 96, 0.1);
        border-top-color: var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 10px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .btn-view-receipt {
        background: #0f3460;
        color: white;
        text-decoration: none;
    }

    .btn-view-receipt:hover {
        background: #162447;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
@if (session('success'))
    <div style="background:#d1fae5; border:1px solid #34d399; color:#065f46; padding:15px; border-radius:8px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <strong>Success!</strong> {{ session('success') }}
        </div>
        @if (session('print_id'))
            <a href="{{ route('bilty.print', session('print_id')) }}" id="printTabTrigger" target="_blank" style="background:#059669; color:#fff; padding:6px 12px; border-radius:4px; text-decoration:none; font-weight:700; font-size:12px; display:flex; align-items:center; gap:6px;">
                🖨 Print Bill (A5 Layout)
            </a>
            @if (session('print_immediate'))
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        const trigger = document.getElementById('printTabTrigger');
                        if (trigger) {
                            // Creating mock user click event bypasses standard browser popup blockers
                            const clickEvent = new MouseEvent('click', {
                                view: window,
                                bubbles: true,
                                cancelable: true
                            });
                            trigger.dispatchEvent(clickEvent);
                        }
                    });
                </script>
            @endif
        @endif
    </div>
@endif

@if (session('error'))
    <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:15px; border-radius:8px; margin-bottom:20px;">
        <strong>Error!</strong> {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:15px; border-radius:8px; margin-bottom:20px;">
        <strong>Validation Errors:</strong>
        <ul style="margin-left: 20px; margin-top: 5px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bilty-card">
    <form action="{{ route('bilty.store') }}" method="POST" id="biltyForm" novalidate>
        @csrf
        
@include('common.cn_form', ['actionUrl' => route('bilty.store')])
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let rowIndex = 1;

    // Master data arrays for high-speed autocomplete
    const locationsList = [
        @foreach ($locations as $loc)
            { id: {{ $loc->id }}, name: @json($loc->name) },
        @endforeach
    ];

    const consignorsList = [
        @foreach ($consignors as $p)
            { 
                id: {{ $p->id }}, 
                name: @json($p->name), 
                mobile: @json($p->mobile ?? ''), 
                gstin: @json($p->gst_no ?? $p->gstin ?? ''), 
                address: @json($p->address ?? '') 
            },
        @endforeach
    ];

    const consigneesList = [
        @foreach ($consignees as $p)
            { 
                id: {{ $p->id }}, 
                name: @json($p->name), 
                mobile: @json($p->mobile ?? ''), 
                gstin: @json($p->gst_no ?? $p->gstin ?? ''), 
                address: @json($p->address ?? '') 
            },
        @endforeach
    ];

    const partiesList = [
        @foreach ($parties as $p)
            { 
                id: {{ $p->id }}, 
                name: @json($p->name) 
            },
        @endforeach
    ];

    const vehiclesList = [
        @foreach ($vehicles as $v)
            @json($v),
        @endforeach
    ];

    // Toggle showing Billing Party input when T.B.B. or Paid is selected
    function toggleBillingParty() {
        const checkedEl = document.querySelector('input[name="billing_type"]:checked');
        const overlay = document.getElementById('formBlockedOverlay');
        const billingWrapper = document.getElementById('billing_party_wrapper');
        const billingId = document.getElementById('billing_party_id');
        
        let hasData = false;
        const consignor = document.getElementById('consignor_id');
        const consignee = document.getElementById('consignee_id');
        const fromLoc = document.getElementById('from_location_id');
        const toLoc = document.getElementById('to_location_id');
        
        if ((consignor && consignor.value) || (consignee && consignee.value) || (fromLoc && fromLoc.value) || (toLoc && toLoc.value)) {
            hasData = true;
        }

        const successAlert = document.querySelector('div[style*="background:#d1fae5"]');
        if (successAlert) {
            hasData = true;
        }

        if (!checkedEl && !hasData) {
            overlay.style.display = 'flex';
            return;
        }
        
        overlay.style.display = 'none';

        const billingType = checkedEl ? checkedEl.value : null;
        if (billingType === 'T.B.B.') {
            billingWrapper.style.display = 'flex';
            // Empty and lock rate inputs as read-only
            document.querySelectorAll('.input-rate').forEach(input => {
                input.value = '0.00';
                input.readOnly = true;
                input.style.backgroundColor = '#eaeaea';
            });
        } else if (billingType === 'Paid' || billingType === 'To Pay') {
            billingWrapper.style.display = 'flex';
            
            // Explicitly enable and unlock all rate input fields
            document.querySelectorAll('.input-rate').forEach(input => {
                input.readOnly = false;
                input.disabled = false;
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.style.backgroundColor = '#ffffff';
                input.style.color = '#333';
            });

            // Restore editable rate/weight fields based on each row's unit setting
            document.querySelectorAll('.grid-row').forEach(row => {
                const wSelect = row.querySelector('.input-unit');
                if (wSelect) {
                    handleUnitChange(wSelect);
                }
            });
        } else {
            billingWrapper.style.display = 'none';
            if (billingId) billingId.value = '';
            if (window.billingPartyAuto) window.billingPartyAuto.clear();
            
            // Explicitly enable and unlock all rate input fields
            document.querySelectorAll('.input-rate').forEach(input => {
                input.readOnly = false;
                input.disabled = false;
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.style.backgroundColor = '#ffffff';
                input.style.color = '#333';
            });

            // Restore editable rate/weight fields based on each row's unit setting
            document.querySelectorAll('.grid-row').forEach(row => {
                const wSelect = row.querySelector('.input-unit');
                if (wSelect) {
                    handleUnitChange(wSelect);
                }
            });
        }
        calculateAll();
    }

    // Add row to items table
    function addRow() {
        const body = document.getElementById('gridBody');
        const newRow = document.createElement('tr');
        newRow.className = 'grid-row';
        newRow.innerHTML = `
            <td>
                <input type="number" name="items[${rowIndex}][no_of_pkgs]" class="input-no_of_pkgs calc-trigger" required min="1" value="1">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][packing]" placeholder="Box/Bag/Roll" value="Box">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][description]" placeholder="Goods Description" value="Goods">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][invoice_no]" placeholder="Inv No">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][invoice_value]" class="input-invoice_value" value="0.00" step="0.01">
            </td>
            <td>
                <select name="items[${rowIndex}][unit]" class="input-unit" onchange="handleUnitChange(this)">
                    <option value="KG">KG</option>
                    <option value="Fixed">Fixed</option>
                </select>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][qty]" class="input-qty calc-trigger" required min="0" step="0.001" value="0">
            </td>
            <td class="weight-col-cell">
                <input type="number" name="items[${rowIndex}][weight_val]" class="input-weight_val calc-trigger" step="0.001" value="0">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][rate]" class="input-rate calc-trigger" required min="0.00" step="0.01" value="0.00">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][st]" class="input-st calc-trigger" value="0.00" step="0.01" style="min-width: 80px; text-align: right;">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][rc]" class="input-rc calc-trigger" value="0.00" step="0.01" style="min-width: 80px; text-align: right;">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][sc]" class="input-sc calc-trigger" value="0.00" step="0.01" style="min-width: 80px; text-align: right;">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][dd]" class="input-dd calc-trigger" value="0.00" step="0.01" style="min-width: 85px; text-align: right;">
            </td>
            <td>
                <button type="button" class="btn-delete-row" onclick="removeRow(this)">&times;</button>
            </td>
        `;
        body.appendChild(newRow);
        rowIndex++;
        
        // Attach change listeners to new row
        attachListenersToRow(newRow);
        
        // Explicitly set the initial enabled/disabled status of weight inputs
        const newWeightSelect = newRow.querySelector('.input-unit');
        if (newWeightSelect) {
            handleUnitChange(newWeightSelect);
        }
        calculateAll();
    }

    // Remove row from items table
    function removeRow(button) {
        const row = button.closest('tr');
        const rows = document.querySelectorAll('.grid-row');
        if (rows.length > 1) {
            row.remove();
            calculateAll();
        } else {
            alert('At least one consignment row is required.');
        }
    }

    // Attach calculation listeners to inputs
    function attachListenersToRow(row) {
        row.querySelectorAll('.calc-trigger').forEach(input => {
            input.addEventListener('input', calculateAll);
        });
        const pkgsInput = row.querySelector('.input-no_of_pkgs');
        if (pkgsInput) {
            pkgsInput.addEventListener('input', function() {
                const wSelect = row.querySelector('.input-unit');
                const qtyInput = row.querySelector('.input-qty');
                if (wSelect && wSelect.value === 'Fixed' && qtyInput) {
                    qtyInput.value = this.value;
                }
            });
        }
        const wSelect = row.querySelector('.input-unit');
        if (wSelect) {
            wSelect.addEventListener('change', function() {
                handleUnitChange(this);
            });
            // Initial call to set fields enabled state correctly
            handleUnitChange(wSelect);
        }
    }

    // Recalculate all billing totals
    function calculateAll() {
        let totalPackages = 0;
        let totalQty = 0.000;
        let grossAmount = 0.00;
        let totalST = 0.00;
        let totalRC = 0.00;
        let totalSC = 0.00;
        let totalDD = 0.00;

        document.querySelectorAll('.grid-row').forEach(row => {
            const pkgs = parseInt(row.querySelector('.input-no_of_pkgs').value) || 0;
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const rate = parseFloat(row.querySelector('.input-rate').value) || 0;
            
            const st = parseFloat(row.querySelector('.input-st').value) || 0;
            const rc = parseFloat(row.querySelector('.input-rc').value) || 0;
            const sc = parseFloat(row.querySelector('.input-sc').value) || 0;
            const dd = parseFloat(row.querySelector('.input-dd').value) || 0;

            totalPackages += pkgs;
            totalQty += qty;
            grossAmount += (qty * rate);
            
            totalST += st;
            totalRC += rc;
            totalSC += sc;
            totalDD += dd;
        });

        // Set summary read-only fields
        document.getElementById('total_packages').value = totalPackages;
        document.getElementById('total_qty').value = totalQty.toFixed(3);
        document.getElementById('gross_amount').value = grossAmount.toFixed(2);
        
        document.getElementById('st_charge').value = totalST.toFixed(2);
        document.getElementById('rc_charge').value = totalRC.toFixed(2);
        document.getElementById('sc_charge').value = totalSC.toFixed(2);
        document.getElementById('dd_charge').value = totalDD.toFixed(2);

        // Read Round Off
        const roundOff = parseFloat(document.getElementById('round_off').value) || 0;

        // Calculate Net Amount
        const netAmount = grossAmount + totalST + totalRC + totalSC + totalDD + roundOff;
        document.getElementById('net_amount').value = netAmount.toFixed(2);

        // Calculate payments & balance
        const cash = parseFloat(document.getElementById('cash_amount').value) || 0;
        const card = parseFloat(document.getElementById('card_amount').value) || 0;
        const upi = parseFloat(document.getElementById('upi_chq_amount').value) || 0;
        
        const balance = netAmount - (cash + card + upi);
        document.getElementById('balance_amount').value = balance.toFixed(2);
        document.getElementById('balanceText').innerText = '₹ ' + balance.toFixed(2);

        // Style balance box based on due amount
        const balanceBox = document.getElementById('balanceBox');
        if (Math.abs(balance) < 0.01) {
            balanceBox.className = 'balance-box balance-paid';
        } else {
            balanceBox.className = 'balance-box balance-unpaid';
        }
    }

    // Fetch and auto-populate party details (Mobile, Address, GSTIN)
    function fetchPartyDetails(prefix, partyId) {
        const mobileInput = document.getElementById(`${prefix}_mobile`);
        const gstinInput = document.getElementById(`${prefix}_gstin`);
        const addressInput = document.getElementById(`${prefix}_address`);

        if (!partyId) {
            if (mobileInput) mobileInput.value = '';
            if (gstinInput) gstinInput.value = '';
            if (addressInput) addressInput.value = '';
            return;
        }

        fetch(`{{ url('/bilty/party-details') }}/${partyId}`)
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                if (mobileInput) mobileInput.value = data.mobile || '';
                if (gstinInput) gstinInput.value = data.gstin || '';
                if (addressInput) addressInput.value = data.address || '';
            })
            .catch(err => {
                console.error('Error fetching party details:', err);
            });
    }

    function handleUnitChange(selectEl) {
        const row = selectEl.closest('tr');
        if (!row) return;
        const qtyInput = row.querySelector('.input-qty');
        const rateInput = row.querySelector('.input-rate');
        const weightCell = row.querySelector('.weight-col-cell');
        const weightInput = row.querySelector('.input-weight_val');
        if (!qtyInput) return;

        // Toggle table headers visibility for weight column dynamically
        const weightHeaders = document.querySelectorAll('.weight-col');
        
        if (selectEl.value === 'KG') {
            // Hides weight column when KG
            if (weightCell) weightCell.style.display = 'none';
            if (weightInput) {
                weightInput.readOnly = true;
                weightInput.value = '0';
                weightInput.style.backgroundColor = '#eaeaea';
            }
            weightHeaders.forEach(th => th.style.display = 'none');

            // Enables Weight/Fixed and Rate inputs for KG type
            qtyInput.readOnly = false;
            qtyInput.removeAttribute('readonly');
            qtyInput.removeAttribute('disabled');
            qtyInput.style.backgroundColor = '#ffffff';
            qtyInput.style.color = '#333';
            
            if (qtyInput.value === '0.000' || qtyInput.value === '0.00' || qtyInput.value === '0') {
                qtyInput.value = '0';
            }

            if (rateInput) {
                rateInput.readOnly = false;
                rateInput.removeAttribute('readonly');
                rateInput.removeAttribute('disabled');
                rateInput.style.backgroundColor = '#ffffff';
                rateInput.style.color = '#333';
            }
        } else {
            // Shows weight column when Fixed
            if (weightCell) weightCell.style.display = 'table-cell';
            if (weightInput) {
                weightInput.readOnly = false;
                weightInput.removeAttribute('readonly');
                weightInput.removeAttribute('disabled');
                weightInput.style.backgroundColor = '#ffffff';
            }
            weightHeaders.forEach(th => th.style.display = 'table-cell'); // use table-cell to match headers structure

            // Enables Weight/Fixed input for Fixed types, defaults value to NoOfPkgs
            const pkgsInput = row.querySelector('.input-no_of_pkgs');
            if (pkgsInput && pkgsInput.value) {
                qtyInput.value = pkgsInput.value;
            } else {
                qtyInput.value = '0';
            }
            qtyInput.readOnly = false;
            qtyInput.removeAttribute('readonly');
            qtyInput.removeAttribute('disabled');
            qtyInput.style.backgroundColor = '#ffffff';
            qtyInput.style.color = '#333';
            
            if (rateInput) {
                rateInput.readOnly = false;
                rateInput.removeAttribute('readonly');
                rateInput.removeAttribute('disabled');
                rateInput.style.backgroundColor = '#ffffff';
                rateInput.style.color = '#333';
            }
        }
        calculateAll();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Force window to scroll to top immediately on refresh
        window.scrollTo(0, 0);
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        // Configure overlay visibility and form locking state based on initial input values
        toggleBillingParty();

        // Attach calculators to initial row
        document.querySelectorAll('.grid-row').forEach(attachListenersToRow);
        
        // Attach calc triggers to summary / payment inputs
        document.querySelectorAll('.calc-trigger').forEach(input => {
            input.addEventListener('input', calculateAll);
        });



        // Save as Draft function
        window.saveAsDraft = function() {
            const biltyNo = document.getElementById('bilty_no');
            if (!biltyNo || !biltyNo.value.trim() || parseInt(biltyNo.value, 10) < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'C.N. Number Required',
                    text: 'Please enter a Consignment Note (C.N. No) before saving as draft.',
                    confirmButtonColor: '#0f3460'
                }).then(() => {
                    if (biltyNo) biltyNo.focus();
                });
                return false;
            }

            // Set form status to draft
            document.getElementById('bilty_status').value = 'draft';

            // Ensure fallback values so empty calculation inputs don't fail
            if (!document.getElementById('invoice_date').value) {
                document.getElementById('invoice_date').value = new Date().toISOString().split('T')[0];
            }
            
            // Ensure numeric inputs are at least 0.00
            ['total_packages', 'total_qty', 'gross_amount', 'st_charge', 'rc_charge', 'sc_charge', 'dd_charge', 'round_off', 'net_amount', 'balance_amount', 'cash_amount', 'card_amount', 'upi_chq_amount'].forEach(id => {
                const el = document.getElementById(id);
                if (el && (!el.value || isNaN(parseFloat(el.value)))) {
                    el.value = '0.00';
                }
            });

            const draftBtn = document.getElementById('btnSaveDraft');
            if (draftBtn) {
                draftBtn.disabled = true;
                draftBtn.style.opacity = '0.7';
                draftBtn.innerHTML = `
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" style="animation: spin 1s linear infinite; margin-right: 6px;"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:0.25;"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:0.75;"></path></svg>
                    Saving Draft...
                `;
            }

            // Submit form directly
            document.getElementById('biltyForm').submit();
        };

        // Intercept form submit to validate fields and alert the user with SweetAlert2
        const biltyForm = document.getElementById('biltyForm');
        biltyForm.addEventListener('submit', function(e) {
            // If saving as draft, bypass strict client-side validation
            if (document.getElementById('bilty_status').value === 'draft') {
                return true;
            }

            document.getElementById('bilty_status').value = 'final';
            const errors = [];
            let firstInvalidEl = null;

            // 1. Billing Type
            const billingTypeEl = document.querySelector('input[name="billing_type"]:checked');
            if (!billingTypeEl) {
                errors.push("<strong>Billing Type:</strong> Please select a Billing Type (Paid, To Pay, or T.B.B.) at the top.");
                if (!firstInvalidEl) firstInvalidEl = document.querySelector('input[name="billing_type"]');
            }

            // 2. C.N. No
            const biltyNo = document.getElementById('bilty_no');
            if (!biltyNo || !biltyNo.value.trim() || parseInt(biltyNo.value, 10) < 1) {
                errors.push("<strong>C.N. No:</strong> Please enter a valid Consignment Note number.");
                if (!firstInvalidEl) firstInvalidEl = biltyNo;
            }

            // 3. C.N. Date
            const invoiceDate = document.getElementById('invoice_date');
            if (!invoiceDate || !invoiceDate.value.trim()) {
                errors.push("<strong>C.N. Date:</strong> Please enter the consignment note date.");
                if (!firstInvalidEl) firstInvalidEl = invoiceDate;
            }

            // 4. From Location
            const fromLoc = document.getElementById('from_location_id');
            if (!fromLoc || !fromLoc.value) {
                errors.push("<strong>From Location:</strong> Please select the origin location.");
                if (!firstInvalidEl) firstInvalidEl = document.getElementById('from_location_text');
            }

            // 5. To Location
            const toLoc = document.getElementById('to_location_id');
            if (!toLoc || !toLoc.value) {
                errors.push("<strong>To Location:</strong> Please select the destination location.");
                if (!firstInvalidEl) firstInvalidEl = document.getElementById('to_location_text');
            }

            // 6. Consignor
            const consignor = document.getElementById('consignor_id');
            if (!consignor || !consignor.value) {
                errors.push("<strong>Consignor:</strong> Please select the Consignor party.");
                if (!firstInvalidEl) firstInvalidEl = document.getElementById('consignor_text');
            }

            // 7. Consignee
            const consignee = document.getElementById('consignee_id');
            if (!consignee || !consignee.value) {
                errors.push("<strong>Consignee:</strong> Please select the Consignee party.");
                if (!firstInvalidEl) firstInvalidEl = document.getElementById('consignee_text');
            }

            // 8. Third Party (if T.B.B.)
            if (billingTypeEl && billingTypeEl.value === 'T.B.B.') {
                const billingParty = document.getElementById('billing_party_id');
                if (!billingParty || !billingParty.value) {
                    errors.push("<strong>Third Party:</strong> Please select a Third Party for T.B.B. billing.");
                    if (!firstInvalidEl) firstInvalidEl = document.getElementById('billing_party_text');
                }
            }

            // 9. Items Grid
            const itemRows = document.querySelectorAll('#gridBody tr.grid-row');
            if (itemRows.length === 0) {
                errors.push("<strong>Consignment Items:</strong> Please add at least one item row in the items table.");
            } else {
                itemRows.forEach((row, idx) => {
                    const rowNum = idx + 1;
                    const pkgs = row.querySelector('.input-no_of_pkgs');
                    const pkgsVal = pkgs ? parseInt(pkgs.value, 10) : 0;
                    if (!pkgs || isNaN(pkgsVal) || pkgsVal < 1) {
                        errors.push(`<strong>Item Row #${rowNum}:</strong> No. of packages must be at least 1.`);
                        if (!firstInvalidEl) firstInvalidEl = pkgs;
                    }

                    const unitVal = row.querySelector('.input-unit')?.value;
                    const weightVal = parseFloat(row.querySelector('.input-weight_val')?.value) || 0;
                    const qtyVal = parseFloat(row.querySelector('.input-qty')?.value) || 0;

                    if (unitVal === 'KG') {
                        if (weightVal <= 0 && qtyVal <= 0) {
                            errors.push(`<strong>Item Row #${rowNum}:</strong> Please enter the weight (KG).`);
                            if (!firstInvalidEl) firstInvalidEl = row.querySelector('.input-weight_val');
                        }
                    } else if (unitVal === 'Fixed') {
                        if (qtyVal <= 0) {
                            errors.push(`<strong>Item Row #${rowNum}:</strong> Quantity must be greater than 0.`);
                            if (!firstInvalidEl) firstInvalidEl = row.querySelector('.input-qty');
                        }
                    }

                    if (billingTypeEl && billingTypeEl.value !== 'T.B.B.') {
                        const rateInput = row.querySelector('.input-rate');
                        const rateVal = rateInput ? parseFloat(rateInput.value) : NaN;
                        if (!rateInput || isNaN(rateVal) || rateVal < 0) {
                            errors.push(`<strong>Item Row #${rowNum}:</strong> Please enter a valid rate.`);
                            if (!firstInvalidEl) firstInvalidEl = rateInput;
                        }
                    }
                });
            }

            if (errors.length > 0) {
                e.preventDefault();
                e.stopPropagation();

                const listHtml = '<div style="text-align: left; max-height: 260px; overflow-y: auto; padding-right: 5px;">' +
                    '<p style="margin-top: 0; margin-bottom: 8px; font-weight: 600; color: #b91c1c;">Please resolve the following before saving:</p>' +
                    '<ul style="margin: 0; padding-left: 18px; color: #374151; font-size: 13px; line-height: 1.6;">' +
                    errors.map(err => `<li style="margin-bottom: 4px;">${err}</li>`).join('') +
                    '</ul></div>';

                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Save Receipt',
                    html: listHtml,
                    confirmButtonText: 'Review Form',
                    confirmButtonColor: '#0f3460',
                    width: '480px'
                }).then(() => {
                    if (firstInvalidEl) {
                        firstInvalidEl.focus();
                        if (typeof firstInvalidEl.select === 'function') firstInvalidEl.select();
                    }
                });
                return false;
            }

            // Show loading state on save button upon successful submission
            const saveBtn = biltyForm.querySelector('.btn-save');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.style.opacity = '0.7';
                saveBtn.innerHTML = `
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" style="animation: spin 1s linear infinite; margin-right: 6px;"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:0.25;"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:0.75;"></path></svg>
                    Saving...
                `;
            }
        });

        // Lookup Bilty details when typing C.N No.
        const biltyNoInput = document.getElementById('bilty_no');
        let currentBiltyId = null;
        let lookupTimeout = null;

        // Synchronous lookup function
        function performBiltyLookup(biltyNo) {
            if (lookupTimeout) {
                clearTimeout(lookupTimeout);
            }

            if (!biltyNo) {
                // If input is cleared, reset button values and remove View link
                currentBiltyId = null;
                biltyForm.setAttribute('action', `{{ route('bilty.store') }}`);
                document.getElementById('bilty_status').value = 'final';
                const draftIndicator = document.getElementById('draftIndicator');
                if (draftIndicator) draftIndicator.style.display = 'none';
                const userDisplay = document.getElementById('bilty_user_display');
                if (userDisplay) userDisplay.value = "{{ auth()->user()->username ?? 'admin' }}";
                const saveBtn = document.querySelector('.btn-save');
                if (saveBtn) {
                    saveBtn.innerHTML = `
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Receipt
                    `;
                }
                const viewBtn = document.getElementById('btnDynamicViewReceipt');
                if (viewBtn) {
                    viewBtn.remove();
                }
                if (window.fromLocAuto) window.fromLocAuto.clear();
                if (window.toLocAuto) window.toLocAuto.clear();
                if (window.consignorAuto) window.consignorAuto.clear();
                if (window.consigneeAuto) window.consigneeAuto.clear();
                if (window.billingPartyAuto) window.billingPartyAuto.clear();
                const vehicleInput = document.getElementById('vehicle_no_text');
                if (vehicleInput) vehicleInput.value = '';
                return;
            }

            // Show loader overlay immediately
            const loader = document.getElementById('biltyLoaderOverlay');
            if (loader) {
                loader.style.display = 'flex';
            }

            // Debounce fetch request by 300ms to allow typing to finish
            lookupTimeout = setTimeout(() => {
                // Query local endpoint lookup
                fetch(`{{ url('/bilty/lookup') }}/${biltyNo}`)
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('Not found');
                        }
                        return res.json();
                    })
                    .then(data => {
                        // Populate header details
                        currentBiltyId = data.bilty.id;
                        biltyForm.setAttribute('action', `{{ url('/bilty/update') }}/${currentBiltyId}`);
                        
                        const isDraft = (data.bilty.status === 'draft');
                        const draftIndicator = document.getElementById('draftIndicator');
                        if (draftIndicator) {
                            draftIndicator.style.display = isDraft ? 'inline-block' : 'none';
                        }
                        document.getElementById('bilty_status').value = isDraft ? 'draft' : 'final';

                        // Update Action buttons text
                        const saveBtn = document.querySelector('.btn-save');
                        if (saveBtn) {
                            saveBtn.innerHTML = `
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                ${isDraft ? 'Finalize & Save' : 'Update Receipt'}
                            `;
                        }

                        const userDisplay = document.getElementById('bilty_user_display');
                        if (userDisplay) {
                            userDisplay.value = data.bilty.user ? (data.bilty.user.username || data.bilty.user.name) : 'admin';
                        }

                        // Format and map radio buttons selection (case-insensitive)
                        const dbBillingType = (data.bilty.billing_type || '').toUpperCase();
                        let targetRadio = null;
                        document.querySelectorAll('input[name="billing_type"]').forEach(r => {
                            const valUpper = r.value.toUpperCase();
                            if (valUpper === dbBillingType || dbBillingType.includes(valUpper) || (valUpper === 'T.B.B.' && dbBillingType.includes('TBB'))) {
                                targetRadio = r;
                            }
                        });
                        if (!targetRadio) {
                            targetRadio = document.querySelector(`input[name="billing_type"][value="${data.bilty.billing_type}"]`);
                        }
                        if (targetRadio) {
                            targetRadio.checked = true;
                        }
                        toggleBillingParty();

                        // Set Date and location IDs
                        if (data.bilty.invoice_date) {
                            document.getElementById('invoice_date').value = data.bilty.invoice_date.split('T')[0];
                        }
                        const fromCity = locationsList.find(x => x.id == data.from_city_id);
                        if (fromCity && window.fromLocAuto) {
                            window.fromLocAuto.setValue(fromCity.id, fromCity.name);
                        } else if (data.bilty.from_location && window.fromLocAuto) {
                            window.fromLocAuto.setValue(data.from_city_id || '', data.bilty.from_location.name);
                        } else if (window.fromLocAuto) {
                            window.fromLocAuto.clear();
                        }

                        const toCity = locationsList.find(x => x.id == data.to_city_id);
                        if (toCity && window.toLocAuto) {
                            window.toLocAuto.setValue(toCity.id, toCity.name);
                        } else if (data.bilty.to_location && window.toLocAuto) {
                            window.toLocAuto.setValue(data.to_city_id || '', data.bilty.to_location.name);
                        } else if (window.toLocAuto) {
                            window.toLocAuto.clear();
                        }

                        // Set party elements
                        if (data.bilty.consignor_id && window.consignorAuto) {
                            const cParty = consignorsList.find(x => x.id == data.bilty.consignor_id);
                            window.consignorAuto.setValue(data.bilty.consignor_id, cParty ? cParty.name : (data.bilty.consignor ? data.bilty.consignor.name : ''));
                            fetchPartyDetails('consignor', data.bilty.consignor_id);
                        } else if (data.bilty.consignor_name && window.consignorAuto) {
                            window.consignorAuto.setValue('', data.bilty.consignor_name);
                            document.getElementById('consignor_mobile').value = data.bilty.consignor_mobile || '';
                            document.getElementById('consignor_gstin').value = '';
                            document.getElementById('consignor_address').value = 'Imported Consignor (No Master Ledger)';
                        } else if (window.consignorAuto) {
                            window.consignorAuto.clear();
                        }

                        if (data.bilty.consignee_id && window.consigneeAuto) {
                            const cParty = consigneesList.find(x => x.id == data.bilty.consignee_id);
                            window.consigneeAuto.setValue(data.bilty.consignee_id, cParty ? cParty.name : (data.bilty.consignee ? data.bilty.consignee.name : ''));
                            fetchPartyDetails('consignee', data.bilty.consignee_id);
                        } else if (data.bilty.consignee_name && window.consigneeAuto) {
                            window.consigneeAuto.setValue('', data.bilty.consignee_name);
                            document.getElementById('consignee_mobile').value = data.bilty.consignee_mobile || '';
                            document.getElementById('consignee_gstin').value = '';
                            document.getElementById('consignee_address').value = 'Imported Consignee (No Master Ledger)';
                        } else if (window.consigneeAuto) {
                            window.consigneeAuto.clear();
                        }

                        if (data.bilty.billing_party_id && window.billingPartyAuto) {
                            const bParty = partiesList.find(x => x.id == data.bilty.billing_party_id);
                            window.billingPartyAuto.setValue(data.bilty.billing_party_id, bParty ? bParty.name : (data.bilty.billingParty ? data.bilty.billingParty.name : ''));
                        } else if (data.bilty.billing_party_name && window.billingPartyAuto) {
                            window.billingPartyAuto.setValue('', data.bilty.billing_party_name);
                        } else if (window.billingPartyAuto) {
                            window.billingPartyAuto.clear();
                        }

                        // Transport info
                        document.getElementById('cn_no').value = data.bilty.cn_no || '';
                        const vehicleVal = data.bilty.vehicle_no || '';
                        const isTransportOption = vehiclesList.includes(vehicleVal);
                        const typeSelector = document.getElementById('vehicle_type');

                        if (isTransportOption) {
                            typeSelector.value = 'Transport Name';
                        } else {
                            typeSelector.value = 'Vehicle Number';
                        }
                        toggleVehicleFields();
                        document.getElementById('vehicle_no_text').value = vehicleVal;

                        document.getElementById('eway_bill_no').value = data.bilty.eway_bill_no || '';
                        if (data.bilty.eway_bill_no) {
                            ewayBills = data.bilty.eway_bill_no.split(',').map(v => v.trim()).filter(v => v !== '');
                            renderEwayTags();
                        }

                        // Calculations
                        document.getElementById('round_off').value = data.bilty.round_off || '0.00';
                        document.getElementById('cash_amount').value = data.bilty.cash_amount || '0.00';
                        document.getElementById('card_amount').value = data.bilty.card_amount || '0.00';
                        document.getElementById('upi_chq_amount').value = data.bilty.upi_chq_amount || '0.00';
                        document.getElementById('ref_no').value = data.bilty.ref_no || '';
                        if (data.bilty.payment_date) {
                            document.getElementById('payment_date').value = data.bilty.payment_date.split('T')[0];
                        }
                        document.getElementById('bank_account').value = data.bilty.bank_account || '';
                        document.getElementById('remark').value = data.bilty.remark || '';
                        document.getElementById('voucher_no').value = data.bilty.voucher_no || '';

                        // Clear items grid and rebuild lines
                        const gridBody = document.getElementById('gridBody');
                        gridBody.innerHTML = '';
                        rowIndex = 0;

                        data.bilty.items.forEach((item, index) => {
                            addRow();
                            const row = document.querySelectorAll('.grid-row')[index];
                            row.querySelector(`input[name="items[${index}][no_of_pkgs]"]`).value = item.no_of_pkgs;
                            row.querySelector(`input[name="items[${index}][packing]"]`).value = item.packing;
                            row.querySelector(`input[name="items[${index}][description]"]`).value = item.description;
                            row.querySelector(`input[name="items[${index}][invoice_no]"]`).value = item.invoice_no;
                            row.querySelector(`input[name="items[${index}][invoice_value]"]`).value = item.invoice_value;
                            
                            const wSelect = row.querySelector(`select[name="items[${index}][unit]"]`);
                            if (wSelect) {
                                wSelect.value = item.unit || 'KG';
                                handleUnitChange(wSelect);
                            }

                            row.querySelector(`input[name="items[${index}][weight_val]"]`).value = item.weight_val;
                            row.querySelector(`input[name="items[${index}][qty]"]`).value = item.qty;
                            row.querySelector(`input[name="items[${index}][rate]"]`).value = item.rate;
                            row.querySelector(`input[name="items[${index}][st]"]`).value = item.st;
                            row.querySelector(`input[name="items[${index}][rc]"]`).value = item.rc;
                            row.querySelector(`input[name="items[${index}][sc]"]`).value = item.sc;
                            row.querySelector(`input[name="items[${index}][dd]"]`).value = item.dd;
                        });
                        
                        calculateAll();

                        // Inject View Receipt button next to Update/Save button dynamically
                        let viewBtn = document.getElementById('btnDynamicViewReceipt');
                        if (!viewBtn) {
                            viewBtn = document.createElement('a');
                            viewBtn.id = 'btnDynamicViewReceipt';
                            viewBtn.className = 'btn-footer btn-view-receipt';
                            viewBtn.target = '_blank';
                            viewBtn.style.display = 'flex';
                            viewBtn.style.alignItems = 'center';
                            viewBtn.style.gap = '8px';
                            viewBtn.innerHTML = `
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View Receipt
                            `;
                            const saveBtn = document.querySelector('.btn-save');
                            if (saveBtn) {
                                saveBtn.parentNode.insertBefore(viewBtn, saveBtn.nextSibling);
                            }
                        }
                        viewBtn.href = `{{ url('/bilty/print') }}/${currentBiltyId}`;

                        // Hide loader after short timeout to let inputs paint properly
                        const loader = document.getElementById('biltyLoaderOverlay');
                        setTimeout(() => {
                            if (loader) loader.style.display = 'none';
                        }, 200);
                    })
                    .catch(err => {
                        // Reset if lookup fails or record doesn't exist (clean default creation state)
                        currentBiltyId = null;
                        biltyForm.setAttribute('action', `{{ route('bilty.store') }}`);
                        const saveBtn = document.querySelector('.btn-save');
                        if (saveBtn) {
                            saveBtn.innerHTML = `
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Save Receipt
                            `;
                        }
                        // Remove dynamic view receipt button if it exists
                        const viewBtn = document.getElementById('btnDynamicViewReceipt');
                        if (viewBtn) {
                            viewBtn.remove();
                        }
                        const loader = document.getElementById('biltyLoaderOverlay');
                        if (loader) loader.style.display = 'none';
                    });
            }, 300);
        }

        // Setup Autocomplete Engine
        function advanceFocusFrom(currentEl) {
            setTimeout(() => {
                const form = document.getElementById('biltyForm');
                if (!form) return;
                const focusables = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled]):not([readonly]), textarea:not([disabled]):not([readonly]), select:not([disabled]):not([readonly]), button:not([disabled])'))
                    .filter(el => el.offsetParent !== null && el.tabIndex !== -1);
                const idx = focusables.indexOf(currentEl);
                if (idx > -1 && idx < focusables.length - 1) {
                    const next = focusables[idx + 1];
                    next.focus();
                    if (typeof next.select === 'function') next.select();
                }
            }, 50);
        }

        function setupAutocomplete({
            inputEl,
            hiddenEl,
            dropdownEl,
            getItems,
            onSelect,
            onClear
        }) {
            if (!inputEl || !dropdownEl) return null;
            let activeIndex = -1;
            let currentMatches = [];

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

            function renderMatches() {
                const query = inputEl.value.trim();
                if (!query) {
                    dropdownEl.style.display = 'none';
                    return;
                }

                const items = getItems();
                currentMatches = items.filter(item => {
                    const name = typeof item === 'string' ? item : item.name;
                    return name.toLowerCase().includes(query.toLowerCase());
                });

                currentMatches.sort((a, b) => {
                    const nameA = (typeof a === 'string' ? a : a.name).toLowerCase();
                    const nameB = (typeof b === 'string' ? b : b.name).toLowerCase();
                    const q = query.toLowerCase();
                    const aStarts = nameA.startsWith(q);
                    const bStarts = nameB.startsWith(q);
                    if (aStarts && !bStarts) return -1;
                    if (!aStarts && bStarts) return 1;
                    return nameA.localeCompare(nameB);
                });

                if (currentMatches.length === 0) {
                    dropdownEl.innerHTML = `<div class="autocomplete-no-match">No results matching "${escapeHtml(query)}"</div>`;
                    dropdownEl.style.display = 'block';
                    activeIndex = -1;
                    return;
                }

                const visibleMatches = currentMatches.slice(0, 25);
                dropdownEl.innerHTML = visibleMatches.map((item, idx) => {
                    const name = typeof item === 'string' ? item : item.name;
                    let metaHtml = '';
                    if (typeof item === 'object') {
                        const metas = [];
                        if (item.mobile) metas.push(`📞 ${item.mobile}`);
                        if (item.gstin) metas.push(`GST: ${item.gstin}`);
                        if (metas.length > 0) {
                            metaHtml = `<span class="item-meta">${escapeHtml(metas.join(' | '))}</span>`;
                        }
                    }
                    return `<div class="autocomplete-item" data-index="${idx}">
                        <span class="item-name">${highlightMatch(name, query)}</span>
                        ${metaHtml}
                    </div>`;
                }).join('');

                dropdownEl.style.display = 'block';
                activeIndex = -1;
            }

            function selectItem(item) {
                if (!item) return;
                const name = typeof item === 'string' ? item : item.name;
                const id = typeof item === 'string' ? item : (item.id ?? item.name);
                
                inputEl.value = name;
                if (hiddenEl) {
                    hiddenEl.value = id;
                    hiddenEl.dispatchEvent(new Event('change', { bubbles: true }));
                }
                dropdownEl.style.display = 'none';
                activeIndex = -1;

                if (onSelect) {
                    onSelect(item);
                }

                advanceFocusFrom(inputEl);
            }

            inputEl.addEventListener('input', function() {
                if (hiddenEl) {
                    const query = inputEl.value.trim().toLowerCase();
                    const items = getItems();
                    const exact = items.find(i => (typeof i === 'string' ? i : i.name).toLowerCase() === query);
                    if (exact) {
                        hiddenEl.value = typeof exact === 'string' ? exact : exact.id;
                        if (onSelect) onSelect(exact);
                    } else {
                        hiddenEl.value = '';
                        if (onClear) onClear();
                    }
                }
                renderMatches();
            });

            inputEl.addEventListener('focus', function() {
                if (inputEl.value.trim().length > 0) {
                    renderMatches();
                }
            });

            inputEl.addEventListener('keydown', function(e) {
                if (dropdownEl.style.display === 'none') {
                    if (e.key === 'ArrowDown' && inputEl.value.trim()) {
                        renderMatches();
                        e.preventDefault();
                    }
                    return;
                }

                const itemsEl = dropdownEl.querySelectorAll('.autocomplete-item');
                if (!itemsEl.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    e.stopPropagation();
                    activeIndex = (activeIndex + 1) % itemsEl.length;
                    updateActiveItem(itemsEl);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    e.stopPropagation();
                    activeIndex = (activeIndex - 1 + itemsEl.length) % itemsEl.length;
                    updateActiveItem(itemsEl);
                } else if (e.key === 'Enter') {
                    if (activeIndex >= 0 && activeIndex < currentMatches.length) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectItem(currentMatches[activeIndex]);
                    } else if (currentMatches.length === 1) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectItem(currentMatches[0]);
                    }
                } else if (e.key === 'Tab') {
                    if (activeIndex >= 0 && activeIndex < currentMatches.length) {
                        selectItem(currentMatches[activeIndex]);
                    }
                    dropdownEl.style.display = 'none';
                } else if (e.key === 'Escape') {
                    dropdownEl.style.display = 'none';
                    activeIndex = -1;
                    e.preventDefault();
                }
            });

            function updateActiveItem(itemsEl) {
                itemsEl.forEach((el, idx) => {
                    if (idx === activeIndex) {
                        el.classList.add('active');
                        el.scrollIntoView({ block: 'nearest' });
                    } else {
                        el.classList.remove('active');
                    }
                });
            }

            dropdownEl.addEventListener('mousedown', function(e) {
                const itemEl = e.target.closest('.autocomplete-item');
                if (itemEl) {
                    const idx = parseInt(itemEl.getAttribute('data-index'), 10);
                    if (!isNaN(idx) && currentMatches[idx]) {
                        e.preventDefault();
                        selectItem(currentMatches[idx]);
                    }
                }
            });

            document.addEventListener('click', function(e) {
                if (!inputEl.contains(e.target) && !dropdownEl.contains(e.target)) {
                    dropdownEl.style.display = 'none';
                    activeIndex = -1;
                }
            });

            return {
                setValue: function(id, name, extra) {
                    if (inputEl) inputEl.value = name || '';
                    if (hiddenEl) hiddenEl.value = id || '';
                    if (extra && onSelect) onSelect(extra);
                },
                clear: function() {
                    if (inputEl) inputEl.value = '';
                    if (hiddenEl) hiddenEl.value = '';
                    dropdownEl.style.display = 'none';
                    if (onClear) onClear();
                }
            };
        }

        // Initialize autocompletes
        window.fromLocAuto = setupAutocomplete({
            inputEl: document.getElementById('from_location_text'),
            hiddenEl: document.getElementById('from_location_id'),
            dropdownEl: document.getElementById('from_location_dropdown'),
            getItems: () => locationsList
        });

        window.toLocAuto = setupAutocomplete({
            inputEl: document.getElementById('to_location_text'),
            hiddenEl: document.getElementById('to_location_id'),
            dropdownEl: document.getElementById('to_location_dropdown'),
            getItems: () => locationsList
        });

        window.consignorAuto = setupAutocomplete({
            inputEl: document.getElementById('consignor_text'),
            hiddenEl: document.getElementById('consignor_id'),
            dropdownEl: document.getElementById('consignor_dropdown'),
            getItems: () => consignorsList,
            onSelect: (item) => {
                document.getElementById('consignor_mobile').value = item.mobile || '';
                document.getElementById('consignor_gstin').value = item.gstin || '';
                document.getElementById('consignor_address').value = item.address || '';
                if (item.id) fetchPartyDetails('consignor', item.id);
            },
            onClear: () => {
                document.getElementById('consignor_mobile').value = '';
                document.getElementById('consignor_gstin').value = '';
                document.getElementById('consignor_address').value = '';
            }
        });

        window.consigneeAuto = setupAutocomplete({
            inputEl: document.getElementById('consignee_text'),
            hiddenEl: document.getElementById('consignee_id'),
            dropdownEl: document.getElementById('consignee_dropdown'),
            getItems: () => consigneesList,
            onSelect: (item) => {
                document.getElementById('consignee_mobile').value = item.mobile || '';
                document.getElementById('consignee_gstin').value = item.gstin || '';
                document.getElementById('consignee_address').value = item.address || '';
                if (item.id) fetchPartyDetails('consignee', item.id);
            },
            onClear: () => {
                document.getElementById('consignee_mobile').value = '';
                document.getElementById('consignee_gstin').value = '';
                document.getElementById('consignee_address').value = '';
            }
        });

        window.billingPartyAuto = setupAutocomplete({
            inputEl: document.getElementById('billing_party_text'),
            hiddenEl: document.getElementById('billing_party_id'),
            dropdownEl: document.getElementById('billing_party_dropdown'),
            getItems: () => partiesList
        });

        window.vehicleNoAuto = setupAutocomplete({
            inputEl: document.getElementById('vehicle_no_text'),
            dropdownEl: document.getElementById('vehicle_no_dropdown'),
            getItems: () => {
                const type = document.getElementById('vehicle_type').value;
                return (type === 'Transport Name') ? vehiclesList : [];
            }
        });

        // Restore any old submitted values if validation redirected back
        @if(old('from_location_id'))
            const oldFrom = locationsList.find(x => x.id == {{ old('from_location_id') }});
            if (oldFrom && window.fromLocAuto) window.fromLocAuto.setValue(oldFrom.id, oldFrom.name);
        @endif
        @if(old('to_location_id'))
            const oldTo = locationsList.find(x => x.id == {{ old('to_location_id') }});
            if (oldTo && window.toLocAuto) window.toLocAuto.setValue(oldTo.id, oldTo.name);
        @endif
        @if(old('consignor_id'))
            const oldConsignor = consignorsList.find(x => x.id == {{ old('consignor_id') }});
            if (oldConsignor && window.consignorAuto) {
                window.consignorAuto.setValue(oldConsignor.id, oldConsignor.name, oldConsignor);
            }
        @endif
        @if(old('consignee_id'))
            const oldConsignee = consigneesList.find(x => x.id == {{ old('consignee_id') }});
            if (oldConsignee && window.consigneeAuto) {
                window.consigneeAuto.setValue(oldConsignee.id, oldConsignee.name, oldConsignee);
            }
        @endif
        @if(old('billing_party_id'))
            const oldParty = partiesList.find(x => x.id == {{ old('billing_party_id') }});
            if (oldParty && window.billingPartyAuto) {
                window.billingPartyAuto.setValue(oldParty.id, oldParty.name);
            }
        @endif

        // Enter key to move to next field script
        biltyForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                const tag = e.target.tagName;
                const type = e.target.type;
                if (tag === 'TEXTAREA' || type === 'submit') {
                    return;
                }
                
                // If an autocomplete dropdown is open with an active item, autocomplete handles Enter
                const openDropdown = document.querySelector('.autocomplete-dropdown[style*="display: block"]');
                if (openDropdown && openDropdown.querySelector('.autocomplete-item.active')) {
                    return;
                }

                e.preventDefault();

                // Find all focusable fields that are visible and active
                const focusables = Array.from(biltyForm.querySelectorAll('input:not([type="hidden"]):not([disabled]):not([readonly]), textarea:not([disabled]):not([readonly]), select:not([disabled]):not([readonly]), button:not([disabled])'))
                    .filter(el => el.offsetParent !== null && el.tabIndex !== -1);
                
                const index = focusables.indexOf(e.target);

                if (index > -1 && index < focusables.length - 1) {
                    const nextField = focusables[index + 1];
                    nextField.focus();
                    if (typeof nextField.select === 'function') {
                        nextField.select();
                    }
                }
            }
        });

        // Search check for CN No lookup trigger
        biltyNoInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                performBiltyLookup(this.value);
            }
        });

        // Run initial calculation
        calculateAll();

        // Check if bilty_no was passed in URL parameters to auto-load it
        const urlParams = new URLSearchParams(window.location.search);
        const urlBiltyNo = urlParams.get('bilty_no');
        if (urlBiltyNo) {
            biltyNoInput.value = urlBiltyNo;
            performBiltyLookup(urlBiltyNo);
        } else {
            @if(!old('bilty_no'))
            biltyNoInput.value = "{{ $nextBiltyNo }}";
            @endif
        }

        // Initialize e-way tags from existing values if any
        renderEwayTags();
        toggleVehicleFields();
    });

    function toggleVehicleFields() {
        const type = document.getElementById('vehicle_type').value;
        const label = document.getElementById('vehicle_no_label');
        const input = document.getElementById('vehicle_no_text');
        const dropdown = document.getElementById('vehicle_no_dropdown');

        if (dropdown) dropdown.style.display = 'none';
        if (type === 'Transport Name') {
            if (label) label.textContent = 'Transport Name';
            if (input) input.placeholder = 'Type transport name...';
        } else {
            if (label) label.textContent = 'Vehicle No.';
            if (input) input.placeholder = 'e.g. AS-01-XX-1234';
        }
    }

    let ewayBills = [];
    const existingVal = document.getElementById('eway_bill_no').value.trim();
    if (existingVal) {
        ewayBills = existingVal.split(',').map(v => v.trim()).filter(v => v !== '');
    }

    function addEwayBill() {
        const input = document.getElementById('eway_bill_input');
        const val = input.value.trim().toUpperCase();
        if (val === '') return;
        if (ewayBills.includes(val)) {
            alert('E-Way Bill number already added.');
            input.value = '';
            return;
        }
        ewayBills.push(val);
        input.value = '';
        updateEwayHiddenInput();
        renderEwayTags();
    }

    function removeEwayBill(val) {
        ewayBills = ewayBills.filter(v => v !== val);
        updateEwayHiddenInput();
        renderEwayTags();
    }

    function updateEwayHiddenInput() {
        document.getElementById('eway_bill_no').value = ewayBills.join(', ');
    }

    function renderEwayTags() {
        const container = document.getElementById('eway_tags_container');
        container.innerHTML = '';
        ewayBills.forEach(bill => {
            const tag = document.createElement('div');
            tag.style.cssText = 'background:#e2e8f0; color:#333; padding:2px 8px; border-radius:4px; font-size:11px; display:flex; align-items:center; gap:5px; font-weight:600;';
            tag.innerHTML = `
                <span>${bill}</span>
                <span onclick="removeEwayBill('${bill}')" style="cursor:pointer; color:#ef4444; font-weight:bold;">&times;</span>
            `;
            container.appendChild(tag);
        });
    }

    function clearFormFields() {
        const selectedRadio = document.querySelector('input[name="billing_type"]:checked');
        const billingTypeVal = selectedRadio ? selectedRadio.value : null;

        const form = document.getElementById('biltyForm');
        if (form) form.reset();

        if (billingTypeVal) {
            const radioToRestore = document.querySelector(`input[name="billing_type"][value="${billingTypeVal}"]`);
            if (radioToRestore) {
                radioToRestore.checked = true;
            }
        }

        if (typeof toggleBillingParty === 'function') toggleBillingParty();
        if (typeof calculateAll === 'function') calculateAll();
    }
</script>
@endsection
