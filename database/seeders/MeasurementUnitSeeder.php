<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MeasurementUnit;

class MeasurementUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'unit_code' => 'KG',
                'unit_name' => 'KILOGRAM',
                'unit_type' => 'weight',
                'package_label' => 'NoOfPkgs',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'unit_code' => 'FIXED',
                'unit_name' => 'FIXED RATE',
                'unit_type' => 'fixed',
                'package_label' => 'NoOfPkgs',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'unit_code' => 'TON',
                'unit_name' => 'METRIC TON',
                'unit_type' => 'weight',
                'package_label' => 'NoOfPkgs',
                'is_system' => false,
                'is_active' => true,
            ],
            [
                'unit_code' => 'CASE',
                'unit_name' => 'CASE',
                'unit_type' => 'fixed',
                'package_label' => 'NoOfCases',
                'is_system' => false,
                'is_active' => true,
            ],
            [
                'unit_code' => 'BOX',
                'unit_name' => 'BOX / CARTON',
                'unit_type' => 'fixed',
                'package_label' => 'NoOfBoxes',
                'is_system' => false,
                'is_active' => true,
            ],
            [
                'unit_code' => 'PCS',
                'unit_name' => 'PIECES',
                'unit_type' => 'fixed',
                'package_label' => 'NoOfPcs',
                'is_system' => false,
                'is_active' => true,
            ],
        ];

        foreach ($units as $unitData) {
            MeasurementUnit::updateOrCreate(
                ['unit_code' => $unitData['unit_code']],
                $unitData
            );
        }
    }
}
