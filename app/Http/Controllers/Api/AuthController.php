<?php

namespace App\Http\Controllers\Api;

use App\Enums\StateEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountForClientRequest;
use App\Http\Services\AuthService;
use App\Models\User;
use App\Trait\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    use ApiResponseTrait;
    /**
     * Handle login request and generate Sanctum token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the request data

        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('login', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Non Authorizé', Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find(Auth::user()->id);

        if (!$user->active) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Utilisateur désactivé', Response::HTTP_UNAUTHORIZED);
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
        return $this->sendResponse(StateEnum::SUCCESS, $data, 'Connection réussi', Response::HTTP_OK);

    }

    public function refresh(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if (!$user) {
            return $this->sendResponse(StateEnum::ECHEC, null, 'Non Authorizé', Response::HTTP_UNAUTHORIZED);
        }

        // Revoke all existing tokens for the user
        $user->tokens->each(function ($token) {
            $token->revoke();
        });

        // Create a new token
        $accessToken = $user->createToken('authToken')->accessToken;

        $data = [
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
        ];

        return $this->sendResponse(StateEnum::SUCCESS, $data, 'Token Refresh', Response::HTTP_OK);
    }

    public function getAuthenticatedUser()
    {
        $user = Auth::user(); // Returns the authenticated user or null if not authenticated
        return $user;
    }

    public function register(AccountForClientRequest $request){
        $validateData = $request->validated();
       return $this->authService->register($validateData, $request);
    }

}
