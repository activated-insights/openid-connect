<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect\Models;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Models\Contracts\ProviderConfigurationInterface;

class ProviderConfiguration implements ProviderConfigurationInterface
{
    public function __construct(
        private mixed  $identifier,
        private string $clientId,
        private string $clientSecret,
        private Uri    $authorizationEndpoint,
        private Uri    $tokenEndpoint,
        private Uri    $userInfoEndpoint
    ) {
    }

    public function getIdentifier(): mixed
    {
        return $this->identifier;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
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
