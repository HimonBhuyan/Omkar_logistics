<?php

namespace App\Jobs;

use App\Models\Bilty;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoDeliverBiltiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = Carbon::now();

        // 1. Vehicle Number entries: > 24 hours
        $cutoffVehicle = $now->copy()->subHours(24);
        $vehicleUpdated = Bilty::whereNotIn('shipping_status', ['Delivered', 'Cancelled'])
            ->whereNotNull('vehicle_no')
            ->whereRaw("TRIM(vehicle_no) != ''")
            ->where(function ($query) {
                $query->whereNull('type')
                    ->orWhereRaw("UPPER(TRIM(type)) = 'VEHICLE NUMBER'")
                    ->orWhereRaw("UPPER(TRIM(type)) != 'TRANSPORT NAME'");
            })
            ->where('created_at', '<=', $cutoffVehicle)
            ->update(['shipping_status' => 'Delivered']);

        // 2. Transport Name entries: > 48 hours
        $cutoffTransport = $now->copy()->subHours(48);
        $transportUpdated = Bilty::whereNotIn('shipping_status', ['Delivered', 'Cancelled'])
            ->whereNotNull('vehicle_no')
            ->whereRaw("TRIM(vehicle_no) != ''")
            ->whereRaw("UPPER(TRIM(type)) = 'TRANSPORT NAME'")
            ->where('created_at', '<=', $cutoffTransport)
            ->update(['shipping_status' => 'Delivered']);

        $totalUpdated = $vehicleUpdated + $transportUpdated;

        $msg = "AutoDeliverBiltiesJob Completed: {$totalUpdated} C.N. entries updated to 'Delivered' ({$vehicleUpdated} Vehicle Number > 24h, {$transportUpdated} Transport Name > 48h).";
        Log::info($msg);
    }
}
