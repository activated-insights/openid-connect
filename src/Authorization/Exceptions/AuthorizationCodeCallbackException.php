<?php

namespace Pinnacle\OpenIdConnect\Authorization\Exceptions;

use Pinnacle\OpenIdConnect\Authorization\Constants\AuthenticationErrorCode;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;

class AuthorizationCodeCallbackException extends OpenIdConnectException
{
    public function __construct(string $errorCode, ?string $errorDescription)
    {
        if ($errorDescription === null) {
            $errorDescription = AuthenticationErrorCode::getDescription($errorCode);
        }

        parent::__construct($errorDescription);
    }
}
