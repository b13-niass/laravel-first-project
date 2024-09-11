<?php

namespace App\Jobs;

use App\Models\Dette;
use App\Models\MongoDette;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SaveToMongoJob implements ShouldQueue
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
        $dettes = Dette::with('client', 'articles', 'paiements')->get();

        $dettes = $dettes->filter(fn($dette) => $dette->montant_du == 0);

        foreach ($dettes->all() as $dette) {

            $mongoDette = new MongoDette();
            $mongoDette->montant = $dette->montant;
            $mongoDette->client_id = $dette->client_id;
            $mongoDette->created_at = $dette->created_at;
            $mongoDette->updated_at = $dette->updated_at;
            $mongoDette->montant_verse = $dette->montant_verse;
            $mongoDette->montant_du = $dette->montant_du;
            $mongoDette->client = $dette->client->toArray();
            $mongoDette->articles = $dette->articles->map(function ($article) {
                return [
                    'id' => $article->id,
                    'libelle' => $article->libelle,
                    'prix' => $article->pivot->prixVente,
                    'qte' => $article->pivot->qteVente,
                    'deleted_at' => $article->deleted_at,
                    'created_at' => $article->created_at,
                    'updated_at' => $article->updated_at
                ];
            })->toArray();

            $mongoDette->paiements = $dette->paiements->toArray();

            $mongoDette->save();
        }

    }
}
