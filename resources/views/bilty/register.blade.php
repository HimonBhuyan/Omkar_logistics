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
    
    <form method="GET" action="{{ route('report.bilty_register') }}">
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
                    <th>Series</th>
                    <th>BiltyNo</th>
                    <th>Date</th>
                    <th>From Loc.</th>
                    <th>To Loc.</th>
                    <th>Consignor</th>
                    <th>Mobile</th>
                    <th>Consignee</th>
                    <th>Mob.</th>
                    <th>Party</th>
                    <th>C.N.</th>
                    <th>E-WayBill No</th>
                    <th>Vehicle No</th>
                    <th>Packages</th>
                    <th>Weight Type</th>
                    <th>QTY</th>
                    <th>Rate</th>
                    <th>Net Amt.</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bilties as $index => $b)
                    <tr onclick="window.location='{{ route('bilty.create') }}?bilty_no={{ $b->bilty_no }}'" style="cursor: pointer;">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $b->series }}</td>
                        <td>{{ $b->bilty_no }}</td>
                        <td>{{ $b->invoice_date ? $b->invoice_date->format('d-m-Y') : '' }}</td>
                        <td>{{ $b->fromLocation ? $b->fromLocation->name : '' }}</td>
                        <td>{{ $b->toLocation ? $b->toLocation->name : '' }}</td>
                        <td>{{ $b->consignor ? $b->consignor->name : $b->consignor_name }}</td>
                        <td>{{ $b->consignor ? $b->consignor->mobile : $b->consignor_mobile }}</td>
                        <td>{{ $b->consignee ? $b->consignee->name : $b->consignee_name }}</td>
                        <td>{{ $b->consignee ? $b->consignee->mobile : $b->consignee_mobile }}</td>
                        <td>{{ $b->billingParty ? $b->billingParty->name : $b->billing_party_name }}</td>
                        <td>{{ $b->cn_no }}</td>
                        <td>{{ $b->eway_bill_no }}</td>
                        <td>{{ $b->vehicle_no }}</td>
                        <td>{{ $b->total_packages }}</td>
                        <td>{{ $b->type === 'Transport Name' ? 'Fixed' : 'KG' }}</td>
                        <td>{{ $b->total_qty }}</td>
                        <td>{{ $b->gross_amount > 0 && $b->total_qty > 0 ? number_format($b->gross_amount / $b->total_qty, 2) : '0.00' }}</td>
                        <td>{{ number_format($b->net_amount, 2) }}</td>
                        <td>
                            <a href="{{ route('bilty.print', $b->id) }}" target="_blank" onclick="event.stopPropagation();" style="color: #003087; font-weight:600; text-decoration:underline;">Print</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="19" style="text-align: center; color: #666; padding: 20px;">No Bilty records found for the selected criteria.</td>
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
            <button type="button" class="btn-action" onclick="window.print()">🖨 Print List</button>
        </div>
    </div>
</div>
@endsection
