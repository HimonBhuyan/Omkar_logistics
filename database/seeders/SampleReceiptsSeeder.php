<?php

namespace Database\Seeders;

use App\Models\AccountLedger;
use App\Models\Bilty;
use App\Models\BiltyItem;
use App\Models\CityModel;
use App\Models\Country;
use App\Models\Location;
use App\Models\Party;
use App\Models\StateModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class SampleReceiptsSeeder extends Seeder
{
    /**
     * Seed 2 sample receipts:
     * 1. Single row consignment note
     * 2. Two row consignment note
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@omkaarlogistics.com',
                'password' => bcrypt('admin')
            ]
        );

        $fromLoc = Location::firstOrCreate(['name' => 'Guwahati']);
        $toLoc = Location::firstOrCreate(['name' => 'Kolkata']);
        $delhiLoc = Location::firstOrCreate(['name' => 'Delhi']);

        // Resolve IDs for seeding
        $indiaCountry = Country::where('name', 'INDIA')->first();
        $assamState = StateModel::where('name', 'ASSAM')->first();
        $guwahatiCity = CityModel::where('name', 'GUWAHATI')->first();
        $wbState = StateModel::where('name', 'WEST BENGAL')->first();
        $kolkataCity = CityModel::where('name', 'KOLKATA')->first();
        $delhiState = StateModel::where('name', 'DELHI')->first();
        $delhiCity = CityModel::where('name', 'DELHI')->orWhere('name', 'NEW DELHI')->first();

        // Create sample account ledgers
        $consignor1 = AccountLedger::firstOrCreate(
            ['code' => 1001],
            [
                'state_code'     => '18',
                'ledger_name'    => 'SHREE SHYAM ENTERPRISES',
                'under_group'    => 'Debtors',
                'contact_person' => 'Rajesh Sharma',
                'address'        => 'Fancy Bazar, S.R.C.B. Road, Guwahati',
                'country_id'     => $indiaCountry->id ?? 1,
                'state_id'       => $assamState->id ?? null,
                'city_id'        => $guwahatiCity->id ?? null,
                'mobile'         => '9864012345',
                'phone_o'        => '0361-2541234',
                'gst_no'         => '18AABCU9603R1ZM',
            ]
        );

        $consignee1 = AccountLedger::firstOrCreate(
            ['code' => 1002],
            [
                'state_code'     => '19',
                'ledger_name'    => 'NORTH EAST TRADING CO.',
                'under_group'    => 'Creditors',
                'contact_person' => 'Amit Banerjee',
                'address'        => 'Burrabazar, 12 Cotton Street, Kolkata',
                'country_id'     => $indiaCountry->id ?? 1,
                'state_id'       => $wbState->id ?? null,
                'city_id'        => $kolkataCity->id ?? null,
                'mobile'         => '9830198765',
                'phone_o'        => '033-22448899',
                'gst_no'         => '19AABCN5544K1ZV',
            ]
        );

        $consignee2 = AccountLedger::firstOrCreate(
            ['code' => 1003],
            [
                'state_code'     => '07',
                'ledger_name'    => 'HINDUSTAN HARDWARE & TEXTILE MART',
                'under_group'    => 'Creditors',
                'contact_person' => 'Vikram Gupta',
                'address'        => 'Chandni Chowk, Main Market, Delhi',
                'country_id'     => $indiaCountry->id ?? 1,
                'state_id'       => $delhiState->id ?? null,
                'city_id'        => $delhiCity->id ?? null,
                'mobile'         => '9811054321',
                'phone_o'        => '011-23998877',
                'gst_no'         => '07AAACH1234L1Z9',
            ]
        );

        // Also ensure parties table has corresponding records if old schema constraints apply
        Party::firstOrCreate(['id' => $consignor1->id], ['name' => $consignor1->ledger_name, 'mobile' => $consignor1->mobile, 'address' => $consignor1->address, 'gstin' => $consignor1->gst_no]);
        Party::firstOrCreate(['id' => $consignee1->id], ['name' => $consignee1->ledger_name, 'mobile' => $consignee1->mobile, 'address' => $consignee1->address, 'gstin' => $consignee1->gst_no]);
        Party::firstOrCreate(['id' => $consignee2->id], ['name' => $consignee2->ledger_name, 'mobile' => $consignee2->mobile, 'address' => $consignee2->address, 'gstin' => $consignee2->gst_no]);

        // Delete any existing sample bilties with bilty_no 4306 and 4307
        $existing = Bilty::whereIn('bilty_no', [4306, 4307])->pluck('id');
        if ($existing->isNotEmpty()) {
            BiltyItem::whereIn('bilty_id', $existing)->delete();
            Bilty::whereIn('id', $existing)->delete();
        }

        // ==========================================
        // SAMPLE 1: Single Row Data Receipt (#4306)
        // ==========================================
        $bilty1 = Bilty::create([
            'series'             => 'A',
            'bilty_no'           => 4306,
            'invoice_date'       => Carbon::now()->toDateString(),
            'from_location_id'   => $fromLoc->id,
            'to_location_id'     => $toLoc->id,
            'consignor_id'       => $consignor1->id,
            'consignor_name'     => $consignor1->ledger_name,
            'consignor_mobile'   => $consignor1->mobile,
            'consignee_id'       => $consignee1->id,
            'consignee_name'     => $consignee1->ledger_name,
            'consignee_mobile'   => $consignee1->mobile,
            'billing_type'       => 'TO PAY',
            'type'               => 'L',
            'billing_party_id'   => $consignor1->id,
            'billing_party_name' => $consignor1->ledger_name,
            'cn_no'              => 'CN-4306',
            'vehicle_no'         => 'AS-01-EC-4412',
            'eway_bill_no'       => '341029485712',
            'total_packages'     => 10,
            'total_qty'          => 120.500,
            'gross_amount'       => 1446.00,
            'st_charge'          => 50.00,
            'rc_charge'          => 30.00,
            'sc_charge'          => 20.00,
            'dd_charge'          => 0.00,
            'round_off'          => 4.00,
            'net_amount'         => 1550.00,
            'voucher_no'         => 1795,
            'status'             => 'active',
            'user_id'            => $admin->id,
        ]);

        BiltyItem::create([
            'bilty_id'      => $bilty1->id,
            'no_of_pkgs'    => 10,
            'packing'       => 'BOX',
            'description'   => 'Electronic Components & Sensor Units',
            'invoice_no'    => 'INV-2026/081',
            'invoice_value' => 45000.00,
            'weight_type'   => 'KG',
            'qty'           => 120.500,
            'rate'          => 12.00,
            'weight_val'    => 120.500,
            'st'            => 50.00,
            'rc'            => 30.00,
            'sc'            => 20.00,
            'dd'            => 0.00,
        ]);

        $this->command->info("✓ Sample 1 (Single Row) Created: ID={$bilty1->id}, C.N.={$bilty1->series}-{$bilty1->bilty_no}");

        // ==========================================
        // SAMPLE 2: Two Row Data Receipt (#4307)
        // ==========================================
        $bilty2 = Bilty::create([
            'series'             => 'A',
            'bilty_no'           => 4307,
            'invoice_date'       => Carbon::now()->toDateString(),
            'from_location_id'   => $fromLoc->id,
            'to_location_id'     => $delhiLoc->id,
            'consignor_id'       => $consignor1->id,
            'consignor_name'     => $consignor1->ledger_name,
            'consignor_mobile'   => $consignor1->mobile,
            'consignee_id'       => $consignee2->id,
            'consignee_name'     => $consignee2->ledger_name,
            'consignee_mobile'   => $consignee2->mobile,
            'billing_type'       => 'PAID',
            'type'               => 'L',
            'billing_party_id'   => $consignor1->id,
            'billing_party_name' => $consignor1->ledger_name,
            'cn_no'              => 'CN-4307',
            'vehicle_no'         => 'AS-25-CC-8901',
            'eway_bill_no'       => '581920448102',
            'total_packages'     => 40,
            'total_qty'          => 530.000,
            'gross_amount'       => 7750.00,
            'st_charge'          => 80.00,
            'rc_charge'          => 40.00,
            'sc_charge'          => 30.00,
            'dd_charge'          => 0.00,
            'round_off'          => 0.00,
            'net_amount'         => 7900.00,
            'cash_amount'        => 7900.00,
            'voucher_no'         => 1796,
            'status'             => 'active',
            'user_id'            => $admin->id,
        ]);

        // Item 1
        BiltyItem::create([
            'bilty_id'      => $bilty2->id,
            'no_of_pkgs'    => 25,
            'packing'       => 'BAGS',
            'description'   => 'Cotton Yarn & Textile Fabrics',
            'invoice_no'    => 'TX-4401',
            'invoice_value' => 75000.00,
            'weight_type'   => 'KG',
            'qty'           => 350.000,
            'rate'          => 15.00,
            'weight_val'    => 350.000,
        ]);

        // Item 2
        BiltyItem::create([
            'bilty_id'      => $bilty2->id,
            'no_of_pkgs'    => 15,
            'packing'       => 'CARTONS',
            'description'   => 'Industrial Hardware & Brass Fasteners',
            'invoice_no'    => 'HW-4402',
            'invoice_value' => 38500.00,
            'weight_type'   => 'KG',
            'qty'           => 180.000,
            'rate'          => 14.00,
            'weight_val'    => 180.000,
        ]);

        $this->command->info("✓ Sample 2 (Two Rows) Created: ID={$bilty2->id}, C.N.={$bilty2->series}-{$bilty2->bilty_no}");

        Schema::enableForeignKeyConstraints();
    }
}
