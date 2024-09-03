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
use App\Services\Interfaces\ArticleService;
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

    public function __construct(private ArticleService $service)
    {
    }

    // Affiche la liste des articles
    public function index(Request $request)
    {
        $data = $this->service->all($request);
        return compact('data');
    }

    // Affiche un article spécifique
    public function show($id)
    {
        $data = $this->service->find($id);
        return compact('data');
    }

    public function showByLibelle(ArticleByLibelleRequest $request)
    {
        $data = $this->service->findByLibelle($request);
        return compact('data');
    }

    // Crée un nouvel article
    public function store(StoreArticleRequest $request)
    {
        $data = $this->service->create($request);
        return compact('data');
    }

    // Met à jour un article existant
    public function update(UpdateArticleRequest $request, $id)
    {
        $data = $this->service->update($request, $id);
        return compact('data');
    }

    // Supprime un article (soft delete)
    public function destroy($id)
    {
        return $this->service->delete($id);
    }

    // Restaure un article supprimé (soft delete)
    public function restore($id)
    {
       $data = $this->service->restore($id);
        return compact('data');
    }

    // Supprime définitivement un article
    public function forceDelete($id)
    {
        $data = $this->service->forceDelete($id);
        return compact('data');
    }

    public function updateStock(UpdateArticleStockRequest $request)
    {
       $data = $this->service->updateStock($request);
        return compact('data');
    }

}
