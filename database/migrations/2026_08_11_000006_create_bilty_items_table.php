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
        Schema::create('bilty_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('bilty_id')->constrained('bilties')->cascadeOnDelete();
            
            $table->integer('no_of_pkgs')->default(0);
            $table->string('packing')->nullable(); // Box, Bag, Roll, Bundle etc.
            $table->string('description')->nullable(); // description of goods
            $table->string('invoice_no')->nullable();
            $table->decimal('invoice_value', 15, 2)->default(0.00);
            $table->string('unit')->default('KG'); // KG, Fixed
            $table->decimal('qty', 12, 3)->default(0.000);
            $table->decimal('rate', 12, 2)->default(0.00);
            
            // Item-level charges (matching legacy fields)
            $table->decimal('st', 10, 2)->default(0.00);
            $table->decimal('rc', 10, 2)->default(0.00);
            $table->decimal('sc', 10, 2)->default(0.00);
            $table->decimal('dd', 10, 2)->default(0.00);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilty_items');
    }
};
