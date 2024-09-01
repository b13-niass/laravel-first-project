<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Trait\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $queryParams = $request->query();
        $sortBy = $queryParams['sort_by'] ?? 'created_at,asc';

        $query = Client::filter($queryParams);

        if ($request->has('sort_by') && $queryParams['sort_by'] !== '') {
            $sortBy = $request->query('sort_by', 'created_at,asc');
            list($attribute, $direction) = explode(',', $sortBy);
            $query->orderBy($attribute, $direction);
        }

        if ($request->has('include') && $queryParams['include'] === 'user') {
            $query->with('user');
        }

        $data = $query->get();
        return $this->sendResponse('success', ClientResource::collection($data) , 'Liste des clients récupérée avec succès', 200);
    }

    public function show(Request $request, $id)
    {
        $query = Client::query();

        if ($request->has('include') && $request->query('include') === 'user') {
            $query->with('user');
        }

        $client = $query->find($id);

        if (!$client) {
            return $this->sendResponse('failed', null, 'Client introuvable', 404);
        }

        return $this->sendResponse('success', $client, 'Client récupéré avec succès', 200);
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
