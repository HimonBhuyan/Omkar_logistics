<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    protected $fillable = ['year_string', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
