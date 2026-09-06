<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'bilty_id',
        'sr_no',
        'bilty_date',
        'bilty_no',
        'cn_no',
        'packages',
        'from_location',
        'to_location',
        'consignee_name',
        'item_description',
        'invoice_no_ref',
        'weight',
        'weight_type',
        'rate',
        'st_charge',
        'freight_amount',
        'unload_rate',
        'unload_amount',
        'other_charges',
        'oda_charge',
        'amount'
    ];

    protected $casts = [
        'bilty_date' => 'date',
        'packages' => 'integer',
        'weight' => 'decimal:3',
        'rate' => 'decimal:2',
        'st_charge' => 'decimal:2',
        'freight_amount' => 'decimal:2',
        'unload_rate' => 'decimal:2',
        'unload_amount' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'oda_charge' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function bilty(): BelongsTo
    {
        return $this->belongsTo(Bilty::class, 'bilty_id');
    }
}
