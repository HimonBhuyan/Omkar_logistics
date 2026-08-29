<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\FinancialYear;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with clean master data for production/new setup.
     * No test bills or dummy parties are seeded.
     */
    public function run(): void
    {
        // 1. Wipe out any old bills, bill items, and sample parties
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('bilty_items')) {
            DB::table('bilty_items')->truncate();
        }
        if (Schema::hasTable('bilties')) {
            DB::table('bilties')->truncate();
        }
        if (Schema::hasTable('parties')) {
            DB::table('parties')->truncate();
        }
        Schema::enableForeignKeyConstraints();

        // 2. Seed company profile
        Company::firstOrCreate(
            ['name' => 'OMKAAR LOGISTICS'],
            ['logo_path' => 'assets/logo.jpg']
        );

        // 3. Seed active financial year
        FinancialYear::firstOrCreate(
            ['year_string' => '2026-2027'],
            ['is_active' => true]
        );
        FinancialYear::firstOrCreate(
            ['year_string' => '2025-2026'],
            ['is_active' => false]
        );

        // 4. Seed default admin user
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@omkaarlogistics.com',
                'password' => bcrypt('admin')
            ]
        );

        // 5. Seed default transport hub locations
        $locations = [
            'Mumbai', 'Delhi', 'Kolkata', 'Chennai', 'Bangalore', 
            'Pune', 'Ahmedabad', 'Howrah', 'Guwahati', 'Patna'
        ];
        foreach ($locations as $loc) {
            Location::firstOrCreate(['name' => $loc]);
        }

        // 6. Seed Group Ledgers (Debtors, Creditors, Bank Accounts, Expenses)
        $this->call(GroupLedgerSeeder::class);

        // 7. Seed General Masters (Countries, States, Cities)
        $this->call(MasterSeeder::class);
    }
}
