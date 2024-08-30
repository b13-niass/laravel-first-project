<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Trait\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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
            return $this->sendResponse('failed', null, 'Le Login ou le mot de passe est incorrect', 401);

        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        // dd($token);

        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
        return $this->sendResponse('success', $data, 'Connection réussi', 200);

    }

    public function refresh(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Revoke all existing tokens for the user
        $user->tokens()->delete();

        // Create a new token
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return $this->sendResponse('success', $token, 'Token refreshed successfully', 200);

    }

    public function getAuthenticatedUser()
    {
        $user = Auth::user(); // Returns the authenticated user or null if not authenticated
        return $user;
    }
    
}
