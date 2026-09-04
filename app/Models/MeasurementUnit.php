<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
    use HasFactory;

    protected $table = 'measurement_units';

    protected $fillable = [
        'unit_code',
        'unit_name',
        'unit_type',
        'package_label',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isDeletable(): bool
    {
        return !$this->is_system && !in_array(strtoupper($this->unit_code), ['KG', 'FIXED']);
    }
}
