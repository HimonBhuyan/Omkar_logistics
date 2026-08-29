<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bilty extends Model
{
    protected $fillable = [
        'series',
        'bilty_no',
        'invoice_date',
        'from_location_id',
        'to_location_id',
        'consignor_id',
        'consignee_id',
        'billing_type',
        'billing_party_id',
        'cn_no',
        'vehicle_no',
        'eway_bill_no',
        'total_packages',
        'total_qty',
        'gross_amount',
        'st_charge',
        'rc_charge',
        'sc_charge',
        'dd_charge',
        'round_off',
        'net_amount',
        'cash_amount',
        'card_amount',
        'upi_chq_amount',
        'ref_no',
        'payment_date',
        'bank_account',
        'balance_amount',
        'remark',
        'voucher_no',
        'status',
        'user_id'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'payment_date' => 'date',
        'total_packages' => 'integer',
        'total_qty' => 'decimal:3',
        'gross_amount' => 'decimal:2',
        'st_charge' => 'decimal:2',
        'rc_charge' => 'decimal:2',
        'sc_charge' => 'decimal:2',
        'dd_charge' => 'decimal:2',
        'round_off' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'upi_chq_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2'
    ];

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function consignor(): BelongsTo
    {
        return $this->belongsTo(AccountLedger::class, 'consignor_id');
    }

    public function consignee(): BelongsTo
    {
        return $this->belongsTo(AccountLedger::class, 'consignee_id');
    }

    public function billingParty(): BelongsTo
    {
        return $this->belongsTo(AccountLedger::class, 'billing_party_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BiltyItem::class, 'bilty_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
