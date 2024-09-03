<?php

namespace App\Services\Interfaces;

interface AuthenticationServiceInterface
{
    public function authenticate($credentials);
    public function logout();
}
