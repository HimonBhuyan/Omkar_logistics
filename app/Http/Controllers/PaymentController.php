<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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

class PaymentController extends Controller
{
    /**
     * Show the Payment Voucher creation form.
     */
    public function create(Request $request)
    {
        $series = strtoupper(trim($request->query('series', 'A')));
        $maxPaymentNo = Payment::where('series', $series)->max('payment_no');
        $nextPaymentNo = ($maxPaymentNo && $maxPaymentNo >= 1) ? ($maxPaymentNo + 1) : 1;

        // Accounts filtered by target Expense / Asset / Creditor groups
        $accounts = $this->getPaymentAccounts();

        // Bank / Cash Ledgers
        $bankAccounts = AccountLedger::where(function($q) {
            $q->where('under_group', 'like', '%Bank%')
              ->orWhere('under_group', 'like', '%Cash%')
              ->orWhere('ledger_name', 'like', '%Bank%')
              ->orWhere('ledger_name', 'like', '%HDFC%')
              ->orWhere('ledger_name', 'like', '%SBI%')
              ->orWhere('ledger_name', 'like', '%ICICI%')
              ->orWhere('ledger_name', 'like', '%AXIS%')
              ->orWhere('ledger_name', 'like', '%PNB%')
              ->orWhere('ledger_name', 'like', '%CBI%')
              ->orWhere('ledger_name', 'like', '%OMKAAR%')
              ->orWhere('ledger_name', 'like', '%SEFALI%')
              ->orWhere('ledger_name', 'like', '%SUSOVAN%');
        })->orderBy('ledger_name')->get();

        if ($bankAccounts->isEmpty()) {
            $bankAccounts = $accounts;
        }

        $currentTime = Carbon::now()->format('H:i:s');
        $currentDate = Carbon::now()->format('Y-m-d');

        // Optional preselected account
        $selectedAccount = null;
        if ($request->filled('account_id')) {
            $selectedAccount = AccountLedger::find($request->account_id);
        } elseif ($request->filled('account_name')) {
            $selectedAccount = AccountLedger::where('ledger_name', $request->account_name)->first();
        }

        return view('payment.create', compact(
            'series',
            'nextPaymentNo',
            'accounts',
            'bankAccounts',
            'currentTime',
            'currentDate',
            'selectedAccount'
        ));
    }

    /**
     * Fetch Account details / balance via AJAX.
     */
    public function getAccountDetails(Request $request)
    {
        $accountName = trim($request->input('account_name', ''));
        $accountId = $request->input('account_id');

        $account = null;
        if ($accountId) {
            $account = AccountLedger::find($accountId);
        }
        if (!$account && $accountName) {
            $account = AccountLedger::where('ledger_name', $accountName)->first();
        }

        if (!$account) {
            return response()->json(['success' => false, 'due_amount' => 0.00, 'alias' => '']);
        }

        // Calculate opening or due amount if present
        $dueAmount = (float)($account->opening ?? 0.00);

        return response()->json([
            'success' => true,
            'id' => $account->id,
            'name' => $account->ledger_name,
            'alias' => $account->code ?? '',
            'due_amount' => $dueAmount,
            'bank_name' => $account->bank_name ?? '',
            'account_no' => $account->account_no ?? '',
            'ifsc' => $account->ifsc ?? '',
        ]);
    }

    /**
     * Store a new Payment Voucher.
     */
    public function store(Request $request)
    {
        $request->validate([
            'series' => 'required|string|max:10',
            'payment_no' => 'required|integer',
            'payment_date' => 'required|date',
            'account_name' => 'required|string',
            'payment_amount' => 'required|numeric|min:0',
        ]);

        $series = strtoupper(trim($request->series));
        $paymentNo = (int)$request->payment_no;

        // Ensure unique series + payment_no
        $existing = Payment::where('series', $series)->where('payment_no', $paymentNo)->first();
        if ($existing) {
            $maxPaymentNo = Payment::where('series', $series)->max('payment_no');
            $paymentNo = $maxPaymentNo + 1;
        }

        $account = AccountLedger::where('ledger_name', $request->account_name)->first();

        DB::beginTransaction();
        try {
            $payment = new Payment();
            $payment->series = $series;
            $payment->payment_no = $paymentNo;
            $payment->payment_date = $request->payment_date;
            $payment->payment_time = $request->payment_time ?: Carbon::now()->format('H:i:s');
            $payment->voucher_no = $request->voucher_no ?: (string)$paymentNo;
            
            $payment->account_id = $account ? $account->id : $request->account_id;
            $payment->account_name = $request->account_name;
            $payment->account_alias = $request->account_alias;

            $payment->customer_invoice_no = $request->customer_invoice_no;
            $payment->customer_bill_amt = (float)($request->customer_bill_amt ?? 0.00);

            $grossPayment = (float)$request->payment_amount;
            $deductAmt = (float)($request->deduct_amount ?? $request->due_amount ?? 0.00);
            $discountAmt = (float)($request->discount_amount ?? 0.00);
            $taxType = $request->tax_type;
            $taxPercent = (float)($request->tax_percent ?? 0.00);
            $taxAmt = (float)($request->tax_amount ?? 0.00);
            
            // If total_amount wasn't passed directly, compute net paid
            $totalAmount = $request->filled('total_amount') && (float)$request->total_amount > 0 
                ? (float)$request->total_amount 
                : round($grossPayment - $discountAmt, 2);

            $payment->payment_amount = $grossPayment;
            $payment->due_amount = $deductAmt;
            $payment->deduct_amount = $deductAmt;
            $payment->discount_amount = $discountAmt;
            $payment->tax_type = $taxType;
            $payment->tax_percent = $taxPercent;
            $payment->tax_amount = $taxAmt;
            $payment->total_amount = $totalAmount;

            $payment->pay_mode = $request->pay_mode ?: 'BANK TRANSFER';
            $payment->bank_name = $request->bank_name;
            if ($request->bank_name) {
                $bankLedger = AccountLedger::where('ledger_name', $request->bank_name)->first();
                $payment->bank_ledger_id = $bankLedger ? $bankLedger->id : null;
            }
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_date = $request->cheque_date ?: null;
            $payment->remark = $request->remark;
            $payment->status = $request->input('status', 'final');
            $payment->user_id = auth()->id();
            $payment->save();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment Voucher saved successfully.',
                    'payment_id' => $payment->id,
                    'payment_no' => $payment->payment_no,
                    'voucher_no' => $payment->voucher_no,
                ]);
            }

            return redirect()->route('payment.edit', $payment->id)
                ->with('success', "Payment #{$payment->series}-{$payment->payment_no} saved successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error saving payment: ' . $e->getMessage());
        }
    }

    /**
     * Show the Payment Voucher edit form.
     */
    public function edit($id)
    {
        $existingPayment = Payment::with(['account', 'bank', 'user'])->findOrFail($id);

        $series = $existingPayment->series ?: 'A';
        $nextPaymentNo = $existingPayment->payment_no;

        // Accounts filtered by target Expense / Asset / Creditor groups
        $accounts = $this->getPaymentAccounts();

        $bankAccounts = AccountLedger::where(function($q) {
            $q->where('under_group', 'like', '%Bank%')
              ->orWhere('under_group', 'like', '%Cash%')
              ->orWhere('ledger_name', 'like', '%Bank%')
              ->orWhere('ledger_name', 'like', '%HDFC%')
              ->orWhere('ledger_name', 'like', '%SBI%')
              ->orWhere('ledger_name', 'like', '%ICICI%')
              ->orWhere('ledger_name', 'like', '%AXIS%')
              ->orWhere('ledger_name', 'like', '%PNB%')
              ->orWhere('ledger_name', 'like', '%CBI%')
              ->orWhere('ledger_name', 'like', '%OMKAAR%')
              ->orWhere('ledger_name', 'like', '%SEFALI%')
              ->orWhere('ledger_name', 'like', '%SUSOVAN%');
        })->orderBy('ledger_name')->get();

        if ($bankAccounts->isEmpty()) {
            $bankAccounts = $accounts;
        }

        $currentTime = $existingPayment->payment_time ?: Carbon::now()->format('H:i:s');
        $currentDate = $existingPayment->payment_date ? $existingPayment->payment_date->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $selectedAccount = $existingPayment->account ?: AccountLedger::where('ledger_name', $existingPayment->account_name)->first();

        return view('payment.create', compact(
            'existingPayment',
            'series',
            'nextPaymentNo',
            'accounts',
            'bankAccounts',
            'currentTime',
            'currentDate',
            'selectedAccount'
        ));
    }

    /**
     * Update an existing Payment Voucher.
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'series' => 'required|string|max:10',
            'payment_no' => 'required|integer',
            'payment_date' => 'required|date',
            'account_name' => 'required|string',
            'payment_amount' => 'required|numeric|min:0',
        ]);

        $account = AccountLedger::where('ledger_name', $request->account_name)->first();

        DB::beginTransaction();
        try {
            $payment->series = strtoupper(trim($request->series));
            $payment->payment_no = (int)$request->payment_no;
            $payment->payment_date = $request->payment_date;
            $payment->payment_time = $request->payment_time ?: Carbon::now()->format('H:i:s');
            $payment->voucher_no = $request->voucher_no ?: (string)$payment->payment_no;
            
            $payment->account_id = $account ? $account->id : $request->account_id;
            $payment->account_name = $request->account_name;
            $payment->account_alias = $request->account_alias;

            $payment->customer_invoice_no = $request->customer_invoice_no;
            $payment->customer_bill_amt = (float)($request->customer_bill_amt ?? 0.00);

            $grossPayment = (float)$request->payment_amount;
            $deductAmt = (float)($request->deduct_amount ?? $request->due_amount ?? 0.00);
            $discountAmt = (float)($request->discount_amount ?? 0.00);
            $taxType = $request->tax_type;
            $taxPercent = (float)($request->tax_percent ?? 0.00);
            $taxAmt = (float)($request->tax_amount ?? 0.00);
            
            $totalAmount = $request->filled('total_amount') && (float)$request->total_amount > 0 
                ? (float)$request->total_amount 
                : round($grossPayment - $discountAmt, 2);

            $payment->payment_amount = $grossPayment;
            $payment->due_amount = $deductAmt;
            $payment->deduct_amount = $deductAmt;
            $payment->discount_amount = $discountAmt;
            $payment->tax_type = $taxType;
            $payment->tax_percent = $taxPercent;
            $payment->tax_amount = $taxAmt;
            $payment->total_amount = $totalAmount;

            $payment->pay_mode = $request->pay_mode ?: 'BANK TRANSFER';
            $payment->bank_name = $request->bank_name;
            if ($request->bank_name) {
                $bankLedger = AccountLedger::where('ledger_name', $request->bank_name)->first();
                $payment->bank_ledger_id = $bankLedger ? $bankLedger->id : null;
            }
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_date = $request->cheque_date ?: null;
            $payment->remark = $request->remark;
            $payment->status = $request->input('status', 'final');
            $payment->save();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment Voucher updated successfully.',
                    'payment_id' => $payment->id,
                    'payment_no' => $payment->payment_no,
                    'voucher_no' => $payment->voucher_no,
                ]);
            }

            return redirect()->route('payment.edit', $payment->id)
                ->with('success', "Payment #{$payment->series}-{$payment->payment_no} updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error updating payment: ' . $e->getMessage());
        }
    }

    /**
     * Mark Payment Voucher as cancelled.
     */
    public function cancel(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->status = 'cancelled';
        $payment->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment marked as CANCELLED.']);
        }

        return redirect()->route('payment.create')
            ->with('success', "Payment #{$payment->series}-{$payment->payment_no} marked as CANCELLED.");
    }

    /**
     * Delete a Payment Voucher.
     */
    public function destroy(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $no = "{$payment->series}-{$payment->payment_no}";
        $payment->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment deleted successfully.']);
        }

        return redirect()->route('payment.create')
            ->with('success', "Payment #{$no} deleted permanently.");
    }

    /**
     * Printable Payment Voucher (Matches Image 3).
     */
    public function print($id)
    {
        $payment = Payment::with(['account', 'bank', 'user'])->findOrFail($id);
        return view('payment.print', compact('payment'));
    }

    /**
     * Helper to build filtered query for Payment Register.
     */
    private function getFilteredPaymentsQuery(Request $request)
    {
        $query = Payment::with(['account', 'bank', 'user']);

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        // Series
        if ($request->filled('series')) {
            $query->where('series', strtoupper(trim($request->series)));
        }

        // Bank Name
        if ($request->filled('bank_name')) {
            $bank = trim($request->bank_name);
            $query->where(function($q) use ($bank) {
                $q->where('bank_name', $bank)
                  ->orWhere('bank_name', 'like', '%' . $bank . '%');
            });
        }

        // Supplier / Account
        if ($request->filled('supplier')) {
            $supplier = trim($request->supplier);
            $query->where(function($q) use ($supplier) {
                $q->where('account_name', $supplier)
                  ->orWhere('account_name', 'like', '%' . $supplier . '%');
            });
        }

        // User
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // Cancel status
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
     * Payment Register Report (Matches Image 2).
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

        $query = $this->getFilteredPaymentsQuery($request);
        $payments = $query->orderBy('payment_date', 'asc')->orderBy('payment_no', 'asc')->get();

        $users = User::orderBy('name')->get();
        $suppliers = AccountLedger::orderBy('ledger_name')->pluck('ledger_name')->filter()->unique()->values();
        $bankNames = AccountLedger::where(function($q) {
            $q->where('under_group', 'like', '%Bank%')
              ->orWhere('ledger_name', 'like', '%Bank%')
              ->orWhere('ledger_name', 'like', '%OMKAAR%')
              ->orWhere('ledger_name', 'like', '%SEFALI%')
              ->orWhere('ledger_name', 'like', '%SUSOVAN%');
        })->pluck('ledger_name')->filter()->unique()->values();

        if ($bankNames->isEmpty()) {
            $bankNames = Payment::whereNotNull('bank_name')->pluck('bank_name')->unique()->values();
        }

        $totalPayAmount = 0;
        $totalDeductAmount = 0;
        $totalDiscount = 0;
        $totalPaid = 0;

        foreach ($payments as $p) {
            $totalPayAmount += (float)$p->payment_amount;
            $totalDeductAmount += (float)($p->deduct_amount ?: $p->due_amount);
            $totalDiscount += (float)$p->discount_amount;
            $totalPaid += (float)$p->total_amount;
        }

        return view('payment.register', compact(
            'payments',
            'users',
            'suppliers',
            'bankNames',
            'totalPayAmount',
            'totalDeductAmount',
            'totalDiscount',
            'totalPaid',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Export Payment Register to Excel (XLSX).
     */
    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredPaymentsQuery($request);
        $payments = $query->orderBy('payment_date', 'asc')->orderBy('payment_no', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Payment Register');

        // Company Header
        $sheet->setCellValue('A1', 'OMKAAR LOGISTICS - Payment Register');
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
            'C3' => 'VoucherNo',
            'D3' => 'Date',
            'E3' => 'Party Name',
            'F3' => 'Pay Amount',
            'G3' => 'Deduct Amount',
            'H3' => 'Discount',
            'I3' => 'Paid',
            'J3' => 'MOP',
            'K3' => 'Bank/Account',
            'L3' => 'No.',
            'M3' => 'Che.Date',
            'N3' => 'Purpose of payment',
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
        $totPayAmt = 0;
        $totDeductAmt = 0;
        $totDisc = 0;
        $totPaid = 0;

        foreach ($payments as $p) {
            $payAmt = (float)$p->payment_amount;
            $deductAmt = (float)($p->deduct_amount ?: $p->due_amount);
            $disc = (float)$p->discount_amount;
            $paid = (float)$p->total_amount;

            $totPayAmt += $payAmt;
            $totDeductAmt += $deductAmt;
            $totDisc += $disc;
            $totPaid += $paid;

            $sheet->setCellValue('A' . $rowNum, $srNo++);
            $sheet->setCellValue('B' . $rowNum, $p->series);
            $sheet->setCellValue('C' . $rowNum, $p->voucher_no ?: $p->payment_no);
            $sheet->setCellValue('D' . $rowNum, $p->payment_date ? $p->payment_date->format('d-m-Y') : '');
            $sheet->setCellValue('E' . $rowNum, $p->account_name);
            $sheet->setCellValue('F' . $rowNum, $payAmt);
            $sheet->setCellValue('G' . $rowNum, $deductAmt);
            $sheet->setCellValue('H' . $rowNum, $disc);
            $sheet->setCellValue('I' . $rowNum, $paid);
            $sheet->setCellValue('J' . $rowNum, $p->pay_mode ?: 'Bank');
            $sheet->setCellValue('K' . $rowNum, $p->bank_name ?: ($p->bank ? $p->bank->ledger_name : ''));
            $sheet->setCellValue('L' . $rowNum, $p->cheque_no ?: '');
            $sheet->setCellValue('N' . $rowNum, $p->remark ?: '');
            $sheet->setCellValue('M' . $rowNum, $p->cheque_date ? $p->cheque_date->format('d-m-Y') : '');
            $sheet->setCellValue('O' . $rowNum, $p->user ? ($p->user->name ?: $p->user->username) : 'ADMIN');

            // Alignments
            $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('F' . $rowNum . ':I' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J' . $rowNum . ':M' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('N' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('O' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Numbers formatting
            $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('I' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

            // Yellow fill for data rows
            $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFE0');
            $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D0D0D0');

            $rowNum++;
        }

        // Totals Row
        $sheet->setCellValue('A' . $rowNum, 'Total');
        $sheet->mergeCells('A' . $rowNum . ':E' . $rowNum);
        $sheet->setCellValue('F' . $rowNum, $totPayAmt);
        $sheet->setCellValue('G' . $rowNum, $totDeductAmt);
        $sheet->setCellValue('H' . $rowNum, $totDisc);
        $sheet->setCellValue('I' . $rowNum, $totPaid);
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
        $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('I' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Payment_Register_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Get accounts allowed for Payment Voucher pulled from Group Ledgers:
     * - Direct Expense / Direct Expenses
     * - Fixed Asset / Fixed Assets
     * - Investment / Investments
     * - Misc Expense (Asset) / Misc Expense (Assest)
     * - Sales Account / Sales Accounts
     * - Transport Expense / Tranport Expense
     * - Staff Salary
     * - Vehicle Expense / vehicle expense
     * - Oil Expense / oil Expense
     */
    private function getPaymentAccounts()
    {
        $allowedGroups = [
            'Direct Expense',
            'Direct Expenses',
            'Direct Exprense',
            'Fixed Asset',
            'Fixed Assets',
            'Investment',
            'Investments',
            'Misc. Expense',
            'Misc. Expenses (Asset)',
            'Misc Expense (Asset)',
            'Misc Expense (Assest)',
            'Misc Expense',
            'Sales Account',
            'Sales Accounts',
            'Transport Expense',
            'Transport Expenses',
            'Tranport Expense',
            'Staff Salary',
            'Salary',
            'Vehicle Expense',
            'Vehicle Expenses',
            'vehicle expense',
            'Oil Expense',
            'Oil Expenses',
            'oil Expense',
        ];

        $accounts = AccountLedger::where(function($q) use ($allowedGroups) {
            foreach ($allowedGroups as $grp) {
                $q->orWhere('under_group', 'like', '%' . $grp . '%');
            }
        })->orderBy('ledger_name')->get();

        return $accounts;
    }
}
