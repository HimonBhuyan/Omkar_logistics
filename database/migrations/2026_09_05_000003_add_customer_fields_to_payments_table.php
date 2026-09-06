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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('customer_invoice_no')->nullable()->after('account_alias');
            $table->decimal('customer_bill_amt', 15, 2)->default(0.00)->after('customer_invoice_no');
            $table->decimal('deduct_amount', 15, 2)->default(0.00)->after('customer_bill_amt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['customer_invoice_no', 'customer_bill_amt', 'deduct_amount']);
        });
    }
};
