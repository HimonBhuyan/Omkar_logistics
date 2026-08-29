<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearBillsSeeder extends Seeder
{
    /**
     * Run the database seeds to clear all bilties/bills and reset transaction numbers.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Truncate bilty items (individual rows in each bill)
        if (Schema::hasTable('bilty_items')) {
            DB::table('bilty_items')->truncate();
            $this->command->info('✓ Truncated table: bilty_items (all bill items removed)');
        }

        // 2. Truncate bilties (all bills / consignment notes)
        if (Schema::hasTable('bilties')) {
            DB::table('bilties')->truncate();
            $this->command->info('✓ Truncated table: bilties (all bills removed)');
        }

        // 3. Truncate sample parties table
        if (Schema::hasTable('parties')) {
            DB::table('parties')->truncate();
            $this->command->info('✓ Truncated table: parties (sample party records removed)');
        }

        Schema::enableForeignKeyConstraints();

        $this->command->info('------------------------------------------------------------');
        $this->command->info('SUCCESS: All seeded bills and test data have been wiped clean!');
        $this->command->info('Next bilty will start cleanly at C.N. 4197 and Voucher 1795.');
        $this->command->info('------------------------------------------------------------');
    }
}
