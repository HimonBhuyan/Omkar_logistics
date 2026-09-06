<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'series',
        'payment_no',
        'payment_date',
        'payment_time',
        'voucher_no',
        'account_id',
        'account_name',
        'account_alias',
        'customer_invoice_no',
        'customer_bill_amt',
        'deduct_amount',
        'payment_amount',
        'due_amount',
        'discount_amount',
        'tax_type',
        'tax_percent',
        'tax_amount',
        'total_amount',
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
        'payment_date' => 'date',
        'cheque_date' => 'date',
        'customer_bill_amt' => 'decimal:2',
        'deduct_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
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
}
