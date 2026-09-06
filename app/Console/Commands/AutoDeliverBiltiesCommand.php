<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\AutoDeliverBiltiesJob;

class AutoDeliverBiltiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bilty:auto-deliver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update C.N. shipping status to Delivered after 24h (Vehicle Number) or 48h (Transport Name)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        AutoDeliverBiltiesJob::dispatchSync();
        $this->info("Dispatched AutoDeliverBiltiesJob successfully.");
        return Command::SUCCESS;
    }
}
