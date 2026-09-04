<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UppercaseExistingDataSeeder extends Seeder
{
    /**
     * Run the database seeds to update all existing records to UPPERCASE.
     */
    public function run(): void
    {
        $tablesWithColumns = [
            'bilties' => [
                'series', 'consignor_name', 'consignor_mobile', 'consignee_name',
                'consignee_mobile', 'billing_type', 'status', 'type',
                'billing_party_name', 'cn_no', 'vehicle_no', 'eway_bill_no',
                'ref_no', 'bank_account', 'remark'
            ],
            'bilty_items' => [
                'packing', 'description', 'invoice_no', 'unit'
            ],
            'account_ledgers' => [
                'state_code', 'ledger_name', 'under_group', 'contact_person',
                'address', 'pin_code', 'phone_o', 'phone_r', 'mobile', 'fax',
                'salesman', 'gst_no', 'di_no', 'transport',
                'bank_name', 'account_no', 'ifsc'
            ],
            'parties' => [
                'name', 'mobile', 'address', 'gstin', 'type'
            ],
            'locations' => [
                'name'
            ],
            'cities' => [
                'name', 'short_name'
            ],
            'states' => [
                'name', 'code', 'short_name'
            ],
            'countries' => [
                'name', 'code'
            ],
            'group_ledgers' => [
                'name'
            ],
            'companies' => [
                'name'
            ],
            'measurement_units' => [
                'unit_code', 'unit_name'
            ],
        ];

        foreach ($tablesWithColumns as $table => $columns) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $updates = [];
                foreach ($columns as $col) {
                    if (isset($row->$col) && is_string($row->$col) && trim($row->$col) !== '') {
                        $upperVal = mb_strtoupper(trim($row->$col), 'UTF-8');
                        if ($upperVal !== $row->$col) {
                            $updates[$col] = $upperVal;
                        }
                    }
                }

                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }
    }
}
