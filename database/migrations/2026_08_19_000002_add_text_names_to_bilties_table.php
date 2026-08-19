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
        // Add text name columns to bilties to persist imported strings so deletions of extra ledgers don't delete text
        Schema::table('bilties', function (Blueprint $table) {
            if (!Schema::hasColumn('bilties', 'consignor_name')) {
                $table->string('consignor_name', 150)->nullable()->after('consignor_id');
            }
            if (!Schema::hasColumn('bilties', 'consignee_name')) {
                $table->string('consignee_name', 150)->nullable()->after('consignee_id');
            }
            if (!Schema::hasColumn('bilties', 'billing_party_name')) {
                $table->string('billing_party_name', 150)->nullable()->after('billing_party_id');
            }
            if (!Schema::hasColumn('bilties', 'consignor_mobile')) {
                $table->string('consignor_mobile', 50)->nullable()->after('consignor_name');
            }
            if (!Schema::hasColumn('bilties', 'consignee_mobile')) {
                $table->string('consignee_mobile', 50)->nullable()->after('consignee_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilties', function (Blueprint $table) {
            $table->dropColumn(['consignor_name', 'consignee_name', 'billing_party_name', 'consignor_mobile', 'consignee_mobile']);
        });
    }
};
