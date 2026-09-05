<?php

namespace App\Http\Controllers;

use App\Models\Bilty;
use App\Models\CityModel;
use App\Models\AccountLedger;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ReportController extends Controller
{
    /**
     * Build the filtered Bilty query based on request parameters.
     */
    protected function getFilteredBiltiesQuery(Request $request)
    {
        $query = Bilty::forCompany()->with(['fromLocation', 'toLocation', 'consignor', 'consignee', 'billingParty', 'items', 'user']);

        // 1. From and To Location filter
        if ($request->filled('from_location_name')) {
            $fromName = trim($request->from_location_name);
            $query->whereHas('fromLocation', function($sq) use ($fromName) {
                $sq->where('name', 'like', '%' . $fromName . '%');
            });
        } elseif ($request->filled('from_location_id')) {
            $query->where('from_location_id', $request->from_location_id);
        }

        if ($request->filled('to_location_name')) {
            $toName = trim($request->to_location_name);
            $query->whereHas('toLocation', function($sq) use ($toName) {
                $sq->where('name', 'like', '%' . $toName . '%');
            });
        } elseif ($request->filled('to_location_id')) {
            $query->where('to_location_id', $request->to_location_id);
        }

        // 2. Consignor / Consignee / Party filter
        if ($request->filled('consignor_name')) {
            $cName = trim($request->consignor_name);
            $query->where(function($q) use ($cName) {
                $q->whereHas('consignor', function($sq) use ($cName) {
                    $sq->where('ledger_name', 'like', '%' . $cName . '%');
                })->orWhere('consignor_name', 'like', '%' . $cName . '%');
            });
        } elseif ($request->filled('consignor_id')) {
            $query->where('consignor_id', $request->consignor_id);
        }

        if ($request->filled('consignee_name')) {
            $ceName = trim($request->consignee_name);
            $query->where(function($q) use ($ceName) {
                $q->whereHas('consignee', function($sq) use ($ceName) {
                    $sq->where('ledger_name', 'like', '%' . $ceName . '%');
                })->orWhere('consignee_name', 'like', '%' . $ceName . '%');
            });
        } elseif ($request->filled('consignee_id')) {
            $query->where('consignee_id', $request->consignee_id);
        }

        if ($request->filled('billing_party_name')) {
            $pName = trim($request->billing_party_name);
            $query->where(function($q) use ($pName) {
                $q->whereHas('billingParty', function($sq) use ($pName) {
                    $sq->where('ledger_name', 'like', '%' . $pName . '%');
                })->orWhere('billing_party_name', 'like', '%' . $pName . '%');
            });
        } elseif ($request->filled('billing_party_id')) {
            $query->where('billing_party_id', $request->billing_party_id);
        }

        // 3. Date range filters
        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // 4. Vehicle No filter
        if ($request->filled('vehicle_no')) {
            $query->where('vehicle_no', 'like', '%' . $request->vehicle_no . '%');
        }

        // 4b. Shipping Status filter
        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', trim($request->shipping_status));
        }

        // 5. Billing Type MOP filters (Paid, To Pay, T.B.B.)
        $isSubmitted = $request->has('search_submitted') || $request->hasAny(['from_date', 'to_date', 'consignor_name', 'consignee_name', 'billing_party_name', 'series', 'vehicle_no', 'shipping_status', 'from_location_name', 'to_location_name', 'mop_paid', 'mop_topay', 'mop_tbb']);

        if ($isSubmitted) {
            $billingTypes = [];
            if ($request->has('mop_paid')) {
                $billingTypes = array_merge($billingTypes, ['Paid', 'PAID', 'paid']);
            }
            if ($request->has('mop_topay')) {
                $billingTypes = array_merge($billingTypes, ['To Pay', 'TO PAY', 'to pay', 'TOPAY']);
            }
            if ($request->has('mop_tbb')) {
                $billingTypes = array_merge($billingTypes, ['T.B.B.', 'T.B.B', 'TBB', 't.b.b.']);
            }

            if (!empty($billingTypes)) {
                $query->whereIn('billing_type', $billingTypes);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // 6. Series filter
        if ($request->filled('series')) {
            $query->where('series', 'like', '%' . $request->series . '%');
        }

        return $query->orderBy('invoice_date', 'desc')->orderBy('bilty_no', 'desc');
    }

    public function biltyRegister(Request $request)
    {
        // If export to Excel requested directly via query param
        if ($request->get('export') === 'excel') {
            return $this->exportExcel($request);
        }

        // Fetch locations list
        $cities = CityModel::orderBy('name')->get();
        
        // Fetch consignors / consignees / parties ledgers list from Party model
        $consignors = Party::where('type', 'consignor')->orWhere('type', 'both')->orderBy('name')->get();
        $consignees = Party::where('type', 'consignee')->orWhere('type', 'both')->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        // Unique vehicle expense ledgers
        $vehiclesList = AccountLedger::whereIn('under_group', ['Vehicle Expense', 'Oil Expense', 'Transport Expense'])
            ->orderBy('ledger_name')
            ->pluck('ledger_name')
            ->unique();

        // Fetch filtered Bilties with items and user
        $bilties = $this->getFilteredBiltiesQuery($request)->get();

        // Calculate aggregate sums for footer
        $totalPaid = 0;
        $totalToPay = 0;
        $totalTbb = 0;
        $totalNetAmt = 0;
        $totalKg = 0;
        $totalFixedQty = 0;
        $totalFixed = 0;

        foreach ($bilties as $b) {
            $totalNetAmt += floatval($b->net_amount);
            $mop = strtoupper(trim($b->billing_type ?? ''));
            if ($mop === 'PAID') {
                $totalPaid += floatval($b->net_amount);
            } elseif ($mop === 'TO PAY' || $mop === 'TOPAY') {
                $totalToPay += floatval($b->net_amount);
            } elseif ($mop === 'T.B.B.' || $mop === 'T.B.B' || $mop === 'TBB') {
                $totalTbb += floatval($b->net_amount);
            }

            if ($b->items->isNotEmpty()) {
                foreach ($b->items as $item) {
                    $u = strtoupper(trim($item->unit ?? ''));
                    if ($u === 'FIXED' || $b->type === 'Transport Name' || $b->type === 'VEHICLE NUMBER') {
                        $totalFixedQty += floatval($item->qty);
                        $totalFixed += floatval($item->weight_val);
                    } else {
                        $totalKg += floatval($item->qty > 0 ? $item->qty : $item->weight_val);
                    }
                }
            } else {
                if ($b->type === 'Transport Name' || $b->type === 'VEHICLE NUMBER') {
                    $totalFixedQty += floatval($b->total_qty);
                } else {
                    $totalKg += floatval($b->total_qty);
                }
            }
        }

        return view('bilty.register', compact(
            'bilties', 'cities', 'consignors', 'consignees', 'parties', 'vehiclesList',
            'totalPaid', 'totalToPay', 'totalTbb', 'totalNetAmt', 'totalKg', 'totalFixedQty', 'totalFixed'
        ));
    }

    /**
     * Download Excel Sheet of the C.N. bills matching the active filters.
     */
    public function exportExcel(Request $request)
    {
        $bilties = $this->getFilteredBiltiesQuery($request)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('C.N. Bills');

        // Main Title Header
        $sheet->setCellValue('A1', 'OMKAAR LOGISTICS - C.N. Bills Register');
        $sheet->mergeCells('A1:AG1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0F3460'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subtitle / Filters meta
        $dateInfo = 'Generated on: ' . date('d-m-Y h:i A');
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $dateInfo .= ' | Period: ' . ($request->from_date ?? 'Start') . ' to ' . ($request->to_date ?? 'End');
        }
        $sheet->setCellValue('A2', $dateInfo);
        $sheet->mergeCells('A2:AG2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('555555'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Table Column Headers (31 Columns)
        $headers = [
            'Srno.',
            'Status',
            'BiltyNo',
            'Date',
            'Time',
            'From Loc.',
            'To Loc.',
            'Consignor',
            'Mobile',
            'Consignee',
            'Mob.',
            'Party',
            'Third Party C.N.',
            'E-WayBill No',
            'Vehicle No',
            'Ship Status',
            'Packages',
            'Packing',
            'Description',
            'Invoice No.',
            'Invoice Value',
            'Unit',
            'QTY',
            'Weight',
            'Rate',
            'ST',
            'RC',
            'SC',
            'DD',
            'Total',
            'Net Amt.',
            'M.O.P',
            'User Name'
        ];

        $headerRow = 4;
        foreach ($headers as $colIdx => $header) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colLetter . $headerRow, $header);
        }

        // Header Styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F3460'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];
        $sheet->getStyle('A4:AG4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(26);

        // Populate Data rows
        $rowNum = 5;
        $totalPaid = 0;
        $totalToPay = 0;
        $totalTbb = 0;
        $totalNet = 0;
        $totalPkgs = 0;
        $totalQty = 0;
        $totalInvVal = 0;
        $totalST = 0;
        $totalRC = 0;
        $totalSC = 0;
        $totalDD = 0;
        $totalSum = 0;

        foreach ($bilties as $index => $b) {
            $statusText = ($b->status === 'draft') ? 'Draft' : 'Final';
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

            $unit = $b->items->first()?->unit ?? (($b->type === 'Transport Name') ? 'Fixed' : 'KG');
            $itemWeight = floatval($b->items->sum('weight_val'));
            $dateFormatted = $b->invoice_date ? $b->invoice_date->format('d-m-Y') : '';
            $timeFormatted = $b->created_at ? $b->created_at->format('h:i A') : '';
            $fromLocName = $b->fromLocation ? $b->fromLocation->name : ($b->from_location_name ?? '');
            $toLocName = $b->toLocation ? $b->toLocation->name : ($b->to_location_name ?? '');
            $consignorName = $b->consignor ? ($b->consignor->ledger_name ?? $b->consignor->name) : ($b->consignor_name ?? '');
            $consignorMobile = $b->consignor ? ($b->consignor->mobile ?: ($b->consignor->phone_o ?: '')) : ($b->consignor_mobile ?? '');
            $consigneeName = $b->consignee ? ($b->consignee->ledger_name ?? $b->consignee->name) : ($b->consignee_name ?? '');
            $consigneeMobile = $b->consignee ? ($b->consignee->mobile ?: ($b->consignee->phone_o ?: '')) : ($b->consignee_mobile ?? '');
            $billingPartyName = $b->billingParty ? ($b->billingParty->ledger_name ?? $b->billingParty->name) : ($b->billing_party_name ?? '');
            $userName = $b->user ? ($b->user->username ?? $b->user->name) : ($b->user_id ? 'User #'.$b->user_id : 'admin');

            $shippingStatusText = $b->shipping_status ?: ($b->vehicle_no ? 'Shipped' : 'Booked');

            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $statusText);
            $sheet->setCellValue('C' . $rowNum, $b->bilty_no);
            $sheet->setCellValue('D' . $rowNum, $dateFormatted);
            $sheet->setCellValue('E' . $rowNum, $timeFormatted);
            $sheet->setCellValue('F' . $rowNum, $fromLocName);
            $sheet->setCellValue('G' . $rowNum, $toLocName);
            $sheet->setCellValue('H' . $rowNum, $consignorName);
            $sheet->setCellValueExplicit('I' . $rowNum, (string) $consignorMobile, DataType::TYPE_STRING);
            $sheet->setCellValue('J' . $rowNum, $consigneeName);
            $sheet->setCellValueExplicit('K' . $rowNum, (string) $consigneeMobile, DataType::TYPE_STRING);
            $sheet->setCellValue('L' . $rowNum, $billingPartyName);
            $sheet->setCellValue('M' . $rowNum, $b->cn_no ?? '');
            $sheet->setCellValueExplicit('N' . $rowNum, (string) ($b->eway_bill_no ?? ''), DataType::TYPE_STRING);
            $sheet->setCellValue('O' . $rowNum, $b->vehicle_no ?? '');
            $sheet->setCellValue('P' . $rowNum, $shippingStatusText);
            $sheet->setCellValue('Q' . $rowNum, intval($b->total_packages));
            $sheet->setCellValue('R' . $rowNum, $packing);
            $sheet->setCellValue('S' . $rowNum, $description);
            $sheet->setCellValueExplicit('T' . $rowNum, (string) $invoiceNo, DataType::TYPE_STRING);
            $sheet->setCellValue('U' . $rowNum, $invoiceVal);
            $sheet->setCellValue('V' . $rowNum, $unit);
            $sheet->setCellValue('W' . $rowNum, floatval($b->total_qty));
            $sheet->setCellValue('X' . $rowNum, $itemWeight > 0 ? $itemWeight : '');
            $sheet->setCellValue('Y' . $rowNum, floatval($rate));
            $sheet->setCellValue('Z' . $rowNum, $st);
            $sheet->setCellValue('AA' . $rowNum, $rc);
            $sheet->setCellValue('AB' . $rowNum, $sc);
            $sheet->setCellValue('AC' . $rowNum, $dd);
            $sheet->setCellValue('AD' . $rowNum, $rowTotal);
            $sheet->setCellValue('AE' . $rowNum, floatval($b->net_amount));
            $sheet->setCellValue('AF' . $rowNum, $b->billing_type ?? '-');
            $sheet->setCellValue('AG' . $rowNum, $userName);

            // Format numbers
            $sheet->getStyle('C' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('Q' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('U' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('W' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.000');
            $sheet->getStyle('X' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.000');
            $sheet->getStyle('Y' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('Z' . $rowNum . ':AE' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

            // Alignment
            $sheet->getStyle('A' . $rowNum . ':E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M' . $rowNum . ':P' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('T' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('V' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('AE' . $rowNum . ':AF' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Row Borders
            $sheet->getStyle('A' . $rowNum . ':AG' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

            // Row background: Yellow for Draft, alternating zebra striping for Final
            if (($b->status ?? 'final') === 'draft') {
                $sheet->getStyle('A' . $rowNum . ':AG' . $rowNum)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF08A');
            } elseif ($index % 2 === 1) {
                $sheet->getStyle('A' . $rowNum . ':AG' . $rowNum)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            // Sum totals
            $totalNet += floatval($b->net_amount);
            $totalPkgs += intval($b->total_packages);
            $totalQty += floatval($b->total_qty);
            $totalInvVal += $invoiceVal;
            $totalST += $st;
            $totalRC += $rc;
            $totalSC += $sc;
            $totalDD += $dd;
            $totalSum += $rowTotal;

            $mop = strtoupper(trim($b->billing_type ?? ''));
            if ($mop === 'PAID') {
                $totalPaid += floatval($b->net_amount);
            } elseif ($mop === 'TO PAY' || $mop === 'TOPAY') {
                $totalToPay += floatval($b->net_amount);
            } elseif ($mop === 'T.B.B.' || $mop === 'T.B.B' || $mop === 'TBB') {
                $totalTbb += floatval($b->net_amount);
            }

            $rowNum++;
        }

        // Totals Row
        $sheet->setCellValue('A' . $rowNum, 'TOTAL:');
        $sheet->mergeCells('A' . $rowNum . ':P' . $rowNum);
        $sheet->setCellValue('Q' . $rowNum, $totalPkgs);
        $sheet->setCellValue('U' . $rowNum, $totalInvVal);
        $sheet->setCellValue('W' . $rowNum, $totalQty);
        $sheet->setCellValue('Y' . $rowNum, $totalST);
        $sheet->setCellValue('Z' . $rowNum, $totalRC);
        $sheet->setCellValue('AA' . $rowNum, $totalSC);
        $sheet->setCellValue('AB' . $rowNum, $totalDD);
        $sheet->setCellValue('AC' . $rowNum, $totalSum);
        $sheet->setCellValue('AD' . $rowNum, $totalNet);

        $totalStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0F3460']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0F3460']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F3460']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ];
        $sheet->getStyle('A' . $rowNum . ':AG' . $rowNum)->applyFromArray($totalStyle);
        $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('Q' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('U' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('W' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.000');
        $sheet->getStyle('Y' . $rowNum . ':AD' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

        // Summary Breakdown Box
        $rowNum += 2;
        $sheet->setCellValue('Y' . $rowNum, 'Paid Total:');
        $sheet->setCellValue('Z' . $rowNum, $totalPaid);
        $sheet->getStyle('Y' . $rowNum)->getFont()->setBold(true);
        $sheet->getStyle('Z' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

        $rowNum++;
        $sheet->setCellValue('Y' . $rowNum, 'To Pay Total:');
        $sheet->setCellValue('Z' . $rowNum, $totalToPay);
        $sheet->getStyle('Y' . $rowNum)->getFont()->setBold(true);
        $sheet->getStyle('Z' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

        $rowNum++;
        $sheet->setCellValue('Y' . $rowNum, 'T.B.B. Total:');
        $sheet->setCellValue('Z' . $rowNum, $totalTbb);
        $sheet->getStyle('Y' . $rowNum)->getFont()->setBold(true);
        $sheet->getStyle('Z' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

        $rowNum++;
        $sheet->setCellValue('Y' . $rowNum, 'Grand Net Total:');
        $sheet->setCellValue('Z' . $rowNum, $totalNet);
        $sheet->getStyle('Y' . $rowNum . ':Z' . $rowNum)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0F3460'));
        $sheet->getStyle('Z' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

        // Auto-fit all columns width
        foreach (range(1, 31) as $colIdx) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $filename = 'CN_Bills_Register_' . date('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate',
            'Pragma' => 'public',
        ]);
    }
}
