<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountLedger extends Model
{
    protected $table = 'account_ledgers';

    protected $fillable = [
        'code', 'state_code', 'ledger_name', 'under_group', 'contact_person',
        'address', 'country_id', 'state_id', 'city_id', 'pin_code',
        'phone_o', 'phone_r', 'points', 'credit_limit', 'limit_days',
        'mobile', 'fax', 'email', 'salesman', 'print_copy',
        'web', 'gst_no', 'di_no', 'transport', 'bank_name',
        'account_no', 'ifsc', 'opening', 'dom', 'margin',
        'dob', 'discnt', 'payment_type', 'customer_type',
    ];

    protected $casts = [
        'dom' => 'date',
        'dob' => 'date',
    ];

    public function countryRelation()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function stateRelation()
    {
        return $this->belongsTo(StateModel::class, 'state_id');
    }

    public function cityRelation()
    {
        return $this->belongsTo(CityModel::class, 'city_id');
    }

    public function getNameAttribute()
    {
        return $this->attributes['ledger_name'] ?? null;
    }
}
