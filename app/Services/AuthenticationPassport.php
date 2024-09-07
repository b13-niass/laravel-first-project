<?php

namespace App\Services;

use App\Enums\StateEnum;
use App\Exceptions\AuthException;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\Client as PassportClientEntity;
use Laravel\Passport\ClientRepository as PassportClientRepository;
use PHPUnit\Exception;

class AuthenticationPassport implements AuthenticationServiceInterface
{
    public function authenticate($credentials)
    {
        try {
            if (!Auth::attempt($credentials)) {
                throw new AuthException('Login ou mot de passe incorrect', Response::HTTP_UNAUTHORIZED);
            }
            $user = User::find(Auth::user()->id);

            if (!$user->active) {
                throw new AuthException('Votre compte est désactivé', Response::HTTP_FORBIDDEN);
            }

            $user->tokens->each(function ($token) {
                $token->revoke();
            });

            $tokenResult = $user->createToken('authToken');
            $accessToken = $tokenResult->accessToken;

            $data = [
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
            ];
            return $data;
        }catch (AuthException $e){
            return $e->render();
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
