<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateArticleStockRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Trait\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    use ApiResponseTrait;
    // Affiche la liste des articles
    public function index()
    {
        $articles = QueryBuilder::for(Article::class)
            ->allowedFilters(['libelle'])
            ->get();

        return $this->sendResponse('success', ArticleResource::collection($articles), 'La liste de tous les articles', 200);
    }

    // Affiche un article spécifique
    public function show($id)
    {
        try {
            $article = Article::findOrFail($id);

            return $this->sendResponse('success', new ArticleResource($article), "L'articles", 200);
        } catch (\Exception $e) {
            return $this->sendResponse("failed", null, $e->getMessage(), 500);
        }
    }

    // Crée un nouvel article
    public function store(StoreArticleRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $article = Article::create($validatedData);
            return $this->sendResponse("success", new ArticleResource($article), 'Article Ajouter avec success', 201);
        } catch (\Exception $e) {
            return $this->sendResponse("failed", null, $e->getMessage(), 500);
        }
    }

    // Met à jour un article existant
    public function update(UpdateArticleRequest $request, $id)
    {
        try {
            $article = Article::findOrFail($id);

            $validatedData = $request->validated();

            $article->update($validatedData);

            return $this->sendResponse("success", new ArticleResource($article), 'Article mis à jour', 200);
        } catch (\Exception $e) {
            return $this->sendResponse("failed", null, $e->getMessage(), 500);
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

        $failedUpdates = [];

        DB::beginTransaction();

        try {
            foreach ($articles as $article) {
                $id = $article['id'];
                $qte = $article['qte'];

                $existingArticle = Article::find($id);

                if ($existingArticle) {
                    $existingArticle->qte += $qte;
                    $existingArticle->save();
                } else {
                    $failedUpdates[] = $article;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $failedUpdates = $articles;
        }

        if (!empty($failedUpdates)) {
            return $this->sendResponse('failed', $failedUpdates, 'Certains articles n\'ont pas pu être mis à jour.', 400);
        }

        return $this->sendResponse('success', $failedUpdates, 'Tous les articles ont été mis à jour avec succès.', 200);
    }
}
