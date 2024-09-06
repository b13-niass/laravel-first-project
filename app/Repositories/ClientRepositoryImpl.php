<?php

namespace App\Repositories;

use App\Enums\StateEnum;
use App\Facades\UploadFacade;
use App\Filters\ClientWithCompteActiveFilter;
use App\Filters\ClientWithCompteFilter;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Interfaces\ClientRepository;
use App\Trait\MyImageTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class ClientRepositoryImpl implements ClientRepository
{
    public function all($request)
    {
        $query = Client::query();

        if ($request->has('compte')) {
            $compte = $request->query('compte');
            $query = (new ClientWithCompteFilter())($query, $compte, 'compte');
        }
        if ($request->has('active')) {
            $active = $request->query('active');
            $query = (new ClientWithCompteActiveFilter())($query, $active, 'active');
        }

        $data= QueryBuilder::for($query)
            ->get();
        return $data;
    }

    public function find($id)
    {
        return Client::findOrFail($id);
    }

    public function create($data)
    {
        DB::beginTransaction();
        $clientData = [
            'surnom' => $data['surnom'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse']?? null
        ];
        $client = Client::create($clientData);
        if (isset($data['user'])) {
            $role = Role::where('role', 'CLIENT')->firstOrFail();
            $file = $data['user']['photo'];

            $imageName = UploadFacade::upload($file);

            if(!$imageName){
                return null;
            }
            $data['user']['photo'] ='images/'.$imageName;
            $user = User::make($data['user']);
            $user->role()->associate($role);
            $user->save();
            if (!$user) {
                DB::rollBack();
            }
            $client->user()->associate($user);
            $client->save();
        }
        DB::commit();
        return $client;
    }

    public function update($request,$id)
    {
        $client = Client::find($id);
        if (!$client) {
            return null;
        }
        $client->update($request->validated());
        return $client;
    }

    public function delete($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return null;
        }
        return $client->delete();
    }

    public function findByPhone($phone)
    {
        $query = Client::query();

        $query->with('user');

        return $query->where('telephone', $phone)->firstOrFail();
    }

    public function findByDettes($id)
    {
        $client = Client::findOrFail($id);
        $dettes = $client->dettes;
        return $dettes;
    }

    public function findWithCompte($id)
    {
        $query = Client::query();
        $client = $query->findOrFail($id);
        $client->load('user');
        return $client;
    }

    public function register($userData, $allData)
    {
        DB::beginTransaction();

        $user = User::create($userData);

        if (!$user){
            DB::rollBack();
            return $allData;
        }

        $client = Client::find($allData['client_id']);
        $client->user()->associate($user);
        $client->save();

        DB::commit();
        $result = [
//            'user' => new UserResource($user),
            'client' => new ClientResource($client)
        ];

        return $result;
    }
}
