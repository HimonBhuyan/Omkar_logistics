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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('series')->default('A');
            $table->unsignedInteger('invoice_no');
            $table->date('invoice_date');
            
            // Party / Account details
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_name')->nullable();
            $table->unsignedBigInteger('consignor_id')->nullable();
            $table->string('consignor_name')->nullable();
            
            // Filters and parameters
            $table->string('for_month')->nullable(); // e.g. 'Sep/2026'
            $table->string('item_filter')->nullable();
            $table->boolean('is_gst_bill')->default(false);
            $table->boolean('is_igst')->default(false);
            $table->string('destination_filter')->nullable();
            
            // Financial amounts
            $table->decimal('bill_amount', 15, 2)->default(0.00);
            $table->decimal('gst_percent', 5, 2)->default(0.00);
            $table->decimal('gst_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            
            $table->text('remark')->nullable();
            $table->string('status')->default('final');
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->timestamps();
            
            $table->unique(['series', 'invoice_no']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->unsignedBigInteger('bilty_id')->nullable();
            $table->integer('sr_no')->default(1);
            $table->date('bilty_date')->nullable();
            $table->unsignedInteger('bilty_no')->nullable();
            $table->string('cn_no')->nullable(); // KRATOS CN NO
            $table->integer('packages')->default(0); // Pkt
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable(); // Destination
            $table->string('consignee_name')->nullable();
            $table->string('item_description')->nullable(); // Items
            $table->string('invoice_no_ref')->nullable(); // Inv.No
            $table->decimal('weight', 12, 3)->default(0.000); // Wgt
            $table->string('weight_type')->default('KG'); // FIXED/KG
            $table->decimal('rate', 12, 2)->default(0.00); // RT/KG/CB
            $table->decimal('st_charge', 12, 2)->default(0.00); // St.Ch.
            $table->decimal('freight_amount', 15, 2)->default(0.00); // Fr.Amt
            $table->decimal('unload_rate', 12, 2)->default(0.00); // Unload Rt.
            $table->decimal('unload_amount', 12, 2)->default(0.00); // Unload Amt.
            $table->decimal('other_charges', 12, 2)->default(0.00); // Oth.Ch.
            $table->decimal('amount', 15, 2)->default(0.00); // Amt.
            $table->timestamps();
        });

        if (Schema::hasTable('bilties') && !Schema::hasColumn('bilties', 'invoice_id')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bilties') && Schema::hasColumn('bilties', 'invoice_id')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->dropColumn('invoice_id');
            });
        }
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
