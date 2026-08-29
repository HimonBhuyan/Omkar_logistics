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
        Schema::table('bilties', function (Blueprint $table) {
            if (!Schema::hasColumn('bilties', 'status')) {
                $table->string('status')->default('final')->after('billing_type');
            }
            if (!Schema::hasColumn('bilties', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('voucher_no')->constrained('users')->nullOnDelete();
            }
            // Make fields nullable so drafts can be saved halfway
            $table->unsignedBigInteger('from_location_id')->nullable()->change();
            $table->unsignedBigInteger('to_location_id')->nullable()->change();
            $table->date('invoice_date')->nullable()->change();
            $table->string('billing_type')->nullable()->default('Paid')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bilties', function (Blueprint $table) {
            if (Schema::hasColumn('bilties', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('bilties', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
