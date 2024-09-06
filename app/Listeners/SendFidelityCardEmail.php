<?php

namespace App\Listeners;

use App\Events\FidelityCardCreated;
use App\Jobs\SendEmailJob;
use App\Mail\CarteMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendFidelityCardEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(FidelityCardCreated $event): void
    {
        dispatch(new SendEmailJob($event->client));
    }
}
