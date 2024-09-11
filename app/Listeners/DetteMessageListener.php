<?php

namespace App\Listeners;

use App\Events\SendMessaged;
use App\Jobs\DetteMessageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DetteMessageListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendMessaged $event): void
    {
        DetteMessageJob::dispatch($event->dettes);
    }
}
