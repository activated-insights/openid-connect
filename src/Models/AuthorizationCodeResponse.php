<?php

namespace Pinnacle\OpenIdConnect\Models;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Provider\ProviderConfiguration;

class AuthorizationCodeResponse
{
    public function __construct(
        private string                $authorizationCode,
        private ProviderConfiguration $provider,
        private Uri                   $redirectUri,
        private string                $challenge
    ) {
    }

    public function getProvider(): ProviderConfigurationInterface
    {
        return $this->provider;
    }

    public function getAuthorizationCode(): string
    {
        return $this->authorizationCode;
    }

    public function getRedirectUri(): Uri
    {
        return $this->redirectUri;
    }

    public function getChallenge(): string
    {
        return $this->challenge;
    }
}
