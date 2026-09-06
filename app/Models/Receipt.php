<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends Model
{
    protected $table = 'receipts';

    protected $fillable = [
        'series',
        'receipt_no',
        'receipt_date',
        'receipt_time',
        'voucher_no',
        'account_id',
        'account_name',
        'mobile',
        'bill_amount',
        'due_amount',
        'discount_amount',
        'tds_amount',
        'receipt_amount',
        'balance_amount',
        'pay_mode',
        'bank_ledger_id',
        'bank_name',
        'cheque_no',
        'cheque_date',
        'remark',
        'status',
        'user_id'
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'cheque_date' => 'date',
        'bill_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'receipt_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountLedger::class, 'account_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(AccountLedger::class, 'bank_ledger_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReceiptItem::class, 'receipt_id');
    }
}
