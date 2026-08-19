<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('countries')->truncate();
        DB::table('countries')->insert([
            ['name' => 'INDIA', 'code' => 'IN', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('states')->truncate();
        $states = [
            ['name' => 'ARUNACHAL PRADESH', 'code' => '12', 'short_name' => 'AR', 'country' => 'INDIA'],
            ['name' => 'Assam', 'code' => '18', 'short_name' => 'AS', 'country' => 'INDIA'],
            ['name' => 'KOLKATA', 'code' => '19', 'short_name' => 'WB', 'country' => 'INDIA'],
            ['name' => 'MANIPUR', 'code' => '14', 'short_name' => 'MN', 'country' => 'INDIA'],
            ['name' => 'MIZORAM', 'code' => '15', 'short_name' => 'MZ', 'country' => 'INDIA'],
            ['name' => 'NAGALAND', 'code' => '13', 'short_name' => 'NL', 'country' => 'INDIA'],
            ['name' => 'SHILLONG', 'code' => '17', 'short_name' => 'ML', 'country' => 'INDIA'],
            ['name' => 'TRIPURA', 'code' => '16', 'short_name' => 'TR', 'country' => 'INDIA'],
            ['name' => 'WEST BENGAL', 'code' => '19', 'short_name' => 'WB', 'country' => 'INDIA'],
        ];
        foreach($states as $s) {
            DB::table('states')->insert(array_merge($s, ['created_at' => now(), 'updated_at' => now()]));
        }

        DB::table('cities')->truncate();
        $cities = [
            ['name' => 'Kalani Jalah Balika Maqtab', 'short_name' => 'KJ', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'Maharashtra', 'short_name' => 'MH', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => '13TH MILE', 'short_name' => '13M', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'A.T. ROAD', 'short_name' => 'AT', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'ACHINTALA', 'short_name' => 'AC', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'ADAGUDAM', 'short_name' => 'AD', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AGARTALA', 'short_name' => 'AG', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AIZAWL', 'short_name' => 'AZ', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'ALL NORTH EAST', 'short_name' => 'NE', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AMBAGAN', 'short_name' => 'AM', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AMBARI', 'short_name' => 'AB', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AMGURI', 'short_name' => 'AM', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AMINGAON', 'short_name' => 'AM', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AMINPARA', 'short_name' => 'AP', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AMJULI', 'short_name' => 'AJ', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'ATHGAON', 'short_name' => 'AT', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'AZARA', 'short_name' => 'AZ', 'state' => 'ARUNACHAL PRADESH'],
            ['name' => 'B.BARUAH ROAD', 'short_name' => 'BB', 'state' => 'ARUNACHAL PRADESH'],
        ];
        foreach($cities as $c) {
            DB::table('cities')->insert(array_merge($c, ['created_at' => now(), 'updated_at' => now()]));
        }
    }
}
