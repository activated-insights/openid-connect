<?php

namespace Pinnacle\OpenIdConnect\Authorization;

use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Tokens\Models\AccessToken;
use Pinnacle\OpenIdConnect\Tokens\Models\RefreshToken;
use Pinnacle\OpenIdConnect\Tokens\Models\Tokens;
use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\UserIdToken;

class TokensResponse
{
    public function __construct(private Tokens $tokens, private ProviderConfigurationInterface $provider)
    {
    }

    public function getAccessToken(): AccessToken
    {
        return $this->tokens->getAccessToken();
    }

    public function getRefreshToken(): ?RefreshToken
    {
        return $this->tokens->getRefreshToken();
    }

    public function getUserIdToken(): UserIdToken
    {
        return $this->tokens->getUserIdToken();
    }

    public function getProvider(): ProviderConfigurationInterface
    {
        return $this->provider;
    }
}
