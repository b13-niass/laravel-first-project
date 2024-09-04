<?php

namespace App\Http\Controllers\Api;

use App\Enums\StateEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountForClientRequest;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Services\AuthService;
use App\Models\User;
use App\Trait\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    protected $authService;
    public function __construct(AuthService $authService, private AuthenticationServiceInterface $authServiceInterface)
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
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('login', 'password');
        $data = $this->authServiceInterface->authenticate($credentials);
        return compact('data');
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
