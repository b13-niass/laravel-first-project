<?php

namespace App\Services;

use App\Enums\StateEnum;
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

            $tokenResult = $user->createToken('authToken');
            $accessToken = $tokenResult->accessToken;
//
//            // Retrieve the client related to the token
//            $clientModel = app(PassportClientRepository::class)->find($tokenResult->token->client_id);
//
//            // Convert Laravel Passport Client to ClientEntityInterface
//            $clientEntity = new PassportClientEntity(
//                $clientModel->id,
//                $clientModel->name,
//                $clientModel->redirect
//            );
//
//            $passportAccessToken = new AccessToken(
//                $user->id,            // $userIdentifier
//                $tokenResult->token->scopes,  // $scopes
//                $clientEntity                // $client
//            );
//            // Set additional properties
//            $passportAccessToken->setIdentifier($tokenResult->token->id);
//            $expiryDateTime = new \DateTimeImmutable($tokenResult->token->expires_at);
//
//            $passportAccessToken->setExpiryDateTime($expiryDateTime);

//            $customAccessTokenService = new \App\Services\CustomAccessTokenService();
//            $accessToken = $customAccessTokenService->generateToken();

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
