<?php

namespace App\Services;

use App\Exceptions\DetteException;
use App\Http\Requests\AddDetteRequest;
use App\Http\Requests\PaiementRequest;
use App\Models\Article;
use App\Repositories\Interfaces\DetteRepository;
use App\Services\Interfaces\DetteService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DetteServiceImpl implements DetteService
{
    public function __construct(private DetteRepository $repository){

    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findByClient($id)
    {
        // TODO: Implement findByClient() method.
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

    public function payer(PaiementRequest $request)
    {
        // TODO: Implement payer() method.
    }
}
