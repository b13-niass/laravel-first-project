<?php

namespace App\Console\Commands;

use App\Jobs\RelanceToCloudJob;
use Illuminate\Console\Command;

class RelanceToCloudClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:relance-cloud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'La commande pour relancer le stockage des photo dans le cloud';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dispatch(new RelanceToCloudJob());
    }
}
