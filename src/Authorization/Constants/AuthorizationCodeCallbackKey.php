<?php

namespace Pinnacle\OpenIdConnect\Authorization\Constants;

use MyCLabs\Enum\Enum;

/**
 * @method static AuthorizationCodeCallbackKey CODE()
 * @method static AuthorizationCodeCallbackKey STATE()
 * @method static AuthorizationCodeCallbackKey CHALLENGE()
 * @method static AuthorizationCodeCallbackKey ERROR()
 * @method static AuthorizationCodeCallbackKey ERROR_DESCRIPTION()
 */
class AuthorizationCodeCallbackKey extends Enum
{
    protected const CODE              = 'code';

    protected const STATE             = 'state';

    protected const CHALLENGE         = 'code_challenge';

    protected const ERROR             = 'error';

    protected const ERROR_DESCRIPTION = 'error_description';
}
