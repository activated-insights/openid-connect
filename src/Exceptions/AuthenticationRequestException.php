<?php

namespace Pinnacle\OpenIdConnect\Exceptions;

use Pinnacle\OpenIdConnect\Exceptions\Constants\AuthenticationRequestErrorCode;

class AuthenticationRequestException extends OpenIdRequestException
{
    public function __construct(string $errorCode, ?string $errorDescription)
    {
        if ($errorDescription === null) {
            $errorDescription = AuthenticationRequestErrorCode::getDescription($errorCode);
        }

        parent::__construct($errorDescription);
    }
}
