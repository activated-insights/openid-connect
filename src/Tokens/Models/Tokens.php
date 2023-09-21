<?php

namespace Pinnacle\OpenIdConnect\Tokens\Models;

use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\UserIdToken;

class Tokens
{
    public function __construct(
        private AccessToken  $accessToken,
        private RefreshToken $refreshToken,
        private UserIdToken  $userIdToken
    ){
    }

    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): RefreshToken
    {
        return $this->refreshToken;
    }

    public function getUserIdToken(): UserIdToken
    {
        return $this->userIdToken;
    }
}
