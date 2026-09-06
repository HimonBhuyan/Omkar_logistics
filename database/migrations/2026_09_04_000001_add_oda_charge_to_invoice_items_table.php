<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('invoice_items') && !Schema::hasColumn('invoice_items', 'oda_charge')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->decimal('oda_charge', 12, 2)->default(0.00)->after('other_charges');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoice_items') && Schema::hasColumn('invoice_items', 'oda_charge')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn('oda_charge');
            });
        }
    }
};
