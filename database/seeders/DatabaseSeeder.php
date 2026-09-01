<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\FinancialYear;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with clean master data for production/new setup.
     * Retains ONLY: companies, financial_years, migrations, and users (admin).
     * All other tables are truncated clean.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $tablesToTruncate = [
            'bilty_items',
            'bilties',
            'parties',
            'account_ledgers',
            'group_ledgers',
            'locations',
            'cities',
            'states',
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
            }
        }

        // 1. Seed company profile
        if (Schema::hasTable('companies')) {
            Company::firstOrCreate(
                ['name' => 'OMKAAR LOGISTICS'],
                ['logo_path' => 'assets/logo.jpg']
            );
        }

        // 2. Seed financial years
        if (Schema::hasTable('financial_years')) {
            FinancialYear::firstOrCreate(
                ['year_string' => '2026-2027'],
                ['is_active' => true]
            );
            FinancialYear::firstOrCreate(
                ['year_string' => '2025-2026'],
                ['is_active' => false]
            );
        }

        // 3. Seed default admin user
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
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}

