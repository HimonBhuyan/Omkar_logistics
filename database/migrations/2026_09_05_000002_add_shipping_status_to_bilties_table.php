<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bilties', function (Blueprint $table) {
            $table->string('shipping_status', 50)->default('Booked')->after('vehicle_no');
        });

        // Backfill existing data: if vehicle_no exists, set shipping_status to 'Shipped', else 'Booked'
        DB::statement("UPDATE bilties SET shipping_status = 'Shipped' WHERE vehicle_no IS NOT NULL AND TRIM(vehicle_no) != ''");
        DB::statement("UPDATE bilties SET shipping_status = 'Booked' WHERE vehicle_no IS NULL OR TRIM(vehicle_no) = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilties', function (Blueprint $table) {
            $table->dropColumn('shipping_status');
        });
    }
};
