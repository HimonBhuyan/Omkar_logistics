<form action="{{ $actionUrl ?? route('bilty.store') }}" method="{{ $method ?? 'POST' }}" id="biltyForm" novalidate>
    @csrf
    @if(isset($isUpdate) && $isUpdate)
        @method('PUT')
    @endif
    
    <!-- Billing Options at the top -->
    <div class="billing-options" style="background:#e3f2fd; border-bottom:1px solid #bbdefb; padding:15px 20px; display:flex; gap:25px; align-items:center; border-radius:0;">
        <span style="font-weight:700; color:#0f3460; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">BILLING TYPE:</span>
        <label style="font-weight:700; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer; text-transform:uppercase;">
            <input type="radio" name="billing_type" value="Paid" {{ old('billing_type', $billingType ?? '') == 'Paid' ? 'checked' : '' }} onclick="toggleBillingParty()">
            PAID
        </label>
        <label style="font-weight:700; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer; text-transform:uppercase;">
            <input type="radio" name="billing_type" value="To Pay" {{ old('billing_type', $billingType ?? '') == 'To Pay' ? 'checked' : '' }} onclick="toggleBillingParty()">
            TO PAY
        </label>
        <label style="font-weight:700; font-size:13px; display:flex; align-items:center; gap:8px; cursor:pointer; text-transform:uppercase;">
            <input type="radio" name="billing_type" value="T.B.B." {{ old('billing_type', $billingType ?? '') == 'T.B.B.' ? 'checked' : '' }} onclick="toggleBillingParty()">
            T.B.B. (TO BE BILLED)
        </label>
    </div>

    <!-- Red Title Bar -->
    <div class="bilty-header-bar">
        <div style="display:flex; align-items:center; gap:10px;">
            <span>C.N BOOK</span>
            <span id="draftIndicator" style="display:none; background:#fef3c7; color:#b45309; border:1px solid #f59e0b; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">DRAFT CONSIGNMENT</span>
        </div>
        
        <div class="bilty-header-inputs">
            <input type="hidden" name="status" id="bilty_status" value="{{ old('status', 'final') }}">
            <div>
                <label for="series_select">SERIES</label>
                @php
                    if (!isset($seriesList)) {
                        try {
                            $seriesList = \App\Models\Series::where('is_active', true)->orderBy('name', 'asc')->get();
                        } catch (\Throwable $e) {
                            $seriesList = collect();
                        }
                    }
                    if (!isset($defaultSeries)) {
                        $fySession = session('financial_year', '2026-2027');
                        $defaultSeries = '26-27';
                        if ($fySession && $fySession !== 'ALL' && strpos($fySession, '-') !== false) {
                            $parts = explode('-', $fySession);
                            if (count($parts) === 2 && strlen(trim($parts[0])) >= 2 && strlen(trim($parts[1])) >= 2) {
                                $defaultSeries = substr(trim($parts[0]), -2) . '-' . substr(trim($parts[1]), -2);
                            }
                        }
                    }
                    $selectedSeries = old('series', isset($bilty) ? $bilty->series : $defaultSeries);
                @endphp
                <select name="series" id="series_select" style="height:30px; font-weight:600; background:#fff; border:1px solid #cbd5e1; border-radius:4px; padding:2px 6px; color:#333;">
                    @if(count($seriesList) > 0)
                        @foreach($seriesList as $s)
                            <option value="{{ $s->name }}" {{ $selectedSeries == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    @else
                        <option value="26-27" selected>26-27</option>
                    @endif
                </select>
            </div>
            
            <div>
                <label for="bilty_no">C.N NO.</label>
                <input type="text" name="bilty_no" id="bilty_no" value="{{ old('bilty_no', $nextBiltyNo ?? '01') }}" required autocomplete="off" style="width:75px; font-weight:bold;">
            </div>
            
            <div>
                <label for="invoice_date">C.N DATE</label>
                <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
            </div>

            <div>
                <label for="bilty_user_display">USER</label>
                <input type="text" id="bilty_user_display" value="{{ auth()->user()->username ?? 'admin' }}" readonly style="height:30px; font-weight:600; color:#475569; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px; padding:2px 8px; cursor:not-allowed; pointer-events:none;" tabindex="-1">
            </div>
        </div>
    </div>

    <div class="bilty-body" id="formContentWrapper" style="position: relative;">
        <div id="formBlockedOverlay" style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(240, 240, 240, 0.75); z-index:9990; display:flex; justify-content:center; align-items:flex-start; padding-top:120px; backdrop-filter: blur(4px); transition: all 0.3s ease; pointer-events: auto;">
            <div style="background:white; border:2px solid var(--secondary-color); padding:25px 50px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.25); font-weight:700; color:#c92a2a; text-align:center; font-size:15px; max-width:90%; position:sticky; top:150px;">
                ⚠️ PLEASE SELECT A BILLING TYPE AT THE TOP TO FILL THIS FORM.
            </div>
        </div>

        <div id="biltyLoaderOverlay" class="bilty-loader-overlay">
            <div style="background:white; padding:25px 50px; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.2); display:flex; flex-direction:column; align-items:center; font-weight:700; color:var(--primary-color);">
                <div class="bilty-spinner"></div>
                <span>LOADING BILTY DETAILS...</span>
            </div>
        </div>
        
        <div class="form-row">
            
            <!-- Left Box: Route & Consignor details -->
            <div class="section-box">
                <div class="section-title">CONSIGNOR & ROUTE</div>
                
                <div class="grid-fields-2">
                    <div class="form-group-custom">
                        <label for="from_location_text">FROM LOC.</label>
                        <div class="autocomplete-wrapper">
                            <input type="text" id="from_location_text" name="from_location_text" class="autocomplete-input" placeholder="TYPE CITY NAME..." autocomplete="off" value="{{ old('from_location_text') }}">
                            <input type="hidden" name="from_location_id" id="from_location_id" value="{{ old('from_location_id') }}">
                            <div class="autocomplete-dropdown" id="from_location_dropdown"></div>
                        </div>
                    </div>
                    
                    <div class="form-group-custom">
                        <label for="to_location_text">TO LOC.</label>
                        <div class="autocomplete-wrapper">
                            <input type="text" id="to_location_text" name="to_location_text" class="autocomplete-input" placeholder="TYPE CITY NAME..." autocomplete="off" value="{{ old('to_location_text') }}">
                            <input type="hidden" name="to_location_id" id="to_location_id" value="{{ old('to_location_id') }}">
                            <div class="autocomplete-dropdown" id="to_location_dropdown"></div>
                        </div>
                    </div>
                </div>

                <div class="grid-fields-2">
                    <div class="form-group-custom" style="grid-column: span 2;">
                        <label for="consignor_text">CONSIGNOR NAME</label>
                        <div class="autocomplete-wrapper">
                            <input type="text" id="consignor_text" name="consignor_name" class="autocomplete-input" placeholder="TYPE CONSIGNOR NAME..." autocomplete="off" value="{{ old('consignor_name', old('consignor_text')) }}">
                            <input type="hidden" name="consignor_id" id="consignor_id" value="{{ old('consignor_id') }}">
                            <div class="autocomplete-dropdown" id="consignor_dropdown"></div>
                        </div>
                    </div>
                </div>

                <div class="grid-fields-2">
                    <div class="form-group-custom">
                        <label for="consignor_mobile">MOBILE</label>
                        <input type="text" name="consignor_mobile" id="consignor_mobile" maxlength="10" inputmode="numeric" placeholder="10-DIGIT MOBILE NO.">
                    </div>
                    
                    <div class="form-group-custom">
                        <label for="consignor_gstin">GSTIN</label>
                        <input type="text" name="consignor_gstin" id="consignor_gstin" placeholder="GSTIN NO.">
                    </div>
                </div>

                <div class="form-group-custom">
                    <label for="consignor_address">ADDRESS</label>
                    <textarea name="consignor_address" id="consignor_address" rows="2" placeholder="ADDRESS"></textarea>
                </div>
            </div>

            <!-- Right Box: Consignee & Billing Options -->
            <div class="section-box">
                <div class="section-title">CONSIGNEE & BILLING DETAILS</div>

                <div class="grid-fields-2">
                    <div class="form-group-custom" style="grid-column: span 2;">
                        <label for="consignee_text">CONSIGNEE NAME</label>
                        <div class="autocomplete-wrapper">
                            <input type="text" id="consignee_text" name="consignee_name" class="autocomplete-input" placeholder="TYPE CONSIGNEE NAME..." autocomplete="off" value="{{ old('consignee_name', old('consignee_text')) }}">
                            <input type="hidden" name="consignee_id" id="consignee_id" value="{{ old('consignee_id') }}">
                            <div class="autocomplete-dropdown" id="consignee_dropdown"></div>
                        </div>
                    </div>
                </div>

                <div class="grid-fields-2">
                    <div class="form-group-custom">
                        <label for="consignee_mobile">MOBILE</label>
                        <input type="text" name="consignee_mobile" id="consignee_mobile" maxlength="10" inputmode="numeric" placeholder="10-DIGIT MOBILE NO.">
                    </div>
                    
                    <div class="form-group-custom">
                        <label for="consignee_gstin">GSTIN</label>
                        <input type="text" name="consignee_gstin" id="consignee_gstin" placeholder="GSTIN NO.">
                    </div>
                </div>

                <div class="form-group-custom">
                    <label for="consignee_address">ADDRESS</label>
                    <textarea name="consignee_address" id="consignee_address" rows="2" placeholder="ADDRESS"></textarea>
                </div>
            </div>

        </div>

        <!-- Middle Section: Consignment, Vehicle, Eway numbers -->
        <div class="section-box" style="margin-bottom: 20px;">
            <div class="section-title">TRANSPORT DETAILS</div>
            <div class="grid-fields-2" style="grid-template-columns: repeat(6, 1fr); gap: 10px;">
                <div class="form-group-custom" id="billing_party_wrapper" style="display: {{ in_array(old('billing_type'), ['Paid', 'To Pay', 'T.B.B.']) ? 'flex' : 'none' }};">
                    <label for="billing_party_text">THIRD PARTY</label>
                    <div class="autocomplete-wrapper">
                        <input type="text" id="billing_party_text" name="billing_party_name" class="autocomplete-input" placeholder="TYPE THIRD PARTY..." autocomplete="off" value="{{ old('billing_party_name', old('billing_party_text')) }}">
                        <input type="hidden" name="billing_party_id" id="billing_party_id" value="{{ old('billing_party_id') }}">
                        <div class="autocomplete-dropdown" id="billing_party_dropdown"></div>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label for="cn_no">THIRD PARTY C/N NO.</label>
                    <input type="text" name="cn_no" id="cn_no" value="{{ old('cn_no') }}" placeholder="THIRD PARTY C/N NO.">
                </div>

                <div class="form-group-custom">
                    <label for="vehicle_type">VEHICLE TYPE</label>
                    <select name="vehicle_type" id="vehicle_type" onchange="toggleVehicleFields()" style="height:32px;">
                        <option value="Vehicle Number">Vehicle Number</option>
                        <option value="Transport Name">Transport Name</option>
                    </select>
                </div>
                
                <div class="form-group-custom" id="vehicle_input_wrapper">
                    <label for="vehicle_no_text" id="vehicle_no_label">VEHICLE NO.</label>
                    <div class="autocomplete-wrapper">
                        <input type="text" name="vehicle_no" id="vehicle_no_text" placeholder="AS-01-XX-1234" value="{{ old('vehicle_no') }}" autocomplete="off">
                        <div class="autocomplete-dropdown" id="vehicle_no_dropdown"></div>
                    </div>
                </div>

                <div class="form-group-custom" id="shipping_status_wrapper">
                    <label for="shipping_status">SHIPPING STATUS</label>
                    @php
                        try {
                            $activeStatuses = \App\Models\ShippingStatus::where('is_active', true)->orderBy('id')->pluck('name')->toArray();
                        } catch (\Throwable $e) {
                            $activeStatuses = [];
                        }
                        if (empty($activeStatuses)) {
                            $activeStatuses = ['Booked', 'Shipped', 'In Transit', 'Delivered'];
                        }
                        if (isset($bilty) && $bilty->shipping_status && !in_array($bilty->shipping_status, $activeStatuses)) {
                            $activeStatuses[] = $bilty->shipping_status;
                        }
                        $selectedStatus = old('shipping_status', isset($bilty) ? $bilty->shipping_status : 'Booked');
                    @endphp
                    <select name="shipping_status" id="shipping_status" style="height:32px; font-weight:600;">
                        @foreach($activeStatuses as $stName)
                            <option value="{{ $stName }}" {{ $selectedStatus == $stName ? 'selected' : '' }}>{{ $stName }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group-custom" style="position: relative;">
                    <label for="eway_bill_no">E-WAY BILL NO.</label>
                    <div style="display:flex; gap:5px;">
                        <input type="text" id="eway_bill_input" placeholder="12-DIGIT EWAY BILL NO." style="flex:1;">
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
                        <th width="8%" id="colHeaderPkgs">NO OF PKGS</th>
                        <th width="10%">PACKING</th>
                        <th width="18%">DESCRIPTION</th>
                        <th width="10%">INVOICE NO</th>
                        <th width="10%">INVOICE VALUE</th>
                        <th width="10%">UNIT</th>
                        <th width="8%">WEIGHT/FIXED</th>
                        <th width="8%" class="weight-col">WEIGHT</th>
                        <th width="8%">RATE</th>
                        <th width="7%" style="min-width: 85px; text-align: right;">ST</th>
                        <th width="7%" style="min-width: 85px; text-align: right;">RC</th>
                        <th width="7%" style="min-width: 85px; text-align: right;">SC</th>
                        <th width="8%" style="min-width: 90px; text-align: right;">DD</th>
                        <th width="4%"></th>
                    </tr>
                </thead>
                <tbody id="gridBody">
                    <!-- Standard Row 1 -->
                    <tr class="grid-row">
                        <td>
                            <input type="number" name="items[0][no_of_pkgs]" class="input-no_of_pkgs calc-trigger" required min="1" value="">
                        </td>
                        <td>
                            <input type="text" name="items[0][packing]" placeholder="BOX/BAG/ROLL" value="">
                        </td>
                        <td>
                            <input type="text" name="items[0][description]" placeholder="GOODS DESCRIPTION" value="">
                        </td>
                        <td>
                            <input type="text" name="items[0][invoice_no]" placeholder="INV NO">
                        </td>
                        <td>
                            <input type="number" name="items[0][invoice_value]" class="input-invoice_value" value="" step="0.01" placeholder="0">
                        </td>
                        <td>
                            <select name="items[0][unit]" class="input-unit" onchange="handleUnitChange(this)">
                                @if(isset($measurementUnits) && count($measurementUnits) > 0)
                                    @foreach($measurementUnits as $u)
                                        <option value="{{ $u->unit_code }}" data-type="{{ $u->unit_type }}" data-pkg-label="{{ $u->package_label ?: 'NoOfPkgs' }}">{{ $u->unit_code }}</option>
                                    @endforeach
                                @else
                                    <option value="KG" data-type="weight" data-pkg-label="NoOfPkgs">KG</option>
                                    <option value="Fixed" data-type="fixed" data-pkg-label="NoOfPkgs">Fixed</option>
                                @endif
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[0][qty]" class="input-qty calc-trigger" required min="0" step="0.001" value="" placeholder="0" style="background-color: #ffffff; color: #000; font-weight: 600;">
                        </td>
                        <td class="weight-col-cell">
                            <input type="number" name="items[0][weight_val]" class="input-weight_val calc-trigger" step="0.001" value="" placeholder="0">
                        </td>
                        <td>
                            <input type="number" name="items[0][rate]" class="input-rate calc-trigger" required min="0.00" step="0.01" value="" placeholder="0" style="background-color: #ffffff; color: #000; font-weight: 600;">
                        </td>
                        <td>
                            <input type="number" name="items[0][st]" class="input-st calc-trigger" value="" placeholder="0" step="0.01" style="min-width: 80px; text-align: right;">
                        </td>
                        <td>
                            <input type="number" name="items[0][rc]" class="input-rc calc-trigger" value="" placeholder="0" step="0.01" style="min-width: 80px; text-align: right;">
                        </td>
                        <td>
                            <input type="number" name="items[0][sc]" class="input-sc calc-trigger" value="" placeholder="0" step="0.01" style="min-width: 80px; text-align: right;">
                        </td>
                        <td>
                            <input type="number" name="items[0][dd]" class="input-dd calc-trigger" value="" placeholder="0" step="0.01" style="min-width: 85px; text-align: right;">
                        </td>
                        <td>
                            <button type="button" class="btn-delete-row" onclick="removeRow(this)">&times;</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="grid-actions">
                <button type="button" class="btn-add-row" onclick="addRow()">+ ADD CONSIGNMENT LINE</button>
                <span style="font-size: 11px; color: #64748b; font-style: italic;">ROW TOTALS ARE QTY * RATE. BOTTOM TAXES SUM AUTOMATICALLY.</span>
            </div>
        </div>

        <!-- Summary, Rounding, Surcharges & Settlement -->
        <div class="totals-and-settlement">
            
            <div>
                <div style="font-size: 13px; font-weight:800; margin-bottom: 12px; color:var(--primary-color); border-bottom: 2px solid #cbd5e1; padding-bottom:6px; text-transform:uppercase;">CHARGES SUMMARY</div>
                
                <div class="totals-grid">
                    <div class="form-group-custom">
                        <label for="total_packages">TOTAL PKGS</label>
                        <input type="text" name="total_packages" id="total_packages" readonly value="0">
                    </div>
                    <div class="form-group-custom">
                        <label for="total_qty">TOTAL QTY</label>
                        <input type="text" name="total_qty" id="total_qty" readonly value="0">
                    </div>
                    <div class="form-group-custom">
                        <label for="gross_amount">GROSS AMT (QTY*RATE)</label>
                        <input type="text" name="gross_amount" id="gross_amount" readonly value="0">
                    </div>
                    
                    <div class="form-group-custom">
                        <label for="st_charge">TOTAL S.T.</label>
                        <input type="text" name="st_charge" id="st_charge" readonly value="0">
                    </div>
                    <div class="form-group-custom">
                        <label for="rc_charge">TOTAL R.C.</label>
                        <input type="text" name="rc_charge" id="rc_charge" readonly value="0">
                    </div>
                    <div class="form-group-custom">
                        <label for="sc_charge">TOTAL S.C.</label>
                        <input type="text" name="sc_charge" id="sc_charge" readonly value="0">
                    </div>
                    <div class="form-group-custom">
                        <label for="dd_charge">TOTAL D.D.</label>
                        <input type="text" name="dd_charge" id="dd_charge" readonly value="0">
                    </div>
                    
                    <div class="form-group-custom">
                        <label for="round_off">ROUND OFF</label>
                        <input type="number" name="round_off" id="round_off" class="calc-trigger" value="0" placeholder="0" step="0.01">
                    </div>

                    <div class="net-amount-card" style="grid-column: span 4; margin-top: 10px;">
                        <label>NET BILL AMOUNT (INR)</label>
                        <input type="text" name="net_amount" id="net_amount" readonly value="0">
                    </div>
                </div>
            </div>

            <!-- Right Side Settlement -->
            <div class="settlement-panel">
                <div style="font-size: 13px; font-weight:800; margin-bottom: 12px; color:var(--primary-color); border-bottom: 2px solid #cbd5e1; padding-bottom:6px; text-transform:uppercase;">PAYMENT SETTLEMENT</div>
                
                <div class="grid-fields-2">
                    <div class="form-group-custom">
                        <label for="cash_amount">CASH PAID</label>
                        <input type="number" name="cash_amount" id="cash_amount" class="calc-trigger" value="0" placeholder="0" step="0.01">
                    </div>
                    <div class="form-group-custom">
                        <label for="card_amount">CARD PAID</label>
                        <input type="number" name="card_amount" id="card_amount" class="calc-trigger" value="0" placeholder="0" step="0.01">
                    </div>
                </div>

                <div class="grid-fields-2">
                    <div class="form-group-custom">
                        <label for="upi_chq_amount">UPI / CHEQUE</label>
                        <input type="number" name="upi_chq_amount" id="upi_chq_amount" class="calc-trigger" value="0" placeholder="0" step="0.01">
                    </div>
                    <div class="form-group-custom">
                        <label for="ref_no">REF NO / CHQ NO.</label>
                        <input type="text" name="ref_no" id="ref_no" placeholder="TXN REF NO.">
                    </div>
                </div>

                <div class="grid-fields-2">
                    <div class="form-group-custom">
                        <label for="payment_date">PAYMENT DATE</label>
                        <input type="date" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="form-group-custom">
                        <label for="bank_account">BANK A/C</label>
                        <select name="bank_account" id="bank_account">
                            <option value="">SELECT BANK</option>
                            <option value="HDFC Bank">HDFC Bank</option>
                            <option value="State Bank of India">State Bank of India</option>
                            <option value="ICICI Bank">ICICI Bank</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-custom" style="margin-top: 10px;">
                    <label for="remark">REMARK</label>
                    <input type="text" name="remark" id="remark" placeholder="BILLING REMARKS...">
                </div>

                <div class="form-group-custom" style="margin-top: 10px;">
                    <label for="voucher_no">VOUCHER NO.</label>
                    <input type="number" name="voucher_no" id="voucher_no" value="{{ $nextVoucherNo ?? '' }}" readonly>
                </div>

                <div id="balanceBox" class="balance-box balance-unpaid">
                    <span>BALANCE DUE:</span>
                    <span id="balanceText">₹ 0</span>
                    <input type="hidden" name="balance_amount" id="balance_amount" value="0">
                </div>
            </div>

        </div>

    </div>

    <!-- Action Buttons Footer -->
    <div class="bilty-actions-footer">
        <button type="button" class="btn-footer btn-reset" onclick="clearFormFields()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            CLEAR FIELDS
        </button>
        <button type="button" class="btn-footer btn-draft" id="btnSaveDraft" onclick="saveAsDraft()" style="background:#f59e0b; color:#fff; border:none; padding:10px 22px; border-radius:6px; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:8px; cursor:pointer; box-shadow:0 2px 5px rgba(245,158,11,0.3); transition:all 0.2s ease;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            SAVE AS DRAFT
        </button>
        <button type="submit" class="btn-footer btn-save" id="btnSaveSubmit" onclick="document.getElementById('bilty_status').value = 'final';">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            <span id="btnSaveSubmitText">SAVE RECEIPT</span>
        </button>
        <a href="#" id="btnPrintReceiptFooter" target="_blank" class="btn-footer btn-view-receipt" style="display:none; text-decoration:none;">
            🖨 VIEW RECEIPT
        </a>
    </div>
</form>
