<?php

namespace Pinnacle\OpenIdConnect\Exceptions;

use Pinnacle\OpenIdConnect\Exceptions\Constants\AuthenticationErrorCode;

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
