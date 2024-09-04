<?php

namespace App\Http\Controllers\Api;

use App\Enums\StateEnum;
use App\Enums\UserRole;
use App\Filters\ActiveFilter;
use App\Filters\IncludeRoleFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Rules\CustomPassword;
use App\Trait\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request){
        $user = User::find(Auth::user()->id);
        if (!Gate::allows("isAdmin", $user)){
            return $this->sendResponse(StateEnum::ECHEC, null, 'Vous n\'êtes pas authorisé à faire cette action', Response::HTTP_FORBIDDEN);
        }

        $query = User::query()->with('role');
        if ($request->has('active')) {
            $active = $request->query('active');
            $query = (new ActiveFilter())($query, $active, 'active');
        }

        if ($request->has('role')) {
            $role = $request->query('role');
            $query = (new IncludeRoleFilter())($query, $role, 'role');
        }

        $users = QueryBuilder::for($query)
            ->get();

        if (count($users) === 0){
            return $this->sendResponse(StateEnum::ECHEC, null, 'Aucun utilisateur disponible', Response::HTTP_NOT_FOUND);
        }
        return $this->sendResponse(StateEnum::SUCCESS, UserResource::collection($users), 'Liste des utilisateurs', Response::HTTP_OK);
    }

    public function create(StoreUserRequest $request)
    {
        $validateData = $request->validated();
        try {
            $role_libelle = $request->get('role');
            $role = Role::where('role', $role_libelle)->firstOrFail();
            $user = User::make($validateData);
            $user->role()->associate($role);
            $user->save();
            return $this->sendResponse(StateEnum::SUCCESS, new UserResource($user), 'Utilisateur crée avec succé', Response::HTTP_OK);
        }catch (\Exception $e) {
            return $this->sendResponse(StateEnum::ECHEC, null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Met à jour les informations d'un utilisateur spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validation des données entrantes
        $rules = [
            'nom' => 'sometimes|required|string|max:100',
            'prenom' => 'sometimes|required|string|max:100',
            'login' => 'sometimes|required|email|unique:users,login,' . $id,
            'role' => 'sometimes|required|string',
            'password' => ["required","string", new CustomPassword()]
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendResponse('failed', $validator->errors()->toArray(), 'Erreur de Validation', 422);
        }

        $validateData = $validator->validated();

        // Trouver l'utilisateur par ID
        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse('failed', null, 'Utilisateur non trouvé', 404);
        }

        if (isset($validateData['password'])) {
            $validateData['password'] = Hash::make($validateData['password']);
        }

        // Mettre à jour les données de l'utilisateur
        $user->update($validateData);

        return $this->sendResponse('success', $user, 'Utilisateur mis à jour', 200);
    }

    /**
     * Supprime un utilisateur spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Trouver l'utilisateur par ID
        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse('failed', null, 'Utilisateur non trouvé', 404);
        }

        // Supprimer l'utilisateur
        $user->delete();

        return $this->sendResponse('success', null, 'Utilisateur supprimé', 204);
    }

}
