<?php

namespace Pinnacle\OpenIdConnect\Models;

use Pinnacle\OpenIdConnect\Models\Contracts\ProviderConfigurationInterface;

class AuthenticationTokensResponse
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
