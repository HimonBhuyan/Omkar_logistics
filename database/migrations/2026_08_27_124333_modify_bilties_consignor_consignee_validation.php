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
        // Drop constraints using individual schema alteration statements wrapped in try-catch
        try {
            Schema::table('bilties', function (Blueprint $table) {
                $table->dropForeign('bilties_consignor_id_foreign');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('bilties', function (Blueprint $table) {
                $table->dropForeign('bilties_consignee_id_foreign');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('bilties', function (Blueprint $table) {
                $table->dropForeign('bilties_billing_party_id_foreign');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilties', function (Blueprint $table) {
            // Restore original constraints pointing back to parties
            $table->foreign('consignor_id')->references('id')->on('parties')->cascadeOnDelete()->change();
            $table->foreign('consignee_id')->references('id')->on('parties')->cascadeOnDelete()->change();
            $table->foreign('billing_party_id')->references('id')->on('parties')->nullOnDelete()->change();
        });
    }
};
