<?php

namespace App\Http\Controllers\Api;

use App\Enums\StateEnum;
use App\Filters\QuantityFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleByLibelleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateArticleStockRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\User;
use App\Trait\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    use ApiResponseTrait;
    // Affiche la liste des articles
    public function index(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if(!Gate::allows("isBoutiquier", $user)){
            return $this->sendResponse(StateEnum::ECHEC, null, 'Cette action n\'est pas autorisée', Response::HTTP_FORBIDDEN);
        }

        $query = Article::query();
        if ($request->has('disponible')) {
            $disponible = $request->query('disponible');
            $query = (new QuantityFilter())($query, $disponible, 'disponible');
        }

        $articles = QueryBuilder::for($query)
            ->allowedFilters(['libelle'])
            ->get();

        if (!$articles){
            return $this->sendResponse(StateEnum::ECHEC, null, 'Aucun article disponible', Response::HTTP_NOT_FOUND);
        }

        return $this->sendResponse(StateEnum::SUCCESS, ArticleResource::collection($articles), 'La liste de tous les articles', Response::HTTP_OK);
    }

    // Affiche un article spécifique
    public function show($id)
    {
        try {
            $user = User::find(Auth::user()->id);
            if(!Gate::allows("isBoutiquier", $user)){
                return $this->sendResponse(StateEnum::ECHEC, null, 'Cette action n\'est pas autorisée', Response::HTTP_FORBIDDEN);
            }

            $article = Article::findOrFail($id);

            return $this->sendResponse(StateEnum::SUCCESS, new ArticleResource($article), "L'articles est reouvé", Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Article non Trouvé', Response::HTTP_LENGTH_REQUIRED);
        }
    }

    public function showByLibelle(ArticleByLibelleRequest $request)
    {
        try {
            $libelle = $request->get('libelle');
            $article = Article::where('libelle', $libelle)->firstOrFail();

            return $this->sendResponse(StateEnum::SUCCESS, new ArticleResource($article), "L'articles est reouvé", Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Article non Trouvé', Response::HTTP_LENGTH_REQUIRED);
        }
    }

    // Crée un nouvel article
    public function store(StoreArticleRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $article = Article::create($validatedData);
            return $this->sendResponse(StateEnum::SUCCESS, new ArticleResource($article), 'Article Enregistrer avec success', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Met à jour un article existant
    public function update(UpdateArticleRequest $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
//            if(!$article) {
//                return $this->sendResponse(StateEnum::ECHEC, null, 'Article non trouvé', Response::HTTP_LENGTH_REQUIRED);
//            }
            $qte = $request->get('qte');
//            dd($qte);
            $article->increment('qte', $qte);

            return $this->sendResponse(StateEnum::SUCCESS ,new ArticleResource($article), 'Article mis à jour', Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Supprime un article (soft delete)
    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);
            $article->delete();
            return $this->sendResponse("success", null, 'Article Supprimer', 201);
        } catch (\Exception $e) {
            return $this->sendResponse("failed", null, $e->getMessage(), 500);
        }
    }

    // Restaure un article supprimé (soft delete)
    public function restore($id)
    {
        try {
            $article = Article::withTrashed()->findOrFail($id);
            $article->restore();
            return $this->sendResponse('success', new ArticleResource($article), 'Article restauré avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendResponse("failed", null, $e->getMessage(), 500);
        }
    }

    // Supprime définitivement un article
    public function forceDelete($id)
    {
        try {
            $article = Article::withTrashed()->findOrFail($id);
            $article->forceDelete();
            return $this->sendResponse('success', null, 'Article supprimé définitivement.', 204);
        } catch (\Exception $e) {
            return $this->sendResponse("failed", null, $e->getMessage(), 500);
        }
    }

    public function updateStock(UpdateArticleStockRequest $request)
    {
        $articles = $request->input('articles');

        $result['failedUpdates'] = [];
        $result['successUpdates'] = [];
        try {
            foreach ($articles as $article) {
                $id = $article['id'];
                $qte = $article['qte'];

                $existingArticle = Article::find($id);
//                dd($existingArticle);
                if ($existingArticle) {
                    $existingArticle->qte += $qte;
                    $existingArticle->save();
                    $result['successUpdates'][] = $existingArticle;
                } else {
                    $result['failedUpdates'][] = $article['id'];
                }
            }

        } catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, $result, 'Certains articles n\'ont pas pu être mis à jour.', Response::HTTP_BAD_REQUEST);
        }

        if (!empty($failedUpdates)) {
            return $this->sendResponse(StateEnum::ECHEC, $result, 'Certains articles n\'ont pas pu être mis à jour.', Response::HTTP_BAD_REQUEST);
        }

        return $this->sendResponse(StateEnum::SUCCESS, $result, 'Tous les articles ont été mis à jour avec succès.', Response::HTTP_OK);
    }

}
