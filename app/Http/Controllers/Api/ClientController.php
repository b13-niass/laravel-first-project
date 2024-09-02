<?php

namespace App\Http\Controllers\Api;

use App\Enums\StateEnum;
use App\Filters\ClientWithCompteActiveFilter;
use App\Filters\ClientWithCompteFilter;
use App\Http\Controllers\Controller;
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
        $user = User::find(Auth::user()->id);
        if (!Gate::allows("isBoutiquier", $user)){
            return $this->sendResponse(StateEnum::ECHEC, null, 'Vous n\'êtes pas authorisé à faire cette action', Response::HTTP_FORBIDDEN);
        }

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

        if (count($data) === 0) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Aucun client trouvé', Response::HTTP_NOT_FOUND);
        }
        return $this->sendResponse(StateEnum::SUCCESS, ClientResource::collection($data) , 'Liste des clients récupérée avec succès', Response::HTTP_OK);
    }

    public function show($id)
    {
        try {
            $client = Client::findOrFail($id);
            $user = User::find(Auth::user()->id);

//            dd(auth()->user()->can('view', $client));
            if (!Gate::allows("view", $client)) {
                return $this->sendResponse(StateEnum::ECHEC, null, 'Vous n\'êtes pas authorisé à faire cette action', Response::HTTP_FORBIDDEN);
            }


            $query = Client::query();
//            $client = $query->findOrFail($id);
//            $client->load('user');
            return $this->sendResponse(StateEnum::SUCCESS, new ClientResource($client), 'Client trouve avec succès', Response::HTTP_OK);
        }catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Client introuvable', Response::HTTP_NOT_FOUND);
        }
    }

    public function showByPhone(ClientByPhoneRequest $request)
    {
        try {
            $query = Client::query();

            $telephone = $request->get('telephone');
            $query->with('user');

            $client = $query->where('telephone', $telephone)->firstOrFail();

            return $this->sendResponse(StateEnum::SUCCESS, new ClientResource($client), 'Client récupéré avec succès', Response::HTTP_OK);
        }catch (Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Pas de client trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showWithUser($id)
    {
        $user = User::find(Auth::user()->id);
        if (!Gate::allows("isBoutiquier", $user)) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Vous n\'êtes pas authorisé à faire cette action', Response::HTTP_FORBIDDEN);
        }

        try {
            $query = Client::query();
            $client = $query->findOrFail($id);
            $client->load('user');
            return $this->sendResponse(StateEnum::SUCCESS, new ClientResource($client), 'Client trouve avec succès', Response::HTTP_OK);
        }catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Client introuvable', Response::HTTP_NOT_FOUND);
        }
    }

    public function showDettesUser($id){
        $user = User::find(Auth::user()->id);
        if (!Gate::allows("isBoutiquier", $user)) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Vous n\'êtes pas authorisé à faire cette action', Response::HTTP_FORBIDDEN);
        }

        try {
            $client = Client::findOrFail($id);
            $dettes = $client->dettes;
            if (count($dettes) === 0){
                return $this->sendResponse(StateEnum::ECHEC, null, 'Aucune détte trouvée', Response::HTTP_NOT_FOUND);
            }

            return $this->sendResponse(StateEnum::SUCCESS, DetteResource::collection($dettes), 'Liste des clients déttes récupérée avec succès', Response::HTTP_OK);
        }catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Erreur lors de la récupération des déttes', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreClientRequest $request)
    {
        $validateData = $request->validated();
//        dd($validateData);
        try {
            // Démarrer une transaction
            DB::beginTransaction();
            $clientData = [
                'surnom' => $validateData['surnom'],
                'telephone' => $validateData['telephone'],
                'adresse' => $validateData['adresse']?? null
            ];
            $client = Client::create($clientData);

//            dd($validateData['user']['photo']);
            if (isset($validateData['user'])) {
                $role = Role::where('role', 'CLIENT')->firstOrFail();
                $file = $validateData['user']['photo'];
                $imageName = time().'.'.$file->extension();
                $file = $file->storeAs('images', $imageName, [
                    'disk' => 'public'
                ]);
                $validateData['user']['photo'] = $imageName;
                $user = User::make($validateData['user']);
                $user->role()->associate($role);
                $user->save();
                if (!$user) {
                    DB::rollBack();
                }
                $client->user()->associate($user);
                $client->save();
            }
            DB::commit();
            return $this->sendResponse('success', new ClientResource($client), 'Client créé avec succès', 201);
        } catch (Exception $e) {
            return $this->sendResponse('failed', null, 'Erreur lors de la transaction', 500);
        }
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->sendResponse('failed', null, 'Client introuvable', 404);
        }

        $client->update($request->validated());

        return $this->sendResponse('success', $client, 'Client mis à jour avec succès', 200);
    }

    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->sendResponse('failed', null, 'lient introuvable', 404);
        }

        $client->delete();

        return $this->sendResponse('success', null, 'Client supprimé avec succès', 204);
    }
}
