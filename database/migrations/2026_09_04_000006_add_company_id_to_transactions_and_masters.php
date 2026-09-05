<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bilties
        if (!Schema::hasColumn('bilties', 'company_id')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });

            // Update unique constraint to include company_id
            try {
                Schema::table('bilties', function (Blueprint $table) {
                    $table->dropUnique(['series', 'bilty_no']);
                });
            } catch (\Throwable $e) {}

            Schema::table('bilties', function (Blueprint $table) {
                $table->unique(['company_id', 'series', 'bilty_no']);
            });
        }

        // 2. Parties
        if (!Schema::hasColumn('parties', 'company_id')) {
            Schema::table('parties', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });
        }

        // 3. Locations
        if (!Schema::hasColumn('locations', 'company_id')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });
        }

        // 4. Account Ledgers
        if (!Schema::hasColumn('account_ledgers', 'company_id')) {
            Schema::table('account_ledgers', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });
        }

        // 5. Group Ledgers
        if (!Schema::hasColumn('group_ledgers', 'company_id')) {
            Schema::table('group_ledgers', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });
        }

        // 6. Measurement Units
        if (!Schema::hasColumn('measurement_units', 'company_id')) {
            Schema::table('measurement_units', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });

            try {
                Schema::table('measurement_units', function (Blueprint $table) {
                    $table->dropUnique(['unit_code']);
                });
            } catch (\Throwable $e) {}

            Schema::table('measurement_units', function (Blueprint $table) {
                $table->unique(['company_id', 'unit_code']);
            });
        }

        // 7. Cities
        if (Schema::hasTable('city_models') && !Schema::hasColumn('city_models', 'company_id')) {
            Schema::table('city_models', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });
        }

        // Backfill existing transactional records with default company_id = 1
        $defaultCompany = DB::table('companies')->first();
        $defaultCompanyId = $defaultCompany ? $defaultCompany->id : 1;

        DB::table('bilties')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
        DB::table('parties')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
        DB::table('locations')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bilties', 'company_id')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
        if (Schema::hasColumn('parties', 'company_id')) {
            Schema::table('parties', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
        if (Schema::hasColumn('locations', 'company_id')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
        if (Schema::hasColumn('account_ledgers', 'company_id')) {
            Schema::table('account_ledgers', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
        if (Schema::hasColumn('group_ledgers', 'company_id')) {
            Schema::table('group_ledgers', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
        if (Schema::hasColumn('measurement_units', 'company_id')) {
            Schema::table('measurement_units', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
        if (Schema::hasTable('city_models') && Schema::hasColumn('city_models', 'company_id')) {
            Schema::table('city_models', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};
