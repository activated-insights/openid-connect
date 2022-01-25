<?php

namespace Pinnacle\OpenIdConnect\Exceptions;

use Pinnacle\OpenIdConnect\Models\Constants\AuthorizationCodeCallbackKey;

class MissingRequiredQueryParametersException extends OpenIdConnectException
{
    public function __construct(AuthorizationCodeCallbackKey $parameterKey)
    {
        parent::__construct(sprintf('Request is missing an %s parameter.', $parameterKey->getValue()));
    }
}
