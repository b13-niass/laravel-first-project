<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\UpdateArticleStockRequest;
use Illuminate\Http\Request;

interface ArticleRepository
{
    public function all(Request $request);
    public function find($id);
    public function create($data);
    public function update($qte, $id);
    public function delete($id);
    public function findByLibelle($libelle);
    public function findByEtat($etat);
    public function restore($id);
    public function forceDelete($id);
    public function updateStock($data);
}
