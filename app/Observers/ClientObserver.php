<?php

namespace App\Observers;

use App\Events\ClientCreated;
use App\Facades\CarteFacade;
use App\Models\Client;
use App\Trait\MyImageTrait;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Log;

class ClientObserver implements ShouldHandleEventsAfterCommit
{
    use MyImageTrait;

//    public function creating(Client $client){
//        Log::info([__LINE__,$client]);
//        dd($client);
//    }

    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        $file = $client->getTransientAttribute('file');
//        dd($file);
        event(new ClientCreated($client, $file));
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
