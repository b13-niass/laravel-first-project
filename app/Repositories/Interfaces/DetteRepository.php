<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface DetteRepository
{
    public function all(Request $request);
    public function find($id);
    public function findWithClient($id);
    public function findWithArticle($id);
    public function findWithPaiement($id);
    public function create($data);
    public function payer($data);
}
