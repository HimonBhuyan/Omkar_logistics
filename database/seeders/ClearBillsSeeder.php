<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\FinancialYear;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ClearBillsSeeder extends Seeder
{
    /**
     * Run the database seeds to clear all data except:
     * - companies
     * - financial_years
     * - migrations
     * - users (admin only)
     *
     * Truncates all transactional, ledger, locations, master data, and temporary tables.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Tables to truncate (wiping transactional, ledger, locations, and temporary data)
        $tablesToTruncate = [
            'bilty_items',
            'bilties',
            'parties',
            'account_ledgers',
            'locations',
            'cities',
            'countries',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'password_reset_tokens',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("✓ Truncated table: {$table}");
            }
        }

        // 1. Maintain & seed Company profile
        if (Schema::hasTable('companies')) {
            Company::firstOrCreate(
                ['name' => 'OMKAAR LOGISTICS'],
                ['logo_path' => 'assets/logo.jpg']
            );
            $this->command->info('✓ Seeded/Retained company: OMKAAR LOGISTICS');
        }

        // 2. Maintain & seed Financial Years
        if (Schema::hasTable('financial_years')) {
            FinancialYear::firstOrCreate(
                ['year_string' => '2026-2027'],
                ['is_active' => true]
            );
            FinancialYear::firstOrCreate(
                ['year_string' => '2025-2026'],
                ['is_active' => false]
            );
            $this->command->info('✓ Seeded/Retained financial years (2026-2027 active)');
        }

        // 3. Maintain & seed Users (Admin only)
        if (Schema::hasTable('users')) {
            DB::table('users')->where('username', '!=', 'admin')->delete();

            $admin = DB::table('users')->where('username', 'admin')->first();
            if (!$admin) {
                DB::table('users')->insert([
                    'name'       => 'Administrator',
                    'username'   => 'admin',
                    'email'      => 'admin@omkaarlogistics.com',
                    'password'   => Hash::make('admin'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info('✓ Seeded default admin user (username: admin / password: admin)');
            } else {
                $this->command->info('✓ Retained existing admin user');
            }
        }

        // 4. Maintain & seed Group Ledgers
        $this->call(GroupLedgerSeeder::class);
        $this->command->info('✓ Retained/Seeded group_ledgers table');

        // 5. Maintain & seed States and Countries
        $this->call(MasterSeeder::class);
        $this->command->info('✓ Retained/Seeded states and countries tables');

        Schema::enableForeignKeyConstraints();

        $this->command->info('----------------------------------------------------------------------');
        $this->command->info('SUCCESS: Clean system initialized!');
        $this->command->info('Kept tables: companies, financial_years, migrations, users, states, group_ledgers.');
        $this->command->info('----------------------------------------------------------------------');
    }
}

