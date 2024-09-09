<?php

namespace App\Jobs;

use App\Facades\UploadFacade;
use App\Models\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadToCloudJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $client;
    public $file;
    /**
     * Create a new job instance.
     */
    public function __construct(Client $client, $file)
    {
        $this->client = $client;
        $this->file = $file;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->file) {
            $originalName = explode('/', $this->file)[1];
            $filePath =  storage_path("/app/public/$this->file");
            $file = new UploadedFile($filePath,$originalName);
            $imageName = UploadFacade::upload($file);
            if ($imageName){
                Log::info( $imageName);
                $user = $this->client->user->update(['photo' => null ,'photo_remote' => $imageName, 'remote' => true]);
                Log::info($user);
            }
        }
    }
}
