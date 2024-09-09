<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddDetteRequest;
use App\Http\Resources\DetteResource;
use App\Services\DetteServiceImpl;
use App\Services\Interfaces\DetteService;
use Illuminate\Http\Request;

class DetteController extends Controller
{
    public function __construct(private DetteService $service){

    }
    public function index(Request $request)
    {
        $data = $this->service->all($request);
        return response(
            [
                'message' => 'La liste des dettes',
                'data' => $data
            ],
            200
        );
    }

    public function create(AddDetteRequest $request){
        $data = $this->service->create($request);
//        dd($data);
        return response(
            [
                'message' => 'La dette a été créé avec succès',
                'data' => $data
            ],
            201
        );
    }

    public function showWithClient($id){

        $data = $this->service->findWithClient($id);
//        dd(new DetteResource($data[0]));
        return response(
            [
                'message' => 'La dette avec son client',
                'data' => $data
            ],
            200
        );
    }
    public function showWithArticle($id){
        $data = $this->service->findWithArticle($id);
        return response(
            [
                'message' => 'La dette avec les articles',
                'data' => $data
            ],
            200
        );
    }
    public function showPaiementsDette($id){
        $data = $this->service->findWithPaiement($id);
        return response(
            [
                'message' => 'La dette avec ses paiements',
                'data' => $data
            ],
            200
        );
    }
}
