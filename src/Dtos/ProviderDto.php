<?php

declare(strict_types=1);

namespace Pinnacle\OpenIdConnect\Dtos;

use GuzzleHttp\Psr7\Uri;

class ProviderDto
{
    private string $clientId;

    private string $clientSecret;

    private Uri    $authorizationEndpoint;

    private Uri    $tokenEndpoint;

    private Uri    $userInfoEndpoint;

    public function __construct(
        string $clientId,
        string $clientSecret,
        Uri $authorizationEndpoint,
        Uri $tokenEndpoint,
        Uri $userInfoEndpoint
    ) {
        $this->clientId              = $clientId;
        $this->clientSecret          = $clientSecret;
        $this->authorizationEndpoint = $authorizationEndpoint;
        $this->tokenEndpoint         = $tokenEndpoint;
        $this->userInfoEndpoint      = $userInfoEndpoint;
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
