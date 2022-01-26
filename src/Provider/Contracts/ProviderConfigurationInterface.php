<?php

namespace Pinnacle\OpenIdConnect\Provider\Contracts;

use GuzzleHttp\Psr7\Uri;
use Pinnacle\OpenIdConnect\Provider\Models\ClientId;
use Pinnacle\OpenIdConnect\Provider\Models\ClientSecret;
use Pinnacle\OpenIdConnect\Provider\Models\Identifier;

interface ProviderConfigurationInterface
{
    public function getIdentifier(): ?Identifier;

    public function getClientId(): ClientId;

    public function getClientSecret(): ClientSecret;

    public function getAuthorizationEndpoint(): Uri;

    public function getTokenEndpoint(): Uri;

    public function getUserInfoEndpoint(): Uri;
}
