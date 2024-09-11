<?php

namespace App\Jobs;

use App\Services\Interfaces\MessageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DetteMessageJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable, InteractsWithQueue, SerializesModels;
    public $dettes;
    /**
     * Create a new job instance.
     */
    public function __construct($dettes)
    {
        $this->dettes = $dettes;
    }

    /**
     * Execute the job.
     */
    public function handle(MessageService $message): void
    {
        $from = 'Peulh Bi';
        foreach($this->dettes as $dette){
            Log::info($dette);
            $message->send("+221767819339", $from, "Bonjour, Nous Vous informons que vous avez toujours une dette de : ".$dette['montant_du'] ." Fcfa");
        }
    }
}
