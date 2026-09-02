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
            if (Schema::hasColumn('bilty_items', 'weight_type')) {
                $table->renameColumn('weight_type', 'unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilty_items', function (Blueprint $table) {
            if (Schema::hasColumn('bilty_items', 'unit')) {
                $table->renameColumn('unit', 'weight_type');
            }
        });
    }
};
