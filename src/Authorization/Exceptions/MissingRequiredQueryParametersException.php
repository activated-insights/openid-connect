<?php

namespace Pinnacle\OpenIdConnect\Authorization\Exceptions;

use Pinnacle\OpenIdConnect\Authorization\Constants\AuthorizationCodeCallbackKey;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;

class MissingRequiredQueryParametersException extends OpenIdConnectException
{
    public function __construct(AuthorizationCodeCallbackKey $parameterKey)
    {
        parent::__construct(sprintf('Request is missing an %s parameter.', $parameterKey->getValue()));
    }
}
