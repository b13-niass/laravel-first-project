<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendMessageDettesJob implements ShouldQueue
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

//        // URL de l'endpoint que vous souhaitez déclencher
//        $url = 'http://127.0.0.1:8002/wane/v1/dettes/clients/messages';
//        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZjY2ZjE3YmRkODQxNzM2NWM2MGNmYmY1NTYwM2U1YTIxYTQ2NmQzZjc5YzQ3MmJlY2Q2NTU3ZWNkNTE4YThjZDhmY2M1YjRmYjlmMDkxMzciLCJpYXQiOjE3MjU5MTYwNTcuODIyMjI3LCJuYmYiOjE3MjU5MTYwNTcuODIyMjI5LCJleHAiOjE3NTc0NTIwNTcuODIwMzA2LCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.H6cVFzGFLpgFci_wprABzb8C3uL1tH7H_NTgPZbc4F0Y08--TX82I7RdOFGgfVVBG1O6tqhgzFpAUO7nu4ayo-9fORFP70xiSbI33PNmusWWsr54c8N62EBDl043FJ5SlWxHQ64TJLGDE7oMam5jyn2J0dEwU5g38ZW3W45XQe3N6b8IOyY_BB06x-8ZJBIsztvr54aMSV3Qc9mNQs26Up4u_FrsViwUZS9IMdqYtQg9lrAFNt1nydnneJ20955D5vTKYDpJkttT64LVOI4MhmYk-u-SVS7X7Td0OZ2bs7mCP7zmqqeQL93erq_qcL-SHqdtAx77QaF1BPLlumn9QVAvbsqSY13CfXOx1F3EdQrqUyerLFGftsW5ITSbkfECgDcXnzvt_ADezp9VUdLPwIKBOOTOcHNPNEf2HqHRQBAe9l0-Uv5Tz2uXL9KeKncmOEsSjtZ78evllZTOHIcChypkaYF6XCE2W9yzniVjYnszeN-fBHJrsMckTGMxJLK_TWfmeqgoDiyAka8ysjUTpnT577_gdVMvYlYL4SCqf7JLz6BNSgqn4kYLh05OzhhbMXCBzrLPNNJ5zV36bZddH4QQcyaTVaKkHbCZFGCp-NW8WdoCiUpslnvWh3f7YzWG039S7iQKs_ciSIGlImDoWh6IkSo5m_heMhh2J7lBPzE';
//
//        try {
//            $response = Http::withToken($token)->get($url);
//            if ($response->successful()) {
//                $this->info('Requête GET réussie : ' . $response->body());
//            } else {
//                $this->error('Erreur lors de la requête GET : ' . $response->status());
//            }
//        } catch (\Exception $e) {
//            $this->error('Erreur lors de l\'exécution : ' . $e->getMessage());
//        }
    }
}
