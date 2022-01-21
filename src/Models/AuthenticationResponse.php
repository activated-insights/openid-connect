<?php

namespace Pinnacle\OpenIdConnect\Models;

use Pinnacle\OpenIdConnect\Models\Contracts\AuthenticationResponseInterface;

class AuthenticationResponse implements AuthenticationResponseInterface
{
    public function __construct(private string $authorizationCode, private string $state)
    {
    }

    public function getAuthorizationCode(): string
    {
        return $this->authorizationCode;
    }

    public function getState(): string
    {
        return $this->state;
    }
}
