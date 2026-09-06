<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipping_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Seed default shipping statuses
        $defaults = [
            ['name' => 'Booked', 'is_active' => true, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shipped', 'is_active' => true, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'In Transit', 'is_active' => true, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Delivered', 'is_active' => true, 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('shipping_statuses')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_statuses');
    }
};
