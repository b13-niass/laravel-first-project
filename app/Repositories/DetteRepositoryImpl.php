<?php

namespace App\Repositories;

use App\Exceptions\DetteException;
use App\Models\Dette;
use App\Repositories\Interfaces\DetteRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetteRepositoryImpl implements DetteRepository
{

    public function all(Request $request)
    {
//        dd(Dette::filter($request)->get());
        return Dette::filter($request);
    }

    public function find($id)
    {
        return Dette::findOrFail($id);
    }

    public function findWithClient($id)
    {
        return Dette::filterWith($id,'client');
    }

    public function findWithArticle($id)
    {
        return Dette::filterWith($id,'articles');
    }

    public function findWithPaiement($id)
    {
        return Dette::filterWith($id,'paiements');
    }

    public function create($data)
    {
            DB::beginTransaction();
            $dette = Dette::make($data);
            $dette->articles_transients = $data['articles'];
            $dette->paiement_transients = $data['paiement']??[];
            $dette->save();
            return $dette;
    }

    public function payer($dette, $montant)
    {
       return $dette->paiements()->create(['montant' => $montant]);
//        $dette->save();
    }

    /**
     * Récupérer toutes les dettes non soldées et les grouper par client_id avec un cumul des montant_du.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTotalDettesNonSolderParClient()
    {
        $dettes = Dette::all();
        $dettes = $dettes->filter(fn($dette) => $dette->montant_du > 0);

        $dettesGroupes = $dettes->groupBy('client_id')->map(function ($group) {
            $client = $group->first()->client;
            $client->montant_du = $group->sum('montant_du');
            return $client;
        })->values();

        return $dettesGroupes;
    }
}
