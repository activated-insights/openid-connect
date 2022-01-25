<?php

namespace Pinnacle\OpenIdConnect\Authorization;

use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;

class TokensResponse
{
    public function __construct(private string $accessToken, private ProviderConfigurationInterface $provider)
    {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getProvider(): ProviderConfigurationInterface
    {
        return $this->provider;
    }
}
