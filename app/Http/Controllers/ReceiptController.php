<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Invoice;
use App\Models\AccountLedger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReceiptController extends Controller
{
    /**
     * Show the Receipt Voucher creation form.
     */
    public function create(Request $request)
    {
        $series = $request->query('series', 'A');
        $maxReceiptNo = Receipt::where('series', $series)->max('receipt_no');
        $nextReceiptNo = ($maxReceiptNo && $maxReceiptNo >= 1) ? ($maxReceiptNo + 1) : 1;

        // All Accounts / Debtors / Parties
        $accounts = AccountLedger::orderBy('ledger_name')->get();

        // Bank Accounts
        $bankAccounts = AccountLedger::where(function($q) {
            $q->where('under_group', 'like', '%Bank%')
              ->orWhere('under_group', 'like', '%Cash%')
              ->orWhere('ledger_name', 'like', '%Bank%')
              ->orWhere('ledger_name', 'like', '%HDFC%')
              ->orWhere('ledger_name', 'like', '%SBI%')
              ->orWhere('ledger_name', 'like', '%ICICI%')
              ->orWhere('ledger_name', 'like', '%AXIS%')
              ->orWhere('ledger_name', 'like', '%PNB%');
        })->orderBy('ledger_name')->get();

        if ($bankAccounts->isEmpty()) {
            $bankAccounts = $accounts;
        }

        $currentTime = Carbon::now()->format('H:i:s');
        $currentDate = Carbon::now()->format('Y-m-d');

        // Optional pre-selected account
        $selectedAccount = null;
        if ($request->filled('account_id')) {
            $selectedAccount = AccountLedger::find($request->account_id);
        } elseif ($request->filled('account_name')) {
            $selectedAccount = AccountLedger::where('ledger_name', $request->account_name)->first();
        }

        return view('receipt.create', compact(
            'series',
            'nextReceiptNo',
            'accounts',
            'bankAccounts',
            'currentTime',
            'currentDate',
            'selectedAccount'
        ));
    }

    /**
     * AJAX endpoint to fetch unsettled invoices for an account.
     */
    public function getPendingInvoices(Request $request)
    {
        $accountName = trim($request->input('account_name', ''));
        $accountId = $request->input('account_id');
        $receiptId = $request->input('receipt_id');

        if (!$accountName && !$accountId) {
            return response()->json(['success' => true, 'invoices' => [], 'mobile' => '']);
        }

        // Find Account
        $account = null;
        if ($accountId) {
            $account = AccountLedger::find($accountId);
        }
        if (!$account && $accountName) {
            $account = AccountLedger::where('ledger_name', $accountName)->first();
        }

        $mobile = '';
        if ($account) {
            $mobiles = array_filter([$account->mobile, $account->phone_o]);
            $mobile = implode(',', array_unique($mobiles));
        }

        // Query Invoices for this account
        $query = Invoice::where(function($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'cancelled');
        });

        if ($account) {
            $query->where(function($q) use ($account) {
                $q->where('account_id', $account->id)
                  ->orWhere('account_name', $account->ledger_name)
                  ->orWhere('account_name', 'like', '%' . $account->ledger_name . '%');
            });
        } else {
            $query->where(function($q) use ($accountName) {
                $q->where('account_name', $accountName)
                  ->orWhere('account_name', 'like', '%' . $accountName . '%');
            });
        }

        $invoices = $query->orderBy('invoice_date', 'asc')
            ->orderBy('invoice_no', 'asc')
            ->get();

        // If editing an existing receipt, get currently saved item values for this receipt
        $existingItemsMap = [];
        if ($receiptId) {
            $existingItems = ReceiptItem::where('receipt_id', $receiptId)->get();
            foreach ($existingItems as $item) {
                $existingItemsMap[$item->invoice_id] = $item;
            }
        }

        $resultInvoices = [];
        foreach ($invoices as $inv) {
            $totalInvoiceAmt = (float)$inv->total_amount;
            $gstAmt = (float)$inv->gst_amount;
            $grossAmt = (float)$inv->bill_amount > 0 ? (float)$inv->bill_amount : ($totalInvoiceAmt - $gstAmt);

            // Total paid from OTHER receipts
            $otherPaidQuery = ReceiptItem::where('invoice_id', $inv->id)
                ->whereHas('receipt', function($q) use ($receiptId) {
                    $q->where('status', '!=', 'cancelled');
                    if ($receiptId) {
                        $q->where('id', '!=', $receiptId);
                    }
                });
            $otherPaid = (float)$otherPaidQuery->sum(DB::raw('paid_amount + discount + tds'));

            $dueAmount = round($totalInvoiceAmt - $otherPaid, 2);

            $isAttachedToCurrent = isset($existingItemsMap[$inv->id]);
            $isFullySettled = ($totalInvoiceAmt > 0 && $otherPaid >= $totalInvoiceAmt);

            // Include if not fully settled on other receipts, or if already attached to current receipt
            if (!$isFullySettled || $isAttachedToCurrent) {
                $savedItem = $existingItemsMap[$inv->id] ?? null;

                $discount = $savedItem ? (float)$savedItem->discount : 0.00;
                $tds = $savedItem ? (float)$savedItem->tds : 0.00;
                $paid = $savedItem ? (float)$savedItem->paid_amount : max(0, $dueAmount);
                $utrNo = $savedItem ? $savedItem->utr_no : '';
                $isFullPay = $savedItem ? (bool)$savedItem->is_full_pay : true;
                $balance = $savedItem ? (float)$savedItem->balance : round(max(0, $dueAmount - $discount - $tds - $paid), 2);

                $resultInvoices[] = [
                    'invoice_id' => $inv->id,
                    'series' => $inv->series ?: 'A',
                    'invoice_no' => $inv->invoice_no,
                    'invoice_date' => $inv->invoice_date ? $inv->invoice_date->format('d-m-Y') : '',
                    'gross_amount' => $grossAmt,
                    'gst_amount' => $gstAmt,
                    'bill_amount' => $totalInvoiceAmt,
                    'old_paid' => $otherPaid,
                    'due_amount' => $dueAmount,
                    'discount' => $discount,
                    'tds' => $tds,
                    'paid' => $paid,
                    'utr_no' => $utrNo,
                    'is_full_pay' => $isFullPay,
                    'balance' => $balance,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'mobile' => $mobile,
            'invoices' => $resultInvoices,
        ]);
    }

    /**
     * Store a new Receipt Voucher.
     */
    public function store(Request $request)
    {
        $request->validate([
            'series' => 'required|string|max:10',
            'receipt_no' => 'required|integer',
            'receipt_date' => 'required|date',
            'account_name' => 'required|string',
        ]);

        $series = strtoupper(trim($request->series));
        $receiptNo = (int)$request->receipt_no;

        // Check uniqueness for series + receipt_no
        $existing = Receipt::where('series', $series)->where('receipt_no', $receiptNo)->first();
        if ($existing) {
            $maxReceiptNo = Receipt::where('series', $series)->max('receipt_no');
            $receiptNo = $maxReceiptNo + 1;
        }

        $account = AccountLedger::where('ledger_name', $request->account_name)->first();

        DB::beginTransaction();
        try {
            $receipt = new Receipt();
            $receipt->series = $series;
            $receipt->receipt_no = $receiptNo;
            $receipt->receipt_date = $request->receipt_date;
            $receipt->receipt_time = $request->receipt_time ?: Carbon::now()->format('H:i:s');
            $receipt->voucher_no = $request->voucher_no ?: (string)$receiptNo;
            $receipt->account_id = $account ? $account->id : null;
            $receipt->account_name = $request->account_name;
            $receipt->mobile = $request->mobile;

            $receipt->bill_amount = (float)$request->bill_amount;
            $receipt->due_amount = (float)$request->due_amount;
            $receipt->discount_amount = (float)$request->discount_amount;
            $receipt->tds_amount = (float)$request->tds_amount;
            $receipt->receipt_amount = (float)$request->receipt_amount;
            $receipt->balance_amount = (float)$request->balance_amount;

            $receipt->pay_mode = $request->pay_mode ?: 'CASH';
            $receipt->bank_name = $request->bank_name;
            if ($request->bank_name) {
                $bankLedger = AccountLedger::where('ledger_name', $request->bank_name)->first();
                $receipt->bank_ledger_id = $bankLedger ? $bankLedger->id : null;
            }
            $receipt->cheque_no = $request->cheque_no;
            $receipt->cheque_date = $request->cheque_date ?: null;
            $receipt->remark = $request->remark;
            $receipt->status = $request->input('status', 'final');
            $receipt->user_id = auth()->id();
            $receipt->save();

            // Save Receipt Items
            $itemsData = $request->input('items', []);
            $srNo = 1;
            foreach ($itemsData as $item) {
                $paid = (float)($item['paid'] ?? 0);
                $discount = (float)($item['discount'] ?? 0);
                $tds = (float)($item['tds'] ?? 0);

                // Only save if there is an amount involved or item selected
                if ($paid > 0 || $discount > 0 || $tds > 0 || !empty($item['is_full_pay'])) {
                    $receiptItem = new ReceiptItem();
                    $receiptItem->receipt_id = $receipt->id;
                    $receiptItem->invoice_id = !empty($item['invoice_id']) ? $item['invoice_id'] : null;
                    $receiptItem->sr_no = $srNo++;
                    $receiptItem->series = $item['series'] ?? 'A';
                    $receiptItem->invoice_no = (int)($item['invoice_no'] ?? 0);
                    $receiptItem->gross_amount = (float)($item['gross_amount'] ?? 0);
                    $receiptItem->gst_amount = (float)($item['gst_amount'] ?? 0);
                    $receiptItem->bill_amount = (float)($item['bill_amount'] ?? 0);
                    $receiptItem->old_paid = (float)($item['old_paid'] ?? 0);
                    $receiptItem->due_amount = (float)($item['due_amount'] ?? 0);
                    $receiptItem->discount = $discount;
                    $receiptItem->tds = $tds;
                    $receiptItem->paid_amount = $paid;
                    $receiptItem->utr_no = $item['utr_no'] ?? null;
                    $receiptItem->is_full_pay = !empty($item['is_full_pay']) && $item['is_full_pay'] != '0';
                    $receiptItem->balance = (float)($item['balance'] ?? 0);
                    $receiptItem->save();
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Receipt saved successfully.',
                    'receipt_id' => $receipt->id,
                    'receipt_no' => $receipt->receipt_no
                ]);
            }

            return redirect()->route('receipt.edit', $receipt->id)
                ->with('success', "Receipt #{$receipt->series}-{$receipt->receipt_no} saved successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error saving receipt: ' . $e->getMessage());
        }
    }

    /**
     * Show the Receipt Voucher edit form.
     */
    public function edit($id)
    {
        $existingReceipt = Receipt::with(['items', 'account'])->findOrFail($id);

        $series = $existingReceipt->series ?: 'A';
        $nextReceiptNo = $existingReceipt->receipt_no;

        // All Accounts
        $accounts = AccountLedger::orderBy('ledger_name')->get();

        // Bank Accounts
        $bankAccounts = AccountLedger::where(function($q) {
            $q->where('under_group', 'like', '%Bank%')
              ->orWhere('under_group', 'like', '%Cash%')
              ->orWhere('ledger_name', 'like', '%Bank%')
              ->orWhere('ledger_name', 'like', '%HDFC%')
              ->orWhere('ledger_name', 'like', '%SBI%')
              ->orWhere('ledger_name', 'like', '%ICICI%')
              ->orWhere('ledger_name', 'like', '%AXIS%')
              ->orWhere('ledger_name', 'like', '%PNB%');
        })->orderBy('ledger_name')->get();

        if ($bankAccounts->isEmpty()) {
            $bankAccounts = $accounts;
        }

        $currentTime = $existingReceipt->receipt_time ?: Carbon::now()->format('H:i:s');
        $currentDate = $existingReceipt->receipt_date ? $existingReceipt->receipt_date->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $selectedAccount = $existingReceipt->account ?: AccountLedger::where('ledger_name', $existingReceipt->account_name)->first();

        return view('receipt.create', compact(
            'existingReceipt',
            'series',
            'nextReceiptNo',
            'accounts',
            'bankAccounts',
            'currentTime',
            'currentDate',
            'selectedAccount'
        ));
    }

    /**
     * Update an existing Receipt Voucher.
     */
    public function update(Request $request, $id)
    {
        $receipt = Receipt::findOrFail($id);

        $request->validate([
            'series' => 'required|string|max:10',
            'receipt_no' => 'required|integer',
            'receipt_date' => 'required|date',
            'account_name' => 'required|string',
        ]);

        $account = AccountLedger::where('ledger_name', $request->account_name)->first();

        DB::beginTransaction();
        try {
            $receipt->series = strtoupper(trim($request->series));
            $receipt->receipt_no = (int)$request->receipt_no;
            $receipt->receipt_date = $request->receipt_date;
            $receipt->receipt_time = $request->receipt_time ?: Carbon::now()->format('H:i:s');
            $receipt->voucher_no = $request->voucher_no ?: (string)$receipt->receipt_no;
            $receipt->account_id = $account ? $account->id : null;
            $receipt->account_name = $request->account_name;
            $receipt->mobile = $request->mobile;

            $receipt->bill_amount = (float)$request->bill_amount;
            $receipt->due_amount = (float)$request->due_amount;
            $receipt->discount_amount = (float)$request->discount_amount;
            $receipt->tds_amount = (float)$request->tds_amount;
            $receipt->receipt_amount = (float)$request->receipt_amount;
            $receipt->balance_amount = (float)$request->balance_amount;

            $receipt->pay_mode = $request->pay_mode ?: 'CASH';
            $receipt->bank_name = $request->bank_name;
            if ($request->bank_name) {
                $bankLedger = AccountLedger::where('ledger_name', $request->bank_name)->first();
                $receipt->bank_ledger_id = $bankLedger ? $bankLedger->id : null;
            }
            $receipt->cheque_no = $request->cheque_no;
            $receipt->cheque_date = $request->cheque_date ?: null;
            $receipt->remark = $request->remark;
            $receipt->status = $request->input('status', 'final');
            $receipt->save();

            // Replace receipt items
            ReceiptItem::where('receipt_id', $receipt->id)->delete();

            $itemsData = $request->input('items', []);
            $srNo = 1;
            foreach ($itemsData as $item) {
                $paid = (float)($item['paid'] ?? 0);
                $discount = (float)($item['discount'] ?? 0);
                $tds = (float)($item['tds'] ?? 0);

                if ($paid > 0 || $discount > 0 || $tds > 0 || !empty($item['is_full_pay'])) {
                    $receiptItem = new ReceiptItem();
                    $receiptItem->receipt_id = $receipt->id;
                    $receiptItem->invoice_id = !empty($item['invoice_id']) ? $item['invoice_id'] : null;
                    $receiptItem->sr_no = $srNo++;
                    $receiptItem->series = $item['series'] ?? 'A';
                    $receiptItem->invoice_no = (int)($item['invoice_no'] ?? 0);
                    $receiptItem->gross_amount = (float)($item['gross_amount'] ?? 0);
                    $receiptItem->gst_amount = (float)($item['gst_amount'] ?? 0);
                    $receiptItem->bill_amount = (float)($item['bill_amount'] ?? 0);
                    $receiptItem->old_paid = (float)($item['old_paid'] ?? 0);
                    $receiptItem->due_amount = (float)($item['due_amount'] ?? 0);
                    $receiptItem->discount = $discount;
                    $receiptItem->tds = $tds;
                    $receiptItem->paid_amount = $paid;
                    $receiptItem->utr_no = $item['utr_no'] ?? null;
                    $receiptItem->is_full_pay = !empty($item['is_full_pay']) && $item['is_full_pay'] != '0';
                    $receiptItem->balance = (float)($item['balance'] ?? 0);
                    $receiptItem->save();
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Receipt updated successfully.',
                    'receipt_id' => $receipt->id
                ]);
            }

            return redirect()->route('receipt.edit', $receipt->id)
                ->with('success', "Receipt #{$receipt->series}-{$receipt->receipt_no} updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error updating receipt: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a Receipt Voucher.
     */
    public function cancel(Request $request, $id)
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->status = 'cancelled';
        $receipt->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Receipt cancelled successfully.']);
        }

        return redirect()->route('receipt.create')
            ->with('success', "Receipt #{$receipt->series}-{$receipt->receipt_no} marked as CANCELLED.");
    }

    /**
     * Delete a Receipt Voucher.
     */
    public function destroy(Request $request, $id)
    {
        $receipt = Receipt::findOrFail($id);
        $receiptNo = "{$receipt->series}-{$receipt->receipt_no}";
        $receipt->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Receipt deleted successfully.']);
        }

        return redirect()->route('receipt.create')
            ->with('success', "Receipt #{$receiptNo} deleted permanently.");
    }

    /**
     * Printable Money Receipt Voucher.
     */
    public function print($id)
    {
        $receipt = Receipt::with(['items.invoice', 'account', 'user'])->findOrFail($id);
        return view('receipt.print', compact('receipt'));
    }

    /**
     * Helper to build filtered query for Receipt Register.
     */
    private function getFilteredReceiptsQuery(Request $request)
    {
        $query = Receipt::with(['items', 'account', 'user']);

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('receipt_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('receipt_date', '<=', $request->to_date);
        }

        // Series
        if ($request->filled('series')) {
            $query->where('series', strtoupper(trim($request->series)));
        }

        // Customer (exact / dropdown)
        if ($request->filled('customer')) {
            $cust = trim($request->customer);
            $query->where(function($q) use ($cust) {
                $q->where('account_name', $cust)
                  ->orWhere('account_name', 'like', '%' . $cust . '%');
            });
        }

        // Customer Like
        if ($request->filled('customer_like')) {
            $custLike = trim($request->customer_like);
            $query->where('account_name', 'like', '%' . $custLike . '%');
        }

        // User
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // Cancel Status (non_cancel, cancel, all)
        $cancelStatus = $request->input('cancel_status', 'non_cancel');
        if ($cancelStatus === 'non_cancel') {
            $query->where(function($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'cancelled');
            });
        } elseif ($cancelStatus === 'cancel') {
            $query->where('status', 'cancelled');
        }

        return $query;
    }

    /**
     * Receipt Register Report.
     */
    public function register(Request $request)
    {
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        if (!$request->has('from_date')) {
            $request->merge(['from_date' => $fromDate]);
        }
        if (!$request->has('to_date')) {
            $request->merge(['to_date' => $toDate]);
        }

        if ($request->get('export') === 'excel') {
            return $this->exportExcel($request);
        }

        $query = $this->getFilteredReceiptsQuery($request);
        $receipts = $query->orderBy('receipt_date', 'asc')->orderBy('receipt_no', 'asc')->get();

        $users = User::orderBy('name')->get();
        $customers = AccountLedger::orderBy('ledger_name')->pluck('ledger_name')->filter()->unique()->values();

        $totalTdsAmt = 0;
        $totalRectAmt = 0;

        foreach ($receipts as $r) {
            $totalTdsAmt += (float)$r->tds_amount;
            $totalRectAmt += (float)$r->receipt_amount;
        }

        return view('receipt.register', compact(
            'receipts',
            'users',
            'customers',
            'totalTdsAmt',
            'totalRectAmt',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Export Receipt Register to Excel (XLSX).
     */
    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredReceiptsQuery($request);
        $receipts = $query->orderBy('receipt_date', 'asc')->orderBy('receipt_no', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Receipt Register');

        // Company Header
        $sheet->setCellValue('A1', 'OMKAAR LOGISTICS - Receipt Register');
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('8B0000');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $fromDateStr = $request->filled('from_date') ? Carbon::parse($request->from_date)->format('d-m-Y') : 'All';
        $toDateStr = $request->filled('to_date') ? Carbon::parse($request->to_date)->format('d-m-Y') : 'All';
        $sheet->setCellValue('A2', "Date Range: {$fromDateStr} To {$toDateStr}");
        $sheet->mergeCells('A2:M2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column Headers
        $headers = [
            'A3' => 'Srno.',
            'B3' => 'Series',
            'C3' => 'Receipt No',
            'D3' => 'Date',
            'E3' => 'Receipt By',
            'F3' => 'TDS',
            'G3' => 'Amount',
            'H3' => 'MOP',
            'I3' => 'Bank/Account',
            'J3' => 'No.',
            'K3' => 'Che.Date',
            'L3' => 'Remark',
            'M3' => 'Entry By',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E4E2DE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '999999']],
            ],
        ];
        $sheet->getStyle('A3:M3')->applyFromArray($headerStyle);
        $sheet->getRowDimension(3)->setRowHeight(24);

        $rowNum = 4;
        $srNo = 1;
        $totalTds = 0;
        $totalAmt = 0;

        foreach ($receipts as $r) {
            $tds = (float)$r->tds_amount;
            $amt = (float)$r->receipt_amount;
            $totalTds += $tds;
            $totalAmt += $amt;

            $sheet->setCellValue('A' . $rowNum, $srNo++);
            $sheet->setCellValue('B' . $rowNum, $r->series);
            $sheet->setCellValue('C' . $rowNum, $r->voucher_no ?: $r->receipt_no);
            $sheet->setCellValue('D' . $rowNum, $r->receipt_date ? $r->receipt_date->format('d-m-Y') : '');
            $sheet->setCellValue('E' . $rowNum, $r->account_name);
            $sheet->setCellValue('F' . $rowNum, $tds);
            $sheet->setCellValue('G' . $rowNum, $amt);
            $sheet->setCellValue('H' . $rowNum, $r->pay_mode);
            $sheet->setCellValue('I' . $rowNum, $r->bank_name ?: ($r->bank ? $r->bank->ledger_name : ''));
            $sheet->setCellValue('J' . $rowNum, $r->cheque_no ?: '');
            $sheet->setCellValue('K' . $rowNum, $r->cheque_date ? $r->cheque_date->format('d-m-Y') : '');
            $sheet->setCellValue('L' . $rowNum, $r->remark ?: '');
            $sheet->setCellValue('M' . $rowNum, $r->user ? ($r->user->name ?: $r->user->username) : 'ADMIN');

            // Alignment
            $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('F' . $rowNum . ':G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $rowNum . ':K' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('M' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Numbers format
            $sheet->getStyle('F' . $rowNum . ':G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $rowNum . ':M' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D0D0D0');

            $rowNum++;
        }

        // Totals Row
        $sheet->setCellValue('A' . $rowNum, 'Total');
        $sheet->mergeCells('A' . $rowNum . ':E' . $rowNum);
        $sheet->setCellValue('F' . $rowNum, $totalTds);
        $sheet->setCellValue('G' . $rowNum, $totalAmt);
        $sheet->setCellValue('H' . $rowNum, '');
        $sheet->setCellValue('I' . $rowNum, '');
        $sheet->setCellValue('J' . $rowNum, '');
        $sheet->setCellValue('K' . $rowNum, '');
        $sheet->setCellValue('L' . $rowNum, '');
        $sheet->setCellValue('M' . $rowNum, '');

        $totalStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEDBDB'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '999999']],
            ],
        ];
        $sheet->getStyle('A' . $rowNum . ':M' . $rowNum)->applyFromArray($totalStyle);
        $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F' . $rowNum . ':G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Receipt_Register_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Helper to build filtered query for Receipt Detail / TDS Report.
     */
    private function getFilteredReceiptItemsQuery(Request $request)
    {
        $query = ReceiptItem::with(['receipt.user', 'receipt.account', 'receipt.bank', 'invoice']);

        $query->whereHas('receipt', function($q) use ($request) {
            // Date range
            if ($request->filled('from_date')) {
                $q->whereDate('receipt_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $q->whereDate('receipt_date', '<=', $request->to_date);
            }

            // Series
            if ($request->filled('series')) {
                $q->where('series', strtoupper(trim($request->series)));
            }

            // Customer (exact / dropdown)
            if ($request->filled('customer')) {
                $cust = trim($request->customer);
                $q->where(function($sq) use ($cust) {
                    $sq->where('account_name', $cust)
                       ->orWhere('account_name', 'like', '%' . $cust . '%');
                });
            }

            // Customer Like
            if ($request->filled('customer_like')) {
                $custLike = trim($request->customer_like);
                $q->where('account_name', 'like', '%' . $custLike . '%');
            }

            // User
            if ($request->filled('user_id') && $request->user_id !== 'all') {
                $q->where('user_id', $request->user_id);
            }

            // Cancel Status (non_cancel, cancel, all)
            $cancelStatus = $request->input('cancel_status', 'non_cancel');
            if ($cancelStatus === 'non_cancel') {
                $q->where(function($sq) {
                    $sq->whereNull('status')->orWhere('status', '!=', 'cancelled');
                });
            } elseif ($cancelStatus === 'cancel') {
                $q->where('status', 'cancelled');
            }
        });

        // Report Type: receipt_detail vs tds_report
        $reportType = $request->input('report_type', 'receipt_detail');
        if ($reportType === 'tds_report') {
            $query->where('tds', '>', 0);
        }

        return $query;
    }

    /**
     * Receipt Detail / TDS Report.
     */
    public function receiptDetailTdsReport(Request $request)
    {
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        if (!$request->has('from_date')) {
            $request->merge(['from_date' => $fromDate]);
        }
        if (!$request->has('to_date')) {
            $request->merge(['to_date' => $toDate]);
        }

        if ($request->get('export') === 'excel') {
            return $this->exportReceiptDetailTdsExcel($request);
        }

        $query = $this->getFilteredReceiptItemsQuery($request);
        $items = $query->join('receipts', 'receipt_items.receipt_id', '=', 'receipts.id')
            ->select('receipt_items.*')
            ->orderBy('receipts.receipt_date', 'asc')
            ->orderBy('receipts.receipt_no', 'asc')
            ->orderBy('receipt_items.sr_no', 'asc')
            ->get();

        $users = User::orderBy('name')->get();
        $customers = AccountLedger::orderBy('ledger_name')->pluck('ledger_name')->filter()->unique()->values();

        $totalTdsAmt = 0;
        $totalPaidAmt = 0;

        foreach ($items as $it) {
            $totalTdsAmt += (float)$it->tds;
            $totalPaidAmt += (float)$it->paid_amount;
        }

        return view('receipt.detail_tds_report', compact(
            'items',
            'users',
            'customers',
            'totalTdsAmt',
            'totalPaidAmt',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Export Receipt Detail / TDS Report to Excel (XLSX).
     */
    public function exportReceiptDetailTdsExcel(Request $request)
    {
        $query = $this->getFilteredReceiptItemsQuery($request);
        $items = $query->join('receipts', 'receipt_items.receipt_id', '=', 'receipts.id')
            ->select('receipt_items.*')
            ->orderBy('receipts.receipt_date', 'asc')
            ->orderBy('receipts.receipt_no', 'asc')
            ->orderBy('receipt_items.sr_no', 'asc')
            ->get();

        $reportType = $request->input('report_type', 'receipt_detail');
        $title = ($reportType === 'tds_report') ? 'TDS Report' : 'Receipt Detail Report';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($reportType === 'tds_report' ? 'TDS Report' : 'Receipt Detail');

        // Company Header
        $sheet->setCellValue('A1', "OMKAAR LOGISTICS - {$title}");
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('8B0000');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $fromDateStr = $request->filled('from_date') ? Carbon::parse($request->from_date)->format('d-m-Y') : 'All';
        $toDateStr = $request->filled('to_date') ? Carbon::parse($request->to_date)->format('d-m-Y') : 'All';
        $sheet->setCellValue('A2', "Date Range: {$fromDateStr} To {$toDateStr}");
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column Headers
        $headers = [
            'A3' => 'Srno.',
            'B3' => 'Series',
            'C3' => 'Receipt No',
            'D3' => 'Date',
            'E3' => 'Receipt By',
            'F3' => 'Invoice No',
            'G3' => 'UTR No',
            'H3' => 'TDS',
            'I3' => 'Amount',
            'J3' => 'MOP',
            'K3' => 'Bank/Account',
            'L3' => 'No.',
            'M3' => 'Che.Date',
            'N3' => 'Remark',
            'O3' => 'Entry By',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E4E2DE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '999999']],
            ],
        ];
        $sheet->getStyle('A3:O3')->applyFromArray($headerStyle);
        $sheet->getRowDimension(3)->setRowHeight(24);

        $rowNum = 4;
        $srNo = 1;
        $totalTds = 0;
        $totalAmt = 0;

        foreach ($items as $it) {
            $r = $it->receipt;
            $tds = (float)$it->tds;
            $amt = (float)$it->paid_amount;
            $totalTds += $tds;
            $totalAmt += $amt;

            $sheet->setCellValue('A' . $rowNum, $srNo++);
            $sheet->setCellValue('B' . $rowNum, $it->series ?: ($r ? $r->series : 'A'));
            $sheet->setCellValue('C' . $rowNum, $r ? ($r->voucher_no ?: $r->receipt_no) : '');
            $sheet->setCellValue('D' . $rowNum, ($r && $r->receipt_date) ? $r->receipt_date->format('d-m-Y') : '');
            $sheet->setCellValue('E' . $rowNum, $r ? $r->account_name : '');
            $sheet->setCellValue('F' . $rowNum, $it->invoice_no);
            $sheet->setCellValue('G' . $rowNum, $it->utr_no ?: '');
            $sheet->setCellValue('H' . $rowNum, $tds);
            $sheet->setCellValue('I' . $rowNum, $amt);
            $sheet->setCellValue('J' . $rowNum, $r ? $r->pay_mode : '');
            $sheet->setCellValue('K' . $rowNum, $r ? ($r->bank_name ?: ($r->bank ? $r->bank->ledger_name : '')) : '');
            $sheet->setCellValue('L' . $rowNum, $r ? ($r->cheque_no ?: '') : '');
            $sheet->setCellValue('M' . $rowNum, ($r && $r->cheque_date) ? $r->cheque_date->format('d-m-Y') : '');
            $sheet->setCellValue('N' . $rowNum, $r ? ($r->remark ?: '') : '');
            $sheet->setCellValue('O' . $rowNum, ($r && $r->user) ? ($r->user->name ?: $r->user->username) : 'ADMIN');

            // Alignment
            $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('F' . $rowNum . ':G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $rowNum . ':I' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J' . $rowNum . ':M' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('N' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('O' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Numbers format
            $sheet->getStyle('H' . $rowNum . ':I' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D0D0D0');

            $rowNum++;
        }

        // Totals Row
        $sheet->setCellValue('A' . $rowNum, 'Total');
        $sheet->mergeCells('A' . $rowNum . ':G' . $rowNum);
        $sheet->setCellValue('H' . $rowNum, $totalTds);
        $sheet->setCellValue('I' . $rowNum, $totalAmt);
        $sheet->setCellValue('J' . $rowNum, '');
        $sheet->setCellValue('K' . $rowNum, '');
        $sheet->setCellValue('L' . $rowNum, '');
        $sheet->setCellValue('M' . $rowNum, '');
        $sheet->setCellValue('N' . $rowNum, '');
        $sheet->setCellValue('O' . $rowNum, '');

        $totalStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEDBDB'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '999999']],
            ],
        ];
        $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->applyFromArray($totalStyle);
        $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H' . $rowNum . ':I' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = ($reportType === 'tds_report' ? 'TDS_Report_' : 'Receipt_Detail_Report_') . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}

