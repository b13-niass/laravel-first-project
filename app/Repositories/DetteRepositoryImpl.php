<?php

namespace App\Repositories;

use App\Exceptions\DetteException;
use App\Models\Dette;
use App\Repositories\Interfaces\DetteRepository;
use Illuminate\Support\Facades\DB;

class DetteRepositoryImpl implements DetteRepository
{

    public function all()
    {
        // TODO: Implement all() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findByClient($id)
    {
        // TODO: Implement findByClient() method.
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

    public function payer($data)
    {
        // TODO: Implement payer() method.
    }
}
