<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Transaction
            ['name' => 'transaction.cn_book', 'display_name' => 'C.N Book (Create & Manage Bilties)', 'module' => 'Transaction'],
            ['name' => 'transaction.receipt', 'display_name' => 'Receipt', 'module' => 'Transaction'],
            ['name' => 'transaction.payment', 'display_name' => 'Payment', 'module' => 'Transaction'],
            ['name' => 'transaction.party_bill', 'display_name' => 'Party Bill', 'module' => 'Transaction'],

            // Account
            ['name' => 'account.group', 'display_name' => 'Account Group', 'module' => 'Account'],
            ['name' => 'account.ledger', 'display_name' => 'Account Ledger', 'module' => 'Account'],
            ['name' => 'account.payment_expenses', 'display_name' => 'Payment & Expenses', 'module' => 'Account'],
            ['name' => 'account.voucher', 'display_name' => 'Voucher', 'module' => 'Account'],
            ['name' => 'account.deposit_bank', 'display_name' => 'Deposit in Bank', 'module' => 'Account'],
            ['name' => 'account.reports', 'display_name' => 'Account Reports', 'module' => 'Account'],

            // Report
            ['name' => 'report.bilty_register', 'display_name' => 'C.N Register Report', 'module' => 'Report'],
            ['name' => 'report.party_bill_register', 'display_name' => 'Party Bill Register', 'module' => 'Report'],
            ['name' => 'report.receipt_register', 'display_name' => 'Receipt Register', 'module' => 'Report'],
            ['name' => 'report.payment_register', 'display_name' => 'Payment Register', 'module' => 'Report'],
            ['name' => 'report.tds_report', 'display_name' => 'Receipt Detail / TDS Report', 'module' => 'Report'],

            // Master
            ['name' => 'master.item', 'display_name' => 'Item Master', 'module' => 'Master'],
            ['name' => 'master.measurement_unit', 'display_name' => 'Measurement Unit Master', 'module' => 'Master'],
            ['name' => 'master.series', 'display_name' => 'Series Master', 'module' => 'Master'],
            ['name' => 'master.transport', 'display_name' => 'Transport Master', 'module' => 'Master'],
            ['name' => 'master.country', 'display_name' => 'Country Master', 'module' => 'Master'],
            ['name' => 'master.state', 'display_name' => 'State Master', 'module' => 'Master'],
            ['name' => 'master.city', 'display_name' => 'City Master', 'module' => 'Master'],
            ['name' => 'master.currency', 'display_name' => 'Currency Master', 'module' => 'Master'],

            // Tools
            ['name' => 'tools.backup', 'display_name' => 'Backup Tools', 'module' => 'Tools'],
            ['name' => 'tools.restore', 'display_name' => 'Restore Tools', 'module' => 'Tools'],
            ['name' => 'tools.settings', 'display_name' => 'System Settings', 'module' => 'Tools'],

            // System
            ['name' => 'system.change_password', 'display_name' => 'Change Password', 'module' => 'System'],
            ['name' => 'system.user_management', 'display_name' => 'User Management', 'module' => 'System'],
            ['name' => 'system.role_management', 'display_name' => 'Role & Permission Management', 'module' => 'System'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        $allPermissionIds = Permission::pluck('id')->toArray();

        // 1. Seed ADMIN Role
        $adminRole = Role::firstOrCreate(
            ['name' => 'ADMIN'],
            ['description' => 'System Administrator with full unrestricted access', 'is_system' => true]
        );
        $adminRole->permissions()->sync($allPermissionIds);

        // 2. Seed MANAGER Role
        $managerRole = Role::firstOrCreate(
            ['name' => 'MANAGER'],
            ['description' => 'Branch Manager with full Transaction, Account, and Report access', 'is_system' => false]
        );
        $managerPermissionNames = [
            'transaction.cn_book', 'transaction.receipt', 'transaction.payment', 'transaction.party_bill',
            'account.group', 'account.ledger', 'account.payment_expenses', 'account.voucher', 'account.deposit_bank', 'account.reports',
            'report.bilty_register', 'report.party_bill_register', 'report.receipt_register', 'report.payment_register', 'report.tds_report',
            'master.item', 'master.measurement_unit', 'master.country', 'master.state', 'master.city',
            'system.change_password',
        ];
        $managerPermIds = Permission::whereIn('name', $managerPermissionNames)->pluck('id')->toArray();
        $managerRole->permissions()->sync($managerPermIds);

        // 3. Seed OPERATOR Role
        $operatorRole = Role::firstOrCreate(
            ['name' => 'OPERATOR'],
            ['description' => 'Booking Operator with C.N Book entry and C.N Register report access', 'is_system' => false]
        );
        $operatorPermissionNames = [
            'transaction.cn_book', 'report.bilty_register', 'master.measurement_unit', 'system.change_password'
        ];
        $operatorPermIds = Permission::whereIn('name', $operatorPermissionNames)->pluck('id')->toArray();
        $operatorRole->permissions()->sync($operatorPermIds);

        // 4. Seed ACCOUNTANT Role
        $accountantRole = Role::firstOrCreate(
            ['name' => 'ACCOUNTANT'],
            ['description' => 'Accounts Officer with Ledger, Voucher, and Financial Reports access', 'is_system' => false]
        );
        $accountantPermissionNames = [
            'account.group', 'account.ledger', 'account.payment_expenses', 'account.voucher', 'account.deposit_bank', 'account.reports',
            'report.bilty_register', 'report.party_bill_register', 'report.receipt_register', 'report.payment_register', 'report.tds_report',
            'system.change_password'
        ];
        $accountantPermIds = Permission::whereIn('name', $accountantPermissionNames)->pluck('id')->toArray();
        $accountantRole->permissions()->sync($accountantPermIds);

        // 5. Attach ADMIN role to default admin user
        $adminUser = User::where(DB::raw('UPPER(username)'), 'ADMIN')->first();
        if ($adminUser) {
            $adminUser->update(['role_id' => $adminRole->id, 'is_active' => true]);
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
