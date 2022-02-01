<?php

namespace Pinnacle\OpenIdConnect\Tokens\Exceptions;

use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;
use Pinnacle\OpenIdConnect\Tokens\Models\UserIdToken\Constants\ClaimKey;

class MissingRequiredClaimKeyException extends OpenIdConnectException
{
    public function __construct(ClaimKey $claimKey)
    {
        parent::__construct(sprintf('User ID token is missing required claim key %s', $claimKey->getValue()));
    }
}
