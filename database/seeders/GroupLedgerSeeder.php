<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupLedgerSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('group_ledgers')->count() === 0) {
            $groups = [
                'Bank Accounts',
                'Bank OD A/c',
                'Capital Account',
                'Cash-in-Hand',
                'Direct Expenses',
                'Direct Incomes',
                'Fixed Assets',
                'Indirect Expenses',
                'Indirect Incomes',
                'Investments',
                'Loans & Advances (Asset)',
                'Loans (Liabilities)',
                'Misc. Expenses (Asset)',
                'Purchase Accounts',
                'Sales Accounts',
                'Secured Loans',
                'Creditors',
                'Debtors',
                // Added by client
                'Transport Expense',
                'Staff Salary',
                'Vehicle Expense',
                'Oil Expense',
            ];

            foreach ($groups as $i => $name) {
                DB::table('group_ledgers')->insert([
                    'name'       => $name,
                    'sort_order' => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
