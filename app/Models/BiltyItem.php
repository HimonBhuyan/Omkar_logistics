<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiltyItem extends Model
{
    protected $fillable = [
        'bilty_id',
        'no_of_pkgs',
        'packing',
        'description',
        'invoice_no',
        'invoice_value',
        'weight_type',
        'qty',
        'rate',
        'weight_val',
        'st',
        'rc',
        'sc',
        'dd'
    ];

    protected $casts = [
        'no_of_pkgs' => 'integer',
        'invoice_value' => 'decimal:2',
        'qty' => 'decimal:3',
        'rate' => 'decimal:2',
        'st' => 'decimal:2',
        'rc' => 'decimal:2',
        'sc' => 'decimal:2',
        'dd' => 'decimal:2'
    ];

    public function bilty(): BelongsTo
    {
        return $this->belongsTo(Bilty::class, 'bilty_id');
    }
}
