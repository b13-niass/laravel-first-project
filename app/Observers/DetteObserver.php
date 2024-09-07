<?php

namespace App\Observers;

use App\Exceptions\DetteException;
use App\Models\Article;
use App\Models\Dette;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetteObserver
{
    /**
     * Handle the Dette "created" event.
     */
    public function created(Dette $dette): void
    {
//        dd(1);
        $articles = $dette->articles_transients;
        $paiement = $dette->paiement_transients;

        $result = $dette->articles()->sync($articles);

        if (!$result){
            DB::rollBack();
        }
        if (count($paiement) > 0){
            $result = $dette->paiements()->create($paiement);
        }
        if (!$result){
            DB::rollBack();
        }
        $result = null;
        foreach ($articles as $id => $article){
            $a = Article::find($id);
            $result = $a->decrement('qte', $article['qteVente']);
        }

        if (!$result){
            DB::rollBack();
        }else{
            DB::commit();
        }
    }

    /**
     * Handle the Dette "creating" event.
     */
    public function creating(Dette $dette): void
    {

    }

    /**
     * Handle the Dette "updated" event.
     */
    public function updated(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "deleted" event.
     */
    public function deleted(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "restored" event.
     */
    public function restored(Dette $dette): void
    {
        //
    }

    /**
     * Handle the Dette "force deleted" event.
     */
    public function forceDeleted(Dette $dette): void
    {
        //
    }
}
