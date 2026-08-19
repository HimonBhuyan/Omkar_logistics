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
        Schema::create('bilties', function (Blueprint $table) {
            $table->id();
            
            // Header information
            $table->string('series')->default('A');
            $table->unsignedInteger('bilty_no');
            $table->date('invoice_date');
            
            // Locations (references locations)
            $table->foreignId('from_location_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('to_location_id')->constrained('locations')->cascadeOnDelete();
            
            // Parties (references parties)
            $table->foreignId('consignor_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('consignee_id')->constrained('parties')->cascadeOnDelete();
            
            // Billing options
            $table->string('billing_type')->default('Paid'); // Paid, To Pay, T.B.B.
            $table->foreignId('billing_party_id')->nullable()->constrained('parties')->nullOnDelete();
            
            // Consignment and Transport details
            $table->string('cn_no')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('eway_bill_no')->nullable();
            
            // Charges and Totals
            $table->integer('total_packages')->default(0);
            $table->decimal('total_qty', 12, 3)->default(0.000);
            $table->decimal('gross_amount', 15, 2)->default(0.00);
            
            // Surcharges & service taxes
            $table->decimal('st_charge', 12, 2)->default(0.00); // ST
            $table->decimal('rc_charge', 12, 2)->default(0.00); // RC
            $table->decimal('sc_charge', 12, 2)->default(0.00); // SC
            $table->decimal('dd_charge', 12, 2)->default(0.00); // DD
            $table->decimal('round_off', 10, 2)->default(0.00);
            $table->decimal('net_amount', 15, 2)->default(0.00);
            
            // Payment settlement
            $table->decimal('cash_amount', 15, 2)->default(0.00);
            $table->decimal('card_amount', 15, 2)->default(0.00);
            $table->decimal('upi_chq_amount', 15, 2)->default(0.00);
            $table->string('ref_no')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('bank_account')->nullable();
            $table->decimal('balance_amount', 15, 2)->default(0.00);
            
            // Metadata & System audit
            $table->text('remark')->nullable();
            $table->unsignedInteger('voucher_no')->nullable();
            
            $table->timestamps();

            // Prevent duplicate bilty numbers within the same series
            $table->unique(['series', 'bilty_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilties');
    }
};
