<?php

namespace App\Services;


use App\Enums\StateEnum;
use App\Filters\QuantityFilter;
use App\Http\Requests\ArticleByLibelleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateArticleStockRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\User;
use App\Repositories\Interfaces\ArticleRepository;
use App\Services\Interfaces\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleServiceImpl implements ArticleService
{
    public function __construct(private ArticleRepository $repository)
    {
    }

    public function all(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if(!Gate::allows("isBoutiquier", $user)){
            return null;
        }
         $articles = $this->repository->all($request);
        if (count($articles) == 0){
            return null;
        }
        return ArticleResource::collection($articles);
    }

    public function find($id)
    {
        try {
            $user = User::find(Auth::user()->id);
            if(!Gate::allows("isBoutiquier", $user)){
                return null;
            }
            $article = $this->repository->find($id);

            return  new ArticleResource($article);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function create(StoreArticleRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $article = $this->repository->create($validatedData);
            return new ArticleResource($article);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function update(UpdateArticleRequest $request ,$id)
    {
        try {
            $qte = $request->get('qte');
            $id = (int) $id;
            $article = $this->repository->update($qte, $id);

            return new ArticleResource($article);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function delete($id)
    {
        try {
            return  $this->repository->delete($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findByLibelle(ArticleByLibelleRequest $request)
    {
        try {
            $libelle = $request->get('libelle');
            $article = $this->repository->findByLibelle($libelle);
            return new ArticleResource($article);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findByEtat($etat)
    {
        // TODO: Implement findByEtat() method.
    }

    public function restore($id)
    {
        try {
            $article = $this->repository->restore($id);
            return new ArticleResource($article);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function forceDelete($id)
    {
        try {
           return $this->repository->forceDelete($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateStock(UpdateArticleStockRequest $request)
    {
        $result = [];
        try {
            $articles = $request->input('articles');
            $result = $this->repository->updateStock($articles);
        } catch (\Exception $e) {
            return $result;
        }

        if (!empty($failedUpdates)) {
            return $result;
        }

        return $result;
    }
}
