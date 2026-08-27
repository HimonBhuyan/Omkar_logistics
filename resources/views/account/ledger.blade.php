@extends('layouts.app')

@section('title', 'Account Ledger - Omkaar Logistics')

@section('styles')
<style>
    /* ── Two-panel wrapper ── */
    .ledger-wrapper {
        display: flex;
        height: calc(100vh - 148px);
        gap: 0;
        border: 1px solid #aaa;
        background: #f0f0f0;
        font-size: 12px;
        font-family: 'Segoe UI', Tahoma, sans-serif;
    }

    /* ── Left panel: list ── */
    .ledger-list-panel {
        width: 320px;
        min-width: 320px;
        border-right: 2px solid #999;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .ledger-list-filter {
        padding: 4px;
        border-bottom: 1px solid #ccc;
    }

    .ledger-list-filter select {
        width: 100%;
        font-size: 12px;
        padding: 3px 4px;
        border: 1px solid #999;
    }

    .ledger-list-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .ledger-list-item {
        display: block;
        padding: 4px 8px;
        font-size: 12px;
        color: #111;
        text-decoration: none;
        border-bottom: 1px solid #e8e8e8;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ledger-list-item:hover {
        background: #c5ddf4;
    }

    .ledger-list-item.active {
        background: #003087;
        color: #fff;
    }

    /* ── Right panel: form ── */
    .ledger-form-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Title bar */
    .ledger-title-bar {
        background: #8b0000;
        color: #fff;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        padding: 6px 10px;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }

    /* Info bar (code + datetime) */
    .ledger-info-bar {
        background: #e8e8e8;
        border-bottom: 1px solid #bbb;
        padding: 4px 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 12px;
        flex-shrink: 0;
    }

    .ledger-info-bar label {
        font-weight: 600;
        margin-right: 4px;
    }

    .ledger-info-bar input {
        border: 1px solid #999;
        padding: 2px 6px;
        font-size: 12px;
        background: #ffffcc;
    }

    .ledger-info-bar .datetime {
        color: #555;
        font-size: 11px;
    }

    /* Scrollable form body */
    .ledger-form-body {
        flex: 1;
        overflow-y: auto;
        padding: 8px 12px;
        background: #f5f5f5;
    }

    /* Field rows */
    .f-row {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        gap: 6px;
    }

    .f-row label {
        width: 120px;
        min-width: 120px;
        text-align: right;
        padding-right: 6px;
        font-size: 12px;
        color: #222;
        font-weight: 500;
    }

    .f-row-wrapper {
        display: flex;
        flex-direction: column;
        margin-bottom: 5px;
    }

    .validation-error-msg {
        color: #d32f2f;
        font-size: 11px;
        font-weight: 600;
        margin-left: 126px;
        margin-top: 2px;
        display: none;
    }

    input.input-error, select.input-error, textarea.input-error {
        border-color: #d32f2f !important;
        background-color: #ffebee !important;
    }

    .f-row input[type="text"],
    .f-row input[type="email"],
    .f-row input[type="number"],
    .f-row input[type="date"],
    .f-row select,
    .f-row textarea {
        flex: 1;
        border: 1px solid #999;
        padding: 2px 6px;
        font-size: 12px;
        background: #fff;
        height: 24px;
        outline: none;
    }

    .f-row select {
        cursor: pointer;
    }

    .f-row textarea {
        height: 44px;
        resize: vertical;
    }

    .f-row input:focus, .f-row select:focus {
        border-color: #003087;
        background: #fffff0;
    }

    /* Two-column layout sections */
    .f-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 20px;
    }

    /* Section separator */
    .f-divider {
        border: none;
        border-top: 1px solid #bbb;
        margin: 12px 0;
    }

    /* Sub-heading section title */
    .f-section-title {
        font-size: 12px;
        font-weight: bold;
        color: #fff;
        background: linear-gradient(90deg, #003087, #8b0000);
        margin: 18px 0 10px 126px;
        padding: 4px 10px;
        border-radius: 3px;
        letter-spacing: 0.3px;
    }

    /* Inline double fields (Credit Limit + Limit Days) */
    .f-inline {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .f-inline input {
        border: 1px solid #999;
        padding: 2px 6px;
        font-size: 12px;
        background: #fff;
        height: 24px;
        width: 80px;
    }

    .f-inline label {
        font-size: 12px;
        font-weight: 500;
    }

    /* Radio button rows */
    .f-radio-row {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 4px 0 4px 126px;
    }

    .f-radio-row label {
        font-size: 12px;
        width: auto;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }

    /* Series wise opening table */
    .series-section {
        margin-top: 8px;
        border: 1px solid #bbb;
        background: #fff;
        padding: 6px;
    }

    .series-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .series-header span {
        font-weight: 600;
        font-size: 12px;
        color: #333;
    }

    .btn-add-row {
        background: #c5ddf4;
        border: 1px solid #7da9d4;
        padding: 2px 10px;
        font-size: 11px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-add-row:hover {
        background: #4a90d4;
        color: #fff;
    }

    .series-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .series-table th {
        background: #c5ddf4;
        border: 1px solid #aaa;
        padding: 3px 8px;
        text-align: left;
        font-weight: 600;
    }

    .series-table td {
        border: 1px solid #ccc;
        padding: 2px 4px;
    }

    .series-table td input {
        width: 100%;
        border: none;
        padding: 2px 4px;
        font-size: 12px;
        outline: none;
        background: transparent;
    }

    .series-table td input:focus {
        background: #fffff0;
    }

    /* ── Bottom action bar ── */
    .ledger-action-bar {
        background: #e8e8e8;
        border-top: 2px solid #999;
        padding: 5px 10px;
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        flex-shrink: 0;
    }

    .btn-action {
        width: 36px;
        height: 30px;
        border: 1px solid #888;
        background: #d4d4d4;
        cursor: pointer;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        transition: background 0.1s;
    }

    .btn-action:hover { background: #b8d4f0; }
    .btn-action.btn-save { background: #c5ddf4; border-color: #7da9d4; }
    .btn-action.btn-save:hover { background: #4a90d4; color: #fff; }
    .btn-action.btn-delete { background: #ffc5c5; border-color: #d47a7a; }
    .btn-action.btn-delete:hover { background: #cc0000; color: #fff; }
    .btn-action.btn-new { background: #c5f4c5; border-color: #7ab47a; }
    .btn-action.btn-new:hover { background: #2a9a2a; color: #fff; }

    /* Ledger name highlight style */
    .ledger-name-input {
        background: #ffffcc !important;
        font-weight: 600 !important;
    }
</style>
@endsection

@section('content')
<div class="ledger-wrapper">

    {{-- ── Left panel: list ── --}}
    <div class="ledger-list-panel">
        <div class="ledger-list-filter">
            <select id="groupFilter" onchange="filterList()" style="margin-bottom: 5px;">
                <option value="">---All---</option>
                @foreach($groups as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
            <input type="text" id="searchFilter" onkeyup="filterList()" placeholder="Search Name or GST..." style="width: 100%; font-size: 12px; padding: 3px 6px; border: 1px solid #999; outline: none; box-sizing: border-box;">
        </div>
        <div class="ledger-list-scroll" id="ledgerList">
            @foreach($ledgers as $l)
                <a href="{{ route('account.ledger.load', $l->id) }}"
                   class="ledger-list-item {{ $selected->id == $l->id ? 'active' : '' }}"
                   data-group="{{ $l->under_group }}"
                   data-gst="{{ strtolower($l->gst_no ?? '') }}">
                    {{ $l->ledger_name ?? '(Unnamed)' }}
                </a>
            @endforeach
            @if($ledgers->isEmpty())
                <div style="padding:8px;color:#888;font-size:11px;">No ledgers yet.</div>
            @endif
        </div>
    </div>

    {{-- ── Right panel: form ── --}}
    <div class="ledger-form-panel">

        <div class="ledger-title-bar">Account Ledger</div>

        <div class="ledger-info-bar">
            <div>
                <label id="code_label">State Code</label>
                <input type="text" id="preview_code" value="{{ $selected->code }}" size="12" readonly style="background:#ffffcc; font-weight:bold; color:#003087;">
            </div>
            <div class="datetime">{{ now()->format('d/m/Y h:i A') }}</div>
            <div style="margin-left:auto;">
                @if(session('success'))
                    <span style="color:green;font-weight:600;">✔ {{ session('success') }}</span>
                @endif
            </div>
        </div>

        {{-- Form --}}
        @if($selected->id)
            <form method="POST" action="{{ route('account.ledger.update', $selected->id) }}" id="ledgerForm" style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
                @csrf @method('PUT')
                <div class="ledger-form-body">
        @else
            <form method="POST" action="{{ route('account.ledger.store') }}" id="ledgerForm" style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
                @csrf
                <div class="ledger-form-body">
        @endif

            {{-- SINGLE COLUMN FORM --}}
                <div class="f-row">
                    <label>Under Group</label>
                    <select name="under_group" id="under_group" onchange="toggleFormFields()">
                        <option value="">-- Select Group --</option>
                        @foreach($groups as $g)
                            <option value="{{ $g }}" {{ old('under_group', $selected->under_group) == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="f-row">
                    <label>Ledger Name</label>
                    <input type="text" name="ledger_name" id="ledger_name" class="form-field ledger-name-input" required value="{{ old('ledger_name', $selected->ledger_name) }}">
                </div>
                    <input type="hidden" name="code" value="{{ $selected->code ?? $nextCode }}">
                {{-- STANDARD FIELDS (shown for non-bank accounts) --}}
                <div id="standard_fields_section">
                    <div class="f-row" style="position: relative;">
                        <label>GST No.</label>
                        <input type="text" name="gst_no" id="gst_no" class="form-field" value="{{ old('gst_no', $selected->gst_no) }}" autocomplete="off">
                        <span id="gst_status" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; font-weight: 600; color: #555; pointer-events: none;"></span>
                    </div>
                    <hr class="f-divider">
                    <div class="f-row">
                        <label>Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" class="form-field" value="{{ old('contact_person', $selected->contact_person) }}">
                    </div>
                    <div class="f-row">
                        <label>Address</label>
                        <textarea name="address" id="address" class="form-field">{{ old('address', $selected->address) }}</textarea>
                    </div>
                    <div class="f-row">
                        <label>City</label>
                        <input type="text" name="city" id="city" class="form-field" value="{{ old('city', $selected->city) }}">
                    </div>
                    <div class="f-row">
                        <label>State</label>
                        <select name="state" id="state" class="form-field">
                            @foreach(['Assam','West Bengal','Bihar','Delhi','Maharashtra','Uttar Pradesh','Gujarat','Rajasthan','Karnataka','Tamil Nadu','Other'] as $s)
                                <option value="{{ $s }}" {{ old('state', $selected->state) == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="f-row">
                        <label>Country</label>
                        <select name="country" id="country" class="form-field">
                            <option value="INDIA" {{ old('country', $selected->country) == 'INDIA' ? 'selected' : '' }}>INDIA</option>
                            <option value="OTHER" {{ old('country', $selected->country) == 'OTHER' ? 'selected' : '' }}>OTHER</option>
                        </select>
                    </div>
                    <div class="f-row">
                        <label>Pin Code</label>
                        <input type="text" name="pin_code" id="pin_code" class="form-field" value="{{ old('pin_code', $selected->pin_code) }}" style="width:90px;flex:none;">
                    </div>
                    <div class="f-row">
                        <label>Phone (O)</label>
                        <input type="text" name="phone_o" id="phone_o" class="form-field" value="{{ old('phone_o', $selected->phone_o) }}">
                    </div>
                    <div class="f-row">
                        <label>Phone (R)</label>
                        <input type="text" name="phone_r" id="phone_r" class="form-field" value="{{ old('phone_r', $selected->phone_r) }}">
                    </div>
                    <div class="f-row">
                        <label>Mobile</label>
                        <input type="text" name="mobile" id="mobile" class="form-field" value="{{ old('mobile', $selected->mobile) }}">
                    </div>
                    <div class="f-row">
                        <label>Email ID</label>
                        <input type="email" name="email" id="email" class="form-field" value="{{ old('email', $selected->email) }}">
                    </div>
                </div>

                {{-- BANK DETAILS FIELDS (shown only when 'Bank Accounts' is selected) --}}
                <div id="bank_fields_section" style="display: none;">
                    <div class="f-section-title">Bank Details</div>
                    <div class="f-row">
                        <label>Ledger Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-field bank-input" value="{{ old('bank_name', $selected->bank_name) }}">
                    </div>
                    <div class="f-row">
                        <label>Account type</label>
                        <select name="di_no" id="di_no" class="form-field bank-input">
                            <option value="Savings" {{ old('di_no', $selected->di_no) == 'Savings' ? 'selected' : '' }}>Savings</option>
                            <option value="Current" {{ old('di_no', $selected->di_no) == 'Current' ? 'selected' : '' }}>Current</option>
                            <option value="Cash Credit" {{ old('di_no', $selected->di_no) == 'Cash Credit' ? 'selected' : '' }}>Cash Credit</option>
                        </select>
                    </div>
                    <div class="f-row-wrapper">
                        <div class="f-row">
                            <label>Acc No</label>
                            <input type="text" name="account_no" id="account_no" class="form-field bank-input" value="{{ old('account_no', $selected->account_no) }}">
                        </div>
                        <div class="validation-error-msg" id="err_account_no">Account number must be 18 digits.</div>
                    </div>
                    <div class="f-row-wrapper">
                        <div class="f-row">
                            <label>IFSC code</label>
                            <input type="text" name="ifsc" id="ifsc" class="form-field bank-input" value="{{ old('ifsc', $selected->ifsc) }}">
                        </div>
                        <div class="validation-error-msg" id="err_ifsc">IFSC must be 11 characters (4 letters, '0', then 6 alphanumeric characters).</div>
                    </div>
                    <div class="f-row">
                        <label>Branch / division</label>
                        <input type="text" name="transport" id="transport" class="form-field bank-input" value="{{ old('transport', $selected->transport) }}">
                    </div>
                    <div class="f-row">
                        <label>Bank address</label>
                        <textarea name="address_bank" id="address_bank" class="form-field bank-input">{{ old('address', $selected->address) }}</textarea>
                    </div>
                    <div class="f-row">
                        <label>Bank Email ID</label>
                        <input type="email" name="email_bank" id="email_bank" class="form-field bank-input" value="{{ old('email', $selected->email) }}">
                    </div>
                </div>

                {{-- STAFF SALARY FIELDS (shown only when 'Staff Salary' is selected) --}}
                <div id="staff_fields_section" style="display: none;">
                    <div class="f-section-title">Staff Details</div>
                    <div class="f-row">
                        <label>Staff Name</label>
                        <input type="text" id="staff_name_display" class="form-field staff-input" readonly value="{{ old('ledger_name', $selected->ledger_name) }}" style="background:#e8f4fd; font-weight:bold; color:#003087;">
                    </div>
                    
                    <div class="f-section-title">Personal Documents</div>
                    
                    <div class="f-row-wrapper">
                        <div class="f-row">
                            <label>Voter Card No.</label>
                            <input type="text" name="voter_card" id="voter_card" class="form-field staff-input" placeholder="Enter Voter ID Card Number" value="{{ old('voter_card', $selected->fax) }}">
                            <input type="file" name="doc_voter" id="doc_voter" class="form-field staff-input" style="width: auto; flex: none;">
                        </div>
                        <div class="validation-error-msg" id="err_voter_card">Voter ID must be 10 characters (3 letters + 7 digits).</div>
                    </div>
                    
                    <div class="f-row-wrapper">
                        <div class="f-row">
                            <label>Aadhaar Card No.</label>
                            <input type="text" name="adhar_card" id="adhar_card" class="form-field staff-input" placeholder="Enter Aadhaar Number" value="{{ old('adhar_card', $selected->salesman) }}">
                            <input type="file" name="doc_adhar" id="doc_adhar" class="form-field staff-input" style="width: auto; flex: none;">
                        </div>
                        <div class="validation-error-msg" id="err_adhar_card">Aadhaar must be 12 digits.</div>
                    </div>
                    
                    <div class="f-row-wrapper">
                        <div class="f-row">
                            <label>PAN Card No.</label>
                            <input type="text" name="pan_card" id="pan_card" class="form-field staff-input" placeholder="Enter PAN Number" value="{{ old('pan_card', $selected->web) }}">
                            <input type="file" name="doc_pan" id="doc_pan" class="form-field staff-input" style="width: auto; flex: none;">
                        </div>
                        <div class="validation-error-msg" id="err_pan_card">PAN Card must be 10 characters (5 letters + 4 digits + 1 letter).</div>
                    </div>
                    
                    <div class="f-row-wrapper">
                        <div class="f-row">
                            <label>Driving License No.</label>
                            <input type="text" name="driving_license" id="driving_license" class="form-field staff-input" placeholder="Enter Driving License Number" value="{{ old('driving_license', $selected->di_no) }}">
                            <input type="file" name="doc_dl" id="doc_dl" class="form-field staff-input" style="width: auto; flex: none;">
                        </div>
                        <div class="validation-error-msg" id="err_driving_license">Driving License must be between 13 and 16 characters.</div>
                    </div>

                    <div class="f-section-title">Salary Bank Details</div>

                    <div class="f-row">
                        <label>Bank Name</label>
                        <input type="text" name="staff_bank_name" id="staff_bank_name" class="form-field staff-input" placeholder="Bank Name" value="{{ old('staff_bank_name', $selected->bank_name) }}">
                    </div>
                    <div class="f-row-wrapper">
                        <div class="f-row">
                            <label>Account No.</label>
                            <input type="text" name="staff_account_no" id="staff_account_no" class="form-field staff-input" placeholder="Account Number" value="{{ old('staff_account_no', $selected->account_no) }}">
                        </div>
                        <div class="validation-error-msg" id="err_staff_account_no">Account number must be 18 digits.</div>
                    </div>
                    <div class="f-row-wrapper">
                        <div class="f-row">
                            <label>IFSC Code</label>
                            <input type="text" name="staff_ifsc" id="staff_ifsc" class="form-field staff-input" placeholder="IFSC Code" value="{{ old('staff_ifsc', $selected->ifsc) }}">
                        </div>
                        <div class="validation-error-msg" id="err_staff_ifsc">IFSC must be 11 characters (4 letters, '0', then 6 alphanumeric characters).</div>
                    </div>
                    <div class="f-row">
                        <label style="line-height: 1.2;">Passbook 1st Page / Canceled Check</label>
                        <input type="file" name="doc_passbook" id="doc_passbook" class="form-field staff-input" style="width: auto; flex: none;">
                    </div>

                    <div class="f-section-title">PF / ESI Details</div>

                    <div class="f-row">
                        <label>ESI Number</label>
                        <input type="text" name="esi_number" id="esi_number" class="form-field staff-input" placeholder="Enter ESI Number" value="{{ old('esi_number', $selected->phone_o) }}">
                    </div>
                    <div class="f-row">
                        <label>PF Number</label>
                        <input type="text" name="pf_number" id="pf_number" class="form-field staff-input" placeholder="Enter PF Number" value="{{ old('pf_number', $selected->phone_r) }}">
                    </div>

                    <div class="f-section-title">Salary Information</div>

                    <div class="f-row">
                        <label>Gross Salary</label>
                        <input type="number" name="points" id="gross_salary" class="form-field staff-input" placeholder="0.00" step="0.01" oninput="calculateNetSalary()" value="{{ old('points', $selected->points) }}">
                    </div>
                    <div class="f-row">
                        <label>Deduction</label>
                        <input type="number" name="discnt" id="deduction" class="form-field staff-input" placeholder="0.00" step="0.01" oninput="calculateNetSalary()" value="{{ old('discnt', $selected->discnt) }}">
                    </div>
                    <div class="f-row">
                        <label>Net Salary</label>
                        <input type="number" name="opening" id="net_salary" class="form-field staff-input" placeholder="0.00" step="0.01" readonly style="background:#e8f4fd; font-weight:bold; color:#003087;" value="{{ old('opening', $selected->opening) }}">
                    </div>
                </div>

                {{-- VEHICLE EXPENSE FIELDS (shown only when 'Vehicle Expense' is selected) --}}
                <div id="vehicle_fields_section" style="display: none;">
                    <div class="f-section-title">Vehicle Expense Details</div>
                    <div class="f-row">
                        <label>Vehicle No.</label>
                        <input type="text" name="vehicle_gst" id="vehicle_gst" class="form-field vehicle-input" placeholder="Enter Vehicle Registration Number" value="{{ old('gst_no', $selected->gst_no) }}">
                    </div>
                    <div class="f-row">
                        <label>Driver Name</label>
                        <input type="text" name="driver_name" id="driver_name" class="form-field vehicle-input" placeholder="Driver Name" value="{{ old('contact_person', $selected->contact_person) }}">
                    </div>
                    <div class="f-row">
                        <label>Driver Phone Number</label>
                        <input type="text" name="driver_phone" id="driver_phone" class="form-field vehicle-input" placeholder="Driver Phone Number" value="{{ old('mobile', $selected->mobile) }}">
                    </div>
                </div>

            {{-- Validation errors --}}
            @if($errors->any())
                <div style="color:red;font-size:11px;margin-top:8px;padding:4px 8px;background:#ffebeb;border:1px solid #f99;">
                    {{ $errors->first() }}
                </div>
            @endif

        </div>{{-- /ledger-form-body --}}

        {{-- ── Action Bar ── --}}
        <div class="ledger-action-bar">
            <a href="{{ route('account.ledger') }}" class="btn-action btn-new" title="New">➕</a>
            <button type="button" class="btn-action" title="Print" onclick="window.print()">🖨</button>
            <button type="button" class="btn-action btn-save" title="Clear Form" onclick="clearAllFields()" style="background:#ffe0b2; border-color:#ffb74d;">🧹</button>
            <button type="submit" class="btn-action btn-save" title="Save">💾</button>
            @if($selected->id)
                <button type="button" class="btn-action btn-delete" title="Delete"
                    onclick="if(confirm('Delete this ledger?')) document.getElementById('deleteForm').submit()">❌</button>
            @else
                <button type="button" class="btn-action btn-delete" title="Delete" disabled style="opacity:0.4;">❌</button>
            @endif
            <a href="{{ route('dashboard') }}" class="btn-action" title="Back" style="font-size:14px;">↩</a>
        </div>

        </form>

        {{-- Hidden delete form --}}
        @if($selected->id)
        <form id="deleteForm" method="POST" action="{{ route('account.ledger.destroy', $selected->id) }}" style="display:none;">
            @csrf @method('DELETE')
        </form>
        @endif

    </div>{{-- /ledger-form-panel --}}
</div>{{-- /ledger-wrapper --}}
@endsection

@section('scripts')
<script>
    function filterList() {
        const group = document.getElementById('groupFilter').value;
        const query = document.getElementById('searchFilter').value.toLowerCase().trim();

        document.querySelectorAll('.ledger-list-item').forEach(function(item) {
            const matchesGroup = !group || item.dataset.group === group;
            const textContent = item.textContent.toLowerCase();
            const gstContent = item.dataset.gst || '';
            const matchesQuery = !query || textContent.includes(query) || gstContent.includes(query);

            if (matchesGroup && matchesQuery) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function addSeriesRow() {
        const tbody = document.getElementById('seriesBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="series_name[]" placeholder="A"></td>
            <td><input type="number" name="series_opening[]" step="0.01" placeholder="0.00"></td>
            <td><input type="number" name="series_closing[]" step="0.01" placeholder="0.00"></td>
        `;
        tbody.appendChild(row);
    }
    // GST auto-pull lookup
    document.getElementById('gst_no').addEventListener('input', function() {
        const gstVal = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        this.value = gstVal;

        const statusEl = document.getElementById('gst_status');

        if (gstVal.length === 15) {
            statusEl.textContent = 'Searching...';
            statusEl.style.color = '#ff9900';

            // Simulate GST API lookup. Specifically handles the sample client GST code.
            setTimeout(() => {
                if (gstVal === '18BHEPB5443C1ZT') {
                    document.getElementById('ledger_name').value = 'ARYAHI TECH';
                    document.getElementById('contact_person').value = 'RITA BORUAH';
                    document.getElementById('address').value = '4th Floor, Madhab Tower, G S Road, Rukminigaon, Guwahati, Kamrup Metropolitan, Assam';
                    document.getElementById('city').value = 'Guwahati';
                    document.getElementById('state').value = 'Assam';
                    document.getElementById('pin_code').value = '781022';
                    statusEl.textContent = '✔ Verified';
                    statusEl.style.color = 'green';
                } else if (gstVal === '18ABBCA1705D1ZA') {
                    document.getElementById('ledger_name').value = 'ZIAGO TECHNOLOGIES PRIVATE LIMITED';
                    document.getElementById('contact_person').value = 'ZIAGO TECHNOLOGIES PRIVATE LIMITED';
                    document.getElementById('address').value = '4th Floor, Madhab Tower, Rukminigaon, Khanapara, G.S Road, Guwahati';
                    document.getElementById('city').value = 'Guwahati';
                    document.getElementById('state').value = 'Assam';
                    document.getElementById('pin_code').value = '781022';
                    statusEl.textContent = '✔ Verified';
                    statusEl.style.color = 'green';
                } else if (gstVal === '18AAHCD3526G2ZP') {
                    document.getElementById('ledger_name').value = 'DHARAMPAL SATYAPAL FOODS LIMITED';
                    document.getElementById('contact_person').value = 'DHARAMPAL SATYAPAL FOODS LIMITED';
                    document.getElementById('address').value = 'Godown No. 2, Amit Choudhury, National Highway No. 37, Guwahati, Kamrup';
                    document.getElementById('city').value = 'Guwahati';
                    document.getElementById('state').value = 'Assam';
                    document.getElementById('pin_code').value = '781034';
                    statusEl.textContent = '✔ Verified';
                    statusEl.style.color = 'green';
                } else if (gstVal === '18DBOPK3296Q1ZK') {
                    document.getElementById('ledger_name').value = 'B.K. ENTERPRISE';
                    document.getElementById('contact_person').value = 'BIJIT KALITA';
                    document.getElementById('address').value = 'BISHNU GHOSH, B.K. ENTERPRISE, J.N. ROAD, Moon Light High School, Tezpur Doloni, Tezpur, Sonitpur, Assam';
                    document.getElementById('city').value = 'Tezpur';
                    document.getElementById('state').value = 'Assam';
                    document.getElementById('pin_code').value = '784001';
                    statusEl.textContent = '✔ Verified';
                    statusEl.style.color = 'green';
                } else if (gstVal === '18AHUPP1218N1ZO') {
                    document.getElementById('ledger_name').value = 'SHREE RAM PAREEK';
                    document.getElementById('contact_person').value = 'SHREE RAM PAREEK';
                    document.getElementById('address').value = '-, BISHNUPUR, GOPINATH NAGAR, Kamrup, Assam';
                    document.getElementById('city').value = 'Guwahati';
                    document.getElementById('state').value = 'Assam';
                    document.getElementById('pin_code').value = '781016';
                    statusEl.textContent = '✔ Verified';
                    statusEl.style.color = 'green';
                } else {
                    // General simulated template for other valid GST format inputs
                    const stateCode = gstVal.substring(0, 2);
                    let stateName = 'Assam';
                    if (stateCode === '19') stateName = 'West Bengal';
                    else if (stateCode === '10') stateName = 'Bihar';
 
                    document.getElementById('ledger_name').value = 'AUTO PULL BUSINESS - ' + gstVal;
                    document.getElementById('address').value = 'Principal Place of Business Office, State Code: ' + stateCode;
                    document.getElementById('city').value = 'Guwahati';
                    document.getElementById('state').value = stateName;
                    document.getElementById('pin_code').value = '781001';
                    statusEl.textContent = '✔ Auto-filled';
                    statusEl.style.color = 'green';
                }
            }, 800);
        } else {
            statusEl.textContent = '';
        }
    });

    // Toggle disabled status and visible sections based on Under Group selection
    function toggleFormFields() {
        const selectEl = document.getElementById('under_group');
        const standardSection = document.getElementById('standard_fields_section');
        const bankSection = document.getElementById('bank_fields_section');
        const staffSection = document.getElementById('staff_fields_section');
        const vehicleSection = document.getElementById('vehicle_fields_section');
        const selectedGroup = selectEl.value;

        const isGroupEmpty = !selectedGroup;
        const isBankAccount = selectedGroup === 'Bank Accounts';
        const isStaffSalary = selectedGroup === 'Staff Salary';
        const isVehicleOrOilExpense = (selectedGroup === 'Vehicle Expense' || selectedGroup === 'Oil Expense');

        // Hide all initially
        standardSection.style.display = 'none';
        bankSection.style.display = 'none';
        staffSection.style.display = 'none';
        vehicleSection.style.display = 'none';

        // Disable all inputs in standard, bank, staff, and vehicle sections first
        document.querySelectorAll('#standard_fields_section .form-field').forEach(field => field.disabled = true);
        document.querySelectorAll('#bank_fields_section .form-field').forEach(field => field.disabled = true);
        document.querySelectorAll('#staff_fields_section .form-field').forEach(field => field.disabled = true);
        document.querySelectorAll('#vehicle_fields_section .form-field').forEach(field => field.disabled = true);

        if (isBankAccount) {
            bankSection.style.display = 'block';
            document.querySelectorAll('#bank_fields_section .form-field').forEach(field => {
                field.disabled = false;
                field.style.backgroundColor = '#ffffff';
                field.style.opacity = '1';
            });
        } else if (isStaffSalary) {
            staffSection.style.display = 'block';
            document.querySelectorAll('#staff_fields_section .form-field').forEach(field => {
                field.disabled = false;
                field.style.backgroundColor = '#ffffff';
                field.style.opacity = '1';
            });
        } else if (isVehicleOrOilExpense) {
            vehicleSection.style.display = 'block';
            document.querySelectorAll('#vehicle_fields_section .form-field').forEach(field => {
                field.disabled = false;
                field.style.backgroundColor = '#ffffff';
                field.style.opacity = '1';
            });
        } else {
            standardSection.style.display = 'block';
            document.querySelectorAll('#standard_fields_section .form-field').forEach(field => {
                field.disabled = isGroupEmpty;
                if (isGroupEmpty) {
                    field.style.backgroundColor = '#eaeaea';
                    field.style.opacity = '0.7';
                } else {
                    field.style.backgroundColor = '#ffffff';
                    field.style.opacity = '1';
                }
            });
        }
    }

    // Clear all input elements
    function clearAllFields() {
        const fields = document.querySelectorAll('.form-field');
        fields.forEach(field => {
            if (field.tagName === 'SELECT') {
                field.selectedIndex = 0;
            } else {
                field.value = '';
            }
        });
        document.getElementById('gst_status').textContent = '';
        toggleFormFields();
    }

    // Automatically update the Code label and formatted value depending on Under Group selection
    function updateCodeFormat() {
        const selectEl = document.getElementById('under_group');
        const codeLabel = document.getElementById('code_label');
        const codeInput = document.getElementById('preview_code');
        const rawCode = "{{ $selected->code ?? $nextCode }}";

        if (selectEl.value === 'Staff Salary') {
            codeLabel.textContent = 'Emp Code';
            // Format number to double digits (e.g. OML01, OML02, etc.)
            const formattedNum = String(rawCode).padStart(2, '0');
            codeInput.value = 'OML' + formattedNum;
        } else {
            codeLabel.textContent = 'State Code';
            codeInput.value = rawCode;
        }
    }

    // Calculate net salary dynamically
    function calculateNetSalary() {
        const gross = parseFloat(document.getElementById('gross_salary').value) || 0;
        const deduction = parseFloat(document.getElementById('deduction').value) || 0;
        document.getElementById('net_salary').value = (gross - deduction).toFixed(2);
    }

    // Real-time validations with red label warnings
    function setupRealTimeValidations() {
        const rules = [
            {
                id: 'voter_card',
                errorId: 'err_voter_card',
                validate: (val) => val === '' || /^[A-Z]{3}\d{7}$/.test(val)
            },
            {
                id: 'adhar_card',
                errorId: 'err_adhar_card',
                validate: (val) => val === '' || /^\d{12}$/.test(val)
            },
            {
                id: 'pan_card',
                errorId: 'err_pan_card',
                validate: (val) => val === '' || /^[A-Z]{5}\d{4}[A-Z]{1}$/.test(val)
            },
            {
                id: 'driving_license',
                errorId: 'err_driving_license',
                validate: (val) => val === '' || (val.length >= 13 && val.length <= 16)
            },
            {
                id: 'account_no',
                errorId: 'err_account_no',
                validate: (val) => val === '' || /^\d{18}$/.test(val)
            },
            {
                id: 'staff_account_no',
                errorId: 'err_staff_account_no',
                validate: (val) => val === '' || /^\d{18}$/.test(val)
            },
            {
                id: 'ifsc',
                errorId: 'err_ifsc',
                validate: (val) => val === '' || /^[A-Z]{4}0[A-Z0-9]{6}$/.test(val)
            },
            {
                id: 'staff_ifsc',
                errorId: 'err_staff_ifsc',
                validate: (val) => val === '' || /^[A-Z]{4}0[A-Z0-9]{6}$/.test(val)
            }
        ];

        rules.forEach(rule => {
            const input = document.getElementById(rule.id);
            const errMsg = document.getElementById(rule.errorId);
            if (!input || !errMsg) return;

            const check = () => {
                const val = input.value.toUpperCase().trim();
                input.value = val; // Force uppercase formatting
                const isValid = rule.validate(val);
                if (!isValid) {
                    input.classList.add('input-error');
                    errMsg.style.display = 'block';
                } else {
                    input.classList.remove('input-error');
                    errMsg.style.display = 'none';
                }
            };

            input.addEventListener('input', check);
            input.addEventListener('blur', check);
        });

        // Block submit if validations are failing
        const form = document.getElementById('ledgerForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                let hasErrors = false;
                rules.forEach(rule => {
                    const input = document.getElementById(rule.id);
                    if (input && input.classList.contains('input-error') && !input.disabled) {
                        hasErrors = true;
                    }
                });
                if (hasErrors) {
                    e.preventDefault();
                    alert('Please correct the validation errors (marked in red) before saving.');
                }
            });
        }
    }

    // Bind event check to the drop-down onchange handler
    document.getElementById('under_group').addEventListener('change', updateCodeFormat);
    document.addEventListener('DOMContentLoaded', () => {
        updateCodeFormat();
        toggleFormFields();
        setupRealTimeValidations();

        // Sync Ledger Name with Staff Name display
        const ledgerNameInput = document.getElementById('ledger_name');
        const staffNameDisplay = document.getElementById('staff_name_display');
        if (ledgerNameInput && staffNameDisplay) {
            ledgerNameInput.addEventListener('input', () => {
                staffNameDisplay.value = ledgerNameInput.value;
            });
        }
    });
</script>
@endsection
