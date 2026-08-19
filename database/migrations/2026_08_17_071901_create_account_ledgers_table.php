<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('code')->unique();
            $table->string('ledger_name')->nullable();
            $table->string('under_group')->nullable();
            $table->string('contact_person')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable()->default('Assam');
            $table->string('country')->nullable()->default('INDIA');
            $table->string('pin_code')->nullable();
            $table->string('phone_o')->nullable();
            $table->string('phone_r')->nullable();
            $table->decimal('points', 10, 2)->nullable()->default(0);
            $table->decimal('credit_limit', 15, 2)->nullable()->default(0);
            $table->integer('limit_days')->nullable()->default(0);
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('salesman')->nullable();
            $table->integer('print_copy')->nullable()->default(1);
            $table->string('web')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('di_no')->nullable();
            $table->string('transport')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('ifsc')->nullable();
            $table->decimal('opening', 15, 2)->nullable()->default(0);
            $table->date('dom')->nullable();
            $table->decimal('margin', 5, 2)->nullable()->default(0);
            $table->date('dob')->nullable();
            $table->decimal('discnt', 5, 2)->nullable()->default(0);
            $table->enum('payment_type', ['cash', 'credit'])->default('cash');
            $table->enum('customer_type', ['retailer', 'wholesaler'])->default('retailer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_ledgers');
    }
};
