<?php

namespace App\Services\Interfaces;


use App\Http\Requests\ArticleByLibelleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateArticleStockRequest;
use Illuminate\Http\Request;

interface ArticleService
{
    public function all(Request $request);
    public function find($id);
    public function create(StoreArticleRequest $request);
    public function update(UpdateArticleRequest $request, $id);
    public function delete($id);
    public function findByLibelle(ArticleByLibelleRequest $request);
    public function findByEtat($etat);
    public function restore($id);
    public function forceDelete($id);
    public function updateStock(UpdateArticleStockRequest $request);
}
