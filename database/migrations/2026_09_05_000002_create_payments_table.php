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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('series')->default('A');
            $table->unsignedInteger('payment_no');
            $table->date('payment_date');
            $table->string('payment_time')->nullable(); // e.g. '22:14:39'
            $table->string('voucher_no')->nullable();
            
            // Account / Supplier details
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_alias')->nullable();
            
            // Financials
            $table->decimal('payment_amount', 15, 2)->default(0.00); // Gross / base payment amount
            $table->decimal('due_amount', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            
            // Tax
            $table->string('tax_type')->nullable(); // GST, TDS, etc.
            $table->decimal('tax_percent', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            
            // Net Total Paid
            $table->decimal('total_amount', 15, 2)->default(0.00);
            
            // Payment Mode & Bank
            $table->string('pay_mode')->default('BANK TRANSFER'); // BANK TRANSFER, CASH, CHEQUE, NEFT/RTGS, UPI, DIRECT
            $table->unsignedBigInteger('bank_ledger_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('cheque_no')->nullable(); // Cheque or UTR or Reference number
            $table->date('cheque_date')->nullable();
            
            // Remark, Status & Audit
            $table->text('remark')->nullable();
            $table->string('status')->default('final'); // draft, final, cancelled
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->timestamps();
            
            $table->unique(['series', 'payment_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
