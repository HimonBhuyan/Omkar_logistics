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
        // Adding type column to bilties if not exists
        if (!Schema::hasColumn('bilties', 'type')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->string('type', 20)->nullable()->after('billing_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bilties', 'type')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
