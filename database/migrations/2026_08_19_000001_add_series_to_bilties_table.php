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
        // Add series column to bilties if not exists
        if (!Schema::hasColumn('bilties', 'series')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->string('series', 10)->default('26-27')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bilties', 'series')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->dropColumn('series');
            });
        }
    }
};
