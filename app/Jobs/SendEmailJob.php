<?php

namespace App\Jobs;

use App\Facades\CarteFacade;
use App\Mail\CarteMail;
use App\Trait\MyImageTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Queueable, MyImageTrait;
    public $client;
    public $filePath;
    /**
     * Create a new job instance.
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $qrcode = base64_encode($this->generateQrcode($this->client->id));

        $data = [
            'qrcode' => $qrcode,
            'client' => $this->client
        ];

        $path = CarteFacade::format($data);
        Log::info($path);
        Mail::to($this->client->user->login)->send(new CarteMail($path));
    }
}
