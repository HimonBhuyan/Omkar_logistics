<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('account_ledgers', 'state_code')) {
                $table->string('state_code', 10)->nullable()->after('code');
            }
            if (!Schema::hasColumn('account_ledgers', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('address')->constrained('countries')->nullOnDelete();
            }
            if (!Schema::hasColumn('account_ledgers', 'state_id')) {
                $table->foreignId('state_id')->nullable()->after('country_id')->constrained('states')->nullOnDelete();
            }
            if (!Schema::hasColumn('account_ledgers', 'city_id')) {
                $table->foreignId('city_id')->nullable()->after('state_id')->constrained('cities')->nullOnDelete();
            }
            if (Schema::hasColumn('account_ledgers', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('account_ledgers', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('account_ledgers', 'city')) {
                $table->dropColumn('city');
            }
        });
    }

    public function down(): void
    {
        Schema::table('account_ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('account_ledgers', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('account_ledgers', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('account_ledgers', 'country')) {
                $table->string('country')->nullable()->after('state');
            }
            if (Schema::hasColumn('account_ledgers', 'city_id')) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            }
            if (Schema::hasColumn('account_ledgers', 'state_id')) {
                $table->dropForeign(['state_id']);
                $table->dropColumn('state_id');
            }
            if (Schema::hasColumn('account_ledgers', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }
            if (Schema::hasColumn('account_ledgers', 'state_code')) {
                $table->dropColumn('state_code');
            }
        });
    }
};
