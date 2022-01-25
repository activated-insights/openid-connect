<?php

namespace Pinnacle\OpenIdConnect\Models\Contracts;

use GuzzleHttp\Psr7\Uri;

interface ProviderInterface
{
    public function getIdentifier(): mixed;

    public function getClientId(): string;

    public function getClientSecret(): string;

    public function getAuthorizationEndpoint(): Uri;

    public function getTokenEndpoint(): Uri;

    public function getUserInfoEndpoint(): Uri;
}
