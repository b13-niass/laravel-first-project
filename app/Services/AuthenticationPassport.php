<?php

namespace App\Services;

use App\Enums\StateEnum;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Exception;

class AuthenticationPassport implements AuthenticationServiceInterface
{
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

            // Revoke all existing tokens for the user
            $user->tokens->each(function ($token) {
                $token->revoke();
            });

            $accessToken = $user->createToken('authToken')->accessToken;

            $data = [
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
            ];
            return $data;
        }catch (Exception $exception){
            return null;
        }
    }

    public function logout()
    {
        try {
            $user = User::find(Auth::user()->id);
            if ($user) {
                $user->tokens()->delete();
                Auth::logout();
                return 'Déconnection avec succès';
            }
            return null;
        }catch (Exception $exception){
            return null;
        }
    }
}
