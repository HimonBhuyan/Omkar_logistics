<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\AutoDeliverBiltiesJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Artisan command to run auto-deliver manually
Artisan::command('bilty:auto-deliver', function () {
    $this->info('Running AutoDeliverBiltiesJob manually...');
    (new AutoDeliverBiltiesJob)->handle();
    $this->info('AutoDeliverBiltiesJob completed successfully.');
})->purpose('Run the bilty auto-deliver job manually');

// Schedule bilty auto-deliver job hourly
Schedule::job(new AutoDeliverBiltiesJob)->hourly();
