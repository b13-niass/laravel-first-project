<?php

namespace App\Http\Controllers\Api;

use App\Enums\StateEnum;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CustomPassword;
use App\Trait\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function create(Request $request)
    {
        $rules = [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'login' => 'required|unique:users,login|email',
            'role' => 'required|string',
            'password' => ["required","string","confirmed", new CustomPassword()]
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendResponse(StateEnum::ECHEC->value, $validator->errors()->toArray(),"Erreur de Validation", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validateData = $validator->validated();

        $user = User::create($validateData);
        $accessToken = $user->createToken('authToken')->accessToken;
//        $data  = [
//            'user' => $user,
//            'accessToken' => $accessToken
//        ];
        return $this->sendResponse('success', $user, 'Utilisateur crée', 200);
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
