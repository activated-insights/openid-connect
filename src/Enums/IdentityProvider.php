<?php

declare(strict_types=1);

namespace Pinnacle\OpenidConnect\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static IdentityProvider FACEBOOK()
 * @method static IdentityProvider GOOGLE()
 * @method static IdentityProvider AMAZON()
 * @method static IdentityProvider APPLE()
 * @method static IdentityProvider COGNITO()
 */
class IdentityProvider extends Enum
{
    protected const FACEBOOK = 'Facebook';

    protected const GOOGLE   = 'Google';

    protected const AMAZON   = 'LoginWithAmazon';

    protected const APPLE    = 'SignInWithApple';

    protected const COGNITO  = 'COGNITO';
}
