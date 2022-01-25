<?php

namespace Pinnacle\OpenIdConnect\Models;

use Pinnacle\OpenIdConnect\Models\Contracts\ProviderInterface;

class AccessTokenResponse
{
    public function __construct(private string $accessToken, private ProviderInterface $provider)
    {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
    
    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }
}
