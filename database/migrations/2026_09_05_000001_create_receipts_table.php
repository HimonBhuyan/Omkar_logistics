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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('series')->default('A');
            $table->unsignedInteger('receipt_no');
            $table->date('receipt_date');
            $table->string('receipt_time')->nullable(); // e.g. '13:22:05'
            $table->string('voucher_no')->nullable();
            
            // Party / Account details
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_name')->nullable();
            $table->string('mobile')->nullable();
            
            // Financial amounts
            $table->decimal('bill_amount', 15, 2)->default(0.00);
            $table->decimal('due_amount', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('tds_amount', 15, 2)->default(0.00);
            $table->decimal('receipt_amount', 15, 2)->default(0.00); // Net paid received
            $table->decimal('balance_amount', 15, 2)->default(0.00);
            
            // Payment details
            $table->string('pay_mode')->default('CASH'); // CASH, BANK, CHEQUE, NEFT/RTGS, UPI
            $table->unsignedBigInteger('bank_ledger_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            
            $table->text('remark')->nullable();
            $table->string('status')->default('final'); // draft, final, cancelled
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->timestamps();
            
            $table->unique(['series', 'receipt_no']);
        });

        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('receipts')->cascadeOnDelete();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->integer('sr_no')->default(1);
            $table->string('series')->default('A');
            $table->unsignedInteger('invoice_no');
            
            // Invoice snapshots and amounts
            $table->decimal('gross_amount', 15, 2)->default(0.00);
            $table->decimal('gst_amount', 15, 2)->default(0.00);
            $table->decimal('bill_amount', 15, 2)->default(0.00);
            $table->decimal('old_paid', 15, 2)->default(0.00);
            $table->decimal('due_amount', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('tds', 15, 2)->default(0.00);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->string('utr_no')->nullable();
            $table->boolean('is_full_pay')->default(false);
            $table->decimal('balance', 15, 2)->default(0.00);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_items');
        Schema::dropIfExists('receipts');
    }
};
