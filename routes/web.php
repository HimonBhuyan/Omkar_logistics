<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\BiltyController;
use App\Http\Controllers\AccountLedgerController;
use App\Http\Controllers\GeneralMasterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PaymentController;

// Auth Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('dashboard');
    Route::get('/bilty/create', [BiltyController::class, 'create'])->name('bilty.create');
    Route::post('/bilty/store', [BiltyController::class, 'store'])->name('bilty.store');
    Route::get('/bilty/print/{id}', [BiltyController::class, 'print'])->name('bilty.print');
    Route::get('/bilty/pdf/{id}', [BiltyController::class, 'downloadPdf'])->name('bilty.pdf');
    Route::get('/bilty/party-details/{id}', [BiltyController::class, 'getPartyDetails'])->name('bilty.party-details');
    Route::get('/bilty/lookup/{bilty_no}', [BiltyController::class, 'lookup'])->name('bilty.lookup');
    Route::post('/bilty/update/{id}', [BiltyController::class, 'update'])->name('bilty.update');

    // Invoice / Party Bill Routes
    Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::get('/invoice/lookup/{invoice_no}', [InvoiceController::class, 'lookup'])->name('invoice.lookup');
    Route::get('/invoice/edit/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::get('/invoice/month-parties', [InvoiceController::class, 'getMonthParties'])->name('invoice.month_parties');
    Route::get('/invoice/pending-bilties', [InvoiceController::class, 'getPendingBilties'])->name('invoice.pending_bilties');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::put('/invoice/update/{id}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::post('/invoice/cancel/{id}', [InvoiceController::class, 'cancel'])->name('invoice.cancel');
    Route::delete('/invoice/destroy/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::post('/invoice/preview', [InvoiceController::class, 'preview'])->name('invoice.preview');
    Route::get('/invoice/print/{id}', [InvoiceController::class, 'print'])->name('invoice.print');
    Route::get('/invoice/register', [InvoiceController::class, 'register'])->name('invoice.register');
    Route::get('/invoice/register/export', [InvoiceController::class, 'exportExcel'])->name('invoice.register.export');

    // Receipt / Payment Acceptance Routes
    Route::get('/receipt/create', [ReceiptController::class, 'create'])->name('receipt.create');
    Route::get('/receipt/pending-invoices', [ReceiptController::class, 'getPendingInvoices'])->name('receipt.pending_invoices');
    Route::post('/receipt/store', [ReceiptController::class, 'store'])->name('receipt.store');
    Route::get('/receipt/edit/{id}', [ReceiptController::class, 'edit'])->name('receipt.edit');
    Route::put('/receipt/update/{id}', [ReceiptController::class, 'update'])->name('receipt.update');
    Route::post('/receipt/cancel/{id}', [ReceiptController::class, 'cancel'])->name('receipt.cancel');
    Route::delete('/receipt/destroy/{id}', [ReceiptController::class, 'destroy'])->name('receipt.destroy');
    Route::get('/receipt/print/{id}', [ReceiptController::class, 'print'])->name('receipt.print');
    Route::get('/receipt/register', [ReceiptController::class, 'register'])->name('receipt.register');
    Route::get('/receipt/register/export', [ReceiptController::class, 'exportExcel'])->name('receipt.register.export');

    // Payment Voucher & Payment Register Routes
    Route::get('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::get('/payment/account-details', [PaymentController::class, 'getAccountDetails'])->name('payment.account_details');
    Route::post('/payment/store', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/payment/edit/{id}', [PaymentController::class, 'edit'])->name('payment.edit');
    Route::put('/payment/update/{id}', [PaymentController::class, 'update'])->name('payment.update');
    Route::post('/payment/cancel/{id}', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::delete('/payment/destroy/{id}', [PaymentController::class, 'destroy'])->name('payment.destroy');
    Route::get('/payment/print/{id}', [PaymentController::class, 'print'])->name('payment.print');
    Route::get('/payment/register', [PaymentController::class, 'register'])->name('payment.register');
    Route::get('/payment/register/export', [PaymentController::class, 'exportExcel'])->name('payment.register.export');

    // Reports
    Route::get('/report/bilty-register', [ReportController::class, 'biltyRegister'])->name('report.bilty_register');
    Route::get('/report/bilty-register/export', [ReportController::class, 'exportExcel'])->name('report.bilty_register.export');
    Route::get('/report/receipt-detail-tds', [ReceiptController::class, 'receiptDetailTdsReport'])->name('report.receipt_detail_tds');
    Route::get('/report/receipt-detail-tds/export', [ReceiptController::class, 'exportReceiptDetailTdsExcel'])->name('report.receipt_detail_tds.export');
    Route::get('/account/reports/sundry-creditors', [ReportController::class, 'sundryCreditorsSummary'])->name('report.sundry_creditors');
    Route::get('/account/reports/sundry-creditors/export', [ReportController::class, 'exportSundryCreditorsExcel'])->name('report.sundry_creditors.export');
    Route::get('/account/reports/sundry-debtors', [ReportController::class, 'sundryDebtorsSummary'])->name('report.sundry_debtors');
    Route::get('/account/reports/sundry-debtors/export', [ReportController::class, 'exportSundryDebtorsExcel'])->name('report.sundry_debtors.export');

    // Account Ledger
    Route::get('/account/ledger', [AccountLedgerController::class, 'index'])->name('account.ledger');
    Route::get('/account/ledger/{id}', [AccountLedgerController::class, 'load'])->name('account.ledger.load');
    Route::post('/account/ledger', [AccountLedgerController::class, 'store'])->name('account.ledger.store');
    Route::put('/account/ledger/{id}', [AccountLedgerController::class, 'update'])->name('account.ledger.update');
    Route::delete('/account/ledger/{id}', [AccountLedgerController::class, 'destroy'])->name('account.ledger.destroy');

    // General Masters
    Route::get('/master/country', [GeneralMasterController::class, 'countryIndex'])->name('master.country');
    Route::get('/master/country/{id}', [GeneralMasterController::class, 'countryIndex'])->name('master.country.load');
    Route::post('/master/country', [GeneralMasterController::class, 'countryStore'])->name('master.country.store');
    Route::delete('/master/country/bulk', [GeneralMasterController::class, 'countryBulkDestroy'])->name('master.country.bulk_destroy');
    Route::delete('/master/country/{id}', [GeneralMasterController::class, 'countryDestroy'])->name('master.country.destroy');

    Route::get('/master/state', [GeneralMasterController::class, 'stateIndex'])->name('master.state');
    Route::get('/master/state/{id}', [GeneralMasterController::class, 'stateIndex'])->name('master.state.load');
    Route::post('/master/state', [GeneralMasterController::class, 'stateStore'])->name('master.state.store');
    Route::delete('/master/state/bulk', [GeneralMasterController::class, 'stateBulkDestroy'])->name('master.state.bulk_destroy');
    Route::delete('/master/state/{id}', [GeneralMasterController::class, 'stateDestroy'])->name('master.state.destroy');

    Route::get('/master/city', [GeneralMasterController::class, 'cityIndex'])->name('master.city');
    Route::get('/master/city/{id}', [GeneralMasterController::class, 'cityIndex'])->name('master.city.load');
    Route::post('/master/city', [GeneralMasterController::class, 'cityStore'])->name('master.city.store');
    Route::delete('/master/city/bulk', [GeneralMasterController::class, 'cityBulkDestroy'])->name('master.city.bulk_destroy');
    Route::delete('/master/city/{id}', [GeneralMasterController::class, 'cityDestroy'])->name('master.city.destroy');
});
