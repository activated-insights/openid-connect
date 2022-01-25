<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect\Provider\Models;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Provider\Contracts\ProviderConfigurationInterface;

class ProviderConfiguration implements ProviderConfigurationInterface
{
    public function __construct(
        private ?Identifier  $identifier,
        private ClientId     $clientId,
        private ClientSecret $clientSecret,
        private Uri          $authorizationEndpoint,
        private Uri          $tokenEndpoint,
        private Uri          $userInfoEndpoint
    ) {
    }

    public function getIdentifier(): ?Identifier
    {
        return $this->identifier;
    }

    public function getClientId(): ClientId
    {
        return $this->clientId;
    }

    public function getClientSecret(): ClientSecret
    {
        return $this->clientSecret;
    }

    public function getAuthorizationEndpoint(): Uri
    {
        return $this->authorizationEndpoint;
    }

    public function getTokenEndpoint(): Uri
    {
        return $this->tokenEndpoint;
    }

    public function getUserInfoEndpoint(): Uri
    {
        return $this->userInfoEndpoint;
    }
}
