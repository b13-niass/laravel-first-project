<?php

namespace App\Services;

use App\Enums\StateEnum;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class AuthenticationSanctum implements AuthenticationServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function authenticate($credentials)
    {
        try {
            if (!Auth::attempt($credentials)) {
                return null;
            }

            $user = User::find(Auth::user()->id);

            if (!$user->active) {
                return null;
            }

            $accessToken = $user->createToken('authToken', ['id' => $user->id, 'role' => $user->role->role])->plainTextToken;

            $data = [
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
            ];
            return $data;
        }catch (\Exception $exception){
            return null;
        }
    }

    public function logout()
    {
        try {
            $user = User::find(Auth::user()->id);
            if ($user) {
                $user->currentAccessToken()->delete();
                Auth::logout();
                return 'Déconnection avec succès';
            }
            return null;
        }catch (Exception $exception){
            return null;
        }
    }
}
