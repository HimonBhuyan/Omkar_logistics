@extends('layouts.app')

@section('title', 'Lorry Receipt (Bilty) Entry - Omkaar Logistics')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
<style>
    /* Adjust Select2 styling to match theme inputs */
    .select2-container .select2-selection--single {
        height: 32px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        padding-top: 2px !important;
        font-size: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 12px !important;
        color: #333 !important;
        line-height: 28px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        font-size: 12px !important;
        color: #9ca3af !important;
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
        
        <!-- Billing Options at the top -->
        <div class="billing-options" style="background:#e3f2fd; border-bottom:1px solid #bbdefb; padding:15px 20px; display:flex; gap:25px; align-items:center; border-radius:0;">
            <span style="font-weight:700; color:#0f3460; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">Billing Type:</span>
            <label style="font-weight:600; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="radio" name="billing_type" value="Paid" {{ old('billing_type') == 'Paid' ? 'checked' : '' }} onclick="toggleBillingParty()">
                Paid
            </label>
            <label style="font-weight:600; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="radio" name="billing_type" value="To Pay" {{ old('billing_type') == 'To Pay' ? 'checked' : '' }} onclick="toggleBillingParty()">
                To Pay
            </label>
            <label style="font-weight:600; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="radio" name="billing_type" value="T.B.B." {{ old('billing_type') == 'T.B.B.' ? 'checked' : '' }} onclick="toggleBillingParty()">
                T.B.B. (To Be Billed)
            </label>
        </div>

         <!-- Red Title Bar -->
        <div class="bilty-header-bar">
            <div style="display:flex; align-items:center; gap:10px;">
                <span>C.N Book</span>
                <span id="draftIndicator" style="display:none; background:#fef3c7; color:#b45309; border:1px solid #f59e0b; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">DRAFT CONSIGNMENT</span>
            </div>
            
            <div class="bilty-header-inputs">
                <input type="hidden" name="series" value="26-27">
                <input type="hidden" name="status" id="bilty_status" value="{{ old('status', 'final') }}">
                <div>
                    <label for="bilty_no">C.N No.</label>
                    <input type="number" name="bilty_no" id="bilty_no" value="{{ old('bilty_no', $nextBiltyNo) }}" required min="1">
                </div>
                
                <div>
                    <label for="invoice_date">C.N Date</label>
                    <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                </div>

                <div>
                    <label for="bilty_user_display">User</label>
                    <input type="text" id="bilty_user_display" value="{{ auth()->user()->username ?? 'admin' }}" readonly style="height:30px; font-weight:600; color:#475569; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px; padding:2px 8px; cursor:not-allowed; pointer-events:none;" tabindex="-1">
                </div>
            </div>
        </div>

        <div class="bilty-body" id="formContentWrapper" style="position: relative;">
            <div id="formBlockedOverlay" style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(240, 240, 240, 0.75); z-index:9990; display:flex; justify-content:center; align-items:flex-start; padding-top:120px; backdrop-filter: blur(4px); transition: all 0.3s ease; pointer-events: auto;">
                <div style="background:white; border:2px solid var(--secondary-color); padding:25px 50px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.25); font-weight:700; color:#c92a2a; text-align:center; font-size:15px; max-width:90%; position:sticky; top:150px;">
                    ⚠️ Please select a Billing Type at the top to fill this form.
                </div>
            </div>

            <div id="biltyLoaderOverlay" class="bilty-loader-overlay">
                <div style="background:white; padding:25px 50px; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.2); display:flex; flex-direction:column; align-items:center; font-weight:700; color:var(--primary-color);">
                    <div class="bilty-spinner"></div>
                    <span>Loading Bilty Details...</span>
                </div>
            </div>
            
            <div class="form-row">
                
                <!-- Left Box: Route & Consignor details -->
                <div class="section-box">
                    <div class="section-title">Consignor & Route</div>
                    
                    <div class="grid-fields-2">
                        <div class="form-group-custom">
                            <label for="from_location_id">From Loc.</label>
                            <select name="from_location_id" id="from_location_id" required>
                                <option value="">Select From Location</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('from_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group-custom">
                            <label for="to_location_id">To Loc.</label>
                            <select name="to_location_id" id="to_location_id" required>
                                <option value="">Select To Location</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('to_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid-fields-2">
                        <div class="form-group-custom" style="grid-column: span 2;">
                            <label for="consignor_id">Consignor Name</label>
                            <select name="consignor_id" id="consignor_id" class="party-select" data-prefix="consignor" required>
                                <option value="">Select Consignor</option>
                                @foreach ($consignors as $p)
                                    <option value="{{ $p->id }}" {{ old('consignor_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid-fields-2">
                        <div class="form-group-custom">
                            <label for="consignor_mobile">Mobile</label>
                            <input type="text" name="consignor_mobile" id="consignor_mobile" readonly placeholder="Auto-populated">
                        </div>
                        
                        <div class="form-group-custom">
                            <label for="consignor_gstin">GSTIN</label>
                            <input type="text" name="consignor_gstin" id="consignor_gstin" readonly placeholder="Auto-populated">
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label for="consignor_address">Address</label>
                        <textarea name="consignor_address" id="consignor_address" rows="2" readonly placeholder="Auto-populated address from master data"></textarea>
                    </div>
                </div>

                <!-- Right Box: Consignee & Billing Options -->
                <div class="section-box">
                    <div class="section-title">Consignee & Billing Details</div>


                    <div class="grid-fields-2">
                        
                        <div class="form-group-custom" style="grid-column: span 2;">
                            <label for="consignee_id">Consignee Name</label>
                            <select name="consignee_id" id="consignee_id" class="party-select" data-prefix="consignee" required>
                                <option value="">Select Consignee</option>
                                @foreach ($consignees as $p)
                                    <option value="{{ $p->id }}" {{ old('consignee_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid-fields-2">
                        <div class="form-group-custom">
                            <label for="consignee_mobile">Mobile</label>
                            <input type="text" name="consignee_mobile" id="consignee_mobile" readonly placeholder="Auto-populated">
                        </div>
                        
                        <div class="form-group-custom">
                            <label for="consignee_gstin">GSTIN</label>
                            <input type="text" name="consignee_gstin" id="consignee_gstin" readonly placeholder="Auto-populated">
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label for="consignee_address">Address</label>
                        <textarea name="consignee_address" id="consignee_address" rows="2" readonly placeholder="Auto-populated address from master data"></textarea>
                    </div>
                </div>

            </div>

            <!-- Middle Section: Consignment, Vehicle, Eway numbers -->
            <div class="section-box" style="margin-bottom: 20px;">
                <div class="section-title">Transport Details</div>
                <div class="grid-fields-2" style="grid-template-columns: repeat(5, 1fr); gap: 10px;">
                    <div class="form-group-custom" id="billing_party_wrapper" style="display: {{ in_array(old('billing_type'), ['Paid', 'T.B.B.']) ? 'flex' : 'none' }};">
                        <label for="billing_party_id">Third Party</label>
                        <select name="billing_party_id" id="billing_party_id">
                            <option value="">Select Third Party</option>
                            @foreach ($parties as $p)
                                <option value="{{ $p->id }}" {{ old('billing_party_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label for="cn_no">C/N No.</label>
                        <input type="text" name="cn_no" id="cn_no" value="{{ old('cn_no') }}" placeholder="Consignment Note No.">
                    </div>

                    <div class="form-group-custom">
                        <label for="vehicle_type">Vehicle Type</label>
                        <select name="vehicle_type" id="vehicle_type" onchange="toggleVehicleFields()" style="height:32px;">
                            <option value="Vehicle Number">Vehicle Number</option>
                            <option value="Transport Name">Transport Name</option>
                        </select>
                    </div>
                    
                    <div class="form-group-custom" id="vehicle_input_wrapper">
                        <label for="vehicle_no_text">Vehicle No.</label>
                        <input type="text" name="vehicle_no" id="vehicle_no_text" placeholder="e.g. AS-01-XX-1234" value="{{ old('vehicle_no') }}">
                    </div>

                    <div class="form-group-custom" id="vehicle_select_wrapper" style="display: none;">
                        <label for="vehicle_no_select">Transport Name</label>
                        <select id="vehicle_no_select" style="width:100%;">
                            <option value="">Select Transport</option>
                            @foreach ($vehicles as $v)
                                <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group-custom" style="position: relative;">
                        <label for="eway_bill_no">E-Way Bill No.</label>
                        <div style="display:flex; gap:5px;">
                            <input type="text" id="eway_bill_input" placeholder="12-digit EWay Bill No." style="flex:1;">
                            <button type="button" onclick="addEwayBill()" style="background:#0f3460; color:#fff; border:none; padding:0 10px; border-radius:4px; font-weight:bold; cursor:pointer;">➕</button>
                        </div>
                        <input type="hidden" name="eway_bill_no" id="eway_bill_no" value="{{ old('eway_bill_no') }}">
                        <div id="eway_tags_container" style="display:flex; flex-wrap:wrap; gap:5px; margin-top:5px;"></div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Bilty Grid Table -->
            <div class="grid-container">
                <table class="bilty-grid" id="itemsTable">
                    <thead>
                        <tr>
                            <th width="8%">NoOfPkgs</th>
                            <th width="10%">Packing</th>
                            <th width="18%">Description</th>
                            <th width="10%">Invoice No</th>
                            <th width="10%">Invoice Value</th>
                            <th width="10%">Weight Type</th>
                            <th width="8%" class="weight-col">Weight</th>
                            <th width="8%">Weight/Fixed</th>
                            <th width="8%">Rate</th>
                            <th width="6%">ST</th>
                            <th width="6%">RC</th>
                            <th width="6%">SC</th>
                            <th width="6%">DD</th>
                            <th width="4%"></th>
                        </tr>
                    </thead>
                    <tbody id="gridBody">
                        <!-- Standard Row 1 -->
                        <tr class="grid-row">
                            <td>
                                <input type="number" name="items[0][no_of_pkgs]" class="input-no_of_pkgs calc-trigger" required min="1" value="1">
                            </td>
                            <td>
                                <input type="text" name="items[0][packing]" placeholder="Box/Bag/Roll" value="Box">
                            </td>
                            <td>
                                <input type="text" name="items[0][description]" placeholder="Goods Description" value="Auto Parts">
                            </td>
                            <td>
                                <input type="text" name="items[0][invoice_no]" placeholder="Inv No">
                            </td>
                            <td>
                                <input type="number" name="items[0][invoice_value]" class="input-invoice_value" value="0.00" step="0.01">
                            </td>
                            <td>
                                <select name="items[0][weight_type]" class="input-weight_type" onchange="handleWeightTypeChange(this)">
                                    <option value="KG">KG</option>
                                    <option value="Fixed">Fixed</option>
                                </select>
                            </td>
                            <td class="weight-col-cell">
                                <input type="number" name="items[0][weight_val]" class="input-weight_val calc-trigger" step="0.001" value="0">
                            </td>
                            <td>
                                <input type="number" name="items[0][qty]" class="input-qty calc-trigger" required min="0" step="0.001" value="0" style="background-color: #ffffff; color: #333;">
                            </td>
                            <td>
                                <input type="number" name="items[0][rate]" class="input-rate calc-trigger" required min="0.00" step="0.01" value="0.00" style="background-color: #ffffff; color: #333;">
                            </td>
                            <td>
                                <input type="number" name="items[0][st]" class="input-st calc-trigger" value="0.00" step="0.01">
                            </td>
                            <td>
                                <input type="number" name="items[0][rc]" class="input-rc calc-trigger" value="0.00" step="0.01">
                            </td>
                            <td>
                                <input type="number" name="items[0][sc]" class="input-sc calc-trigger" value="0.00" step="0.01">
                            </td>
                            <td>
                                <input type="number" name="items[0][dd]" class="input-dd calc-trigger" value="0.00" step="0.01">
                            </td>
                            <td>
                                <button type="button" class="btn-delete-row" onclick="removeRow(this)">&times;</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="grid-actions">
                    <button type="button" class="btn-add-row" onclick="addRow()">+ Add Consignment Line</button>
                    <span style="font-size: 11px; color: #64748b; font-style: italic;">Row totals are Qty * Rate. Bottom taxes sum automatically.</span>
                </div>
            </div>

            <!-- Summary, Rounding, Surcharges & Settlement -->
            <div class="totals-and-settlement">
                
                <div>
                    <div style="font-size: 12px; font-weight:700; margin-bottom: 12px; color:var(--primary-color); border-bottom: 1px solid #cbd5e1; padding-bottom:4px;">CHARGES SUMMARY</div>
                    
                    <div class="totals-grid">
                        <div class="form-group-custom">
                            <label for="total_packages">Total Pkgs</label>
                            <input type="text" name="total_packages" id="total_packages" readonly value="1">
                        </div>
                        <div class="form-group-custom">
                            <label for="total_qty">Total Qty</label>
                            <input type="text" name="total_qty" id="total_qty" readonly value="1.000">
                        </div>
                        <div class="form-group-custom">
                            <label for="gross_amount">Gross Amt (Qty*Rate)</label>
                            <input type="text" name="gross_amount" id="gross_amount" readonly value="500.00">
                        </div>
                        
                        <div class="form-group-custom">
                            <label for="st_charge">Total S.T.</label>
                            <input type="text" name="st_charge" id="st_charge" readonly value="0.00">
                        </div>
                        <div class="form-group-custom">
                            <label for="rc_charge">Total R.C.</label>
                            <input type="text" name="rc_charge" id="rc_charge" readonly value="0.00">
                        </div>
                        <div class="form-group-custom">
                            <label for="sc_charge">Total S.C.</label>
                            <input type="text" name="sc_charge" id="sc_charge" readonly value="0.00">
                        </div>
                        <div class="form-group-custom">
                            <label for="dd_charge">Total D.D.</label>
                            <input type="text" name="dd_charge" id="dd_charge" readonly value="0.00">
                        </div>
                        
                        <div class="form-group-custom">
                            <label for="round_off">Round Off</label>
                            <input type="number" name="round_off" id="round_off" class="calc-trigger" value="0.00" step="0.01">
                        </div>

                        <div class="net-amount-card" style="grid-column: span 4; margin-top: 10px;">
                            <label>NET BILL AMOUNT (INR)</label>
                            <input type="text" name="net_amount" id="net_amount" readonly value="500.00">
                        </div>
                    </div>
                </div>

                <!-- Right Side Settlement -->
                <div class="settlement-panel">
                    <div style="font-size: 12px; font-weight:700; margin-bottom: 12px; color:var(--primary-color); border-bottom: 1px solid #cbd5e1; padding-bottom:4px;">PAYMENT SETTLEMENT</div>
                    
                    <div class="grid-fields-2">
                        <div class="form-group-custom">
                            <label for="cash_amount">Cash Paid</label>
                            <input type="number" name="cash_amount" id="cash_amount" class="calc-trigger" value="0.00" step="0.01">
                        </div>
                        <div class="form-group-custom">
                            <label for="card_amount">Card Paid</label>
                            <input type="number" name="card_amount" id="card_amount" class="calc-trigger" value="0.00" step="0.01">
                        </div>
                    </div>

                    <div class="grid-fields-2">
                        <div class="form-group-custom">
                            <label for="upi_chq_amount">UPI / Cheque</label>
                            <input type="number" name="upi_chq_amount" id="upi_chq_amount" class="calc-trigger" value="0.00" step="0.01">
                        </div>
                        <div class="form-group-custom">
                            <label for="ref_no">Ref No / Chq No.</label>
                            <input type="text" name="ref_no" id="ref_no" placeholder="Txn Ref No.">
                        </div>
                    </div>

                    <div class="grid-fields-2">
                        <div class="form-group-custom">
                            <label for="payment_date">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="form-group-custom">
                            <label for="bank_account">Bank A/C</label>
                            <select name="bank_account" id="bank_account">
                                <option value="">Select Bank</option>
                                <option value="HDFC Bank">HDFC Bank</option>
                                <option value="State Bank of India">State Bank of India</option>
                                <option value="ICICI Bank">ICICI Bank</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-custom" style="margin-top: 10px;">
                        <label for="remark">Remark</label>
                        <input type="text" name="remark" id="remark" placeholder="Billing remarks...">
                    </div>

                    <div class="form-group-custom" style="margin-top: 10px;">
                        <label for="voucher_no">Voucher No.</label>
                        <input type="number" name="voucher_no" id="voucher_no" value="{{ $nextVoucherNo }}" readonly>
                    </div>

                    <div id="balanceBox" class="balance-box balance-unpaid">
                        <span>Balance Due:</span>
                        <span id="balanceText">₹ 500.00</span>
                        <input type="hidden" name="balance_amount" id="balance_amount" value="500.00">
                    </div>
                </div>

            </div>

        </div>

        <!-- Action Buttons Footer -->
        <div class="bilty-actions-footer">
            <button type="reset" class="btn-footer btn-reset" onclick="setTimeout(calculateAll, 100)">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Clear Fields
            </button>
            <button type="button" class="btn-footer btn-draft" id="btnSaveDraft" onclick="saveAsDraft()" style="background:#f59e0b; color:#fff; border:none; padding:10px 22px; border-radius:6px; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:8px; cursor:pointer; box-shadow:0 2px 5px rgba(245,158,11,0.3); transition:all 0.2s ease;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Save as Draft
            </button>
            <button type="submit" class="btn-footer btn-save" onclick="document.getElementById('bilty_status').value = 'final';">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Receipt
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let rowIndex = 1;

    // Toggle showing Billing Party dropdown when T.B.B. or Paid is selected
    function toggleBillingParty() {
        const checkedEl = document.querySelector('input[name="billing_type"]:checked');
        const overlay = document.getElementById('formBlockedOverlay');
        const billingWrapper = document.getElementById('billing_party_wrapper');
        const billingSelect = document.getElementById('billing_party_id');
        
        // Target all form elements (except the Billing Type radios and Row Rate inputs) to disable interaction
        const formElements = document.querySelectorAll('#biltyForm select');
        
        // Check if any key input fields contain data (excluding defaults like packing, description)
        let hasData = false;
        const consignor = document.getElementById('consignor_id');
        const consignee = document.getElementById('consignee_id');
        const fromLoc = document.getElementById('from_location_id');
        const toLoc = document.getElementById('to_location_id');
        
        // Check if green success notification alert is present on the page
        const successAlert = document.querySelector('div[style*="background:#d1fae5"]');
        if (successAlert) {
            hasData = true;
        }

        if (!checkedEl && !hasData) {
            overlay.style.display = 'flex';
            formElements.forEach(el => {
                if (el.tagName === 'SELECT' && $(el).data('select2')) {
                    $(el).prop('disabled', true).trigger('change.select2');
                }
            });
            return;
        }
        
        overlay.style.display = 'none';
        formElements.forEach(el => {
            if (el.tagName === 'SELECT' && $(el).data('select2')) {
                $(el).prop('disabled', false).trigger('change.select2');
            }
        });

        const billingType = checkedEl ? checkedEl.value : null;
        if (billingType === 'T.B.B.') {
            billingWrapper.style.display = 'flex';
            billingSelect.setAttribute('required', 'required');
            // Empty and lock rate inputs as read-only
            document.querySelectorAll('.input-rate').forEach(input => {
                input.value = '0.00';
                input.readOnly = true;
                input.style.backgroundColor = '#eaeaea';
            });
        } else if (billingType === 'Paid') {
            billingWrapper.style.display = 'flex';
            billingSelect.removeAttribute('required');
            
            // Explicitly enable and unlock all rate input fields
            document.querySelectorAll('.input-rate').forEach(input => {
                input.readOnly = false;
                input.disabled = false;
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.style.backgroundColor = '#ffffff';
                input.style.color = '#333';
            });

            // Restore editable rate/weight fields based on each row's weight type setting
            document.querySelectorAll('.grid-row').forEach(row => {
                const wSelect = row.querySelector('.input-weight_type');
                if (wSelect) {
                    handleWeightTypeChange(wSelect);
                }
            });
        } else {
            billingWrapper.style.display = 'none';
            billingSelect.removeAttribute('required');
            billingSelect.value = '';
            if ($(billingSelect).data('select2')) {
                $(billingSelect).val('').trigger('change');
            }
            
            // Explicitly enable and unlock all rate input fields
            document.querySelectorAll('.input-rate').forEach(input => {
                input.readOnly = false;
                input.disabled = false;
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.style.backgroundColor = '#ffffff';
                input.style.color = '#333';
            });

            // Restore editable rate/weight fields based on each row's weight type setting
            document.querySelectorAll('.grid-row').forEach(row => {
                const wSelect = row.querySelector('.input-weight_type');
                if (wSelect) {
                    handleWeightTypeChange(wSelect);
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
                <select name="items[${rowIndex}][weight_type]" class="input-weight_type" onchange="handleWeightTypeChange(this)">
                    <option value="KG">KG</option>
                    <option value="Fixed">Fixed</option>
                </select>
            </td>
            <td class="weight-col-cell">
                <input type="number" name="items[${rowIndex}][weight_val]" class="input-weight_val calc-trigger" step="0.001" value="0">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][qty]" class="input-qty calc-trigger" required min="0" step="0.001" value="0">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][rate]" class="input-rate calc-trigger" required min="0.00" step="0.01" value="0.00">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][st]" class="input-st calc-trigger" value="0.00" step="0.01">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][rc]" class="input-rc calc-trigger" value="0.00" step="0.01">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][sc]" class="input-sc calc-trigger" value="0.00" step="0.01">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][dd]" class="input-dd calc-trigger" value="0.00" step="0.01">
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
        const newWeightSelect = newRow.querySelector('.input-weight_type');
        if (newWeightSelect) {
            handleWeightTypeChange(newWeightSelect);
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
        const wSelect = row.querySelector('.input-weight_type');
        if (wSelect) {
            wSelect.addEventListener('change', function() {
                handleWeightTypeChange(this);
            });
            // Initial call to set fields enabled state correctly
            handleWeightTypeChange(wSelect);
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
    function attachPartyLookup(selectElement) {
        selectElement.addEventListener('change', function() {
            const partyId = this.value;
            const prefix = this.getAttribute('data-prefix');
            
            const mobileInput = document.getElementById(`${prefix}_mobile`);
            const gstinInput = document.getElementById(`${prefix}_gstin`);
            const addressInput = document.getElementById(`${prefix}_address`);

            if (!partyId) {
                mobileInput.value = '';
                gstinInput.value = '';
                addressInput.value = '';
                return;
            }

            // Perform fetch lookup
            fetch(`{{ url('/bilty/party-details') }}/${partyId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    mobileInput.value = data.mobile || '';
                    gstinInput.value = data.gstin || '';
                    addressInput.value = data.address || '';
                })
                .catch(err => {
                    console.error('Error fetching party details:', err);
                });
        });
    }

    function handleWeightTypeChange(selectEl) {
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

            // Enables Weight/Fixed input for Fixed types, defaults value to 0
            qtyInput.value = '0';
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

        // Attach party select details autocompleter
        document.querySelectorAll('.party-select').forEach(attachPartyLookup);
        
        // Handle Select2 custom event change integration
        $('.party-select').on('select2:select select2:clear change', function(e) {
            // Forward changes to native event handlers so attachPartyLookup runs
            if (e.originalEvent) return; // Prevent loop if native event triggers jQuery change
            const event = new Event('change', { bubbles: true });
            this.dispatchEvent(event);
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
                if (!firstInvalidEl) firstInvalidEl = fromLoc;
            }

            // 5. To Location
            const toLoc = document.getElementById('to_location_id');
            if (!toLoc || !toLoc.value) {
                errors.push("<strong>To Location:</strong> Please select the destination location.");
                if (!firstInvalidEl) firstInvalidEl = toLoc;
            }

            // 6. Consignor
            const consignor = document.getElementById('consignor_id');
            if (!consignor || !consignor.value) {
                errors.push("<strong>Consignor:</strong> Please select the Consignor party.");
                if (!firstInvalidEl) firstInvalidEl = consignor;
            }

            // 7. Consignee
            const consignee = document.getElementById('consignee_id');
            if (!consignee || !consignee.value) {
                errors.push("<strong>Consignee:</strong> Please select the Consignee party.");
                if (!firstInvalidEl) firstInvalidEl = consignee;
            }

            // 8. Third Party (if T.B.B.)
            if (billingTypeEl && billingTypeEl.value === 'T.B.B.') {
                const billingParty = document.getElementById('billing_party_id');
                if (!billingParty || !billingParty.value) {
                    errors.push("<strong>Third Party:</strong> Please select a Third Party for T.B.B. billing.");
                    if (!firstInvalidEl) firstInvalidEl = billingParty;
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

                    const weightType = row.querySelector('.input-weight_type')?.value;
                    const weightVal = parseFloat(row.querySelector('.input-weight_val')?.value) || 0;
                    const qtyVal = parseFloat(row.querySelector('.input-qty')?.value) || 0;

                    if (weightType === 'KG') {
                        if (weightVal <= 0 && qtyVal <= 0) {
                            errors.push(`<strong>Item Row #${rowNum}:</strong> Please enter the weight (KG).`);
                            if (!firstInvalidEl) firstInvalidEl = row.querySelector('.input-weight_val');
                        }
                    } else if (weightType === 'Fixed') {
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
                        if ($(firstInvalidEl).data('select2')) {
                            $(firstInvalidEl).select2('open');
                        } else {
                            firstInvalidEl.focus();
                            if (typeof firstInvalidEl.select === 'function') firstInvalidEl.select();
                        }
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

                        // Format and map radio buttons selection
                        const radio = document.querySelector(`input[name="billing_type"][value="${data.bilty.billing_type}"]`);
                        if (radio) {
                            radio.checked = true;
                            toggleBillingParty();
                        }

                        // Set Date and location IDs
                        if (data.bilty.invoice_date) {
                            document.getElementById('invoice_date').value = data.bilty.invoice_date.split('T')[0];
                        }
                        if (data.from_city_id) {
                            $('#from_location_id').val(data.from_city_id).trigger('change');
                        }
                        if (data.to_city_id) {
                            $('#to_location_id').val(data.to_city_id).trigger('change');
                        }

                        // Set party elements
                        if (data.bilty.consignor_id) {
                            $('#consignor_id').val(data.bilty.consignor_id).trigger('change');
                            document.getElementById('consignor_id').dispatchEvent(new Event('change'));
                        } else if (data.bilty.consignor_name) {
                            // If ledger doesn't exist, create a temporary option in the Select2 dropdown so it shows the name
                            const tempOpt = new Option(data.bilty.consignor_name, '', true, true);
                            $('#consignor_id').append(tempOpt).trigger('change');
                            document.getElementById('consignor_mobile').value = data.bilty.consignor_mobile || '';
                            document.getElementById('consignor_gstin').value = '';
                            document.getElementById('consignor_address').value = 'Imported Consignor (No Master Ledger)';
                        } else {
                            $('#consignor_id').val('').trigger('change');
                        }

                        if (data.bilty.consignee_id) {
                            $('#consignee_id').val(data.bilty.consignee_id).trigger('change');
                            document.getElementById('consignee_id').dispatchEvent(new Event('change'));
                        } else if (data.bilty.consignee_name) {
                            // If ledger doesn't exist, create a temporary option in the Select2 dropdown so it shows the name
                            const tempOpt = new Option(data.bilty.consignee_name, '', true, true);
                            $('#consignee_id').append(tempOpt).trigger('change');
                            document.getElementById('consignee_mobile').value = data.bilty.consignee_mobile || '';
                            document.getElementById('consignee_gstin').value = '';
                            document.getElementById('consignee_address').value = 'Imported Consignee (No Master Ledger)';
                        } else {
                            $('#consignee_id').val('').trigger('change');
                        }

                        if (data.bilty.billing_party_id) {
                            $('#billing_party_id').val(data.bilty.billing_party_id).trigger('change');
                            document.getElementById('billing_party_id').dispatchEvent(new Event('change'));
                        } else if (data.bilty.billing_party_name) {
                            const tempOpt = new Option(data.bilty.billing_party_name, '', true, true);
                            $('#billing_party_id').append(tempOpt).trigger('change');
                        } else {
                            $('#billing_party_id').val('').trigger('change');
                        }

                        // Transport info
                        document.getElementById('cn_no').value = data.bilty.cn_no || '';
                        
                        // Check if the loaded vehicle_no belongs to vehicles select2 list
                        const vehicleVal = data.bilty.vehicle_no || '';
                        const vehicleSelectEl = document.getElementById('vehicle_no_select');
                        let isDropdownOption = false;
                        
                        if (vehicleSelectEl && vehicleVal) {
                            for (let option of vehicleSelectEl.options) {
                                if (option.value === vehicleVal) {
                                    isDropdownOption = true;
                                    break;
                                }
                            }
                        }

                        const typeSelector = document.getElementById('vehicle_type');
                        if (isDropdownOption) {
                            typeSelector.value = 'Transport Name';
                            toggleVehicleFields();
                            $('#vehicle_no_select').val(vehicleVal).trigger('change');
                            document.getElementById('vehicle_no_text').value = '';
                        } else {
                            typeSelector.value = 'Vehicle Number';
                            toggleVehicleFields();
                            document.getElementById('vehicle_no_text').value = vehicleVal;
                            $('#vehicle_no_select').val('').trigger('change');
                        }

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
                            
                            const wSelect = row.querySelector(`select[name="items[${index}][weight_type]"]`);
                            wSelect.value = item.weight_type;
                            handleWeightTypeChange(wSelect);

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

                        // Hide loader after short timeout to let Select2 inputs paint properly
                        const loader = document.getElementById('biltyLoaderOverlay');
                        setTimeout(() => {
                            if (loader) loader.style.display = 'none';
                        }, 400);
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

        // Enter key to move to next field script
        biltyForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                const tag = e.target.tagName;
                const type = e.target.type;
                if (tag === 'TEXTAREA' || type === 'submit') {
                    return;
                }
                e.preventDefault();

                // Find all focusable fields that are visible and active
                const focusables = Array.from(biltyForm.querySelectorAll('input:not([type="hidden"]):not([disabled]):not([readonly]), textarea:not([disabled]), button:not([disabled]), .select2-selection:not([tabindex="-1"])'));
                
                let currentTarget = e.target;
                let nativeSelect = null;

                if (e.target.classList.contains('select2-selection')) {
                    const selectEl = $(e.target).closest('.select2-container').prev('select');
                    if (selectEl.length) {
                        nativeSelect = selectEl[0];
                    }
                } else if (e.target.tagName === 'SELECT') {
                    nativeSelect = e.target;
                }

                // If this is a Select2 dropdown, decide whether to open or move next
                if (nativeSelect) {
                    const hasValue = nativeSelect.value !== '';
                    
                    if (!hasValue) {
                        // Open the dropdown if empty
                        $(nativeSelect).select2('open');
                        return;
                    }
                    
                    // If it has value, align currentTarget to its visible container to index correctly
                    const select2Wrapper = $(nativeSelect).next('.select2-container').find('.select2-selection')[0];
                    if (select2Wrapper) {
                        currentTarget = select2Wrapper;
                    }
                }

                let index = focusables.indexOf(currentTarget);

                if (index > -1 && index < focusables.length - 1) {
                    const nextField = focusables[index + 1];
                    
                    if (nextField.classList.contains('select2-selection')) {
                        const associatedSelect = $(nextField).closest('.select2-container').prev('select');
                        if (associatedSelect.length) {
                            const nextHasValue = associatedSelect.val() !== '';
                            if (!nextHasValue) {
                                associatedSelect.select2('open');
                            } else {
                                nextField.focus();
                            }
                        }
                    } else {
                        nextField.focus();
                        if (typeof nextField.select === 'function') {
                            nextField.select();
                        }
                    }
                }
            }
        });

        // Search check for CN No lookup trigger
        biltyNoInput.addEventListener('keydown', function(e) {
            // Check if key is Enter
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
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('#from_location_id').select2({
            placeholder: 'Select From Location',
            allowClear: true,
            width: '100%',
            minimumInputLength: 1
        });
        $('#to_location_id').select2({
            placeholder: 'Select To Location',
            allowClear: true,
            width: '100%',
            minimumInputLength: 1
        });
        $('#consignor_id').select2({
            placeholder: 'Select Consignor',
            allowClear: true,
            width: '100%',
            minimumInputLength: 1
        });
        $('#consignee_id').select2({
            placeholder: 'Select Consignee',
            allowClear: true,
            width: '100%',
            minimumInputLength: 1
        });
        $('#billing_party_id').select2({
            placeholder: 'Select Third Party',
            allowClear: true,
            width: '100%',
            minimumInputLength: 1
        });
        $('#vehicle_no_select').select2({
            placeholder: 'Select Transport',
            allowClear: true,
            width: '100%',
            minimumInputLength: 1
        });

        // Auto focus search field when Select2 dropdown is opened
        $(document).on('select2:open', function() {
            setTimeout(() => {
                const searchField = document.querySelector('.select2-search__field');
                if (searchField) {
                    searchField.focus();

                    // Lock Enter key inside the search field to stop event bubbling
                    $(searchField).off('keydown').on('keydown', function(e) {
                        if (e.key === 'Enter' || e.keyCode === 13) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                        }
                    });
                }
            }, 100);
        });

        // When a value is selected, automatically trigger focus advance to the next element
        let select2ClosingPreventReopen = false;
        
        // Track when a select2 dropdown is actively closing
        $(document).on('select2:closing', function(e) {
            select2ClosingPreventReopen = true;
        });

        $(document).on('select2:close', function(e) {
            // Keep blocking reopen for a brief window to let focus move away
            setTimeout(() => {
                select2ClosingPreventReopen = false;
            }, 300);
        });

        $(document).on('select2:select', function(e) {
            const selectEl = e.target;
            select2ClosingPreventReopen = true;
            
            // Close the dropdown immediately
            $(selectEl).select2('close');

            setTimeout(() => {
                // Find all focusable fields that are visible and active
                const form = document.getElementById('biltyForm');
                const focusables = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled]):not([readonly]), textarea:not([disabled]), button:not([disabled]), .select2-selection:not([tabindex="-1"])'));
                
                // Get the select2 selection element for the current select
                const currentSelection = $(selectEl).next('.select2-container').find('.select2-selection')[0];
                
                if (currentSelection) {
                    // Blur the current select2 element so it cannot receive keydown bubble
                    currentSelection.blur();
                }

                const index = focusables.indexOf(currentSelection);

                if (index > -1 && index < focusables.length - 1) {
                    const nextField = focusables[index + 1];
                    
                    if (nextField.classList.contains('select2-selection')) {
                        const nextSelect = $(nextField).closest('.select2-container').prev('select');
                        if (nextSelect.length) {
                            nextSelect.select2('open');
                        }
                    } else {
                        nextField.focus();
                        if (typeof nextField.select === 'function') {
                            nextField.select();
                        }
                    }
                }
            }, 100);
        });

        // Block reopening during selection/Enter transition
        $(document).on('select2:opening', function(e) {
            if (select2ClosingPreventReopen) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        // Initialize e-way tags from existing values if any
        renderEwayTags();
    });

    function toggleVehicleFields() {
        const type = document.getElementById('vehicle_type').value;
        const textWrapper = document.getElementById('vehicle_input_wrapper');
        const selectWrapper = document.getElementById('vehicle_select_wrapper');
        const textInput = document.getElementById('vehicle_no_text');
        const selectEl = document.getElementById('vehicle_no_select');

        if (type === 'Vehicle Number') {
            textWrapper.style.display = 'flex';
            selectWrapper.style.display = 'none';
            textInput.setAttribute('name', 'vehicle_no');
            // Remove name attribute from dropdown so it does not submit duplicate key data
            selectEl.removeAttribute('name');
            // Clear opposite field
            $(selectEl).val('').trigger('change');
        } else {
            textWrapper.style.display = 'none';
            selectWrapper.style.display = 'flex';
            textInput.removeAttribute('name');
            selectEl.setAttribute('name', 'vehicle_no');
            // Clear opposite field
            textInput.value = '';
        }
    }

    // Bind change listener on select2 select
    $(document).ready(function() {
        $('#vehicle_no_select').on('select2:select', function(e) {
            // No custom action needed, just ensures selection is registered
        });
        // Initial call
        toggleVehicleFields();
    });

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
</script>
@endsection
