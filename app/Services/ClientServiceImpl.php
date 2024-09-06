<?php

namespace App\Services;


use App\Enums\StateEnum;
use App\Events\ClientCreated;
use App\Facades\CarteFacade;
use App\Facades\ClientRepositoryFacade;
use App\Facades\UploadFacade;
use App\Http\Requests\AccountForClientRequest;
use App\Http\Requests\ClientByPhoneRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\DetteResource;
use App\Mail\CarteMail;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Services\Interfaces\ClientService;
use App\Trait\MyImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClientServiceImpl implements ClientService
{

    public function all(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if (!Gate::allows("isBoutiquier", $user)){
            return null;
        }
        $data = ClientRepositoryFacade::all($request);
        Log::info($data);
        if (count($data) === 0) {
            return null;
        }
        return ClientResource::collection($data);
    }

    public function find($id)
    {
        try {
            $client = ClientRepositoryFacade::find($id);
            $user = User::find(Auth::user()->id);

            if (!Gate::allows("view", $client)) {
                return  null;
            }

            return new ClientResource($client);
        }catch (\Exception $e) {
            return null;
        }
    }

    public function create($data)
    {
        try {
            $client = ClientRepositoryFacade::create($data);
            return new ClientResource($client);
        } catch (Exception $e) {
            return null;
        }
    }

    public function update(UpdateClientRequest $request, $id)
    {
        try {
            $client = ClientRepositoryFacade::update($request, $id);
            return new ClientResource($client);
        }catch (\Exception $e) {
            return null;
        }
    }

    public function delete($id)
    {
        try {
            return ClientRepositoryFacade::delete($id);
        }catch (\Exception $e) {
            return null;
        }
    }

    public function findByPhone(ClientByPhoneRequest $request)
    {
        try {
            $telephone = $request->get('telephone');

           $client = ClientRepositoryFacade::findByPhone($telephone);
            return new ClientResource($client);
        }catch (Exception $e) {
            return null;
        }
    }

    public function findByDettes($id)
    {
        $user = User::find(Auth::user()->id);
        if (!Gate::allows("isBoutiquier", $user)) {
            return null;
        }

        try {
            $dettes = ClientRepositoryFacade::findByDettes($id);
            if (count($dettes) === 0){
                return null;
            }

            return DetteResource::collection($dettes);
        }catch (\Exception $e) {
            return null;
        }
    }

    public function findWithCompte($id)
    {
        try {
            $user = User::find(Auth::user()->id);
            if (!Gate::allows("isBoutiquier", $user)) {
                return null;
            }
            $client = ClientRepositoryFacade::findWithCompte($id);
            return new ClientResource($client);
        }catch (\Exception $e) {
            return null;
        }
    }

    public function register(array $data, AccountForClientRequest $request)
    {
        try {
//            dd($request->photo);
            $imageName = UploadFacade::upload($request->photo);

            if(!$imageName){
                return null;
            }

//            dd($imageName);
            $userData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'login' => $data['login'],
                'role_id' => $data['role_id'],
                'active' => $data['active'],
                'photo' => $imageName,
                'password' => $data['password'],
            ];

            $result = ClientRepositoryFacade::register($userData, $data);

            return $result;
        }catch (Exception $e) {
            DB::rollBack();
            return $data;
        }
    }
}
