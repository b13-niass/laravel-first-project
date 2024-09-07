<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RelanceToCloudJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $clients = Client::whereHas('user', function ($query) {
            $query->where('remote', false);
        })->get();

        foreach ($clients as $client) {
            dispatch(new UploadToCloudJob($client,$client->user->photo));
        }
        Log::info($clients);
    }
}
