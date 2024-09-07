<?php

namespace App\Jobs;

use App\Facades\CarteFacade;
use App\Mail\CarteMail;
use App\Trait\MyImageTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Queueable, MyImageTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $client;
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

            Mail::to($this->client->user->login)->send(new CarteMail($path));
    }

}
