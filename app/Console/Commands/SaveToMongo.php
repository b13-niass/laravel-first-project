<?php

namespace App\Console\Commands;

use App\Jobs\SaveToMongoJob;
use Illuminate\Console\Command;

class SaveToMongo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-to-mongo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SaveToMongoJob::dispatch();
    }
}
