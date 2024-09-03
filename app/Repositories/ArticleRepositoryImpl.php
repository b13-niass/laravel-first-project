<?php

namespace App\Repositories;


use App\Filters\QuantityFilter;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepository;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleRepositoryImpl implements ArticleRepository
{

    public function all(Request $request)
    {
        $query = Article::query();
        if ($request->has('disponible')) {
            $disponible = $request->query('disponible');
            $query = (new QuantityFilter())($query, $disponible, 'disponible');
        }

        $articles = QueryBuilder::for($query)
            ->allowedFilters(['libelle'])
            ->get();

        return $articles;
    }

    public function find($id)
    {
        return Article::findOrFail($id);
    }

    public function create($data)
    {
        return Article::create($data);
    }

    public function update($qte, $id)
    {
//        \Illuminate\Support\Facades\Log::info([$id,$qte]);
        $article = Article::findOrFail($id);
        $article->increment('qte', $qte);
        return $article;
    }

    public function delete($id)
    {
        $article = Article::findOrFail($id);
        return $article->delete();
    }

    public function findByLibelle($libelle)
    {
        return Article::where('libelle', $libelle)->firstOrFail();
    }

    public function findByEtat($etat)
    {
        // TODO: Implement findByEtat() method.
    }

    public function restore($id)
    {
        $article = Article::withTrashed()->findOrFail($id);
        $article->restore();
        return $article;
    }

    public function forceDelete($id)
    {
        $article = Article::withTrashed()->findOrFail($id);
        return $article->forceDelete();
    }

    public function updateStock($data)
    {
        $result['failedUpdates'] = [];
        $result['successUpdates'] = [];
        foreach ($data as $article) {
            $id = $article['id'];
            $qte = $article['qte'];

            $existingArticle = Article::find($id);
            if ($existingArticle) {
                $existingArticle->qte += $qte;
                $existingArticle->save();
                $result['successUpdates'][] = new ArticleResource($existingArticle);
            } else {
                $result['failedUpdates'][] = $article['id'];
            }
        }
        return $result;
    }
}
