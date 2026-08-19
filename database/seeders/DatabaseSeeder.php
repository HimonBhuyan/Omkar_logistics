<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed default company
        \App\Models\Company::create([
            'name' => 'OMKAAR LOGISTICS',
            'logo_path' => 'assets/logo.jpg'
        ]);

        // Seed financial years
        \App\Models\FinancialYear::create([
            'year_string' => '2026-2027',
            'is_active' => true
        ]);
        \App\Models\FinancialYear::create([
            'year_string' => '2025-2026',
            'is_active' => false
        ]);

        // Seed admin user
        \App\Models\User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@omkaarlogistics.com',
            'password' => bcrypt('admin')
        ]);

        // Seed locations
        $locations = [
            'Mumbai', 'Delhi', 'Kolkata', 'Chennai', 'Bangalore', 
            'Pune', 'Ahmedabad', 'Howrah', 'Guwahati', 'Patna'
        ];
        foreach ($locations as $loc) {
            \App\Models\Location::create(['name' => $loc]);
        }

        // Seed sample parties (consignors, consignees, billing parties)
        \App\Models\Party::create([
            'name' => 'Tata Motors Ltd',
            'mobile' => '9876543210',
            'address' => 'Sector 4, MIDC Industrial Area, Pune, MH',
            'gstin' => '27AAAAA1111A1Z1',
            'type' => 'consignor'
        ]);

        \App\Models\Party::create([
            'name' => 'Reliance Industries Ltd',
            'mobile' => '9988776655',
            'address' => 'Reliance Corporate Park, Ghansoli, Navi Mumbai, MH',
            'gstin' => '27BBBBB2222B2Z2',
            'type' => 'consignor'
        ]);

        \App\Models\Party::create([
            'name' => 'Kishore Goods Carrier',
            'mobile' => '8877665544',
            'address' => 'Phase 2, Transport Nagar, New Delhi, DL',
            'gstin' => '07DDDDD4444D4Z4',
            'type' => 'consignee'
        ]);

        \App\Models\Party::create([
            'name' => 'Mahesh Logistics Service',
            'mobile' => '7766554433',
            'address' => 'Block C, Okhla Phase 3, New Delhi, DL',
            'gstin' => '07EEEEE5555E5Z5',
            'type' => 'consignee'
        ]);

        \App\Models\Party::create([
            'name' => 'Shree Transport Corp',
            'mobile' => '9123456789',
            'address' => '45, Strand Road, Transport Plaza, Kolkata, WB',
            'gstin' => '19CCCCC3333C3Z3',
            'type' => 'billing_party'
        ]);

        \App\Models\Party::create([
            'name' => 'General Transport Co (Walk-in)',
            'mobile' => '9000000000',
            'address' => 'Walk-in Party Address',
            'gstin' => 'URP-GST-NOT-REQ',
            'type' => 'both'
        ]);
    }
}
