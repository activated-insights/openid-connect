<?php

namespace Pinnacle\OpenIdConnect\Authorization;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Authentication\Models\Challenge;
use Pinnacle\OpenIdConnect\Authorization\Models\AuthorizationCode;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;
use Pinnacle\OpenIdConnect\Provider\Models\ProviderConfiguration;

class AuthorizationCodeResponse
{
    public function __construct(
        private AuthorizationCode     $authorizationCode,
        private ProviderConfiguration $provider,
        private Uri                   $redirectUri,
        private Challenge             $challenge
    ) {
    }

    public function getProvider(): ProviderConfigurationInterface
    {
        return $this->provider;
    }

    public function getAuthorizationCode(): AuthorizationCode
    {
        return $this->authorizationCode;
    }

    public function getRedirectUri(): Uri
    {
        return $this->redirectUri;
    }

    public function getChallenge(): Challenge
    {
        return $this->challenge;
    }
}
