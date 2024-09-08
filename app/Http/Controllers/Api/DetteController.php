<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddDetteRequest;
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
}
