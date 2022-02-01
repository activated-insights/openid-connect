<?php

namespace Pinnacle\OpenIdConnect\Tokens\Models;

use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\UserIdToken;

class Tokens
{
    public function __construct(private AccessToken $accessToken, private UserIdToken $userIdToken)
    {
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    /**
     * @return UserIdToken
     */
    public function getUserIdToken(): UserIdToken
    {
        return $this->userIdToken;
    }
}
