<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Voucher #{{ $receipt->series }}-{{ $receipt->receipt_no }} - Omkaar Logistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 15px;
        }

        .receipt-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 12px;
            box-sizing: border-box;
        }

        .header-title {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .header-title h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #8b0000;
            text-transform: uppercase;
        }

        .header-title p {
            margin: 2px 0;
            font-size: 10.5px;
        }

        .doc-badge {
            display: inline-block;
            background: #8b0000;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
            padding: 3px 18px;
            border-radius: 3px;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info-grid td {
            padding: 3px 4px;
            font-size: 11px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #333;
            width: 105px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 12px;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 10.5px;
        }

        .items-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .total-row td {
            background: #fdf2f2;
            font-weight: bold;
        }

        .footer-summary {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .words-box {
            width: 58%;
            border: 1px solid #777;
            padding: 8px;
            background: #fafafa;
            font-size: 11px;
        }

        .sign-box {
            width: 35%;
            text-align: right;
            padding-top: 25px;
        }

        .no-print-bar {
            text-align: center;
            margin-bottom: 15px;
        }

        .btn-print {
            background: #0288d1;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 6px 20px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 12px;
        }

        @media print {
            .no-print-bar {
                display: none;
            }
            body {
                padding: 0;
            }
            .receipt-container {
                border: 1.5px solid #000;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button class="btn-print" onclick="window.print();">🖨 Print Money Receipt</button>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="header-title">
            <h1>OMKAAR LOGISTICS</h1>
            <p>Plot No. 12, Transport Nagar, Guwahati, Assam - 781022 | Phone: +91 9864322678</p>
            <p><b>GSTIN:</b> 18AAAFO1234A1Z5 | <b>PAN:</b> AAAFO1234A</p>
            <div class="doc-badge">MONEY RECEIPT / PAYMENT ACKNOWLEDGMENT</div>
        </div>

        <!-- Receipt & Party Details -->
        <table class="info-grid">
            <tr>
                <td class="info-label">Receipt No.:</td>
                <td style="width: 35%;"><b>{{ $receipt->series }}-{{ $receipt->receipt_no }}</b> (Voucher: {{ $receipt->voucher_no }})</td>
                <td class="info-label">Date:</td>
                <td><b>{{ $receipt->receipt_date ? $receipt->receipt_date->format('d-m-Y') : date('d-m-Y') }}</b> ({{ $receipt->receipt_time ?: '' }})</td>
            </tr>
            <tr>
                <td class="info-label">Received From:</td>
                <td><b>{{ $receipt->account_name }}</b></td>
                <td class="info-label">Mobile:</td>
                <td>{{ $receipt->mobile ?: ($receipt->account ? $receipt->account->mobile : '') }}</td>
            </tr>
            <tr>
                <td class="info-label">Pay Mode:</td>
                <td><b>{{ $receipt->pay_mode }}</b> {{ $receipt->bank_name ? '(' . $receipt->bank_name . ')' : '' }}</td>
                <td class="info-label">Chq / Ref No:</td>
                <td>{{ $receipt->cheque_no ?: 'N/A' }} {{ $receipt->cheque_date ? 'Dt: ' . $receipt->cheque_date->format('d-m-Y') : '' }}</td>
            </tr>
            @if($receipt->remark)
            <tr>
                <td class="info-label">Remark:</td>
                <td colspan="3">{{ $receipt->remark }}</td>
            </tr>
            @endif
        </table>

        <!-- Table of Invoices Settled -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 35px;">Sr.No</th>
                    <th style="width: 50px;">Series</th>
                    <th style="width: 75px;">Invoice No</th>
                    <th>Bill Amt (₹)</th>
                    <th>Old Paid (₹)</th>
                    <th>Due Amt (₹)</th>
                    <th>Discount (₹)</th>
                    <th>TDS (₹)</th>
                    <th>Paid (₹)</th>
                    <th>Balance (₹)</th>
                </tr>
            </thead>
            <tbody>
                @php $sr = 1; @endphp
                @forelse ($receipt->items as $item)
                    <tr>
                        <td class="text-center">{{ $sr++ }}</td>
                        <td class="text-center">{{ $item->series }}</td>
                        <td class="text-center font-bold">{{ $item->invoice_no }}</td>
                        <td class="text-right">{{ number_format($item->bill_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($item->old_paid, 2) }}</td>
                        <td class="text-right">{{ number_format($item->due_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($item->discount, 2) }}</td>
                        <td class="text-right">{{ number_format($item->tds, 2) }}</td>
                        <td class="text-right font-bold">{{ number_format($item->paid_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($item->balance, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 15px;">No invoice line items recorded.</td>
                    </tr>
                @endforelse
                <tr class="total-row">
                    <td colspan="3" class="text-center"><b>TOTAL :</b></td>
                    <td class="text-right">₹{{ number_format($receipt->bill_amount, 2) }}</td>
                    <td class="text-right"></td>
                    <td class="text-right">₹{{ number_format($receipt->due_amount, 2) }}</td>
                    <td class="text-right">₹{{ number_format($receipt->discount_amount, 2) }}</td>
                    <td class="text-right">₹{{ number_format($receipt->tds_amount, 2) }}</td>
                    <td class="text-right font-bold" style="color: #0044cc;">₹{{ number_format($receipt->receipt_amount, 2) }}</td>
                    <td class="text-right">₹{{ number_format($receipt->balance_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary & Signatures -->
        <div class="footer-summary">
            <div class="words-box">
                <b>Total Amount Received:</b> ₹{{ number_format($receipt->receipt_amount, 2) }}<br>
                @if($receipt->tds_amount > 0)
                    <b>TDS Deducted:</b> ₹{{ number_format($receipt->tds_amount, 2) }}<br>
                @endif
                <small style="color: #666; font-style: italic; margin-top: 5px; display: inline-block;">
                    * System generated Receipt Voucher.
                </small>
            </div>

            <div class="sign-box">
                <p style="margin: 0; font-weight: bold;">For OMKAAR LOGISTICS</p>
                <div style="height: 40px;"></div>
                <p style="margin: 0; border-top: 1px dashed #000; padding-top: 2px; font-size: 10px;">
                    Authorized Signatory
                </p>
            </div>
        </div>
    </div>

</body>
</html>
