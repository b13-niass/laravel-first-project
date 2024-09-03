<?php

namespace App\Http\Controllers\Api;

use App\Enums\StateEnum;
use App\Facades\ClientServiceFacade;
use App\Filters\ClientWithCompteActiveFilter;
use App\Filters\ClientWithCompteFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountForClientRequest;
use App\Http\Requests\ClientByPhoneRequest;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\DetteResource;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Trait\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\QueryBuilder;

class ClientController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $data = ClientServiceFacade::all($request);
        return compact('data');
    }

    public function show($id)
    {
       $data = ClientServiceFacade::find($id);
        return compact('data');
    }

    public function showByPhone(ClientByPhoneRequest $request)
    {
        $data = ClientServiceFacade::findByPhone($request);
        return compact('data');
    }

    public function showWithUser($id)
    {
       $data = ClientServiceFacade::findWithCompte($id);
        return compact('data');
    }

    public function showDettesUser($id){
        $data = ClientServiceFacade::findByDettes($id);
        return compact('data');
    }

    public function store(StoreClientRequest $request)
    {
        $validateData = $request->validated();
        $data = ClientServiceFacade::create($validateData);
        return compact('data');
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $data = ClientServiceFacade::update($request, $id);
        return compact('data');
    }

    public function destroy($id)
    {
       $data = ClientServiceFacade::delete($id);
        return compact('data');
    }

    public function register(AccountForClientRequest $request){
        $validateData = $request->validated();
        $data = ClientServiceFacade::register($validateData, $request);
        return compact('data');
    }
}
