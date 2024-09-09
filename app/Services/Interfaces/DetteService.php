<?php

namespace App\Services\Interfaces;

use App\Http\Requests\AddDetteRequest;
use App\Http\Requests\PaiementRequest;
use Illuminate\Http\Request;

interface DetteService
{
    public function all(Request $request);
    public function find($id);
    public function findWithClient($id);
    public function findWithArticle($id);
    public function findWithPaiement($id);
    public function create(AddDetteRequest $request);
    public function payer(PaiementRequest $request);
}
