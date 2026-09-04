<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\BiltyController;
use App\Http\Controllers\AccountLedgerController;
use App\Http\Controllers\GeneralMasterController;
use App\Http\Controllers\ReportController;

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

    // Bilty Register Report
    Route::get('/report/bilty-register', [ReportController::class, 'biltyRegister'])->name('report.bilty_register');
    Route::get('/report/bilty-register/export', [ReportController::class, 'exportExcel'])->name('report.bilty_register.export');

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

    Route::get('/master/measurement-unit', [GeneralMasterController::class, 'measurementUnitIndex'])->name('master.measurement-unit');
    Route::get('/master/measurement-unit/{id}', [GeneralMasterController::class, 'measurementUnitIndex'])->name('master.measurement-unit.load');
    Route::post('/master/measurement-unit', [GeneralMasterController::class, 'measurementUnitStore'])->name('master.measurement-unit.store');
    Route::delete('/master/measurement-unit/bulk', [GeneralMasterController::class, 'measurementUnitBulkDestroy'])->name('master.measurement-unit.bulk_destroy');
    Route::delete('/master/measurement-unit/{id}', [GeneralMasterController::class, 'measurementUnitDestroy'])->name('master.measurement-unit.destroy');
});
