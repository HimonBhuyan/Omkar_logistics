<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Series;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('bilties', 'series_id')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->foreignId('series_id')->nullable()->after('company_id')->constrained('series')->onDelete('restrict');
            });
        }

        // Backfill series_id for any existing bilties
        $bilties = DB::table('bilties')->whereNull('series_id')->get();
        foreach ($bilties as $b) {
            $seriesCode = $b->series ? strtoupper(trim($b->series)) : '26-27';
            $seriesObj = Series::firstOrCreate(
                ['name' => $seriesCode],
                ['description' => 'FY ' . $seriesCode, 'is_active' => true]
            );
            DB::table('bilties')->where('id', $b->id)->update(['series_id' => $seriesObj->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bilties', 'series_id')) {
            Schema::table('bilties', function (Blueprint $table) {
                $table->dropForeign(['series_id']);
                $table->dropColumn('series_id');
            });
        }
    }
};
