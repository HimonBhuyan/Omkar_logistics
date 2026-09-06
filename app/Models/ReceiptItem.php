<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptItem extends Model
{
    protected $table = 'receipt_items';

    protected $fillable = [
        'receipt_id',
        'invoice_id',
        'sr_no',
        'series',
        'invoice_no',
        'gross_amount',
        'gst_amount',
        'bill_amount',
        'old_paid',
        'due_amount',
        'discount',
        'tds',
        'paid_amount',
        'utr_no',
        'is_full_pay',
        'balance'
    ];

    protected $casts = [
        'is_full_pay' => 'boolean',
        'gross_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'bill_amount' => 'decimal:2',
        'old_paid' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tds' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
