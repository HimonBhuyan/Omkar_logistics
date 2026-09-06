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
    /* Filter Bar Stacking & Autocomplete Floating Dropdown Styling */
    .filter-section {
        position: relative;
        z-index: 100;
        overflow: visible !important;
        background: #d4d0c8;
        padding: 10px;
        border-bottom: 1px solid #999;
        font-size: 12px;
    }
    .filter-grid:nth-of-type(1) {
        position: relative;
        z-index: 20;
        overflow: visible !important;
        display: grid;
        grid-template-columns: repeat(4, 1fr) auto;
        gap: 8px;
        align-items: center;
    }
    .filter-grid:nth-of-type(2) {
        position: relative;
        z-index: 10;
        overflow: visible !important;
        display: grid;
        grid-template-columns: 1fr 1fr auto auto auto auto;
        gap: 8px;
        align-items: center;
    }
    .filter-group {
        position: relative;
        overflow: visible !important;
        flex: 1;
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
    .btn-search, .btn-clear {
        background: #f0f0f0;
        border: 1px solid #7f9db9;
        padding: 4px 10px;
        font-weight: bold;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        height: 24px;
        color: #111;
        box-sizing: border-box;
        border-radius: 2px;
        white-space: nowrap;
    }
    .btn-search:hover, .btn-clear:hover {
        background: #e0e0e0;
        color: #800000;
    }
    .table-container {
        position: relative;
        z-index: 1;
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
    }
    .autocomplete-wrapper {
        position: relative;
        width: 100%;
        flex: 1;
    }
    .autocomplete-input {
        width: 100%;
        height: 24px;
        border: 1px solid #7f9db9;
        font-size: 12px;
        padding: 2px 4px;
        box-sizing: border-box;
        border-radius: 3px;
        background: #ffffff;
    }
    .autocomplete-input:focus {
        border-color: #0f3460;
        outline: none;
    }
    .autocomplete-dropdown {
        position: absolute;
        top: calc(100% + 2px);
        left: 0;
        width: 100%;
        min-width: 220px;
        max-height: 220px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #7f9db9;
        border-radius: 4px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        z-index: 999999 !important;
        display: none;
    }
    .autocomplete-item {
        padding: 6px 8px;
        font-size: 11px;
        color: #1e293b;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    .autocomplete-item:hover,
    .autocomplete-item.active {
        background-color: #f1f5f9;
        color: #0f3460;
        font-weight: 600;
    }
    .autocomplete-item .match-text {
        font-weight: 700;
        color: #c92a2a;
        text-decoration: underline;
    }
    .autocomplete-item .item-meta {
        font-size: 10px;
        color: #64748b;
        margin-left: 8px;
    }
    .autocomplete-no-match {
        padding: 6px 8px;
        font-size: 11px;
        color: #94a3b8;
        font-style: italic;
    }
</style>
@endsection

@section('content')
<div class="report-card">
    <div class="report-header-bar">Bilty Register</div>
    
@php
    $isSubmitted = request()->has('search_submitted') || request()->hasAny(['from_date', 'to_date', 'consignor_name', 'consignee_name', 'billing_party_name', 'series', 'vehicle_no', 'from_location_name', 'to_location_name', 'mop_paid', 'mop_topay', 'mop_tbb']);
    $mopPaidChecked = $isSubmitted ? request()->has('mop_paid') : true;
    $mopTopayChecked = $isSubmitted ? request()->has('mop_topay') : true;
    $mopTbbChecked = $isSubmitted ? request()->has('mop_tbb') : true;
@endphp

    @include('common.bilty_filter_form', [
        'actionUrl' => route('report.bilty_register'),
        'method' => 'GET',
        'showDates' => true,
        'showClear' => true,
        'showBillingMode' => true,
        'mopPaidChecked' => $mopPaidChecked,
        'mopTopayChecked' => $mopTopayChecked,
        'mopTbbChecked' => $mopTbbChecked
    ])

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
                    <th>Ship Status</th>
                    <th>Packages</th>
                    <th>Packing</th>
                    <th>Description</th>
                    <th>Invoice No.</th>
                    <th>Invoice Value</th>
                    <th>Unit</th>
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
                        <td>
                            @php
                                $shipStatus = $b->shipping_status ?: ($b->vehicle_no ? 'Shipped' : 'Booked');
                            @endphp
                            @if(in_array($shipStatus, ['Shipped', 'In Transit']))
                                <span style="background:#e0f2fe; color:#0369a1; border:1px solid #7dd3fc; padding:2px 6px; border-radius:10px; font-size:10px; font-weight:700; white-space:nowrap;">{{ $shipStatus }}</span>
                            @elseif($shipStatus === 'Delivered')
                                <span style="background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:2px 6px; border-radius:10px; font-size:10px; font-weight:700; white-space:nowrap;">Delivered</span>
                            @elseif($shipStatus === 'Cancelled')
                                <span style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; padding:2px 6px; border-radius:10px; font-size:10px; font-weight:700; white-space:nowrap;">Cancelled</span>
                            @else
                                <span style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; padding:2px 6px; border-radius:10px; font-size:10px; font-weight:700; white-space:nowrap;">Booked</span>
                            @endif
                        </td>
                        <td>{{ $b->total_packages }}</td>
                        <td>{{ $packing }}</td>
                        <td>{{ $description }}</td>
                        <td>{{ $invoiceNo }}</td>
                        <td>{{ $invoiceVal > 0 ? number_format($invoiceVal, 2) : '0.00' }}</td>
                        <td>{{ $b->items->first()?->unit ?? (($b->type === 'Transport Name') ? 'Fixed' : 'KG') }}</td>
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
                            <span style="color:#ccc;">|</span>
                            <a href="{{ route('bilty.pdf', $b->id) }}" target="_blank" onclick="event.stopPropagation();" style="color: #c0392b; font-weight:600; text-decoration:underline;">PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="33" style="text-align: center; color: #666; padding: 20px;">No Bilty records found for the selected criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-footer">
        <div class="totals-box">
            <div class="totals-item">
                <span class="totals-label">Paid:</span>
                <span class="totals-value">{{ number_format($totalPaid, 2) }}</span>
            </div>
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
                <span class="totals-label">Fixed Qty:</span>
                <span class="totals-value">{{ number_format($totalFixedQty, 3) }}</span>
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
    // Master data arrays for high-speed autocomplete
    const locationsList = [
        @foreach ($cities as $loc)
            { id: {{ $loc->id }}, name: @json($loc->name) },
        @endforeach
    ];

    const consignorsList = [
        @foreach ($consignors as $p)
            { 
                id: {{ $p->id }}, 
                name: @json($p->name),
                mobile: @json($p->mobile ?? ''),
                gstin: @json($p->gst_no ?? $p->gstin ?? '')
            },
        @endforeach
    ];

    const consigneesList = [
        @foreach ($consignees as $p)
            { 
                id: {{ $p->id }}, 
                name: @json($p->name),
                mobile: @json($p->mobile ?? ''),
                gstin: @json($p->gst_no ?? $p->gstin ?? '')
            },
        @endforeach
    ];

    const partiesList = [
        @foreach ($parties as $p)
            { id: {{ $p->id }}, name: @json($p->name) },
        @endforeach
    ];

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
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

    function setupAutocomplete(config) {
        const { inputEl, dropdownEl, getItems } = config;
        if (!inputEl || !dropdownEl) return null;

        let activeIndex = -1;
        let currentMatches = [];

        const filterGroup = inputEl.closest('.filter-group');
        const filterGrid = inputEl.closest('.filter-grid');

        function showDropdown() {
            dropdownEl.style.display = 'block';
            if (filterGroup) filterGroup.style.zIndex = '99999';
            if (filterGrid) filterGrid.style.zIndex = '99999';
        }

        function hideDropdown() {
            dropdownEl.style.display = 'none';
            if (filterGroup) filterGroup.style.zIndex = '';
            if (filterGrid) filterGrid.style.zIndex = '';
            activeIndex = -1;
        }

        function renderMatches() {
            const query = inputEl.value.trim();
            if (query.length < 2) {
                hideDropdown();
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
                showDropdown();
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

            showDropdown();
        }

        function selectItem(item) {
            if (!item) return;
            const name = typeof item === 'string' ? item : item.name;
            inputEl.value = name;
            hideDropdown();
        }

        inputEl.addEventListener('input', function() {
            renderMatches();
        });

        inputEl.addEventListener('focus', function() {
            if (inputEl.value.trim().length >= 2) {
                renderMatches();
            }
        });

        inputEl.addEventListener('click', function() {
            if (inputEl.value.trim().length >= 2) {
                renderMatches();
            }
        });

        inputEl.addEventListener('keydown', function(e) {
            if (dropdownEl.style.display === 'none') {
                if (e.key === 'ArrowDown' && inputEl.value.trim().length >= 2) {
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
                hideDropdown();
            } else if (e.key === 'Escape') {
                hideDropdown();
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
                hideDropdown();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setupAutocomplete({
            inputEl: document.getElementById('consignor_text'),
            dropdownEl: document.getElementById('consignor_dropdown'),
            getItems: () => consignorsList
        });

        setupAutocomplete({
            inputEl: document.getElementById('consignee_text'),
            dropdownEl: document.getElementById('consignee_dropdown'),
            getItems: () => consigneesList
        });

        setupAutocomplete({
            inputEl: document.getElementById('billing_party_text'),
            dropdownEl: document.getElementById('billing_party_dropdown'),
            getItems: () => partiesList
        });

        setupAutocomplete({
            inputEl: document.getElementById('from_location_text'),
            dropdownEl: document.getElementById('from_location_dropdown'),
            getItems: () => locationsList
        });

        setupAutocomplete({
            inputEl: document.getElementById('to_location_text'),
            dropdownEl: document.getElementById('to_location_dropdown'),
            getItems: () => locationsList
        });
    });

    function clearFilters(e) {
        if (e) e.preventDefault();
        const fromVal = document.getElementById('from_date') ? document.getElementById('from_date').value : '';
        const toVal = document.getElementById('to_date') ? document.getElementById('to_date').value : '';

        const params = new URLSearchParams();
        if (fromVal) params.append('from_date', fromVal);
        if (toVal) params.append('to_date', toVal);

        const mopPaid = document.querySelector('input[name="mop_paid"]');
        const mopTopay = document.querySelector('input[name="mop_topay"]');
        const mopTbb = document.querySelector('input[name="mop_tbb"]');

        if (mopPaid && mopPaid.checked) params.append('mop_paid', '1');
        if (mopTopay && mopTopay.checked) params.append('mop_topay', '1');
        if (mopTbb && mopTbb.checked) params.append('mop_tbb', '1');

        const baseUrl = "{{ route('report.bilty_register') }}";
        window.location.href = baseUrl + (params.toString() ? '?' + params.toString() : '');
    }

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
