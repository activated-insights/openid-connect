<?php

namespace Pinnacle\OpenIdConnect\Models\Contracts;

interface AuthenticationResponseInterface
{
    public function getAuthorizationCode(): string;

    public function getState(): string;
}
