<?php

namespace Pinnacle\OpenIdConnect\Models\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static AuthenticationRequestParameterKey CODE()
 * @method static AuthenticationRequestParameterKey STATE()
 * @method static AuthenticationRequestParameterKey CHALLENGE()
 * @method static AuthenticationRequestParameterKey ERROR()
 * @method static AuthenticationRequestParameterKey ERROR_DESCRIPTION()
 */
class AuthenticationRequestParameterKey extends Enum
{
    protected const CODE              = 'code';

    protected const STATE             = 'state';

    protected const CHALLENGE         = 'code_challenge';

    protected const ERROR             = 'error';

    protected const ERROR_DESCRIPTION = 'error_description';
}
