<?php

namespace App\Observers;

use App\Events\FidelityCardCreated;
use App\Facades\CarteFacade;
use App\Models\Client;
use App\Trait\MyImageTrait;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ClientObserver implements ShouldHandleEventsAfterCommit
{
    use MyImageTrait;
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
            event(new FidelityCardCreated($client));
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}
