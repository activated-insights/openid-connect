<?php

namespace Pinnacle\OpenIdConnect\Authorization;

use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Tokens\Models\AccessToken;

class TokensResponse
{
    public function __construct(private AccessToken $accessToken, private ProviderConfigurationInterface $provider)
    {
    }

    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    public function getProvider(): ProviderConfigurationInterface
    {
        return $this->provider;
    }
}
