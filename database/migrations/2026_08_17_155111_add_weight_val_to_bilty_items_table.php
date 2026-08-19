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
        Schema::table('bilty_items', function (Blueprint $table) {
            $table->decimal('weight_val', 12, 3)->default(0.000)->after('weight_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilty_items', function (Blueprint $table) {
            $table->dropColumn('weight_val');
        });
    }
};
