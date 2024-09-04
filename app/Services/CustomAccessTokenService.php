<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain;

class CustomAccessTokenService
{
    public function generateToken()
    {
        $privateKey = file_get_contents(storage_path('oauth-private.key'));

        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($privateKey)
        );

        $now = new \DateTimeImmutable();
        $expireAt = $now->modify('+1 hour'); // Adjust the expiration as needed

        $token = $config->builder()
            ->issuedBy('http://your-app.com') // Customize your app URL
            ->permittedFor('http://your-app.com') // Customize your app URL
            ->identifiedBy(Auth::user()->id) // Set the 'sub' claim with the user ID
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expireAt)
            ->withClaim('scopes', []) // Scopes claim, empty array as specified
            ->withClaim('user_role', $this->getUserRole(Auth::user())) // Scopes claim, empty array as specified
            ->getToken($config->signer(), $config->signingKey());

        return $token->toString(); // Return the token as a string
    }

    protected function generateJti()
    {
        return bin2hex(random_bytes(32)); // Generate a random JWT ID
    }

    /**
     * Get the user role (customize this method as needed).
     *
     * @param $user
     * @return string
     */
    protected function getUserRole($user)
    {
        // Assuming you have a method to get the user role
        return $user->role;
    }
}
