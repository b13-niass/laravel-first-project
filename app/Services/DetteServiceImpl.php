<?php

namespace App\Services;

use App\Exceptions\DetteException;
use App\Http\Requests\AddDetteRequest;
use App\Http\Requests\PaiementRequest;
use App\Http\Resources\DetteResource;
use App\Models\Article;
use App\Repositories\Interfaces\DetteRepository;
use App\Services\Interfaces\DetteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DetteServiceImpl implements DetteService
{
    public function __construct(private DetteRepository $repository){

    }

    public function all(Request $request)
    {
        try {
            $dettes = $this->repository->all($request);
            return DetteResource::collection($dettes);
        }catch(DetteException $e){
            return $e->render();
        }
    }

    public function find($id)
    {
        try {
            $dette = $this->repository->find($id);
            if($dette){
                return $dette;
            }else{
                throw new DetteException("Dette introuvable", Response::HTTP_LENGTH_REQUIRED);
            }
        }catch(DetteException $e) {
            return $e->render();
        }
    }

    public function findWithClient($id)
    {
        try {
            $dette = $this->repository->find($id);
            $result = $this->repository->findWithClient($id);
            return new DetteResource($result[0]);
        }catch(ModelNotFoundException $e) {
            throw new DetteException("Dette introuvable", Response::HTTP_LENGTH_REQUIRED);
        }
    }

    public function findWithArticle($id)
    {
        try {
            $dette = $this->repository->find($id);
                $result =  $this->repository->findWithArticle($id);
                 return new DetteResource($result[0]);
        }catch(ModelNotFoundException $e) {
            throw new DetteException("Dette introuvable", Response::HTTP_LENGTH_REQUIRED);
        }
    }

    public function findWithPaiement($id)
    {
        try {
            $dette = $this->repository->find($id);
                $result =  $this->repository->findWithPaiement($id);
                 return new DetteResource($result[0]);
        }catch(ModelNotFoundException $e) {
            throw new DetteException("Dette introuvable", Response::HTTP_LENGTH_REQUIRED);
        }
    }

    public function create(AddDetteRequest $request)
    {
        $data = $request->only('montant', 'client_id','articles','paiement');
        try {
            $collection = collect($data['articles']);
            $groupedArticles = $collection->groupBy('article_id')->mapWithKeys(function ($items, $articleId) {
                return [
                    $articleId => [
                        'qteVente' => $items->sum('qteVente'),
                        'prixVente' => $items->first()['prixVente']
                    ]
                ];
            });

            $montant = $groupedArticles->map(function ($item) {
                return $item['qteVente'] * $item['prixVente'];
            })->sum();

            if ($montant !== $data['montant']) {
                throw new DetteException('Le montant des articles ne correspond pas au montant de la dette', Response::HTTP_CONFLICT);
            }

            $insufficientStock = $groupedArticles->filter(function ($article, $articleId) {
                $articleStock = Article::find($articleId)->qte;
                return $article['qteVente'] > $articleStock;
            });
            $articleInvalid = [];
            if ($insufficientStock->isNotEmpty()) {
                foreach ($insufficientStock as $articleId => $article) {
                    $articleStock = Article::find($articleId)->qte;
                    $articleInvalid[] = "Article ID $articleId : Quantité demandée ({$article['qteVente']}) dépasse la quantité en stock ({$articleStock}).";
                }

                if (count($articleInvalid) > 0){
                    throw new DetteException(implode('', $articleInvalid), Response::HTTP_CONFLICT);
                }
            }

            $data['articles'] = $groupedArticles->toArray();
            $result = $this->repository->create($data);

            return $result;
        }catch (DetteException $e){
            DB::rollBack();
//            dd($e->render());
            return $e->render();
        }
    }

    public function payer(PaiementRequest $request, $id)
    {
        try {
            $dette = $this->repository->find($id);
            $montant = $request->get('montant');
            if ($montant > $dette->montant_du){
                throw new DetteException('Le montant du paiement dépasse le montant de la dette', Response::HTTP_CONFLICT);
            }
            $result = $this->repository->payer($dette, $montant);
//            dd($result);
            if ($result){
                $data = $this->repository->findWithPaiement($dette->id);
                return new DetteResource($data[0]);
            }
        }catch(ModelNotFoundException$e) {
            throw new DetteException("Dette introuvable", Response::HTTP_LENGTH_REQUIRED);
        }catch (DetteException $e){
            return $e->render();
        }
    }
}
