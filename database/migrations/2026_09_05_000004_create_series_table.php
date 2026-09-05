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
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default series
        $defaults = [
            ['name' => '26-27', 'description' => 'FY 2026-2027', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '25-26', 'description' => 'FY 2025-2026', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('series')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
