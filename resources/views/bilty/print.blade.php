<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consignment Note #{{ $bilty->series }}-{{ $bilty->bilty_no }} - Omkaar Logistics</title>
    <style>
        * {
            box-sizing: border-box !important;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #000;
        }

        body {
            background-color: #ffffff !important;
            font-size: 11px;
            padding: 0 !important;
            margin: 0 !important;
            min-height: 100vh;
        }

        @page {
            size: A5 landscape !important;
            margin: 0 !important;
        }

        .receipt-outer {
            width: 100% !important;
            height: calc(100vh - 50px) !important;
            border: 1px solid #000000;
            padding: 2px;
            position: relative;
            background: #fff;
            overflow: hidden;
        }

        .receipt-container {
            width: 100%;
            height: 100%;
            border: 1.5px solid #000000;
            padding: 6px 8px;
            position: relative;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Branding and Header */
        .company-header {
            display: grid;
            grid-template-columns: 100px 1fr 100px;
            align-items: center;
            padding-bottom: 2px;
        }

        .logo-box img {
            width: 90px;
            height: auto;
            display: block;
        }

        .logo-text-middle {
            text-align: center;
            line-height: 1.2;
        }

        .logo-text-middle .sub-header {
            font-size: 9px;
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        .logo-text-middle h1 {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0px 0 1px 0;
        }

        .logo-text-middle .address-details {
            font-size: 9px;
            font-weight: normal;
            line-height: 1.2;
        }

        .logo-text-middle .address-details .contact-line {
            display: block;
        }

        /* Meta Rows */
        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 1px;
            font-size: 10.5px;
        }

        .meta-left {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .meta-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 1px;
        }

        .cn-pill-box {
            border: 1.5px solid #000;
            border-radius: 0px;
            padding: 1px 8px;
            font-size: 11px;
            font-weight: bold;
            min-width: 140px;
            text-align: center;
        }

        .meta-date {
            font-weight: bold;
            font-size: 10.5px;
        }

        .party-name-row {
            font-weight: bold;
            font-size: 10.5px;
            margin-top: 1px;
        }

        /* Locations Box */
        .locations-box {
            display: grid;
            grid-template-columns: 2.2fr 1.6fr 2.2fr;
            align-items: center;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .loc-pill {
            border: 1.5px solid #000;
            border-radius: 0px;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 11.5px;
            display: flex;
            gap: 10px;
        }

        .loc-pill label {
            font-weight: bold;
            font-size: 11.5px;
        }

        /* Parties Grid */
        .parties-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-bottom: 2px;
        }

        .party-column {
            border: 1.5px solid #000;
            border-radius: 0px;
            padding: 2px 4px;
            font-size: 9.5px;
            line-height: 1.3;
            min-height: 60px;
        }

        .party-row {
            display: flex;
            margin-bottom: 1px;
        }

        .party-row label {
            width: 70px;
            font-weight: bold;
        }

        .party-row span {
            flex: 1;
        }

        /* Consignment Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            border-bottom: none;
            margin-bottom: 0;
        }

        .items-table th {
            border: 1px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 3px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 3px;
            font-size: 10px;
            text-align: center;
            height: 22px;
            vertical-align: middle;
        }

        /* Bottom block (shares the same outer border as items table) */
        .bottom-block {
            border-left: 1.5px solid #000;
            border-right: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            margin-top: 0;
        }

        .bottom-left {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 4px 6px;
        }

        .signature-col {
            font-size: 10px;
            line-height: 1.4;
        }

        .signature-col.left-col {
            text-align: left;
        }

        .signature-col.right-col {
            text-align: right;
        }

        .signature-col .seal-sign {
            font-weight: normal;
        }

        .signature-col .receipt-ack {
            font-weight: bold;
        }

        .signature-col .booking-incharge {
            font-weight: bold;
        }

        .signature-col .terms-footer {
            font-size: 8.5px;
            font-weight: bold;
            font-style: italic;
        }

        /* Charges table - full outer box, single internal vertical divider, no row lines */
        .charges-table {
            border-collapse: collapse;
            width: 140px;
            border: 1.5px solid #000;
        }

        .charges-table td {
            padding: 2px 4px;
            font-size: 10px;
        }

        .charges-table td:first-child {
            font-weight: bold;
            width: 60px;
            border-right: 1.5px solid #000;
        }

        .charges-table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .btn-print {
            background: #0f3460;
            color: white;
            border: none;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 0px;
            cursor: pointer;
            position: absolute;
            top: -40px;
            right: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            display: block;
            z-index: 1000;
        }

        .btn-back-link {
            background: #4e5d6c;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 0px;
            text-decoration: none;
            position: absolute;
            top: -40px;
            left: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        @media print {
            body {
                padding: 0 !important;
                margin: 0 !important;
                background-color: transparent;
            }

            .btn-print,
            .btn-back-link {
                display: none !important;
            }

            .receipt-outer {
                border: none !important;
                margin: 0 !important;
                width: 100% !important;
                height: 100% !important;
            }
        }
    </style>
</head>

<body>

    <div style="position: relative; max-width: 100%; margin: 45px 15px 15px 15px;">
        <a href="{{ route('bilty.create') }}" class="btn-back-link">⬅ Back to Entry Form</a>
        <button class="btn-print" onclick="window.print()">🖨 Print Bill (A5)</button>

        <div class="receipt-outer">
            <div class="receipt-container">

                <!-- Company Header -->
                <div class="company-header">
                    <div class="logo-box">
                        <img src="{{ asset('assets/logo.jpg') }}" alt="Omkaar Logistics">
                    </div>
                    <div class="logo-text-middle">
                        <div class="sub-header">CONSIGNMENT NOTE</div>
                        <h1>OMKAAR LOGISTICS</h1>
                        <div class="address-details">
                            <span class="contact-line">Head office: Lokhra Lalunggaon Near NPS School, GHY-40</span>
                            <span class="contact-line">Mangaldoi, Kharupetia, Tangla, Dhekiajuli, Basimari,</span>
                            <span class="contact-line">Udalguri, Rangia, Nalbari, Patshala</span>
                            <span class="contact-line">📞 98640-82153, 97335-35513</span>
                            <span class="contact-line">✉ omkaar.logistics@gmail.com</span>
                            <span class="contact-line">GSTIN: 18AAHFO6045J1ZY</span>
                        </div>
                    </div>
                    <div class="logo-box" style="text-align:right;">
                        <img src="{{ asset('assets/logo.jpg') }}" alt="Omkaar Logistics">
                    </div>
                </div>

                <!-- Meta rows: E-Way/C.N.No on left, C.N. pill/Date on right -->
                <div class="meta-row">
                    <div class="meta-left">
                        <div style="margin-top: 15px;">C.N.No: {{ $bilty->cn_no ?? '' }}</div>
                        <div>E-WayBill No: {{ $bilty->eway_bill_no ?? '' }}</div>
                    </div>
                    <div class="meta-right">
                        <div class="meta-date" style="margin-bottom: 3px;">Date:&nbsp; &nbsp;
                            {{ $bilty->invoice_date->format('d/m/Y') }}</div>
                        <div class="cn-pill-box">C.N. No.:{{ $bilty->series }}-{{ $bilty->bilty_no }}</div>
                    </div>
                </div>

                <div class="party-name-row">
                    Third Party Name:
                    {{ $bilty->billingParty ? $bilty->billingParty->ledger_name : $bilty->consignor->ledger_name }}
                </div>

                <!-- Locations (From / To) Box -->
                <div class="locations-box">
                    <div class="loc-pill">
                        <label>From:</label>
                        <span>{{ $bilty->fromLocation->name }}</span>
                    </div>

                    <div style="text-align: center; font-weight: bold; font-size: 13px;">
                        Freight Mode: {{ $bilty->billing_type }}
                    </div>

                    <div class="loc-pill">
                        <label>To:</label>
                        <span>{{ $bilty->toLocation->name }}</span>
                    </div>
                </div>

                <!-- Parties Grid -->
                <div class="parties-grid">
                    <div class="party-column">
                        <div class="party-row">
                            <label>Consignor:</label>
                            <span>{{ $bilty->consignor->ledger_name }}</span>
                        </div>
                        <div class="party-row">
                            <label>Mobile No:</label>
                            <span>{{ $bilty->consignor->mobile ?: $bilty->consignor->phone_o ?: $bilty->consignor->phone_r }}</span>
                        </div>
                        <div class="party-row">
                            <label>GSTIN No:</label>
                            <span>{{ $bilty->consignor->gst_no }}</span>
                        </div>
                        <div class="party-row" style="margin-top: 4px;">
                            <label>Address:</label>
                            <span>{{ $bilty->consignor->address }}</span>
                        </div>
                    </div>
                    <div class="party-column">
                        <div class="party-row">
                            <label>Consignee:</label>
                            <span>{{ $bilty->consignee->ledger_name }}</span>
                        </div>
                        <div class="party-row">
                            <label>Mobile No:</label>
                            <span>{{ $bilty->consignee->mobile ?: $bilty->consignee->phone_o ?: $bilty->consignee->phone_r }}</span>
                        </div>
                        <div class="party-row">
                            <label>GSTIN No:</label>
                            <span>{{ $bilty->consignee->gst_no }}</span>
                        </div>
                        <div class="party-row" style="margin-top: 4px;">
                            <label>Address:</label>
                            <span>{{ $bilty->consignee->address }}</span>
                        </div>
                    </div>
                </div>

                <!-- Consignment Items Table -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="10%">No Of<br>Packages</th>
                            <th width="12%">Packing</th>
                            <th width="33%">Discription</th>
                            <th width="15%">Invoice No.</th>
                            <th width="10%">Invoice<br>Value</th>
                            <th width="10%">Weight</th>
                            <th width="10%">Qty</th>
                            <th width="10%">Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bilty->items as $item)
                            <tr>
                                <td>{{ $item->no_of_pkgs }}</td>
                                <td>{{ $item->packing }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->invoice_no ?? '' }}</td>
                                <td>{{ $item->invoice_value > 0 ? number_format($item->invoice_value, 2) : '' }}</td>
                                <td>
                                    @if ($item->weight_type === 'KG')
                                        {{ $item->qty > 0 ? number_format($item->qty, 3) : '' }}
                                    @else
                                        {{ $item->weight_val > 0 ? number_format($item->weight_val, 3) : '' }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->weight_type === 'KG')
                                        {{-- KG type has no separate quantity --}}
                                    @else
                                        {{ $item->qty > 0 ? number_format($item->qty, 2) : '' }}
                                    @endif
                                </td>
                                <td>{{ number_format($item->rate, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Bottom block: signatures + charges, sharing the table's outer border -->
                <div class="bottom-block">
                    <div class="bottom-left">
                        <div class="signature-col left-col">
                            <div class="seal-sign">Seal &amp; Sign</div>
                            <div class="receipt-ack">Receipt Acknowledgment</div>
                        </div>
                        <div class="signature-col right-col">
                            <div class="booking-incharge">Sign. Of the Booking Incharge</div>
                            <div class="terms-footer">* We are not responsible for Brokage /Leakage &amp; Damage of any
                                goods</div>
                        </div>
                    </div>

                    <table class="charges-table">
                        <tr>
                            <td>ST</td>
                            <td>{{ number_format($bilty->st_charge, 2) }}</td>
                        </tr>
                        <tr>
                            <td>RC</td>
                            <td>{{ number_format($bilty->rc_charge, 2) }}</td>
                        </tr>
                        <tr>
                            <td>SC</td>
                            <td>{{ number_format($bilty->sc_charge, 2) }}</td>
                        </tr>
                        <tr>
                            <td>DD</td>
                            <td>{{ number_format($bilty->dd_charge, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>{{ number_format($bilty->net_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>

</body>

</html>