<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Voucher - {{ $payment->series }}-{{ $payment->voucher_no ?: $payment->payment_no }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        }

        body {
            background-color: #808080;
            color: #000;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top Crystal Reports Toolbar ── */
        .cr-toolbar {
            background: #e4e2de;
            border-bottom: 1px solid #999;
            padding: 3px 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            user-select: none;
        }

        .cr-tools-left {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .cr-btn {
            background: #e4e2de;
            border: 1px solid #b4b4b4;
            padding: 2px 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 22px;
            font-size: 11px;
            border-radius: 2px;
        }

        .cr-btn:hover {
            background: #fff;
            border-color: #0055ff;
        }

        .cr-divider {
            width: 1px;
            height: 18px;
            background: #b4b4b4;
            margin: 0 4px;
        }

        .cr-page-input {
            width: 32px;
            height: 18px;
            border: 1px solid #7f9db9;
            text-align: center;
            font-size: 11px;
        }

        .cr-tools-right {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 10.5px;
            color: #333;
        }

        .cr-close-btn {
            background: #e4e2de;
            border: 1px solid #b4b4b4;
            padding: 1px 12px;
            cursor: pointer;
            font-size: 11px;
            border-radius: 2px;
        }

        .cr-close-btn:hover {
            background: #fee;
            border-color: #c00;
            color: #c00;
        }

        /* ── Main Report Area ── */
        .cr-main-area {
            display: flex;
            flex: 1;
            overflow: auto;
        }

        /* Left Side Tree Navigation */
        .cr-tree-nav {
            width: 140px;
            background: #ffffff;
            border-right: 1px solid #999;
            display: flex;
            flex-direction: column;
        }

        .cr-tab-header {
            background: #e4e2de;
            border-bottom: 1px solid #999;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            color: #000;
        }

        .cr-tree-body {
            flex: 1;
            background: #fff;
        }

        /* Right Canvas Area */
        .cr-canvas-area {
            flex: 1;
            background: #808080;
            padding: 20px 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow-y: auto;
        }

        /* Printable Sheet (A4 Portrait Canvas) */
        .report-sheet {
            background: #ffffff;
            width: 680px;
            min-height: 850px;
            padding: 30px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.4);
            box-sizing: border-box;
            position: relative;
        }

        /* Solid Box Border */
        .report-box {
            border: 1.5px solid #000000;
            padding: 20px 25px 35px 25px;
            width: 100%;
            min-height: 480px;
            position: relative;
        }

        /* Header Details */
        .company-name {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            font-family: Arial, "Helvetica Neue", sans-serif;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .company-address {
            font-size: 10.5px;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .company-tax-info {
            font-size: 10.5px;
            text-align: center;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .company-email {
            font-size: 10.5px;
            text-align: center;
            margin-bottom: 16px;
        }

        .voucher-title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5px;
            margin-bottom: 24px;
        }

        /* Content Rows */
        .voucher-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-bottom: 30px;
        }

        .voucher-table td {
            padding: 9px 0;
            vertical-align: middle;
        }

        .label-cell {
            font-weight: bold;
            color: #000;
            white-space: nowrap;
        }

        .value-cell {
            color: #000;
            font-weight: 500;
        }

        /* Signatures */
        .signature-row {
            margin-top: 100px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 10px;
            font-size: 11.5px;
        }

        .sign-title {
            color: #000;
        }

        /* ── Bottom Status Bar ── */
        .cr-status-bar {
            background: #e4e2de;
            border-top: 1px solid #999;
            padding: 3px 15px;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
            color: #333;
            user-select: none;
        }

        @media print {
            body {
                background: #fff;
            }
            .cr-toolbar, .cr-tree-nav, .cr-status-bar {
                display: none !important;
            }
            .cr-main-area {
                overflow: visible !important;
            }
            .cr-canvas-area {
                padding: 0 !important;
                background: #fff !important;
            }
            .report-sheet {
                box-shadow: none !important;
                width: 100% !important;
                padding: 10px !important;
            }
            .report-box {
                border: 1.5px solid #000 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Crystal Reports Toolbar -->
    <div class="cr-toolbar">
        <div class="cr-tools-left">
            <button class="cr-btn" onclick="window.print();" title="Print (Ctrl+P)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
            </button>
            <button class="cr-btn" onclick="window.print();" title="Export">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
            </button>

            <div class="cr-divider"></div>

            <button class="cr-btn" title="First Page">&#124;&#9664;</button>
            <button class="cr-btn" title="Previous Page">&#9664;</button>
            <button class="cr-btn" title="Next Page">&#9654;</button>
            <button class="cr-btn" title="Last Page">&#9654;&#124;</button>

            <div class="cr-divider"></div>

            <input type="text" class="cr-page-input" value="1" readonly>
            <span>/ 1</span>

            <div class="cr-divider"></div>

            <button class="cr-btn" title="Find in Report">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </div>

        <div class="cr-tools-right">
            <span>SAP CRYSTAL REPORTS</span>
            <button class="cr-close-btn" onclick="window.close();">Close</button>
        </div>
    </div>

    <!-- Main Report Body Area -->
    <div class="cr-main-area">
        <!-- Left Side Tree -->
        <div class="cr-tree-nav">
            <div class="cr-tab-header">Main Report</div>
            <div class="cr-tree-body"></div>
        </div>

        <!-- Right Canvas Area -->
        <div class="cr-canvas-area">
            <div class="report-sheet">
                <div class="report-box">
                    <!-- Company Header -->
                    <div class="company-name">OMKAAR LOGISTICS</div>
                    <div class="company-address">LALUNGGAON, NEAR NPS SCHOLL, KAMRUP METROPOLITAN, ASSAM, 781040</div>
                    <div class="company-tax-info">GST : 18AAHF06045J1ZY PAN : AAHF06045J</div>
                    <div class="company-email">E-mail : omkaar.logistics@gmail.com</div>

                    <!-- Voucher Title -->
                    <div class="voucher-title">PAYMENT VOUCHER</div>

                    <!-- Voucher Details Table (Image 3 layout) -->
                    <table class="voucher-table">
                        <tr>
                            <td class="label-cell" style="width: 105px;">Voucher No. :</td>
                            <td class="value-cell" style="width: 250px;">{{ $payment->voucher_no ?: $payment->payment_no }}</td>
                            <td class="label-cell" style="width: 50px;">Date:</td>
                            <td class="value-cell" style="text-align: left;">{{ $payment->payment_date ? $payment->payment_date->format('d-m-Y') : date('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Name:</td>
                            <td class="value-cell">{{ $payment->account_name }}</td>
                            <td class="label-cell">Time:</td>
                            <td class="value-cell">
                                @php
                                    $timeStr = $payment->payment_time ?: date('H:i:s');
                                    try {
                                        $formattedTime = \Carbon\Carbon::createFromFormat('H:i:s', $timeStr)->format('h:i:s a');
                                    } catch (\Exception $e) {
                                        $formattedTime = $timeStr;
                                    }
                                @endphp
                                {{ $formattedTime }}
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Paid Amount :</td>
                            <td class="value-cell">{{ number_format($payment->total_amount ?: $payment->payment_amount, 2, '.', '') }}</td>
                            <td class="label-cell" colspan="2">
                                <span style="font-weight: bold;">MOP :</span> 
                                <span style="margin-left: 8px; margin-right: 25px;">{{ $payment->pay_mode === 'BANK TRANSFER' ? 'Bank' : ($payment->pay_mode ?: 'Bank') }}</span>
                                <span style="font-weight: bold;">Bank Name:</span> 
                                <span style="margin-left: 8px;">{{ $payment->bank_name ?: ($payment->bank ? $payment->bank->ledger_name : 'OMKAAR CBI CD') }}</span>
                            </td>
                        </tr>
                        @if($payment->customer_invoice_no || $payment->customer_bill_amt > 0)
                        <tr>
                            <td class="label-cell">Cust Inv No :</td>
                            <td class="value-cell">{{ $payment->customer_invoice_no ?: 'N/A' }}</td>
                            <td class="label-cell" colspan="2">
                                <span style="font-weight: bold;">Bill Amt:</span>
                                <span style="margin-left: 8px;">₹{{ number_format($payment->customer_bill_amt, 2) }}</span>
                                @if($payment->deduct_amount > 0)
                                    <span style="font-weight: bold; margin-left: 20px;">Deduct Amt:</span>
                                    <span style="margin-left: 8px;">₹{{ number_format($payment->deduct_amount, 2) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label-cell" style="width: 140px;">Purpose of payment :</td>
                            <td class="value-cell" colspan="3">{{ $payment->remark ?: '' }}</td>
                        </tr>
                    </table>

                    <!-- Signatures -->
                    <div class="signature-row">
                        <div class="sign-title">Receiver's Signature</div>
                        <div class="sign-title">payer's Signature</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Status Bar -->
    <div class="cr-status-bar">
        <span>Current Page No.: 1</span>
        <span>Total Page No.: 1</span>
        <span>Zoom Factor: 100%</span>
    </div>

</body>
</html>
