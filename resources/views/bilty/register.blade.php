@extends('layouts.app')

@section('styles')
<style>
    .report-card {
        background: #fff;
        border: 1px solid #7da9d4;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border-radius: 4px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .report-header-bar {
        background: #800000;
        color: #fff;
        padding: 6px 12px;
        font-weight: 700;
        font-size: 14px;
        text-align: center;
        border-bottom: 1px solid #7da9d4;
    }
    .filter-section {
        background: #d4d0c8;
        padding: 10px;
        border-bottom: 1px solid #999;
        font-size: 12px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr) auto;
        gap: 8px;
        align-items: center;
    }
    .filter-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .filter-group label {
        font-weight: 600;
        min-width: 60px;
        color: #111;
        white-space: nowrap;
    }
    .filter-group input[type="text"],
    .filter-group input[type="date"],
    .filter-group select {
        width: 100%;
        height: 24px;
        border: 1px solid #7f9db9;
        font-size: 12px;
        padding: 2px 4px;
        box-sizing: border-box;
    }
    .filter-checkboxes {
        display: flex;
        gap: 15px;
        align-items: center;
        background: #e4e2de;
        padding: 4px 8px;
        border: 1px solid #aaa;
        margin-top: 5px;
    }
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-search {
        background: #f0f0f0;
        border: 1px solid #7f9db9;
        padding: 4px 10px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        height: 24px;
    }
    .btn-search:hover {
        background: #e0e0e0;
    }
    .table-container {
        overflow-x: auto;
        max-height: 500px;
        background: #fff;
    }
    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    .report-table th {
        background: #f0f0f0;
        color: #111;
        border: 1px solid #ccc;
        padding: 4px 6px;
        font-weight: bold;
        text-align: left;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .report-table td {
        border: 1px solid #ddd;
        padding: 4px 6px;
        white-space: nowrap;
    }
    .report-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .report-table tr:hover {
        background-color: #ecf3f9;
    }
    .report-table tr.draft-row,
    .report-table tr.draft-row:nth-child(even),
    .report-table tr.draft-row td {
        background-color: #fef08a !important; /* Distinct yellow for draft */
    }
    .report-table tr.draft-row:hover,
    .report-table tr.draft-row:hover td {
        background-color: #fde047 !important; /* Richer yellow on hover */
    }
    .report-footer {
        background: #d4d0c8;
        padding: 10px;
        border-top: 1px solid #999;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        font-weight: bold;
    }
    .totals-box {
        display: flex;
        gap: 20px;
    }
    .totals-item {
        display: flex;
        gap: 8px;
    }
    .totals-label {
        color: #333;
    }
    .totals-value {
        color: #800000;
    }
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    .btn-action {
        background: #f0f0f0;
        border: 1px solid #7f9db9;
        padding: 4px 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        color: #111;
    }
    .btn-action:hover {
        background: #e0e0e0;
    }
    @media print {
        @page {
            size: landscape;
            margin: 10mm !important;
        }
        body {
            background-color: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        /* Hide navbar navigation, filters wrapper and buttons */
        .fixed-top-nav, footer, .filter-section, .action-buttons, .report-table td:last-child, .report-table th:last-child {
            display: none !important;
        }
        body {
            background-color: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .report-card {
            border: none !important;
            box-shadow: none !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .table-container {
            max-height: none !important;
            overflow: visible !important;
        }
        .report-table th {
            position: static !important;
            background-color: #f0f0f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endsection

@section('content')
<div class="report-card">
    <div class="report-header-bar">Bilty Register</div>
    
    <form method="GET" action="{{ route('report.bilty_register') }}" id="biltyFilterForm">
        <div class="filter-section">
            <div class="filter-grid">
                
                <div class="filter-group">
                    <input type="checkbox" name="filter_consignor" id="filter_consignor" value="1" {{ request('filter_consignor') ? 'checked' : '' }}>
                    <label for="filter_consignor" style="min-width: unset;">Consignor</label>
                    <select name="consignor_id">
                        <option value="">--All--</option>
                        @foreach($consignors as $c)
                            <option value="{{ $c->id }}" {{ request('consignor_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <input type="checkbox" name="filter_consignee" id="filter_consignee" value="1" {{ request('filter_consignee') ? 'checked' : '' }}>
                    <label for="filter_consignee" style="min-width: unset;">Consignee</label>
                    <select name="consignee_id">
                        <option value="">--All--</option>
                        @foreach($consignees as $c)
                            <option value="{{ $c->id }}" {{ request('consignee_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <input type="checkbox" name="filter_party" id="filter_party" value="1" {{ request('filter_party') ? 'checked' : '' }}>
                    <label for="filter_party" style="min-width: unset;">Party</label>
                    <select name="billing_party_id">
                        <option value="">--All--</option>
                        @foreach($parties as $p)
                            <option value="{{ $p->id }}" {{ request('billing_party_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="from_date">From</label>
                    <input type="date" name="from_date" id="from_date" value="{{ request('from_date', date('Y-m-d')) }}">
                </div>

                <div class="filter-group">
                    <label for="to_date">To</label>
                    <input type="date" name="to_date" id="to_date" value="{{ request('to_date', date('Y-m-d')) }}">
                </div>

            </div>

            <div class="filter-grid" style="margin-top: 8px;">
                <div class="filter-group">
                    <label for="from_location_id">From Loc.</label>
                    <select name="from_location_id" id="from_location_id">
                        <option value="">--All--</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('from_location_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="to_location_id">To Loc.</label>
                    <select name="to_location_id" id="to_location_id">
                        <option value="">--All--</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('to_location_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="series">Series</label>
                    <input type="text" name="series" id="series" value="{{ request('series') }}" placeholder="e.g. 26-27">
                </div>

                <div class="filter-group">
                    <label for="vehicle_no">Vehicle No.</label>
                    <input type="text" name="vehicle_no" id="vehicle_no" value="{{ request('vehicle_no') }}" placeholder="Search Vehicle No.">
                </div>

                <button type="submit" class="btn-search">
                    🔍 Search
                </button>
            </div>

            <div class="filter-checkboxes">
                <span style="font-weight:700;">Billing Mode:</span>
                <label class="checkbox-item">
                    <input type="checkbox" name="mop_paid" value="1" {{ request('mop_paid', '1') == '1' ? 'checked' : '' }}> Paid
                </label>
                <label class="checkbox-item">
                    <input type="checkbox" name="mop_topay" value="1" {{ request('mop_topay', '1') == '1' ? 'checked' : '' }}> To Pay
                </label>
                <label class="checkbox-item">
                    <input type="checkbox" name="mop_tbb" value="1" {{ request('mop_tbb', '1') == '1' ? 'checked' : '' }}> T.B.B.
                </label>
            </div>
        </div>
    </form>

    <div class="table-container">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Srno.</th>
                    <th>Status</th>
                    <th>BiltyNo</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>From Loc.</th>
                    <th>To Loc.</th>
                    <th>Consignor</th>
                    <th>Mobile</th>
                    <th>Consignee</th>
                    <th>Mob.</th>
                    <th>Party</th>
                    <th>Third Party C.N.</th>
                    <th>E-WayBill No</th>
                    <th>Vehicle No</th>
                    <th>Packages</th>
                    <th>Packing</th>
                    <th>Description</th>
                    <th>Invoice No.</th>
                    <th>Invoice Value</th>
                    <th>Weight Type</th>
                    <th>QTY</th>
                    <th>Rate</th>
                    <th>ST</th>
                    <th>RC</th>
                    <th>SC</th>
                    <th>DD</th>
                    <th>Total</th>
                    <th>Net Amt.</th>
                    <th>M.O.P</th>
                    <th>User Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bilties as $index => $b)
                    @php
                        $packing = $b->items->pluck('packing')->filter(fn($v) => filled($v))->unique()->implode(', ');
                        $description = $b->items->pluck('description')->filter(fn($v) => filled($v))->unique()->implode(', ');
                        $invoiceNo = $b->items->pluck('invoice_no')->filter(fn($v) => filled($v))->unique()->implode(', ');
                        $invoiceVal = floatval($b->items->sum('invoice_value'));

                        $st = $b->st_charge > 0 ? floatval($b->st_charge) : floatval($b->items->sum('st'));
                        $rc = $b->rc_charge > 0 ? floatval($b->rc_charge) : floatval($b->items->sum('rc'));
                        $sc = $b->sc_charge > 0 ? floatval($b->sc_charge) : floatval($b->items->sum('sc'));
                        $dd = $b->dd_charge > 0 ? floatval($b->dd_charge) : floatval($b->items->sum('dd'));

                        $rate = ($b->gross_amount > 0 && $b->total_qty > 0) ? ($b->gross_amount / $b->total_qty) : ($b->items->first()?->rate ?? 0);
                        $gross = ($b->gross_amount > 0) ? floatval($b->gross_amount) : (floatval($b->total_qty) * floatval($rate));
                        $rowTotal = $gross + $st + $rc + $sc + $dd;
                        if ($rowTotal == 0 && floatval($b->net_amount) > 0) {
                            $rowTotal = floatval($b->net_amount);
                        }
                    @endphp
                    @php
                        $isDraft = (($b->status ?? 'final') === 'draft');
                    @endphp
                    <tr onclick="window.location='{{ route('bilty.create') }}?bilty_no={{ $b->bilty_no }}'" class="{{ $isDraft ? 'draft-row' : '' }}" style="cursor: pointer; {{ $isDraft ? 'background-color: #fef08a;' : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($isDraft)
                                <span style="background:#fffbeb; color:#92400e; border:1px solid #d97706; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:800; display:inline-block; white-space:nowrap;">Draft</span>
                            @else
                                <span style="background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:700; display:inline-block; white-space:nowrap;">Final</span>
                            @endif
                        </td>
                        <td>{{ $b->bilty_no }}</td>
                        <td>{{ $b->invoice_date ? $b->invoice_date->format('d-m-Y') : '' }}</td>
                        <td>{{ $b->created_at ? $b->created_at->format('h:i A') : '' }}</td>
                        <td>{{ $b->fromLocation ? $b->fromLocation->name : ($b->from_location_name ?? '') }}</td>
                        <td>{{ $b->toLocation ? $b->toLocation->name : ($b->to_location_name ?? '') }}</td>
                        <td>{{ $b->consignor ? ($b->consignor->ledger_name ?? $b->consignor->name) : ($b->consignor_name ?? '') }}</td>
                        <td>{{ $b->consignor ? ($b->consignor->mobile ?: ($b->consignor->phone_o ?: '')) : ($b->consignor_mobile ?? '') }}</td>
                        <td>{{ $b->consignee ? ($b->consignee->ledger_name ?? $b->consignee->name) : ($b->consignee_name ?? '') }}</td>
                        <td>{{ $b->consignee ? ($b->consignee->mobile ?: ($b->consignee->phone_o ?: '')) : ($b->consignee_mobile ?? '') }}</td>
                        <td>{{ $b->billingParty ? ($b->billingParty->ledger_name ?? $b->billingParty->name) : ($b->billing_party_name ?? '') }}</td>
                        <td>{{ $b->cn_no }}</td>
                        <td>{{ $b->eway_bill_no }}</td>
                        <td>{{ $b->vehicle_no }}</td>
                        <td>{{ $b->total_packages }}</td>
                        <td>{{ $packing }}</td>
                        <td>{{ $description }}</td>
                        <td>{{ $invoiceNo }}</td>
                        <td>{{ $invoiceVal > 0 ? number_format($invoiceVal, 2) : '0.00' }}</td>
                        <td>{{ $b->items->first()?->weight_type ?? (($b->type === 'Transport Name') ? 'Fixed' : 'KG') }}</td>
                        <td>{{ $b->total_qty }}</td>
                        <td>{{ number_format($rate, 2) }}</td>
                        <td>{{ number_format($st, 2) }}</td>
                        <td>{{ number_format($rc, 2) }}</td>
                        <td>{{ number_format($sc, 2) }}</td>
                        <td>{{ number_format($dd, 2) }}</td>
                        <td>{{ number_format($rowTotal, 2) }}</td>
                        <td>{{ number_format($b->net_amount, 2) }}</td>
                        <td><span style="font-weight:600; color:#0f3460;">{{ $b->billing_type ?? '-' }}</span></td>
                        <td>{{ $b->user ? ($b->user->username ?? $b->user->name) : ($b->user_id ? 'User #'.$b->user_id : 'admin') }}</td>
                        <td>
                            <a href="{{ route('bilty.print', $b->id) }}" target="_blank" onclick="event.stopPropagation();" style="color: #003087; font-weight:600; text-decoration:underline;">Print</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="32" style="text-align: center; color: #666; padding: 20px;">No Bilty records found for the selected criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-footer">
        <div class="totals-box">
            <div class="totals-item">
                <span class="totals-label">To Pay:</span>
                <span class="totals-value">{{ number_format($totalToPay, 2) }}</span>
            </div>
            <div class="totals-item">
                <span class="totals-label">T.B.B:</span>
                <span class="totals-value">{{ number_format($totalTbb, 2) }}</span>
            </div>
            <div class="totals-item">
                <span class="totals-label">Net Amt:</span>
                <span class="totals-value">{{ number_format($totalNetAmt, 2) }}</span>
            </div>
            <div class="totals-item">
                <span class="totals-label">KG Wt:</span>
                <span class="totals-value">{{ number_format($totalKg, 3) }}</span>
            </div>
            <div class="totals-item">
                <span class="totals-label">Fixed Wt:</span>
                <span class="totals-value">{{ number_format($totalFixed, 3) }}</span>
            </div>
        </div>

        <div class="action-buttons">
            <button type="button" class="btn-action" id="btnPrintList" onclick="downloadExcel(event)" title="Download Excel Sheet of C.N Bills">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#107c41;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Print List
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function downloadExcel(e) {
        if (e) e.preventDefault();
        const btn = document.getElementById('btnPrintList');
        const originalHtml = btn ? btn.innerHTML : 'Print List';
        if (btn) {
            btn.style.opacity = '0.7';
            btn.innerHTML = `
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:0.25;"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:0.75;"></path>
                </svg>
                Downloading...
            `;
            setTimeout(() => {
                btn.style.opacity = '1';
                btn.innerHTML = originalHtml;
            }, 2500);
        }

        const form = document.getElementById('biltyFilterForm');
        let baseUrl = "{{ route('report.bilty_register.export') }}";
        if (form) {
            const formData = new FormData(form);
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value !== '') {
                    params.append(key, value);
                }
            }
            const qs = params.toString();
            window.location.href = baseUrl + (qs ? '?' + qs : '');
        } else {
            window.location.href = baseUrl;
        }
    }
</script>
@endsection
