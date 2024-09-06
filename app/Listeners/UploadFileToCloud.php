<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\UploadToCloudJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UploadFileToCloud
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
    public function handle(ClientCreated $event): void
    {
        $imageName = time().'.'.$event->file->getClientOriginalExtension();
        $fileName = $event->file->storeAs('images', $imageName, [
            'disk' => 'public'
        ]);
        $user = $event->client->user->update(['photo' => $fileName, 'remote' => false]);
        dispatch(new UploadToCloudJob($event->client, $fileName));
    }
}
