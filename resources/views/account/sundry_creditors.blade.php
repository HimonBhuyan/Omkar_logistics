@extends('layouts.app')

@section('title', 'Sundry Creditors Ledger Summery - Omkaar Logistics')

@section('styles')
<style>
    /* Classic Desktop ERP Window */
    .ledger-summary-window {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        background: #d4d0c8;
        border: 1px solid #808080;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        font-size: 11.5px;
        color: #000;
        user-select: none;
    }

    /* 1. Red Header Bar */
    .ledger-title-bar {
        background: #8b0000;
        color: #ffffff;
        text-align: center;
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 0.5px;
        padding: 4px 0;
        border-bottom: 1px solid #5a0000;
    }

    /* 2. Filter Section (Matches Image) */
    .filter-panel {
        background: #d4d0c8;
        padding: 8px 18px 8px 18px;
        border-bottom: 1px solid #999;
        display: flex;
        justify-content: center;
    }

    .filter-box-compact {
        display: inline-flex;
        flex-direction: column;
        gap: 5px;
        background: #d4d0c8;
    }

    .filter-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-label {
        font-weight: bold;
        font-size: 11.5px;
        color: #000;
        min-width: 65px;
        text-align: right;
    }

    .filter-input {
        height: 22px;
        border: 1px solid #7f9db9;
        background: #ffffff;
        font-size: 11.5px;
        padding: 1px 5px;
        color: #000;
        box-sizing: border-box;
    }

    .filter-input:focus {
        outline: 1px solid #0055ff;
    }

    .btn-search-icon {
        width: 32px;
        height: 48px;
        background: #e4e2de;
        border: 1px solid #7f9db9;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 2px;
        box-shadow: 1px 1px 2px rgba(0,0,0,0.15);
    }

    .btn-search-icon:hover {
        background: #ffffff;
        border-color: #0055ff;
    }

    /* 3. Data Table Grid (Matches Image) */
    .table-container {
        width: 100%;
        overflow-x: auto;
        overflow-y: auto;
        max-height: calc(100vh - 255px);
        min-height: 320px;
        background: #ffffff;
        border-bottom: 1px solid #808080;
    }

    .grid-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11.5px;
        background: #f0f0f0;
    }

    .grid-table th {
        background: #e4e2de;
        color: #000;
        font-weight: bold;
        padding: 4px 6px;
        border: 1px solid #808080;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .grid-table td {
        border: 1px solid #b0b0b0;
        padding: 3px 8px;
        white-space: nowrap;
        color: #000;
        vertical-align: middle;
        background-color: #e4e2de;
    }

    /* Selected Row (Navy Blue with white text matching classic desktop app) */
    .grid-table tr.selected td {
        background-color: #000080 !important;
        color: #ffffff !important;
        font-weight: bold;
    }

    /* Subtle alternate tint */
    .grid-table tr.cyan-row td {
        background-color: #e0f7fa;
    }

    .grid-table tr:hover td {
        background-color: #d0e4ff;
    }

    .grid-table tr.selected:hover td {
        background-color: #000080 !important;
        color: #ffffff !important;
    }

    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }

    /* Totals Summary Row (Light Pink/Coral matching Image) */
    .grid-table tr.total-row td {
        background-color: #fedbdb !important;
        font-weight: bold;
        border-top: 2px solid #808080;
        border-bottom: 2px solid #808080;
        color: #000;
    }

    /* 4. Bottom Toolbar */
    .summary-bottom-bar {
        background: #d4d0c8;
        padding: 6px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .bottom-status-text {
        font-size: 11.5px;
        font-weight: bold;
        color: #333;
    }

    .bottom-action-buttons {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-report-action {
        width: 32px;
        height: 28px;
        background: #e4e2de;
        border: 1px solid #808080;
        border-radius: 2px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 1px 1px 2px rgba(0,0,0,0.15);
        text-decoration: none;
        color: #000;
    }

    .btn-report-action:hover {
        background: #ffffff;
        border-color: #0055ff;
    }

    @media print {
        .fixed-top-nav, header, nav, footer, .filter-panel, .summary-bottom-bar, #top-loading-bar {
            display: none !important;
        }
        body {
            padding: 0 !important;
            background: #fff !important;
        }
        .ledger-summary-window {
            border: none !important;
            box-shadow: none !important;
        }
        .table-container {
            max-height: none !important;
            overflow: visible !important;
        }
        .grid-table td {
            background-color: #ffffff !important;
        }
        .grid-table tr.total-row td {
            background-color: #fedbdb !important;
        }
    }
</style>
@endsection

@section('content')
<div class="ledger-summary-window">
    <!-- Red Title Bar (Matches Image) -->
    <div class="ledger-title-bar">
        Sundry Creditors Ledger Summery
    </div>

    <!-- Filter Section (Matches Image Layout) -->
    <form id="summaryFilterForm" method="GET" action="{{ route('report.sundry_creditors') }}" class="filter-panel">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="filter-box-compact">
                <!-- Row 1: Series / From Date -->
                <div class="filter-row">
                    <label class="filter-label">Series</label>
                    <input type="text" name="series" id="series" class="filter-input text-center" 
                           value="{{ request('series', $series ?? '') }}" style="width: 55px;" placeholder="ALL">

                    <span class="filter-label" style="min-width: auto; margin-left: 6px;">From</span>
                    <input type="date" name="from_date" id="from_date" class="filter-input" 
                           value="{{ request('from_date', $fromDate ?? '2026-01-01') }}" style="width: 120px;">
                </div>

                <!-- Row 2: Accounts / To Date -->
                <div class="filter-row">
                    <label class="filter-label">Accounts</label>
                    <input type="text" name="accounts" id="accounts" class="filter-input text-center" 
                           value="{{ request('accounts', $accountRange ?? 'A To Z') }}" style="width: 55px;">

                    <span class="filter-label" style="min-width: auto; margin-left: 6px;">To</span>
                    <input type="date" name="to_date" id="to_date" class="filter-input" 
                           value="{{ request('to_date', $toDate ?? date('Y-m-d')) }}" style="width: 120px;">
                </div>
            </div>

            <!-- Search Button with Magnifying Glass -->
            <button type="submit" class="btn-search-icon" title="Search / Filter Records">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.5">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="21" y1="21" x2="16" y2="16"/>
                </svg>
            </button>
        </div>
    </form>

    <!-- Table Grid (Matches Image) -->
    <div class="table-container">
        <table class="grid-table" id="summaryTable">
            <thead>
                <tr>
                    <th style="width: 45px;">Srno.</th>
                    <th style="text-align: left; padding-left: 10px;">Account Name</th>
                    <th style="width: 160px; text-align: right; padding-right: 15px;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php $sr = 1; @endphp
                @forelse($records as $index => $r)
                    <tr class="{{ $index === 1 ? 'selected' : ($index === 9 ? 'cyan-row' : '') }}" 
                        data-id="{{ $r['id'] }}"
                        ondblclick="window.location='{{ route('account.ledger.load', $r['id']) }}'" 
                        onclick="selectRow(this);"
                        title="Double click to open Ledger for {{ $r['name'] }}"
                        style="cursor: pointer;">
                        <td class="text-center font-bold">{{ $sr++ }}</td>
                        <td class="text-left font-bold" style="padding-left: 10px;">
                            {{ strtoupper($r['name']) }}
                        </td>
                        <td class="text-right font-bold" style="padding-right: 15px;">
                            {{ $r['balance_formatted'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center" style="padding: 30px; background: #fff; font-size: 12px; color: #666;">
                            No {{ strtolower($groupType) }} ledger records found.
                        </td>
                    </tr>
                @endforelse

                <!-- Totals Row (Light Pink/Coral matching Image) -->
                @if(count($records) > 0)
                    <tr class="total-row">
                        <td colspan="2" class="text-left font-bold" style="padding-left: 10px;">Total</td>
                        <td class="text-right font-bold" style="padding-right: 15px;">
                            {{ $totalFormatted }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Bottom Toolbar -->
    <div class="summary-bottom-bar">
        <div class="bottom-status-text">
            Total Records: {{ count($records) }}
        </div>

        <div class="bottom-action-buttons">
            <!-- Print Report -->
            <button type="button" class="btn-report-action" onclick="window.print();" title="Print Report">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6a1b9a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8" fill="#e1bee7"></rect>
                </svg>
            </button>

            <!-- Excel Export -->
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn-report-action" title="Export to Excel (XLSX)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" fill="#e8f5e9"/>
                    <line x1="8" y1="8" x2="16" y2="16" stroke="#2e7d32" stroke-width="2.5"/>
                    <line x1="16" y1="8" x2="8" y2="16" stroke="#2e7d32" stroke-width="2.5"/>
                </svg>
            </a>

            <!-- Exit / Back to Dashboard -->
            <a href="{{ route('dashboard') }}" class="btn-report-action" title="Exit / Close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectRow(row) {
        document.querySelectorAll('#summaryTable tbody tr').forEach(tr => {
            tr.classList.remove('selected');
        });
        row.classList.add('selected');
    }

    // Keyboard Arrow navigation up/down
    document.addEventListener('keydown', function(e) {
        const rows = Array.from(document.querySelectorAll('#summaryTable tbody tr:not(.total-row)'));
        if (rows.length === 0) return;

        let currentIndex = rows.findIndex(tr => tr.classList.contains('selected'));

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            let nextIndex = currentIndex + 1;
            if (nextIndex >= rows.length) nextIndex = 0;
            selectRow(rows[nextIndex]);
            rows[nextIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            let prevIndex = currentIndex - 1;
            if (prevIndex < 0) prevIndex = rows.length - 1;
            selectRow(rows[prevIndex]);
            rows[prevIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            if (currentIndex >= 0 && rows[currentIndex]) {
                const id = rows[currentIndex].getAttribute('data-id');
                if (id) {
                    window.location = `/account/ledger/${id}`;
                }
            }
        }
    });
</script>
@endsection
