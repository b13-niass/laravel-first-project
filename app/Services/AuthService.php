<?php

namespace App\Services;

use App\Enums\StateEnum;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\User;
use App\Trait\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AuthService {
    use ApiResponseTrait;

    public function register(array $data, Request $request)
    {
        try {
            $imageName = time().'.'.$request->photo->extension();
            $file = $request->photo->storeAs('images', $imageName, [
                'disk' => 'public'
            ]);
            $userData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'login' => $data['login'],
                'role_id' => $data['role_id'],
                'active' => $data['active'],
                'photo' => $imageName,
                'password' => $data['password'],
            ];


            return $this->sendResponse(StateEnum::SUCCESS, $result, 'Compte client Ajouter', Response::HTTP_OK);
        }catch (Exception $e) {
            DB::rollBack();
            return $this->sendResponse(StateEnum::ECHEC, $data, 'Erreur lors de la création', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
