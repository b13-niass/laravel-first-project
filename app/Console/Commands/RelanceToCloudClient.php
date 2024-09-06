<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RelanceToCloudClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:relance-mail-client';

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
        //
    }
}
