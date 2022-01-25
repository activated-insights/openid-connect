<?php

namespace Pinnacle\OpenIdConnect\Exceptions;

use Pinnacle\OpenIdConnect\Models\Constants\AuthenticationRequestParameterKey;

class MissingRequiredQueryParametersException extends OpenIdRequestException
{
    public function __construct(AuthenticationRequestParameterKey $parameterKey)
    {
        parent::__construct(sprintf('Request is missing an %s parameter.', $parameterKey->getValue()));
    }
}
