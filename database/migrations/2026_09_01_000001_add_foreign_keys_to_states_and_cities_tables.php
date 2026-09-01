<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('states', function (Blueprint $table) {
            if (!Schema::hasColumn('states', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained('countries')->nullOnDelete();
            }
            if (Schema::hasColumn('states', 'country')) {
                $table->dropColumn('country');
            }
        });

        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'state_id')) {
                $table->foreignId('state_id')->nullable()->after('id')->constrained('states')->nullOnDelete();
            }
            if (Schema::hasColumn('cities', 'state')) {
                $table->dropColumn('state');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'state')) {
                $table->string('state')->nullable()->after('short_name');
            }
            if (Schema::hasColumn('cities', 'state_id')) {
                $table->dropForeign(['state_id']);
                $table->dropColumn('state_id');
            }
        });

        Schema::table('states', function (Blueprint $table) {
            if (!Schema::hasColumn('states', 'country')) {
                $table->string('country')->nullable()->default('INDIA')->after('short_name');
            }
            if (Schema::hasColumn('states', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
        });
    }
};
