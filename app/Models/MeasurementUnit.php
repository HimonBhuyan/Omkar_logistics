<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
    use HasFactory;

    protected $table = 'measurement_units';

    protected $fillable = [
        'company_id',
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

    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: session('company_id');

        return $query->where(function ($q) use ($companyId) {
            $q->whereNull('company_id')
              ->orWhere('is_system', true);

            if ($companyId) {
                $q->orWhere('company_id', $companyId);
            }
        });
    }

    public function isDeletable(): bool
    {
        return !$this->is_system && !in_array(strtoupper($this->unit_code), ['KG', 'FIXED']);
    }
}
