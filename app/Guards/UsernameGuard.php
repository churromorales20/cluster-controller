<?php

namespace App\Guards;

use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Laravel\Sanctum\Guard as SanctumGuard;

class UsernameGuard extends SanctumGuard
{
    use GuardHelpers;

    public function __construct($provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->inputKey = 'username';
        $this->storageKey = 'username';
    }
}